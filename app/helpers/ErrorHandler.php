<?php

/**
 * Comprehensive Error Handling and Logging System
 * 
 * This class provides centralized error handling, user-friendly error messages,
 * and critical error alerting for administrators.
 * 
 * Requirements: 17.1, 17.2, 17.3, 17.4, 17.5, 17.6, 17.7
 */
class ErrorHandler
{
    private $auditLog;
    private $mailer;
    private $logToFile;
    private $logFilePath;
    
    public function __construct()
    {
        $this->auditLog = new AuditLog();
        require_once '../app/helpers/Mailer.php';
        $this->mailer = new Mailer();
        $this->logToFile = true;
        $this->logFilePath = '../logs/error.log';
        
        // Ensure log directory exists
        $logDir = dirname($this->logFilePath);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
    }
    
    /**
     * Handle application errors with appropriate logging and user feedback
     * 
     * @param Exception $exception The exception to handle
     * @param string $context Context where error occurred (controller, model, etc.)
     * @param int|null $userId User ID if available
     * @param array $additionalData Additional context data
     * @return array Error response for user display
     */
    public function handleError($exception, $context = 'application', $userId = null, $additionalData = [])
    {
        $errorId = $this->generateErrorId();
        $severity = $this->determineSeverity($exception, $context);
        
        // Log the error
        $this->logError($exception, $context, $severity, $userId, $additionalData, $errorId);
        
        // Send alerts for critical errors
        if ($severity === 'critical') {
            $this->sendCriticalErrorAlert($exception, $context, $errorId, $userId);
        }
        
        // Return user-friendly error message
        return $this->generateUserFriendlyResponse($exception, $severity, $errorId);
    }
    
    /**
     * Handle file upload errors specifically
     * 
     * @param Exception $exception Upload exception
     * @param string $filename Original filename
     * @param int|null $userId User ID
     * @return array Error response
     */
    public function handleFileUploadError($exception, $filename = null, $userId = null)
    {
        $errorId = $this->generateErrorId();
        $context = 'file_upload';
        $severity = 'medium';
        
        $additionalData = [
            'filename' => $filename,
            'upload_context' => 'SPED_workflow'
        ];
        
        // Log the error
        $this->logError($exception, $context, $severity, $userId, $additionalData, $errorId);
        
        // Return specific file upload error message
        return [
            'success' => false,
            'error_id' => $errorId,
            'message' => $this->getFileUploadErrorMessage($exception->getMessage()),
            'type' => 'file_upload_error'
        ];
    }
    
    /**
     * Handle database errors with connection retry logic
     * 
     * @param Exception $exception Database exception
     * @param string $operation Database operation being performed
     * @param int|null $userId User ID
     * @return array Error response
     */
    public function handleDatabaseError($exception, $operation = 'unknown', $userId = null)
    {
        $errorId = $this->generateErrorId();
        $context = 'database';
        $severity = $this->isDatabaseConnectionError($exception) ? 'critical' : 'high';
        
        $additionalData = [
            'operation' => $operation,
            'sql_state' => $this->extractSQLState($exception),
            'error_code' => $exception->getCode()
        ];
        
        // Log the error
        $this->logError($exception, $context, $severity, $userId, $additionalData, $errorId);
        
        // Send alert for critical database errors
        if ($severity === 'critical') {
            $this->sendCriticalErrorAlert($exception, $context, $errorId, $userId);
        }
        
        return [
            'success' => false,
            'error_id' => $errorId,
            'message' => $this->getDatabaseErrorMessage($exception, $operation),
            'type' => 'database_error',
            'retry_suggested' => $this->isDatabaseConnectionError($exception)
        ];
    }
    
