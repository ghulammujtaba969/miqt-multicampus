<?php
/**
 * Parent Dashboard
 * MIQT System
 */

require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../includes/functions.php';

// Check if user is logged in and is a parent
if (!isLoggedIn() || getUserRole() != 'parent') {
    setFlash('danger', 'Access denied. Parent login required.');
    redirect(SITE_URL . '/index.php');
}

$pageTitle = 'Parent Dashboard';

$parent_id = isset($_SESSION['parent_id']) ? $_SESSION['parent_id'] : null;

// Get parent details
$parent = null;
if ($parent_id) {
    $sql = "SELECT * FROM parents WHERE id = ?";
    $stmt = $db->prepare($sql);
    $stmt->execute([$parent_id]);
    $parent = $stmt->fetch();
}

// Get children (students) linked to this parent
$children = [];
if ($parent_id) {
    $sql = "SELECT s.*, c.class_name, psr.relation_type, psr.is_primary
            FROM parent_student_relation psr
            JOIN students s ON psr.student_id = s.id
            LEFT JOIN classes c ON s.class_id = c.id
            WHERE psr.parent_id = ? AND s.status = 'active'
            ORDER BY psr.is_primary DESC, s.first_name, s.last_name";
    $stmt = $db->prepare($sql);
    $stmt->execute([$parent_id]);
    $children = $stmt->fetchAll();
}

// Get overall statistics for all children
$stats = [
    'total_children' => count($children),
    'total_attendance' => 0,
    'total_progress' => 0
];

if (count($children) > 0) {
    $child_ids = array_column($children, 'id');
    $placeholders = implode(',', array_fill(0, count($child_ids), '?'));
    
    // Total attendance
    $sql = "SELECT COUNT(*) as total FROM student_attendance WHERE student_id IN ($placeholders)";
    $stmt = $db->prepare($sql);
    $stmt->execute($child_ids);
    $stats['total_attendance'] = $stmt->fetch()['total'];
    
    // Total progress
    $sql = "SELECT COUNT(*) as total FROM quran_progress WHERE student_id IN ($placeholders)";
    $stmt = $db->prepare($sql);
    $stmt->execute($child_ids);
    $stats['total_progress'] = $stmt->fetch()['total'];
}

require_once '../../includes/header_parent.php';
?>

<div class="dashboard">
    <h1 class="page-title"><i class="fas fa-users"></i> Parent Dashboard</h1>
    <p class="subtitle">Welcome, <?php echo getUserName(); ?>!</p>

    <?php if ($parent): ?>
    <div class="dashboard-card mb-20">
        <div class="card-body">
            <div class="form-row">
                <div class="form-group">
                    <strong>Parent ID:</strong> <?php echo $parent['parent_id']; ?>
                </div>
                <div class="form-group">
                    <strong>Name:</strong> <?php echo $parent['first_name'] . ' ' . $parent['last_name']; ?>
                </div>
                <div class="form-group">
                    <strong>Phone:</strong> <?php echo $parent['phone']; ?>
                </div>
                <div class="form-group">
                    <strong>Email:</strong> <?php echo $parent['email'] ?: 'N/A'; ?>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Statistics Cards -->
    <div class="stats-grid">
        <div class="stat-card stat-primary">
            <div class="stat-icon">
                <i class="fas fa-user-graduate"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $stats['total_children']; ?></h3>
                <p>My Children</p>
            </div>
        </div>

        <div class="stat-card stat-success">
            <div class="stat-icon">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $stats['total_attendance']; ?></h3>
                <p>Total Attendance Records</p>
            </div>
        </div>

        <div class="stat-card stat-info">
            <div class="stat-icon">
                <i class="fas fa-book-quran"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $stats['total_progress']; ?></h3>
                <p>Total Progress Records</p>
            </div>
        </div>
    </div>

    <!-- My Children -->
    <div class="dashboard-card mt-20">
        <div class="card-header">
            <h3><i class="fas fa-user-graduate"></i> My Children</h3>
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
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($children as $child): ?>
                        <tr>
                            <td><?php echo $child['student_id']; ?></td>
                            <td><?php echo $child['first_name'] . ' ' . $child['last_name']; ?></td>
                            <td><?php echo $child['class_name'] ?? 'N/A'; ?></td>
                            <td>
                                <span class="badge badge-info"><?php echo ucfirst($child['relation_type']); ?></span>
                                <?php if ($child['is_primary']): ?>
                                    <span class="badge badge-success">Primary</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge badge-success"><?php echo ucfirst($child['status']); ?></span>
                            </td>
                            <td>
                                <a href="<?php echo SITE_URL; ?>/modules/students/view_student.php?id=<?php echo $child['id']; ?>" class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i> View Profile
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <p class="text-center text-muted">No children linked to your account. Please contact the administration.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Quick Access -->
    <?php if (count($children) > 0): ?>
    <div class="dashboard-card mt-20">
        <div class="card-header">
            <h3><i class="fas fa-bolt"></i> Quick Access</h3>
        </div>
        <div class="card-body">
            <div class="dashboard-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));">
                <?php foreach ($children as $child): ?>
                <div class="dashboard-card">
                    <div class="card-body" style="text-align: center;">
                        <h4><?php echo $child['first_name'] . ' ' . $child['last_name']; ?></h4>
                        <p class="text-muted"><?php echo $child['student_id']; ?></p>
                        <div style="margin-top: 15px;">
                            <a href="<?php echo SITE_URL; ?>/modules/students/view_student.php?id=<?php echo $child['id']; ?>" class="btn btn-primary btn-sm">
                                <i class="fas fa-user"></i> Profile
                            </a>
                            <a href="<?php echo SITE_URL; ?>/modules/students/view_student.php?id=<?php echo $child['id']; ?>#progress" class="btn btn-info btn-sm">
                                <i class="fas fa-book-quran"></i> Progress
                            </a>
                            <a href="<?php echo SITE_URL; ?>/modules/students/view_student.php?id=<?php echo $child['id']; ?>#exams" class="btn btn-success btn-sm">
                                <i class="fas fa-file-alt"></i> Results
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php require_once '../../includes/footer.php'; ?>

