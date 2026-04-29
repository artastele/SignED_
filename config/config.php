<?php

// Load environment variables
require_once __DIR__ . '/env.php';

try {
    Env::load();
} catch (Exception $e) {
    // If .env doesn't exist, use defaults (for backward compatibility)
    // In production, this should throw an error
}

// Application Configuration
define('URLROOT', Env::get('APP_URL', 'http://localhost/SignED_'));
define('ASSETS', URLROOT . '/public/assets');
define('APPROOT', dirname(dirname(__FILE__)));
define('SITENAME', Env::get('APP_NAME', 'SignED'));
define('APP_ENV', Env::get('APP_ENV', 'local'));
define('APP_DEBUG', Env::get('APP_DEBUG', 'true') === 'true');

// SMTP Email Configuration
// SECURITY: Credentials now loaded from .env file
define('SMTP_HOST', Env::get('SMTP_HOST', 'smtp.gmail.com'));
define('SMTP_PORT', Env::get('SMTP_PORT', '587'));
define('SMTP_USERNAME', Env::get('SMTP_USERNAME', ''));
define('SMTP_PASSWORD', Env::get('SMTP_PASSWORD', ''));
define('SMTP_FROM_EMAIL', Env::get('SMTP_FROM_EMAIL', 'noreply@signed.local'));
define('SMTP_FROM_NAME', Env::get('SMTP_FROM_NAME', 'SignED System'));

// Security Settings
define('SESSION_LIFETIME', Env::get('SESSION_LIFETIME', '3600'));
define('SESSION_TIMEOUT', Env::get('SESSION_TIMEOUT', '900'));
define('MAX_LOGIN_ATTEMPTS', Env::get('MAX_LOGIN_ATTEMPTS', '5'));
define('LOCKOUT_DURATION', Env::get('LOCKOUT_DURATION', '1800'));

// File Upload Settings
define('MAX_FILE_SIZE', Env::get('MAX_FILE_SIZE', '10485760')); // 10MB
define('ALLOWED_FILE_TYPES', Env::get('ALLOWED_FILE_TYPES', 'pdf,jpg,jpeg,png,doc,docx'));

// Error Reporting (based on environment)
if (APP_ENV === 'production') {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', APPROOT . '/logs/error.log');
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

