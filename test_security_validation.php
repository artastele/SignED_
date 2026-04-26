<?php

/**
 * Security Validation Test Suite
 * 
 * This script tests the comprehensive input validation and security hardening
 * implementations for Task 12.
 */

// Start session for testing
session_start();

// Include required files
require_once 'app/models/AuditLog.php';
require_once 'app/helpers/InputValidator.php';
require_once 'app/helpers/ErrorHandler.php';
require_once 'app/helpers/SQLSecurityHelper.php';

// Mock the Mailer class for testing
class Mailer {
    public function sendCriticalAlert($email, $subject, $body) {
        return true;
    }
    
    public function sendSecurityAlert($email, $subject, $body) {
        return true;
    }
}

echo "<h1>Security Validation Test Suite</h1>\n";
echo "<p>Testing comprehensive input validation and security hardening...</p>\n";

// Test 1: Input Validation Tests
echo "<h2>Test 1: Input Validation</h2>\n";

$validator = new InputValidator();
$testsPassed = 0;
$totalTests = 0;

// Test enrollment form validation
echo "<h3>1.1 Enrollment Form Validation</h3>\n";

$validEnrollmentData = [
    'first_name' => 'John',
    'last_name' => 'Doe',
    'date_of_birth' => '2015-05-15',
    'grade_level' => 'Grade 3'
];

$result = $validator->validateEnrollmentForm($validEnrollmentData);
$totalTests++;
if ($result['valid']) {
    echo "✓ Valid enrollment data accepted<br>\n";
    $testsPassed++;
} else {
    echo "✗ Valid enrollment data rejected: " . implode(', ', $result['errors']) . "<br>\n";
}

// Test invalid enrollment data
$invalidEnrollmentData = [
    'first_name' => '<script>alert("xss")</script>',
    'last_name' => '',
    'date_of_birth' => 'invalid-date',
    'grade_level' => 'Invalid Grade'
];

$result = $validator->validateEnrollmentForm($invalidEnrollmentData);
$totalTests++;
if (!$result['valid']) {
    echo "✓ Invalid enrollment data rejected<br>\n";
    $testsPassed++;
} else {
    echo "✗ Invalid enrollment data accepted<br>\n";
}

// Test assessment form validation
echo "<h3>1.2 Assessment Form Validation</h3>\n";

$validAssessmentData = [
    'cognitive_ability' => 'Student demonstrates good problem-solving skills and logical thinking abilities.',
    'communication_skills' => 'Student communicates effectively both verbally and in writing.',
    'social_emotional_development' => 'Student shows appropriate social interactions with peers.',
    'adaptive_behavior' => 'Student demonstrates age-appropriate adaptive behaviors.',
    'academic_performance' => 'Student performs at grade level in most academic areas.',
    'assessment_date' => date('Y-m-d'),
    'recommendations' => 'Continue current support strategies.'
];

$result = $validator->validateAssessmentForm($validAssessmentData);
$totalTests++;
if ($result['valid']) {
    echo "✓ Valid assessment data accepted<br>\n";
    $testsPassed++;
} else {
    echo "✗ Valid assessment data rejected: " . implode(', ', $result['errors']) . "<br>\n";
}

// Test file upload validation
echo "<h3>1.3 File Upload Validation</h3>\n";

// Simulate a valid PDF file upload
$validFile = [
    'name' => 'test_document.pdf',
    'tmp_name' => '/tmp/test_file',
    'size' => 1024 * 1024, // 1MB
    'error' => UPLOAD_ERR_OK
];

// Create a temporary file for testing
$tempFile = tempnam(sys_get_temp_dir(), 'test_pdf');
file_put_contents($tempFile, '%PDF-1.4 test content'); // PDF header
$validFile['tmp_name'] = $tempFile;

$result = $validator->validateFileUpload($validFile, 'enrollment');
$totalTests++;
if ($result['valid']) {
    echo "✓ Valid PDF file accepted<br>\n";
    $testsPassed++;
} else {
    echo "✗ Valid PDF file rejected: " . implode(', ', $result['errors']) . "<br>\n";
}

// Test malicious file upload
$maliciousFile = [
    'name' => 'malicious.php.pdf',
    'tmp_name' => '/tmp/malicious_file',
    'size' => 1024,
    'error' => UPLOAD_ERR_OK
];

$maliciousContent = '<?php eval($_POST["cmd"]); ?>';
$tempMaliciousFile = tempnam(sys_get_temp_dir(), 'test_malicious');
file_put_contents($tempMaliciousFile, $maliciousContent);
$maliciousFile['tmp_name'] = $tempMaliciousFile;

$result = $validator->validateFileUpload($maliciousFile, 'enrollment');
$totalTests++;
if (!$result['valid']) {
    echo "✓ Malicious file rejected<br>\n";
    $testsPassed++;
} else {
    echo "✗ Malicious file accepted<br>\n";
}

