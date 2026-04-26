<?php

class Assessment extends Model
{
    private $documentStore;
    private $auditLog;

    /**
     * Get DocumentStore instance (lazy loading)
     */
    private function getDocumentStore()
    {
        if (!$this->documentStore) {
            require_once __DIR__ . '/DocumentStore.php';
            $this->documentStore = new DocumentStore();
        }
        return $this->documentStore;
    }

    /**
     * Get AuditLog instance (lazy loading)
     */
    private function getAuditLog()
    {
        if (!$this->auditLog) {
            require_once __DIR__ . '/AuditLog.php';
            $this->auditLog = new AuditLog();
        }
        return $this->auditLog;
    }

    /**
     * Create new assessment with encrypted data storage
     * Requirements: 5.4, 5.5
     */
    public function create($learnerId, $assessmentData)
    {
        try {
            // Begin transaction for data integrity
            $this->db->beginTransaction();

            // Encrypt sensitive assessment data before storage
            $encryptedData = $this->encryptAssessmentData($assessmentData);

            $sql = "INSERT INTO assessments 
                    (learner_id, assessed_by, cognitive_ability, communication_skills, 
                     social_emotional_development, adaptive_behavior, academic_performance, 
                     recommendations, assessment_date)
                    VALUES (:learner_id, :assessed_by, :cognitive_ability, :communication_skills,
                            :social_emotional_development, :adaptive_behavior, :academic_performance,
                            :recommendations, :assessment_date)";

            $stmt = $this->db->prepare($sql);

            $result = $stmt->execute([
                ':learner_id' => $learnerId,
                ':assessed_by' => $assessmentData['assessed_by'],
                ':cognitive_ability' => $encryptedData['cognitive_ability'],
                ':communication_skills' => $encryptedData['communication_skills'],
                ':social_emotional_development' => $encryptedData['social_emotional_development'],
                ':adaptive_behavior' => $encryptedData['adaptive_behavior'],
                ':academic_performance' => $encryptedData['academic_performance'],
                ':recommendations' => $encryptedData['recommendations'],
                ':assessment_date' => $assessmentData['assessment_date']
            ]);

            if (!$result) {
                throw new Exception("Failed to insert assessment record");
            }

            $assessmentId = $this->db->lastInsertId();

            // Log assessment creation in audit log
            $this->getAuditLog()->logDocumentAccess(
                $assessmentData['assessed_by'],
                $assessmentId,
                'create',
                ['learner_id' => $learnerId, 'assessment_type' => 'initial_assessment']
            );

            $this->db->commit();
            return $assessmentId;

        } catch (Exception $e) {
            $this->db->rollBack();
            $this->getAuditLog()->logError('assessment', 'high', 'Assessment creation failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Encrypt sensitive assessment data using AES-256
     */
    private function encryptAssessmentData($assessmentData)
    {
        $sensitiveFields = [
            'cognitive_ability',
            'communication_skills',
            'social_emotional_development',
            'adaptive_behavior',
            'academic_performance',
            'recommendations'
        ];

        $encryptedData = [];
        foreach ($sensitiveFields as $field) {
            if (isset($assessmentData[$field])) {
                $encryptedData[$field] = $this->getDocumentStore()->encryptText($assessmentData[$field]);
            }
        }

        return $encryptedData;
    }

    /**
     * Decrypt assessment data for authorized access
     */
    private function decryptAssessmentData($assessmentRecord)
    {
        if (!$assessmentRecord) {
            return null;
        }

        $sensitiveFields = [
            'cognitive_ability',
            'communication_skills',
            'social_emotional_development',
            'adaptive_behavior',
            'academic_performance',
            'recommendations'
        ];

        foreach ($sensitiveFields as $field) {
            if (isset($assessmentRecord->$field)) {
                $assessmentRecord->$field = $this->getDocumentStore()->decryptText($assessmentRecord->$field);
            }
        }

        return $assessmentRecord;
    }

    /**
     * Get assessment by learner ID with decrypted data
     */
    public function getByLearner($learnerId)
    {
        $sql = "SELECT a.*, u.fullname as assessor_name, l.first_name, l.last_name
                FROM assessments a 
                JOIN users u ON a.assessed_by = u.id
                JOIN learners l ON a.learner_id = l.id
                WHERE a.learner_id = :learner_id 
                ORDER BY a.created_at DESC 
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':learner_id' => $learnerId]);

        $assessment = $stmt->fetch(PDO::FETCH_OBJ);
        
        // Decrypt sensitive data for authorized access
        return $this->decryptAssessmentData($assessment);
    }

    /**
     * Get assessment data for IEP generation with decrypted data
     */
    public function getForIepGeneration($learnerId)
    {
        $sql = "SELECT a.*, l.first_name, l.last_name, l.date_of_birth, l.grade_level
                FROM assessments a 
                JOIN learners l ON a.learner_id = l.id
                WHERE a.learner_id = :learner_id 
                ORDER BY a.created_at DESC 
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':learner_id' => $learnerId]);

