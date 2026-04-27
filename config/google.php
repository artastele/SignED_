<?php

// Google OAuth Configuration
// FIXED: Swapped the Client ID and Secret to correct positions
define('GOOGLE_CLIENT_ID', '262376879974-5pd504lbt1acfppd0j71mo81lhl34jd9.apps.googleusercontent.com');
define('GOOGLE_CLIENT_SECRET', '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');
define('GOOGLE_REDIRECT_URI', URLROOT . '/auth/googleCallback');

// Google OAuth URLs
define('GOOGLE_AUTH_URL', 'https://accounts.google.com/o/oauth2/auth');
define('GOOGLE_TOKEN_URL', 'https://oauth2.googleapis.com/token');
define('GOOGLE_USER_INFO_URL', 'https://www.googleapis.com/oauth2/v2/userinfo');

// OAuth Scopes
define('GOOGLE_SCOPES', 'openid email profile');