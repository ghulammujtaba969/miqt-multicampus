<?php
/**
 * View Staff Profile
 * MIQT System
 */

require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../includes/functions.php';

if (!hasPermission(['principal', 'vice_principal', 'coordinator'])) {
    setFlash('danger', 'You do not have permission to access this page');
    redirect(SITE_URL . '/modules/dashboard/index.php');
}

$pageTitle = 'Staff Profile';

$staff_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get staff details from users table
$sql = "SELECT * FROM users WHERE id = ? AND role IN ('staff', 'principal', 'vice_principal', 'coordinator')";
$stmt = $db->prepare($sql);
$stmt->execute([$staff_id]);
$staff = $stmt->fetch();

if (!$staff) {
    setFlash('danger', 'Staff member not found');
    redirect(SITE_URL . '/modules/hr/staff.php');
}

require_once '../../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title"><i class="fas fa-user-tie"></i> Staff Profile</h1>
    <p class="subtitle"><?php echo htmlspecialchars($staff['username']); ?></p>
</div>

<div class="dashboard-grid mb-20">
    <div class="dashboard-card">
        <div class="card-body" style="text-align: center;">
            <i class="fas fa-user-circle" style="font-size: 150px; color: #ccc; margin-bottom: 20px;"></i>

            <h2><?php echo htmlspecialchars($staff['full_name']); ?></h2>
            <p class="text-muted"><?php echo htmlspecialchars($staff['username']); ?></p>
            <p>
                <span class="badge badge-primary">
                    <?php 
                    $roleLabels = [
                        'principal' => 'Principal',
                        'vice_principal' => 'Vice Principal',
                        'coordinator' => 'Coordinator',
                        'staff' => 'Staff Member'
                    ];
                    echo $roleLabels[$staff['role']] ?? ucfirst($staff['role']);
                    ?>
                </span>
            </p>

            <div style="margin-top: 20px;">
                <a href="edit_staff.php?id=<?php echo $staff['id']; ?>" class="btn btn-primary">
                    <i class="fas fa-edit"></i> Edit Profile
                </a>
                <a href="delete_staff.php?id=<?php echo $staff['id']; ?>" class="btn btn-danger"
                   onclick="return confirm('Are you sure you want to delete this staff member?')">
                    <i class="fas fa-trash"></i> Delete
                </a>
                <a href="staff.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
        </div>
    </div>

    <div class="dashboard-card">
        <div class="card-header">
            <h3><i class="fas fa-info-circle"></i> Basic Information</h3>
        </div>
        <div class="card-body">
            <table class="table">
                <tr>
                    <th>Username:</th>
                    <td><?php echo htmlspecialchars($staff['username']); ?></td>
                </tr>
                <tr>
                    <th>Email:</th>
                    <td><?php echo htmlspecialchars($staff['email'] ?: 'N/A'); ?></td>
                </tr>
                <tr>
                    <th>Role:</th>
                    <td><span class="badge badge-info"><?php echo ucfirst($staff['role']); ?></span></td>
                </tr>
                <tr>
                    <th>Status:</th>
                    <td>
                        <?php
                        $badges = [
                            'active' => 'badge-success',
                            'inactive' => 'badge-secondary'
                        ];
                        $badge = $badges[$staff['status']] ?? 'badge-secondary';
                        ?>
                        <span class="badge <?php echo $badge; ?>">
                            <?php echo ucfirst($staff['status']); ?>
                        </span>
                    </td>
                </tr>
                <tr>
                    <th>Created:</th>
                    <td><?php echo formatDate($staff['created_at']); ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>

