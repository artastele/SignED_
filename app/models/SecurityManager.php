<?php

class SecurityManager extends Model
{
    private $sessionTimeout = 900; // 15 minutes in seconds
    
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * Classify document based on type and content
     * 
     * @param string $documentType Type of document
     * @param array $metadata Additional document metadata
     * @return string Classification level (public, internal, confidential, restricted)
     */
    public function classifyDocument($documentType, $metadata = [])
    {
        switch ($documentType) {
            case 'enrollment':
                // Enrollment documents contain personal information
                return 'confidential';
                
            case 'assessment':
                // Assessment records contain sensitive educational data
                return 'restricted';
                
            case 'iep':
                // IEP documents contain highly sensitive educational plans
                return 'restricted';
                
            case 'learning_material':
                // Learning materials are generally internal use
                return 'internal';
                
            case 'submission':
                // Student submissions contain personal work
                return 'confidential';
                
            case 'meeting':
                // Meeting records contain sensitive discussions
                return 'restricted';
                
            default:
                // Default to internal classification
                return 'internal';
        }
    }
    
    /**
     * Enforce access control for document operations
     * 
     * @param int $documentId Document ID
     * @param int $userId User ID requesting access
     * @param string $action Action being performed (read, write, delete, download)
     * @return array Access control result
     */
    public function enforceAccess($documentId, $userId, $action)
    {
        try {
            // Get document and user information
            $sql = "SELECT ds.*, u.role, u.email 
                    FROM document_store ds 
                    JOIN users u ON u.id = ? 
                    WHERE ds.id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$userId, $documentId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$result) {
                return [
                    'allowed' => false,
                    'reason' => 'Document or user not found'
                ];
            }
            
            $document = $result;
            $userRole = $result['role'];
            $classification = $document['classification'];
            
            // Check session timeout
            if (!$this->checkSessionTimeout($userId)) {
                return [
                    'allowed' => false,
                    'reason' => 'Session expired',
                    'action_required' => 'reauth'
                ];
            }
            
            // Admin has access to all documents
            if ($userRole === 'admin') {
                $this->logAccessAttempt($userId, $documentId, $action, true, 'Admin access');
                return [
                    'allowed' => true,
                    'classification' => $classification,
                    'restrictions' => $this->getRestrictions($classification, $action)
                ];
            }
            
            // Check role-based access
            $accessAllowed = $this->checkRoleBasedAccess($userRole, $document, $action);
            
            if (!$accessAllowed) {
                $this->logAccessAttempt($userId, $documentId, $action, false, 'Insufficient permissions');
                return [
                    'allowed' => false,
                    'reason' => 'Insufficient permissions for this document type'
                ];
            }
            
            // Check classification-based restrictions
            $classificationAllowed = $this->checkClassificationAccess($userRole, $classification, $action);
            
            if (!$classificationAllowed) {
                $this->logAccessAttempt($userId, $documentId, $action, false, 'Classification restriction');
                return [
                    'allowed' => false,
                    'reason' => 'Access denied due to document classification'
                ];
            }
            
            $this->logAccessAttempt($userId, $documentId, $action, true, 'Access granted');
            
            return [
                'allowed' => true,
                'classification' => $classification,
                'restrictions' => $this->getRestrictions($classification, $action)
            ];
            
        } catch (Exception $e) {
            error_log("SecurityManager::enforceAccess() Error: " . $e->getMessage());
            return [
                'allowed' => false,
                'reason' => 'Security check failed'
            ];
        }
    }
    
    /**
     * Check session timeout and enforce timeout policy
     * 
     * @param int $userId User ID to check
     * @return bool Session is valid
     */
    public function checkSessionTimeout($userId = null)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            return false;
        }
        
        // If userId provided, verify it matches session
        if ($userId && $_SESSION['user_id'] != $userId) {
            return false;
        }
        
        // Check last activity timestamp
        if (isset($_SESSION['last_activity'])) {
            $timeSinceLastActivity = time() - $_SESSION['last_activity'];
            
            if ($timeSinceLastActivity > $this->sessionTimeout) {
                // Session expired
                $this->logSessionEvent($_SESSION['user_id'], 'timeout');
                session_destroy();
                return false;
            }
        }
        
        // Update last activity timestamp
        $_SESSION['last_activity'] = time();
        
        return true;
    }
    
    /**
     * Get DLP restrictions for document classification and action
     * 
     * @param string $classification Document classification
     * @param string $action Action being performed
     * @return array DLP restrictions
     */
    public function getRestrictions($classification, $action)
    {
        $restrictions = [
            'watermark_required' => false,
            'download_allowed' => true,
            'print_allowed' => true,
            'screenshot_blocked' => false,
            'copy_blocked' => false,
            'browser_restrictions' => []
        ];
        
        switch ($classification) {
            case 'restricted':
                $restrictions['watermark_required'] = true;
                $restrictions['screenshot_blocked'] = true;
                $restrictions['copy_blocked'] = true;
                $restrictions['browser_restrictions'] = [
                    'disable_right_click' => true,
                    'disable_text_selection' => true,
                    'disable_developer_tools' => true
                ];
                break;
                
            case 'confidential':
                $restrictions['watermark_required'] = true;
                $restrictions['browser_restrictions'] = [
                    'disable_right_click' => true
                ];
                break;
                
            case 'internal':
                // No additional restrictions for internal documents
                break;
                
            case 'public':
                // No restrictions for public documents
                break;
        }
        
        // Additional restrictions for download action
        if ($action === 'download' && in_array($classification, ['restricted', 'confidential'])) {
            $restrictions['audit_download'] = true;
            $restrictions['email_notification'] = true;
        }
        
        return $restrictions;
    }
    
    /**
     * Check role-based access to document
     * 
     * @param string $userRole User's role
     * @param array $document Document information
     * @param string $action Action being performed
     * @return bool Access allowed
     */
    private function checkRoleBasedAccess($userRole, $document, $action)
    {
        $documentType = $document['document_type'];
        $documentUserId = $document['user_id'];
        
        switch ($userRole) {
            case 'parent':
                // Parents can access their own enrollment documents
                return $documentType === 'enrollment' && $documentUserId == $_SESSION['user_id'];
                
            case 'learner':
                // Learners can access their learning materials and submit work
                return in_array($documentType, ['learning_material', 'submission']) && 
                       $this->isLearnerDocument($documentUserId, $_SESSION['user_id']);
                
            case 'sped_teacher':
                // SPED teachers can access most document types
                return in_array($documentType, ['enrollment', 'assessment', 'iep', 'learning_material', 'submission', 'meeting']);
                
            case 'guidance':
                // Guidance can access assessment, IEP, and meeting documents
                return in_array($documentType, ['assessment', 'iep', 'meeting']);
                
            case 'principal':
                // Principals can access IEP and meeting documents
                return in_array($documentType, ['iep', 'meeting']);
                
            default:
                return false;
        }
    }
    
    /**
     * Check classification-based access restrictions
     * 
     * @param string $userRole User's role
     * @param string $classification Document classification
     * @param string $action Action being performed
     * @return bool Access allowed
     */
    private function checkClassificationAccess($userRole, $classification, $action)
    {
        // Define role clearance levels
        $roleClearance = [
            'admin' => ['public', 'internal', 'confidential', 'restricted'],
            'principal' => ['public', 'internal', 'confidential', 'restricted'],
            'sped_teacher' => ['public', 'internal', 'confidential', 'restricted'],
            'guidance' => ['public', 'internal', 'confidential'],
            'parent' => ['public', 'internal', 'confidential'],
            'learner' => ['public', 'internal']
        ];
        
        if (!isset($roleClearance[$userRole])) {
            return false;
        }
        
        return in_array($classification, $roleClearance[$userRole]);
    }
    
    /**
     * Check if document belongs to a specific learner
     * 
     * @param int $documentUserId Document owner user ID
     * @param int $learnerUserId Learner user ID
     * @return bool Document belongs to learner
     */
    private function isLearnerDocument($documentUserId, $learnerUserId)
    {
        // Check if the learner is associated with the document
        $sql = "SELECT COUNT(*) as count 
                FROM learners l 
                WHERE l.user_id = ? AND 
                      (l.user_id = ? OR l.parent_id = ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$learnerUserId, $documentUserId, $documentUserId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['count'] > 0;
    }
    
    /**
     * Log access attempt for audit purposes
     * 
     * @param int $userId User ID
     * @param int $documentId Document ID
     * @param string $action Action attempted
     * @param bool $success Whether access was granted
     * @param string $reason Reason for decision
     */
    private function logAccessAttempt($userId, $documentId, $action, $success, $reason)
    {
        try {
            $sql = "INSERT INTO audit_logs (
                        user_id, action_type, entity_type, entity_id,
                        additional_data, created_at
                    ) VALUES (?, 'document_access', 'document', ?, ?, NOW())";
            
            $additionalData = json_encode([
                'action' => $action,
                'success' => $success,
                'reason' => $reason,
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
            ]);
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$userId, $documentId, $additionalData]);
            
        } catch (Exception $e) {
            error_log("SecurityManager::logAccessAttempt() Error: " . $e->getMessage());
        }
    }
    
    /**
     * Log session events
     * 
     * @param int $userId User ID
     * @param string $event Event type (timeout, login, logout)
     */
    private function logSessionEvent($userId, $event)
    {
        try {
            $sql = "INSERT INTO audit_logs (
                        user_id, action_type, entity_type, entity_id,
                        additional_data, created_at
                    ) VALUES (?, ?, 'session', ?, ?, NOW())";
            
            $actionType = $event === 'timeout' ? 'logout' : $event;
            
            $additionalData = json_encode([
                'event' => $event,
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
            ]);
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$userId, $actionType, $userId, $additionalData]);
            
        } catch (Exception $e) {
            error_log("SecurityManager::logSessionEvent() Error: " . $e->getMessage());
        }
    }
    
    /**
     * Generate security headers for document viewing
     * 
     * @param string $classification Document classification
     * @return array HTTP headers to set
     */
    public function getSecurityHeaders($classification)
    {
        $headers = [
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'DENY',
            'X-XSS-Protection' => '1; mode=block'
        ];
        
        if (in_array($classification, ['restricted', 'confidential'])) {
            $headers['Cache-Control'] = 'no-cache, no-store, must-revalidate';
            $headers['Pragma'] = 'no-cache';
            $headers['Expires'] = '0';
        }
        
        return $headers;
    }
    
    /**
     * Check for suspicious activity patterns
     * 
     * @param int $userId User ID to check
     * @param string $action Action being performed
     * @return array Suspicious activity result
     */
    public function checkSuspiciousActivity($userId, $action)
    {
        try {
            // Check for rapid successive access attempts
            $sql = "SELECT COUNT(*) as count 
                    FROM audit_logs 
                    WHERE user_id = ? 
                      AND action_type = 'document_access' 
                      AND created_at > DATE_SUB(NOW(), INTERVAL 1 MINUTE)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$userId]);
            $recentAccess = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($recentAccess['count'] > 10) {
                return [
                    'suspicious' => true,
                    'reason' => 'Rapid access attempts',
                    'action' => 'rate_limit'
                ];
            }
            
            // Check for access outside normal hours (if configured)
            $currentHour = (int)date('H');
            if ($currentHour < 6 || $currentHour > 22) {
                return [
                    'suspicious' => true,
                    'reason' => 'Access outside normal hours',
                    'action' => 'alert_admin'
                ];
            }
            
            return [
                'suspicious' => false
            ];
            
        } catch (Exception $e) {
            error_log("SecurityManager::checkSuspiciousActivity() Error: " . $e->getMessage());
            return [
                'suspicious' => false
            ];
        }
    }
}