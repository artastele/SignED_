<?php

// Load Learner model if not already loaded
if (!class_exists('Learner')) {
    require_once __DIR__ . '/Learner.php';
}

// Load Enrollment model if not already loaded
if (!class_exists('Enrollment')) {
    require_once __DIR__ . '/Enrollment.php';
}

class Assessment extends Model
{
    /**
     * Create new assessment for learner
     */
    public function create($learnerId, $status = 'locked')
    {
        $sql = "INSERT INTO assessments (learner_id, status, created_at)
                VALUES (:learner_id, :status, CURRENT_TIMESTAMP)";

        $stmt = $this->db->prepare($sql);

        $result = $stmt->execute([
            ':learner_id' => $learnerId,
            ':status' => $status
        ]);

        return $result ? $this->db->lastInsertId() : false;
    }

    /**
     * Unlock assessment for parent (triggered on enrollment approval)
     */
    public function unlockAssessment($learnerId)
    {
        // Check if assessment already exists
        $existing = $this->getByLearnerId($learnerId);
        
        if ($existing) {
            // Update existing assessment to unlocked
            $sql = "UPDATE assessments 
                    SET status = 'unlocked', 
                        updated_at = CURRENT_TIMESTAMP 
                    WHERE learner_id = :learner_id";
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([':learner_id' => $learnerId]);
        } else {
            // Create new assessment with unlocked status
            return $this->create($learnerId, 'unlocked');
        }
    }