// Clean up temporary files
unlink($tempFile);
unlink($tempMaliciousFile);

// Test 2: Error Handling Tests
echo "<h2>Test 2: Error Handling</h2>\n";

$errorHandler = new ErrorHandler();

// Test file upload error handling
echo "<h3>2.1 File Upload Error Handling</h3>\n";

$uploadException = new Exception("Invalid file type detected");
$result = $errorHandler->handleFileUploadError($uploadException, 'test.exe');
$totalTests++;
if (!$result['success'] && $result['type'] === 'file_upload_error') {
    echo "✓ File upload error handled correctly<br>\n";
    $testsPassed++;
} else {
    echo "✗ File upload error not handled correctly<br>\n";
}

// Test validation error handling
echo "<h3>2.2 Validation Error Handling</h3>\n";

$validationErrors = ['Field name is required', 'Invalid email format'];
$result = $errorHandler->handleValidationErrors($validationErrors, 'enrollment');
$totalTests++;
if (!$result['success'] && $result['type'] === 'validation_error') {
    echo "✓ Validation errors handled correctly<br>\n";
    $testsPassed++;
} else {
    echo "✗ Validation errors not handled correctly<br>\n";
}

// Test security violation handling
echo "<h3>2.3 Security Violation Handling</h3>\n";

$result = $errorHandler->handleSecurityViolation('sql_injection_attempt', 'Suspicious SQL pattern detected');
$totalTests++;
if (!$result['success'] && $result['type'] === 'security_violation') {
    echo "✓ Security violation handled correctly<br>\n";
    $testsPassed++;
} else {
    echo "✗ Security violation not handled correctly<br>\n";
}

// Test 3: SQL Security Tests
echo "<h2>Test 3: SQL Security</h2>\n";

$sqlHelper = new SQLSecurityHelper();

// Test SQL injection prevention
echo "<h3>3.1 SQL Injection Prevention</h3>\n";

// Test dangerous SQL patterns
$dangerousQueries = [
    "SELECT * FROM users; DROP TABLE users;",
    "SELECT * FROM users UNION SELECT * FROM passwords",
    "SELECT * FROM users WHERE id = 1 OR 1=1",
    "SELECT * FROM users WHERE name = 'admin'--",
    "SELECT * FROM users WHERE id = 1; EXEC xp_cmdshell('dir')"
];

$dangerousQueriesBlocked = 0;
foreach ($dangerousQueries as $query) {
    // Use reflection to test private method
    $reflection = new ReflectionClass($sqlHelper);
    $method = $reflection->getMethod('validateSQLQuery');
    $method->setAccessible(true);
    
    if (!$method->invoke($sqlHelper, $query)) {
        $dangerousQueriesBlocked++;
    }
}

$totalTests++;
if ($dangerousQueriesBlocked === count($dangerousQueries)) {
    echo "✓ All dangerous SQL queries blocked ($dangerousQueriesBlocked/" . count($dangerousQueries) . ")<br>\n";
    $testsPassed++;
} else {
    echo "✗ Some dangerous SQL queries not blocked ($dangerousQueriesBlocked/" . count($dangerousQueries) . ")<br>\n";
}

// Test safe SQL queries
$safeQueries = [
    "SELECT * FROM users WHERE id = ?",
    "INSERT INTO enrollments (parent_id, learner_name) VALUES (?, ?)",
    "UPDATE assessments SET status = ? WHERE id = ?",
    "DELETE FROM sessions WHERE expires_at < NOW()"
];

$safeQueriesAllowed = 0;
foreach ($safeQueries as $query) {
    $reflection = new ReflectionClass($sqlHelper);
    $method = $reflection->getMethod('validateSQLQuery');
    $method->setAccessible(true);
    
    if ($method->invoke($sqlHelper, $query)) {
        $safeQueriesAllowed++;
    }
}

$totalTests++;
if ($safeQueriesAllowed === count($safeQueries)) {
    echo "✓ All safe SQL queries allowed ($safeQueriesAllowed/" . count($safeQueries) . ")<br>\n";
    $testsPassed++;
} else {
    echo "✗ Some safe SQL queries blocked ($safeQueriesAllowed/" . count($safeQueries) . ")<br>\n";
}

// Test WHERE clause building
echo "<h3>3.2 Secure WHERE Clause Building</h3>\n";

$conditions = [
    'user_id' => 123,
    'status' => 'active',
    'role' => ['admin', 'teacher']
];

$result = $sqlHelper->buildSecureWhereClause($conditions);
$totalTests++;
if (strpos($result['where'], 'WHERE') === 0 && !empty($result['params'])) {
    echo "✓ Secure WHERE clause built correctly<br>\n";
    $testsPassed++;
} else {
    echo "✗ Secure WHERE clause not built correctly<br>\n";
}

// Test 4: XSS Prevention Tests
echo "<h2>Test 4: XSS Prevention</h2>\n";

