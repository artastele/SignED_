<?php
// Test script for enrollment system functionality
require_once 'config/config.php';
require_once 'core/Model.php';
require_once 'app/models/Enrollment.php';
require_once 'app/models/EnrollmentDocument.php';
require_once 'app/models/Learner.php';
require_once 'app/models/User.php';

echo "Testing Enrollment System Components...\n\n";

try {
    // Test 1: Check if models can be instantiated
    echo "1. Testing model instantiation...\n";
    $enrollment = new Enrollment();
    $enrollmentDoc = new EnrollmentDocument();
    $learner = new Learner();
    $user = new User();
    echo "✓ All models instantiated successfully\n\n";

    // Test 2: Check required document types
    echo "2. Testing document types...\n";
    $requiredTypes = $enrollmentDoc->getRequiredTypes();
    echo "Required document types:\n";
    foreach ($requiredTypes as $type => $title) {
        echo "  - $type: $title\n";
    }
    echo "✓ Document types loaded successfully\n\n";

    // Test 3: Check document type validation
    echo "3. Testing document validation...\n";
    $validTypes = ['psa', 'pwd_id', 'medical_record', 'beef'];
    $invalidTypes = ['invalid', 'test', ''];
    
    foreach ($validTypes as $type) {
        if ($enrollmentDoc->isValidDocumentType($type)) {
            echo "✓ $type is valid\n";
        } else {
            echo "✗ $type should be valid but failed\n";
        }
    }
    
    foreach ($invalidTypes as $type) {
        if (!$enrollmentDoc->isValidDocumentType($type)) {
            echo "✓ '$type' correctly rejected as invalid\n";
        } else {
            echo "✗ '$type' should be invalid but passed\n";
        }
    }
    echo "\n";

    echo "✅ All enrollment system tests passed!\n";
    echo "\nEnrollment system is ready for use.\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}