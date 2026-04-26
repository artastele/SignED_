<?php

/**
 * Security Validation Trait
 * 
 * This trait provides security validation methods that can be used across
 * all controllers to ensure consistent security practices.
 * 
 * Requirements: 16.1, 16.2, 16.3, 16.4, 16.5, 16.6, 16.7
 */
trait SecurityValidation
{
    private $inputValidator;
    private $errorHandler;
    
    /**
     * Initialize security validation components
     */
    protected function initializeSecurity()
    {
        if (!$this->inputValidator) {
            require_once '../app/helpers/InputValidator.php';
            $this->inputValidator = new InputValidator();
        }
        
        if (!$this->errorHandler) {
            require_once '../app/helpers/ErrorHandler.php';
            $this->errorHandler = new ErrorHandler();
        }
    }
    
    /**
     * Validate and sanitize form input with comprehensive security checks
     * 
     * @param array $data Form data to validate
     * @param string $formType Type of form (enrollment, assessment, iep, meeting)
     * @return array Validation result
     */
    protected function validateFormInput($data, $formType)
    {
        $this->initializeSecurity();
        
        // Perform CSRF token validation
        if (!$this->validateCSRFToken($data)) {
            return $this->errorHandler->handleSecurityViolation(
                'csrf_token_mismatch',
                'Invalid or missing CSRF token',
                $this->getCurrentUserId(),
                ['form_type' => $formType]
            );
        }
        
        // Rate limiting check
        if (!$this->checkRateLimit($formType)) {
            return $this->errorHandler->handleSecurityViolation(
                'rate_limit_exceeded',
                "Too many $formType form submissions",
                $this->getCurrentUserId(),
                ['form_type' => $formType]
            );
        }
        
        // Validate based on form type
        switch ($formType) {
            case 'enrollment':
                $result = $this->inputValidator->validateEnrollmentForm($data);
                break;
            case 'assessment':
                $result = $this->inputValidator->validateAssessmentForm($data);
                break;
            case 'iep':
                $result = $this->inputValidator->validateIepForm($data);
                break;
            case 'meeting':
                $result = $this->inputValidator->validateMeetingForm($data);
                break;
            default:
                $result = ['valid' => false, 'errors' => ['Unknown form type']];
        }
        
        if (!$result['valid']) {
            return $this->errorHandler->handleValidationErrors(
                $result['errors'],
                $formType,
                $this->getCurrentUserId()
            );
        }
        
        return [
            'success' => true,
            'data' => $result['data']
        ];
    }
    
    /**
     * Validate file upload with comprehensive security scanning
     * 
     * @param array $file $_FILES array element
     * @param string $context Upload context
     * @return array Validation result
     */
    protected function validateFileUpload($file, $context)
    {
        $this->initializeSecurity();
        
        $userId = $this->getCurrentUserId();
        
        // Check upload rate limiting
        if (!$this->checkUploadRateLimit()) {
            return $this->errorHandler->handleSecurityViolation(
                'upload_rate_limit_exceeded',
                'Too many file uploads in short period',
                $userId,
                ['context' => $context]
            );
        }
        
        // Validate file upload
        $result = $this->inputValidator->validateFileUpload($file, $context, $userId);
        
        if (!$result['valid']) {
            return $this->errorHandler->handleFileUploadError(
                new Exception(implode(', ', $result['errors'])),
                $file['name'] ?? 'unknown',
                $userId
            );
        }
        
        return [
            'success' => true,
            'file_info' => $result['file_info']
        ];
    }
    
    /**
     * Execute database query with SQL injection prevention
     * 
     * @param string $sql SQL query with placeholders
     * @param array $params Parameters for the query
     * @param string $operation Operation description for logging
     * @return array Query result
     */
    protected function executeSecureQuery($sql, $params = [], $operation = 'database_operation')
    {
        $this->initializeSecurity();
        
        try {
            // Validate SQL query for suspicious patterns
            if (!$this->validateSQLQuery($sql)) {
                throw new Exception("SQL query failed security validation");
            }
            
            // Prepare and execute with parameterized query
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute($params);
            
            if (!$result) {
                throw new PDOException("Query execution failed: " . implode(', ', $stmt->errorInfo()));
            }
            
            return [
                'success' => true,
                'statement' => $stmt
            ];
            
        } catch (Exception $e) {
            return $this->errorHandler->handleDatabaseError(
                $e,
                $operation,
                $this->getCurrentUserId()
            );
        }
    }
    
