<?php

// Load User model if not already loaded
if (!class_exists('User')) {
    require_once __DIR__ . '/User.php';
}

// Load Assessment model if not already loaded
if (!class_exists('Assessment')) {
    require_once __DIR__ . '/Assessment.php';
}

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

        $sql = "INSERT INTO learners (user_id, parent_id, previous_learner_id, first_name, middle_name, last_name, suffix, date_of_birth, grade_level, school_year, status)
                VALUES (:user_id, :parent_id, :previous_learner_id, :first_name, :middle_name, :last_name, :suffix, :date_of_birth, :grade_level, :school_year, :status)";

        $stmt = $this->db->prepare($sql);

        $result = $stmt->execute([
            ':user_id' => $userId,
            ':parent_id' => $enrollmentData['parent_id'],
            ':previous_learner_id' => $enrollmentData['previous_learner_id'] ?? null,
            ':first_name' => $enrollmentData['first_name'],
            ':middle_name' => $enrollmentData['middle_name'] ?? null,
            ':last_name' => $enrollmentData['last_name'],
            ':suffix' => $enrollmentData['suffix'] ?? null,
            ':date_of_birth' => $enrollmentData['date_of_birth'],
            ':grade_level' => $enrollmentData['grade_level'],
            ':school_year' => $enrollmentData['school_year'] ?? date('Y') . '-' . (date('Y') + 1),
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
     * Search learners by name (for old student lookup)
     * Searches first name, last name, or full name
     * Only returns learners belonging to the specified parent
     */
    public function searchByName($searchTerm, $parentId)
    {
        $searchTerm = '%' . $searchTerm . '%';
        
        $sql = "SELECT * FROM learners 
                WHERE parent_id = :parent_id 
                AND (
                    CONCAT(first_name, ' ', IFNULL(middle_name, ''), ' ', last_name) LIKE :search_term
                    OR CONCAT(first_name, ' ', last_name) LIKE :search_term
                    OR first_name LIKE :search_term
                    OR last_name LIKE :search_term
                )
                ORDER BY last_name ASC, first_name ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':parent_id' => $parentId,
            ':search_term' => $searchTerm
        ]);

        return $stmt->fetchAll(PDO::FETCH_OBJ);
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

    /**
     * Get all enrolled learners with parent information
     */
    public function getAllEnrolled()
    {
        $sql = "SELECT l.*, 
                       u.fullname as parent_name, 
                       u.email as parent_email,
                       CONCAT(l.first_name, ' ', IFNULL(l.middle_name, ''), ' ', l.last_name, ' ', IFNULL(l.suffix, '')) as full_name
                FROM learners l 
                LEFT JOIN users u ON l.parent_id = u.id 
                WHERE l.status = 'enrolled'
                ORDER BY l.last_name ASC, l.first_name ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Generate unique 12-digit LRN (Learner Reference Number)
     * Format: YYYYMM + 6 random digits
     * Example: 202604123456 (April 2026 + random 6 digits)
     */
    public function generateLRN()
    {
        $maxAttempts = 100;
        $attempt = 0;

        do {
            // Get current year and month
            $yearMonth = date('Ym'); // Format: YYYYMM (e.g., 202604)
            
            // Generate 6 random digits
            $randomDigits = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
            
            // Combine to create 12-digit LRN
            $lrn = $yearMonth . $randomDigits;
            
            // Check if LRN already exists
            if (!$this->checkLRNExists($lrn)) {
                return $lrn;
            }
            
            $attempt++;
        } while ($attempt < $maxAttempts);

        // If we couldn't generate a unique LRN after max attempts, throw error
        throw new Exception("Failed to generate unique LRN after {$maxAttempts} attempts");
    }

    /**
     * Check if LRN already exists in database
     */
    public function checkLRNExists($lrn)
    {
        $sql = "SELECT COUNT(*) as count FROM learners WHERE lrn = :lrn";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':lrn' => $lrn]);
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        
        return $result->count > 0;
    }

    /**
     * Create learner account with LRN as username
     * Default password: default123
     */
    public function createLearnerAccount($learnerId, $lrn)
    {
        // Get learner data
        $learner = $this->getById($learnerId);
        
        if (!$learner) {
            return false;
        }

        // Create user account
        $userModel = new User();
        
        $userData = [
            'first_name' => $learner->first_name,
            'middle_name' => $learner->middle_name ?? null,
            'last_name' => $learner->last_name,
            'suffix' => $learner->suffix ?? null,
            'email' => $lrn . '@learner.signed.edu', // Use LRN as email prefix
            'password' => password_hash('default123', PASSWORD_DEFAULT),
            'role' => 'learner',
            'is_verified' => 1,
            'auth_provider' => 'local'
        ];
        
        $userId = $userModel->createUser($userData);
        
        if (!$userId) {
            return false;
        }

        // Update learner record with user_id and LRN
        $sql = "UPDATE learners 
                SET user_id = :user_id, 
                    lrn = :lrn, 
                    updated_at = CURRENT_TIMESTAMP 
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        
        $result = $stmt->execute([
            ':user_id' => $userId,
            ':lrn' => $lrn,
            ':id' => $learnerId
        ]);

        // Auto-unlock assessment for parent
        if ($result) {
            $assessmentModel = new Assessment();
            $assessmentModel->unlockAssessment($learnerId);
        }

        return $result ? $userId : false;
    }

    /**
     * Update learner with LRN
     */
    public function updateLRN($learnerId, $lrn)
    {
        $sql = "UPDATE learners 
                SET lrn = :lrn, 
                    updated_at = CURRENT_TIMESTAMP 
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute([
            ':lrn' => $lrn,
            ':id' => $learnerId
        ]);
    }

    /**
     * Log LRN generation
     */
    public function logLRNGeneration($lrn, $learnerId, $enrollmentId, $generatedBy)
    {
        $sql = "INSERT INTO lrn_generation_log (lrn, learner_id, enrollment_id, generated_by)
                VALUES (:lrn, :learner_id, :enrollment_id, :generated_by)";

        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute([
            ':lrn' => $lrn,
            ':learner_id' => $learnerId,
            ':enrollment_id' => $enrollmentId,
            ':generated_by' => $generatedBy
        ]);
    }

    /**
     * Validate LRN login
     */
    public function validateLRNLogin($lrn, $password)
    {
        // Get learner by LRN
        $learner = $this->getByLRN($lrn);
        
        if (!$learner || !$learner->user_id) {
            return false;
        }

        // Get user account
        $userModel = new User();
        $user = $userModel->getUserById($learner->user_id);
        
        if (!$user) {
            return false;
        }

        // Verify password
        if (password_verify($password, $user->password)) {
            return $user;
        }

        return false;
    }

    /**
     * Get learner by enrollment ID
     */
    public function getByEnrollment($enrollmentId)
    {
        $sql = "SELECT l.* FROM learners l
                JOIN enrollments e ON l.first_name = e.learner_first_name 
                    AND l.last_name = e.learner_last_name
                    AND l.parent_id = e.parent_id
                WHERE e.id = :enrollment_id
                LIMIT 1";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':enrollment_id' => $enrollmentId]);
        
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    /**
     * Get previous learner record (for returning students)
     */
    public function getPreviousLearner($learnerId)
    {
        $sql = "SELECT l.* FROM learners l
                JOIN learners current ON current.previous_learner_id = l.id
                WHERE current.id = :learner_id
                LIMIT 1";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':learner_id' => $learnerId]);
        
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    /**
     * Check if learner is a returning student
     */
    public function isReturningStudent($learnerId)
    {
        $sql = "SELECT previous_learner_id FROM learners WHERE id = :learner_id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':learner_id' => $learnerId]);
        
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        
        return $result && $result->previous_learner_id !== null;
    }
}
