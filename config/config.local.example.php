<?php
/**
 * LIVE SERVER ONLY — copy to config.local.php on the hosting account (not in local XAMPP).
 *
 * 1. On the server, copy this file to: config/config.local.php
 * 2. Replace placeholders with your live DB user, password, database name, and SITE_URL
 * 3. SITE_URL must be your public URL with trailing slash, e.g. https://miqt.theteacher.pk/
 *
 * Local development: do not create config.local.php — config.php defaults are used.
 */

define('DB_HOST', '127.0.0.1');
define('DB_USER', 'your_live_db_user');
define('DB_PASS', 'your_live_db_password');
define('DB_NAME', 'your_live_database');

define('SITE_NAME', 'MIQT - Minhaj Institute of Qirat & Tajweed');
define('SITE_URL', 'https://your-live-domain.example/');
