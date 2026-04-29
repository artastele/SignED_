<?php
// Super simple debug - no fancy stuff
session_start();

echo "<h1>Simple Debug</h1>";
echo "<pre>";

echo "=== SESSION DATA ===\n";
print_r($_SESSION);

echo "\n=== COOKIE DATA ===\n";
print_r($_COOKIE);

echo "\n=== SESSION PARAMS ===\n";
print_r(session_get_cookie_params());

echo "\n=== SERVER INFO ===\n";
echo "REQUEST_URI: " . $_SERVER['REQUEST_URI'] . "\n";
echo "SCRIPT_NAME: " . $_SERVER['SCRIPT_NAME'] . "\n";
echo "PHP_SELF: " . $_SERVER['PHP_SELF'] . "\n";

echo "</pre>";

echo "<hr>";
echo "<h2>Test Link</h2>";
echo "<a href='/SignED_/assessment'>Click here to go to assessment</a>";
?>
