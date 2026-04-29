<?php

class Enrollment extends Model
{
    /**
     * Create new enrollment
     */
    public function create($parentId, $learnerData)
    {
        $sql = "INSERT INTO enrollments (parent_id, learner_first_name, learner_last_name, learner_dob, learner_grade)
                VALUES (:parent_id, :first_name, :last_name, :dob, :grade)";

        $stmt = $this->db->prepare($sql);

        $result = $stmt->execute([
            ':parent_id' => $parentId,
            ':first_name' => $learnerData['first_name'],
            ':last_name' => $learnerData['last_name'],
            ':dob' => $learnerData['date_of_birth'],
            ':grade' => $learnerData['grade_level']
        ]);

        return $result ? $this->db->lastInsertId() : false;
    }

    /**
     * Create new enrollment with BEEF data
     */
    public function createWithBeef($enrollmentData)
    {
        $sql = "INSERT INTO enrollments (
                    parent_id, learner_first_name, learner_last_name, learner_dob, learner_grade,
                    beef_data, is_returning_student, previous_lrn, parent_contact_number, parent_address
                ) VALUES (
                    :parent_id, :first_name, :last_name, :dob, :grade,
                    :beef_data, :is_returning, :previous_lrn, :contact, :address
                )";

        $stmt = $this->db->prepare($sql);

        $result = $stmt->execute([
            ':parent_id' => $enrollmentData['parent_id'],
            ':first_name' => $enrollmentData['learner_first_name'],
            ':last_name' => $enrollmentData['learner_last_name'],
            ':dob' => $enrollmentData['learner_dob'],
            ':grade' => $enrollmentData['learner_grade'],
            ':beef_data' => $enrollmentData['beef_data'],
            ':is_returning' => $enrollmentData['is_returning_student'],
            ':previous_lrn' => $enrollmentData['previous_lrn'],
            ':contact' => $enrollmentData['parent_contact_number'],
            ':address' => $enrollmentData['parent_address']
        ]);

        return $result ? $this->db->lastInsertId() : false;
    }

    /**
     * Upload document for enrollment
     */
    public function uploadDocument($enrollmentId, $documentType, $fileData)
    {
        $sql = "INSERT INTO enrollment_documents 
                (enrollment_id, document_type, original_filename, encrypted_filename, file_size, mime_type, encryption_key_id)
                VALUES (:enrollment_id, :document_type, :original_filename, :encrypted_filename, :file_size, :mime_type, :encryption_key_id)
                ON DUPLICATE KEY UPDATE 
                original_filename = VALUES(original_filename),
                encrypted_filename = VALUES(encrypted_filename),
                file_size = VALUES(file_size),
                mime_type = VALUES(mime_type),
                encryption_key_id = VALUES(encryption_key_id),
                uploaded_at = CURRENT_TIMESTAMP";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':enrollment_id' => $enrollmentId,
            ':document_type' => $documentType,
            ':original_filename' => $fileData['original_filename'],
            ':encrypted_filename' => $fileData['encrypted_filename'],
            ':file_size' => $fileData['file_size'],
            ':mime_type' => $fileData['mime_type'],
            ':encryption_key_id' => $fileData['encryption_key_id']
        ]);
    }

    /**
     * Get enrollments by status
     */
    public function getByStatus($status)
    {
        $sql = "SELECT e.*, u.fullname as parent_name, u.email as parent_email,
                       COUNT(ed.id) as document_count
                FROM enrollments e 
                JOIN users u ON e.parent_id = u.id 
                LEFT JOIN enrollment_documents ed ON e.id = ed.enrollment_id
                WHERE e.status = :status 
                GROUP BY e.id
                ORDER BY e.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':status' => $status]);

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Update enrollment status
     */
    public function updateStatus($enrollmentId, $status, $reason = null, $verifiedBy = null)
    {
        $sql = "UPDATE enrollments 
                SET status = :status, 
                    rejection_reason = :reason,
                    verified_by = :verified_by,
                    verified_at = CASE WHEN :status IN ('approved', 'rejected') THEN CURRENT_TIMESTAMP ELSE verified_at END,
                    updated_at = CURRENT_TIMESTAMP 
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':status' => $status,
            ':reason' => $reason,
            ':verified_by' => $verifiedBy,
            ':id' => $enrollmentId
        ]);
    }

    /**
     * Get enrollment documents
     */
    public function getDocuments($enrollmentId)
    {
        $sql = "SELECT * FROM enrollment_documents WHERE enrollment_id = :enrollment_id ORDER BY document_type";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':enrollment_id' => $enrollmentId]);

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Get enrollment by ID with documents
     */
    public function getById($enrollmentId)
    {
        $sql = "SELECT e.*, u.fullname as parent_name, u.email as parent_email
                FROM enrollments e 
                JOIN users u ON e.parent_id = u.id 
                WHERE e.id = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $enrollmentId]);

        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    /**
     * Check if all required documents are uploaded
     * Only PSA Birth Certificate is required
     */
    public function hasAllDocuments($enrollmentId)
    {
        $sql = "SELECT COUNT(*) as doc_count 
                FROM enrollment_documents 
                WHERE enrollment_id = :enrollment_id 
                AND document_type = 'psa'";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':enrollment_id' => $enrollmentId]);

        $result = $stmt->fetch(PDO::FETCH_OBJ);
        return $result->doc_count >= 1; // Only PSA is required
    }

    /**
     * Get enrollments by parent
     */
    public function getByParent($parentId)
    {
        $sql = "SELECT e.*, COUNT(ed.id) as document_count
                FROM enrollments e 
                LEFT JOIN enrollment_documents ed ON e.id = ed.enrollment_id
                WHERE e.parent_id = :parent_id 
                GROUP BY e.id
                ORDER BY e.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':parent_id' => $parentId]);

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Get total enrollment count
     */
    public function getTotalCount()
    {
        $sql = "SELECT COUNT(*) as total FROM enrollments";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        return $result->total;
    }

    /**
     * Get latest enrollment by learner ID
     */
    public function getLatestByLearner($learnerId)
    {
        $sql = "SELECT e.* 
                FROM enrollments e
                JOIN learners l ON e.learner_first_name = l.first_name 
                    AND e.learner_last_name = l.last_name
                WHERE l.id = :learner_id
                ORDER BY e.created_at DESC
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':learner_id' => $learnerId]);

        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    /**
     * Get count of pending verifications
     */
    public function getPendingVerificationCount()
    {
        $sql = "SELECT COUNT(*) as count FROM enrollments WHERE status = 'pending_verification'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        return $result->count;
    }

    /**
     * Get recent enrollments
     */
    public function getRecent($limit = 5)
    {
        $sql = "SELECT e.*, u.fullname as parent_name 
                FROM enrollments e 
                JOIN users u ON e.parent_id = u.id 
                ORDER BY e.created_at DESC 
                LIMIT :limit";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Mark LRN as generated for enrollment
     */
    public function markLRNGenerated($enrollmentId)
    {
        $sql = "UPDATE enrollments 
                SET lrn_generated = TRUE, 
                    lrn_generated_at = CURRENT_TIMESTAMP 
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([':id' => $enrollmentId]);
    }
}