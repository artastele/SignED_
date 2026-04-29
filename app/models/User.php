<?php

class User extends Model
{
    public function register($data)
    {
        $sql = "INSERT INTO users (first_name, middle_name, last_name, suffix, email, password, role, auth_provider, is_verified, otp_code, otp_expires_at)
                VALUES (:first_name, :middle_name, :last_name, :suffix, :email, :password, :role, :auth_provider, :is_verified, :otp_code, :otp_expires_at)";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':first_name' => $data['first_name'],
            ':middle_name' => $data['middle_name'],
            ':last_name' => $data['last_name'],
            ':suffix' => $data['suffix'],
            ':email' => $data['email'],
            ':password' => $data['password'],
            ':role' => $data['role'] ?? null,
            ':auth_provider' => 'local',
            ':is_verified' => 0,
            ':otp_code' => $data['otp_code'],
            ':otp_expires_at' => $data['otp_expires_at']
        ]);
    }

    public function createUser($data)
    {
        $sql = "INSERT INTO users (first_name, middle_name, last_name, suffix, email, password, role, auth_provider, is_verified)
                VALUES (:first_name, :middle_name, :last_name, :suffix, :email, :password, :role, :auth_provider, :is_verified)";

        $stmt = $this->db->prepare($sql);

        $result = $stmt->execute([
            ':first_name' => $data['first_name'],
            ':middle_name' => $data['middle_name'] ?? null,
            ':last_name' => $data['last_name'],
            ':suffix' => $data['suffix'] ?? null,
            ':email' => $data['email'],
            ':password' => $data['password'],
            ':role' => $data['role'],
            ':auth_provider' => $data['auth_provider'],
            ':is_verified' => $data['is_verified']
        ]);

        return $result ? $this->db->lastInsertId() : false;
    }

    public function findUserByEmail($email)
    {
        $sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':email' => $email]);

        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function verifyOTP($email, $otp)
{
    $sql = "SELECT * FROM users WHERE email = :email LIMIT 1";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([':email' => $email]);

    $user = $stmt->fetch(PDO::FETCH_OBJ);

    if (!$user) return false;

    // check OTP match
    if ($user->otp_code != $otp) return false;

    // check expiration using PHP time
    if (strtotime($user->otp_expires_at) < time()) return false;

    return $user;
}
    public function markAsVerified($email)
    {
        $sql = "UPDATE users
                SET is_verified = 1,
                    otp_code = NULL,
                    otp_expires_at = NULL
                WHERE email = :email";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':email' => $email]);
    }
    public function login($email)
{
    $sql = "SELECT * FROM users WHERE email = :email LIMIT 1";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([':email' => $email]);

    return $stmt->fetch(PDO::FETCH_OBJ);
}

public function updateRole($userId, $role)
{
    $sql = "UPDATE users SET role = :role WHERE id = :id";
    $stmt = $this->db->prepare($sql);

    return $stmt->execute([
        ':role' => $role,
        ':id' => $userId
    ]);
}
public function getAllUsers()
{
    $sql = "SELECT id, first_name, middle_name, last_name, suffix, fullname, email, role, is_verified, created_at 
            FROM users 
            ORDER BY id DESC";

    $stmt = $this->db->prepare($sql);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_OBJ);
}

public function getUserById($id)
{
    $sql = "SELECT * FROM users WHERE id = :id LIMIT 1";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([':id' => $id]);

    return $stmt->fetch(PDO::FETCH_OBJ);
}

public function updateUserRole($id, $role)
{
    $sql = "UPDATE users SET role = :role WHERE id = :id";
    $stmt = $this->db->prepare($sql);

    return $stmt->execute([
        ':id' => $id,
        ':role' => $role
    ]);
}

public function deleteUser($id)
{
    $sql = "DELETE FROM users WHERE id = :id";
    $stmt = $this->db->prepare($sql);

    return $stmt->execute([':id' => $id]);
}

