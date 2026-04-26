<?php

class LearningMaterial extends Model
{
    /**
     * Upload learning material for IEP objective
     */
    public function upload($iepId, $uploadedBy, $materialData, $fileData)
    {
        $sql = "INSERT INTO learning_materials 
                (iep_id, uploaded_by, title, description, iep_objective, 
                 original_filename, encrypted_filename, file_size, mime_type, 
                 encryption_key_id, due_date)
                VALUES (:iep_id, :uploaded_by, :title, :description, :iep_objective,
                        :original_filename, :encrypted_filename, :file_size, :mime_type,
                        :encryption_key_id, :due_date)";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':iep_id' => $iepId,
            ':uploaded_by' => $uploadedBy,
            ':title' => $materialData['title'],
            ':description' => $materialData['description'],
            ':iep_objective' => $materialData['iep_objective'],
            ':original_filename' => $fileData['original_filename'],
            ':encrypted_filename' => $fileData['encrypted_filename'],
            ':file_size' => $fileData['file_size'],
            ':mime_type' => $fileData['mime_type'],
            ':encryption_key_id' => $fileData['encryption_key_id'],
            ':due_date' => $materialData['due_date']
        ]);
    }

    /**
     * Get materials assigned to learner
     */
    public function getByLearner($learnerId)
    {
        $sql = "SELECT lm.*, i.learner_id, u.fullname as uploaded_by_name
                FROM learning_materials lm
                JOIN ieps i ON lm.iep_id = i.id
                JOIN users u ON lm.uploaded_by = u.id
                WHERE i.learner_id = :learner_id
                AND i.status = 'active'
                ORDER BY lm.due_date ASC, lm.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':learner_id' => $learnerId]);

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Get materials by IEP
     */
    public function getByIep($iepId)
    {
        $sql = "SELECT lm.*, u.fullname as uploaded_by_name
                FROM learning_materials lm
                JOIN users u ON lm.uploaded_by = u.id
                WHERE lm.iep_id = :iep_id
                ORDER BY lm.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':iep_id' => $iepId]);

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Get material by ID
     */
    public function getById($materialId)
    {
        $sql = "SELECT lm.*, i.learner_id, l.first_name, l.last_name, u.fullname as uploaded_by_name
                FROM learning_materials lm
                JOIN ieps i ON lm.iep_id = i.id
                JOIN learners l ON i.learner_id = l.id
                JOIN users u ON lm.uploaded_by = u.id
                WHERE lm.id = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $materialId]);

        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    /**
     * Submit learner work for material
     */
    public function submitWork($materialId, $learnerId, $fileData, $notes = null)
    {
        $sql = "INSERT INTO learner_submissions 
                (material_id, learner_id, original_filename, encrypted_filename, 
                 file_size, mime_type, encryption_key_id, submission_notes)
                VALUES (:material_id, :learner_id, :original_filename, :encrypted_filename,
                        :file_size, :mime_type, :encryption_key_id, :submission_notes)";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':material_id' => $materialId,
            ':learner_id' => $learnerId,
            ':original_filename' => $fileData['original_filename'],
            ':encrypted_filename' => $fileData['encrypted_filename'],
            ':file_size' => $fileData['file_size'],
            ':mime_type' => $fileData['mime_type'],
            ':encryption_key_id' => $fileData['encryption_key_id'],
            ':submission_notes' => $notes
        ]);
    }

    /**
     * Get submissions for material
     */
    public function getSubmissions($materialId)
    {
        $sql = "SELECT ls.*, l.first_name, l.last_name, u.fullname as reviewed_by_name
                FROM learner_submissions ls
                JOIN learners l ON ls.learner_id = l.id
                LEFT JOIN users u ON ls.reviewed_by = u.id
                WHERE ls.material_id = :material_id
                ORDER BY ls.submitted_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':material_id' => $materialId]);

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Get submissions by learner
     */
    public function getSubmissionsByLearner($learnerId)
    {
        $sql = "SELECT ls.*, lm.title as material_title, u.fullname as reviewed_by_name
                FROM learner_submissions ls
                JOIN learning_materials lm ON ls.material_id = lm.id
                LEFT JOIN users u ON ls.reviewed_by = u.id
                WHERE ls.learner_id = :learner_id
                ORDER BY ls.submitted_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':learner_id' => $learnerId]);

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Review submission
     */
    public function reviewSubmission($submissionId, $reviewedBy, $reviewNotes)
    {
        $sql = "UPDATE learner_submissions 
                SET reviewed_by = :reviewed_by, 
                    review_notes = :review_notes, 
                    reviewed_at = CURRENT_TIMESTAMP
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':reviewed_by' => $reviewedBy,
            ':review_notes' => $reviewNotes,
            ':id' => $submissionId
        ]);
    }

    /**
     * Get materials uploaded by user
     */
    public function getByUploader($uploaderId)
    {
        $sql = "SELECT lm.*, i.learner_id, l.first_name, l.last_name
                FROM learning_materials lm
                JOIN ieps i ON lm.iep_id = i.id
                JOIN learners l ON i.learner_id = l.id
                WHERE lm.uploaded_by = :uploader_id
                ORDER BY lm.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':uploader_id' => $uploaderId]);

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Update material
     */
    public function update($materialId, $materialData)
    {
        $sql = "UPDATE learning_materials 
                SET title = :title, 
                    description = :description, 
                    iep_objective = :iep_objective,
                    due_date = :due_date
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':title' => $materialData['title'],
            ':description' => $materialData['description'],
            ':iep_objective' => $materialData['iep_objective'],
            ':due_date' => $materialData['due_date'],
            ':id' => $materialId
        ]);
    }

    /**
     * Get materials with submission status for learner
     */
    public function getMaterialsWithSubmissionStatus($learnerId)
    {
        $sql = "SELECT lm.*, i.learner_id, u.fullname as uploaded_by_name,
                       CASE WHEN ls.id IS NOT NULL THEN 1 ELSE 0 END as has_submission,
                       ls.submitted_at, ls.reviewed_at, ls.review_notes
                FROM learning_materials lm
                JOIN ieps i ON lm.iep_id = i.id
                JOIN users u ON lm.uploaded_by = u.id
                LEFT JOIN learner_submissions ls ON lm.id = ls.material_id AND ls.learner_id = :learner_id
                WHERE i.learner_id = :learner_id
                AND i.status = 'active'
                ORDER BY lm.due_date ASC, lm.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':learner_id' => $learnerId]);

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Get overdue materials for learner
     */
    public function getOverdueMaterials($learnerId)
    {
        $sql = "SELECT lm.*, i.learner_id, u.fullname as uploaded_by_name
                FROM learning_materials lm
                JOIN ieps i ON lm.iep_id = i.id
                JOIN users u ON lm.uploaded_by = u.id
                LEFT JOIN learner_submissions ls ON lm.id = ls.material_id AND ls.learner_id = :learner_id
                WHERE i.learner_id = :learner_id
                AND i.status = 'active'
                AND lm.due_date < CURDATE()
                AND ls.id IS NULL
                ORDER BY lm.due_date ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':learner_id' => $learnerId]);

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Get upcoming materials (due within next 7 days)
     */
    public function getUpcomingMaterials($learnerId)
    {
        $sql = "SELECT lm.*, i.learner_id, u.fullname as uploaded_by_name
                FROM learning_materials lm
                JOIN ieps i ON lm.iep_id = i.id
                JOIN users u ON lm.uploaded_by = u.id
                LEFT JOIN learner_submissions ls ON lm.id = ls.material_id AND ls.learner_id = :learner_id
                WHERE i.learner_id = :learner_id
                AND i.status = 'active'
                AND lm.due_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)
                AND ls.id IS NULL
                ORDER BY lm.due_date ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':learner_id' => $learnerId]);

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Get progress statistics for learner
     */
    public function getProgressStats($learnerId)
    {
        $sql = "SELECT 
                    COUNT(lm.id) as total_materials,
                    COUNT(ls.id) as submitted_materials,
                    COUNT(CASE WHEN lm.due_date < CURDATE() AND ls.id IS NULL THEN 1 END) as overdue_materials,
                    COUNT(CASE WHEN lm.due_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY) AND ls.id IS NULL THEN 1 END) as upcoming_materials
                FROM learning_materials lm
                JOIN ieps i ON lm.iep_id = i.id
                LEFT JOIN learner_submissions ls ON lm.id = ls.material_id AND ls.learner_id = :learner_id
                WHERE i.learner_id = :learner_id
                AND i.status = 'active'";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':learner_id' => $learnerId]);

        $result = $stmt->fetch(PDO::FETCH_OBJ);
        
        if ($result) {
            $result->completion_percentage = $result->total_materials > 0 
                ? round(($result->submitted_materials / $result->total_materials) * 100) 
                : 0;
        }

        return $result;
    }

    /**
     * Get materials by objective with progress
     */
    public function getMaterialsByObjectiveWithProgress($learnerId)
    {
        $sql = "SELECT lm.iep_objective,
                       COUNT(lm.id) as total_materials,
                       COUNT(ls.id) as submitted_materials
                FROM learning_materials lm
                JOIN ieps i ON lm.iep_id = i.id
                LEFT JOIN learner_submissions ls ON lm.id = ls.material_id AND ls.learner_id = :learner_id
                WHERE i.learner_id = :learner_id
                AND i.status = 'active'
                GROUP BY lm.iep_objective
                ORDER BY lm.iep_objective";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':learner_id' => $learnerId]);

        $results = $stmt->fetchAll(PDO::FETCH_OBJ);
        
        foreach ($results as $result) {
            $result->completion_percentage = $result->total_materials > 0 
                ? round(($result->submitted_materials / $result->total_materials) * 100) 
                : 0;
        }

        return $results;
    }

    /**
     * Get submission by ID with material info
     */
    public function getSubmissionById($submissionId)
    {
        $sql = "SELECT ls.*, lm.title as material_title, lm.iep_objective,
                       l.first_name, l.last_name, u.fullname as reviewed_by_name
                FROM learner_submissions ls
                JOIN learning_materials lm ON ls.material_id = lm.id
                JOIN learners l ON ls.learner_id = l.id
                LEFT JOIN users u ON ls.reviewed_by = u.id
                WHERE ls.id = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $submissionId]);

        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    /**
     * Get unreviewed submissions for teacher
     */
    public function getUnreviewedSubmissions($teacherId)
    {
        $sql = "SELECT ls.*, lm.title as material_title, l.first_name, l.last_name
                FROM learner_submissions ls
                JOIN learning_materials lm ON ls.material_id = lm.id
                JOIN learners l ON ls.learner_id = l.id
                WHERE lm.uploaded_by = :teacher_id
                AND ls.reviewed_by IS NULL
                ORDER BY ls.submitted_at ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':teacher_id' => $teacherId]);

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Delete material and associated submissions
     */
    public function delete($materialId)
    {
        try {
            $this->db->beginTransaction();

            // Delete associated submissions first
            $sql = "DELETE FROM learner_submissions WHERE material_id = :material_id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':material_id' => $materialId]);

            // Delete the material
            $sql = "DELETE FROM learning_materials WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([':id' => $materialId]);

            $this->db->commit();
            return $result;

        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }
}