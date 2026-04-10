<?php
/**
 * Delete Teacher
 * MIQT System
 */

require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../includes/functions.php';

if (!hasPermission(['principal', 'vice_principal', 'coordinator'])) {
    setFlash('danger', 'You do not have permission to access this page');
    redirect(SITE_URL . '/modules/dashboard/index.php');
}

$teacher_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get teacher details
$sql = "SELECT * FROM teachers WHERE id = ?";
$stmt = $db->prepare($sql);
$stmt->execute([$teacher_id]);
$teacher = $stmt->fetch();

if (!$teacher) {
    setFlash('danger', 'Teacher not found');
    redirect(SITE_URL . '/modules/hr/teachers.php');
}

try {
    // Delete photo if exists
    if ($teacher['photo'] && file_exists(PHOTO_PATH . $teacher['photo'])) {
        unlink(PHOTO_PATH . $teacher['photo']);
    }

    // Delete teacher (this will cascade delete related records if configured)
    $sql = "DELETE FROM teachers WHERE id = ?";
    $stmt = $db->prepare($sql);
    $stmt->execute([$teacher_id]);

    logActivity($db, 'Delete Teacher', 'HR', "Deleted teacher: {$teacher['first_name']} {$teacher['last_name']}");
    setFlash('success', 'Teacher deleted successfully!');
} catch(PDOException $e) {
    setFlash('danger', 'Error deleting teacher: ' . $e->getMessage());
}

redirect(SITE_URL . '/modules/hr/teachers.php');
?>
