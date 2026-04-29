<?php

class AssessmentController extends Controller
{
    private $assessmentModel;
    private $learnerModel;
    private $auditLog;

    public function __construct()
    {
        $this->assessmentModel = $this->model('Assessment');
        $this->learnerModel = $this->model('Learner');
        $this->auditLog = $this->model('AuditLog');
    }

    /**
     * Parent assessment form page
     */
    public function index()
    {
        $this->requireParent();

        $parentId = $this->getCurrentUserId();
        
        // Get learners for this parent
        $learners = $this->learnerModel->getByParent($parentId);
        
        // Get assessments for each learner
        $assessments = [];
        foreach ($learners as $learner) {
            $assessment = $this->assessmentModel->getByLearnerId($learner->id);
            if ($assessment) {
                $assessments[] = $assessment;
            }
        }

        $data = [
            'learners' => $learners,
            'assessments' => $assessments,
            'role' => $_SESSION['role'] ?? 'parent',
            'user_name' => $_SESSION['fullname'] ?? 'Parent',
            'current_page' => 'assessment'
        ];

        $this->view('assessment/index', $data);
    }

    /**
     * Create/Start initial assessment
     * Redirects to assessment form for the first approved learner
     */
    public function create()
    {
        $this->requireParent();

        $parentId = $this->getCurrentUserId();
        
        // Get learners for this parent
        $learners = $this->learnerModel->getByParent($parentId);
        
        if (empty($learners)) {
            header('Location: ' . URLROOT . '/parent/dashboard?error=No enrolled learners found');
            exit;
        }
        
        // Get the first learner (or you can let parent choose)
        $firstLearner = $learners[0];
        
        // Redirect to assessment form with learner ID
        header('Location: ' . URLROOT . '/assessment/form?learner_id=' . $firstLearner->id);
        exit;
    }

    /**
     * Start or continue assessment for a learner
     */
    public function form()
    {
        $this->requireParent();

        $learnerId = $_GET['learner_id'] ?? null;

        if (!$learnerId) {
            header('Location: ' . URLROOT . '/assessment?error=Learner ID required');
            exit;
        }

        // Get learner
        $learner = $this->learnerModel->getById($learnerId);

        if (!$learner) {
            header('Location: ' . URLROOT . '/assessment?error=Learner not found');
            exit;
        }

        // Verify parent owns this learner
        if ($learner->parent_id != $this->getCurrentUserId()) {
            header('Location: ' . URLROOT . '/assessment?error=Unauthorized access');
            exit;
        }

        // Get or create assessment
        $assessment = $this->assessmentModel->getByLearnerId($learnerId);

        if (!$assessment) {
            // Create new assessment
            $assessmentId = $this->assessmentModel->create($learnerId, 'unlocked');
            $assessment = $this->assessmentModel->getById($assessmentId);
        }

        // Check if assessment is locked
        if ($assessment->status === 'locked') {
            header('Location: ' . URLROOT . '/assessment?error=Assessment is locked. Please wait for enrollment approval.');
            exit;
        }

        // Check if already submitted
        if ($assessment->status === 'submitted' || $assessment->status === 'reviewed') {
            header('Location: ' . URLROOT . '/assessment/viewAssessment?id=' . $assessment->id);
            exit;
        }

        // Auto-fill learner background
        $learnerBackground = $this->assessmentModel->autoFillLearnerBackground($learnerId);

        // Get existing data if draft
        $educationHistory = $assessment->education_history ? json_decode($assessment->education_history, true) : [];
        $additionalInfo = $assessment->additional_info ? json_decode($assessment->additional_info, true) : [];

        $data = [
            'assessment' => $assessment,
            'learner' => $learner,
            'learner_background' => $learnerBackground,
            'education_history' => $educationHistory,
            'additional_info' => $additionalInfo,
            'role' => $_SESSION['role'] ?? 'parent',
            'user_name' => $_SESSION['fullname'] ?? 'Parent',
            'current_page' => 'assessment'
        ];

        $this->view('assessment/form', $data);
    }

    /**
     * Save assessment draft (AJAX)
     */
    public function saveDraft()
    {
        $this->requireParent();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }

        $assessmentId = $_POST['assessment_id'] ?? null;
        $section = $_POST['section'] ?? null;
        $data = $_POST['data'] ?? [];

