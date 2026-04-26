<?php

/**
 * Comprehensive Input Validation and Security Helper
 * 
 * This class provides centralized input validation, sanitization, and security
 * hardening methods for all SPED workflow forms and file uploads.
 * 
 * Requirements: 16.1, 16.2, 16.3, 16.4, 16.5, 16.6, 16.7
 */
class InputValidator
{
    private $auditLog;
    
    public function __construct()
    {
        $this->auditLog = new AuditLog();
    }
    
    /**
     * Validate and sanitize form data for enrollment submission
     * 
     * @param array $data Form data to validate
     * @return array Validation result with sanitized data or errors
     */
    public function validateEnrollmentForm($data)
    {
        $errors = [];
        $sanitized = [];
        
        // Required fields validation
        $requiredFields = ['first_name', 'last_name', 'date_of_birth', 'grade_level'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                $errors[] = "Field '$field' is required";
                continue;
            }
            
            // Sanitize based on field type
            switch ($field) {
                case 'first_name':
                case 'last_name':
                    $sanitized[$field] = $this->sanitizeName($data[$field]);
                    if (strlen($sanitized[$field]) < 2) {
                        $errors[] = "Field '$field' must be at least 2 characters";
                    }
                    break;
                    
                case 'date_of_birth':
                    $sanitized[$field] = $this->validateDate($data[$field]);
                    if (!$sanitized[$field]) {
                        $errors[] = "Invalid date format for '$field'";
                    } else {
                        // Validate age range (3-21 years)
                        $age = $this->calculateAge($sanitized[$field]);
                        if ($age < 3 || $age > 21) {
                            $errors[] = "Learner age must be between 3 and 21 years";
                        }
                    }
                    break;
                    
                case 'grade_level':
                    $sanitized[$field] = $this->sanitizeGradeLevel($data[$field]);
                    if (!$sanitized[$field]) {
                        $errors[] = "Invalid grade level";
                    }
                    break;
            }
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'data' => $sanitized
        ];
    }
    
    /**
     * Validate and sanitize assessment form data
     * 
     * @param array $data Assessment form data
     * @return array Validation result
     */
    public function validateAssessmentForm($data)
    {
        $errors = [];
        $sanitized = [];
        
        $requiredFields = [
            'cognitive_ability',
            'communication_skills',
            'social_emotional_development',
            'adaptive_behavior',
            'academic_performance',
            'assessment_date'
        ];
        
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                $errors[] = "Field '$field' is required";
                continue;
            }
            
            if ($field === 'assessment_date') {
                $sanitized[$field] = $this->validateDate($data[$field]);
                if (!$sanitized[$field]) {
                    $errors[] = "Invalid assessment date format";
                } elseif (strtotime($sanitized[$field]) > time()) {
                    $errors[] = "Assessment date cannot be in the future";
                }
            } else {
                // Text field validation
                $sanitized[$field] = $this->sanitizeTextArea($data[$field]);
                if (strlen($sanitized[$field]) < 10) {
                    $errors[] = "Field '$field' must contain at least 10 characters for a meaningful assessment";
                }
                if (strlen($sanitized[$field]) > 5000) {
                    $errors[] = "Field '$field' exceeds maximum length of 5000 characters";
                }
            }
        }
        
        // Optional recommendations field
        if (isset($data['recommendations'])) {
            $sanitized['recommendations'] = $this->sanitizeTextArea($data['recommendations']);
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'data' => $sanitized
        ];
    }
    
    /**
     * Validate and sanitize IEP form data
     * 
     * @param array $data IEP form data
     * @return array Validation result
     */
    public function validateIepForm($data)
    {
        $errors = [];
        $sanitized = [];
        
        $requiredFields = [
            'present_level_performance',
            'annual_goals',
            'short_term_objectives',
            'special_education_services',
            'accommodations',
            'progress_measurement',
            'start_date',
            'end_date'
        ];
        
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                $errors[] = "Field '$field' is required";
                continue;
            }
            
            if (in_array($field, ['start_date', 'end_date'])) {
                $sanitized[$field] = $this->validateDate($data[$field]);
                if (!$sanitized[$field]) {
                    $errors[] = "Invalid date format for '$field'";
                }
            } else {
                $sanitized[$field] = $this->sanitizeTextArea($data[$field]);
                if (strlen($sanitized[$field]) < 20) {
                    $errors[] = "Field '$field' must contain at least 20 characters";
                }
                if (strlen($sanitized[$field]) > 10000) {
                    $errors[] = "Field '$field' exceeds maximum length of 10000 characters";
                }
            }
        }
        
        // Validate date range
        if (isset($sanitized['start_date']) && isset($sanitized['end_date'])) {
            if (strtotime($sanitized['start_date']) >= strtotime($sanitized['end_date'])) {
                $errors[] = "End date must be after start date";
            }
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'data' => $sanitized
        ];
    }
    
    /**
     * Validate and sanitize meeting form data
     * 
     * @param array $data Meeting form data
     * @return array Validation result
     */
    public function validateMeetingForm($data)
    {
        $errors = [];
        $sanitized = [];
        
        $requiredFields = ['learner_id', 'meeting_date', 'meeting_time', 'location', 'participants'];
        
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                $errors[] = "Field '$field' is required";
                continue;
            }
            
            switch ($field) {
                case 'learner_id':
                    $sanitized[$field] = $this->validateInteger($data[$field]);
                    if (!$sanitized[$field]) {
                        $errors[] = "Invalid learner ID";
                    }
                    break;
                    
                case 'meeting_date':
                    $sanitized[$field] = $this->validateDate($data[$field]);
                    if (!$sanitized[$field]) {
                        $errors[] = "Invalid meeting date format";
                    }
                    break;
                    
                case 'meeting_time':
                    $sanitized[$field] = $this->validateTime($data[$field]);
                    if (!$sanitized[$field]) {
                        $errors[] = "Invalid meeting time format";
                    }
                    break;
                    
                case 'location':
                    $sanitized[$field] = $this->sanitizeText($data[$field]);
                    if (strlen($sanitized[$field]) < 3) {
                        $errors[] = "Meeting location must be at least 3 characters";
                    }
                    break;
                    
                case 'participants':
                    if (!is_array($data[$field])) {
                        $errors[] = "Participants must be an array";
                    } else {
                        $sanitized[$field] = $this->validateParticipants($data[$field]);
                        if (empty($sanitized[$field])) {
                            $errors[] = "At least one participant is required";
                        }
                    }
                    break;
            }
        }
        
        // Validate meeting datetime is in the future
        if (isset($sanitized['meeting_date']) && isset($sanitized['meeting_time'])) {
            $meetingDateTime = $sanitized['meeting_date'] . ' ' . $sanitized['meeting_time'];
            if (strtotime($meetingDateTime) <= time()) {
                $errors[] = "Meeting date and time must be in the future";
            }
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'data' => $sanitized
        ];
    }
    
    /**
     * Comprehensive file upload validation with security scanning
     * 
     * @param array $file $_FILES array element
     * @param string $context Upload context (enrollment, learning_material, submission)
     * @param int|null $userId User ID for audit logging
     * @return array Validation result
     */
    public function validateFileUpload($file, $context, $userId = null)
    {
        $errors = [];
        
        // Check for upload errors
        if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = $this->getUploadErrorMessage($file['error'] ?? UPLOAD_ERR_NO_FILE);
            return ['valid' => false, 'errors' => $errors];
        }
        
        // Define allowed file types by context
        $allowedTypes = $this->getAllowedFileTypes($context);
        $maxSize = $this->getMaxFileSize($context);
        
        // Validate file extension
        $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($fileExtension, $allowedTypes['extensions'])) {
            $errors[] = "Invalid file type. Allowed types: " . implode(', ', $allowedTypes['extensions']);
        }
        
        // Validate file size
        if ($file['size'] > $maxSize) {
            $errors[] = "File size exceeds " . $this->formatFileSize($maxSize) . " limit";
        }
        
        // Validate MIME type
        $mimeType = $this->getMimeType($file['tmp_name']);
        if (!in_array($mimeType, $allowedTypes['mime_types'])) {
            $errors[] = "Invalid file MIME type: " . $mimeType;
        }
        
        // Security validations
        $securityCheck = $this->performSecurityChecks($file, $userId);
        if (!$securityCheck['valid']) {
            $errors = array_merge($errors, $securityCheck['errors']);
        }
        
        // Malware scanning
        $malwareCheck = $this->scanForMalware($file['tmp_name'], $userId);
        if (!$malwareCheck['valid']) {
            $errors = array_merge($errors, $malwareCheck['errors']);
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'file_info' => [
                'original_name' => $file['name'],
                'size' => $file['size'],
                'mime_type' => $mimeType,
                'extension' => $fileExtension
            ]
        ];
    }
    
    /**
     * Sanitize user name input
     * 
     * @param string $name Name to sanitize
     * @return string Sanitized name
     */
    private function sanitizeName($name)
    {
        // Remove HTML tags and encode special characters
        $sanitized = htmlspecialchars(trim($name), ENT_QUOTES, 'UTF-8');
        
        // Allow only letters, spaces, hyphens, and apostrophes
        $sanitized = preg_replace('/[^a-zA-Z\s\-\']/', '', $sanitized);
        
        // Remove multiple spaces
        $sanitized = preg_replace('/\s+/', ' ', $sanitized);
        
        return trim($sanitized);
    }
    
    /**
     * Sanitize text input (single line)
     * 
     * @param string $text Text to sanitize
     * @return string Sanitized text
     */
    private function sanitizeText($text)
    {
        // Remove HTML tags and encode special characters
        $sanitized = htmlspecialchars(trim($text), ENT_QUOTES, 'UTF-8');
        
        // Remove script tags and javascript
        $sanitized = preg_replace('/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/mi', '', $sanitized);
        
        // Remove javascript: and vbscript: protocols
        $sanitized = preg_replace('/javascript\s*:/i', '', $sanitized);
        $sanitized = preg_replace('/vbscript\s*:/i', '', $sanitized);
        
        // Remove event handlers
        $sanitized = preg_replace('/on\w+\s*=/i', '', $sanitized);
        
        // Remove remaining HTML tags
        $sanitized = strip_tags($sanitized);
        
        // Remove control characters except newlines and tabs
        $sanitized = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $sanitized);
        
        return trim($sanitized);
    }
    
    /**
     * Sanitize textarea input (multi-line)
     * 
     * @param string $text Text to sanitize
     * @return string Sanitized text
     */
    private function sanitizeTextArea($text)
    {
        // Remove HTML tags and encode special characters
        $sanitized = htmlspecialchars(trim($text), ENT_QUOTES, 'UTF-8');
        
        // Remove script tags and javascript
        $sanitized = preg_replace('/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/mi', '', $sanitized);
        
        // Remove remaining HTML tags
        $sanitized = strip_tags($sanitized);
        
        // Remove control characters except newlines, carriage returns, and tabs
        $sanitized = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $sanitized);
        
        // Normalize line endings
        $sanitized = str_replace(["\r\n", "\r"], "\n", $sanitized);
        
        // Limit consecutive newlines
        $sanitized = preg_replace('/\n{3,}/', "\n\n", $sanitized);
        
        return trim($sanitized);
    }
    
    /**
     * Validate and sanitize grade level
     * 
     * @param string $grade Grade level to validate
     * @return string|false Sanitized grade level or false if invalid
     */
    private function sanitizeGradeLevel($grade)
    {
        $grade = trim($grade);
        
        // Valid grade levels for SPED
        $validGrades = [
            'Pre-K', 'Kindergarten', 'Grade 1', 'Grade 2', 'Grade 3', 'Grade 4',
            'Grade 5', 'Grade 6', 'Grade 7', 'Grade 8', 'Grade 9', 'Grade 10',
            'Grade 11', 'Grade 12', 'Special Education'
        ];
        
        return in_array($grade, $validGrades) ? $grade : false;
    }
    
    /**
     * Validate date format and return standardized date
     * 
     * @param string $date Date string to validate
     * @return string|false Validated date in Y-m-d format or false if invalid
     */
    private function validateDate($date)
    {
        $dateObj = DateTime::createFromFormat('Y-m-d', $date);
        if ($dateObj && $dateObj->format('Y-m-d') === $date) {
            return $date;
        }
        
        // Try alternative formats
        $formats = ['m/d/Y', 'd/m/Y', 'Y/m/d'];
        foreach ($formats as $format) {
            $dateObj = DateTime::createFromFormat($format, $date);
            if ($dateObj) {
                return $dateObj->format('Y-m-d');
            }
        }
        
        return false;
    }
    
    /**
     * Validate time format
     * 
     * @param string $time Time string to validate
     * @return string|false Validated time in H:i format or false if invalid
     */
    private function validateTime($time)
    {
        $timeObj = DateTime::createFromFormat('H:i', $time);
        if ($timeObj && $timeObj->format('H:i') === $time) {
            return $time;
        }
        
        // Try 12-hour format
        $timeObj = DateTime::createFromFormat('g:i A', $time);
        if ($timeObj) {
            return $timeObj->format('H:i');
        }
        
        return false;
    }
    
    /**
     * Validate integer input
     * 
     * @param mixed $value Value to validate
     * @return int|false Validated integer or false if invalid
     */
    private function validateInteger($value)
    {
        if (is_numeric($value) && (int)$value == $value && $value > 0) {
            return (int)$value;
        }
        return false;
    }
    
    /**
     * Validate meeting participants array
     * 
     * @param array $participants Participant IDs
     * @return array Validated participant IDs
     */
    private function validateParticipants($participants)
    {
        $validated = [];
        foreach ($participants as $participantId) {
            $id = $this->validateInteger($participantId);
            if ($id) {
                $validated[] = $id;
            }
        }
        return array_unique($validated);
    }
    
    /**
     * Calculate age from date of birth
     * 
     * @param string $dateOfBirth Date of birth in Y-m-d format
     * @return int Age in years
     */
    private function calculateAge($dateOfBirth)
    {
        $dob = new DateTime($dateOfBirth);
        $now = new DateTime();
        return $dob->diff($now)->y;
    }
    
    /**
     * Get allowed file types by context
     * 
     * @param string $context Upload context
     * @return array Allowed extensions and MIME types
     */
    private function getAllowedFileTypes($context)
    {
        $types = [
            'enrollment' => [
                'extensions' => ['pdf', 'jpg', 'jpeg', 'png'],
                'mime_types' => ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png']
            ],
            'learning_material' => [
                'extensions' => ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'zip'],
                'mime_types' => [
                    'application/pdf',
                    'application/msword',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'application/vnd.ms-powerpoint',
                    'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                    'application/zip'
                ]
            ],
            'submission' => [
                'extensions' => ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'],
                'mime_types' => [
                    'application/pdf',
                    'application/msword',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'image/jpeg',
                    'image/jpg',
                    'image/png'
                ]
            ]
        ];
        
        return $types[$context] ?? $types['enrollment'];
    }
    
    /**
     * Get maximum file size by context
     * 
     * @param string $context Upload context
     * @return int Maximum file size in bytes
     */
    private function getMaxFileSize($context)
    {
        $sizes = [
            'enrollment' => 5 * 1024 * 1024,      // 5MB
            'learning_material' => 10 * 1024 * 1024, // 10MB
            'submission' => 5 * 1024 * 1024       // 5MB
        ];
        
        return $sizes[$context] ?? $sizes['enrollment'];
    }
    
    /**
     * Get MIME type of file
     * 
     * @param string $filePath Path to file
     * @return string MIME type
     */
    private function getMimeType($filePath)
    {
        if (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $filePath);
            finfo_close($finfo);
            return $mimeType;
        }
        
        // Fallback to mime_content_type if available
        if (function_exists('mime_content_type')) {
            return mime_content_type($filePath);
        }
        
        // Last resort: guess from extension
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        $mimeTypes = [
            'pdf' => 'application/pdf',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'ppt' => 'application/vnd.ms-powerpoint',
            'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'zip' => 'application/zip'
        ];
        
        return $mimeTypes[$extension] ?? 'application/octet-stream';
    }
    
    /**
     * Perform security checks on uploaded file
     * 
     * @param array $file File information
     * @param int|null $userId User ID for logging
     * @return array Security check result
     */
    private function performSecurityChecks($file, $userId = null)
    {
        $errors = [];
        
        // Check for executable file extensions in filename
        $dangerousExtensions = ['exe', 'bat', 'cmd', 'com', 'pif', 'scr', 'vbs', 'js', 'jar', 'php', 'asp', 'jsp'];
        $filename = strtolower($file['name']);
        
        foreach ($dangerousExtensions as $ext) {
            if (strpos($filename, '.' . $ext) !== false) {
                $errors[] = "File contains potentially dangerous extension: .$ext";
                
                // Log security incident
                if ($userId) {
                    $this->auditLog->logError(
                        'file_upload',
                        'high',
                        "Dangerous file upload attempt: {$file['name']}",
                        null,
                        ['filename' => $file['name'], 'user_id' => $userId],
                        $userId
                    );
                }
                break;
            }
        }
        
        // Check for null bytes in filename (path traversal attempt)
        if (strpos($file['name'], "\0") !== false) {
            $errors[] = "Invalid characters in filename";
            
            if ($userId) {
                $this->auditLog->logError(
                    'file_upload',
                    'critical',
                    "Null byte injection attempt in filename: {$file['name']}",
                    null,
                    ['filename' => $file['name'], 'user_id' => $userId],
                    $userId
                );
            }
        }
        
        // Check for path traversal attempts
        if (strpos($file['name'], '../') !== false || strpos($file['name'], '..\\') !== false) {
            $errors[] = "Invalid path in filename";
            
            if ($userId) {
                $this->auditLog->logError(
                    'file_upload',
                    'critical',
                    "Path traversal attempt in filename: {$file['name']}",
                    null,
                    ['filename' => $file['name'], 'user_id' => $userId],
                    $userId
                );
            }
        }
        
        // Check file header for magic bytes (basic file type verification)
        $headerCheck = $this->validateFileHeader($file['tmp_name'], $file['name']);
        if (!$headerCheck['valid']) {
            $errors[] = $headerCheck['error'];
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }
    
    /**
     * Basic malware scanning using file signatures and heuristics
     * 
     * @param string $filePath Path to uploaded file
     * @param int|null $userId User ID for logging
     * @return array Scan result
     */
    private function scanForMalware($filePath, $userId = null)
    {
        $errors = [];
        
        // Read first 1024 bytes for signature scanning
        $handle = fopen($filePath, 'rb');
        if (!$handle) {
            $errors[] = "Unable to scan file for security threats";
            return ['valid' => false, 'errors' => $errors];
        }
        
        $header = fread($handle, 1024);
        fclose($handle);
        
        // Check for suspicious patterns
        $suspiciousPatterns = [
            '/eval\s*\(/i',                    // PHP eval
            '/<script[^>]*>/i',                // Script tags
            '/javascript:/i',                  // JavaScript protocol
            '/vbscript:/i',                    // VBScript protocol
            '/on\w+\s*=/i',                   // Event handlers
            '/\$_(?:GET|POST|REQUEST|COOKIE)/i', // PHP superglobals
            '/exec\s*\(/i',                    // Command execution
            '/system\s*\(/i',                  // System calls
            '/shell_exec\s*\(/i',              // Shell execution
            '/passthru\s*\(/i',                // Passthru execution
        ];
        
        foreach ($suspiciousPatterns as $pattern) {
            if (preg_match($pattern, $header)) {
                $errors[] = "File contains potentially malicious content";
                
                if ($userId) {
                    $this->auditLog->logError(
                        'file_upload',
                        'critical',
                        "Malicious content detected in uploaded file",
                        null,
                        ['pattern' => $pattern, 'user_id' => $userId],
                        $userId
                    );
                }
                break;
            }
        }
        
        // Check file size vs. declared size (zip bomb detection)
        $actualSize = filesize($filePath);
        if ($actualSize > 50 * 1024 * 1024) { // 50MB limit for any file
            $errors[] = "File size exceeds security limits";
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }
    
    /**
     * Validate file header matches expected file type
     * 
     * @param string $filePath Path to file
     * @param string $filename Original filename
     * @return array Validation result
     */
    private function validateFileHeader($filePath, $filename)
    {
        $handle = fopen($filePath, 'rb');
        if (!$handle) {
            return ['valid' => false, 'error' => 'Unable to read file header'];
        }
        
        $header = fread($handle, 16);
        fclose($handle);
        
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        // File signatures (magic numbers)
        $signatures = [
            'pdf' => ['%PDF'],
            'jpg' => ["\xFF\xD8\xFF"],
            'jpeg' => ["\xFF\xD8\xFF"],
            'png' => ["\x89PNG\r\n\x1A\n"],
            'zip' => ["PK\x03\x04", "PK\x05\x06", "PK\x07\x08"],
            'doc' => ["\xD0\xCF\x11\xE0\xA1\xB1\x1A\xE1"],
            'docx' => ["PK\x03\x04"], // DOCX is a ZIP file
            'ppt' => ["\xD0\xCF\x11\xE0\xA1\xB1\x1A\xE1"],
            'pptx' => ["PK\x03\x04"]  // PPTX is a ZIP file
        ];
        
        if (!isset($signatures[$extension])) {
            return ['valid' => true]; // Unknown extension, skip header check
        }
        
        foreach ($signatures[$extension] as $signature) {
            if (strpos($header, $signature) === 0) {
                return ['valid' => true];
            }
        }
        
        return [
            'valid' => false,
            'error' => "File header does not match expected file type for .$extension"
        ];
    }
    
    /**
     * Get upload error message
     * 
     * @param int $errorCode PHP upload error code
     * @return string Error message
     */
    private function getUploadErrorMessage($errorCode)
    {
        $messages = [
            UPLOAD_ERR_OK => 'No error',
            UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize directive',
            UPLOAD_ERR_FORM_SIZE => 'File exceeds MAX_FILE_SIZE directive',
            UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
            UPLOAD_ERR_EXTENSION => 'File upload stopped by extension'
        ];
        
        return $messages[$errorCode] ?? 'Unknown upload error';
    }
    
    /**
     * Format file size for display
     * 
     * @param int $bytes File size in bytes
     * @return string Formatted file size
     */
    private function formatFileSize($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= (1 << (10 * $pow));
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }
}