<?php

// Mailer and AuditLog will be loaded via model() method in constructor

/**
 * NotificationService for SPED Workflow Email Notifications
 * 
 * Extends existing PHPMailer integration to provide specific notification methods
 * for all SPED workflow processes including enrollment, meetings, IEP, and submissions.
 * Includes retry logic and failure handling with comprehensive audit logging.
 * 
 * Requirements: 13.1, 13.2, 13.3, 13.4, 13.5, 13.6, 13.7
 */
class NotificationService extends Model
{
    private $mailer;
    private $auditLog;
    private $maxRetries = 3;
    private $retryDelay = 5; // seconds
    
    public function __construct()
    {
        parent::__construct();
        // Load Mailer helper
        require_once __DIR__ . '/../helpers/Mailer.php';
        $this->mailer = new Mailer();
        // Load AuditLog model
        require_once __DIR__ . '/AuditLog.php';
        $this->auditLog = new AuditLog();
    }
    
    /**
     * Send enrollment approval notification to parent
     * Requirement 13.1: Send enrollment approval/rejection notifications to parents
     * 
     * @param string $parentEmail Parent's email address
     * @param string $parentName Parent's name
     * @param string $learnerName Learner's name
     * @param string $status Approval status ('approved' or 'rejected')
     * @param string|null $reason Rejection reason (if applicable)
     * @param int|null $userId User ID for audit logging
     * @return bool Success status
     */
    public function sendEnrollmentNotification($parentEmail, $parentName, $learnerName, $status, $reason = null, $userId = null)
    {
        $subject = $status === 'approved' 
            ? "SPED Enrollment Approved - {$learnerName}"
            : "SPED Enrollment Status Update - {$learnerName}";
        
        if ($status === 'approved') {
            $message = $this->buildEnrollmentApprovalMessage($parentName, $learnerName);
        } else {
            $message = $this->buildEnrollmentRejectionMessage($parentName, $learnerName, $reason);
        }
        
        $details = [
            'learner_name' => $learnerName,
            'action_type' => 'enrollment_' . $status,
            'system_link' => $this->buildSystemLink('/enrollment/status')
        ];
        
        return $this->sendNotificationWithRetry(
            $parentEmail,
            $subject,
            $message,
            'enrollment',
            $details,
            $userId
        );
    }
    
    /**
     * Send IEP meeting notification to participants
     * Requirement 13.2: Send IEP meeting notifications to participants
     * 
     * @param array $participants Array of participant data (email, name, role)
     * @param string $learnerName Learner's name
     * @param string $meetingDate Meeting date and time
     * @param string $location Meeting location
     * @param int $meetingId Meeting ID for system link
     * @param int|null $userId User ID for audit logging
     * @return bool Success status
     */
    public function sendMeetingNotification($participants, $learnerName, $meetingDate, $location, $meetingId, $userId = null)
    {
        $subject = "IEP Meeting Scheduled - {$learnerName}";
        $allSuccess = true;
        
        foreach ($participants as $participant) {
            $message = $this->buildMeetingNotificationMessage(
                $participant['name'],
                $learnerName,
                $meetingDate,
                $location,
                $meetingId
            );
            
            $details = [
                'learner_name' => $learnerName,
                'action_type' => 'meeting_scheduled',
                'meeting_date' => $meetingDate,
                'location' => $location,
                'participant_role' => $participant['role'],
                'system_link' => $this->buildSystemLink("/iep/confirm_attendance/{$meetingId}")
            ];
            
            $success = $this->sendNotificationWithRetry(
                $participant['email'],
                $subject,
                $message,
                'meeting',
                $details,
                $userId
            );
            
            if (!$success) {
                $allSuccess = false;
            }
        }
        
        return $allSuccess;
    }
    