    /**
     * Handle email sending errors
     * 
     * @param Exception $exception Email exception
     * @param string $recipient Recipient email
     * @param string $subject Email subject
     * @param int|null $userId User ID
     * @return array Error response
     */
    public function handleEmailError($exception, $recipient = null, $subject = null, $userId = null)
    {
        $errorId = $this->generateErrorId();
        $context = 'email';
        $severity = 'medium';
        
        $additionalData = [
            'recipient' => $recipient,
            'subject' => $subject,
            'smtp_error' => $this->extractSMTPError($exception)
        ];
        
        // Log the error
        $this->logError($exception, $context, $severity, $userId, $additionalData, $errorId);
        
        return [
            'success' => false,
            'error_id' => $errorId,
            'message' => 'Email notification could not be sent. The system administrator has been notified.',
            'type' => 'email_error'
        ];
    }
    
    /**
     * Handle validation errors from forms
     * 
     * @param array $validationErrors Array of validation error messages
     * @param string $formType Type of form being validated
     * @param int|null $userId User ID
     * @return array Error response
     */
    public function handleValidationErrors($validationErrors, $formType = 'form', $userId = null)
    {
        $errorId = $this->generateErrorId();
        
        // Log validation errors for analysis
        $this->auditLog->logError(
            'validation',
            'low',
            "Form validation failed: $formType",
            null,
            [
                'form_type' => $formType,
                'errors' => $validationErrors,
                'error_id' => $errorId
            ],
            $userId
        );
        
        return [
            'success' => false,
            'error_id' => $errorId,
            'message' => 'Please correct the following errors:',
            'errors' => $validationErrors,
            'type' => 'validation_error'
        ];
    }
    
    /**
     * Handle security violations
     * 
     * @param string $violationType Type of security violation
     * @param string $details Violation details
     * @param int|null $userId User ID
     * @param array $additionalData Additional context
     * @return array Error response
     */
    public function handleSecurityViolation($violationType, $details, $userId = null, $additionalData = [])
    {
        $errorId = $this->generateErrorId();
        $context = 'security';
        $severity = 'critical';
        
        $additionalData = array_merge($additionalData, [
            'violation_type' => $violationType,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            'request_uri' => $_SERVER['REQUEST_URI'] ?? 'unknown'
        ]);
        
        // Create exception for logging
        $exception = new Exception("Security violation: $violationType - $details");
        
        // Log the security violation
        $this->logError($exception, $context, $severity, $userId, $additionalData, $errorId);
        
        // Send immediate alert
        $this->sendSecurityAlert($violationType, $details, $errorId, $userId, $additionalData);
        
        return [
            'success' => false,
            'error_id' => $errorId,
            'message' => 'Access denied. This incident has been logged.',
            'type' => 'security_violation'
        ];
    }
    
    /**
     * Log error to database and file system
     * 
     * @param Exception $exception The exception
     * @param string $context Error context
     * @param string $severity Error severity
     * @param int|null $userId User ID
     * @param array $additionalData Additional data
     * @param string $errorId Unique error ID
     */
    private function logError($exception, $context, $severity, $userId, $additionalData, $errorId)
    {
        // Log to database via AuditLog
        $this->auditLog->logError(
            $context,
            $severity,
            $exception->getMessage(),
            $exception->getTraceAsString(),
            array_merge($additionalData, [
                'error_id' => $errorId,
                'file' => $exception->getFile(),
                'line' => $exception->getLine()
            ]),
            $userId
        );
        
        // Log to file system
        if ($this->logToFile) {
            $this->logToFile($exception, $context, $severity, $errorId, $additionalData);
        }
    }
    
    /**
     * Log error to file system
     * 
     * @param Exception $exception The exception
     * @param string $context Error context
     * @param string $severity Error severity
     * @param string $errorId Error ID
     * @param array $additionalData Additional data
     */
    private function logToFile($exception, $context, $severity, $errorId, $additionalData)
    {
        $timestamp = date('Y-m-d H:i:s');
        $logEntry = [
            'timestamp' => $timestamp,
            'error_id' => $errorId,
            'severity' => $severity,
            'context' => $context,
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString(),
            'additional_data' => $additionalData
        ];
        
        $logLine = "[$timestamp] [$severity] [$context] [$errorId] " . 
                   $exception->getMessage() . " in " . 
                   $exception->getFile() . ":" . $exception->getLine() . 
                   " | Data: " . json_encode($additionalData) . PHP_EOL;
        
        file_put_contents($this->logFilePath, $logLine, FILE_APPEND | LOCK_EX);
    }
    
