<?php

class IepController extends Controller
{
    private $iepModel;
    private $learnerModel;
    private $assessmentModel;
    private $auditLog;

    public function __construct()
    {
        $this->iepModel = $this->model('Iep');
        $this->learnerModel = $this->model('Learner');
        $this->assessmentModel = $this->model('Assessment');
        $this->auditLog = $this->model('AuditLog');
    }

    /**
     * List all IEPs (for SPED teacher)
     */
    public function list()
    {
        $this->requireSpedStaff();

        // Get all IEPs with learner info
        $ieps = $this->iepModel->getAllWithLearners();

        $data = [
            'ieps' => $ieps,
            'role' => $_SESSION['role'] ?? 'sped_teacher',
            'user_name' => $_SESSION['fullname'] ?? 'SPED Teacher',
            'current_page' => 'iep'
        ];

        $this->view('iep/list', $data);
    }

    /**
     * Create new IEP draft
     */
    public function create()
    {
        $this->requireSpedStaff();

        $learnerId = $_GET['learner_id'] ?? null;

        if (!$learnerId) {
            header('Location: ' . URLROOT . '/iep/list?error=Learner ID required');
            exit;
        }

        // Get learner info
        $learner = $this->learnerModel->getById($learnerId);
        if (!$learner) {
            header('Location: ' . URLROOT . '/iep/list?error=Learner not found');
            exit;
        }

        // Get assessment data
        $assessment = $this->assessmentModel->getByLearnerId($learnerId);

        // Check if IEP already exists
        $existingIep = $this->iepModel->getByLearnerId($learnerId);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleCreate($learnerId, $existingIep);
        } else {
            $this->showCreateForm($learner, $assessment, $existingIep);
        }
    }

    /**
     * Show IEP creation form
     */
    private function showCreateForm($learner, $assessment, $existingIep = null, $data = [])
    {
        $data['learner'] = $learner;
        $data['assessment'] = $assessment;
        $data['existing_iep'] = $existingIep;
        $data['role'] = $_SESSION['role'] ?? 'sped_teacher';
        $data['user_name'] = $_SESSION['fullname'] ?? 'SPED Teacher';
        $data['current_page'] = 'iep';

        $this->view('iep/create', $data);
    }

    /**
     * Handle IEP creation/update
     */
    private function handleCreate($learnerId, $existingIep)
    {
        try {
            $createdBy = $_SESSION['user_id'] ?? null;

            // Collect IEP data
            $iepData = [
                'learner_id' => $learnerId,
                'assessment_id' => $_POST['assessment_id'] ?? null,
                'created_by' => $createdBy,
                'status' => 'draft'
            ];

            // Collect goals
            $goals = [];
            if (isset($_POST['goals']) && is_array($_POST['goals'])) {
                foreach ($_POST['goals'] as $goal) {
                    if (!empty($goal['domain']) && !empty($goal['skill'])) {
                        $goals[] = [
                            'domain' => trim($goal['domain']),
                            'skill' => trim($goal['skill']),
                            'description' => trim($goal['description'] ?? ''),
                            'quarter1_recommendation' => trim($goal['quarter1'] ?? ''),
                            'quarter2_recommendation' => trim($goal['quarter2'] ?? ''),
                            'mastered_yes' => isset($goal['mastered']) && $goal['mastered'] === 'yes' ? 1 : 0,
                            'mastered_no' => isset($goal['mastered']) && $goal['mastered'] === 'no' ? 1 : 0,
                            'performance_level' => trim($goal['performance_level'] ?? '')
                        ];
                    }
                }
            }

            // Collect services
            $services = [];
            if (isset($_POST['services']) && is_array($_POST['services'])) {
                foreach ($_POST['services'] as $service) {
                    if (!empty($service['type'])) {
                        $services[] = [
                            'service_type' => trim($service['type']),
                            'provider' => trim($service['provider'] ?? ''),
                            'frequency' => trim($service['frequency'] ?? ''),
                            'duration' => trim($service['duration'] ?? ''),
                            'location' => trim($service['location'] ?? '')
                        ];
                    }
                }
            }

            // Collect accommodations
            $accommodations = [];
            if (isset($_POST['accommodations']) && is_array($_POST['accommodations'])) {
                foreach ($_POST['accommodations'] as $accommodation) {
                    if (!empty($accommodation['type']) && !empty($accommodation['description'])) {
                        $accommodations[] = [
                            'accommodation_type' => trim($accommodation['type']),
                            'description' => trim($accommodation['description'])
                        ];
                    }
                }
            }

            // Store as draft data
            $draftData = [
                'goals' => $goals,
                'services' => $services,
                'accommodations' => $accommodations,
                'remarks' => trim($_POST['remarks'] ?? '')
            ];

            $iepData['draft_data'] = json_encode($draftData);

            if ($existingIep) {
                // Update existing IEP
                $result = $this->iepModel->updateDraft($existingIep->id, $iepData);
                $iepId = $existingIep->id;
                $message = 'IEP draft updated successfully';
            } else {
                // Create new IEP
                $iepId = $this->iepModel->createDraft($iepData);
                $result = $iepId ? true : false;
                $message = 'IEP draft created successfully';
            }

            if (!$result) {
                throw new Exception("Failed to save IEP draft");
            }

            // Save goals, services, accommodations
            if ($iepId) {
                $this->iepModel->saveGoals($iepId, $goals);
                $this->iepModel->saveServices($iepId, $services);
                $this->iepModel->saveAccommodations($iepId, $accommodations);
            }

            // Log action
            $this->auditLog->logAction(
                $createdBy,
                'iep_draft_saved',
                'IEP draft saved for learner ID: ' . $learnerId
            );

            header('Location: ' . URLROOT . '/iep/create?learner_id=' . $learnerId . '&success=' . urlencode($message));
            exit;

        } catch (Exception $e) {
            error_log("IEP Creation Error: " . $e->getMessage());
            $this->showCreateForm($this->learnerModel->getById($learnerId), null, $existingIep, ['error' => $e->getMessage()]);
        }
    }

    /**
     * Send IEP (proceed to document upload)
     */
    public function send()
    {
        $this->requireSpedStaff();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . URLROOT . '/iep/list');
            exit;
        }

        $iepId = $_POST['iep_id'] ?? null;

        if (!$iepId) {
            header('Location: ' . URLROOT . '/iep/list?error=IEP ID required');
            exit;
        }

        try {
            // Get IEP
            $iep = $this->iepModel->getById($iepId);
            if (!$iep) {
                throw new Exception("IEP not found");
            }

            // Validate minimum requirements
            $draftData = json_decode($iep->draft_data, true);
            
            if (empty($draftData['goals']) || count($draftData['goals']) < 1) {
                throw new Exception("At least 1 goal is required to send IEP");
            }

            if (empty($draftData['services']) || count($draftData['services']) < 1) {
                throw new Exception("At least 1 service is required to send IEP");
            }

            // Update status to pending_upload
            $this->iepModel->updateStatus($iepId, 'pending_upload');

            // Log action
            $this->auditLog->logAction(
                $_SESSION['user_id'],
                'iep_sent',
                'IEP sent for document upload, IEP ID: ' . $iepId
            );

            // Redirect to document upload page
            header('Location: ' . URLROOT . '/iep/uploadDraft?iep_id=' . $iepId);
            exit;

        } catch (Exception $e) {
            header('Location: ' . URLROOT . '/iep/create?learner_id=' . $iep->learner_id . '&error=' . urlencode($e->getMessage()));
            exit;
        }
    }

    /**
     * Upload IEP draft document
     */
    public function uploadDraft()
    {
        $this->requireSpedStaff();

        $iepId = $_GET['iep_id'] ?? null;

        if (!$iepId) {
            header('Location: ' . URLROOT . '/iep/list?error=IEP ID required');
            exit;
        }

        // Get IEP with learner info
        $iep = $this->iepModel->getByIdWithLearner($iepId);
        if (!$iep) {
            header('Location: ' . URLROOT . '/iep/list?error=IEP not found');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleUploadDraft($iepId);
        } else {
            $this->showUploadDraftForm($iep);
        }
    }

    /**
     * Show upload draft form
     */
    private function showUploadDraftForm($iep, $data = [])
    {
        $data['iep'] = $iep;
        $data['role'] = $_SESSION['role'] ?? 'sped_teacher';
        $data['user_name'] = $_SESSION['fullname'] ?? 'SPED Teacher';
        $data['current_page'] = 'iep';

        $this->view('iep/upload_draft', $data);
    }

    /**
     * Handle draft document upload
     */
    private function handleUploadDraft($iepId)
    {
        try {
            // Validate file upload
            if (!isset($_FILES['iep_draft']) || $_FILES['iep_draft']['error'] !== UPLOAD_ERR_OK) {
                throw new Exception("Please upload IEP draft document");
            }

            $file = $_FILES['iep_draft'];

            // Validate file type (PDF only)
            $allowedTypes = ['application/pdf'];
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);

            if (!in_array($mimeType, $allowedTypes)) {
                throw new Exception("Only PDF files are allowed");
            }

            // Validate file size (10MB max)
            $maxSize = 10 * 1024 * 1024; // 10MB
            if ($file['size'] > $maxSize) {
                throw new Exception("File size must not exceed 10MB");
            }

            // Store document
            $documentStore = $this->model('DocumentStore');
            $documentId = $documentStore->store(
                $file,
                'iep_draft',
                $iepId,
                $_SESSION['user_id']
            );

            if (!$documentId) {
                throw new Exception("Failed to store document");
            }

            // Update IEP with document ID
            $this->iepModel->updateDraftDocument($iepId, $documentId);

            // Update status to pending_meeting
            $this->iepModel->updateStatus($iepId, 'pending_meeting');

            // Send notifications to Guidance, Principal, and Parent
            $this->sendDraftNotifications($iepId);

            // Log action
            $this->auditLog->logAction(
                $_SESSION['user_id'],
                'iep_draft_uploaded',
                'IEP draft document uploaded, IEP ID: ' . $iepId
            );

            header('Location: ' . URLROOT . '/iep/scheduleMeeting?iep_id=' . $iepId . '&success=' . urlencode('IEP draft uploaded and shared successfully'));
            exit;

        } catch (Exception $e) {
            $iep = $this->iepModel->getByIdWithLearner($iepId);
            $this->showUploadDraftForm($iep, ['error' => $e->getMessage()]);
        }
    }

    /**
     * Send notifications when draft is uploaded
     */
    private function sendDraftNotifications($iepId)
    {
        $iep = $this->iepModel->getByIdWithLearner($iepId);
        $notificationService = $this->model('NotificationService');
        $userModel = $this->model('User');

        // Get Guidance Counselors
        $guidanceUsers = $userModel->getUsersByRole('guidance');
        foreach ($guidanceUsers as $user) {
            $notificationService->createNotification(
                $user->id,
                'iep_draft_shared',
                'New IEP draft for ' . $iep->first_name . ' ' . $iep->last_name . ' is ready for your review',
                '/iep/reviewDraft?iep_id=' . $iepId
            );
        }

        // Get Principals
        $principals = $userModel->getUsersByRole('principal');
        foreach ($principals as $user) {
            $notificationService->createNotification(
                $user->id,
                'iep_draft_shared',
                'New IEP draft for ' . $iep->first_name . ' ' . $iep->last_name . ' is ready for your review',
                '/iep/reviewDraft?iep_id=' . $iepId
            );
        }

        // Notify Parent
        if ($iep->parent_id) {
            $notificationService->createNotification(
                $iep->parent_id,
                'iep_draft_shared',
                'IEP draft for ' . $iep->first_name . ' ' . $iep->last_name . ' has been prepared. A meeting will be scheduled soon.',
                '/iep/viewDraft?iep_id=' . $iepId
            );
        }
    }

    /**
     * Schedule IEP meeting
     */
    public function scheduleMeeting()
    {
        $this->requireSpedStaff();

        $iepId = $_GET['iep_id'] ?? null;

        if (!$iepId) {
            header('Location: ' . URLROOT . '/iep/list?error=IEP ID required');
            exit;
        }

        // Get IEP with learner info
        $iep = $this->iepModel->getByIdWithLearner($iepId);
        if (!$iep) {
            header('Location: ' . URLROOT . '/iep/list?error=IEP not found');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleScheduleMeeting($iepId);
        } else {
            $this->showScheduleMeetingForm($iep);
        }
    }

    /**
     * Show meeting scheduling form
     */
    private function showScheduleMeetingForm($iep, $data = [])
    {
        $data['iep'] = $iep;
        $data['role'] = $_SESSION['role'] ?? 'sped_teacher';
        $data['user_name'] = $_SESSION['fullname'] ?? 'SPED Teacher';
        $data['current_page'] = 'iep';

        $this->view('iep/schedule_meeting', $data);
    }

    /**
     * Handle meeting scheduling
     */
    private function handleScheduleMeeting($iepId)
    {
        try {
            // Validate meeting date (minimum 3 days notice)
            $meetingDate = $_POST['meeting_date'] ?? null;
            if ($meetingDate) {
                $selectedDate = strtotime($meetingDate);
                $minDate = strtotime('+3 days');
                
                if ($selectedDate < $minDate) {
                    throw new Exception("Meeting date must be at least 3 days from today");
                }
            }

            $meetingData = [
                'iep_id' => $iepId,
                'meeting_date' => $meetingDate,
                'meeting_time' => $_POST['meeting_time'] ?? null,
                'location' => trim($_POST['location'] ?? ''),
                'agenda' => trim($_POST['agenda'] ?? ''),
                'scheduled_by' => $_SESSION['user_id'] ?? null
            ];

            // Validate required fields
            if (empty($meetingData['meeting_date']) || empty($meetingData['meeting_time'])) {
                throw new Exception("Meeting date and time are required");
            }

            // Create meeting
            $meetingModel = $this->model('IepMeeting');
            $meetingId = $meetingModel->create($meetingData);

            if (!$meetingId) {
                throw new Exception("Failed to schedule meeting");
            }

            // Add participants
            $participants = $_POST['participants'] ?? [];
            if (empty($participants)) {
                throw new Exception("At least one participant is required");
            }

            $this->addMeetingParticipants($meetingId, $iepId, $participants);

            // Update IEP status and meeting_scheduled flag
            $this->iepModel->updateStatus($iepId, 'meeting_scheduled');
            $this->iepModel->markMeetingScheduled($iepId);

            // Send invitations to all participants
            $this->sendMeetingInvitations($meetingId, $iepId);

            // Log action
            $this->auditLog->logAction(
                $_SESSION['user_id'],
                'iep_meeting_scheduled',
                'IEP meeting scheduled, Meeting ID: ' . $meetingId
            );

            header('Location: ' . URLROOT . '/iep/meetings?success=' . urlencode('Meeting scheduled successfully. Invitations sent to all participants.'));
            exit;

        } catch (Exception $e) {
            $iep = $this->iepModel->getByIdWithLearner($iepId);
            $this->showScheduleMeetingForm($iep, ['error' => $e->getMessage()]);
        }
    }

    /**
     * Add participants to meeting
     */
    private function addMeetingParticipants($meetingId, $iepId, $participantTypes)
    {
        $participantModel = $this->model('IepParticipant');
        $userModel = $this->model('User');
        $iep = $this->iepModel->getByIdWithLearner($iepId);

        foreach ($participantTypes as $type) {
            $participantData = [
                'meeting_id' => $meetingId,
                'participant_type' => $type,
                'is_required' => in_array($type, ['parent', 'sped_teacher', 'guidance', 'principal']) ? 1 : 0
            ];

            switch ($type) {
                case 'parent':
                    $participantData['user_id'] = $iep->parent_id ?? null;
                    $participantData['name'] = $iep->parent_name ?? 'Parent/Guardian';
                    $participantData['email'] = $iep->parent_email ?? null;
                    break;

                case 'sped_teacher':
                    $participantData['user_id'] = $_SESSION['user_id'];
                    $participantData['name'] = $_SESSION['fullname'];
                    $participantData['email'] = $_SESSION['email'] ?? null;
                    break;

                case 'guidance':
                    // Get first guidance counselor
                    $guidanceUsers = $userModel->getUsersByRole('guidance');
                    if (!empty($guidanceUsers)) {
                        $guidance = $guidanceUsers[0];
                        $participantData['user_id'] = $guidance->id;
                        $participantData['name'] = $guidance->fullname;
                        $participantData['email'] = $guidance->email;
                    } else {
                        $participantData['name'] = 'Guidance Counselor';
                    }
                    break;

                case 'principal':
                    // Get first principal
                    $principals = $userModel->getUsersByRole('principal');
                    if (!empty($principals)) {
                        $principal = $principals[0];
                        $participantData['user_id'] = $principal->id;
                        $participantData['name'] = $principal->fullname;
                        $participantData['email'] = $principal->email;
                    } else {
                        $participantData['name'] = 'Principal';
                    }
                    break;

                case 'gen_ed_teacher':
                    $participantData['name'] = 'General Education Teacher';
                    break;

                case 'specialist':
                    $participantData['name'] = 'Specialist';
                    break;
            }

            $participantModel->add($participantData);
        }
    }

    /**
     * Send meeting invitations to participants
     */
    private function sendMeetingInvitations($meetingId, $iepId)
    {
        $participantModel = $this->model('IepParticipant');
        $notificationService = $this->model('NotificationService');
        $participants = $participantModel->getByMeetingId($meetingId);
        $iep = $this->iepModel->getByIdWithLearner($iepId);
        $meeting = $this->model('IepMeeting')->getById($meetingId);

        foreach ($participants as $participant) {
            if ($participant->user_id) {
                $message = 'You are invited to IEP meeting for ' . $iep->first_name . ' ' . $iep->last_name . 
                          ' on ' . date('F j, Y', strtotime($meeting->meeting_date)) . 
                          ' at ' . date('g:i A', strtotime($meeting->meeting_time)) . 
                          '. Please confirm your attendance.';

                $notificationService->createNotification(
                    $participant->user_id,
                    'meeting_invitation',
                    $message,
                    '/iep/confirmAttendance?participant_id=' . $participant->id
                );
            }
        }
    }

    /**
     * View IEP meetings
     */
    public function meetings()
    {
        $this->requireSpedStaff();

        $meetingModel = $this->model('IepMeeting');
        $meetings = $meetingModel->getAllWithDetails();

        $data = [
            'meetings' => $meetings,
            'role' => $_SESSION['role'] ?? 'sped_teacher',
            'user_name' => $_SESSION['fullname'] ?? 'SPED Teacher',
            'current_page' => 'iep'
        ];

        $this->view('iep/meetings', $data);
    }

    /**
     * Confirm meeting attendance
     */
    public function confirmAttendance()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . URLROOT . '/auth/login');
            exit;
        }

        $participantId = $_GET['participant_id'] ?? null;

        if (!$participantId) {
            header('Location: ' . URLROOT . '/' . $_SESSION['role'] . '/dashboard?error=Participant ID required');
            exit;
        }

        $participantModel = $this->model('IepParticipant');
        $participant = $participantModel->getById($participantId);

        if (!$participant) {
            header('Location: ' . URLROOT . '/' . $_SESSION['role'] . '/dashboard?error=Participant not found');
            exit;
        }

        // Verify user has access
        if ($participant->user_id != $_SESSION['user_id']) {
            header('Location: ' . URLROOT . '/' . $_SESSION['role'] . '/dashboard?error=Access denied');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleConfirmAttendance($participantId, $participant);
        } else {
            $this->showConfirmAttendanceForm($participant);
        }
    }

    /**
     * Show confirm attendance form
     */
    private function showConfirmAttendanceForm($participant, $data = [])
    {
        $data['participant'] = $participant;
        $data['role'] = $_SESSION['role'];
        $data['user_name'] = $_SESSION['fullname'];
        $data['current_page'] = 'iep';

        $this->view('iep/confirm_attendance', $data);
    }

    /**
     * Handle attendance confirmation
     */
    private function handleConfirmAttendance($participantId, $participant)
    {
        try {
            $action = $_POST['action'] ?? null;
            $participantModel = $this->model('IepParticipant');

            if ($action === 'confirm') {
                // Confirm attendance
                $participantModel->updateStatus($participantId, 'confirmed');

                // Log action
                $this->auditLog->logAction(
                    $_SESSION['user_id'],
                    'meeting_attendance_confirmed',
                    'Confirmed attendance for meeting ID: ' . $participant->meeting_id
                );

                header('Location: ' . URLROOT . '/iep/confirmAttendance?participant_id=' . $participantId . '&success=' . urlencode('Attendance confirmed successfully'));
                exit;

            } elseif ($action === 'decline') {
                $declineReason = trim($_POST['decline_reason'] ?? '');

                if (empty($declineReason)) {
                    throw new Exception("Please provide a reason for declining");
                }

                // Decline attendance
                $participantModel->updateStatus($participantId, 'declined', $declineReason);

                // If parent declined, notify SPED teacher to reschedule
                if ($participant->participant_type === 'parent') {
                    $meetingModel = $this->model('IepMeeting');
                    $meeting = $meetingModel->getById($participant->meeting_id);
                    
                    // Update meeting status
                    $meetingModel->updateStatus($participant->meeting_id, 'cancelled');

                    // Notify SPED teacher
                    $notificationService = $this->model('NotificationService');
                    $notificationService->createNotification(
                        $meeting->scheduled_by,
                        'meeting_parent_declined',
                        'Parent declined IEP meeting for ' . $participant->first_name . ' ' . $participant->last_name . '. Meeting needs to be rescheduled. Reason: ' . $declineReason,
                        '/iep/scheduleMeeting?iep_id=' . $meeting->iep_id
                    );

                    // Update IEP status back to pending_meeting
                    $this->iepModel->updateStatus($meeting->iep_id, 'pending_meeting');
                }

                // Log action
                $this->auditLog->logAction(
                    $_SESSION['user_id'],
                    'meeting_attendance_declined',
                    'Declined attendance for meeting ID: ' . $participant->meeting_id
                );

                header('Location: ' . URLROOT . '/iep/confirmAttendance?participant_id=' . $participantId . '&success=' . urlencode('Response recorded. ' . ($participant->participant_type === 'parent' ? 'SPED teacher will reschedule the meeting.' : '')));
                exit;

            } else {
                throw new Exception("Invalid action");
            }

        } catch (Exception $e) {
            $this->showConfirmAttendanceForm($participant, ['error' => $e->getMessage()]);
        }
    }

    /**
     * Record meeting notes
     */
    public function recordMeeting()
    {
        $this->requireSpedStaff();

        $meetingId = $_GET['meeting_id'] ?? null;

        if (!$meetingId) {
            header('Location: ' . URLROOT . '/iep/meetings?error=Meeting ID required');
            exit;
        }

        $meetingModel = $this->model('IepMeeting');
        $meeting = $meetingModel->getById($meetingId);

        if (!$meeting) {
            header('Location: ' . URLROOT . '/iep/meetings?error=Meeting not found');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleRecordMeeting($meetingId, $meeting);
        } else {
            $this->showRecordMeetingForm($meeting);
        }
    }

    /**
     * Show record meeting form
     */
    private function showRecordMeetingForm($meeting, $data = [])
    {
        // Get participants
        $participantModel = $this->model('IepParticipant');
        $participants = $participantModel->getByMeetingId($meeting->id);

        // Get feedback
        $feedbackModel = $this->model('IepFeedback');
        $feedback = $feedbackModel->getByIepId($meeting->iep_id);

        $data['meeting'] = $meeting;
        $data['participants'] = $participants;
        $data['feedback'] = $feedback;
        $data['role'] = $_SESSION['role'];
        $data['user_name'] = $_SESSION['fullname'];
        $data['current_page'] = 'iep';

        $this->view('iep/record_meeting', $data);
    }

    /**
     * Handle record meeting submission
     */
    private function handleRecordMeeting($meetingId, $meeting)
    {
        try {
            $meetingNotes = trim($_POST['meeting_notes'] ?? '');
            $decisions = trim($_POST['decisions'] ?? '');

            if (empty($meetingNotes) || empty($decisions)) {
                throw new Exception("Meeting notes and decisions are required");
            }

            $meetingModel = $this->model('IepMeeting');

            // Record meeting notes and decisions
            $meetingModel->recordMinutes($meetingId, $meetingNotes);
            
            // Update decisions
            $sql = "UPDATE iep_meetings 
                    SET decisions = :decisions,
                        completed_by = :completed_by,
                        completed_at = CURRENT_TIMESTAMP,
                        status = 'completed',
                        updated_at = CURRENT_TIMESTAMP
                    WHERE id = :id";

            $stmt = $meetingModel->db->prepare($sql);
            $stmt->execute([
                ':decisions' => $decisions,
                ':completed_by' => $_SESSION['user_id'],
                ':id' => $meetingId
            ]);

            // Update IEP status to meeting_completed
            $this->iepModel->updateStatus($meeting->iep_id, 'meeting_completed');

            // Update learner status
            $learnerModel = $this->model('Learner');
            $sql = "UPDATE learners 
                    SET iep_status = 'iep_meeting_complete' 
                    WHERE id = :id";
            $stmt = $learnerModel->db->prepare($sql);
            $stmt->execute([':id' => $meeting->learner_id]);

            // Notify participants that meeting is completed
            $this->notifyMeetingCompleted($meetingId, $meeting);

            // Log action
            $this->auditLog->logAction(
                $_SESSION['user_id'],
                'iep_meeting_completed',
                'IEP meeting completed, Meeting ID: ' . $meetingId
            );

            header('Location: ' . URLROOT . '/iep/meetings?success=' . urlencode('Meeting notes recorded successfully. IEP is ready for finalization.'));
            exit;

        } catch (Exception $e) {
            $this->showRecordMeetingForm($meeting, ['error' => $e->getMessage()]);
        }
    }

    /**
     * Notify participants that meeting is completed
     */
    private function notifyMeetingCompleted($meetingId, $meeting)
    {
        $participantModel = $this->model('IepParticipant');
        $notificationService = $this->model('NotificationService');
        $participants = $participantModel->getByMeetingId($meetingId);

        foreach ($participants as $participant) {
            if ($participant->user_id) {
                $message = 'IEP meeting for ' . $meeting->first_name . ' ' . $meeting->last_name . 
                          ' has been completed. Meeting notes have been recorded.';

                $notificationService->createNotification(
                    $participant->user_id,
                    'meeting_completed',
                    $message,
                    '/iep/view?id=' . $meeting->iep_id
                );
            }
        }
    }

    /**
     * Review IEP draft (for Guidance and Principal)
     */
    public function reviewDraft()
    {
        $this->requireSpedStaff();

        $iepId = $_GET['iep_id'] ?? null;

        if (!$iepId) {
            header('Location: ' . URLROOT . '/iep/list?error=IEP ID required');
            exit;
        }

        $iep = $this->iepModel->getByIdWithLearner($iepId);
        if (!$iep) {
            header('Location: ' . URLROOT . '/iep/list?error=IEP not found');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleReviewDraft($iepId);
        } else {
            $this->showReviewDraftForm($iep);
        }
    }

    /**
     * Show review draft form
     */
    private function showReviewDraftForm($iep, $data = [])
    {
        // Get existing feedback
        $feedbackModel = $this->model('IepFeedback');
        $existingFeedback = $feedbackModel->getByIepId($iep->id);

        $data['iep'] = $iep;
        $data['existing_feedback'] = $existingFeedback;
        $data['role'] = $_SESSION['role'] ?? 'sped_teacher';
        $data['user_name'] = $_SESSION['fullname'] ?? 'SPED Teacher';
        $data['current_page'] = 'iep';

        $this->view('iep/review_draft', $data);
    }

    /**
     * Handle review draft submission
     */
    private function handleReviewDraft($iepId)
    {
        try {
            $feedback = trim($_POST['feedback'] ?? '');

            if (empty($feedback)) {
                throw new Exception("Feedback is required");
            }

            $feedbackModel = $this->model('IepFeedback');
            $feedbackModel->create([
                'iep_id' => $iepId,
                'user_id' => $_SESSION['user_id'],
                'user_role' => $_SESSION['role'],
                'feedback_type' => 'draft_review',
                'feedback' => $feedback
            ]);

            // Log action
            $this->auditLog->logAction(
                $_SESSION['user_id'],
                'iep_feedback_added',
                'Feedback added to IEP ID: ' . $iepId
            );

            header('Location: ' . URLROOT . '/iep/reviewDraft?iep_id=' . $iepId . '&success=' . urlencode('Feedback submitted successfully'));
            exit;

        } catch (Exception $e) {
            $iep = $this->iepModel->getByIdWithLearner($iepId);
            $this->showReviewDraftForm($iep, ['error' => $e->getMessage()]);
        }
    }

    /**
     * View IEP draft (for Parent - read-only)
     */
    public function viewDraft()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . URLROOT . '/auth/login');
            exit;
        }

        $iepId = $_GET['iep_id'] ?? null;

        if (!$iepId) {
            header('Location: ' . URLROOT . '/parent/dashboard?error=IEP ID required');
            exit;
        }

        $iep = $this->iepModel->getByIdWithLearner($iepId);
        if (!$iep) {
            header('Location: ' . URLROOT . '/parent/dashboard?error=IEP not found');
            exit;
        }

        // Verify parent has access to this IEP
        if ($_SESSION['role'] === 'parent' && $iep->parent_id != $_SESSION['user_id']) {
            header('Location: ' . URLROOT . '/parent/dashboard?error=Access denied');
            exit;
        }

        $data = [
            'iep' => $iep,
            'role' => $_SESSION['role'],
            'user_name' => $_SESSION['fullname'],
            'current_page' => 'iep'
        ];

        $this->view('iep/view_draft', $data);
    }

    /**
     * Require SPED staff role
     */
    public function requireSpedStaff()
    {
        $allowedRoles = ['sped_teacher', 'guidance', 'principal', 'admin'];
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'] ?? '', $allowedRoles)) {
            header('Location: ' . URLROOT . '/auth/login');
            exit;
        }
    }
}
