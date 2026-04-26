<?php

class LearnerController extends Controller
{
    private $learningMaterial;
    private $learner;
    private $iep;
    private $documentStore;
    private $mailer;
    private $auditLog;

    public function __construct()
    {
        parent::__construct();
        $this->requireAuth();
        $this->learningMaterial = $this->model('LearningMaterial');
        $this->learner = $this->model('Learner');
        $this->iep = $this->model('Iep');
        $this->documentStore = $this->model('DocumentStore');
        require_once __DIR__ . '/../helpers/Mailer.php';
        $this->mailer = new Mailer();
        $this->auditLog = $this->model('AuditLog');
    }

    /**
     * Learner dashboard
     * Requirements: 9.1, 14.5
     */
    public function dashboard()
    {
        $this->requireSpedRole(['learner']);

        $learnerId = $this->getLearnerIdFromUser($_SESSION['user_id']);
        
        if (!$learnerId) {
            // Create a basic learner record if it doesn't exist
            $userModel = $this->model('User');
            $user = $userModel->getUserById($_SESSION['user_id']);
            $nameParts = explode(' ', $user->fullname, 2);
            
            $learnerData = [
                'user_id' => $_SESSION['user_id'],
                'parent_id' => null,
                'first_name' => $nameParts[0] ?? 'Unknown',
                'last_name' => $nameParts[1] ?? 'Unknown',
                'date_of_birth' => null,
                'grade_level' => null,
                'status' => 'pending_info'
            ];
            
            $this->learner->createBasicLearner($learnerData);
            $learnerId = $this->getLearnerIdFromUser($_SESSION['user_id']);
            
            if (!$learnerId) {
                $this->redirect('/auth/login?error=learner_creation_failed');
                return;
            }
        }

        // Get learner information
        $learner = $this->learner->getById($learnerId);
        
        // Get assigned materials
        $materials = $this->learningMaterial->getByLearner($learnerId);
        
        // Get recent submissions
        $submissions = $this->learningMaterial->getSubmissionsByLearner($learnerId);
        
        // Get current IEP
        $currentIep = $this->iep->getCurrentForLearner($learnerId);

        // Calculate progress statistics
        $stats = $this->calculateProgressStats($materials, $submissions);

        $this->view('learner/dashboard', [
            'learner' => $learner,
            'materials' => $materials,
            'submissions' => $submissions,
            'current_iep' => $currentIep,
            'stats' => $stats
        ]);
    }

    /**
     * Display assigned learning materials
     * Requirements: 9.1, 9.2
     */
    public function materials($learnerId = null)
    {
        // Allow SPED teachers to view materials for any learner
        if ($_SESSION['role'] === 'sped_teacher' && $learnerId) {
            $targetLearnerId = (int)$learnerId;
        } else {
            $this->requireSpedRole(['learner']);
            $targetLearnerId = $this->getLearnerIdFromUser($_SESSION['user_id']);
        }

        if (!$targetLearnerId) {
            $this->redirect('/dashboard?error=learner_not_found');
            return;
        }

        $learner = $this->learner->getById($targetLearnerId);
        if (!$learner) {
            $this->redirect('/dashboard?error=learner_not_found');
            return;
        }

        // Get materials organized by IEP objective
        $materials = $this->learningMaterial->getByLearner($targetLearnerId);
        $organizedMaterials = $this->organizeMaterialsByObjective($materials);

        // Get submissions for each material
        $submissionsData = [];
        foreach ($materials as $material) {
            $submissions = $this->learningMaterial->getSubmissions($material->id);
            $submissionsData[$material->id] = $submissions;
        }

        $this->view('learner/materials', [
            'learner' => $learner,
            'materials' => $materials,
            'organized_materials' => $organizedMaterials,
            'submissions_data' => $submissionsData,
            'is_teacher_view' => $_SESSION['role'] === 'sped_teacher'
        ]);
    }

    /**
     * Upload learning material (SPED Teacher only)
     * Requirements: 8.1, 8.4, 8.5
     */
    public function uploadMaterial()
    {
        $this->requireSpedRole(['sped_teacher']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->handleMaterialUpload();
        }

        // Get active IEPs for material assignment
        $activeIeps = $this->iep->getActive();

        // Get badge counts for sidebar
        $enrollmentModel = $this->model('Enrollment');
        $assessmentModel = $this->model('Assessment');
        $iepModel = $this->model('Iep');

        $this->view('learner/upload_material', [
            'active_ieps' => $activeIeps,
            'role' => $_SESSION['role'],
            'current_page' => 'materials',
            'user_name' => $_SESSION['fullname'] ?? 'User',
            'pending_verifications_count' => $enrollmentModel->getPendingVerificationCount(),
            'pending_assessments_count' => $assessmentModel->getPendingCount(),
            'upcoming_meetings_count' => $iepModel->getUpcomingMeetingsCount(),
            'pending_approvals_count' => $iepModel->getPendingApprovalCount()
        ]);
    }

