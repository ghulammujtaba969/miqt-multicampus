<?php
/**
 * Logout Script
 * MIQT System
 */

require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../includes/functions.php';

// Log activity before destroying session
if (isLoggedIn()) {
    logActivity($db, 'Logout', 'Authentication', 'User logged out');
}

// Destroy session
session_unset();
session_destroy();

// Redirect to login page
redirect(SITE_URL . '/index.php');
?>
