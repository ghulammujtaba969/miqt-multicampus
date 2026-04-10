<?php
/**
 * View Parent Profile
 * MIQT System
 */

require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../includes/functions.php';

$pageTitle = 'Parent Profile';

$parent_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get parent details
$sql = "SELECT p.*, u.username, u.status as user_status FROM parents p
        LEFT JOIN users u ON p.user_id = u.id
        WHERE p.id = ?";
$stmt = $db->prepare($sql);
$stmt->execute([$parent_id]);
$parent = $stmt->fetch();

if (!$parent) {
    setFlash('danger', 'Parent not found');
    redirect(SITE_URL . '/modules/students/parents.php');
}

// Get linked students
$sql = "SELECT psr.*, s.*, c.class_name
        FROM parent_student_relation psr
        JOIN students s ON psr.student_id = s.id
        LEFT JOIN classes c ON s.class_id = c.id
        WHERE psr.parent_id = ?
        ORDER BY psr.is_primary DESC, s.first_name, s.last_name";
$stmt = $db->prepare($sql);
$stmt->execute([$parent_id]);
$children = $stmt->fetchAll();

require_once '../../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title"><i class="fas fa-users"></i> Parent Profile</h1>
    <p class="subtitle"><?php echo $parent['parent_id']; ?></p>
</div>

<div class="dashboard-grid mb-20">
    <div class="dashboard-card">
        <div class="card-body" style="text-align: center;">
            <i class="fas fa-user-circle" style="font-size: 150px; color: #ccc; margin-bottom: 20px;"></i>
            <h2><?php echo htmlspecialchars($parent['first_name'] . ' ' . $parent['last_name']); ?></h2>
            <p class="text-muted"><?php echo htmlspecialchars($parent['parent_id']); ?></p>

            <div style="margin-top: 20px;">
                <?php if (hasPermission(['principal', 'vice_principal', 'coordinator'])): ?>
                <a href="edit_parent.php?id=<?php echo $parent['id']; ?>" class="btn btn-primary">
                    <i class="fas fa-edit"></i> Edit Profile
                </a>
                <a href="delete_parent.php?id=<?php echo $parent['id']; ?>" class="btn btn-danger"
                   onclick="return confirm('Are you sure you want to delete this parent?')">
                    <i class="fas fa-trash"></i> Delete
                </a>
                <?php endif; ?>
                <a href="parents.php" class="btn btn-secondary">
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
                    <th>Parent ID:</th>
                    <td><?php echo htmlspecialchars($parent['parent_id']); ?></td>
                </tr>
                <tr>
                    <th>Name:</th>
                    <td><?php echo htmlspecialchars($parent['first_name'] . ' ' . $parent['last_name']); ?></td>
                </tr>
                <tr>
                    <th>CNIC:</th>
                    <td><?php echo htmlspecialchars($parent['cnic'] ?: 'N/A'); ?></td>
                </tr>
                <tr>
                    <th>Relation:</th>
                    <td><span class="badge badge-info"><?php echo ucfirst($parent['relation']); ?></span></td>
                </tr>
                <tr>
                    <th>Occupation:</th>
                    <td><?php echo htmlspecialchars($parent['occupation'] ?: 'N/A'); ?></td>
                </tr>
                <tr>
                    <th>Status:</th>
                    <td><span class="badge <?php echo $parent['status'] == 'active' ? 'badge-success' : 'badge-danger'; ?>">
                        <?php echo ucfirst($parent['status']); ?>
                    </span></td>
                </tr>
                <?php if ($parent['username']): ?>
                <tr>
                    <th>Login Account:</th>
                    <td>
                        <span class="badge badge-success"><?php echo htmlspecialchars($parent['username']); ?></span>
                        <?php if (hasPermission(['principal', 'vice_principal'])): ?>
                        <a href="<?php echo SITE_URL; ?>/modules/users/edit_user.php?id=<?php echo $parent['user_id']; ?>" class="btn btn-sm btn-primary" style="margin-left: 10px;">
                            <i class="fas fa-edit"></i> Edit Account
                        </a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endif; ?>
            </table>
        </div>
    </div>
</div>

<div class="dashboard-card">
    <div class="card-header">
        <h3><i class="fas fa-phone"></i> Contact Information</h3>
    </div>
    <div class="card-body">
        <table class="table">
            <tr>
                <th>Phone:</th>
                <td><?php echo htmlspecialchars($parent['phone']); ?></td>
            </tr>
            <tr>
                <th>Email:</th>
                <td><?php echo htmlspecialchars($parent['email'] ?: 'N/A'); ?></td>
            </tr>
            <tr>
                <th>Address:</th>
                <td><?php echo htmlspecialchars($parent['address'] ?: 'N/A'); ?></td>
            </tr>
            <tr>
                <th>City:</th>
                <td><?php echo htmlspecialchars($parent['city'] ?: 'N/A'); ?></td>
            </tr>
        </table>
    </div>
</div>

<!-- Linked Children -->
<div class="dashboard-card mt-20">
    <div class="card-header">
        <h3><i class="fas fa-user-graduate"></i> Linked Children (<?php echo count($children); ?>)</h3>
    </div>
    <div class="card-body">
        <?php if (count($children) > 0): ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Student ID</th>
                        <th>Name</th>
                        <th>Class</th>
                        <th>Relation</th>
                        <th>Primary Contact</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($children as $child): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($child['student_id']); ?></td>
                        <td><?php echo htmlspecialchars($child['first_name'] . ' ' . $child['last_name']); ?></td>
                        <td><?php echo htmlspecialchars($child['class_name'] ?? 'N/A'); ?></td>
                        <td>
                            <span class="badge badge-info"><?php echo ucfirst($child['relation_type']); ?></span>
                        </td>
                        <td>
                            <?php if ($child['is_primary']): ?>
                                <span class="badge badge-success">Yes</span>
                            <?php else: ?>
                                <span class="badge badge-secondary">No</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge badge-success"><?php echo ucfirst($child['status']); ?></span>
                        </td>
                        <td>
                            <a href="view_student.php?id=<?php echo $child['id']; ?>" class="btn btn-sm btn-primary">
                                <i class="fas fa-eye"></i> View
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <p class="text-center text-muted">No children linked to this parent</p>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>

