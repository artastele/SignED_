<?php

require_once '../app/traits/SecurityValidation.php';

class EnrollmentController extends Controller
{
    use SecurityValidation;
    
    private $enrollmentModel;
    private $documentStore;
    private $auditLog;
    private $mailer;

    public function __construct()
    {
        parent::__construct();
        $this->enrollmentModel = $this->model('Enrollment');
        $this->documentStore = $this->model('DocumentStore');
        $this->auditLog = $this->model('AuditLog');
        require_once '../app/helpers/Mailer.php';
        $this->mailer = new Mailer();
        $this->initializeSecurity();
    }

    /**
     * Parent document submission interface
     */
    public function submit()
    {
        $this->requireParent();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleSubmission();
        } else {
            $this->showSubmissionForm();
        }
    }

    /**
     * BEEF Form (Basic Education Enrollment Form)
     */
    public function beef()
    {
        $this->requireParent();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleBeefSubmission();
        } else {
            $this->showBeefForm();
        }
    }

    /**
     * Show BEEF form
     */
    private function showBeefForm($data = [])
    {
        $this->view('enrollment/beef', $data);
    }

    /**
     * Handle BEEF form submission
     */
    private function handleBeefSubmission()
    {
        // Validate session integrity
        if (!$this->validateSessionIntegrity()) {
            header('Location: ' . URLROOT . '/auth/login?error=session_invalid');
            exit;
        }

        try {
            $parentId = $this->getCurrentUserId();

            // Check if this is a returning student
            $isReturning = isset($_POST['is_returning']) && $_POST['is_returning'] == '1';
            $previousLrn = $isReturning ? trim($_POST['previous_lrn'] ?? '') : null;

            // Collect learner data
            $learnerData = [
                'first_name' => trim($_POST['learner_first_name']),
                'middle_name' => trim($_POST['learner_middle_name'] ?? ''),
                'last_name' => trim($_POST['learner_last_name']),
                'suffix' => trim($_POST['learner_suffix'] ?? ''),
                'date_of_birth' => $_POST['learner_dob'],
                'gender' => $_POST['learner_gender'],
                'place_of_birth' => trim($_POST['place_of_birth'] ?? ''),
                'nationality' => trim($_POST['nationality'] ?? 'Filipino'),
                'religion' => trim($_POST['religion'] ?? ''),
                'mother_tongue' => trim($_POST['mother_tongue'] ?? ''),
                'indigenous_people' => trim($_POST['indigenous_people'] ?? ''),
                'is_4ps_beneficiary' => isset($_POST['is_4ps']) ? 1 : 0,
                'grade_level' => $_POST['learner_grade']
            ];

            // Collect parent/guardian data
            $parentData = [
                'father_name' => trim($_POST['father_name'] ?? ''),
                'father_occupation' => trim($_POST['father_occupation'] ?? ''),
                'father_contact' => trim($_POST['father_contact'] ?? ''),
                'mother_name' => trim($_POST['mother_name'] ?? ''),
                'mother_occupation' => trim($_POST['mother_occupation'] ?? ''),
                'mother_contact' => trim($_POST['mother_contact'] ?? ''),
                'guardian_name' => trim($_POST['guardian_name'] ?? ''),
                'guardian_relationship' => trim($_POST['guardian_relationship'] ?? ''),
                'guardian_contact' => trim($_POST['guardian_contact'] ?? ''),
                'home_address' => trim($_POST['home_address'] ?? ''),
                'contact_number' => trim($_POST['contact_number'] ?? '')
            ];

            // Store complete BEEF data as JSON
            $beefData = array_merge($learnerData, $parentData);

            // Create enrollment record
            $enrollmentData = [
                'parent_id' => $parentId,
                'learner_first_name' => $learnerData['first_name'],
                'learner_last_name' => $learnerData['last_name'],
                'learner_dob' => $learnerData['date_of_birth'],
                'learner_grade' => $learnerData['grade_level'],
                'beef_data' => json_encode($beefData),
                'is_returning_student' => $isReturning ? 1 : 0,
                'previous_lrn' => $previousLrn,
                'parent_contact_number' => $parentData['contact_number'],
                'parent_address' => $parentData['home_address']
            ];

            $enrollmentId = $this->enrollmentModel->createWithBeef($enrollmentData);

            if (!$enrollmentId) {
                throw new Exception("Failed to create enrollment");
            }

            // Log the enrollment creation
            $this->auditLog->logStatusChange(
                $parentId,
                'enrollment',
                $enrollmentId,
                null,
                'pending_documents',
                'BEEF form submitted'
            );

            // Redirect to document upload page
            header('Location: ' . URLROOT . '/enrollment/upload?id=' . $enrollmentId . '&success=' . urlencode('BEEF form submitted successfully! Please upload required documents.'));
            exit;

        } catch (Exception $e) {
            $errorResponse = $this->errorHandler->handleError(
                $e,
                'enrollment',
                $this->getCurrentUserId(),
                ['action' => 'submit_beef']
            );
            $this->showBeefForm($errorResponse);
        }
    }

    /**
     * Handle enrollment submission with enhanced security validation
     */
    private function handleSubmission()
    {
        // Validate session integrity
        if (!$this->validateSessionIntegrity()) {
            header('Location: ' . URLROOT . '/auth/login?error=session_invalid');
            exit;
        }
        
        // Validate and sanitize form input
        $validationResult = $this->validateFormInput($_POST, 'enrollment');
        
        if (!$validationResult['success']) {
            $this->showSubmissionForm($validationResult);
            return;
        }
        
        $learnerData = $validationResult['data'];
        
        try {
            // Create enrollment with sanitized data
            $parentId = $this->getCurrentUserId();
            $enrollmentId = $this->enrollmentModel->create($parentId, $learnerData);

            if (!$enrollmentId) {
                throw new Exception("Failed to create enrollment");
            }

            // Log the enrollment creation
            $this->auditLog->logStatusChange(
                $parentId,
                'enrollment',
                $enrollmentId,
                null,
                'pending_documents',
                'Initial enrollment submission'
            );

            // Redirect to document upload page
            header('Location: ' . URLROOT . '/enrollment/upload?id=' . $enrollmentId);
            exit;

        } catch (Exception $e) {
            $errorResponse = $this->errorHandler->handleError(
                $e,
                'enrollment',
                $this->getCurrentUserId(),
                ['action' => 'create_enrollment']
            );
            $this->showSubmissionForm($errorResponse);
        }
    }

    /**
     * Show enrollment submission form
     */
    private function showSubmissionForm($data = [])
    {
        // Get existing enrollments for this parent
        $parentId = $this->getCurrentUserId();
        $existingEnrollments = $this->enrollmentModel->getByParent($parentId);

        $data['existing_enrollments'] = $existingEnrollments;
        $this->view('enrollment/submit', $data);
    }

    /**
     * Handle document file uploads with validation
     */
    public function upload()
    {
        $this->requireParent();

        $enrollmentId = $_GET['id'] ?? null;
        if (!$enrollmentId) {
            die('Enrollment ID required');
        }

        // Verify enrollment belongs to current parent
        $enrollment = $this->enrollmentModel->getById($enrollmentId);
        if (!$enrollment || $enrollment->parent_id != $this->getCurrentUserId()) {
            die('Access denied');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleFileUpload($enrollmentId);
        } else {
            $this->showUploadForm($enrollmentId);
        }
    }

    /**
     * Handle file upload processing with enhanced security validation
     */
    private function handleFileUpload($enrollmentId)
    {
        // Validate session integrity
        if (!$this->validateSessionIntegrity()) {
            header('Location: ' . URLROOT . '/auth/login?error=session_invalid');
            exit;
        }
        
        try {
            // Validate document type
            $documentType = $_POST['document_type'] ?? '';
            $allowedTypes = ['psa', 'pwd_id', 'medical_record', 'beef'];
            if (!in_array($documentType, $allowedTypes)) {
                throw new Exception("Invalid document type. Allowed types: " . implode(', ', $allowedTypes));
            }

            // Validate file upload with comprehensive security checks
            $fileValidation = $this->validateFileUpload($_FILES['document'], 'enrollment');
            
            if (!$fileValidation['success']) {
                $this->showUploadForm($enrollmentId, $fileValidation);
                return;
            }

            $file = $_FILES['document'];
            $fileInfo = $fileValidation['file_info'];

            // Store document using DocumentStore with encryption
            $storeResult = $this->documentStore->store(
                $file['tmp_name'],
                'confidential', // Classification for enrollment documents
                $this->getCurrentUserId(),
                'enrollment'
            );

            if (!$storeResult['success']) {
                throw new Exception("Failed to store document: " . $storeResult['error']);
            }

            // Save document reference in enrollment_documents table
            $fileData = [
                'original_filename' => $fileInfo['original_name'],
                'encrypted_filename' => $storeResult['encrypted_filename'],
                'file_size' => $fileInfo['size'],
                'mime_type' => $fileInfo['mime_type'],
                'encryption_key_id' => $storeResult['key_id']
            ];

            $uploadResult = $this->enrollmentModel->uploadDocument($enrollmentId, $documentType, $fileData);

            if (!$uploadResult) {
                throw new Exception("Failed to save document reference");
            }

            // Log document upload
            $this->auditLog->logDocumentAccess(
                $this->getCurrentUserId(),
                $storeResult['document_id'],
                'upload',
                ['document_type' => $documentType, 'enrollment_id' => $enrollmentId]
            );

            // Check if all documents are uploaded
            if ($this->enrollmentModel->hasAllDocuments($enrollmentId)) {
                // Update enrollment status to pending verification
                $this->enrollmentModel->updateStatus($enrollmentId, 'pending_verification');
                
                // Log status change
                $this->auditLog->logStatusChange(
                    $this->getCurrentUserId(),
                    'enrollment',
                    $enrollmentId,
                    'pending_documents',
                    'pending_verification',
                    'All required documents uploaded'
                );

                $message = "Document uploaded successfully! All required documents have been submitted. Your enrollment is now pending verification.";
            } else {
                $message = "Document uploaded successfully! Please upload the remaining required documents.";
            }

            $this->showUploadForm($enrollmentId, ['success' => $message]);

        } catch (Exception $e) {
            $errorResponse = $this->errorHandler->handleFileUploadError(
                $e,
                $_FILES['document']['name'] ?? 'unknown',
                $this->getCurrentUserId()
            );
            $this->showUploadForm($enrollmentId, $errorResponse);
        }
    }

    /**
     * Show document upload form
     */
    private function showUploadForm($enrollmentId, $data = [])
    {
        $enrollment = $this->enrollmentModel->getById($enrollmentId);
        $documents = $this->enrollmentModel->getDocuments($enrollmentId);

        // Create array of uploaded document types for easy checking
        $uploadedTypes = [];
        foreach ($documents as $doc) {
            $uploadedTypes[$doc->document_type] = $doc;
        }

        $data['enrollment'] = $enrollment;
        $data['documents'] = $documents;
        $data['uploaded_types'] = $uploadedTypes;
        $data['required_types'] = [
            'psa' => 'PSA Birth Certificate',
            'pwd_id' => 'PWD ID Card',
            'medical_record' => 'Medical Records',
            'beef' => 'Basic Education Enrollment Form (BEEF)'
        ];

        $this->view('enrollment/upload', $data);
    }

    /**
     * SPED teacher/admin verification interface
     */
    public function verify()
    {
        $this->requireSpedStaff();

        // Get enrollments pending verification
        $pendingEnrollments = $this->enrollmentModel->getByStatus('pending_verification');

        // Get current user role for sidebar
        $role = $this->getCurrentUserRole();
        
        // Get badge counts for sidebar
        $learnerModel = $this->model('Learner');
        $iepModel = $this->model('Iep');
        $meetingModel = $this->model('IepMeeting');
        
        $data = [
            'enrollments' => $pendingEnrollments,
            'role' => $role,
            'current_page' => 'verify',
            'pending_verifications_count' => count($pendingEnrollments),
            'pending_assessments_count' => count($learnerModel->getByStatus('assessment_pending')),
            'upcoming_meetings_count' => count($meetingModel->getUpcoming()),
            'pending_approvals_count' => count($iepModel->getByStatus('pending_approval'))
        ];

        $this->view('enrollment/verify', $data);
    }

    /**
     * View enrollment details for verification
     */
    public function viewEnrollment()
    {
        $this->requireSpedStaff();

        $enrollmentId = $_GET['id'] ?? null;
        if (!$enrollmentId) {
            die('Enrollment ID required');
        }

        $enrollment = $this->enrollmentModel->getById($enrollmentId);
        if (!$enrollment) {
            die('Enrollment not found');
        }

        $documents = $this->enrollmentModel->getDocuments($enrollmentId);

        $data = [
            'enrollment' => $enrollment,
            'documents' => $documents
        ];

        parent::view('enrollment/view', $data);
    }

    /**
     * Download enrollment document
     */
    public function download()
    {
        $this->requireSpedStaff();

        $documentId = $_GET['doc_id'] ?? null;
        if (!$documentId) {
            die('Document ID required');
        }

        // Retrieve document from DocumentStore
        $result = $this->documentStore->retrieve($documentId, $this->getCurrentUserId(), true);

        if (!$result['success']) {
            die('Error retrieving document: ' . $result['error']);
        }

        // Set appropriate headers for download
        header('Content-Type: ' . $result['mime_type']);
        header('Content-Disposition: attachment; filename="' . $result['filename'] . '"');
        header('Content-Length: ' . strlen($result['content']));

        // Output the document content
        echo $result['content'];
        exit;
    }

    /**
     * Approve enrollment and create learner record
     */
    public function approve()
    {
        $this->requireSpedStaff();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            die('Invalid request method');
        }

        $enrollmentId = $_POST['enrollment_id'] ?? null;
        if (!$enrollmentId) {
            die('Enrollment ID required');
        }

        try {
            $enrollment = $this->enrollmentModel->getById($enrollmentId);
            if (!$enrollment) {
                throw new Exception("Enrollment not found");
            }

            if ($enrollment->status !== 'pending_verification') {
                throw new Exception("Enrollment is not in pending verification status");
            }

            // Update enrollment status to approved
            $verifiedBy = $this->getCurrentUserId();
            $updateResult = $this->enrollmentModel->updateStatus($enrollmentId, 'approved', null, $verifiedBy);

            if (!$updateResult) {
                throw new Exception("Failed to update enrollment status");
            }

            // Create learner record
            $learnerModel = $this->model('Learner');
            $learnerData = [
                'parent_id' => $enrollment->parent_id,
                'first_name' => $enrollment->learner_first_name,
                'last_name' => $enrollment->learner_last_name,
                'date_of_birth' => $enrollment->learner_dob,
                'grade_level' => $enrollment->learner_grade,
                'status' => 'enrolled'
            ];

            $learnerId = $learnerModel->createFromEnrollment($learnerData);

            if (!$learnerId) {
                throw new Exception("Failed to create learner record");
            }

            // Log approval action
            $this->auditLog->logApprovalAction(
                $verifiedBy,
                'enrollment',
                $enrollmentId,
                'approve',
                'Enrollment approved and learner record created'
            );

            // Send approval email notification
            $this->sendApprovalEmail($enrollment, true);

            // Redirect back to verification page with success message
            header('Location: ' . URLROOT . '/enrollment/verify?success=Enrollment approved successfully');
            exit;

        } catch (Exception $e) {
            $this->auditLog->logError('enrollment', 'high', $e->getMessage(), null, $_POST, $this->getCurrentUserId());
            header('Location: ' . URLROOT . '/enrollment/verify?error=' . urlencode($e->getMessage()));
            exit;
        }
    }

    /**
     * Reject enrollment with reason
     */
    public function reject()
    {
        $this->requireSpedStaff();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            die('Invalid request method');
        }

        $enrollmentId = $_POST['enrollment_id'] ?? null;
        $reason = trim($_POST['rejection_reason'] ?? '');

        if (!$enrollmentId) {
            die('Enrollment ID required');
        }

        if (empty($reason)) {
            header('Location: ' . URLROOT . '/enrollment/verify?error=Rejection reason is required');
            exit;
        }

        try {
            $enrollment = $this->enrollmentModel->getById($enrollmentId);
            if (!$enrollment) {
                throw new Exception("Enrollment not found");
            }

            if ($enrollment->status !== 'pending_verification') {
                throw new Exception("Enrollment is not in pending verification status");
            }

            // Update enrollment status to rejected
            $verifiedBy = $this->getCurrentUserId();
            $updateResult = $this->enrollmentModel->updateStatus($enrollmentId, 'rejected', $reason, $verifiedBy);

            if (!$updateResult) {
                throw new Exception("Failed to update enrollment status");
            }

            // Log rejection action
            $this->auditLog->logApprovalAction(
                $verifiedBy,
                'enrollment',
                $enrollmentId,
                'reject',
                $reason
            );

            // Send rejection email notification
            $this->sendRejectionEmail($enrollment, $reason);

            // Redirect back to verification page with success message
            header('Location: ' . URLROOT . '/enrollment/verify?success=Enrollment rejected successfully');
            exit;

        } catch (Exception $e) {
            $this->auditLog->logError('enrollment', 'high', $e->getMessage(), null, $_POST, $this->getCurrentUserId());
            header('Location: ' . URLROOT . '/enrollment/verify?error=' . urlencode($e->getMessage()));
            exit;
        }
    }

    /**
     * Send approval email notification
     */
    private function sendApprovalEmail($enrollment, $approved)
    {
        try {
            $learnerName = $enrollment->learner_first_name . ' ' . $enrollment->learner_last_name;
            
            // Use enhanced mailer method
            $emailSent = $this->mailer->sendEnrollmentNotification(
                $enrollment->parent_email, 
                $learnerName, 
                'approved'
            );

            // Log email attempt
            $this->auditLog->logEmailSent(
                $this->getCurrentUserId(),
                $enrollment->parent_email,
                'SPED Enrollment Approved - ' . $learnerName,
                'enrollment_approval',
                $emailSent
            );

        } catch (Exception $e) {
            $this->auditLog->logError('email', 'medium', 'Failed to send approval email: ' . $e->getMessage());
        }
    }

    /**
     * Send rejection email notification
     */
    private function sendRejectionEmail($enrollment, $reason)
    {
        try {
            $learnerName = $enrollment->learner_first_name . ' ' . $enrollment->learner_last_name;
            
            // Use enhanced mailer method
            $emailSent = $this->mailer->sendEnrollmentNotification(
                $enrollment->parent_email, 
                $learnerName, 
                'rejected', 
                $reason
            );

            // Log email attempt
            $this->auditLog->logEmailSent(
                $this->getCurrentUserId(),
                $enrollment->parent_email,
                'SPED Enrollment Status Update - ' . $learnerName,
                'enrollment_rejection',
                $emailSent
            );

        } catch (Exception $e) {
            $this->auditLog->logError('email', 'medium', 'Failed to send rejection email: ' . $e->getMessage());
        }
    }

    /**
     * Get enrollment status for parent dashboard
     */
    public function status()
    {
        $this->requireParent();

        $parentId = $this->getCurrentUserId();
        $enrollments = $this->enrollmentModel->getByParent($parentId);

        $data = [
            'enrollments' => $enrollments
        ];

        $this->view('enrollment/status', $data);
    }

    /**
     * LRN Lookup API for Old Students
     * Returns learner data based on LRN for auto-filling BEEF form
     */
    public function lookupLRN()
    {
        $this->requireParent();

        // Only accept POST requests
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Invalid request method']);
            exit;
        }

        try {
            $lrn = trim($_POST['lrn'] ?? '');

            // Validate LRN format (12 digits)
            if (empty($lrn) || strlen($lrn) !== 12 || !ctype_digit($lrn)) {
                throw new Exception("Invalid LRN format. LRN must be exactly 12 digits.");
            }

            // Look up learner by LRN
            $learnerModel = $this->model('Learner');
            $learner = $learnerModel->getByLRN($lrn);

            if (!$learner) {
                throw new Exception("No learner found with LRN: " . $lrn);
            }

            // Check if learner belongs to current parent
            $parentId = $this->getCurrentUserId();
            if ($learner->parent_id != $parentId) {
                throw new Exception("This learner does not belong to your account.");
            }

            // Get previous enrollment data if exists
            $previousEnrollment = $this->enrollmentModel->getLatestByLearner($learner->id);

            // Prepare response data
            $responseData = [
                'success' => true,
                'learner' => [
                    'lrn' => $learner->lrn,
                    'first_name' => $learner->first_name,
                    'last_name' => $learner->last_name,
                    'middle_name' => $learner->middle_name ?? '',
                    'extension_name' => $learner->extension_name ?? '',
                    'date_of_birth' => $learner->date_of_birth,
                    'gender' => $learner->gender ?? '',
                    'place_of_birth' => $learner->place_of_birth ?? '',
                    'mother_tongue' => $learner->mother_tongue ?? '',
                    'disability_type' => $learner->disability_type ?? '',
                    'last_grade_completed' => $learner->grade_level ?? '',
                ]
            ];

            // Add previous enrollment data if exists
            if ($previousEnrollment && $previousEnrollment->beef_data) {
                $beefData = json_decode($previousEnrollment->beef_data, true);
                if ($beefData) {
                    $responseData['previous_data'] = $beefData;
                }
            }

            // Log the lookup
            $this->auditLog->logAction(
                $parentId,
                'lrn_lookup',
                'learner',
                $learner->id,
                ['lrn' => $lrn]
            );

            header('Content-Type: application/json');
            echo json_encode($responseData);
            exit;

        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
            exit;
        }
    }
}