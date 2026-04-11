<?php
/**
 * MIQT System Configuration File
 * Minhaj Institute of Qirat & Tajweed
 */

if (file_exists(__DIR__ . '/config.local.php')) {
    require_once __DIR__ . '/config.local.php';
}

// Database Configuration (override in config.local.php on the server)
if (!defined('DB_HOST')) {
    define('DB_HOST', 'localhost');
}
if (!defined('DB_USER')) {
    define('DB_USER', 'root');
}
if (!defined('DB_PASS')) {
    define('DB_PASS', '');
}
if (!defined('DB_NAME')) {
    define('DB_NAME', 'u921830511_miqt');
}

// Site Configuration
if (!defined('SITE_NAME')) {
    define('SITE_NAME', 'MIQT - Minhaj Institute of Qirat & Tajweed');
}

/**
 * Public base URL (must match how users open the app in the browser).
 * On production, either define SITE_URL in config.local.php, set env MIQT_SITE_URL,
 * or rely on auto-detection below (HTTP host + path under DOCUMENT_ROOT).
 */
if (!defined('SITE_URL')) {
    $envUrl = getenv('MIQT_SITE_URL');
    if (is_string($envUrl) && trim($envUrl) !== '') {
        define('SITE_URL', rtrim(trim($envUrl), '/\\') . '/');
    } elseif (PHP_SAPI !== 'cli' && !empty($_SERVER['HTTP_HOST'])) {
        $projectRoot = realpath(__DIR__ . '/..');
        $docRoot = !empty($_SERVER['DOCUMENT_ROOT']) ? realpath($_SERVER['DOCUMENT_ROOT']) : false;
        $basePath = '';
        if ($projectRoot && $docRoot && strpos($projectRoot, $docRoot) === 0) {
            $basePath = str_replace('\\', '/', substr($projectRoot, strlen($docRoot)));
        }
        if ($basePath === '/' || $basePath === false) {
            $basePath = '';
        }
        $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower((string) $_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https')
            || (!empty($_SERVER['HTTP_X_FORWARDED_SSL']) && (string) $_SERVER['HTTP_X_FORWARDED_SSL'] === 'on')
            || (!empty($_SERVER['HTTP_FRONT_END_HTTPS']) && (string) $_SERVER['HTTP_FRONT_END_HTTPS'] === 'on')
            || (!empty($_SERVER['SERVER_PORT']) && (string) $_SERVER['SERVER_PORT'] === '443');
        $proto = $https ? 'https' : 'http';
        define('SITE_URL', $proto . '://' . $_SERVER['HTTP_HOST'] . $basePath . '/');
    } else {
        define('SITE_URL', 'http://localhost/miqt/02-04-2026-miqt/');
    }
}

if (!defined('MIQT_LOGO_URL')) {
    define('MIQT_LOGO_URL', SITE_URL . 'assets/img/favicon.png');
}

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

// Error reporting: hide HTML errors on real hosts; check server error_log if pages are blank
$miqtLocalHost = PHP_SAPI === 'cli'
    || (isset($_SERVER['HTTP_HOST']) && preg_match('/^(localhost|127\.0\.0\.1)(:\\d+)?$/i', (string) $_SERVER['HTTP_HOST']));
error_reporting(E_ALL);
ini_set('log_errors', '1');
ini_set('display_errors', $miqtLocalHost ? '1' : '0');

// Start Session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
