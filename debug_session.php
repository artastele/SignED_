<?php
// Debug session for assessment issue
session_start();

echo "<h2>Session Debug Information</h2>";
echo "<pre>";
echo "Session ID: " . session_id() . "\n\n";
echo "Session Data:\n";
print_r($_SESSION);
echo "\n\n";

echo "Current URL: " . $_SERVER['REQUEST_URI'] . "\n";
echo "HTTP Host: " . $_SERVER['HTTP_HOST'] . "\n";

if (isset($_SESSION['user_id'])) {
    echo "\n✅ User is logged in\n";
    echo "User ID: " . $_SESSION['user_id'] . "\n";
    echo "Role: " . ($_SESSION['role'] ?? 'NOT SET') . "\n";
    echo "Full Name: " . ($_SESSION['fullname'] ?? 'NOT SET') . "\n";
    
    if ($_SESSION['role'] === 'parent') {
        echo "\n✅ User is a PARENT - Can access assessment\n";
    } else {
        echo "\n❌ User is NOT a parent - Cannot access assessment\n";
    }
} else {
    echo "\n❌ User is NOT logged in\n";
}

echo "</pre>";

echo "<hr>";
echo "<h3>Test Links:</h3>";
echo '<a href="/SignED_/assessment">Go to Assessment Page</a><br>';
echo '<a href="/SignED_/parent/dashboard">Go to Parent Dashboard</a><br>';
echo '<a href="/SignED_/auth/login">Go to Login</a><br>';
?>
