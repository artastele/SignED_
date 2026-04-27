<?php

class Learner extends Model
{
    /**
     * Create a new learner from approved enrollment
     */
    public function create($enrollmentData)
    {
        $sql = "INSERT INTO learners (user_id, parent_id, first_name, last_name, date_of_birth, grade_level, status)
                VALUES (:user_id, :parent_id, :first_name, :last_name, :date_of_birth, :grade_level, :status)";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':user_id' => $enrollmentData['user_id'],
            ':parent_id' => $enrollmentData['parent_id'],
            ':first_name' => $enrollmentData['first_name'],
            ':last_name' => $enrollmentData['last_name'],
            ':date_of_birth' => $enrollmentData['date_of_birth'],
            ':grade_level' => $enrollmentData['grade_level'],
            ':status' => 'enrolled'
        ]);
    }

    /**
     * Create learner record from approved enrollment
     */
    public function createFromEnrollment($enrollmentData)
    {
        // First create a user account for the learner
        $userModel = new User();
        
        // Generate a temporary email and password for the learner
        $learnerEmail = strtolower($enrollmentData['first_name'] . '.' . $enrollmentData['last_name']) . '@learner.signed.edu';
        $tempPassword = bin2hex(random_bytes(8)); // Generate random password
        
        $userData = [
            'first_name' => $enrollmentData['first_name'],
            'middle_name' => $enrollmentData['middle_name'] ?? null,
            'last_name' => $enrollmentData['last_name'],
            'suffix' => $enrollmentData['suffix'] ?? null,
            'email' => $learnerEmail,
            'password' => password_hash($tempPassword, PASSWORD_DEFAULT),
            'role' => 'learner',
            'is_verified' => 1,
            'auth_provider' => 'local'
        ];
        
        $userId = $userModel->createUser($userData);
        
        if (!$userId) {
            return false;
        }

        $sql = "INSERT INTO learners (user_id, parent_id, first_name, last_name, date_of_birth, grade_level, status)
                VALUES (:user_id, :parent_id, :first_name, :last_name, :date_of_birth, :grade_level, :status)";

        $stmt = $this->db->prepare($sql);

        $result = $stmt->execute([
            ':user_id' => $userId,
            ':parent_id' => $enrollmentData['parent_id'],
            ':first_name' => $enrollmentData['first_name'],
            ':last_name' => $enrollmentData['last_name'],
            ':date_of_birth' => $enrollmentData['date_of_birth'],
            ':grade_level' => $enrollmentData['grade_level'],
            ':status' => $enrollmentData['status']
        ]);

        return $result ? $this->db->lastInsertId() : false;
    }

    /**
     * Get learners by status
     */
    public function getByStatus($status)
    {
        $sql = "SELECT l.*, u.fullname as parent_name, u.email as parent_email 
                FROM learners l 
                JOIN users u ON l.parent_id = u.id 
                WHERE l.status = :status 
                ORDER BY l.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':status' => $status]);

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Update learner status
     */
    public function updateStatus($learnerId, $status)
    {
        $sql = "UPDATE learners SET status = :status, updated_at = CURRENT_TIMESTAMP WHERE id = :id";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':status' => $status,
            ':id' => $learnerId
        ]);
    }

    /**
     * Get learner with assessment data
     */
    public function getWithAssessment($learnerId)
    {
        $sql = "SELECT l.*, a.* 
                FROM learners l 
                LEFT JOIN assessments a ON l.id = a.learner_id 
                WHERE l.id = :learner_id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':learner_id' => $learnerId]);

        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    /**
     * Get learner with IEP data
     */
    public function getWithIep($learnerId)
    {
        $sql = "SELECT l.*, i.* 
                FROM learners l 
                LEFT JOIN ieps i ON l.id = i.learner_id 
                WHERE l.id = :learner_id 
                ORDER BY i.created_at DESC 
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':learner_id' => $learnerId]);

        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    /**
     * Get all learners for a parent
     */
    public function getByParent($parentId)
    {
        $sql = "SELECT * FROM learners WHERE parent_id = :parent_id ORDER BY created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':parent_id' => $parentId]);

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Get learner by user ID
     */
    public function getByUserId($userId)
    {
        $sql = "SELECT * FROM learners WHERE user_id = :user_id LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $userId]);

        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    /**
     * Create a basic learner record (for Google Sign In users)
     */
    public function createBasicLearner($learnerData)
    {
        $sql = "INSERT INTO learners (user_id, parent_id, first_name, last_name, date_of_birth, grade_level, status)
                VALUES (:user_id, :parent_id, :first_name, :last_name, :date_of_birth, :grade_level, :status)";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':user_id' => $learnerData['user_id'],
            ':parent_id' => $learnerData['parent_id'],
            ':first_name' => $learnerData['first_name'],
            ':last_name' => $learnerData['last_name'],
            ':date_of_birth' => $learnerData['date_of_birth'],
            ':grade_level' => $learnerData['grade_level'],
            ':status' => $learnerData['status']
        ]);
    }

    /**
     * Get learner by ID
     */
    public function getById($learnerId)
    {
        $sql = "SELECT l.*, u.fullname as parent_name, u.email as parent_email 
                FROM learners l 
                JOIN users u ON l.parent_id = u.id 
                WHERE l.id = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $learnerId]);

        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    /**
     * Get learners ready for next workflow step
     */
    public function getReadyForAssessment()
    {
        return $this->getByStatus('enrolled');
    }

    public function getReadyForIepMeeting()
    {
        return $this->getByStatus('assessment_complete');
    }

    public function getActiveIeps()
    {
        return $this->getByStatus('active');
    }

    /**
     * Get learner by LRN (Learner Reference Number)
     */
    public function getByLRN($lrn)
    {
        $sql = "SELECT * FROM learners WHERE lrn = :lrn LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':lrn' => $lrn]);

        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    /**
     * Get total learner count
     */
    public function getTotalCount()
    {
        $sql = "SELECT COUNT(*) as total FROM learners";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        return $result->total;
    }
}