<?php
// DATABASE CREATION (MySQL Integration)
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'user_db');

// ========================================
// SECURITY SETTINGS
// ========================================
define('MAX_LOGIN_ATTEMPTS', 3);
define('LOGIN_ATTEMPT_TIMEOUT', 15); // minutes
define('SESSION_TIMEOUT', 30); // minutes
define('PASSWORD_MIN_LENGTH', 8);

// ========================================
//  Login Functionality
// ========================================
define('ITEMS_PER_PAGE', 10);
define('DATE_FORMAT', 'M d, Y');
define('DATETIME_FORMAT', 'M d, Y H:i');

// ========================================
// EMAIL SETTINGS (if using email)
// ========================================
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'your-email@gmail.com');
define('SMTP_PASS', 'your-password');

// ========================================
// ERROR REPORTING
// ========================================
if (getenv('ENV') === 'development') {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
}

// ========================================
// TIMEZONE
// ========================================
date_default_timezone_set('UTC');

// ========================================
// SESSION CONTROL
// ========================================
ini_set('session.gc_maxlifetime', SESSION_TIMEOUT * 60);
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 0); // Set to 1 if using HTTPS
ini_set('session.cookie_samesite', 'Lax');

?>