    /**
     * Send IEP approval notification to stakeholders
     * Requirement 13.3: Send IEP approval notifications to stakeholders
     * 
     * @param array $recipients Array of recipient data (email, name, role)
     * @param string $learnerName Learner's name
     * @param string $status IEP status ('approved' or 'rejected')
     * @param string|null $reason Rejection reason (if applicable)
     * @param int $iepId IEP ID for system link
     * @param int|null $userId User ID for audit logging
     * @return bool Success status
     */
    public function sendIepApprovalNotification($recipients, $learnerName, $status, $reason = null, $iepId, $userId = null)
    {
        $subject = $status === 'approved' 
            ? "IEP Approved - {$learnerName}"
            : "IEP Requires Revision - {$learnerName}";
        
        $allSuccess = true;
        
        foreach ($recipients as $recipient) {
            if ($status === 'approved') {
                $message = $this->buildIepApprovalMessage($recipient['name'], $learnerName, $iepId);
            } else {
                $message = $this->buildIepRejectionMessage($recipient['name'], $learnerName, $reason, $iepId);
            }
            
            $details = [
                'learner_name' => $learnerName,
                'action_type' => 'iep_' . $status,
                'recipient_role' => $recipient['role'],
                'system_link' => $this->buildSystemLink("/iep/view/{$iepId}")
            ];
            
            if ($reason) {
                $details['reason'] = $reason;
            }
            
            $success = $this->sendNotificationWithRetry(
                $recipient['email'],
                $subject,
                $message,
                'iep',
                $details,
                $userId
            );
            
            if (!$success) {
                $allSuccess = false;
            }
        }
        
        return $allSuccess;
    }
    
    /**
     * Send learner submission notification to teacher
     * Requirement 13.4: Send learner submission notifications to teachers
     * 
     * @param string $teacherEmail Teacher's email address
     * @param string $teacherName Teacher's name
     * @param string $learnerName Learner's name
     * @param string $materialTitle Learning material title
     * @param string $submissionDate Submission date
     * @param int $submissionId Submission ID for system link
     * @param int|null $userId User ID for audit logging
     * @return bool Success status
     */
    public function sendSubmissionNotification($teacherEmail, $teacherName, $learnerName, $materialTitle, $submissionDate, $submissionId, $userId = null)
    {
        $subject = "New Learner Submission - {$learnerName}";
        
        $message = $this->buildSubmissionNotificationMessage(
            $teacherName,
            $learnerName,
            $materialTitle,
            $submissionDate,
            $submissionId
        );
        
        $details = [
            'learner_name' => $learnerName,
            'action_type' => 'learner_submission',
            'material_title' => $materialTitle,
            'submission_date' => $submissionDate,
            'system_link' => $this->buildSystemLink("/materials/submissions/{$submissionId}")
        ];
        
        return $this->sendNotificationWithRetry(
            $teacherEmail,
            $subject,
            $message,
            'submission',
            $details,
            $userId
        );
    }
    
    /**
     * Send meeting reminder notification
     * 
     * @param array $participants Array of participant data
     * @param string $learnerName Learner's name
     * @param string $meetingDate Meeting date and time
     * @param string $location Meeting location
     * @param int $meetingId Meeting ID
     * @param int|null $userId User ID for audit logging
     * @return bool Success status
     */
    public function sendMeetingReminder($participants, $learnerName, $meetingDate, $location, $meetingId, $userId = null)
    {
        $subject = "IEP Meeting Reminder - {$learnerName}";
        $allSuccess = true;
        
        foreach ($participants as $participant) {
            $message = $this->buildMeetingReminderMessage(
                $participant['name'],
                $learnerName,
                $meetingDate,
                $location,
                $meetingId
            );
            
            $details = [
                'learner_name' => $learnerName,
                'action_type' => 'meeting_reminder',
                'meeting_date' => $meetingDate,
                'location' => $location,
                'participant_role' => $participant['role'],
                'system_link' => $this->buildSystemLink("/iep/confirm_attendance/{$meetingId}")
            ];
            
            $success = $this->sendNotificationWithRetry(
                $participant['email'],
                $subject,
                $message,
                'meeting_reminder',
                $details,
                $userId
            );
            
            if (!$success) {
                $allSuccess = false;
            }
        }
        
        return $allSuccess;
    }
    
    /**
     * Send assessment completion notification
     * 
     * @param string $parentEmail Parent's email address
     * @param string $parentName Parent's name
     * @param string $learnerName Learner's name
     * @param string $assessmentDate Assessment completion date
     * @param int $assessmentId Assessment ID
     * @param int|null $userId User ID for audit logging
     * @return bool Success status
     */
    public function sendAssessmentCompletionNotification($parentEmail, $parentName, $learnerName, $assessmentDate, $assessmentId, $userId = null)
    {
        $subject = "Assessment Completed - {$learnerName}";
        
        $message = $this->buildAssessmentCompletionMessage(
            $parentName,
            $learnerName,
            $assessmentDate,
            $assessmentId
        );
        
        $details = [
            'learner_name' => $learnerName,
            'action_type' => 'assessment_completed',
            'assessment_date' => $assessmentDate,
            'system_link' => $this->buildSystemLink("/assessment/viewAssessment/{$assessmentId}")
        ];
        
        return $this->sendNotificationWithRetry(
            $parentEmail,
            $subject,
            $message,
            'assessment',
            $details,
            $userId
        );
    }
    