    /**
     * Send critical error alert to administrators
     * 
     * @param Exception $exception The exception
     * @param string $context Error context
     * @param string $errorId Error ID
     * @param int|null $userId User ID
     */
    private function sendCriticalErrorAlert($exception, $context, $errorId, $userId = null)
    {
        try {
            $subject = "CRITICAL ERROR ALERT - SignED SPED System [$errorId]";
            
            $body = $this->buildCriticalErrorEmailBody($exception, $context, $errorId, $userId);
            
            // Get admin email addresses
            $adminEmails = $this->getAdminEmails();
            
            foreach ($adminEmails as $adminEmail) {
                $this->mailer->sendCriticalAlert($adminEmail, $subject, $body);
            }
            
        } catch (Exception $e) {
            // If email fails, log to file as backup
            error_log("Failed to send critical error alert: " . $e->getMessage());
            $this->logToFile($e, 'email_alert', 'critical', $errorId, [
                'original_error' => $exception->getMessage()
            ]);
        }
    }
    
    /**
     * Send security violation alert
     * 
     * @param string $violationType Violation type
     * @param string $details Violation details
     * @param string $errorId Error ID
     * @param int|null $userId User ID
     * @param array $additionalData Additional data
     */
    private function sendSecurityAlert($violationType, $details, $errorId, $userId, $additionalData)
    {
        try {
            $subject = "SECURITY ALERT - SignED SPED System [$errorId]";
            
            $body = $this->buildSecurityAlertEmailBody($violationType, $details, $errorId, $userId, $additionalData);
            
            // Get admin email addresses
            $adminEmails = $this->getAdminEmails();
            
            foreach ($adminEmails as $adminEmail) {
                $this->mailer->sendSecurityAlert($adminEmail, $subject, $body);
            }
            
        } catch (Exception $e) {
            error_log("Failed to send security alert: " . $e->getMessage());
        }
    }
    
    /**
     * Generate unique error ID
     * 
     * @return string Unique error ID
     */
    private function generateErrorId()
    {
        return 'ERR_' . date('Ymd_His') . '_' . substr(md5(uniqid(mt_rand(), true)), 0, 8);
    }
    
    /**
     * Determine error severity based on exception and context
     * 
     * @param Exception $exception The exception
     * @param string $context Error context
     * @return string Severity level
     */
    private function determineSeverity($exception, $context)
    {
        // Critical errors
        if ($context === 'security' || 
            $this->isDatabaseConnectionError($exception) ||
            strpos($exception->getMessage(), 'encryption') !== false ||
            strpos($exception->getMessage(), 'authentication') !== false) {
            return 'critical';
        }
        
        // High severity errors
        if ($context === 'database' ||
            $exception instanceof PDOException ||
            strpos($exception->getMessage(), 'permission') !== false) {
            return 'high';
        }
        
        // Medium severity errors
        if ($context === 'file_upload' ||
            $context === 'email' ||
            strpos($exception->getMessage(), 'validation') !== false) {
            return 'medium';
        }
        
        // Default to low severity
        return 'low';
    }
    
