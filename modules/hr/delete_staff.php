<?php
/**
 * Delete Staff
 * MIQT System
 */

require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../includes/functions.php';

if (!hasPermission(['principal', 'vice_principal', 'coordinator'])) {
    setFlash('danger', 'You do not have permission to access this page');
    redirect(SITE_URL . '/modules/dashboard/index.php');
}

$staff_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get staff details
$sql = "SELECT * FROM users WHERE id = ? AND role IN ('staff', 'principal', 'vice_principal', 'coordinator')";
$stmt = $db->prepare($sql);
$stmt->execute([$staff_id]);
$staff = $stmt->fetch();

if (!$staff) {
    setFlash('danger', 'Staff member not found');
    redirect(SITE_URL . '/modules/hr/staff.php');
}

try {
    // Delete user account (staff is stored in users table)
    $sql = "DELETE FROM users WHERE id = ?";
    $stmt = $db->prepare($sql);
    $stmt->execute([$staff_id]);
    
    logActivity($db, 'Delete Staff', 'HR', "Deleted staff: {$staff['full_name']}");
    setFlash('success', 'Staff member deleted successfully!');
} catch(PDOException $e) {
    setFlash('danger', 'Error deleting staff: ' . $e->getMessage());
}

redirect(SITE_URL . '/modules/hr/staff.php');
?>