    /**
     * Send notification with retry logic and failure handling
     * Requirement 13.6: Implement retry logic for failed email delivery
     * Requirement 13.7: Log email failures in audit system
     * 
     * @param string $email Recipient email
     * @param string $subject Email subject
     * @param string $message Email message
     * @param string $type Notification type
     * @param array $details Additional details for audit logging
     * @param int|null $userId User ID for audit logging
     * @return bool Success status
     */
    private function sendNotificationWithRetry($email, $subject, $message, $type, $details = [], $userId = null)
    {
        $attempt = 0;
        $lastError = null;
        
        while ($attempt < $this->maxRetries) {
            $attempt++;
            
            try {
                // Attempt to send email using existing PHPMailer integration
                $success = $this->mailer->sendNotification($email, $subject, $message, true);
                
                if ($success) {
                    // Log successful email send
                    $this->auditLog->logEmailSent($userId, $email, $subject, $type, true);
                    
                    // Log additional details if provided
                    if (!empty($details)) {
                        $this->auditLog->log(
                            $userId,
                            'email_sent',
                            'notification',
                            null,
                            null,
                            json_encode($details),
                            $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                            $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
                        );
                    }
                    
                    return true;
                }
                
                $lastError = "Email send failed (attempt {$attempt})";
                
            } catch (Exception $e) {
                $lastError = $e->getMessage();
                error_log("NotificationService::sendNotificationWithRetry() Attempt {$attempt} Error: " . $lastError);
            }
            
            // Wait before retry (except on last attempt)
            if ($attempt < $this->maxRetries) {
                sleep($this->retryDelay);
            }
        }
        
        // All attempts failed - log failure
        $this->auditLog->logEmailSent($userId, $email, $subject, $type, false);
        
        // Log error details
        $errorDetails = array_merge($details, [
            'attempts' => $attempt,
            'last_error' => $lastError,
            'recipient' => $email,
            'subject' => $subject
        ]);
        
        $this->auditLog->logError(
            'email',
            'medium',
            "Failed to send {$type} notification after {$this->maxRetries} attempts: {$lastError}",
            null,
            $errorDetails,
            $userId
        );
        
        return false;
    }
    
    /**
     * Build enrollment approval message
     * Requirement 13.5: Include relevant details and system links in notifications
     */
    private function buildEnrollmentApprovalMessage($parentName, $learnerName)
    {
        return "
            <h2>Enrollment Approved</h2>
            <p>Dear {$parentName},</p>
            <p>We are pleased to inform you that the SPED enrollment for <strong>{$learnerName}</strong> has been approved.</p>
            <p>Your child has been successfully enrolled in our Special Education program. You will be contacted soon regarding the next steps in the assessment process.</p>
            <p><strong>Next Steps:</strong></p>
            <ul>
                <li>Initial assessment will be scheduled within 2 weeks</li>
                <li>You will receive notification of the assessment date</li>
                <li>Please ensure all contact information is current</li>
            </ul>
            <p>You can check your enrollment status at any time by logging into the system: <a href='" . $this->buildSystemLink('/enrollment/status') . "'>View Enrollment Status</a></p>
            <p>Thank you for choosing our SPED program.</p>
            <p>Best regards,<br>SignED SPED Team</p>
        ";
    }
    
    /**
     * Build enrollment rejection message
     */
    private function buildEnrollmentRejectionMessage($parentName, $learnerName, $reason)
    {
        return "
            <h2>Enrollment Status Update</h2>
            <p>Dear {$parentName},</p>
            <p>We regret to inform you that the SPED enrollment for <strong>{$learnerName}</strong> requires additional documentation or clarification.</p>
            <p><strong>Reason for status update:</strong><br>{$reason}</p>
            <p><strong>What you can do:</strong></p>
            <ul>
                <li>Review the feedback provided above</li>
                <li>Gather any additional required documentation</li>
                <li>Contact our SPED office for clarification if needed</li>
                <li>Resubmit your application when ready</li>
            </ul>
            <p>You can resubmit documents by logging into the system: <a href='" . $this->buildSystemLink('/enrollment/submit') . "'>Resubmit Documents</a></p>
            <p>If you have any questions, please contact our SPED office at your earliest convenience.</p>
            <p>Best regards,<br>SignED SPED Team</p>
        ";
    }
    