    /**
     * Generate user-friendly error response
     * 
     * @param Exception $exception The exception
     * @param string $severity Error severity
     * @param string $errorId Error ID
     * @return array User-friendly error response
     */
    private function generateUserFriendlyResponse($exception, $severity, $errorId)
    {
        $userMessages = [
            'critical' => 'A critical system error has occurred. Please contact the system administrator immediately.',
            'high' => 'A system error has occurred. Please try again or contact support if the problem persists.',
            'medium' => 'An error occurred while processing your request. Please try again.',
            'low' => 'Please check your input and try again.'
        ];
        
        // Don't expose technical details to users
        $message = $userMessages[$severity] ?? $userMessages['medium'];
        
        // Add specific context for certain error types
        if (strpos($exception->getMessage(), 'file') !== false) {
            $message = 'There was a problem with the file upload. Please check the file type and size, then try again.';
        } elseif (strpos($exception->getMessage(), 'database') !== false) {
            $message = 'A database error occurred. Please try again in a few moments.';
        } elseif (strpos($exception->getMessage(), 'email') !== false) {
            $message = 'Email notification could not be sent, but your request was processed successfully.';
        }
        
        return [
            'success' => false,
            'error_id' => $errorId,
            'message' => $message,
            'severity' => $severity,
            'type' => 'application_error'
        ];
    }
    
    /**
     * Get file upload specific error message
     * 
     * @param string $errorMessage Original error message
     * @return string User-friendly file upload error message
     */
    private function getFileUploadErrorMessage($errorMessage)
    {
        $errorMessage = strtolower($errorMessage);
        
        if (strpos($errorMessage, 'file type') !== false || strpos($errorMessage, 'extension') !== false) {
            return 'The file type is not allowed. Please upload a PDF, Word document, PowerPoint, or image file.';
        }
        
        if (strpos($errorMessage, 'file size') !== false || strpos($errorMessage, 'size exceeds') !== false) {
            return 'The file is too large. Please upload a file smaller than the allowed limit.';
        }
        
        if (strpos($errorMessage, 'malicious') !== false || strpos($errorMessage, 'security') !== false) {
            return 'The file failed security checks. Please ensure the file is safe and try again.';
        }
        
        if (strpos($errorMessage, 'upload') !== false) {
            return 'File upload failed. Please check your internet connection and try again.';
        }
        
        return 'There was a problem with the file upload. Please try again with a different file.';
    }
    
    /**
     * Get database specific error message
     * 
     * @param Exception $exception Database exception
     * @param string $operation Database operation
     * @return string User-friendly database error message
     */
    private function getDatabaseErrorMessage($exception, $operation)
    {
        if ($this->isDatabaseConnectionError($exception)) {
            return 'The system is temporarily unavailable. Please try again in a few moments.';
        }
        
        if (strpos($exception->getMessage(), 'duplicate') !== false) {
            return 'This record already exists. Please check your information and try again.';
        }
        
        if (strpos($exception->getMessage(), 'foreign key') !== false) {
            return 'This action cannot be completed due to related data. Please contact support for assistance.';
        }
        
        return 'A database error occurred while processing your request. Please try again.';
    }
    
