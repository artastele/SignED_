<?php

class Iep extends Model
{
    /**
     * Create IEP from assessment data
     */
    public function create($learnerId, $createdBy, $assessmentData, $meetingId = null)
    {
        $sql = "INSERT INTO ieps 
                (learner_id, created_by, meeting_id, present_level_performance, 
                 annual_goals, short_term_objectives, special_education_services, 
                 accommodations, progress_measurement, start_date, end_date)
                VALUES (:learner_id, :created_by, :meeting_id, :present_level_performance,
                        :annual_goals, :short_term_objectives, :special_education_services,
                        :accommodations, :progress_measurement, :start_date, :end_date)";

        $stmt = $this->db->prepare($sql);

        $result = $stmt->execute([
            ':learner_id' => $learnerId,
            ':created_by' => $createdBy,
            ':meeting_id' => $meetingId,
            ':present_level_performance' => $assessmentData['present_level_performance'],
            ':annual_goals' => $assessmentData['annual_goals'],
            ':short_term_objectives' => $assessmentData['short_term_objectives'],
            ':special_education_services' => $assessmentData['special_education_services'],
            ':accommodations' => $assessmentData['accommodations'],
            ':progress_measurement' => $assessmentData['progress_measurement'],
            ':start_date' => $assessmentData['start_date'],
            ':end_date' => $assessmentData['end_date']
        ]);

        return $result ? $this->db->lastInsertId() : false;
    }

    /**
     * Save IEP draft
     */
    public function save($iepId, $iepData)
    {
        $sql = "UPDATE ieps 
                SET present_level_performance = :present_level_performance,
                    annual_goals = :annual_goals,
                    short_term_objectives = :short_term_objectives,
                    special_education_services = :special_education_services,
                    accommodations = :accommodations,
                    progress_measurement = :progress_measurement,
                    start_date = :start_date,
                    end_date = :end_date,
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':present_level_performance' => $iepData['present_level_performance'],
            ':annual_goals' => $iepData['annual_goals'],
            ':short_term_objectives' => $iepData['short_term_objectives'],
            ':special_education_services' => $iepData['special_education_services'],
            ':accommodations' => $iepData['accommodations'],
            ':progress_measurement' => $iepData['progress_measurement'],
            ':start_date' => $iepData['start_date'],
            ':end_date' => $iepData['end_date'],
            ':id' => $iepId
        ]);
    }

    /**
     * Submit IEP for approval
     */
    public function submitForApproval($iepId)
    {
        $sql = "UPDATE ieps 
                SET status = 'pending_approval', 
                    updated_at = CURRENT_TIMESTAMP 
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([':id' => $iepId]);
    }

    /**
     * Approve IEP
     */
    public function approve($iepId, $principalId, $digitalSignature = null)
    {
        $sql = "UPDATE ieps 
                SET status = 'approved', 
                    approved_by = :approved_by, 
                    approved_at = CURRENT_TIMESTAMP,
                    digital_signature = :digital_signature,
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':approved_by' => $principalId,
            ':digital_signature' => $digitalSignature,
            ':id' => $iepId
        ]);
    }

    /**
     * Reject IEP
     */
    public function reject($iepId, $reason)
    {
        $sql = "UPDATE ieps 
                SET status = 'rejected', 
                    rejection_reason = :reason,
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':reason' => $reason,
            ':id' => $iepId
        ]);
    }

    /**
     * Get IEPs by status
     */
    public function getByStatus($status)
    {
        $sql = "SELECT i.*, l.first_name, l.last_name, u.fullname as created_by_name
                FROM ieps i
                JOIN learners l ON i.learner_id = l.id
                JOIN users u ON i.created_by = u.id
                WHERE i.status = :status
                ORDER BY i.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':status' => $status]);

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Get IEP by ID
     */
    public function getById($iepId)
    {
        $sql = "SELECT i.*, l.first_name, l.last_name, l.date_of_birth, l.grade_level,
                       u.fullname as created_by_name, p.fullname as approved_by_name
                FROM ieps i
                JOIN learners l ON i.learner_id = l.id
                JOIN users u ON i.created_by = u.id
                LEFT JOIN users p ON i.approved_by = p.id
                WHERE i.id = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $iepId]);

        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    /**
     * Get current IEP for learner
     */
    public function getCurrentForLearner($learnerId)
    {
        $sql = "SELECT i.*, u.fullname as created_by_name
                FROM ieps i
                JOIN users u ON i.created_by = u.id
                WHERE i.learner_id = :learner_id 
                AND i.status IN ('approved', 'active')
                ORDER BY i.created_at DESC
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':learner_id' => $learnerId]);

        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    /**
     * Get IEPs created by user
     */
    public function getByCreator($creatorId)
    {
        $sql = "SELECT i.*, l.first_name, l.last_name
                FROM ieps i
                JOIN learners l ON i.learner_id = l.id
                WHERE i.created_by = :creator_id
                ORDER BY i.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':creator_id' => $creatorId]);

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Activate approved IEP
     */
    public function activate($iepId)
    {
        $sql = "UPDATE ieps 
                SET status = 'active', 
                    updated_at = CURRENT_TIMESTAMP 
                WHERE id = :id AND status = 'approved'";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([':id' => $iepId]);
    }

    /**
     * Get IEPs pending approval
     */
    public function getPendingApproval()
    {
        return $this->getByStatus('pending_approval');
    }

    /**
     * Get active IEPs
     */
    public function getActive()
    {
        return $this->getByStatus('active');
    }

    /**
     * Get recent approvals for dashboard
     */
    public function getRecentApprovals($limit = 5)
    {
        $sql = "SELECT i.*, l.first_name, l.last_name, u.fullname as created_by_name
                FROM ieps i
                JOIN learners l ON i.learner_id = l.id
                JOIN users u ON i.created_by = u.id
                WHERE i.status = 'approved' AND i.approved_at IS NOT NULL
                ORDER BY i.approved_at DESC
                LIMIT :limit";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Get count of upcoming meetings
     */
    public function getUpcomingMeetingsCount()
    {
        $sql = "SELECT COUNT(*) as count FROM iep_meetings 
                WHERE meeting_date >= CURDATE() AND status = 'scheduled'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        return $result->count;
    }

    /**
     * Get count of pending approvals
     */
    public function getPendingApprovalCount()
    {
        $sql = "SELECT COUNT(*) as count FROM ieps WHERE status = 'pending_approval'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        return $result->count;
    }
}