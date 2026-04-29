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
        try {
            $parentId = $this->getCurrentUserId();

            // Collect all BEEF form data
            $learnerData = [
                'first_name' => trim($_POST['first_name'] ?? ''),
                'middle_name' => trim($_POST['middle_name'] ?? ''),
                'last_name' => trim($_POST['last_name'] ?? ''),
                'suffix' => trim($_POST['extension_name'] ?? ''),
                'date_of_birth' => $_POST['date_of_birth'] ?? null,
                'age' => $_POST['age'] ?? null,
                'gender' => $_POST['gender'] ?? null,
                'place_of_birth' => trim($_POST['place_of_birth'] ?? ''),
                'mother_tongue' => trim($_POST['mother_tongue'] ?? ''),
                'psa_birth_cert' => trim($_POST['psa_birth_cert'] ?? ''),
                'lrn' => trim($_POST['lrn'] ?? ''),
                'is_indigenous' => $_POST['is_indigenous'] ?? 'No',
                'indigenous_specify' => trim($_POST['indigenous_specify'] ?? ''),
                'is_4ps' => $_POST['is_4ps'] ?? 'No',
                '4ps_household_id' => trim($_POST['4ps_household_id'] ?? ''),
                'is_disabled' => $_POST['is_disabled'] ?? 'No',
                'disability_types' => isset($_POST['disability']) ? implode(', ', $_POST['disability']) : '',
                'grade_level' => $_POST['grade_level'] ?? null,
                'school_year' => $_POST['school_year'] ?? ''
            ];

            // Collect address data
            $addressData = [
                'current_house_no' => trim($_POST['current_house_no'] ?? ''),
                'current_street' => trim($_POST['current_street'] ?? ''),
                'current_barangay' => trim($_POST['current_barangay'] ?? ''),
                'current_city' => trim($_POST['current_city'] ?? ''),
                'current_province' => trim($_POST['current_province'] ?? ''),
                'current_country' => trim($_POST['current_country'] ?? 'Philippines'),
                'current_zip_code' => trim($_POST['current_zip_code'] ?? ''),
                'same_address' => $_POST['same_address_option'] ?? 'no',
                'permanent_house_no' => trim($_POST['permanent_house_no'] ?? ''),
                'permanent_street' => trim($_POST['permanent_street'] ?? ''),
                'permanent_barangay' => trim($_POST['permanent_barangay'] ?? ''),
                'permanent_city' => trim($_POST['permanent_city'] ?? ''),
                'permanent_province' => trim($_POST['permanent_province'] ?? ''),
                'permanent_country' => trim($_POST['permanent_country'] ?? 'Philippines'),
                'permanent_zip_code' => trim($_POST['permanent_zip_code'] ?? '')
            ];

            // Collect parent/guardian data
            $parentData = [
                'father_last_name' => trim($_POST['father_last_name'] ?? ''),
                'father_first_name' => trim($_POST['father_first_name'] ?? ''),
                'father_middle_name' => trim($_POST['father_middle_name'] ?? ''),
                'father_contact' => trim($_POST['father_contact'] ?? ''),
                'mother_last_name' => trim($_POST['mother_last_name'] ?? ''),
                'mother_first_name' => trim($_POST['mother_first_name'] ?? ''),
                'mother_middle_name' => trim($_POST['mother_middle_name'] ?? ''),
                'mother_contact' => trim($_POST['mother_contact'] ?? ''),
                'guardian_last_name' => trim($_POST['guardian_last_name'] ?? ''),
                'guardian_first_name' => trim($_POST['guardian_first_name'] ?? ''),
                'guardian_middle_name' => trim($_POST['guardian_middle_name'] ?? ''),
                'guardian_contact' => trim($_POST['guardian_contact'] ?? '')
            ];

            // Collect student type specific data
            $studentTypeData = [
                'student_type' => $_POST['student_type'] ?? 'new',
                'last_grade_completed' => trim($_POST['last_grade_completed'] ?? ''),
                'last_school_year' => trim($_POST['last_school_year'] ?? ''),
                'last_school_attended' => trim($_POST['last_school_attended'] ?? ''),
                'last_school_id' => trim($_POST['last_school_id'] ?? '')
            ];

            // Collect SHS data (if applicable)
            $shsData = [
                'semester' => trim($_POST['semester'] ?? ''),
                'track' => trim($_POST['track'] ?? ''),
                'strand' => trim($_POST['strand'] ?? '')
            ];

            // Collect learning modalities
            $learningModalities = isset($_POST['learning_modality']) ? implode(', ', $_POST['learning_modality']) : '';

            // Store complete BEEF data as JSON
            $beefData = array_merge(
                $learnerData,
                $addressData,
                $parentData,
                $studentTypeData,
                $shsData,
                ['learning_modalities' => $learningModalities]
            );

            // Build home address string
            $homeAddress = trim($addressData['current_house_no'] . ' ' . 
                               $addressData['current_street']) . ', ' .
                           $addressData['current_barangay'] . ', ' .
                           $addressData['current_city'] . ', ' .
                           $addressData['current_province'];

            // Get primary contact number
            $contactNumber = $parentData['mother_contact'] ?: 
                           ($parentData['father_contact'] ?: $parentData['guardian_contact']);

            // Create enrollment record
            $enrollmentData = [
                'parent_id' => $parentId,
                'learner_first_name' => $learnerData['first_name'],
                'learner_last_name' => $learnerData['last_name'],
                'learner_dob' => $learnerData['date_of_birth'],
                'learner_grade' => $learnerData['grade_level'],
                'beef_data' => json_encode($beefData),
                'is_returning_student' => ($studentTypeData['student_type'] === 'old') ? 1 : 0,
                'previous_lrn' => $learnerData['lrn'],
                'parent_contact_number' => $contactNumber,
                'parent_address' => $homeAddress
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
                'BEEF form submitted - ' . $studentTypeData['student_type'] . ' student'
            );

            // Redirect to document upload page
            header('Location: ' . URLROOT . '/enrollment/upload?id=' . $enrollmentId . '&success=' . urlencode('BEEF form submitted successfully! Please upload required documents.'));
            exit;

        } catch (Exception $e) {
            error_log("BEEF Submission Error: " . $e->getMessage());
            $this->showBeefForm(['error' => $e->getMessage()]);
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
            // Validate document type (BEEF is already submitted, not uploadable here)
            $documentType = $_POST['document_type'] ?? '';
            $allowedTypes = ['psa', 'pwd_id', 'medical_record'];
            if (!in_array($documentType, $allowedTypes)) {
                throw new Exception("Invalid document type. Allowed types: PSA Birth Certificate, PWD ID, Medical Records");
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

            // Check if all required documents are uploaded (only PSA is required)
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
                    'Required document (PSA Birth Certificate) uploaded'
                );

                $message = "Document uploaded successfully! The required PSA Birth Certificate has been submitted. Your enrollment is now pending verification.";
            } else {
                $message = "Document uploaded successfully! Please upload the PSA Birth Certificate to proceed with verification.";
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

        // Get current user info for sidebar
        $userModel = $this->model('User');
        $currentUser = $userModel->getUserById($this->getCurrentUserId());
        
        $data['enrollment'] = $enrollment;
        $data['documents'] = $documents;
        $data['uploaded_types'] = $uploadedTypes;
        $data['required_types'] = [
            'psa' => 'PSA Birth Certificate',
            'pwd_id' => 'PWD ID Card',
            'medical_record' => 'Medical Records',
            'beef' => 'Basic Education Enrollment Form (BEEF)'
        ];
        
        // Add sidebar data
        $data['role'] = $this->getCurrentUserRole();
        $data['user_name'] = $currentUser->fullname ?? ($currentUser->first_name . ' ' . $currentUser->last_name);
        $data['current_page'] = 'upload';

        $this->view('enrollment/upload', $data);
    }

    /**
     * SPED teacher/admin verification interface
     */
    public function verify()
    {
        $this->requireSpedStaff();

        // Get status filter from query parameter
        $statusFilter = $_GET['status'] ?? 'pending_verification';
        
        // Get enrollments based on status filter
        $enrollments = $this->enrollmentModel->getByStatus($statusFilter);
        
        // Get approved count for the card
        $approvedEnrollments = $this->enrollmentModel->getByStatus('approved');
        $approvedCount = count($approvedEnrollments);

        // Get current user role for sidebar
        $role = $this->getCurrentUserRole();
        
        // Get badge counts for sidebar
        $learnerModel = $this->model('Learner');
        $iepModel = $this->model('Iep');
        $meetingModel = $this->model('IepMeeting');
        
        $data = [
            'enrollments' => $enrollments,
            'approved_count' => $approvedCount,
            'role' => $role,
            'current_page' => 'verify',
            'pending_verifications_count' => count($this->enrollmentModel->getByStatus('pending_verification')),
            'pending_assessments_count' => count($learnerModel->getByStatus('assessment_pending')),
            'upcoming_meetings_count' => count($meetingModel->getUpcoming()),
            'pending_approvals_count' => count($iepModel->getByStatus('pending_approval'))
        ];

        $this->view('enrollment/verify', $data);
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
     * View enrollment document inline (opens in new tab)
     */
    public function viewDocument()
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

        // Set appropriate headers for inline viewing
        header('Content-Type: ' . $result['mime_type']);
        header('Content-Disposition: inline; filename="' . $result['filename'] . '"');
        header('Content-Length: ' . strlen($result['content']));
        
        // Security headers for PDF viewing
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: SAMEORIGIN');

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

            // Extract learner data from BEEF JSON
            $beefData = json_decode($enrollment->beef_data, true);
            
            // Check if this is a returning student
            $isReturningStudent = ($enrollment->is_returning_student == 1);
            $previousLearnerId = null;
            $previousAssessmentId = null;
            
            if ($isReturningStudent && !empty($enrollment->previous_lrn)) {
                // Find previous learner record by LRN
                $previousLearner = $learnerModel->getByLRN($enrollment->previous_lrn);
                if ($previousLearner) {
                    $previousLearnerId = $previousLearner->id;
                    
                    // Get previous assessment
                    $assessmentModel = new Assessment();
                    $previousAssessment = $assessmentModel->getByLearnerId($previousLearnerId);
                    if ($previousAssessment) {
                        $previousAssessmentId = $previousAssessment->id;
                    }
                }
            }
            
            // Create learner record with complete data from BEEF form
            $learnerData = [
                'parent_id' => $enrollment->parent_id,
                'previous_learner_id' => $previousLearnerId, // Link to old record
                'first_name' => $beefData['first_name'] ?? $enrollment->learner_first_name,
                'middle_name' => $beefData['middle_name'] ?? null,
                'last_name' => $beefData['last_name'] ?? $enrollment->learner_last_name,
                'suffix' => $beefData['suffix'] ?? null,
                'date_of_birth' => $beefData['date_of_birth'] ?? $enrollment->learner_dob,
                'grade_level' => $beefData['grade_level'] ?? $enrollment->learner_grade,
                'school_year' => $beefData['school_year'] ?? date('Y') . '-' . (date('Y') + 1),
                'status' => 'enrolled'
            ];

            $learnerId = $learnerModel->createFromEnrollment($learnerData);

            if (!$learnerId) {
                throw new Exception("Failed to create learner record");
            }

            // ============================================
            // AUTO-GENERATE LRN AND LEARNER ACCOUNT
            // ============================================
            
            try {
                // Generate unique 12-digit LRN
                $lrn = $learnerModel->generateLRN();
                
                // Create learner account with LRN as username
                $userId = $learnerModel->createLearnerAccount($learnerId, $lrn);
                
                if (!$userId) {
                    throw new Exception("Failed to create learner account");
                }
                
                // Log LRN generation
                $learnerModel->logLRNGeneration($lrn, $learnerId, $enrollmentId, $verifiedBy);
                
                // Update enrollment to mark LRN as generated
                $this->enrollmentModel->markLRNGenerated($enrollmentId);
                
                // Get learner full name for email
                $learnerFullName = trim(
                    $learnerData['first_name'] . ' ' . 
                    ($learnerData['middle_name'] ?? '') . ' ' . 
                    $learnerData['last_name'] . ' ' . 
                    ($learnerData['suffix'] ?? '')
                );
                
                // Send approval email with LRN and credentials
                $this->sendApprovalWithCredentials(
                    $enrollment,
                    $learnerFullName,
                    $lrn,
                    'default123' // Default password
                );
                
            } catch (Exception $e) {
                // Log error but don't fail the approval
                $this->auditLog->logError(
                    'lrn_generation',
                    'high',
                    'LRN generation failed: ' . $e->getMessage(),
                    $learnerId,
                    ['enrollment_id' => $enrollmentId],
                    $verifiedBy
                );
                
                // Still send regular approval email
                $this->sendApprovalEmail($enrollment, true);
            }

            // Log approval action
            $this->auditLog->logApprovalAction(
                $verifiedBy,
                'enrollment',
                $enrollmentId,
                'approve',
                'Enrollment approved, learner record created (ID: ' . $learnerId . '), LRN generated: ' . ($lrn ?? 'N/A')
            );

            // Redirect back to verification page with success message
            header('Location: ' . URLROOT . '/enrollment/verify?success=Enrollment approved successfully. LRN generated: ' . ($lrn ?? 'N/A'));
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
     * Send approval email with LRN and learner credentials
     */
    private function sendApprovalWithCredentials($enrollment, $learnerName, $lrn, $defaultPassword)
    {
        try {
            $parentName = $enrollment->parent_name ?? 'Parent/Guardian';
            $gradeLevel = $enrollment->learner_grade ?? 'N/A';
            
            $subject = 'Enrollment Approved - Learner Account Created';
            
            $body = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                <div style='background: linear-gradient(135deg, #a01422 0%, #1e4072 100%); padding: 20px; text-align: center;'>
                    <h2 style='color: white; margin: 0;'>SignED SPED System</h2>
                </div>
                
                <div style='padding: 30px; background-color: #f9f9f9;'>
                    <h3 style='color: #a01422;'>Enrollment Approved!</h3>
                    
                    <p>Dear {$parentName},</p>
                    
                    <p>Good news! Your child's enrollment has been approved.</p>
                    
                    <div style='background-color: white; padding: 20px; border-left: 4px solid #a01422; margin: 20px 0;'>
                        <p style='margin: 5px 0;'><strong>Student:</strong> {$learnerName}</p>
                        <p style='margin: 5px 0;'><strong>Grade:</strong> {$gradeLevel}</p>
                        <p style='margin: 5px 0;'><strong>Status:</strong> <span style='color: #28a745;'>Enrolled</span></p>
                    </div>
                    
                    <h4 style='color: #1e4072; margin-top: 30px;'>LEARNER ACCOUNT CREDENTIALS:</h4>
                    
                    <div style='background-color: #fff3cd; padding: 20px; border-radius: 5px; margin: 20px 0;'>
                        <p style='margin: 10px 0;'><strong>Username (LRN):</strong> <span style='font-size: 18px; color: #a01422;'>{$lrn}</span></p>
                        <p style='margin: 10px 0;'><strong>Password:</strong> <span style='font-size: 18px; color: #a01422;'>{$defaultPassword}</span></p>
                    </div>
                    
                    <p>Your child can now login to the SignED SPED System using these credentials.</p>
                    
                    <div style='background-color: #d1ecf1; padding: 15px; border-radius: 5px; margin: 20px 0;'>
                        <p style='margin: 0;'><strong>IMPORTANT:</strong> Please change the password after first login for security.</p>
                    </div>
                    
                    <p style='text-align: center; margin: 30px 0;'>
                        <a href='" . URLROOT . "/auth/login' style='background-color: #a01422; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block;'>Login Now</a>
                    </p>
                    
                    <p style='margin-top: 30px;'>Thank you,<br><strong>SignED SPED System</strong></p>
                </div>
                
                <div style='background-color: #333; color: white; padding: 15px; text-align: center; font-size: 12px;'>
                    <p style='margin: 0;'>This is an automated message. Please do not reply to this email.</p>
                </div>
            </div>
            ";
            
            // Send email
            $emailSent = $this->mailer->sendEmail(
                $enrollment->parent_email,
                $subject,
                $body
            );

            // Log email attempt
            $this->auditLog->logEmailSent(
                $this->getCurrentUserId(),
                $enrollment->parent_email,
                $subject,
                'enrollment_approval_with_credentials',
                $emailSent
            );

        } catch (Exception $e) {
            $this->auditLog->logError('email', 'medium', 'Failed to send approval email with credentials: ' . $e->getMessage());
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
     * Returns learner data based on LRN or Full Name for auto-filling BEEF form
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
            $searchInput = trim($_POST['lrn'] ?? '');

            if (empty($searchInput)) {
                throw new Exception("Please enter an LRN or full name to search.");
            }

            $learnerModel = $this->model('Learner');
            $parentId = $this->getCurrentUserId();
            $learner = null;

            // Check if input is LRN (12 digits) or name
            if (strlen($searchInput) === 12 && ctype_digit($searchInput)) {
                // Search by LRN
                $learner = $learnerModel->getByLRN($searchInput);
                
                if ($learner && $learner->parent_id != $parentId) {
                    throw new Exception("This learner does not belong to your account.");
                }
            } else {
                // Search by full name
                $learners = $learnerModel->searchByName($searchInput, $parentId);
                
                if (empty($learners)) {
                    throw new Exception("No learner found with name: " . $searchInput);
                } elseif (count($learners) > 1) {
                    // Multiple matches found
                    $names = array_map(function($l) {
                        return $l->first_name . ' ' . $l->last_name . ' (LRN: ' . $l->lrn . ')';
                    }, $learners);
                    throw new Exception("Multiple learners found. Please use LRN to search: " . implode(', ', $names));
                } else {
                    $learner = $learners[0];
                }
            }

            if (!$learner) {
                throw new Exception("No learner found with the provided information.");
            }

            // Get previous enrollment data if exists
            $previousEnrollment = $this->enrollmentModel->getLatestByLearner($learner->id);

            // Prepare response data
            $responseData = [
                'success' => true,
                'learner' => [
                    'lrn' => $learner->lrn,
                    'psa_birth_cert' => $learner->psa_birth_cert ?? '',
                    'first_name' => $learner->first_name,
                    'last_name' => $learner->last_name,
                    'middle_name' => $learner->middle_name ?? '',
                    'extension_name' => $learner->suffix ?? '',
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
                'LRN/Name lookup successful',
                'Search: ' . $searchInput . ', Found: ' . $learner->lrn
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

    /**
     * View enrollment details (for SPED staff)
     */
    public function viewDetails()
    {
        $this->requireSpedStaff();

        $enrollmentId = $_GET['id'] ?? null;
        if (!$enrollmentId) {
            die('Enrollment ID required');
        }

        // Get enrollment details
        $enrollment = $this->enrollmentModel->getById($enrollmentId);
        if (!$enrollment) {
            die('Enrollment not found');
        }

        // Get documents
        $documents = $this->enrollmentModel->getDocuments($enrollmentId);

        // Get current user info for sidebar
        $userModel = $this->model('User');
        $currentUser = $userModel->getUserById($this->getCurrentUserId());
        
        $data = [
            'enrollment' => $enrollment,
            'documents' => $documents,
            'role' => $this->getCurrentUserRole(),
            'user_name' => $currentUser->fullname ?? ($currentUser->first_name . ' ' . $currentUser->last_name),
            'current_page' => 'verify'
        ];

        $this->view('enrollment/view', $data);
    }

    /**
     * View enrollment for verification (alias for viewDetails)
     */
    public function viewEnrollment()
    {
        $this->viewDetails();
    }

    /**
     * View BEEF Form data (read-only)
     */
    public function viewBeef()
    {
        $this->requireSpedStaff();

        $enrollmentId = $_GET['id'] ?? null;
        if (!$enrollmentId) {
            die('Enrollment ID required');
        }

        // Get enrollment details
        $enrollment = $this->enrollmentModel->getById($enrollmentId);
        if (!$enrollment) {
            die('Enrollment not found');
        }

        // Decode BEEF data
        $beefData = json_decode($enrollment->beef_data, true);
        if (!$beefData) {
            die('BEEF form data not found');
        }

        // Get current user info for sidebar
        $userModel = $this->model('User');
        $currentUser = $userModel->getUserById($this->getCurrentUserId());
        
        $data = [
            'enrollment' => $enrollment,
            'beef_data' => $beefData,
            'role' => $this->getCurrentUserRole(),
            'user_name' => $currentUser->fullname ?? ($currentUser->first_name . ' ' . $currentUser->last_name),
            'current_page' => 'verify'
        ];

        $this->view('enrollment/viewBeef', $data);
    }
}