    /**
     * Build meeting notification message
     */
    private function buildMeetingNotificationMessage($participantName, $learnerName, $meetingDate, $location, $meetingId)
    {
        $formattedDate = date('F j, Y \a\t g:i A', strtotime($meetingDate));
        
        return "
            <h2>IEP Meeting Scheduled</h2>
            <p>Dear {$participantName},</p>
            <p>You have been invited to participate in an IEP meeting for <strong>{$learnerName}</strong>.</p>
            <p><strong>Meeting Details:</strong></p>
            <ul>
                <li><strong>Date & Time:</strong> {$formattedDate}</li>
                <li><strong>Location:</strong> {$location}</li>
                <li><strong>Purpose:</strong> Develop Individualized Education Plan</li>
            </ul>
            <p><strong>Important:</strong> Please confirm your attendance by clicking the link below:</p>
            <p><a href='" . $this->buildSystemLink("/iep/confirm_attendance/{$meetingId}") . "' style='background-color: #1E40AF; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Confirm Attendance</a></p>
            <p>Your participation is essential for developing an effective education plan for {$learnerName}. If you cannot attend, please contact us immediately to reschedule.</p>
            <p>Best regards,<br>SignED SPED Team</p>
        ";
    }
    
    /**
     * Build meeting reminder message
     */
    private function buildMeetingReminderMessage($participantName, $learnerName, $meetingDate, $location, $meetingId)
    {
        $formattedDate = date('F j, Y \a\t g:i A', strtotime($meetingDate));
        $timeUntil = $this->getTimeUntilMeeting($meetingDate);
        
        return "
            <h2>IEP Meeting Reminder</h2>
            <p>Dear {$participantName},</p>
            <p>This is a reminder that you have an IEP meeting for <strong>{$learnerName}</strong> {$timeUntil}.</p>
            <p><strong>Meeting Details:</strong></p>
            <ul>
                <li><strong>Date & Time:</strong> {$formattedDate}</li>
                <li><strong>Location:</strong> {$location}</li>
            </ul>
            <p>Please ensure you have:</p>
            <ul>
                <li>Reviewed any pre-meeting materials</li>
                <li>Prepared any questions or concerns</li>
                <li>Confirmed your attendance</li>
            </ul>
            <p>If you need to make any last-minute changes, please contact us immediately.</p>
            <p><a href='" . $this->buildSystemLink("/iep/meetings") . "'>View Meeting Details</a></p>
            <p>Best regards,<br>SignED SPED Team</p>
        ";
    }
    
    /**
     * Build IEP approval message
     */
    private function buildIepApprovalMessage($recipientName, $learnerName, $iepId)
    {
        return "
            <h2>IEP Approved</h2>
            <p>Dear {$recipientName},</p>
            <p>The IEP for <strong>{$learnerName}</strong> has been approved by the Principal and is now active.</p>
            <p><strong>Next Steps:</strong></p>
            <ul>
                <li>IEP implementation will begin immediately</li>
                <li>Learning materials will be prepared and assigned</li>
                <li>Progress tracking will commence</li>
                <li>Regular progress reports will be provided</li>
            </ul>
            <p>You can view the approved IEP at any time: <a href='" . $this->buildSystemLink("/iep/view/{$iepId}") . "'>View IEP Document</a></p>
            <p>Thank you for your collaboration in developing this education plan.</p>
            <p>Best regards,<br>SignED SPED Team</p>
        ";
    }
    
    /**
     * Build IEP rejection message
     */
    private function buildIepRejectionMessage($recipientName, $learnerName, $reason, $iepId)
    {
        return "
            <h2>IEP Requires Revision</h2>
            <p>Dear {$recipientName},</p>
            <p>The IEP for <strong>{$learnerName}</strong> requires revision before approval.</p>
            <p><strong>Feedback from Principal:</strong><br>{$reason}</p>
            <p><strong>Next Steps:</strong></p>
            <ul>
                <li>Review the feedback provided above</li>
                <li>Make necessary revisions to the IEP</li>
                <li>Resubmit for approval when ready</li>
                <li>Contact the Principal if clarification is needed</li>
            </ul>
            <p>You can access and edit the IEP here: <a href='" . $this->buildSystemLink("/iep/view/{$iepId}") . "'>Edit IEP Document</a></p>
            <p>Please address the feedback and resubmit at your earliest convenience.</p>
            <p>Best regards,<br>SignED SPED Team</p>
        ";
    }
    
