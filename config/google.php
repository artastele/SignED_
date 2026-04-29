<?php

// Load environment variables
require_once __DIR__ . '/env.php';

try {
    Env::load();
} catch (Exception $e) {
    // If .env doesn't exist, use defaults
}

// Google OAuth Configuration
define('GOOGLE_CLIENT_ID', Env::get('GOOGLE_CLIENT_ID', ''));
define('GOOGLE_CLIENT_SECRET', Env::get('GOOGLE_CLIENT_SECRET', ''));
define('GOOGLE_REDIRECT_URI', Env::get('GOOGLE_REDIRECT_URI', URLROOT . '/auth/googleCallback'));

// Google OAuth URLs
define('GOOGLE_AUTH_URL', 'https://accounts.google.com/o/oauth2/v2/auth');
define('GOOGLE_TOKEN_URL', 'https://oauth2.googleapis.com/token');
define('GOOGLE_USER_INFO_URL', 'https://www.googleapis.com/oauth2/v2/userinfo');
define('GOOGLE_SCOPES', 'https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/userinfo.profile');
