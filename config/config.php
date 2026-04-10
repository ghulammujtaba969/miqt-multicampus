<?php
/**
 * MIQT System Configuration File
 * Minhaj Institute of Qirat & Tajweed
 */

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'u921830511_miqt');

// Site Configuration
define('SITE_NAME', 'MIQT - Minhaj Institute of Qirat & Tajweed');
// define('SITE_URL', 'https://miqt.edus.app');
define('SITE_URL', 'http://localhost/miqt/02-04-2026-miqt/');
define('BASE_PATH', dirname(dirname(__FILE__)));

// Directory Paths
define('UPLOAD_PATH', BASE_PATH . '/uploads/');
define('PHOTO_PATH', UPLOAD_PATH . 'photos/');
define('EXCEL_PATH', UPLOAD_PATH . 'excel/');

// Session Configuration
define('SESSION_TIMEOUT', 3600); // 1 hour in seconds

// Pagination
define('RECORDS_PER_PAGE', 20);

// Date Format
define('DATE_FORMAT', 'Y-m-d');
define('DATETIME_FORMAT', 'Y-m-d H:i:s');
define('DISPLAY_DATE_FORMAT', 'd-m-Y');

// File Upload Settings
define('MAX_FILE_SIZE', 52428800); // 50MB in bytes
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/jpg']);
define('ALLOWED_EXCEL_TYPES', ['application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']);

// Timezone
date_default_timezone_set('Asia/Karachi');

// Error Reporting (Set to 0 in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start Session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
