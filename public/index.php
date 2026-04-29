<?php

// Configure session settings before starting
ini_set('session.use_strict_mode', 1);
ini_set('session.gc_maxlifetime', 3600); // 1 hour
ini_set('session.use_cookies', 1);
ini_set('session.use_only_cookies', 1);

// Set session cookie parameters properly
session_set_cookie_params([
    'lifetime' => 0,  // Session cookie expires when browser closes
    'path' => '/SignED_/',  // Fixed: Changed from /SignED_/public/ to /SignED_/
    'domain' => '',  // Empty domain works for localhost
    'secure' => false,  // false for localhost/http
    'httponly' => true,
    'samesite' => 'Lax'  // Prevents CSRF attacks
]);

session_start();

// Regenerate session ID if not set (security measure)
if (!isset($_SESSION['initiated'])) {
    session_regenerate_id(true);
    $_SESSION['initiated'] = true;
}

require_once '../config/config.php';
require_once '../config/database.php';
require_once '../config/google.php';
require_once '../core/App.php';
require_once '../core/Controller.php';
require_once '../core/Model.php';

$app = new App();