    /**
     * Validate CSRF token
     * 
     * @param array $data Form data
     * @return bool Token is valid
     */
    private function validateCSRFToken($data)
    {
        if (!isset($_SESSION['csrf_token']) || !isset($data['csrf_token'])) {
            return false;
        }
        
        return hash_equals($_SESSION['csrf_token'], $data['csrf_token']);
    }
    
    /**
     * Generate CSRF token for forms
     * 
     * @return string CSRF token
     */
    protected function generateCSRFToken()
    {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    /**
     * Check rate limiting for form submissions
     * 
     * @param string $formType Type of form
     * @return bool Rate limit not exceeded
     */
    private function checkRateLimit($formType)
    {
        $userId = $this->getCurrentUserId();
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        
        // Check user-based rate limiting
        if ($userId && !$this->checkUserRateLimit($userId, $formType)) {
            return false;
        }
        
        // Check IP-based rate limiting
        if (!$this->checkIPRateLimit($ipAddress, $formType)) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Check user-based rate limiting
     * 
     * @param int $userId User ID
     * @param string $formType Form type
     * @return bool Rate limit not exceeded
     */
    private function checkUserRateLimit($userId, $formType)
    {
        $cacheKey = "rate_limit_user_{$userId}_{$formType}";
        $limit = $this->getRateLimitForForm($formType);
        
        return $this->checkRateLimitCache($cacheKey, $limit['requests'], $limit['window']);
    }
    
    /**
     * Check IP-based rate limiting
     * 
     * @param string $ipAddress IP address
     * @param string $formType Form type
     * @return bool Rate limit not exceeded
     */
    private function checkIPRateLimit($ipAddress, $formType)
    {
        $cacheKey = "rate_limit_ip_{$ipAddress}_{$formType}";
        $limit = $this->getRateLimitForForm($formType);
        
        // IP limits are more restrictive
        return $this->checkRateLimitCache($cacheKey, $limit['requests'] / 2, $limit['window']);
    }
    
    /**
     * Check upload rate limiting
     * 
     * @return bool Rate limit not exceeded
     */
    private function checkUploadRateLimit()
    {
        $userId = $this->getCurrentUserId();
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        
        // 10 uploads per hour per user
        $userKey = "upload_rate_limit_user_{$userId}";
        if (!$this->checkRateLimitCache($userKey, 10, 3600)) {
            return false;
        }
        
        // 20 uploads per hour per IP
        $ipKey = "upload_rate_limit_ip_{$ipAddress}";
        if (!$this->checkRateLimitCache($ipKey, 20, 3600)) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Check rate limit using file-based cache
     * 
     * @param string $key Cache key
     * @param int $maxRequests Maximum requests allowed
     * @param int $windowSeconds Time window in seconds
     * @return bool Rate limit not exceeded
     */
    private function checkRateLimitCache($key, $maxRequests, $windowSeconds)
    {
        $cacheDir = '../cache';
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0755, true);
        }
        
        $cacheFile = $cacheDir . '/' . md5($key) . '.txt';
        $now = time();
        
        // Read existing data
        $requests = [];
        if (file_exists($cacheFile)) {
            $data = file_get_contents($cacheFile);
            if ($data) {
                $requests = json_decode($data, true) ?: [];
            }
        }
        
        // Remove old requests outside the window
        $requests = array_filter($requests, function($timestamp) use ($now, $windowSeconds) {
            return ($now - $timestamp) < $windowSeconds;
        });
        
        // Check if limit exceeded
        if (count($requests) >= $maxRequests) {
            return false;
        }
        
        // Add current request
        $requests[] = $now;
        
        // Save updated data
        file_put_contents($cacheFile, json_encode($requests), LOCK_EX);
        
        return true;
    }
    
    /**
     * Get rate limit configuration for form type
     * 
     * @param string $formType Form type
     * @return array Rate limit configuration
     */
    private function getRateLimitForForm($formType)
    {
        $limits = [
            'enrollment' => ['requests' => 5, 'window' => 3600],    // 5 per hour
            'assessment' => ['requests' => 10, 'window' => 3600],   // 10 per hour
            'iep' => ['requests' => 10, 'window' => 3600],          // 10 per hour
            'meeting' => ['requests' => 20, 'window' => 3600],      // 20 per hour
            'default' => ['requests' => 10, 'window' => 3600]       // 10 per hour
        ];
        
        return $limits[$formType] ?? $limits['default'];
    }
    
    /**
     * Validate SQL query for suspicious patterns
     * 
     * @param string $sql SQL query
     * @return bool Query is safe
     */
    private function validateSQLQuery($sql)
    {
        // Check for dangerous SQL patterns
        $dangerousPatterns = [
            '/;\s*(DROP|DELETE|TRUNCATE|ALTER|CREATE|INSERT|UPDATE)\s+/i',
            '/UNION\s+SELECT/i',
            '/\/\*.*\*\//s',
            '/--\s*[^\r\n]*/i',
            '/;\s*EXEC\s*\(/i',
            '/;\s*EXECUTE\s*\(/i'
        ];
        
        foreach ($dangerousPatterns as $pattern) {
            if (preg_match($pattern, $sql)) {
                // Log suspicious SQL attempt
                $this->errorHandler->handleSecurityViolation(
                    'suspicious_sql_query',
                    "Potentially dangerous SQL pattern detected",
                    $this->getCurrentUserId(),
                    ['sql_pattern' => $pattern, 'query' => substr($sql, 0, 200)]
                );
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Sanitize output for display to prevent XSS
     * 
     * @param mixed $data Data to sanitize
     * @param string $context Output context (html, attribute, javascript, css)
     * @return mixed Sanitized data
     */
    protected function sanitizeOutput($data, $context = 'html')
    {
        if (is_array($data)) {
            return array_map(function($item) use ($context) {
                return $this->sanitizeOutput($item, $context);
            }, $data);
        }
        
        if (is_object($data)) {
            foreach ($data as $key => $value) {
                $data->$key = $this->sanitizeOutput($value, $context);
            }
            return $data;
        }
        
        if (!is_string($data)) {
            return $data;
        }
        
        switch ($context) {
            case 'html':
                return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
                
            case 'attribute':
                return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
                
            case 'javascript':
                return json_encode($data, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
                
            case 'css':
                return preg_replace('/[^a-zA-Z0-9\-_]/', '', $data);
                
            case 'url':
                return urlencode($data);
                
            default:
                return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        }
    }
    
    /**
     * Log security event for audit purposes
     * 
     * @param string $eventType Type of security event
     * @param string $description Event description
     * @param array $additionalData Additional event data
     */
    protected function logSecurityEvent($eventType, $description, $additionalData = [])
    {
        $this->initializeSecurity();
        
        $auditLog = new AuditLog();
        $auditLog->logError(
            'security',
            'medium',
            $description,
            null,
            array_merge($additionalData, [
                'event_type' => $eventType,
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
            ]),
            $this->getCurrentUserId()
        );
    }
    
    /**
     * Validate session integrity
     * 
     * @return bool Session is valid
     */
    protected function validateSessionIntegrity()
    {
        // Check if session exists
        if (!isset($_SESSION['user_id'])) {
            return false;
        }
        
        // Check session timeout
        if (!$this->checkSessionTimeout()) {
            return false;
        }
        
        // Validate session fingerprint
        $currentFingerprint = $this->generateSessionFingerprint();
        if (isset($_SESSION['fingerprint']) && $_SESSION['fingerprint'] !== $currentFingerprint) {
            // Session hijacking attempt
            $this->logSecurityEvent(
                'session_hijacking_attempt',
                'Session fingerprint mismatch detected',
                ['expected' => $_SESSION['fingerprint'], 'actual' => $currentFingerprint]
            );
            
            session_destroy();
            return false;
        }
        
        // Set fingerprint if not exists
        if (!isset($_SESSION['fingerprint'])) {
            $_SESSION['fingerprint'] = $currentFingerprint;
        }
        
        return true;
    }
    
    /**
     * Generate session fingerprint for security
     * 
     * @return string Session fingerprint
     */
    private function generateSessionFingerprint()
    {
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $acceptLanguage = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '';
        $acceptEncoding = $_SERVER['HTTP_ACCEPT_ENCODING'] ?? '';
        
        return hash('sha256', $userAgent . $acceptLanguage . $acceptEncoding);
    }
    
    /**
     * Enhanced session timeout check with security logging
     * 
     * @return bool Session is not timed out
     */
    public function checkSessionTimeout()
    {
        if (!isset($_SESSION['user_id'])) {
            return false;
        }
        
        $timeout = 15 * 60; // 15 minutes
        
        if (isset($_SESSION['last_activity'])) {
            if (time() - $_SESSION['last_activity'] > $timeout) {
                // Log session timeout
                $this->logSecurityEvent(
                    'session_timeout',
                    'User session timed out',
                    ['user_id' => $_SESSION['user_id'], 'last_activity' => $_SESSION['last_activity']]
                );
                
                session_unset();
                session_destroy();
                return false;
            }
        }
        
        // Update last activity
        $_SESSION['last_activity'] = time();
        
        return true;
    }
}