    /**
     * Get assessment by learner ID
     */
    public function getByLearnerId($learnerId)
    {
        $sql = "SELECT a.*, 
                       l.first_name, l.middle_name, l.last_name, l.suffix,
                       l.lrn, l.date_of_birth, l.grade_level,
                       u.fullname as parent_name, u.email as parent_email
                FROM assessments a
                JOIN learners l ON a.learner_id = l.id
                JOIN users u ON l.parent_id = u.id
                WHERE a.learner_id = :learner_id
                ORDER BY a.created_at DESC
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':learner_id' => $learnerId]);

        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    /**
     * Get assessment by ID
     */
    public function getById($assessmentId)
    {
        $sql = "SELECT a.*, 
                       l.first_name, l.middle_name, l.last_name, l.suffix,
                       l.lrn, l.date_of_birth, l.grade_level, l.parent_id,
                       u.fullname as parent_name, u.email as parent_email
                FROM assessments a
                JOIN learners l ON a.learner_id = l.id
                JOIN users u ON l.parent_id = u.id
                WHERE a.id = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $assessmentId]);

        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    /**
     * Auto-fill learner background from BEEF data
     */
    public function autoFillLearnerBackground($learnerId)
    {
        // Get learner data
        $learnerModel = new Learner();
        $learner = $learnerModel->getById($learnerId);
        
        if (!$learner) {
            return null;
        }

        // Get enrollment data with BEEF
        $enrollmentModel = new Enrollment();
        $enrollment = $enrollmentModel->getLatestByLearner($learnerId);
        
        $beefData = $enrollment ? json_decode($enrollment->beef_data, true) : [];

        // Build learner background data
        $background = [
            'personal_info' => [
                'first_name' => $learner->first_name ?? '',
                'middle_name' => $learner->middle_name ?? '',
                'last_name' => $learner->last_name ?? '',
                'suffix' => $learner->suffix ?? '',
                'full_name' => trim($learner->first_name . ' ' . ($learner->middle_name ?? '') . ' ' . $learner->last_name . ' ' . ($learner->suffix ?? '')),
                'lrn' => $learner->lrn ?? 'Not assigned',
                'date_of_birth' => $learner->date_of_birth,
                'age' => $this->calculateAge($learner->date_of_birth),
                'sex' => $beefData['sex'] ?? 'N/A',
                'religion' => $beefData['religion'] ?? 'N/A',
                'grade_level' => $learner->grade_level,
                'address' => $beefData['address'] ?? $enrollment->parent_address ?? 'N/A',
                'school_year' => date('Y') . '-' . (date('Y') + 1),
                'school' => 'SignED SPED',
                'adviser' => 'TBA'
            ],
            'family_background' => [
                'father_name' => $beefData['father_name'] ?? 'N/A',
                'father_contact' => $beefData['father_contact'] ?? 'N/A',
                'father_occupation' => $beefData['father_occupation'] ?? 'N/A',
                'mother_name' => $beefData['mother_name'] ?? 'N/A',
                'mother_contact' => $beefData['mother_contact'] ?? 'N/A',
                'mother_occupation' => $beefData['mother_occupation'] ?? 'N/A',
                'guardian_name' => $beefData['guardian_name'] ?? 'N/A',
                'guardian_contact' => $beefData['guardian_contact'] ?? $enrollment->parent_contact_number ?? 'N/A',
                'guardian_occupation' => $beefData['guardian_occupation'] ?? 'N/A',
                'parent_guardian' => $learner->parent_name ?? 'N/A',
                'contact_number' => $beefData['parent_contact'] ?? $enrollment->parent_contact_number ?? 'N/A',
                'email' => $learner->parent_email ?? 'N/A'
            ],
            'medical_history' => [
                'disability_type' => $beefData['disability_type'] ?? 'N/A',
                'medical_conditions' => $beefData['medical_conditions'] ?? 'N/A',
                'medications' => $beefData['medications'] ?? 'None',
                'allergies' => $beefData['allergies'] ?? 'None'
            ]
        ];

        return $background;
    }

    /**
     * Calculate age from date of birth
     */
    private function calculateAge($dateOfBirth)
    {
        if (!$dateOfBirth) {
            return 'N/A';
        }

        $dob = new DateTime($dateOfBirth);
        $now = new DateTime();
        $age = $dob->diff($now)->y;

        return $age . ' years old';
    }

    /**
     * Save assessment draft
     */
    public function saveAssessmentDraft($assessmentId, $data)
    {
        $sql = "UPDATE assessments 
                SET learner_background = :learner_background,
                    education_history = :education_history,
                    additional_info = :additional_info,
                    status = 'draft',
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':learner_background' => json_encode($data['learner_background'] ?? []),
            ':education_history' => json_encode($data['education_history'] ?? []),
            ':additional_info' => json_encode($data['additional_info'] ?? []),
            ':id' => $assessmentId
        ]);
    }

    /**
     * Submit assessment (mark as complete)
     */
    public function submitAssessment($assessmentId)
    {
        $sql = "UPDATE assessments 
                SET status = 'submitted',
                    submitted_by_parent = TRUE,
                    parent_submitted_at = CURRENT_TIMESTAMP,
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([':id' => $assessmentId]);
    }

    /**
     * Validate if assessment is complete (Education History required)
     */
    public function validateAssessmentComplete($data)
    {
        $educationHistory = $data['education_history'] ?? [];

        // Check if required fields in education history are filled
        $requiredFields = [
            'previous_school',
            'previous_grade_level',
            'with_iep',
            'with_support_services'
        ];

        foreach ($requiredFields as $field) {
            if (empty($educationHistory[$field])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get assessments for SPED review (submitted status)
     */
    public function getSubmittedAssessments()
    {
        $sql = "SELECT a.*, 
                       l.first_name, l.middle_name, l.last_name, l.lrn,
                       l.grade_level,
                       u.fullname as parent_name,
                       a.parent_submitted_at
                FROM assessments a
                JOIN learners l ON a.learner_id = l.id
                JOIN users u ON l.parent_id = u.id
                WHERE a.status = 'submitted'
                ORDER BY a.parent_submitted_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Mark assessment as reviewed by SPED
     */
    public function markAsReviewed($assessmentId)
    {
        $sql = "UPDATE assessments 
                SET status = 'reviewed',
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([':id' => $assessmentId]);
    }

    /**
     * Get assessments by parent ID
     */
    public function getByParentId($parentId)
    {
        $sql = "SELECT a.*, 
                       l.first_name, l.middle_name, l.last_name, l.lrn,
                       l.grade_level
                FROM assessments a
                JOIN learners l ON a.learner_id = l.id
                WHERE l.parent_id = :parent_id
                ORDER BY a.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':parent_id' => $parentId]);

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Create notification for assessment event
     */
    public function createNotification($assessmentId, $userId, $notificationType)
    {
        $sql = "INSERT INTO assessment_notifications (assessment_id, user_id, notification_type)
                VALUES (:assessment_id, :user_id, :notification_type)";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':assessment_id' => $assessmentId,
            ':user_id' => $userId,
            ':notification_type' => $notificationType
        ]);
    }

    /**
     * Get unread assessment notifications for user
     */
    public function getUnreadNotifications($userId)
    {
        $sql = "SELECT an.*, 
                       a.learner_id,
                       l.first_name, l.last_name
                FROM assessment_notifications an
                JOIN assessments a ON an.assessment_id = a.id
                JOIN learners l ON a.learner_id = l.id
                WHERE an.user_id = :user_id 
                AND an.is_read = FALSE
                ORDER BY an.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $userId]);

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Get count of submitted assessments (for SPED dashboard)
     */
    public function getSubmittedCount()
    {
        $sql = "SELECT COUNT(*) as count FROM assessments WHERE status = 'submitted'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        return $result->count;
    }

    /**
     * Get count of pending assessments (submitted but not reviewed)
     */
    public function getPendingCount()
    {
        $sql = "SELECT COUNT(*) as count FROM assessments WHERE status = 'submitted'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        return $result->count;
    }
}