        if (!$assessmentId || !$section) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Missing required fields']);
            exit;
        }

        // Get assessment
        $assessment = $this->assessmentModel->getById($assessmentId);

        if (!$assessment) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Assessment not found']);
            exit;
        }

        // Verify parent owns this assessment
        if ($assessment->parent_id != $this->getCurrentUserId()) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        // Prepare data for saving
        $saveData = [
            'learner_background' => $assessment->learner_background ? json_decode($assessment->learner_background, true) : [],
            'education_history' => $assessment->education_history ? json_decode($assessment->education_history, true) : [],
            'additional_info' => $assessment->additional_info ? json_decode($assessment->additional_info, true) : []
        ];

        // Update the specific section
        $saveData[$section] = $data;

        // Save draft
        $result = $this->assessmentModel->saveAssessmentDraft($assessmentId, $saveData);

        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Draft saved successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to save draft']);
        }
        exit;
    }

    /**
     * Submit assessment
     */
    public function submit()
    {
        $this->requireParent();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . URLROOT . '/assessment');
            exit;
        }

        $assessmentId = $_POST['assessment_id'] ?? null;

        if (!$assessmentId) {
            header('Location: ' . URLROOT . '/assessment?error=Assessment ID required');
            exit;
        }

        // Get assessment
        $assessment = $this->assessmentModel->getById($assessmentId);

        if (!$assessment) {
            header('Location: ' . URLROOT . '/assessment?error=Assessment not found');
            exit;
        }

        // Verify parent owns this assessment
        if ($assessment->parent_id != $this->getCurrentUserId()) {
            header('Location: ' . URLROOT . '/assessment?error=Unauthorized access');
            exit;
        }

        // FIRST: Save the form data from POST
        $learnerBackground = [
            'personal_info' => [
                'last_name' => trim($_POST['last_name'] ?? ''),
                'first_name' => trim($_POST['first_name'] ?? ''),
                'middle_name' => trim($_POST['middle_name'] ?? ''),
                'suffix' => trim($_POST['suffix'] ?? ''),
                'date_of_birth' => trim($_POST['date_of_birth'] ?? ''),
                'age' => trim($_POST['age'] ?? ''),
                'sex' => trim($_POST['sex'] ?? ''),
                'religion' => trim($_POST['religion'] ?? ''),
                'address' => trim($_POST['address'] ?? ''),
                'lrn' => trim($_POST['lrn'] ?? ''),
                'school_year' => trim($_POST['school_year'] ?? ''),
                'school' => trim($_POST['school'] ?? ''),
                'adviser' => trim($_POST['adviser'] ?? '')
            ],
            'family_background' => [
                'father_name' => trim($_POST['father_name'] ?? ''),
                'father_contact' => trim($_POST['father_contact'] ?? ''),
                'father_occupation' => trim($_POST['father_occupation'] ?? ''),
                'mother_name' => trim($_POST['mother_name'] ?? ''),
                'mother_contact' => trim($_POST['mother_contact'] ?? ''),
                'mother_occupation' => trim($_POST['mother_occupation'] ?? ''),
                'guardian_name' => trim($_POST['guardian_name'] ?? ''),
                'guardian_contact' => trim($_POST['guardian_contact'] ?? ''),
                'guardian_occupation' => trim($_POST['guardian_occupation'] ?? '')
            ]
        ];

        $educationHistory = [
            'previous_school' => trim($_POST['previous_school'] ?? ''),
            'previous_grade_level' => trim($_POST['previous_grade_level'] ?? ''),
            'with_iep' => trim($_POST['with_iep'] ?? ''),
            'with_support_services' => trim($_POST['with_support_services'] ?? ''),
            'support_services' => $_POST['support_services'] ?? [],
            'support_services_others' => trim($_POST['support_services_others'] ?? '')
        ];

        // Assessment Information (Part B)
        $assessmentInfo = [];
        if (isset($_POST['assessment_info']) && is_array($_POST['assessment_info'])) {
            foreach ($_POST['assessment_info'] as $row) {
                // Only add rows that have at least one field filled
                if (!empty($row['service']) || !empty($row['mdt_members']) || !empty($row['date']) || !empty($row['documents'])) {
                    $assessmentInfo[] = [
                        'service' => trim($row['service'] ?? ''),
                        'mdt_members' => trim($row['mdt_members'] ?? ''),
                        'date' => trim($row['date'] ?? ''),
                        'documents' => trim($row['documents'] ?? '')
                    ];
                }
            }
        }

        $additionalInfo = [
            'assessment_info' => $assessmentInfo
        ];

        // Save the data first
        $saveData = [
            'learner_background' => $learnerBackground,
            'education_history' => $educationHistory,
            'additional_info' => $additionalInfo
        ];

        $this->assessmentModel->saveAssessmentDraft($assessmentId, $saveData);

        // THEN: Validate assessment is complete
        $assessmentData = [
            'education_history' => $educationHistory
        ];

        if (!$this->assessmentModel->validateAssessmentComplete($assessmentData)) {
            header('Location: ' . URLROOT . '/assessment/form?learner_id=' . $assessment->learner_id . '&error=Please complete all required fields in Education History section');
            exit;
        }

        // Submit assessment
        $result = $this->assessmentModel->submitAssessment($assessmentId);

        if ($result) {
            // Create notification for SPED teachers
            $this->notifySPEDTeachers($assessmentId);

            // Log submission
            $this->auditLog->logAction(
                $this->getCurrentUserId(),
                'assessment_submit',
                'Assessment submitted for learner ID: ' . $assessment->learner_id
            );

            header('Location: ' . URLROOT . '/assessment?success=Assessment submitted successfully');
        } else {
            header('Location: ' . URLROOT . '/assessment/form?learner_id=' . $assessment->learner_id . '&error=Failed to submit assessment');
        }
        exit;
    }

    /**
     * View submitted assessment (read-only)
     */
    public function viewAssessment()
    {
        $assessmentId = $_GET['id'] ?? null;

        if (!$assessmentId) {
            header('Location: ' . URLROOT . '/assessment?error=Assessment ID required');
            exit;
        }

        $assessment = $this->assessmentModel->getById($assessmentId);

        if (!$assessment) {
            header('Location: ' . URLROOT . '/assessment?error=Assessment not found');
            exit;
        }

        // Check permissions
        $userRole = $_SESSION['role'] ?? '';
        $userId = $this->getCurrentUserId();

        if ($userRole === 'parent' && $assessment->parent_id != $userId) {
            header('Location: ' . URLROOT . '/assessment?error=Unauthorized access');
            exit;
        }

        // Decode JSON data
        $learnerBackground = json_decode($assessment->learner_background, true);
        $educationHistory = json_decode($assessment->education_history, true);
        $additionalInfo = json_decode($assessment->additional_info, true);

        $data = [
            'assessment' => $assessment,
            'learner_background' => $learnerBackground,
            'education_history' => $educationHistory,
            'additional_info' => $additionalInfo,
            'role' => $userRole,
            'user_name' => $_SESSION['user_name'] ?? 'User',
            'current_page' => 'assessment'
        ];

        $this->view('assessment/view', $data);
    }

    /**
     * SPED teacher review page
     */
    public function review()
    {
        $this->requireSpedStaff();

        // Get all submitted assessments
        $assessments = $this->assessmentModel->getSubmittedAssessments();

        $data = [
            'assessments' => $assessments,
            'role' => $_SESSION['role'] ?? 'sped_teacher',
            'user_name' => $_SESSION['fullname'] ?? 'SPED Teacher',
            'current_page' => 'assessment'
        ];

        $this->view('assessment/review', $data);
    }

    /**
     * Mark assessment as reviewed
     */
    public function markReviewed()
    {
        $this->requireSpedStaff();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . URLROOT . '/assessment/review');
            exit;
        }

        $assessmentId = $_POST['assessment_id'] ?? null;

        if (!$assessmentId) {
            header('Location: ' . URLROOT . '/assessment/review?error=Assessment ID required');
            exit;
        }

        $result = $this->assessmentModel->markAsReviewed($assessmentId);

        if ($result) {
            // Log action
            $this->auditLog->logAction(
                $this->getCurrentUserId(),
                'assessment_reviewed',
                'Assessment ID: ' . $assessmentId . ' marked as reviewed'
            );

            header('Location: ' . URLROOT . '/assessment/review?success=Assessment marked as reviewed');
        } else {
            header('Location: ' . URLROOT . '/assessment/review?error=Failed to mark assessment as reviewed');
        }
        exit;
    }

    /**
     * Notify SPED teachers about new assessment submission
     */
    private function notifySPEDTeachers($assessmentId)
    {
        // Get all SPED teachers
        $userModel = $this->model('User');
        $spedTeachers = $userModel->getUsersByRole('sped_teacher');

        foreach ($spedTeachers as $teacher) {
            $this->assessmentModel->createNotification($assessmentId, $teacher->id, 'submitted');
        }
    }

    /**
     * Require parent role
     */
    public function requireParent()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'parent') {
            header('Location: ' . URLROOT . '/auth/login');
            exit;
        }
    }

    /**
     * Require SPED staff role
     */
    public function requireSpedStaff()
    {
        $allowedRoles = ['sped_teacher', 'guidance', 'admin'];
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], $allowedRoles)) {
            header('Location: ' . URLROOT . '/auth/login');
            exit;
        }
    }

    /**
     * Get current user ID
     */
    public function getCurrentUserId()
    {
        return $_SESSION['user_id'] ?? null;
    }
}
