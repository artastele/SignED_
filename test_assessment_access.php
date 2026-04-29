<?php
/**
 * Test Assessment Access
 * This script tests if the assessment page can be accessed after the session path fix
 */

// Start session with the FIXED path
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/SignED_/',  // FIXED PATH
    'domain' => '',
    'secure' => false,
    'httponly' => true,
    'samesite' => 'Lax'
]);

session_start();

echo "<h2>Assessment Access Test</h2>";
echo "<hr>";

// Check if logged in
if (!isset($_SESSION['user_id'])) {
    echo "<p style='color: red;'>❌ NOT LOGGED IN</p>";
    echo "<p>Please <a href='/SignED_/auth/login'>login as a parent</a> first.</p>";
    exit;
}

echo "<p style='color: green;'>✅ LOGGED IN</p>";
echo "<p><strong>User ID:</strong> " . $_SESSION['user_id'] . "</p>";
echo "<p><strong>Role:</strong> " . ($_SESSION['role'] ?? 'NOT SET') . "</p>";
echo "<p><strong>Name:</strong> " . ($_SESSION['fullname'] ?? 'NOT SET') . "</p>";

echo "<hr>";

// Check if parent
if ($_SESSION['role'] !== 'parent') {
    echo "<p style='color: red;'>❌ NOT A PARENT</p>";
    echo "<p>You need to be logged in as a parent to access the assessment page.</p>";
    exit;
}

echo "<p style='color: green;'>✅ YOU ARE A PARENT</p>";

echo "<hr>";
echo "<h3>Test Links:</h3>";
echo "<ul>";
echo "<li><a href='/SignED_/assessment' target='_blank'>Open Assessment Page</a></li>";
echo "<li><a href='/SignED_/parent/dashboard' target='_blank'>Open Parent Dashboard</a></li>";
echo "</ul>";

echo "<hr>";
echo "<h3>Session Cookie Info:</h3>";
echo "<pre>";
echo "Cookie Path: /SignED_/ (FIXED)\n";
echo "Session ID: " . session_id() . "\n";
echo "Cookie Params:\n";
print_r(session_get_cookie_params());
echo "</pre>";
?>
