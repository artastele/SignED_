<?php

class IepController extends Controller
{
    private $iepMeeting;
    private $iep;
    private $learner;
    private $assessment;
    private $mailer;
    private $auditLog;
    private $documentStore;

    public function __construct()
    {
        parent::__construct();
        $this->requireAuth();
        $this->iepMeeting = $this->model('IepMeeting');
        $this->iep = $this->model('Iep');
        $this->learner = $this->model('Learner');
        $this->assessment = $this->model('Assessment');
        $this->documentStore = $this->model('DocumentStore');
        require_once __DIR__ . '/../helpers/Mailer.php';
        $this->mailer = new Mailer();
        $this->auditLog = $this->model('AuditLog');
    }

    /**
     * Schedule IEP meeting
     * Requirements: 6.1, 6.2
     */
    public function scheduleMeeting()
    {
        $this->requireSpedRole(['sped_teacher', 'admin']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->handleMeetingScheduling();
        }

        // Show meeting scheduling form
        $learners = $this->learner->getReadyForIepMeeting();
        $users = $this->getUsersForMeeting();

        $this->view('iep/schedule_meeting', [
            'learners' => $learners,
            'users' => $users
        ]);
    }

    /**
     * Handle meeting scheduling submission
     */
    private function handleMeetingScheduling()
    {
        try {
            // Validate required fields
            $required = ['learner_id', 'meeting_date', 'meeting_time', 'location', 'participants'];
            foreach ($required as $field) {
                if (empty($_POST[$field])) {
                    throw new Exception("Field {$field} is required");
                }
            }

            $learnerId = (int)$_POST['learner_id'];
            $meetingDateTime = $_POST['meeting_date'] . ' ' . $_POST['meeting_time'];
            $location = trim($_POST['location']);
            $participants = $_POST['participants']; // Array of user IDs

            // Validate meeting date is in the future
            if (strtotime($meetingDateTime) <= time()) {
                throw new Exception("Meeting date must be in the future");
            }

            // Validate participants include required roles
            $this->validateMeetingParticipants($participants);

            // Schedule the meeting
            $meetingId = $this->iepMeeting->schedule(
                $learnerId,
                $_SESSION['user_id'],
                $meetingDateTime,
                $location,
                $this->formatParticipants($participants)
            );

            if (!$meetingId) {
                throw new Exception("Failed to schedule meeting");
            }

            // Send email notifications to participants
            $this->sendMeetingNotifications($meetingId, $participants);

            // Log the action
            $this->auditLog->logStatusChange(
                $_SESSION['user_id'],
                'iep_meeting',
                $meetingId,
                null,
                'scheduled'
            );

            $this->redirect('/iep/meetings?success=scheduled');

        } catch (Exception $e) {
            $this->auditLog->logError('iep_meeting', 'medium', 'Meeting scheduling failed: ' . $e->getMessage());
            
            $learners = $this->learner->getReadyForIepMeeting();
            $users = $this->getUsersForMeeting();

            $this->view('iep/schedule_meeting', [
                'learners' => $learners,
                'users' => $users,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Confirm attendance for meeting participant
     * Requirements: 6.3, 6.4
     */
    public function confirmAttendance($meetingId = null)
    {
        if (!$meetingId) {
            $this->redirect('/dashboard?error=invalid_meeting');
            return;
        }

        $meetingId = (int)$meetingId;
        $meeting = $this->iepMeeting->getById($meetingId);

        if (!$meeting) {
            $this->redirect('/dashboard?error=meeting_not_found');
            return;
        }

        // Check if user is a participant
        $participants = $this->iepMeeting->getParticipants($meetingId);
        $isParticipant = false;
        foreach ($participants as $participant) {
            if ($participant->user_id == $_SESSION['user_id']) {
                $isParticipant = true;
                break;
            }
        }

        if (!$isParticipant) {
            $this->redirect('/dashboard?error=not_participant');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $status = $_POST['status'] ?? 'confirmed';
            
            if (in_array($status, ['confirmed', 'declined'])) {
                $result = $this->iepMeeting->confirmParticipant($meetingId, $_SESSION['user_id'], $status);
                
                if ($result) {
                    // Check if all participants confirmed
                    if ($status === 'confirmed' && $this->iepMeeting->allParticipantsConfirmed($meetingId)) {
                        // Update meeting status to confirmed
                        $this->updateMeetingStatus($meetingId, 'confirmed');
                    }

                    // Log the action
                    $this->auditLog->logStatusChange(
                        $_SESSION['user_id'],
                        'iep_meeting_participant',
                        $meetingId,
                        'invited',
                        $status
                    );

                    $this->redirect('/dashboard?success=attendance_' . $status);
                } else {
                    $error = 'Failed to update attendance status';
                }
            } else {
                $error = 'Invalid attendance status';
            }
        }

        $this->view('iep/confirm_attendance', [
            'meeting' => $meeting,
            'participants' => $participants,
            'error' => $error ?? null
        ]);
    }

    /**
     * Record meeting completion
     * Requirements: 6.5, 6.6, 6.7
     */
    public function recordMeeting($meetingId = null)
    {
        $this->requireSpedRole(['sped_teacher', 'admin']);

        if (!$meetingId) {
            $this->redirect('/iep/meetings?error=invalid_meeting');
            return;
        }

        $meetingId = (int)$meetingId;
        $meeting = $this->iepMeeting->getById($meetingId);

        if (!$meeting || $meeting->status !== 'confirmed') {
            $this->redirect('/iep/meetings?error=meeting_not_ready');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->handleMeetingCompletion($meetingId);
        }

        $participants = $this->iepMeeting->getParticipants($meetingId);

        $this->view('iep/record_meeting', [
            'meeting' => $meeting,
            'participants' => $participants
        ]);
    }

    /**
     * Handle meeting completion submission
     */
    private function handleMeetingCompletion($meetingId)
    {
        try {
            $notes = trim($_POST['meeting_notes'] ?? '');
            $signatures = $_POST['signatures'] ?? [];

            if (empty($notes)) {
                throw new Exception("Meeting notes are required");
            }

            // Validate signatures
            $participants = $this->iepMeeting->getParticipants($meetingId);
            foreach ($participants as $participant) {
                if (empty($signatures[$participant->user_id])) {
                    throw new Exception("Signature required for " . $participant->fullname);
                }
            }

            // Format signatures for storage
            $formattedSignatures = [];
            foreach ($signatures as $userId => $signatureData) {
                $formattedSignatures[] = [
                    'user_id' => $userId,
                    'signature_data' => $signatureData
                ];
            }

            // Record meeting completion
            $result = $this->iepMeeting->recordCompletion($meetingId, $notes, $formattedSignatures);

            if (!$result) {
                throw new Exception("Failed to record meeting completion");
            }

            // Update learner status
            $meeting = $this->iepMeeting->getById($meetingId);
            $this->learner->updateStatus($meeting->learner_id, 'iep_meeting_complete');

            // Store meeting record in document store
            $this->storeMeetingRecord($meetingId, $notes, $formattedSignatures);

            // Log the action
            $this->auditLog->logStatusChange(
                $_SESSION['user_id'],
                'iep_meeting',
                $meetingId,
                'confirmed',
                'completed'
            );

            $this->redirect('/iep/meetings?success=meeting_recorded');

        } catch (Exception $e) {
            $this->auditLog->logError('iep_meeting', 'medium', 'Meeting recording failed: ' . $e->getMessage());
            
            $meeting = $this->iepMeeting->getById($meetingId);
            $participants = $this->iepMeeting->getParticipants($meetingId);

            $this->view('iep/record_meeting', [
                'meeting' => $meeting,
                'participants' => $participants,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Create IEP document
     * Requirements: 7.1, 7.2, 7.3
     */
    public function createIep($learnerId = null)
    {
        $this->requireSpedRole(['sped_teacher', 'admin']);

        if (!$learnerId) {
            $this->redirect('/iep/list?error=invalid_learner');
            return;
        }

        $learnerId = (int)$learnerId;
        $learner = $this->learner->getById($learnerId);

        if (!$learner || $learner->status !== 'iep_meeting_complete') {
            $this->redirect('/iep/list?error=learner_not_ready');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->handleIepCreation($learnerId);
        }

        // Pre-populate with assessment data
        $assessmentData = $this->assessment->getForIepGeneration($learnerId);
        $prepopulatedData = $this->prepopulateIepFromAssessment($assessmentData);

        $this->view('iep/create', [
            'learner' => $learner,
            'assessment' => $assessmentData,
            'prepopulated' => $prepopulatedData
        ]);
    }

    /**
     * Handle IEP creation submission
     */
    private function handleIepCreation($learnerId)
    {
        try {
            // Validate required fields
            $required = [
                'present_level_performance',
                'annual_goals',
                'short_term_objectives',
                'special_education_services',
                'accommodations',
                'progress_measurement',
                'start_date',
                'end_date'
            ];

            foreach ($required as $field) {
                if (empty($_POST[$field])) {
                    throw new Exception("Field {$field} is required");
                }
            }

            // Validate dates
            $startDate = $_POST['start_date'];
            $endDate = $_POST['end_date'];

            if (strtotime($startDate) >= strtotime($endDate)) {
                throw new Exception("End date must be after start date");
            }

            // Get assessment data for IEP creation
            $assessmentData = $this->assessment->getForIepGeneration($learnerId);
            
            // Prepare IEP data
            $iepData = [
                'present_level_performance' => trim($_POST['present_level_performance']),
                'annual_goals' => trim($_POST['annual_goals']),
                'short_term_objectives' => trim($_POST['short_term_objectives']),
                'special_education_services' => trim($_POST['special_education_services']),
                'accommodations' => trim($_POST['accommodations']),
                'progress_measurement' => trim($_POST['progress_measurement']),
                'start_date' => $startDate,
                'end_date' => $endDate
            ];

            // Create IEP
            $result = $this->iep->create($learnerId, $_SESSION['user_id'], $iepData);

            if (!$result) {
                throw new Exception("Failed to create IEP");
            }

            // Log the action
            $this->auditLog->logStatusChange(
                $_SESSION['user_id'],
                'iep',
                $result,
                null,
                'draft'
            );

            $this->redirect('/iep/view/' . $result . '?success=created');

        } catch (Exception $e) {
            $this->auditLog->logError('iep', 'medium', 'IEP creation failed: ' . $e->getMessage());
            
            $learner = $this->learner->getById($learnerId);
            $assessmentData = $this->assessment->getForIepGeneration($learnerId);
            $prepopulatedData = $this->prepopulateIepFromAssessment($assessmentData);

            $this->view('iep/create', [
                'learner' => $learner,
                'assessment' => $assessmentData,
                'prepopulated' => $prepopulatedData,
                'error' => $e->getMessage(),
                'form_data' => $_POST
            ]);
        }
    }

    /**
     * Approve IEP (Principal only)
     * Requirements: 7.4, 7.5, 7.6
     */
    public function approve($iepId = null)
    {
        $this->requireSpedRole(['principal', 'admin']);

        if (!$iepId) {
            $this->redirect('/iep/pending?error=invalid_iep');
            return;
        }

        $iepId = (int)$iepId;
        $iep = $this->iep->getById($iepId);

        if (!$iep || $iep->status !== 'pending_approval') {
            $this->redirect('/iep/pending?error=iep_not_ready');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->handleIepApproval($iepId);
        }

        $this->view('iep/approve', [
            'iep' => $iep
        ]);
    }

    /**
     * Handle IEP approval submission
     */
    private function handleIepApproval($iepId)
    {
        try {
            $digitalSignature = $_POST['digital_signature'] ?? '';
            
            if (empty($digitalSignature)) {
                throw new Exception("Digital signature is required");
            }

            // Approve the IEP
            $result = $this->iep->approve($iepId, $_SESSION['user_id'], $digitalSignature);

            if (!$result) {
                throw new Exception("Failed to approve IEP");
            }

            // Send notifications
            $this->sendIepApprovalNotifications($iepId);

            // Log the action
            $this->auditLog->logStatusChange(
                $_SESSION['user_id'],
                'iep',
                $iepId,
                'pending_approval',
                'approved'
            );

            $this->redirect('/iep/pending?success=approved');

        } catch (Exception $e) {
            $this->auditLog->logError('iep', 'medium', 'IEP approval failed: ' . $e->getMessage());
            
            $iep = $this->iep->getById($iepId);
            $this->view('iep/approve', [
                'iep' => $iep,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Reject IEP (Principal only)
     * Requirements: 7.7, 7.8, 7.9
     */
    public function reject($iepId = null)
    {
        $this->requireSpedRole(['principal', 'admin']);

        if (!$iepId) {
            $this->redirect('/iep/pending?error=invalid_iep');
            return;
        }

        $iepId = (int)$iepId;
        $iep = $this->iep->getById($iepId);

        if (!$iep || $iep->status !== 'pending_approval') {
            $this->redirect('/iep/pending?error=iep_not_ready');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->handleIepRejection($iepId);
        }

        $this->view('iep/reject', [
            'iep' => $iep
        ]);
    }

    /**
     * Handle IEP rejection submission
     */
    private function handleIepRejection($iepId)
    {
        try {
            $reason = trim($_POST['rejection_reason'] ?? '');
            
            if (empty($reason)) {
                throw new Exception("Rejection reason is required");
            }

            // Reject the IEP
            $result = $this->iep->reject($iepId, $reason);

            if (!$result) {
                throw new Exception("Failed to reject IEP");
            }

            // Send notifications
            $this->sendIepRejectionNotifications($iepId, $reason);

            // Log the action
            $this->auditLog->logStatusChange(
                $_SESSION['user_id'],
                'iep',
                $iepId,
                'pending_approval',
                'rejected'
            );

            $this->redirect('/iep/pending?success=rejected');

        } catch (Exception $e) {
            $this->auditLog->logError('iep', 'medium', 'IEP rejection failed: ' . $e->getMessage());
            
            $iep = $this->iep->getById($iepId);
            $this->view('iep/reject', [
                'iep' => $iep,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * List meetings
     */
    public function meetings()
    {
        $userRole = $_SESSION['role'];
        
        if ($userRole === 'sped_teacher' || $userRole === 'admin') {
            $meetings = $this->iepMeeting->getByStatus('scheduled');
        } else {
            $meetings = $this->iepMeeting->getForUser($_SESSION['user_id']);
        }

        $this->view('iep/meetings', [
            'meetings' => $meetings,
            'user_role' => $userRole
        ]);
    }

    /**
     * List IEPs
     */
    public function list()
    {
        $userRole = $_SESSION['role'];
        
        if ($userRole === 'principal' || $userRole === 'admin') {
            $ieps = $this->iep->getPendingApproval();
        } else {
            $ieps = $this->iep->getByCreator($_SESSION['user_id']);
        }

        // Get badge counts for sidebar
        $enrollmentModel = $this->model('Enrollment');
        $learnerModel = $this->model('Learner');
        $meetingModel = $this->model('IepMeeting');

        $this->view('iep/list', [
            'ieps' => $ieps,
            'role' => $userRole,
            'current_page' => 'iep',
            'pending_verifications_count' => count($enrollmentModel->getByStatus('pending_verification')),
            'pending_assessments_count' => count($learnerModel->getByStatus('assessment_pending')),
            'upcoming_meetings_count' => count($meetingModel->getUpcoming()),
            'pending_approvals_count' => count($ieps)
        ]);
    }

    /**
     * View IEP details
     */
    public function viewIep($iepId = null)
    {
        if (!$iepId) {
            $this->redirect('/iep/list?error=invalid_iep');
            return;
        }

        $iep = $this->iep->getById((int)$iepId);
        
        if (!$iep) {
            $this->redirect('/iep/list?error=iep_not_found');
            return;
        }

        parent::view('iep/view', [
            'iep' => $iep
        ]);
    }

    // Helper methods

    /**
     * Get users available for meeting participation
     */
    private function getUsersForMeeting()
    {
        $sql = "SELECT id, fullname, email, role 
                FROM users 
                WHERE role IN ('guidance', 'principal', 'sped_teacher') 
                ORDER BY role, fullname";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Validate meeting participants include required roles
     */
    private function validateMeetingParticipants($participants)
    {
        $sql = "SELECT role FROM users WHERE id IN (" . implode(',', array_fill(0, count($participants), '?')) . ")";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($participants);
        
        $roles = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        if (!in_array('guidance', $roles) || !in_array('principal', $roles)) {
            throw new Exception("Meeting must include both Guidance and Principal participants");
        }
    }

    /**
     * Format participants for database storage
     */
    private function formatParticipants($participantIds)
    {
        $sql = "SELECT id, role FROM users WHERE id IN (" . implode(',', array_fill(0, count($participantIds), '?')) . ")";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($participantIds);
        
        $users = $stmt->fetchAll(PDO::FETCH_OBJ);
        
        $formatted = [];
        foreach ($users as $user) {
            $formatted[] = [
                'user_id' => $user->id,
                'role' => $user->role
            ];
        }
        
        return $formatted;
    }

    /**
     * Send meeting notifications to participants
     */
    private function sendMeetingNotifications($meetingId, $participantIds)
    {
        $meeting = $this->iepMeeting->getById($meetingId);
        $participants = $this->iepMeeting->getParticipants($meetingId);

        foreach ($participants as $participant) {
            $subject = "IEP Meeting Scheduled - {$meeting->first_name} {$meeting->last_name}";
            $message = "
                <h2>IEP Meeting Scheduled</h2>
                <p>Dear {$participant->fullname},</p>
                <p>You have been invited to participate in an IEP meeting for <strong>{$meeting->first_name} {$meeting->last_name}</strong>.</p>
                <p><strong>Meeting Details:</strong></p>
                <ul>
                    <li>Date & Time: " . date('F j, Y \a\t g:i A', strtotime($meeting->meeting_date)) . "</li>
                    <li>Location: {$meeting->location}</li>
                </ul>
                <p>Please confirm your attendance by logging into the system.</p>
                <p>Best regards,<br>SignED SPED Team</p>
            ";

            $this->mailer->sendNotification($participant->email, $subject, $message, true);
        }
    }

    /**
     * Update meeting status
     */
    private function updateMeetingStatus($meetingId, $status)
    {
        $sql = "UPDATE iep_meetings SET status = :status WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':status' => $status, ':id' => $meetingId]);
    }

    /**
     * Store meeting record in document store
     */
    private function storeMeetingRecord($meetingId, $notes, $signatures)
    {
        $meeting = $this->iepMeeting->getById($meetingId);
        $participants = $this->iepMeeting->getParticipants($meetingId);

        $recordData = [
            'meeting_id' => $meetingId,
            'learner' => $meeting->first_name . ' ' . $meeting->last_name,
            'date' => $meeting->meeting_date,
            'location' => $meeting->location,
            'notes' => $notes,
            'participants' => $participants,
            'signatures' => $signatures,
            'recorded_at' => date('Y-m-d H:i:s'),
            'recorded_by' => $_SESSION['user_id']
        ];

        $documentStore = $this->documentStore;
        $recordJson = json_encode($recordData, JSON_PRETTY_PRINT);
        
        // Store as encrypted document
        $filename = "meeting_record_{$meetingId}_" . date('Y-m-d_H-i-s') . ".json";
        $tempFile = sys_get_temp_dir() . '/' . $filename;
        file_put_contents($tempFile, $recordJson);
        
        $documentStore->store($tempFile, 'restricted');
        unlink($tempFile);
    }

    /**
     * Pre-populate IEP fields from assessment data
     */
    private function prepopulateIepFromAssessment($assessmentData)
    {
        if (!$assessmentData) {
            return [];
        }

        return [
            'present_level_performance' => 
                "Cognitive Ability: {$assessmentData->cognitive_ability}\n\n" .
                "Communication Skills: {$assessmentData->communication_skills}\n\n" .
                "Social-Emotional Development: {$assessmentData->social_emotional_development}\n\n" .
                "Adaptive Behavior: {$assessmentData->adaptive_behavior}\n\n" .
                "Academic Performance: {$assessmentData->academic_performance}",
            
            'annual_goals' => $assessmentData->recommendations ?? '',
            
            'start_date' => date('Y-m-d'),
            'end_date' => date('Y-m-d', strtotime('+1 year'))
        ];
    }

    /**
     * Send IEP approval notifications
     */
    private function sendIepApprovalNotifications($iepId)
    {
        $iep = $this->iep->getById($iepId);
        $learner = $this->learner->getById($iep->learner_id);

        // Get notification recipients
        $recipients = [
            ['email' => $learner->parent_email, 'name' => $learner->parent_name, 'role' => 'Parent'],
            ['email' => $iep->created_by_name, 'name' => $iep->created_by_name, 'role' => 'SPED Teacher']
        ];

        // Get guidance counselor
        $sql = "SELECT email, fullname FROM users WHERE role = 'guidance' LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $guidance = $stmt->fetch(PDO::FETCH_OBJ);
        
        if ($guidance) {
            $recipients[] = ['email' => $guidance->email, 'name' => $guidance->fullname, 'role' => 'Guidance'];
        }

        foreach ($recipients as $recipient) {
            $subject = "IEP Approved - {$iep->first_name} {$iep->last_name}";
            $message = "
                <h2>IEP Approved</h2>
                <p>Dear {$recipient['name']},</p>
                <p>The IEP for <strong>{$iep->first_name} {$iep->last_name}</strong> has been approved by the Principal.</p>
                <p>The IEP is now active and implementation can begin.</p>
                <p>Best regards,<br>SignED SPED Team</p>
            ";

            $this->mailer->sendNotification($recipient['email'], $subject, $message, true);
        }
    }

    /**
     * Send IEP rejection notifications
     */
    private function sendIepRejectionNotifications($iepId, $reason)
    {
        $iep = $this->iep->getById($iepId);

        $subject = "IEP Requires Revision - {$iep->first_name} {$iep->last_name}";
        $message = "
            <h2>IEP Requires Revision</h2>
            <p>Dear {$iep->created_by_name},</p>
            <p>The IEP for <strong>{$iep->first_name} {$iep->last_name}</strong> requires revision before approval.</p>
            <p><strong>Reason for revision:</strong><br>{$reason}</p>
            <p>Please review and resubmit the IEP after making the necessary changes.</p>
            <p>Best regards,<br>SignED SPED Team</p>
        ";

        // Get creator's email
        $sql = "SELECT email FROM users WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $iep->created_by]);
        $creator = $stmt->fetch(PDO::FETCH_OBJ);

        if ($creator) {
            $this->mailer->sendNotification($creator->email, $subject, $message, true);
        }
    }
}