// Google OAuth Methods
public function findUserByGoogleId($googleId)
{
    $sql = "SELECT * FROM users WHERE google_id = :google_id LIMIT 1";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([':google_id' => $googleId]);

    return $stmt->fetch(PDO::FETCH_OBJ);
}

public function createGoogleUser($googleData)
{
    // Split Google name into parts (basic split)
    $nameParts = explode(' ', $googleData['name']);
    $firstName = $nameParts[0] ?? '';
    $lastName = count($nameParts) > 1 ? end($nameParts) : '';
    $middleName = count($nameParts) > 2 ? implode(' ', array_slice($nameParts, 1, -1)) : null;
    
    $sql = "INSERT INTO users (first_name, middle_name, last_name, email, google_id, auth_provider, is_verified) 
            VALUES (:first_name, :middle_name, :last_name, :email, :google_id, 'google', 1)";

    $stmt = $this->db->prepare($sql);

    return $stmt->execute([
        ':first_name' => $firstName,
        ':middle_name' => $middleName,
        ':last_name' => $lastName,
        ':email' => $googleData['email'],
        ':google_id' => $googleData['id']
    ]);
}

public function linkGoogleAccount($userId, $googleId)
{
    $sql = "UPDATE users SET google_id = :google_id WHERE id = :id";
    $stmt = $this->db->prepare($sql);

    return $stmt->execute([
        ':google_id' => $googleId,
        ':id' => $userId
    ]);
}

/**
 * Get count of SPED users
 */
public function getSpedUserCount()
{
    $sql = "SELECT COUNT(*) as total FROM users 
            WHERE role IN ('sped_teacher', 'guidance', 'principal', 'learner', 'parent', 'admin')";
    $stmt = $this->db->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_OBJ);
    return $result->total;
}

/**
 * Get total user count
 */
public function getTotalCount()
{
    $sql = "SELECT COUNT(*) as total FROM users";
    $stmt = $this->db->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_OBJ);
    return $result->total;
}

/**
 * Get user count by role
 */
public function getUserCountByRole()
{
    $sql = "SELECT role, COUNT(*) as count FROM users GROUP BY role";
    $stmt = $this->db->prepare($sql);
    $stmt->execute();
    
    $results = $stmt->fetchAll(PDO::FETCH_OBJ);
    $counts = [];
    foreach ($results as $row) {
        $counts[$row->role] = $row->count;
    }
    return $counts;
}

/**
 * Get recent users
 */
public function getRecentUsers($limit = 5)
{
    $sql = "SELECT id, fullname, email, role, is_verified, created_at 
            FROM users 
            ORDER BY created_at DESC 
            LIMIT :limit";
    
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    
    return $stmt->fetchAll(PDO::FETCH_OBJ);
}

/**
 * Update user profile
 */
public function updateProfile($userId, $data)
{
    $sql = "UPDATE users 
            SET first_name = :first_name,
                middle_name = :middle_name,
                last_name = :last_name,
                suffix = :suffix,
                email = :email,
                phone = :phone,
                address = :address
            WHERE id = :id";
    
    $stmt = $this->db->prepare($sql);
    
    return $stmt->execute([
        ':first_name' => $data['first_name'],
        ':middle_name' => $data['middle_name'],
        ':last_name' => $data['last_name'],
        ':suffix' => $data['suffix'],
        ':email' => $data['email'],
        ':phone' => $data['phone'] ?? null,
        ':address' => $data['address'] ?? null,
        ':id' => $userId
    ]);
}

/**
 * Get users by role
 */
public function getUsersByRole($role)
{
    $sql = "SELECT id, first_name, middle_name, last_name, suffix, fullname, email, role, is_verified, created_at 
            FROM users 
            WHERE role = :role
            ORDER BY id DESC";
    
    $stmt = $this->db->prepare($sql);
    $stmt->execute([':role' => $role]);
    
    return $stmt->fetchAll(PDO::FETCH_OBJ);
}
}