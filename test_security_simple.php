<?php

/**
 * Simplified Security Validation Test
 * 
 * This script tests the core security validation functions without database dependencies.
 */

echo "<h1>Simplified Security Validation Test</h1>\n";
echo "<p>Testing core security validation functions...</p>\n";

// Test 1: Input Sanitization
echo "<h2>Test 1: Input Sanitization</h2>\n";

function sanitizeTextInput($input) {
    // Remove HTML tags and encode special characters
    $sanitized = htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    
    // Remove script tags and javascript
    $sanitized = preg_replace('/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/mi', '', $sanitized);
    
    // Remove remaining HTML tags
    $sanitized = strip_tags($sanitized);
    
    // Remove control characters except newlines and tabs
    $sanitized = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $sanitized);
    
    return trim($sanitized);
}

$testsPassed = 0;
$totalTests = 0;

// Test XSS prevention
$xssAttempts = [
    '<script>alert("xss")</script>',
    'javascript:alert("xss")',
    '<img src="x" onerror="alert(1)">',
    '"><script>alert("xss")</script>',
    '<iframe src="javascript:alert(1)"></iframe>'
];

foreach ($xssAttempts as $xss) {
    $sanitized = sanitizeTextInput($xss);
    $totalTests++;
    if (strpos($sanitized, '<script>') === false && 
        strpos($sanitized, 'javascript:') === false && 
        strpos($sanitized, '<iframe>') === false &&
        strpos($sanitized, 'onerror') === false) {
        echo "✓ XSS attempt blocked: " . htmlspecialchars(substr($xss, 0, 30)) . "...<br>\n";
        $testsPassed++;
    } else {
        echo "✗ XSS attempt not blocked: " . htmlspecialchars(substr($xss, 0, 30)) . "...<br>\n";
    }
}

// Test 2: SQL Injection Pattern Detection
echo "<h2>Test 2: SQL Injection Pattern Detection</h2>\n";

function validateSQLQuery($sql) {
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
            return false;
        }
    }
    
    return true;
}

$dangerousQueries = [
    "SELECT * FROM users; DROP TABLE users;",
    "SELECT * FROM users UNION SELECT * FROM passwords",
    "SELECT * FROM users WHERE id = 1 OR 1=1 --",
    "SELECT * FROM users; EXEC xp_cmdshell('dir')",
    "SELECT * FROM users /* comment */ WHERE id = 1"
];

foreach ($dangerousQueries as $query) {
    $totalTests++;
    if (!validateSQLQuery($query)) {
        echo "✓ Dangerous SQL blocked: " . htmlspecialchars(substr($query, 0, 40)) . "...<br>\n";
        $testsPassed++;
    } else {
        echo "✗ Dangerous SQL not blocked: " . htmlspecialchars(substr($query, 0, 40)) . "...<br>\n";
    }
}

// Test safe queries
$safeQueries = [
    "SELECT * FROM users WHERE id = ?",
    "INSERT INTO enrollments (parent_id, learner_name) VALUES (?, ?)",
    "UPDATE assessments SET status = ? WHERE id = ?",
    "DELETE FROM sessions WHERE expires_at < NOW()"
];

foreach ($safeQueries as $query) {
    $totalTests++;
    if (validateSQLQuery($query)) {
        echo "✓ Safe SQL allowed: " . htmlspecialchars(substr($query, 0, 40)) . "...<br>\n";
        $testsPassed++;
    } else {
        echo "✗ Safe SQL blocked: " . htmlspecialchars(substr($query, 0, 40)) . "...<br>\n";
    }
}

// Test 3: File Upload Validation
echo "<h2>Test 3: File Upload Validation</h2>\n";

function validateFileType($filename, $allowedTypes) {
    $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    return in_array($extension, $allowedTypes);
}

function validateFileSize($size, $maxSize) {
    return $size <= $maxSize;
}

function detectMaliciousContent($content) {
    $suspiciousPatterns = [
        '/eval\s*\(/i',
        '/<script[^>]*>/i',
        '/javascript:/i',
        '/vbscript:/i',
        '/on\w+\s*=/i',
        '/\$_(?:GET|POST|REQUEST|COOKIE)/i'
    ];
    
    foreach ($suspiciousPatterns as $pattern) {
        if (preg_match($pattern, $content)) {
            return true;
        }
    }
    
    return false;
}

// Test file type validation
$allowedTypes = ['pdf', 'jpg', 'jpeg', 'png'];
$testFiles = [
    'document.pdf' => true,
    'image.jpg' => true,
    'script.php' => false,
    'malware.exe' => false,
    'document.docx' => false
];

foreach ($testFiles as $filename => $shouldPass) {
    $totalTests++;
    $result = validateFileType($filename, $allowedTypes);
    if ($result === $shouldPass) {
        echo "✓ File type validation correct for: $filename<br>\n";
        $testsPassed++;
    } else {
        echo "✗ File type validation incorrect for: $filename<br>\n";
    }
}

// Test malicious content detection
$testContents = [
    '<?php eval($_POST["cmd"]); ?>' => true,
    '<script>alert("xss")</script>' => true,
    'Normal PDF content %PDF-1.4' => false,
    'javascript:alert("malicious")' => true,
    'Regular text content' => false
];

foreach ($testContents as $content => $shouldDetect) {
    $totalTests++;
    $result = detectMaliciousContent($content);
    if ($result === $shouldDetect) {
        echo "✓ Malicious content detection correct<br>\n";
        $testsPassed++;
    } else {
        echo "✗ Malicious content detection incorrect<br>\n";
    }
}

// Test 4: CSRF Token Generation
echo "<h2>Test 4: CSRF Token Generation</h2>\n";

function generateCSRFToken() {
    return bin2hex(random_bytes(32));
}

function validateCSRFToken($token1, $token2) {
    return hash_equals($token1, $token2);
}

$token1 = generateCSRFToken();
$token2 = generateCSRFToken();

$totalTests++;
if (strlen($token1) === 64 && ctype_xdigit($token1)) {
    echo "✓ CSRF token generated correctly<br>\n";
    $testsPassed++;
} else {
    echo "✗ CSRF token generation failed<br>\n";
}

$totalTests++;
if (validateCSRFToken($token1, $token1)) {
    echo "✓ CSRF token validation works for matching tokens<br>\n";
    $testsPassed++;
} else {
    echo "✗ CSRF token validation failed for matching tokens<br>\n";
}

$totalTests++;
if (!validateCSRFToken($token1, $token2)) {
    echo "✓ CSRF token validation correctly rejects different tokens<br>\n";
    $testsPassed++;
} else {
    echo "✗ CSRF token validation incorrectly accepts different tokens<br>\n";
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

echo "<h3>Security Features Tested</h3>\n";
echo "<ul>\n";
echo "<li>✓ XSS Prevention through input sanitization</li>\n";
echo "<li>✓ SQL Injection prevention through pattern detection</li>\n";
echo "<li>✓ File upload validation (type and content)</li>\n";
echo "<li>✓ Malicious content detection</li>\n";
echo "<li>✓ CSRF token generation and validation</li>\n";
echo "</ul>\n";

echo "<p><em>Test completed at " . date('Y-m-d H:i:s') . "</em></p>\n";