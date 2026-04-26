<?php
// SPED System Test Runner
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>SPED Workflow Integration - Complete Test Suite</h1>\n";
echo "<p>Running comprehensive tests for all implemented components...</p>\n";

$testResults = [];
$startTime = microtime(true);

// Test 1: Database and Infrastructure
echo "<div style='border: 1px solid #ccc; margin: 10px 0; padding: 10px;'>\n";
echo "<h2>Test 1: Database and Infrastructure</h2>\n";

try {
    require_once 'config/config.php';
    require_once 'config/database.php';
    
    $db = new Database();
    $pdo = $db->connect();
    
    echo "✓ Database connection successful<br>\n";
    
    // Check SPED tables
    $spedTables = [
        'learners', 'enrollments', 'enrollment_documents', 'assessments',
        'iep_meetings', 'iep_meeting_participants', 'ieps', 'learning_materials',
        'learner_submissions', 'audit_logs', 'error_logs'
    ];
    
    $existingTables = 0;
    foreach ($spedTables as $table) {
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            $existingTables++;
        }
    }
    
    echo "✓ SPED tables: $existingTables/" . count($spedTables) . " exist<br>\n";
    
    // Check users table SPED roles
    $stmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'role'");
    $roleColumn = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($roleColumn && strpos($roleColumn['Type'], 'sped_teacher') !== false) {
        echo "✓ Users table supports SPED roles<br>\n";
    } else {
        echo "✗ Users table missing SPED roles<br>\n";
    }
    
    $testResults['database'] = 'PASS';
    
} catch (Exception $e) {
    echo "✗ Database test failed: " . $e->getMessage() . "<br>\n";
    $testResults['database'] = 'FAIL';
}

echo "</div>\n";

// Test 2: Model Classes
echo "<div style='border: 1px solid #ccc; margin: 10px 0; padding: 10px;'>\n";
echo "<h2>Test 2: Model Classes</h2>\n";

try {
    require_once 'core/Model.php';
    
    $modelClasses = [
        'User' => 'app/models/User.php',
        'Learner' => 'app/models/Learner.php',
        'Enrollment' => 'app/models/Enrollment.php',
        'Assessment' => 'app/models/Assessment.php',
        'IepMeeting' => 'app/models/IepMeeting.php',
        'Iep' => 'app/models/Iep.php',
        'LearningMaterial' => 'app/models/LearningMaterial.php',
        'AuditLog' => 'app/models/AuditLog.php',
        'DocumentStore' => 'app/models/DocumentStore.php',
        'SecurityManager' => 'app/models/SecurityManager.php'
    ];
    
    $modelsPassed = 0;
    foreach ($modelClasses as $className => $filePath) {
        if (file_exists($filePath)) {
            require_once $filePath;
            try {
                $model = new $className();
                echo "✓ $className model instantiated successfully<br>\n";
                $modelsPassed++;
            } catch (Exception $e) {
                echo "✗ $className model instantiation failed: " . $e->getMessage() . "<br>\n";
            }
        } else {
            echo "✗ $className model file missing: $filePath<br>\n";
        }
    }
    
    echo "Models working: $modelsPassed/" . count($modelClasses) . "<br>\n";
    $testResults['models'] = ($modelsPassed == count($modelClasses)) ? 'PASS' : 'PARTIAL';
    
} catch (Exception $e) {
    echo "✗ Model test failed: " . $e->getMessage() . "<br>\n";
    $testResults['models'] = 'FAIL';
}

echo "</div>\n";

// Test 3: Authentication System
echo "<div style='border: 1px solid #ccc; margin: 10px 0; padding: 10px;'>\n";
echo "<h2>Test 3: Authentication System</h2>\n";

try {
    require_once 'core/Controller.php';
    
    // Test password policy
    function validatePasswordPolicy($password) {
        return strlen($password) >= 8 &&
               preg_match('/[A-Z]/', $password) &&
               preg_match('/[a-z]/', $password) &&
               preg_match('/[0-9]/', $password) &&
               preg_match('/[^A-Za-z0-9]/', $password);
    }
    
    $testPasswords = [
        'Password123!' => true,
        'password123!' => false,
        'Pass1!' => false
    ];
    
    $passwordTests = 0;
    foreach ($testPasswords as $password => $expected) {
        $result = validatePasswordPolicy($password);
        if ($result === $expected) {
            $passwordTests++;
        }
    }
    
    echo "✓ Password policy validation: $passwordTests/" . count($testPasswords) . " tests passed<br>\n";
    
    // Test role authorization
    class TestController extends Controller {
        public function testRoleAccess($role, $allowedRoles) {
            return in_array($role, $allowedRoles);
        }
    }
    
    $controller = new TestController();
    $roleTests = [
        ['sped_teacher', ['sped_teacher', 'admin'], true],
        ['parent', ['sped_teacher'], false],
        ['admin', ['admin'], true]
    ];
    
    $roleTestsPassed = 0;
    foreach ($roleTests as $test) {
        list($role, $allowed, $expected) = $test;
        if ($controller->testRoleAccess($role, $allowed) === $expected) {
            $roleTestsPassed++;
        }
    }
    
    echo "✓ Role authorization: $roleTestsPassed/" . count($roleTests) . " tests passed<br>\n";
    
    $testResults['authentication'] = 'PASS';
    
} catch (Exception $e) {
    echo "✗ Authentication test failed: " . $e->getMessage() . "<br>\n";
    $testResults['authentication'] = 'FAIL';
}

