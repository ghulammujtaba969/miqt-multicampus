<?php
/**
 * Delete Parent
 * MIQT System
 */

require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../includes/functions.php';

if (!hasPermission(['principal', 'vice_principal', 'coordinator'])) {
    setFlash('danger', 'You do not have permission to access this page');
    redirect(SITE_URL . '/modules/dashboard/index.php');
}

$parent_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get parent details
$sql = "SELECT p.*, u.id as user_id FROM parents p
        LEFT JOIN users u ON p.user_id = u.id
        WHERE p.id = ?";
$stmt = $db->prepare($sql);
$stmt->execute([$parent_id]);
$parent = $stmt->fetch();

if (!$parent) {
    setFlash('danger', 'Parent not found');
    redirect(SITE_URL . '/modules/students/parents.php');
}

try {
    $db->beginTransaction();
    
    // Delete parent-student relations
    $sql = "DELETE FROM parent_student_relation WHERE parent_id = ?";
    $stmt = $db->prepare($sql);
    $stmt->execute([$parent_id]);
    
    // Delete parent
    $sql = "DELETE FROM parents WHERE id = ?";
    $stmt = $db->prepare($sql);
    $stmt->execute([$parent_id]);
    
    // Delete user account if exists
    if ($parent['user_id']) {
        $sql = "DELETE FROM users WHERE id = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$parent['user_id']]);
    }
    
    $db->commit();
    
    logActivity($db, 'Delete Parent', 'Students', "Deleted parent: {$parent['first_name']} {$parent['last_name']}");
    setFlash('success', 'Parent deleted successfully!');
} catch(PDOException $e) {
    $db->rollBack();
    setFlash('danger', 'Error deleting parent: ' . $e->getMessage());
}

redirect(SITE_URL . '/modules/students/parents.php');
?>

