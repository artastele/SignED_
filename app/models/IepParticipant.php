<?php

class IepParticipant extends Model
{
    /**
     * Add participant to meeting
     */
    public function add($participantData)
    {
        $sql = "INSERT INTO iep_meeting_participants 
                (meeting_id, user_id, participant_type, name, email, is_required, invitation_status)
                VALUES (:meeting_id, :user_id, :participant_type, :name, :email, :is_required, 'pending')";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':meeting_id' => $participantData['meeting_id'],
            ':user_id' => $participantData['user_id'] ?? null,
            ':participant_type' => $participantData['participant_type'],
            ':name' => $participantData['name'],
            ':email' => $participantData['email'] ?? null,
            ':is_required' => $participantData['is_required'] ?? 0
        ]);
    }

    /**
     * Get participants by meeting ID
     */
    public function getByMeetingId($meetingId)
    {
        $sql = "SELECT p.*, u.fullname as user_fullname, u.email as user_email
                FROM iep_meeting_participants p
                LEFT JOIN users u ON p.user_id = u.id
                WHERE p.meeting_id = :meeting_id
                ORDER BY p.is_required DESC, p.participant_type ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':meeting_id' => $meetingId]);

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Get participant by ID
     */
    public function getById($participantId)
    {
        $sql = "SELECT p.*, m.meeting_date, m.meeting_time, m.location,
                       i.learner_id, l.first_name, l.last_name
                FROM iep_meeting_participants p
                JOIN iep_meetings m ON p.meeting_id = m.id
                JOIN ieps i ON m.iep_id = i.id
                JOIN learners l ON i.learner_id = l.id
                WHERE p.id = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $participantId]);

        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    /**
     * Update invitation status
     */
    public function updateStatus($participantId, $status, $declineReason = null)
    {
        $sql = "UPDATE iep_meeting_participants 
                SET invitation_status = :status,
                    confirmed_at = " . ($status === 'confirmed' ? 'CURRENT_TIMESTAMP' : 'NULL') . ",
                    decline_reason = :decline_reason,
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':status' => $status,
            ':decline_reason' => $declineReason,
            ':id' => $participantId
        ]);
    }

    /**
     * Get participants by user ID
     */
    public function getByUserId($userId)
    {
        $sql = "SELECT p.*, m.meeting_date, m.meeting_time, m.location, m.status as meeting_status,
                       i.learner_id, l.first_name, l.last_name
                FROM iep_meeting_participants p
                JOIN iep_meetings m ON p.meeting_id = m.id
                JOIN ieps i ON m.iep_id = i.id
                JOIN learners l ON i.learner_id = l.id
                WHERE p.user_id = :user_id
                ORDER BY m.meeting_date DESC, m.meeting_time DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $userId]);

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Check if all required participants confirmed
     */
    public function allRequiredConfirmed($meetingId)
    {
        $sql = "SELECT COUNT(*) as count 
                FROM iep_meeting_participants 
                WHERE meeting_id = :meeting_id 
                AND is_required = 1 
                AND invitation_status != 'confirmed'";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':meeting_id' => $meetingId]);
        $result = $stmt->fetch(PDO::FETCH_OBJ);

        return $result->count == 0;
    }

    /**
     * Check if parent declined
     */
    public function parentDeclined($meetingId)
    {
        $sql = "SELECT COUNT(*) as count 
                FROM iep_meeting_participants 
                WHERE meeting_id = :meeting_id 
                AND participant_type = 'parent' 
                AND invitation_status = 'declined'";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':meeting_id' => $meetingId]);
        $result = $stmt->fetch(PDO::FETCH_OBJ);

        return $result->count > 0;
    }

    /**
     * Get pending invitations count for user
     */
    public function getPendingCountByUser($userId)
    {
        $sql = "SELECT COUNT(*) as count 
                FROM iep_meeting_participants 
                WHERE user_id = :user_id 
                AND invitation_status = 'pending'";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        $result = $stmt->fetch(PDO::FETCH_OBJ);

        return $result->count;
    }

    /**
     * Delete participants by meeting ID
     */
    public function deleteByMeetingId($meetingId)
    {
        $sql = "DELETE FROM iep_meeting_participants WHERE meeting_id = :meeting_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':meeting_id' => $meetingId]);
    }
}
