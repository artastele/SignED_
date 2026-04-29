<?php
/**
 * Direct Assessment Test
 * Tests if we can access AssessmentController directly
 */

// Use SAME session settings as index.php
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/SignED_/',
    'domain' => '',
    'secure' => false,
    'httponly' => true,
    'samesite' => 'Lax'
]);

session_start();

require_once '../config/config.php';
require_once '../config/database.php';
require_once '../core/Controller.php';
require_once '../core/Model.php';

echo "<h1>Direct Assessment Controller Test</h1>";
echo "<hr>";

echo "<h2>1. Session Check</h2>";
echo "<pre>";
echo "Session ID: " . session_id() . "\n";
echo "User ID: " . (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'NOT SET') . "\n";
echo "Role: " . (isset($_SESSION['role']) ? $_SESSION['role'] : 'NOT SET') . "\n";
echo "Full Name: " . (isset($_SESSION['fullname']) ? $_SESSION['fullname'] : 'NOT SET') . "\n";
echo "</pre>";

if (!isset($_SESSION['user_id'])) {
    echo "<p style='color: red;'>❌ NOT LOGGED IN - Cannot test controller</p>";
    echo "<p><a href='" . URLROOT . "/auth/login'>Login here</a></p>";
    exit;
}

if ($_SESSION['role'] !== 'parent') {
    echo "<p style='color: red;'>❌ NOT A PARENT - Cannot test assessment</p>";
    exit;
}

echo "<p style='color: green;'>✅ Session is valid</p>";

echo "<hr>";
echo "<h2>2. Load AssessmentController</h2>";

try {
    require_once '../app/controllers/AssessmentController.php';
    echo "<p style='color: green;'>✅ AssessmentController loaded</p>";
    
    echo "<hr>";
    echo "<h2>3. Test requireParent() Method</h2>";
    
    $controller = new AssessmentController();
    echo "<p>Calling requireParent()...</p>";
    
    // This should NOT redirect if session is valid
    $controller->requireParent();
    
    echo "<p style='color: green;'>✅ requireParent() passed! No redirect happened.</p>";
    
    echo "<hr>";
    echo "<h2>4. Test index() Method</h2>";
    echo "<p>If this works, you should see the assessment page below:</p>";
    echo "<hr>";
    
    // Call the index method
    $controller->index();
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>
