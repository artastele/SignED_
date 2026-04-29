<?php

class IepMeeting extends Model
{
    /**
     * Create new IEP meeting
     */
    public function create($meetingData)
    {
        $sql = "INSERT INTO iep_meetings 
                (iep_id, meeting_date, meeting_time, location, agenda, scheduled_by, status)
                VALUES (:iep_id, :meeting_date, :meeting_time, :location, :agenda, :scheduled_by, 'scheduled')";

        $stmt = $this->db->prepare($sql);

        $result = $stmt->execute([
            ':iep_id' => $meetingData['iep_id'],
            ':meeting_date' => $meetingData['meeting_date'],
            ':meeting_time' => $meetingData['meeting_time'],
            ':location' => $meetingData['location'],
            ':agenda' => $meetingData['agenda'],
            ':scheduled_by' => $meetingData['scheduled_by']
        ]);

        return $result ? $this->db->lastInsertId() : false;
    }

    /**
     * Get all meetings with IEP and learner details
     */
    public function getAllWithDetails()
    {
        $sql = "SELECT m.*, 
                       i.learner_id, i.status as iep_status,
                       l.first_name, l.middle_name, l.last_name, l.suffix, l.grade_level, l.lrn,
                       u.fullname as scheduled_by_name
                FROM iep_meetings m
                JOIN ieps i ON m.iep_id = i.id
                JOIN learners l ON i.learner_id = l.id
                JOIN users u ON m.scheduled_by = u.id
                ORDER BY m.meeting_date DESC, m.meeting_time DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Get upcoming meetings
     */
    public function getUpcoming()
    {
        $sql = "SELECT m.*, 
                       i.learner_id, i.status as iep_status,
                       l.first_name, l.middle_name, l.last_name, l.suffix, l.grade_level, l.lrn,
                       u.fullname as scheduled_by_name
                FROM iep_meetings m
                JOIN ieps i ON m.iep_id = i.id
                JOIN learners l ON i.learner_id = l.id
                JOIN users u ON m.scheduled_by = u.id
                WHERE m.meeting_date >= CURDATE() AND m.status = 'scheduled'
                ORDER BY m.meeting_date ASC, m.meeting_time ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Get meeting by ID
     */
    public function getById($meetingId)
    {
        $sql = "SELECT m.*, 
                       i.learner_id, i.status as iep_status, i.draft_data,
                       l.first_name, l.middle_name, l.last_name, l.suffix, l.grade_level, l.lrn,
                       u.fullname as scheduled_by_name
                FROM iep_meetings m
                JOIN ieps i ON m.iep_id = i.id
                JOIN learners l ON i.learner_id = l.id
                JOIN users u ON m.scheduled_by = u.id
                WHERE m.id = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $meetingId]);

        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    /**
     * Get meetings by IEP ID
     */
    public function getByIepId($iepId)
    {
        $sql = "SELECT m.*, u.fullname as scheduled_by_name
                FROM iep_meetings m
                JOIN users u ON m.scheduled_by = u.id
                WHERE m.iep_id = :iep_id
                ORDER BY m.meeting_date DESC, m.meeting_time DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':iep_id' => $iepId]);

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Update meeting status
     */
    public function updateStatus($meetingId, $status)
    {
        $sql = "UPDATE iep_meetings 
                SET status = :status,
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':status' => $status,
            ':id' => $meetingId
        ]);
    }

    /**
     * Confirm meeting attendance
     */
    public function confirmAttendance($meetingId, $userId)
    {
        $sql = "UPDATE iep_meetings 
                SET status = 'confirmed',
                    confirmed_by = :confirmed_by,
                    confirmed_at = CURRENT_TIMESTAMP,
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':confirmed_by' => $userId,
            ':id' => $meetingId
        ]);
    }

    /**
     * Record meeting minutes
     */
    public function recordMinutes($meetingId, $minutes)
    {
        $sql = "UPDATE iep_meetings 
                SET meeting_minutes = :minutes,
                    status = 'completed',
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':minutes' => $minutes,
            ':id' => $meetingId
        ]);
    }

    /**
     * Add meeting attendee
     */
    public function addAttendee($meetingId, $attendeeData)
    {
        $sql = "INSERT INTO iep_meeting_attendees 
                (meeting_id, name, role, designation, signature, attendance_status)
                VALUES (:meeting_id, :name, :role, :designation, :signature, :attendance_status)";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':meeting_id' => $meetingId,
            ':name' => $attendeeData['name'],
            ':role' => $attendeeData['role'],
            ':designation' => $attendeeData['designation'] ?? null,
            ':signature' => $attendeeData['signature'] ?? null,
            ':attendance_status' => $attendeeData['attendance_status'] ?? 'invited'
        ]);
    }

    /**
     * Get meeting attendees
     */
    public function getAttendees($meetingId)
    {
        $sql = "SELECT * FROM iep_meeting_attendees 
                WHERE meeting_id = :meeting_id 
                ORDER BY id ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':meeting_id' => $meetingId]);

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Update attendee status
     */
    public function updateAttendeeStatus($attendeeId, $status, $signature = null)
    {
        $sql = "UPDATE iep_meeting_attendees 
                SET attendance_status = :status,
                    signature = :signature,
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':status' => $status,
            ':signature' => $signature,
            ':id' => $attendeeId
        ]);
    }

    /**
     * Cancel meeting
     */
    public function cancel($meetingId, $reason)
    {
        $sql = "UPDATE iep_meetings 
                SET status = 'cancelled',
                    cancellation_reason = :reason,
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':reason' => $reason,
            ':id' => $meetingId
        ]);
    }

    /**
     * Reschedule meeting
     */
    public function reschedule($meetingId, $newDate, $newTime, $reason)
    {
        $sql = "UPDATE iep_meetings 
                SET meeting_date = :new_date,
                    meeting_time = :new_time,
                    reschedule_reason = :reason,
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':new_date' => $newDate,
            ':new_time' => $newTime,
            ':reason' => $reason,
            ':id' => $meetingId
        ]);
    }

    /**
     * Get meeting count by status
     */
    public function getCountByStatus($status)
    {
        $sql = "SELECT COUNT(*) as count FROM iep_meetings WHERE status = :status";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':status' => $status]);
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        return $result->count;
    }
}