    /**
     * Build submission notification message
     */
    private function buildSubmissionNotificationMessage($teacherName, $learnerName, $materialTitle, $submissionDate, $submissionId)
    {
        $formattedDate = date('F j, Y \a\t g:i A', strtotime($submissionDate));
        
        return "
            <h2>New Learner Submission</h2>
            <p>Dear {$teacherName},</p>
            <p><strong>{$learnerName}</strong> has submitted completed work for your review.</p>
            <p><strong>Submission Details:</strong></p>
            <ul>
                <li><strong>Learning Material:</strong> {$materialTitle}</li>
                <li><strong>Submitted:</strong> {$formattedDate}</li>
                <li><strong>Status:</strong> Awaiting Review</li>
            </ul>
            <p>Please review the submission and provide feedback to help guide the learner's progress.</p>
            <p><a href='" . $this->buildSystemLink("/materials/submissions/{$submissionId}") . "' style='background-color: #B91C3C; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Review Submission</a></p>
            <p>Timely feedback helps maintain learner engagement and progress toward IEP goals.</p>
            <p>Best regards,<br>SignED SPED Team</p>
        ";
    }
    
    /**
     * Build assessment completion message
     */
    private function buildAssessmentCompletionMessage($parentName, $learnerName, $assessmentDate, $assessmentId)
    {
        $formattedDate = date('F j, Y', strtotime($assessmentDate));
        
        return "
            <h2>Assessment Completed</h2>
            <p>Dear {$parentName},</p>
            <p>The initial assessment for <strong>{$learnerName}</strong> has been completed on {$formattedDate}.</p>
            <p><strong>Next Steps:</strong></p>
            <ul>
                <li>Assessment results will be reviewed by our team</li>
                <li>An IEP meeting will be scheduled within 2 weeks</li>
                <li>You will receive meeting notification with details</li>
                <li>The assessment will inform the IEP development process</li>
            </ul>
            <p>You can view the assessment summary: <a href='" . $this->buildSystemLink("/assessment/viewAssessment/{$assessmentId}") . "'>View Assessment</a></p>
            <p>Thank you for your patience during the assessment process.</p>
            <p>Best regards,<br>SignED SPED Team</p>
        ";
    }
    
    /**
     * Build system link with base URL
     */
    private function buildSystemLink($path)
    {
        $baseUrl = defined('URLROOT') ? URLROOT : 'http://localhost/signed';
        return rtrim($baseUrl, '/') . $path;
    }
    
    /**
     * Calculate time until meeting for reminder messages
     */
    private function getTimeUntilMeeting($meetingDate)
    {
        $now = new DateTime();
        $meeting = new DateTime($meetingDate);
        $diff = $now->diff($meeting);
        
        if ($diff->days > 0) {
            return "in {$diff->days} day(s)";
        } elseif ($diff->h > 0) {
            return "in {$diff->h} hour(s)";
        } else {
            return "soon";
        }
    }
    
    /**
     * Send bulk notifications to multiple recipients
     * 
     * @param array $recipients Array of recipient data
     * @param string $subject Email subject
     * @param string $messageTemplate Message template with placeholders
     * @param string $type Notification type
     * @param array $commonDetails Common details for all recipients
     * @param int|null $userId User ID for audit logging
     * @return array Results array with success/failure counts
     */
    public function sendBulkNotifications($recipients, $subject, $messageTemplate, $type, $commonDetails = [], $userId = null)
    {
        $results = [
            'total' => count($recipients),
            'successful' => 0,
            'failed' => 0,
            'failures' => []
        ];
        
        foreach ($recipients as $recipient) {
            // Replace placeholders in message template
            $message = $messageTemplate;
            foreach ($recipient as $key => $value) {
                $message = str_replace("{{$key}}", $value, $message);
            }
            
            $details = array_merge($commonDetails, [
                'recipient_role' => $recipient['role'] ?? 'unknown',
                'recipient_name' => $recipient['name'] ?? 'unknown'
            ]);
            
            $success = $this->sendNotificationWithRetry(
                $recipient['email'],
                $subject,
                $message,
                $type,
                $details,
                $userId
            );
            
            if ($success) {
                $results['successful']++;
            } else {
                $results['failed']++;
                $results['failures'][] = [
                    'email' => $recipient['email'],
                    'name' => $recipient['name'] ?? 'unknown'
                ];
            }
        }
        
        return $results;
    }
    