    /**
     * Check if exception is a database connection error
     * 
     * @param Exception $exception The exception
     * @return bool True if connection error
     */
    private function isDatabaseConnectionError($exception)
    {
        $message = strtolower($exception->getMessage());
        $connectionErrors = [
            'connection refused',
            'server has gone away',
            'lost connection',
            'timeout',
            'access denied',
            'unknown database',
            'can\'t connect'
        ];
        
        foreach ($connectionErrors as $error) {
            if (strpos($message, $error) !== false) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Extract SQL state from database exception
     * 
     * @param Exception $exception Database exception
     * @return string|null SQL state code
     */
    private function extractSQLState($exception)
    {
        if ($exception instanceof PDOException && isset($exception->errorInfo[0])) {
            return $exception->errorInfo[0];
        }
        return null;
    }
    
    /**
     * Extract SMTP error from email exception
     * 
     * @param Exception $exception Email exception
     * @return string|null SMTP error code
     */
    private function extractSMTPError($exception)
    {
        $message = $exception->getMessage();
        if (preg_match('/SMTP Error: (.+)/', $message, $matches)) {
            return $matches[1];
        }
        return null;
    }
    
    /**
     * Build critical error email body
     * 
     * @param Exception $exception The exception
     * @param string $context Error context
     * @param string $errorId Error ID
     * @param int|null $userId User ID
     * @return string Email body
     */
    private function buildCriticalErrorEmailBody($exception, $context, $errorId, $userId)
    {
        $timestamp = date('Y-m-d H:i:s');
        $userInfo = $userId ? "User ID: $userId" : "No user session";
        
        return "
CRITICAL ERROR ALERT - SignED SPED System

Error ID: $errorId
Timestamp: $timestamp
Context: $context
$userInfo

Error Message:
{$exception->getMessage()}

File: {$exception->getFile()}
Line: {$exception->getLine()}

IP Address: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown') . "
User Agent: " . ($_SERVER['HTTP_USER_AGENT'] ?? 'unknown') . "
Request URI: " . ($_SERVER['REQUEST_URI'] ?? 'unknown') . "

Stack Trace:
{$exception->getTraceAsString()}

Please investigate this error immediately.

SignED SPED System
        ";
    }
    
    /**
     * Build security alert email body
     * 
     * @param string $violationType Violation type
     * @param string $details Violation details
     * @param string $errorId Error ID
     * @param int|null $userId User ID
     * @param array $additionalData Additional data
     * @return string Email body
     */
    private function buildSecurityAlertEmailBody($violationType, $details, $errorId, $userId, $additionalData)
    {
        $timestamp = date('Y-m-d H:i:s');
        $userInfo = $userId ? "User ID: $userId" : "No user session";
        
        return "
SECURITY VIOLATION ALERT - SignED SPED System

Alert ID: $errorId
Timestamp: $timestamp
Violation Type: $violationType
$userInfo

Details:
$details

IP Address: " . ($additionalData['ip_address'] ?? 'unknown') . "
User Agent: " . ($additionalData['user_agent'] ?? 'unknown') . "
Request URI: " . ($additionalData['request_uri'] ?? 'unknown') . "

Additional Data:
" . json_encode($additionalData, JSON_PRETTY_PRINT) . "

This security incident requires immediate attention.

SignED SPED System
        ";
    }
    
    /**
     * Get administrator email addresses
     * 
     * @return array Admin email addresses
     */
    private function getAdminEmails()
    {
        try {
            // Use existing database connection pattern
            require_once '../config/database.php';
            $database = new Database();
            $db = $database->connect();
            
            $sql = "SELECT email FROM users WHERE role = 'admin' AND email IS NOT NULL";
            $stmt = $db->prepare($sql);
            $stmt->execute();
            
            $emails = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $emails[] = $row['email'];
            }
            
            // Fallback to default admin email if no admins found
            if (empty($emails)) {
                $emails[] = 'admin@signed-sped.local'; // Configure this in production
            }
            
            return $emails;
            
        } catch (Exception $e) {
            // Fallback email if database query fails
            return ['admin@signed-sped.local'];
        }
    }
    
    /**
     * Get error statistics for admin dashboard
     * 
     * @param int $days Number of days to analyze
     * @return array Error statistics
     */
    public function getErrorStatistics($days = 7)
    {
        try {
            require_once '../config/database.php';
            $database = new Database();
            $db = $database->connect();
            
            $sql = "SELECT 
                        error_type,
                        severity,
                        COUNT(*) as count,
                        DATE(created_at) as error_date
                    FROM error_logs 
                    WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
                    GROUP BY error_type, severity, DATE(created_at)
                    ORDER BY error_date DESC, count DESC";
            
            $stmt = $db->prepare($sql);
            $stmt->execute([$days]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            error_log("Failed to get error statistics: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Clean up old error logs
     * 
     * @param int $retentionDays Number of days to retain logs
     * @return bool Success status
     */
    public function cleanupOldLogs($retentionDays = 90)
    {
        try {
            require_once '../config/database.php';
            $database = new Database();
            $db = $database->connect();
            
            $sql = "DELETE FROM error_logs WHERE created_at < DATE_SUB(NOW(), INTERVAL ? DAY)";
            $stmt = $db->prepare($sql);
            $stmt->execute([$retentionDays]);
            
            return true;
            
        } catch (Exception $e) {
            error_log("Failed to cleanup old logs: " . $e->getMessage());
            return false;
        }
    }
}