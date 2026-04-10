<?php
/**
 * Common Functions
 * MIQT System
 */

/**
 * Sanitize input data
 */
function sanitize($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Get current user role
 */
function getUserRole() {
    return isset($_SESSION['user_role']) ? $_SESSION['user_role'] : null;
}

/**
 * Check if user has permission
 */
function hasPermission($allowedRoles = []) {
    if (!isLoggedIn()) {
        return false;
    }

    $userRole = getUserRole();
    return in_array($userRole, $allowedRoles);
}

/**
 * Redirect to a page
 */
function redirect($url) {
    header("Location: " . $url);
    exit();
}

/**
 * Display flash message
 */
function setFlash($type, $message) {
    $_SESSION['flash_type'] = $type;
    $_SESSION['flash_message'] = $message;
}

/**
 * Get and clear flash message
 */
function getFlash() {
    if (isset($_SESSION['flash_message'])) {
        $type = $_SESSION['flash_type'];
        $message = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_type']);
        return ['type' => $type, 'message' => $message];
    }
    return null;
}

/**
 * Format date for display
 */
function formatDate($date, $format = DISPLAY_DATE_FORMAT) {
    if (empty($date) || $date === '0000-00-00' || $date === null) {
        return 'N/A';
    }
    return date($format, strtotime($date));
}

/**
 * Get current user ID
 */
function getUserId() {
    return isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
}

/**
 * Get current user name
 */
function getUserName() {
    return isset($_SESSION['user_name']) ? $_SESSION['user_name'] : null;
}

/**
 * Two-letter initials for theme avatar (ASCII-safe).
 */
function getUserInitials($name = null) {
    $name = $name !== null ? $name : getUserName();
    if (empty($name)) {
        return '?';
    }
    $parts = preg_split('/\s+/', trim($name));
    $parts = array_filter($parts);
    $parts = array_values($parts);
    $initials = '';
    $max = min(2, count($parts));
    for ($i = 0; $i < $max; $i++) {
        $initials .= strtoupper(substr($parts[$i], 0, 1));
    }
    return $initials !== '' ? $initials : '?';
}

/**
 * Generate unique ID
 */
function generateUniqueId($prefix = '') {
    return $prefix . strtoupper(substr(md5(time() . rand()), 0, 10));
}

/**
 * Upload file
 */
function uploadFile($file, $targetDir, $allowedTypes = ALLOWED_IMAGE_TYPES) {
    if (!isset($file) || $file['error'] != 0) {
        return ['success' => false, 'message' => 'File upload error'];
    }

    // Check file size
    if ($file['size'] > MAX_FILE_SIZE) {
        return ['success' => false, 'message' => 'File size exceeds limit'];
    }

    // Check file type
    if (!in_array($file['type'], $allowedTypes)) {
        return ['success' => false, 'message' => 'File type not allowed'];
    }

    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '_' . time() . '.' . $extension;
    $targetPath = $targetDir . $filename;

    // Create directory if not exists
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    // Move file
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        return ['success' => true, 'filename' => $filename];
    }

    return ['success' => false, 'message' => 'Failed to upload file'];
}

/**
 * Delete file
 */
function deleteFile($filepath) {
    if (file_exists($filepath)) {
        return unlink($filepath);
    }
    return false;
}

/**
 * Log activity
 */
function logActivity($db, $action, $module, $description = '') {
    $userId = getUserId();
    $ipAddress = $_SERVER['REMOTE_ADDR'];

    $sql = "INSERT INTO activity_logs (user_id, action, module, description, ip_address)
            VALUES (?, ?, ?, ?, ?)";

    try {
        $stmt = $db->prepare($sql);
        $stmt->execute([$userId, $action, $module, $description, $ipAddress]);
        return true;
    } catch(PDOException $e) {
        return false;
    }
}

/**
 * Get pagination HTML
 */
function getPagination($currentPage, $totalPages, $url) {
    $html = '<nav><ul class="pagination">';

    // Previous button
    if ($currentPage > 1) {
        $html .= '<li><a href="' . $url . '?page=' . ($currentPage - 1) . '">Previous</a></li>';
    }

    // Page numbers
    for ($i = 1; $i <= $totalPages; $i++) {
        $active = ($i == $currentPage) ? 'active' : '';
        $html .= '<li class="' . $active . '"><a href="' . $url . '?page=' . $i . '">' . $i . '</a></li>';
    }

    // Next button
    if ($currentPage < $totalPages) {
        $html .= '<li><a href="' . $url . '?page=' . ($currentPage + 1) . '">Next</a></li>';
    }

    $html .= '</ul></nav>';
    return $html;
}

/**
 * Calculate grade based on percentage
 */
function calculateGrade($percentage) {
    if ($percentage >= 90) return 'A+';
    if ($percentage >= 80) return 'A';
    if ($percentage >= 70) return 'B';
    if ($percentage >= 60) return 'C';
    if ($percentage >= 50) return 'D';
    if ($percentage >= 40) return 'E';
    return 'F';
}

/**
 * Convert number to Urdu
 */
function convertToUrdu($number) {
    $urduNumbers = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
    $englishNumbers = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
    return str_replace($englishNumbers, $urduNumbers, $number);
}

/**
 * Get Juz name in Arabic
 */
function getJuzName($juzNumber) {
    $juzNames = [
        1 => 'الٓمٓ', 2 => 'سَيَقُولُ', 3 => 'تِلْكَ ٱلرُّسُلُ', 4 => 'لَن تَنَالُوا۟', 5 => 'وَٱلْمُحْصَنَٰتُ',
        6 => 'لَّا يُحِبُّ ٱللَّهُ', 7 => 'وَإِذَا سَمِعُوا۟', 8 => 'وَلَوْ أَنَّنَا', 9 => 'قَالَ ٱلْمَلَأُ', 10 => 'وَٱعْلَمُوٓا۟',
        11 => 'يَعْتَذِرُونَ', 12 => 'وَمَا مِنْ دَآبَّةٍ', 13 => 'وَمَآ أُبَرِّئُ', 14 => 'رُبَمَا', 15 => 'سُبْحَٰنَ ٱلَّذِىٓ',
        16 => 'قَالَ أَلَمْ', 17 => 'ٱقْتَرَبَ لِلنَّاسِ', 18 => 'قَدْ أَفْلَحَ', 19 => 'وَقَالَ ٱلَّذِينَ', 20 => 'أَمَّنْ خَلَقَ',
        21 => 'ٱتْلُ مَآ أُوحِىَ', 22 => 'وَمَن يَقْنُتْ', 23 => 'وَمَآ لِىَ', 24 => 'فَمَنْ أَظْلَمُ', 25 => 'إِلَيْهِ يُرَدُّ',
        26 => 'حمٓ', 27 => 'قَالَ فَمَا', 28 => 'قَدْ سَمِعَ', 29 => 'تَبَارَكَ ٱلَّذِى', 30 => 'عَمَّ'
    ];
    return isset($juzNames[$juzNumber]) ? $juzNames[$juzNumber] : '';
}

/**
 * Validate email
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Generate random password
 */
function generatePassword($length = 8) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $password = '';
    for ($i = 0; $i < $length; $i++) {
        $password .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $password;
}
?>
