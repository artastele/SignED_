<?php

class AuditLog extends Model
{
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * Log user login attempt
     * 
     * @param string $email User email
     * @param string $ipAddress IP address
     * @param bool $success Login success status
     * @param int|null $userId User ID (if login successful)
     * @return bool Log entry created
     */
    public function logLogin($email, $ipAddress, $success, $userId = null)
    {
        try {
            $sql = "INSERT INTO audit_logs (
                        user_id, action_type, entity_type, entity_id,
                        old_value, new_value, ip_address, user_agent,
                        additional_data, created_at
                    ) VALUES (?, 'login', 'user', ?, ?, ?, ?, ?, ?, NOW())";
            
            $additionalData = json_encode([
                'email' => $email,
                'success' => $success,
                'timestamp' => date('Y-m-d H:i:s')
            ]);
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $userId,
                $userId,
                null, // old_value
                $success ? 'login_success' : 'login_failed',
                $ipAddress,
                $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
                $additionalData
            ]);
            
            return true;
            
        } catch (Exception $e) {
            error_log("AuditLog::logLogin() Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Log user logout
     * 
     * @param int $userId User ID
     * @param string $ipAddress IP address
     * @return bool Log entry created
     */
    public function logLogout($userId, $ipAddress)
    {
        try {
            $sql = "INSERT INTO audit_logs (
                        user_id, action_type, entity_type, entity_id,
                        new_value, ip_address, user_agent,
                        additional_data, created_at
                    ) VALUES (?, 'logout', 'user', ?, 'logout', ?, ?, ?, NOW())";
            
            $additionalData = json_encode([
                'timestamp' => date('Y-m-d H:i:s')
            ]);
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $userId,
                $userId,
                $ipAddress,
                $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
                $additionalData
            ]);
            
            return true;
            
        } catch (Exception $e) {
            error_log("AuditLog::logLogout() Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Log document access (view, download, upload, delete)
     * 
     * @param int $userId User ID
     * @param int $documentId Document ID
     * @param string $action Action performed (view, download, upload, delete)
     * @param array $metadata Additional metadata
     * @return bool Log entry created
     */
    public function logDocumentAccess($userId, $documentId, $action, $metadata = [])
    {
        try {
            $sql = "INSERT INTO audit_logs (
                        user_id, action_type, entity_type, entity_id,
                        new_value, ip_address, user_agent,
                        additional_data, created_at
                    ) VALUES (?, 'document_access', 'document', ?, ?, ?, ?, ?, NOW())";
            
            $additionalData = json_encode(array_merge([
                'action' => $action,
                'timestamp' => date('Y-m-d H:i:s')
            ], $metadata));
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $userId,
                $documentId,
                $action,
                $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
                $additionalData
            ]);
            
            return true;
            
        } catch (Exception $e) {
            error_log("AuditLog::logDocumentAccess() Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Log status changes for entities (enrollment, assessment, IEP, etc.)
     * 
     * @param int $userId User ID making the change
     * @param string $entityType Type of entity (enrollment, assessment, iep, etc.)
     * @param int $entityId Entity ID
     * @param string $oldStatus Previous status
     * @param string $newStatus New status
     * @param string $reason Reason for change (optional)
     * @return bool Log entry created
     */
    public function logStatusChange($userId, $entityType, $entityId, $oldStatus, $newStatus, $reason = null)
    {
        try {
            $sql = "INSERT INTO audit_logs (
                        user_id, action_type, entity_type, entity_id,
                        old_value, new_value, ip_address, user_agent,
                        additional_data, created_at
                    ) VALUES (?, 'status_change', ?, ?, ?, ?, ?, ?, ?, NOW())";
            
            $additionalData = json_encode([
                'reason' => $reason,
                'timestamp' => date('Y-m-d H:i:s')
            ]);
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $userId,
                $entityType,
                $entityId,
                $oldStatus,
                $newStatus,
                $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
                $additionalData
            ]);
            
            return true;
            
        } catch (Exception $e) {
            error_log("AuditLog::logStatusChange() Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Log role changes for users
     * 
     * @param int $adminId Admin user ID making the change
     * @param int $targetUserId Target user ID
     * @param string $oldRole Previous role
     * @param string $newRole New role
     * @return bool Log entry created
     */
    public function logRoleChange($adminId, $targetUserId, $oldRole, $newRole)
    {
        try {
            $sql = "INSERT INTO audit_logs (
                        user_id, action_type, entity_type, entity_id,
                        old_value, new_value, ip_address, user_agent,
                        additional_data, created_at
                    ) VALUES (?, 'role_change', 'user', ?, ?, ?, ?, ?, ?, NOW())";
            
            $additionalData = json_encode([
                'admin_id' => $adminId,
                'target_user_id' => $targetUserId,
                'timestamp' => date('Y-m-d H:i:s')
            ]);
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $adminId,
                $targetUserId,
                $oldRole,
                $newRole,
                $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
                $additionalData
            ]);
            
            return true;
            
        } catch (Exception $e) {
            error_log("AuditLog::logRoleChange() Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Log approval/rejection actions
     * 
     * @param int $userId User ID performing the action
     * @param string $entityType Type of entity (enrollment, iep, etc.)
     * @param int $entityId Entity ID
     * @param string $action Action performed (approve, reject)
     * @param string $reason Reason for decision (optional)
     * @return bool Log entry created
     */
    public function logApprovalAction($userId, $entityType, $entityId, $action, $reason = null)
    {
        try {
            $sql = "INSERT INTO audit_logs (
                        user_id, action_type, entity_type, entity_id,
                        new_value, ip_address, user_agent,
                        additional_data, created_at
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
            
            $actionType = $action === 'approve' ? 'approval' : 'rejection';
            
            $additionalData = json_encode([
                'action' => $action,
                'reason' => $reason,
                'timestamp' => date('Y-m-d H:i:s')
            ]);
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $userId,
                $actionType,
                $entityType,
                $entityId,
                $action,
                $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
                $additionalData
            ]);
            
            return true;
            
        } catch (Exception $e) {
            error_log("AuditLog::logApprovalAction() Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Log meeting scheduling and updates
     * 
     * @param int $userId User ID scheduling the meeting
     * @param int $meetingId Meeting ID
     * @param string $action Action performed (schedule, update, cancel, complete)
     * @param array $metadata Additional meeting metadata
     * @return bool Log entry created
     */
    public function logMeetingAction($userId, $meetingId, $action, $metadata = [])
    {
        try {
            $sql = "INSERT INTO audit_logs (
                        user_id, action_type, entity_type, entity_id,
                        new_value, ip_address, user_agent,
                        additional_data, created_at
                    ) VALUES (?, 'meeting_schedule', 'iep_meeting', ?, ?, ?, ?, ?, NOW())";
            
            $additionalData = json_encode(array_merge([
                'action' => $action,
                'timestamp' => date('Y-m-d H:i:s')
            ], $metadata));
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $userId,
                $meetingId,
                $action,
                $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
                $additionalData
            ]);
            
            return true;
            
        } catch (Exception $e) {
            error_log("AuditLog::logMeetingAction() Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Log email notifications sent
     * 
     * @param int $userId User ID (sender or system)
     * @param string $recipient Recipient email address
     * @param string $subject Email subject
     * @param string $type Email type (enrollment, meeting, iep, etc.)
     * @param bool $success Email sent successfully
     * @return bool Log entry created
     */
    public function logEmailSent($userId, $recipient, $subject, $type, $success)
    {
        try {
            $sql = "INSERT INTO audit_logs (
                        user_id, action_type, entity_type, entity_id,
                        new_value, ip_address, user_agent,
                        additional_data, created_at
                    ) VALUES (?, 'email_sent', 'email', NULL, ?, ?, ?, ?, NOW())";
            
            $additionalData = json_encode([
                'recipient' => $recipient,
                'subject' => $subject,
                'type' => $type,
                'success' => $success,
                'timestamp' => date('Y-m-d H:i:s')
            ]);
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $userId,
                $success ? 'sent' : 'failed',
                $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
                $additionalData
            ]);
            
            return true;
            
        } catch (Exception $e) {
            error_log("AuditLog::logEmailSent() Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Query audit logs with filtering
     * 
     * @param array $filters Filtering criteria
     * @param int $limit Number of records to return
     * @param int $offset Offset for pagination
     * @return array Query results
     */
    public function query($filters = [], $limit = 100, $offset = 0)
    {
        try {
            $whereConditions = [];
            $params = [];
            
            // Build WHERE clause based on filters
            if (isset($filters['user_id'])) {
                $whereConditions[] = "user_id = ?";
                $params[] = $filters['user_id'];
            }
            
            if (isset($filters['action_type'])) {
                $whereConditions[] = "action_type = ?";
                $params[] = $filters['action_type'];
            }
            
            if (isset($filters['entity_type'])) {
                $whereConditions[] = "entity_type = ?";
                $params[] = $filters['entity_type'];
            }
            
            if (isset($filters['entity_id'])) {
                $whereConditions[] = "entity_id = ?";
                $params[] = $filters['entity_id'];
            }
            
            if (isset($filters['date_from'])) {
                $whereConditions[] = "created_at >= ?";
                $params[] = $filters['date_from'];
            }
            
            if (isset($filters['date_to'])) {
                $whereConditions[] = "created_at <= ?";
                $params[] = $filters['date_to'];
            }
            
            if (isset($filters['ip_address'])) {
                $whereConditions[] = "ip_address = ?";
                $params[] = $filters['ip_address'];
            }
            
            // Build the query
            $sql = "SELECT al.*, u.email, u.first_name, u.last_name 
                    FROM audit_logs al 
                    LEFT JOIN users u ON al.user_id = u.id";
            
            if (!empty($whereConditions)) {
                $sql .= " WHERE " . implode(" AND ", $whereConditions);
            }
            
            $sql .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";
            $params[] = $limit;
            $params[] = $offset;
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Get total count for pagination
            $countSql = "SELECT COUNT(*) as total FROM audit_logs al";
            if (!empty($whereConditions)) {
                $countSql .= " WHERE " . implode(" AND ", $whereConditions);
            }
            
            $countStmt = $this->db->prepare($countSql);
            $countStmt->execute(array_slice($params, 0, -2)); // Remove limit and offset
            $totalCount = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            return [
                'success' => true,
                'data' => $results,
                'total' => $totalCount,
                'limit' => $limit,
                'offset' => $offset
            ];
            
        } catch (Exception $e) {
            error_log("AuditLog::query() Error: " . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'data' => [],
                'total' => 0
            ];
        }
    }
    
    /**
     * Get audit log statistics
     * 
     * @param array $filters Optional filters
     * @return array Statistics data
     */
    public function getStatistics($filters = [])
    {
        try {
            $whereConditions = [];
            $params = [];
            
            // Apply date filters if provided
            if (isset($filters['date_from'])) {
                $whereConditions[] = "created_at >= ?";
                $params[] = $filters['date_from'];
            }
            
            if (isset($filters['date_to'])) {
                $whereConditions[] = "created_at <= ?";
                $params[] = $filters['date_to'];
            }
            
            $whereClause = !empty($whereConditions) ? " WHERE " . implode(" AND ", $whereConditions) : "";
            
            // Get action type statistics
            $sql = "SELECT action_type, COUNT(*) as count 
                    FROM audit_logs" . $whereClause . " 
                    GROUP BY action_type 
                    ORDER BY count DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $actionStats = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Get user activity statistics
            $sql = "SELECT u.email, u.first_name, u.last_name, COUNT(*) as activity_count 
                    FROM audit_logs al 
                    JOIN users u ON al.user_id = u.id" . $whereClause . " 
                    GROUP BY al.user_id 
                    ORDER BY activity_count DESC 
                    LIMIT 10";
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $userStats = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Get daily activity statistics
            $sql = "SELECT DATE(created_at) as date, COUNT(*) as count 
                    FROM audit_logs" . $whereClause . " 
                    GROUP BY DATE(created_at) 
                    ORDER BY date DESC 
                    LIMIT 30";
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $dailyStats = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return [
                'success' => true,
                'action_statistics' => $actionStats,
                'user_activity' => $userStats,
                'daily_activity' => $dailyStats
            ];
            
        } catch (Exception $e) {
            error_log("AuditLog::getStatistics() Error: " . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Log system errors with severity levels
     * 
     * @param string $errorType Type of error (file_upload, database, email, etc.)
     * @param string $severity Severity level (low, medium, high, critical)
     * @param string $message Error message
     * @param string $stackTrace Stack trace (optional)
     * @param array $requestData Request data (optional)
     * @param int|null $userId User ID (if applicable)
     * @return bool Log entry created
     */
    public function logError($errorType, $severity, $message, $stackTrace = null, $requestData = [], $userId = null)
    {
        try {
            $sql = "INSERT INTO error_logs (
                        user_id, error_type, severity, error_message,
                        stack_trace, request_data, ip_address, user_agent,
                        created_at
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $userId,
                $errorType,
                $severity,
                $message,
                $stackTrace,
                json_encode($requestData),
                $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
            ]);
            
            // Send alert for critical errors
            if ($severity === 'critical') {
                $this->sendCriticalErrorAlert($errorType, $message);
            }
            
            return true;
            
        } catch (Exception $e) {
            error_log("AuditLog::logError() Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Send critical error alert to administrators
     * 
     * @param string $errorType Error type
     * @param string $message Error message
     */
    private function sendCriticalErrorAlert($errorType, $message)
    {
        try {
            // Get admin users
            $sql = "SELECT email FROM users WHERE role = 'admin'";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $subject = "CRITICAL ERROR ALERT - SignED SPED System";
            $body = "A critical error has occurred in the SignED SPED system:\n\n";
            $body .= "Error Type: " . $errorType . "\n";
            $body .= "Message: " . $message . "\n";
            $body .= "Time: " . date('Y-m-d H:i:s') . "\n";
            $body .= "IP Address: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown') . "\n\n";
            $body .= "Please investigate immediately.";
            
            // Send email to each admin (using existing PHPMailer integration)
            foreach ($admins as $admin) {
                // This would integrate with the existing email system
                // For now, just log the alert
                error_log("CRITICAL ERROR ALERT sent to: " . $admin['email']);
            }
            
        } catch (Exception $e) {
            error_log("AuditLog::sendCriticalErrorAlert() Error: " . $e->getMessage());
        }
    }

    /**
     * Get recent activity for dashboard
     */
    public function getRecentActivity($limit = 10)
    {
        try {
            $sql = "SELECT al.*, u.fullname, u.email
                    FROM audit_logs al
                    LEFT JOIN users u ON al.user_id = u.id
                    ORDER BY al.created_at DESC
                    LIMIT :limit";

            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_OBJ);

        } catch (Exception $e) {
            error_log("AuditLog::getRecentActivity() Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Generic log method for controller use
     */
    public function log($userId, $actionType, $entityType = null, $entityId = null, $oldValue = null, $newValue = null, $ipAddress = null, $userAgent = null)
    {
        try {
            $sql = "INSERT INTO audit_logs (
                        user_id, action_type, entity_type, entity_id,
                        old_value, new_value, ip_address, user_agent,
                        created_at
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";

            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                $userId,
                $actionType,
                $entityType,
                $entityId,
                $oldValue,
                $newValue,
                $ipAddress,
                $userAgent
            ]);

        } catch (Exception $e) {
            error_log("AuditLog::log() Error: " . $e->getMessage());
            return false;
        }
    }
}