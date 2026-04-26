<?php

class IepMeeting extends Model
{
    /**
     * Schedule new IEP meeting
     */
    public function schedule($learnerId, $scheduledBy, $meetingDate, $location, $participants)
    {
        try {
            $this->db->beginTransaction();

            // Create meeting
            $sql = "INSERT INTO iep_meetings (learner_id, scheduled_by, meeting_date, location)
                    VALUES (:learner_id, :scheduled_by, :meeting_date, :location)";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':learner_id' => $learnerId,
                ':scheduled_by' => $scheduledBy,
                ':meeting_date' => $meetingDate,
                ':location' => $location
            ]);

            $meetingId = $this->db->lastInsertId();

            // Add participants
            foreach ($participants as $participant) {
                $this->addParticipant($meetingId, $participant['user_id'], $participant['role']);
            }

            $this->db->commit();
            return $meetingId;

        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    /**
     * Add participant to meeting
     */
    public function addParticipant($meetingId, $userId, $role)
    {
        $sql = "INSERT INTO iep_meeting_participants (meeting_id, user_id, role)
                VALUES (:meeting_id, :user_id, :role)
                ON DUPLICATE KEY UPDATE role = VALUES(role)";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':meeting_id' => $meetingId,
            ':user_id' => $userId,
            ':role' => $role
        ]);
    }

    /**
     * Confirm participant attendance
     */
    public function confirmParticipant($meetingId, $userId, $status = 'confirmed')
    {
        $sql = "UPDATE iep_meeting_participants 
                SET attendance_status = :status 
                WHERE meeting_id = :meeting_id AND user_id = :user_id";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':status' => $status,
            ':meeting_id' => $meetingId,
            ':user_id' => $userId
        ]);
    }

    /**
     * Record meeting completion
     */
    public function recordCompletion($meetingId, $notes, $signatures = [])
    {
        try {
            $this->db->beginTransaction();

            // Update meeting status
            $sql = "UPDATE iep_meetings 
                    SET status = 'completed', 
                        meeting_notes = :notes, 
                        completed_at = CURRENT_TIMESTAMP 
                    WHERE id = :id";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':notes' => $notes,
                ':id' => $meetingId
            ]);

            // Record signatures
            foreach ($signatures as $signature) {
                $this->recordSignature($meetingId, $signature['user_id'], $signature['signature_data']);
            }

            $this->db->commit();
            return true;

        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    /**
     * Record participant signature
     */
    public function recordSignature($meetingId, $userId, $signatureData)
    {
        $sql = "UPDATE iep_meeting_participants 
                SET signature_data = :signature_data, 
                    signed_at = CURRENT_TIMESTAMP,
                    attendance_status = 'attended'
                WHERE meeting_id = :meeting_id AND user_id = :user_id";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':signature_data' => $signatureData,
            ':meeting_id' => $meetingId,
            ':user_id' => $userId
        ]);
    }

    /**
     * Get meetings by status
     */
    public function getByStatus($status)
    {
        $sql = "SELECT m.*, l.first_name, l.last_name, u.fullname as scheduled_by_name
                FROM iep_meetings m
                JOIN learners l ON m.learner_id = l.id
                JOIN users u ON m.scheduled_by = u.id
                WHERE m.status = :status
                ORDER BY m.meeting_date ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':status' => $status]);

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Get meeting by ID with participants
     */
    public function getById($meetingId)
    {
        $sql = "SELECT m.*, l.first_name, l.last_name, u.fullname as scheduled_by_name
                FROM iep_meetings m
                JOIN learners l ON m.learner_id = l.id
                JOIN users u ON m.scheduled_by = u.id
                WHERE m.id = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $meetingId]);

        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    /**
     * Get meeting participants
     */
    public function getParticipants($meetingId)
    {
        $sql = "SELECT p.*, u.fullname, u.email
                FROM iep_meeting_participants p
                JOIN users u ON p.user_id = u.id
                WHERE p.meeting_id = :meeting_id
                ORDER BY p.role";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':meeting_id' => $meetingId]);

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Get meetings for user
     */
    public function getForUser($userId)
    {
        $sql = "SELECT m.*, l.first_name, l.last_name, p.role, p.attendance_status
                FROM iep_meetings m
                JOIN learners l ON m.learner_id = l.id
                JOIN iep_meeting_participants p ON m.id = p.meeting_id
                WHERE p.user_id = :user_id
                ORDER BY m.meeting_date DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $userId]);

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Check if all required participants confirmed
     */
    public function allParticipantsConfirmed($meetingId)
    {
        $sql = "SELECT COUNT(*) as total, 
                       SUM(CASE WHEN attendance_status = 'confirmed' THEN 1 ELSE 0 END) as confirmed
                FROM iep_meeting_participants 
                WHERE meeting_id = :meeting_id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':meeting_id' => $meetingId]);

        $result = $stmt->fetch(PDO::FETCH_OBJ);
        return $result->total == $result->confirmed;
    }

    /**
     * Get upcoming meetings
     */
    public function getUpcoming($limit = 10)
    {
        $sql = "SELECT m.*, l.first_name, l.last_name, u.fullname as scheduled_by_name
                FROM iep_meetings m
                JOIN learners l ON m.learner_id = l.id
                JOIN users u ON m.scheduled_by = u.id
                WHERE m.meeting_date >= NOW() AND m.status IN ('scheduled', 'confirmed')
                ORDER BY m.meeting_date ASC
                LIMIT :limit";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
}