    /**
     * Handle material upload submission
     */
    private function handleMaterialUpload()
    {
        try {
            // Validate required fields
            $required = ['iep_id', 'title', 'iep_objective'];
            foreach ($required as $field) {
                if (empty($_POST[$field])) {
                    throw new Exception("Field {$field} is required");
                }
            }

            // Validate file upload
            if (!isset($_FILES['material_file']) || $_FILES['material_file']['error'] !== UPLOAD_ERR_OK) {
                throw new Exception("Please select a file to upload");
            }

            $file = $_FILES['material_file'];
            
            // Validate file type for learning materials
            $allowedTypes = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'zip'];
            $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            
            if (!in_array($fileExtension, $allowedTypes)) {
                throw new Exception("Invalid file type. Allowed types: " . implode(', ', $allowedTypes));
            }

            // Validate file size (10MB limit for learning materials)
            $maxSize = 10 * 1024 * 1024; // 10MB
            if ($file['size'] > $maxSize) {
                throw new Exception("File size exceeds 10MB limit");
            }

            // Store file in document store
            $storeResult = $this->documentStore->store(
                $file['tmp_name'],
                'internal', // Learning materials are internal classification
                $_SESSION['user_id'],
                'learning_material'
            );

            if (!$storeResult['success']) {
                throw new Exception("Failed to store file: " . $storeResult['error']);
            }

            // Prepare material data
            $materialData = [
                'title' => trim($_POST['title']),
                'description' => trim($_POST['description'] ?? ''),
                'iep_objective' => trim($_POST['iep_objective']),
                'due_date' => !empty($_POST['due_date']) ? $_POST['due_date'] : null
            ];

            // Prepare file data
            $fileData = [
                'original_filename' => $file['name'],
                'encrypted_filename' => $storeResult['encrypted_filename'],
                'file_size' => $file['size'],
                'mime_type' => $file['type'],
                'encryption_key_id' => $storeResult['key_id']
            ];

            // Upload material
            $result = $this->learningMaterial->upload(
                (int)$_POST['iep_id'],
                $_SESSION['user_id'],
                $materialData,
                $fileData
            );

            if (!$result) {
                throw new Exception("Failed to save material information");
            }

            // Get learner information for notification
            $iep = $this->iep->getById((int)$_POST['iep_id']);
            $learner = $this->learner->getById($iep->learner_id);

            // Send notification to learner
            $this->sendMaterialNotification($learner, $materialData['title']);

            // Log the action
            $this->auditLog->logStatusChange(
                $_SESSION['user_id'],
                'learning_material',
                $this->db->lastInsertId(),
                null,
                'uploaded'
            );

            $this->redirect('/learner/materials/' . $iep->learner_id . '?success=material_uploaded');

        } catch (Exception $e) {
            $this->auditLog->logError('learning_material', 'medium', 'Material upload failed: ' . $e->getMessage());
            
            $activeIeps = $this->iep->getActive();
            $this->view('learner/upload_material', [
                'active_ieps' => $activeIeps,
                'error' => $e->getMessage(),
                'form_data' => $_POST,
                'role' => $_SESSION['role'],
                'current_page' => 'materials',
                'user_name' => $_SESSION['fullname'] ?? 'User',
                'pending_verifications_count' => $this->model('Enrollment')->getPendingVerificationCount(),
                'pending_assessments_count' => $this->model('Assessment')->getPendingCount(),
                'upcoming_meetings_count' => $this->iep->getUpcomingMeetingsCount(),
                'pending_approvals_count' => $this->iep->getPendingApprovalCount()
            ]);
        }
    }

    /**
     * Submit completed work (Learner only)
     * Requirements: 9.3, 9.4, 9.5
     */
    public function submitWork($materialId = null)
    {
        $this->requireSpedRole(['learner']);

        if (!$materialId) {
            $this->redirect('/learner/materials?error=invalid_material');
            return;
        }

        $materialId = (int)$materialId;
        $material = $this->learningMaterial->getById($materialId);
        
        if (!$material) {
            $this->redirect('/learner/materials?error=material_not_found');
            return;
        }

        $learnerId = $this->getLearnerIdFromUser($_SESSION['user_id']);
        
        // Verify this material is assigned to the current learner
        if ($material->learner_id != $learnerId) {
            $this->redirect('/learner/materials?error=access_denied');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->handleWorkSubmission($materialId, $learnerId);
        }

        // Get existing submissions for this material
        $existingSubmissions = $this->learningMaterial->getSubmissions($materialId);

        $this->view('learner/submit_work', [
            'material' => $material,
            'existing_submissions' => $existingSubmissions
        ]);
    }

    /**
     * Handle work submission
     */
    private function handleWorkSubmission($materialId, $learnerId)
    {
        try {
            // Validate file upload
            if (!isset($_FILES['submission_file']) || $_FILES['submission_file']['error'] !== UPLOAD_ERR_OK) {
                throw new Exception("Please select a file to upload");
            }

            $file = $_FILES['submission_file'];
            
            // Validate file type for learner submissions
            $allowedTypes = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];
            $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            
            if (!in_array($fileExtension, $allowedTypes)) {
                throw new Exception("Invalid file type. Allowed types: " . implode(', ', $allowedTypes));
            }

            // Validate file size (5MB limit for submissions)
            $maxSize = 5 * 1024 * 1024; // 5MB
            if ($file['size'] > $maxSize) {
                throw new Exception("File size exceeds 5MB limit");
            }

            // Store file in document store
            $storeResult = $this->documentStore->store(
                $file['tmp_name'],
                'internal', // Learner submissions are internal classification
                $_SESSION['user_id'],
                'learner_submission'
            );

            if (!$storeResult['success']) {
                throw new Exception("Failed to store file: " . $storeResult['error']);
            }

            // Prepare file data
            $fileData = [
                'original_filename' => $file['name'],
                'encrypted_filename' => $storeResult['encrypted_filename'],
                'file_size' => $file['size'],
                'mime_type' => $file['type'],
                'encryption_key_id' => $storeResult['key_id']
            ];

            // Submit work
            $result = $this->learningMaterial->submitWork(
                $materialId,
                $learnerId,
                $fileData,
                trim($_POST['submission_notes'] ?? '')
            );

            if (!$result) {
                throw new Exception("Failed to submit work");
            }

            // Get material and learner information for notification
            $material = $this->learningMaterial->getById($materialId);
            $learner = $this->learner->getById($learnerId);

            // Send notification to SPED teacher
            $this->sendSubmissionNotification($material, $learner);

            // Log the action
            $this->auditLog->logStatusChange(
                $_SESSION['user_id'],
                'learner_submission',
                $this->db->lastInsertId(),
                null,
                'submitted'
            );

            $this->redirect('/learner/materials?success=work_submitted');

        } catch (Exception $e) {
            $this->auditLog->logError('learner_submission', 'medium', 'Work submission failed: ' . $e->getMessage());
            
            $material = $this->learningMaterial->getById($materialId);
            $existingSubmissions = $this->learningMaterial->getSubmissions($materialId);

            $this->view('learner/submit_work', [
                'material' => $material,
                'existing_submissions' => $existingSubmissions,
                'error' => $e->getMessage(),
                'form_data' => $_POST
            ]);
        }
    }

    /**
     * Track progress for learner
     * Requirements: 9.6, 9.7
     */
    public function trackProgress($learnerId = null)
    {
        // Allow SPED teachers to track progress for any learner
        if ($_SESSION['role'] === 'sped_teacher' && $learnerId) {
            $targetLearnerId = (int)$learnerId;
        } else {
            $this->requireSpedRole(['learner']);
            $targetLearnerId = $this->getLearnerIdFromUser($_SESSION['user_id']);
        }

        if (!$targetLearnerId) {
            $this->redirect('/dashboard?error=learner_not_found');
            return;
        }

        $learner = $this->learner->getById($targetLearnerId);
        if (!$learner) {
            $this->redirect('/dashboard?error=learner_not_found');
            return;
        }

        // Get current IEP
        $currentIep = $this->iep->getCurrentForLearner($targetLearnerId);
        
        // Get all materials and submissions
        $materials = $this->learningMaterial->getByLearner($targetLearnerId);
        $submissions = $this->learningMaterial->getSubmissionsByLearner($targetLearnerId);

        // Calculate detailed progress metrics
        $progressData = $this->calculateDetailedProgress($materials, $submissions);

        $this->view('learner/track_progress', [
            'learner' => $learner,
            'current_iep' => $currentIep,
            'materials' => $materials,
            'submissions' => $submissions,
            'progress_data' => $progressData,
            'is_teacher_view' => $_SESSION['role'] === 'sped_teacher'
        ]);
    }

    /**
     * Download material file
     */
    public function downloadMaterial($materialId = null)
    {
        if (!$materialId) {
            $this->redirect('/learner/materials?error=invalid_material');
            return;
        }

        $materialId = (int)$materialId;
        $material = $this->learningMaterial->getById($materialId);
        
        if (!$material) {
            $this->redirect('/learner/materials?error=material_not_found');
            return;
        }

        // Check access permissions
        if ($_SESSION['role'] === 'learner') {
            $learnerId = $this->getLearnerIdFromUser($_SESSION['user_id']);
            if ($material->learner_id != $learnerId) {
                $this->redirect('/learner/materials?error=access_denied');
                return;
            }
        } elseif (!in_array($_SESSION['role'], ['sped_teacher', 'admin'])) {
            $this->redirect('/dashboard?error=access_denied');
            return;
        }

        // Retrieve file from document store
        $documentId = $this->getDocumentIdForMaterial($materialId);
        if (!$documentId) {
            $this->redirect('/learner/materials?error=file_not_found');
            return;
        }

        $result = $this->documentStore->retrieve($documentId, $_SESSION['user_id']);
        
        if (!$result['success']) {
            $this->redirect('/learner/materials?error=download_failed');
            return;
        }

        // Set headers for file download
        header('Content-Type: ' . $result['mime_type']);
        header('Content-Disposition: attachment; filename="' . $result['filename'] . '"');
        header('Content-Length: ' . strlen($result['content']));
        
        // Output file content
        echo $result['content'];
        exit;
    }

    /**
     * Download submission file
     */
    public function downloadSubmission($submissionId = null)
    {
        if (!$submissionId) {
            $this->redirect('/learner/materials?error=invalid_submission');
            return;
        }

        $submissionId = (int)$submissionId;
        
        // Get submission details
        $sql = "SELECT ls.*, lm.learner_id 
                FROM learner_submissions ls
                JOIN learning_materials lm ON ls.material_id = lm.id
                WHERE ls.id = :id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $submissionId]);
        $submission = $stmt->fetch(PDO::FETCH_OBJ);
        
        if (!$submission) {
            $this->redirect('/learner/materials?error=submission_not_found');
            return;
        }

        // Check access permissions
        if ($_SESSION['role'] === 'learner') {
            $learnerId = $this->getLearnerIdFromUser($_SESSION['user_id']);
            if ($submission->learner_id != $learnerId) {
                $this->redirect('/learner/materials?error=access_denied');
                return;
            }
        } elseif (!in_array($_SESSION['role'], ['sped_teacher', 'admin'])) {
            $this->redirect('/dashboard?error=access_denied');
            return;
        }

        // Retrieve file from document store
        $documentId = $this->getDocumentIdForSubmission($submissionId);
        if (!$documentId) {
            $this->redirect('/learner/materials?error=file_not_found');
            return;
        }

        $result = $this->documentStore->retrieve($documentId, $_SESSION['user_id']);
        
        if (!$result['success']) {
            $this->redirect('/learner/materials?error=download_failed');
            return;
        }

        // Set headers for file download
        header('Content-Type: ' . $result['mime_type']);
        header('Content-Disposition: attachment; filename="' . $result['filename'] . '"');
        header('Content-Length: ' . strlen($result['content']));
        
        // Output file content
        echo $result['content'];
        exit;
    }

    // Helper methods

    /**
     * Get learner ID from user ID
     */
    private function getLearnerIdFromUser($userId)
    {
        $sql = "SELECT id FROM learners WHERE user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        return $result ? $result->id : null;
    }

    /**
     * Calculate progress statistics
     */
    private function calculateProgressStats($materials, $submissions)
    {
        $totalMaterials = count($materials);
        $submittedCount = 0;
        $overdueCount = 0;
        $upcomingCount = 0;

        $submittedMaterials = [];
        foreach ($submissions as $submission) {
            $submittedMaterials[$submission->material_id] = true;
        }

        foreach ($materials as $material) {
            if (isset($submittedMaterials[$material->id])) {
                $submittedCount++;
            }

            if ($material->due_date) {
                $dueDate = strtotime($material->due_date);
                $now = time();
                
                if ($dueDate < $now && !isset($submittedMaterials[$material->id])) {
                    $overdueCount++;
                } elseif ($dueDate > $now && $dueDate < ($now + 7 * 24 * 60 * 60)) {
                    $upcomingCount++;
                }
            }
        }

        return [
            'total_materials' => $totalMaterials,
            'submitted_count' => $submittedCount,
            'overdue_count' => $overdueCount,
            'upcoming_count' => $upcomingCount,
            'completion_percentage' => $totalMaterials > 0 ? round(($submittedCount / $totalMaterials) * 100) : 0
        ];
    }

    /**
     * Organize materials by IEP objective
     */
    private function organizeMaterialsByObjective($materials)
    {
        $organized = [];
        
        foreach ($materials as $material) {
            $objective = $material->iep_objective;
            if (!isset($organized[$objective])) {
                $organized[$objective] = [];
            }
            $organized[$objective][] = $material;
        }
        
        return $organized;
    }

    /**
     * Calculate detailed progress metrics
     */
    private function calculateDetailedProgress($materials, $submissions)
    {
        $objectiveProgress = [];
        $submissionsByMaterial = [];
        
        // Group submissions by material
        foreach ($submissions as $submission) {
            $submissionsByMaterial[$submission->material_id][] = $submission;
        }

        // Calculate progress by objective
        foreach ($materials as $material) {
            $objective = $material->iep_objective;
            
            if (!isset($objectiveProgress[$objective])) {
                $objectiveProgress[$objective] = [
                    'total_materials' => 0,
                    'submitted_materials' => 0,
                    'materials' => []
                ];
            }
            
            $objectiveProgress[$objective]['total_materials']++;
            $objectiveProgress[$objective]['materials'][] = $material;
            
            if (isset($submissionsByMaterial[$material->id])) {
                $objectiveProgress[$objective]['submitted_materials']++;
            }
        }

        // Calculate completion percentages
        foreach ($objectiveProgress as $objective => &$progress) {
            $progress['completion_percentage'] = $progress['total_materials'] > 0 
                ? round(($progress['submitted_materials'] / $progress['total_materials']) * 100) 
                : 0;
        }

        return [
            'objective_progress' => $objectiveProgress,
            'submissions_by_material' => $submissionsByMaterial
        ];
    }

    /**
     * Send material notification to learner
     */
    private function sendMaterialNotification($learner, $materialTitle)
    {
        // Get learner's user account
        $sql = "SELECT email FROM users WHERE id = :user_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $learner->user_id]);
        $user = $stmt->fetch(PDO::FETCH_OBJ);

        if ($user) {
            $subject = "New Learning Material Assigned - {$materialTitle}";
            $message = "
                <h2>New Learning Material Assigned</h2>
                <p>Dear {$learner->first_name},</p>
                <p>A new learning material has been assigned to you: <strong>{$materialTitle}</strong></p>
                <p>Please log into your account to access the material and complete the assigned work.</p>
                <p>Best regards,<br>SignED SPED Team</p>
            ";

            $this->mailer->sendNotification($user->email, $subject, $message, true);
        }
    }

    /**
     * Send submission notification to SPED teacher
     */
    private function sendSubmissionNotification($material, $learner)
    {
        // Get SPED teacher's email
        $sql = "SELECT email, fullname FROM users WHERE id = :user_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $material->uploaded_by]);
        $teacher = $stmt->fetch(PDO::FETCH_OBJ);

        if ($teacher) {
            $subject = "Work Submitted - {$learner->first_name} {$learner->last_name}";
            $message = "
                <h2>Work Submitted</h2>
                <p>Dear {$teacher->fullname},</p>
                <p><strong>{$learner->first_name} {$learner->last_name}</strong> has submitted work for the learning material: <strong>{$material->title}</strong></p>
                <p>Please log into the system to review the submission.</p>
                <p>Best regards,<br>SignED SPED Team</p>
            ";

            $this->mailer->sendNotification($teacher->email, $subject, $message, true);
        }
    }

    /**
     * Get document ID for material (helper for file downloads)
     */
    private function getDocumentIdForMaterial($materialId)
    {
        $sql = "SELECT ds.id 
                FROM document_store ds
                JOIN learning_materials lm ON ds.encrypted_filename = lm.encrypted_filename
                WHERE lm.id = :material_id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':material_id' => $materialId]);
        
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        return $result ? $result->id : null;
    }

    /**
     * Get document ID for submission (helper for file downloads)
     */
    private function getDocumentIdForSubmission($submissionId)
    {
        $sql = "SELECT ds.id 
                FROM document_store ds
                JOIN learner_submissions ls ON ds.encrypted_filename = ls.encrypted_filename
                WHERE ls.id = :submission_id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':submission_id' => $submissionId]);
        
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        return $result ? $result->id : null;
    }
}