<?php

class IepFeedback extends Model
{
    /**
     * Create feedback
     */
    public function create($feedbackData)
    {
        $sql = "INSERT INTO iep_feedback 
                (iep_id, user_id, user_role, feedback_type, feedback, status)
                VALUES (:iep_id, :user_id, :user_role, :feedback_type, :feedback, 'pending')";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':iep_id' => $feedbackData['iep_id'],
            ':user_id' => $feedbackData['user_id'],
            ':user_role' => $feedbackData['user_role'],
            ':feedback_type' => $feedbackData['feedback_type'],
            ':feedback' => $feedbackData['feedback']
        ]);
    }

    /**
     * Get feedback by IEP ID
     */
    public function getByIepId($iepId)
    {
        $sql = "SELECT f.*, u.fullname as user_name
                FROM iep_feedback f
                JOIN users u ON f.user_id = u.id
                WHERE f.iep_id = :iep_id
                ORDER BY f.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':iep_id' => $iepId]);

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Get feedback by user
     */
    public function getByUserId($userId)
    {
        $sql = "SELECT f.*, i.learner_id, l.first_name, l.last_name
                FROM iep_feedback f
                JOIN ieps i ON f.iep_id = i.id
                JOIN learners l ON i.learner_id = l.id
                WHERE f.user_id = :user_id
                ORDER BY f.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $userId]);

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Update feedback status
     */
    public function updateStatus($feedbackId, $status)
    {
        $sql = "UPDATE iep_feedback 
                SET status = :status,
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':status' => $status,
            ':id' => $feedbackId
        ]);
    }

    /**
     * Get pending feedback count for user
     */
    public function getPendingCountByUser($userId)
    {
        $sql = "SELECT COUNT(*) as count 
                FROM iep_feedback 
                WHERE user_id = :user_id AND status = 'pending'";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        $result = $stmt->fetch(PDO::FETCH_OBJ);

        return $result->count;
    }
}
