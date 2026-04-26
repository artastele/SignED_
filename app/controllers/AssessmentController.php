<?php

require_once '../app/traits/SecurityValidation.php';

class AssessmentController extends Controller
{
    use SecurityValidation;
    
    private $assessmentModel;
    private $learnerModel;
    private $auditLog;
    private $documentStore;

    public function __construct()
    {
        parent::__construct();
        $this->assessmentModel = $this->model('Assessment');
        $this->learnerModel = $this->model('Learner');
        $this->auditLog = $this->model('AuditLog');
        $this->documentStore = $this->model('DocumentStore');
        $this->initializeSecurity();
    }

    /**
     * Display learners with enrollment status "Verified" ready for assessment
     * Requirements: 5.1
     */
    public function list()
    {
        $this->requireSpedRole(['sped_teacher', 'admin']);

        // Get learners ready for assessment (status: enrolled)
        $learners = $this->learnerModel->getReadyForAssessment();

        // Get current user role for sidebar
        $role = $this->getCurrentUserRole();
        
        // Get badge counts for sidebar
        $enrollmentModel = $this->model('Enrollment');
        $iepModel = $this->model('Iep');
        $meetingModel = $this->model('IepMeeting');
        
        $data = [
            'learners' => $learners,
            'page_title' => 'Learners Ready for Assessment',
            'role' => $role,
            'current_page' => 'assessments',
            'pending_verifications_count' => count($enrollmentModel->getByStatus('pending_verification')),
            'pending_assessments_count' => count($learners),
            'upcoming_meetings_count' => count($meetingModel->getUpcoming()),
            'pending_approvals_count' => count($iepModel->getByStatus('pending_approval'))
        ];

        $this->view('assessment/list', $data);
    }

    /**
     * Display assessment form for a specific learner
     * Requirements: 5.2
     */
    public function create()
    {
        $this->requireSpedRole(['sped_teacher', 'admin']);

        $learnerId = $_GET['learner_id'] ?? null;
        if (!$learnerId) {
            die('Learner ID required');
        }

        // Verify learner exists and is ready for assessment
        $learner = $this->learnerModel->getById($learnerId);
        if (!$learner) {
            die('Learner not found');
        }

        if ($learner->status !== 'enrolled') {
            die('Learner is not ready for assessment');
        }

        // Check if assessment already exists
        if ($this->assessmentModel->hasAssessment($learnerId)) {
            header('Location: ' . URLROOT . '/assessment/view?learner_id=' . $learnerId);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleAssessmentSubmission($learnerId);
        } else {
            $this->showAssessmentForm($learnerId);
        }
    }

    /**
     * Handle assessment form submission with comprehensive validation
     * Requirements: 5.3, 5.4, 5.5, 5.6
     */
    private function handleAssessmentSubmission($learnerId)
    {
        // Validate session integrity
        if (!$this->validateSessionIntegrity()) {
            header('Location: ' . URLROOT . '/auth/login?error=session_invalid');
            exit;
        }
        
        // Validate and sanitize form input
        $validationResult = $this->validateFormInput($_POST, 'assessment');
        
        if (!$validationResult['success']) {
            $this->showAssessmentForm($learnerId, $validationResult);
            return;
        }
        
        $assessmentData = $validationResult['data'];
        $assessmentData['assessed_by'] = $this->getCurrentUserId();
        
        try {
            // Store assessment as encrypted Assessment_Record
            $assessmentId = $this->assessmentModel->create($learnerId, $assessmentData);

            if (!$assessmentId) {
                throw new Exception("Failed to create assessment record");
            }

            // Change learner status to "Assessment Complete"
            $statusUpdated = $this->learnerModel->updateStatus($learnerId, 'assessment_complete');

            if (!$statusUpdated) {
                throw new Exception("Failed to update learner status");
            }

            // Record action in audit log
            $this->auditLog->logStatusChange(
                $this->getCurrentUserId(),
                'learner',
                $learnerId,
                'enrolled',
                'assessment_complete',
                'Initial assessment completed'
            );

            // Log assessment creation
            $this->auditLog->logDocumentAccess(
                $this->getCurrentUserId(),
                $assessmentId,
                'create',
                ['learner_id' => $learnerId, 'assessment_type' => 'initial_assessment']
            );

            // Redirect to assessment view
            header('Location: ' . URLROOT . '/assessment/view?assessment_id=' . $assessmentId . '&success=Assessment completed successfully');
            exit;

        } catch (Exception $e) {
            $errorResponse = $this->errorHandler->handleError(
                $e,
                'assessment',
                $this->getCurrentUserId(),
                ['learner_id' => $learnerId, 'action' => 'create_assessment']
            );
            $this->showAssessmentForm($learnerId, $errorResponse);
        }
    }

    /**
     * Show assessment form
     */
    private function showAssessmentForm($learnerId, $data = [])
    {
        $learner = $this->learnerModel->getById($learnerId);
        
        $data['learner'] = $learner;
        $data['assessment_date'] = date('Y-m-d'); // Default to today
        $data['page_title'] = 'Initial Assessment - ' . $learner->first_name . ' ' . $learner->last_name;

        $this->view('assessment/create', $data);
    }

    /**
     * Save assessment (for draft functionality)
     */
    public function save()
    {
        $this->requireSpedRole(['sped_teacher', 'admin']);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            die('Invalid request method');
        }

        $learnerId = $_POST['learner_id'] ?? null;
        if (!$learnerId) {
            die('Learner ID required');
        }

        // This method can be used for saving drafts or updates
        // For now, redirect to create method which handles the full submission
        $this->create();
    }

    /**
     * Display assessment data in read-only format
     * Requirements: 5.7
     */
    public function viewAssessment()
    {
        $this->requireSpedStaff(); // Allow SPED teachers, guidance, and principals to view

        $assessmentId = $_GET['assessment_id'] ?? null;
        $learnerId = $_GET['learner_id'] ?? null;

        if ($assessmentId) {
            $assessment = $this->assessmentModel->getById($assessmentId);
        } elseif ($learnerId) {
            $assessment = $this->assessmentModel->getByLearner($learnerId);
        } else {
            die('Assessment ID or Learner ID required');
        }

        if (!$assessment) {
            die('Assessment not found');
        }

        // Log assessment access
        $this->auditLog->logDocumentAccess(
            $this->getCurrentUserId(),
            $assessment->id,
            'view',
            ['learner_id' => $assessment->learner_id]
        );

        $data = [
            'assessment' => $assessment,
            'page_title' => 'Assessment Results - ' . $assessment->first_name . ' ' . $assessment->last_name,
            'readonly' => true
        ];

        parent::view('assessment/view', $data);
    }

    /**
     * List all assessments (for SPED teachers to review their work)
     */
    public function myAssessments()
    {
        $this->requireSpedRole(['sped_teacher', 'admin']);

        $assessorId = $this->getCurrentUserId();
        $assessments = $this->assessmentModel->getByAssessor($assessorId);

        $data = [
            'assessments' => $assessments,
            'page_title' => 'My Assessments'
        ];

        $this->view('assessment/my_assessments', $data);
    }
}