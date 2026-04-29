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

    /**
     * Get count of active IEPs
     */
    public function getActiveCount()
    {
        $sql = "SELECT COUNT(*) as count FROM ieps WHERE status = 'active'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        return $result->count;
    }

    /**
     * Create IEP draft (new method for IEP P2 workflow)
     */
    public function createDraft($iepData)
    {
        $sql = "INSERT INTO ieps 
                (learner_id, assessment_id, created_by, draft_data, status)
                VALUES (:learner_id, :assessment_id, :created_by, :draft_data, 'draft')";

        $stmt = $this->db->prepare($sql);

        $result = $stmt->execute([
            ':learner_id' => $iepData['learner_id'],
            ':assessment_id' => $iepData['assessment_id'] ?? null,
            ':created_by' => $iepData['created_by'],
            ':draft_data' => $iepData['draft_data']
        ]);

        return $result ? $this->db->lastInsertId() : false;
    }

    /**
     * Update IEP draft
     */
    public function updateDraft($iepId, $iepData)
    {
        $sql = "UPDATE ieps 
                SET draft_data = :draft_data,
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':draft_data' => $iepData['draft_data'],
            ':id' => $iepId
        ]);
    }

    /**
     * Get IEP by learner ID
     */
    public function getByLearnerId($learnerId)
    {
        $sql = "SELECT i.*, l.first_name, l.last_name, l.grade_level, l.lrn,
                       u.fullname as created_by_name
                FROM ieps i
                JOIN learners l ON i.learner_id = l.id
                JOIN users u ON i.created_by = u.id
                WHERE i.learner_id = :learner_id
                ORDER BY i.created_at DESC
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':learner_id' => $learnerId]);

        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    /**
     * Get IEP by ID with learner info
     */
    public function getByIdWithLearner($iepId)
    {
        $sql = "SELECT i.*, 
                       l.first_name, l.middle_name, l.last_name, l.suffix,
                       l.grade_level, l.lrn, l.date_of_birth, l.parent_id,
                       u.fullname as created_by_name,
                       p.fullname as parent_name, p.email as parent_email
                FROM ieps i
                JOIN learners l ON i.learner_id = l.id
                JOIN users u ON i.created_by = u.id
                LEFT JOIN users p ON l.parent_id = p.id
                WHERE i.id = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $iepId]);

        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    /**
     * Get all IEPs with learner info
     */
    public function getAllWithLearners()
    {
        $sql = "SELECT i.*, 
                       l.first_name, l.last_name, l.grade_level, l.lrn,
                       u.fullname as created_by_name
                FROM ieps i
                JOIN learners l ON i.learner_id = l.id
                JOIN users u ON i.created_by = u.id
                ORDER BY i.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Update IEP status
     */
    public function updateStatus($iepId, $status)
    {
        $sql = "UPDATE ieps 
                SET status = :status,
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':status' => $status,
            ':id' => $iepId
        ]);
    }

    /**
     * Mark meeting as scheduled
     */
    public function markMeetingScheduled($iepId)
    {
        $sql = "UPDATE ieps 
                SET meeting_scheduled = 1,
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([':id' => $iepId]);
    }

    /**
     * Save IEP goals
     */
    public function saveGoals($iepId, $goals)
    {
        // Delete existing goals
        $sql = "DELETE FROM iep_goals WHERE iep_id = :iep_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':iep_id' => $iepId]);

        // Insert new goals
        if (!empty($goals)) {
            $sql = "INSERT INTO iep_goals 
                    (iep_id, domain, skill, description, quarter1_recommendation, 
                     quarter2_recommendation, mastered_yes, mastered_no, performance_level)
                    VALUES (:iep_id, :domain, :skill, :description, :quarter1, 
                            :quarter2, :mastered_yes, :mastered_no, :performance_level)";

            $stmt = $this->db->prepare($sql);

            foreach ($goals as $goal) {
                $stmt->execute([
                    ':iep_id' => $iepId,
                    ':domain' => $goal['domain'],
                    ':skill' => $goal['skill'],
                    ':description' => $goal['description'],
                    ':quarter1' => $goal['quarter1_recommendation'],
                    ':quarter2' => $goal['quarter2_recommendation'],
                    ':mastered_yes' => $goal['mastered_yes'],
                    ':mastered_no' => $goal['mastered_no'],
                    ':performance_level' => $goal['performance_level']
                ]);
            }
        }

        return true;
    }

    /**
     * Save IEP services
     */
    public function saveServices($iepId, $services)
    {
        // Delete existing services
        $sql = "DELETE FROM iep_services WHERE iep_id = :iep_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':iep_id' => $iepId]);

        // Insert new services
        if (!empty($services)) {
            $sql = "INSERT INTO iep_services 
                    (iep_id, service_type, provider, frequency, duration, location)
                    VALUES (:iep_id, :service_type, :provider, :frequency, :duration, :location)";

            $stmt = $this->db->prepare($sql);

            foreach ($services as $service) {
                $stmt->execute([
                    ':iep_id' => $iepId,
                    ':service_type' => $service['service_type'],
                    ':provider' => $service['provider'],
                    ':frequency' => $service['frequency'],
                    ':duration' => $service['duration'],
                    ':location' => $service['location']
                ]);
            }
        }

        return true;
    }

    /**
     * Save IEP accommodations
     */
    public function saveAccommodations($iepId, $accommodations)
    {
        // Delete existing accommodations
        $sql = "DELETE FROM iep_accommodations WHERE iep_id = :iep_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':iep_id' => $iepId]);

        // Insert new accommodations
        if (!empty($accommodations)) {
            $sql = "INSERT INTO iep_accommodations 
                    (iep_id, accommodation_type, description)
                    VALUES (:iep_id, :accommodation_type, :description)";

            $stmt = $this->db->prepare($sql);

            foreach ($accommodations as $accommodation) {
                $stmt->execute([
                    ':iep_id' => $iepId,
                    ':accommodation_type' => $accommodation['accommodation_type'],
                    ':description' => $accommodation['description']
                ]);
            }
        }

        return true;
    }

    /**
     * Get IEP goals
     */
    public function getGoals($iepId)
    {
        $sql = "SELECT * FROM iep_goals WHERE iep_id = :iep_id ORDER BY id ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':iep_id' => $iepId]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Get IEP services
     */
    public function getServices($iepId)
    {
        $sql = "SELECT * FROM iep_services WHERE iep_id = :iep_id ORDER BY id ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':iep_id' => $iepId]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Get IEP accommodations
     */
    public function getAccommodations($iepId)
    {
        $sql = "SELECT * FROM iep_accommodations WHERE iep_id = :iep_id ORDER BY id ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':iep_id' => $iepId]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Update draft document ID
     */
    public function updateDraftDocument($iepId, $documentId)
    {
        $sql = "UPDATE ieps 
                SET draft_document_id = :document_id,
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':document_id' => $documentId,
            ':id' => $iepId
        ]);
    }

    /**
     * Update signed document ID
     */
    public function updateSignedDocument($iepId, $documentId)
    {
        $sql = "UPDATE ieps 
                SET signed_document_id = :document_id,
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':document_id' => $documentId,
            ':id' => $iepId
        ]);
    }

    /**
     * Update finalized data
     */
    public function updateFinalizedData($iepId, $finalizedData)
    {
        $sql = "UPDATE ieps 
                SET finalized_data = :finalized_data,
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':finalized_data' => json_encode($finalizedData),
            ':id' => $iepId
        ]);
    }

    /**
     * Mark as guidance reviewed
     */
    public function markGuidanceReviewed($iepId, $userId, $feedback = null)
    {
        $sql = "UPDATE ieps 
                SET guidance_reviewed = 1,
                    guidance_reviewed_at = CURRENT_TIMESTAMP,
                    guidance_reviewed_by = :user_id,
                    guidance_feedback = :feedback,
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':user_id' => $userId,
            ':feedback' => $feedback,
            ':id' => $iepId
        ]);
    }

    /**
     * Save step objectives (IEP P3)
     */
    public function saveStepObjectives($iepId, $stepObjectives)
    {
        // Delete existing step objectives
        $sql = "DELETE FROM iep_step_objectives WHERE iep_id = :iep_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':iep_id' => $iepId]);

        // Insert new step objectives
        if (!empty($stepObjectives)) {
            $sql = "INSERT INTO iep_step_objectives 
                    (iep_id, step_number, step_objective, plan_of_activities, materials, 
                     duration_of_lp, instructional_evaluation, observation)
                    VALUES (:iep_id, :step_number, :step_objective, :plan_of_activities, 
                            :materials, :duration_of_lp, :instructional_evaluation, :observation)";

            $stmt = $this->db->prepare($sql);

            foreach ($stepObjectives as $step) {
                $stmt->execute([
                    ':iep_id' => $iepId,
                    ':step_number' => $step['step_number'],
                    ':step_objective' => $step['step_objective'],
                    ':plan_of_activities' => $step['plan_of_activities'] ?? null,
                    ':materials' => $step['materials'] ?? null,
                    ':duration_of_lp' => $step['duration_of_lp'] ?? null,
                    ':instructional_evaluation' => $step['instructional_evaluation'] ?? null,
                    ':observation' => $step['observation'] ?? null
                ]);
            }
        }

        return true;
    }

    /**
     * Get step objectives
     */
    public function getStepObjectives($iepId)
    {
        $sql = "SELECT * FROM iep_step_objectives 
                WHERE iep_id = :iep_id 
                ORDER BY step_number ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':iep_id' => $iepId]);

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Get IEPs pending guidance review
     */
    public function getPendingGuidanceReview()
    {
        $sql = "SELECT i.*, 
                       l.first_name, l.last_name, l.grade_level, l.lrn,
                       u.fullname as created_by_name
                FROM ieps i
                JOIN learners l ON i.learner_id = l.id
                JOIN users u ON i.created_by = u.id
                WHERE i.status = 'pending_guidance_review'
                ORDER BY i.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
}
