<?php
/**
 * Delete Student
 * MIQT System
 */

require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../includes/functions.php';

if (!hasPermission(['principal', 'vice_principal', 'coordinator'])) {
    setFlash('danger', 'You do not have permission to access this page');
    redirect(SITE_URL . '/modules/dashboard/index.php');
}

$student_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get student details
$sql = "SELECT * FROM students WHERE id = ?";
$stmt = $db->prepare($sql);
$stmt->execute([$student_id]);
$student = $stmt->fetch();

if (!$student) {
    setFlash('danger', 'Student not found');
    redirect(SITE_URL . '/modules/students/students.php');
}

try {
    // Delete photo if exists
    if ($student['photo'] && file_exists(PHOTO_PATH . $student['photo'])) {
        unlink(PHOTO_PATH . $student['photo']);
    }

    // Delete student (this will cascade delete related records)
    $sql = "DELETE FROM students WHERE id = ?";
    $stmt = $db->prepare($sql);
    $stmt->execute([$student_id]);

    logActivity($db, 'Delete Student', 'Students', "Deleted student: {$student['first_name']} {$student['last_name']}");
    setFlash('success', 'Student deleted successfully!');
} catch(PDOException $e) {
    setFlash('danger', 'Error deleting student: ' . $e->getMessage());
}

redirect(SITE_URL . '/modules/students/students.php');
?>