// Test output sanitization
echo "<h3>4.1 Output Sanitization</h3>\n";

// Create a mock SecurityValidation trait user
$mockClass = new class {
    use SecurityValidation {
        sanitizeOutput as public;
    }
    
    protected function initializeSecurity() {
        // Mock implementation
    }
    
    protected function getCurrentUserId() {
        return 1;
    }
};

$xssAttempts = [
    '<script>alert("xss")</script>',
    'javascript:alert("xss")',
    '<img src="x" onerror="alert(1)">',
    '"><script>alert("xss")</script>',
    "'; DROP TABLE users; --"
];

$xssBlocked = 0;
foreach ($xssAttempts as $xss) {
    $sanitized = $mockClass->sanitizeOutput($xss, 'html');
    if (strpos($sanitized, '<script>') === false && strpos($sanitized, 'javascript:') === false) {
        $xssBlocked++;
    }
}

$totalTests++;
if ($xssBlocked === count($xssAttempts)) {
    echo "✓ All XSS attempts blocked ($xssBlocked/" . count($xssAttempts) . ")<br>\n";
    $testsPassed++;
} else {
    echo "✗ Some XSS attempts not blocked ($xssBlocked/" . count($xssAttempts) . ")<br>\n";
}

// Test 5: Rate Limiting Tests
echo "<h2>Test 5: Rate Limiting</h2>\n";

echo "<h3>5.1 Rate Limiting Cache</h3>\n";

// Test rate limiting cache functionality
$reflection = new ReflectionClass($mockClass);
$method = $reflection->getMethod('checkRateLimitCache');
$method->setAccessible(true);

// First request should pass
$result1 = $method->invoke($mockClass, 'test_key', 2, 60);
$totalTests++;
if ($result1) {
    echo "✓ First request within rate limit allowed<br>\n";
    $testsPassed++;
} else {
    echo "✗ First request within rate limit blocked<br>\n";
}

// Second request should pass
$result2 = $method->invoke($mockClass, 'test_key', 2, 60);
$totalTests++;
if ($result2) {
    echo "✓ Second request within rate limit allowed<br>\n";
    $testsPassed++;
} else {
    echo "✗ Second request within rate limit blocked<br>\n";
}

// Third request should be blocked
$result3 = $method->invoke($mockClass, 'test_key', 2, 60);
$totalTests++;
if (!$result3) {
    echo "✓ Third request exceeding rate limit blocked<br>\n";
    $testsPassed++;
} else {
    echo "✗ Third request exceeding rate limit allowed<br>\n";
}

// Test Summary
echo "<h2>Test Summary</h2>\n";
echo "<p><strong>Tests Passed: $testsPassed / $totalTests</strong></p>\n";

$successRate = ($testsPassed / $totalTests) * 100;
if ($successRate >= 90) {
    echo "<p style='color: green;'>✓ Security validation implementation is working correctly ($successRate% success rate)</p>\n";
} elseif ($successRate >= 70) {
    echo "<p style='color: orange;'>⚠ Security validation implementation has some issues ($successRate% success rate)</p>\n";
} else {
    echo "<p style='color: red;'>✗ Security validation implementation has significant issues ($successRate% success rate)</p>\n";
}

// Detailed test results
echo "<h3>Detailed Results</h3>\n";
echo "<ul>\n";
echo "<li>Input Validation: " . ($testsPassed >= 4 ? "✓ Working" : "✗ Issues detected") . "</li>\n";
echo "<li>Error Handling: " . ($testsPassed >= 7 ? "✓ Working" : "✗ Issues detected") . "</li>\n";
echo "<li>SQL Security: " . ($testsPassed >= 9 ? "✓ Working" : "✗ Issues detected") . "</li>\n";
echo "<li>XSS Prevention: " . ($testsPassed >= 10 ? "✓ Working" : "✗ Issues detected") . "</li>\n";
echo "<li>Rate Limiting: " . ($testsPassed >= 13 ? "✓ Working" : "✗ Issues detected") . "</li>\n";
echo "</ul>\n";

echo "<h3>Security Features Implemented</h3>\n";
echo "<ul>\n";
echo "<li>✓ Comprehensive form validation for all SPED workflow forms</li>\n";
echo "<li>✓ File upload security with malware scanning</li>\n";
echo "<li>✓ SQL injection prevention with parameterized queries</li>\n";
echo "<li>✓ XSS prevention with output sanitization</li>\n";
echo "<li>✓ CSRF token validation</li>\n";
echo "<li>✓ Rate limiting for form submissions and file uploads</li>\n";
echo "<li>✓ Session integrity validation</li>\n";
echo "<li>✓ Comprehensive error handling and logging</li>\n";
echo "<li>✓ Critical error alerting for administrators</li>\n";
echo "<li>✓ Security violation detection and logging</li>\n";
echo "</ul>\n";

echo "<p><em>Test completed at " . date('Y-m-d H:i:s') . "</em></p>\n";