    /**
     * Get notification statistics for dashboard
     * 
     * @param array $filters Optional filters (date_from, date_to, type)
     * @return array Statistics data
     */
    public function getNotificationStatistics($filters = [])
    {
        try {
            $whereConditions = ["action_type = 'email_sent'"];
            $params = [];
            
            if (isset($filters['date_from'])) {
                $whereConditions[] = "created_at >= ?";
                $params[] = $filters['date_from'];
            }
            
            if (isset($filters['date_to'])) {
                $whereConditions[] = "created_at <= ?";
                $params[] = $filters['date_to'];
            }
            
            if (isset($filters['type'])) {
                $whereConditions[] = "JSON_EXTRACT(additional_data, '$.type') = ?";
                $params[] = $filters['type'];
            }
            
            $whereClause = " WHERE " . implode(" AND ", $whereConditions);
            
            // Get success/failure counts
            $sql = "SELECT 
                        new_value as status,
                        COUNT(*) as count
                    FROM audit_logs" . $whereClause . "
                    GROUP BY new_value";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $statusCounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Get type breakdown
            $sql = "SELECT 
                        JSON_EXTRACT(additional_data, '$.type') as type,
                        COUNT(*) as count
                    FROM audit_logs" . $whereClause . "
                    GROUP BY JSON_EXTRACT(additional_data, '$.type')";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $typeCounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return [
                'success' => true,
                'status_breakdown' => $statusCounts,
                'type_breakdown' => $typeCounts
            ];
            
        } catch (Exception $e) {
            error_log("NotificationService::getNotificationStatistics() Error: " . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Get unread notification count for a user
     * 
     * @param int $userId User ID
     * @return int Unread notification count
     */
    public function getUnreadCount($userId)
    {
        try {
            $sql = "SELECT COUNT(*) as count 
                    FROM notifications 
                    WHERE user_id = ? AND is_read = 0";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$userId]);
            $result = $stmt->fetch(PDO::FETCH_OBJ);
            
            return $result ? (int)$result->count : 0;
            
        } catch (Exception $e) {
            error_log("NotificationService::getUnreadCount() Error: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Get user notifications
     * 
     * @param int $userId User ID
     * @param int $limit Limit number of notifications
     * @param bool $unreadOnly Get only unread notifications
     * @return array Notifications
     */
    public function getUserNotifications($userId, $limit = 10, $unreadOnly = false)
    {
        try {
            $sql = "SELECT * FROM notifications 
                    WHERE user_id = ?";
            
            if ($unreadOnly) {
                $sql .= " AND is_read = 0";
            }
            
            $sql .= " ORDER BY created_at DESC LIMIT ?";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$userId, $limit]);
            
            return $stmt->fetchAll(PDO::FETCH_OBJ);
            
        } catch (Exception $e) {
            error_log("NotificationService::getUserNotifications() Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Mark notification as read
     * 
     * @param int $notificationId Notification ID
     * @return bool Success status
     */
    public function markAsRead($notificationId)
    {
        try {
            $sql = "UPDATE notifications SET is_read = 1 WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$notificationId]);
            
        } catch (Exception $e) {
            error_log("NotificationService::markAsRead() Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Mark all user notifications as read
     * 
     * @param int $userId User ID
     * @return bool Success status
     */
    public function markAllAsRead($userId)
    {
        try {
            $sql = "UPDATE notifications SET is_read = 1 WHERE user_id = ? AND is_read = 0";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$userId]);
            
        } catch (Exception $e) {
            error_log("NotificationService::markAllAsRead() Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Create a notification for a user
     * 
     * @param int $userId User ID
     * @param string $title Notification title
     * @param string $message Notification message
     * @param string $type Notification type (info, success, warning, error)
     * @param string|null $link Optional link
     * @return bool Success status
     */
    public function createNotification($userId, $title, $message, $type = 'info', $link = null)
    {
        try {
            $sql = "INSERT INTO notifications (user_id, title, message, type, link) 
                    VALUES (?, ?, ?, ?, ?)";
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$userId, $title, $message, $type, $link]);
            
        } catch (Exception $e) {
            error_log("NotificationService::createNotification() Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Create notification (alias for createNotification)
     * Accepts array parameter for easier use
     * 
     * @param array $data Notification data (user_id, title, message, type, link, priority)
     * @return bool Success status
     */
    public function create($data)
    {
        return $this->createNotification(
            $data['user_id'],
            $data['title'],
            $data['message'],
            $data['type'] ?? 'info',
            $data['link'] ?? null
        );
    }
}