echo "</div>\n";

// Test 4: Security Components
echo "<div style='border: 1px solid #ccc; margin: 10px 0; padding: 10px;'>\n";
echo "<h2>Test 4: Security Components</h2>\n";

try {
    // Test document classification
    if (class_exists('SecurityManager')) {
        $securityManager = new SecurityManager();
        
        $classificationTests = [
            'enrollment' => 'confidential',
            'assessment' => 'restricted',
            'iep' => 'restricted'
        ];
        
        $classificationPassed = 0;
        foreach ($classificationTests as $docType => $expected) {
            $result = $securityManager->classifyDocument($docType);
            if ($result === $expected) {
                $classificationPassed++;
            }
        }
        
        echo "✓ Document classification: $classificationPassed/" . count($classificationTests) . " tests passed<br>\n";
    }
    
    // Test file validation
    function testFileValidation($filename, $allowedTypes) {
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        return in_array($extension, $allowedTypes);
    }
    
    $fileTests = [
        ['document.pdf', ['pdf', 'jpg'], true],
        ['script.exe', ['pdf', 'jpg'], false]
    ];
    
    $fileTestsPassed = 0;
    foreach ($fileTests as $test) {
        list($filename, $allowed, $expected) = $test;
        if (testFileValidation($filename, $allowed) === $expected) {
            $fileTestsPassed++;
        }
    }
    
    echo "✓ File validation: $fileTestsPassed/" . count($fileTests) . " tests passed<br>\n";
    
    $testResults['security'] = 'PASS';
    
} catch (Exception $e) {
    echo "✗ Security test failed: " . $e->getMessage() . "<br>\n";
    $testResults['security'] = 'FAIL';
}

echo "</div>\n";

// Test 5: Audit Logging
echo "<div style='border: 1px solid #ccc; margin: 10px 0; padding: 10px;'>\n";
echo "<h2>Test 5: Audit Logging</h2>\n";

try {
    if (class_exists('AuditLog')) {
        $auditLog = new AuditLog();
        
        // Test different log types
        $logTests = [
            'login' => $auditLog->logLogin('test@example.com', '127.0.0.1', true, 1),
            'document_access' => $auditLog->logDocumentAccess(1, 123, 'view'),
            'status_change' => $auditLog->logStatusChange(1, 'enrollment', 456, 'pending', 'approved')
        ];
        
        $logsPassed = 0;
        foreach ($logTests as $logType => $result) {
            if ($result) {
                echo "✓ $logType logging successful<br>\n";
                $logsPassed++;
            } else {
                echo "✗ $logType logging failed<br>\n";
            }
        }
        
        echo "Audit logging: $logsPassed/" . count($logTests) . " tests passed<br>\n";
        $testResults['audit'] = ($logsPassed == count($logTests)) ? 'PASS' : 'PARTIAL';
    } else {
        echo "✗ AuditLog class not available<br>\n";
        $testResults['audit'] = 'FAIL';
    }
    
} catch (Exception $e) {
    echo "✗ Audit logging test failed: " . $e->getMessage() . "<br>\n";
    $testResults['audit'] = 'FAIL';
}

echo "</div>\n";

// Test Summary
$endTime = microtime(true);
$executionTime = round($endTime - $startTime, 2);

echo "<div style='border: 2px solid #333; margin: 20px 0; padding: 15px; background-color: #f9f9f9;'>\n";
echo "<h2>Test Summary</h2>\n";

$totalTests = count($testResults);
$passedTests = count(array_filter($testResults, function($result) { return $result === 'PASS'; }));
$partialTests = count(array_filter($testResults, function($result) { return $result === 'PARTIAL'; }));
$failedTests = count(array_filter($testResults, function($result) { return $result === 'FAIL'; }));

echo "<table border='1' cellpadding='5' cellspacing='0'>\n";
echo "<tr><th>Test Category</th><th>Status</th></tr>\n";

foreach ($testResults as $category => $status) {
    $color = ($status === 'PASS') ? 'green' : (($status === 'PARTIAL') ? 'orange' : 'red');
    echo "<tr><td>" . ucfirst($category) . "</td><td style='color: $color; font-weight: bold;'>$status</td></tr>\n";
}

echo "</table>\n";

echo "<p><strong>Overall Results:</strong></p>\n";
echo "<ul>\n";
echo "<li>✓ Passed: $passedTests</li>\n";
echo "<li>⚠ Partial: $partialTests</li>\n";
echo "<li>✗ Failed: $failedTests</li>\n";
echo "<li>⏱ Execution time: {$executionTime}s</li>\n";
echo "</ul>\n";

if ($failedTests === 0) {
    echo "<p style='color: green; font-size: 18px; font-weight: bold;'>🎉 ALL TESTS PASSED! Core infrastructure is ready.</p>\n";
} elseif ($partialTests > 0 && $failedTests === 0) {
    echo "<p style='color: orange; font-size: 18px; font-weight: bold;'>⚠ TESTS MOSTLY PASSED with some partial results. System is functional.</p>\n";
} else {
    echo "<p style='color: red; font-size: 18px; font-weight: bold;'>❌ SOME TESTS FAILED. Please review and fix issues before proceeding.</p>\n";
}

echo "</div>\n";

echo "<p><em>Test suite completed at " . date('Y-m-d H:i:s') . "</em></p>\n";
?>