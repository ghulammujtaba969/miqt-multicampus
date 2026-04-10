<?php
/**
 * Teachers List
 * MIQT System
 */

require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../includes/functions.php';

if (!hasPermission(['principal', 'vice_principal', 'coordinator'])) {
    setFlash('danger', 'You do not have permission to access this page');
    redirect(SITE_URL . '/modules/dashboard/index.php');
}

$pageTitle = 'All Teachers';

// Search
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';

// Build query
$where = "WHERE t.status != 'deleted'";
$params = [];

if (!empty($search)) {
    $where .= " AND (t.teacher_id LIKE ? OR t.first_name LIKE ? OR t.last_name LIKE ? OR t.phone LIKE ?)";
    $searchTerm = "%$search%";
    $params = [$searchTerm, $searchTerm, $searchTerm, $searchTerm];
}

// Get teachers
$sql = "SELECT t.*, u.username FROM teachers t
        LEFT JOIN users u ON t.user_id = u.id
        $where
        ORDER BY t.created_at DESC";
$stmt = $db->prepare($sql);
$stmt->execute($params);
$teachers = $stmt->fetchAll();

// Get statistics
$sql = "SELECT
        COUNT(*) as total,
        SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active,
        SUM(CASE WHEN status = 'on_leave' THEN 1 ELSE 0 END) as on_leave
        FROM teachers WHERE status != 'deleted'";
$stmt = $db->prepare($sql);
$stmt->execute();
$stats = $stmt->fetch();

require_once '../../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title"><i class="fas fa-chalkboard-teacher"></i> All Teachers</h1>
    <p class="subtitle">Total: <?php echo $stats['total']; ?> | Active: <?php echo $stats['active']; ?> | On Leave: <?php echo $stats['on_leave']; ?></p>
</div>

<div class="dashboard-card mb-20">
    <div class="card-body">
        <form method="GET" action="" class="form-row">
            <div class="form-group">
                <input type="text" name="search" class="form-control" placeholder="Search by ID, Name, or Phone"
                       value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Search
                </button>
                <a href="teachers.php" class="btn btn-secondary">
                    <i class="fas fa-redo"></i> Reset
                </a>
                <a href="add_teacher.php" class="btn btn-success">
                    <i class="fas fa-plus"></i> Add Teacher
                </a>
            </div>
        </form>
    </div>
</div>

<div class="dashboard-card">
    <div class="card-body">
        <?php if (count($teachers) > 0): ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Photo</th>
                    <th>Teacher ID</th>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Specialization</th>
                    <th>Joining Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($teachers as $teacher): ?>
                <tr>
                    <td>
                        <?php if ($teacher['photo']): ?>
                        <img src="<?php echo SITE_URL . '/uploads/photos/' . $teacher['photo']; ?>"
                             alt="Photo" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                        <?php else: ?>
                        <i class="fas fa-user-circle" style="font-size: 40px; color: #ccc;"></i>
                        <?php endif; ?>
                    </td>
                    <td><?php echo $teacher['teacher_id']; ?></td>
                    <td>
                        <strong><?php echo $teacher['first_name'] . ' ' . $teacher['last_name']; ?></strong><br>
                        <small class="text-muted"><?php echo $teacher['father_name']; ?></small>
                    </td>
                    <td><?php echo $teacher['phone']; ?></td>
                    <td><?php echo $teacher['specialization'] ?? 'N/A'; ?></td>
                    <td><?php echo formatDate($teacher['joining_date']); ?></td>
                    <td>
                        <?php
                        $badge_class = [
                            'active' => 'badge-success',
                            'inactive' => 'badge-secondary',
                            'on_leave' => 'badge-warning'
                        ];
                        ?>
                        <span class="badge <?php echo $badge_class[$teacher['status']] ?? 'badge-secondary'; ?>">
                            <?php echo ucfirst(str_replace('_', ' ', $teacher['status'])); ?>
                        </span>
                    </td>
                    <td>
                        <a href="view_teacher.php?id=<?php echo $teacher['id']; ?>" class="btn btn-sm btn-info" title="View">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="edit_teacher.php?id=<?php echo $teacher['id']; ?>" class="btn btn-sm btn-primary" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <p class="text-center text-muted">No teachers found</p>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>