        $assessment = $stmt->fetch(PDO::FETCH_OBJ);
        
        // Decrypt sensitive data for IEP generation
        return $this->decryptAssessmentData($assessment);
    }

    /**
     * Get assessment by ID with decrypted data and audit logging
     */
    public function getById($assessmentId)
    {
        $sql = "SELECT a.*, u.fullname as assessor_name, l.first_name, l.last_name
                FROM assessments a 
                JOIN users u ON a.assessed_by = u.id
                JOIN learners l ON a.learner_id = l.id
                WHERE a.id = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $assessmentId]);

        $assessment = $stmt->fetch(PDO::FETCH_OBJ);
        
        // Decrypt sensitive data for authorized access
        return $this->decryptAssessmentData($assessment);
    }

    /**
     * Get all assessments by assessor with decrypted data
     */
    public function getByAssessor($assessorId)
    {
        $sql = "SELECT a.*, l.first_name, l.last_name
                FROM assessments a 
                JOIN learners l ON a.learner_id = l.id
                WHERE a.assessed_by = :assessor_id 
                ORDER BY a.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':assessor_id' => $assessorId]);

        $assessments = $stmt->fetchAll(PDO::FETCH_OBJ);
        
        // Decrypt sensitive data for each assessment
        foreach ($assessments as $assessment) {
            $assessment = $this->decryptAssessmentData($assessment);
        }
        
        return $assessments;
    }

    /**
     * Update assessment with encrypted data storage
     */
    public function update($assessmentId, $assessmentData)
    {
        try {
            $this->db->beginTransaction();

            // Encrypt sensitive assessment data before storage
            $encryptedData = $this->encryptAssessmentData($assessmentData);

            $sql = "UPDATE assessments 
                    SET cognitive_ability = :cognitive_ability,
                        communication_skills = :communication_skills,
                        social_emotional_development = :social_emotional_development,
                        adaptive_behavior = :adaptive_behavior,
                        academic_performance = :academic_performance,
                        recommendations = :recommendations,
                        assessment_date = :assessment_date,
                        updated_at = CURRENT_TIMESTAMP
                    WHERE id = :id";

            $stmt = $this->db->prepare($sql);

            $result = $stmt->execute([
                ':cognitive_ability' => $encryptedData['cognitive_ability'],
                ':communication_skills' => $encryptedData['communication_skills'],
                ':social_emotional_development' => $encryptedData['social_emotional_development'],
                ':adaptive_behavior' => $encryptedData['adaptive_behavior'],
                ':academic_performance' => $encryptedData['academic_performance'],
                ':recommendations' => $encryptedData['recommendations'],
                ':assessment_date' => $assessmentData['assessment_date'],
                ':id' => $assessmentId
            ]);

            if (!$result) {
                throw new Exception("Failed to update assessment record");
            }

            // Log assessment update in audit log
            $this->getAuditLog()->logDocumentAccess(
                $assessmentData['assessed_by'] ?? null,
                $assessmentId,
                'update',
                ['assessment_id' => $assessmentId]
            );

            $this->db->commit();
            return true;

        } catch (Exception $e) {
            $this->db->rollBack();
            $this->getAuditLog()->logError('assessment', 'medium', 'Assessment update failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if learner has assessment
     */
    public function hasAssessment($learnerId)
    {
        $sql = "SELECT COUNT(*) as count FROM assessments WHERE learner_id = :learner_id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':learner_id' => $learnerId]);

        $result = $stmt->fetch(PDO::FETCH_OBJ);
        return $result->count > 0;
    }

    /**
     * Get count of pending assessments (learners waiting for assessment)
     */
    public function getPendingCount()
    {
        $sql = "SELECT COUNT(*) as count FROM learners WHERE status = 'assessment_pending'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        return $result->count;
    }
}
