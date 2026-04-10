<?php
/**
 * Staff List
 * MIQT System
 */

require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../includes/functions.php';

if (!hasPermission(['principal', 'vice_principal', 'coordinator'])) {
    setFlash('danger', 'You do not have permission to access this page');
    redirect(SITE_URL . '/modules/dashboard/index.php');
}

$pageTitle = 'All Staff';

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * RECORDS_PER_PAGE;

// Search
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$department_filter = isset($_GET['department']) ? sanitize($_GET['department']) : '';

// Build query - include staff, principal, vice_principal, coordinator
$where = "WHERE u.role IN ('staff', 'principal', 'vice_principal', 'coordinator')";
$params = [];

if (!empty($search)) {
    $where .= " AND (u.username LIKE ? OR u.full_name LIKE ? OR u.email LIKE ?)";
    $searchTerm = "%$search%";
    $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm]);
}

// Get total records
$sql = "SELECT COUNT(*) as total FROM users u $where";
$stmt = $db->prepare($sql);
$stmt->execute($params);
$total = $stmt->fetch()['total'];
$total_pages = ceil($total / RECORDS_PER_PAGE);

// Get staff
$sql = "SELECT u.* FROM users u
        $where
        ORDER BY 
            CASE u.role
                WHEN 'principal' THEN 1
                WHEN 'vice_principal' THEN 2
                WHEN 'coordinator' THEN 3
                WHEN 'staff' THEN 4
            END,
            u.created_at DESC
        LIMIT " . RECORDS_PER_PAGE . " OFFSET $offset";
$stmt = $db->prepare($sql);
$stmt->execute($params);
$staff_list = $stmt->fetchAll();

// Get statistics
$sql = "SELECT
        COUNT(*) as total,
        SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active,
        SUM(CASE WHEN status = 'inactive' THEN 1 ELSE 0 END) as inactive,
        SUM(CASE WHEN role = 'principal' THEN 1 ELSE 0 END) as principal,
        SUM(CASE WHEN role = 'vice_principal' THEN 1 ELSE 0 END) as vice_principal,
        SUM(CASE WHEN role = 'coordinator' THEN 1 ELSE 0 END) as coordinator,
        SUM(CASE WHEN role = 'staff' THEN 1 ELSE 0 END) as staff
        FROM users WHERE role IN ('staff', 'principal', 'vice_principal', 'coordinator')";
$stmt = $db->prepare($sql);
$stmt->execute();
$stats = $stmt->fetch();

require_once '../../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title"><i class="fas fa-user-tie"></i> All Staff</h1>
    <p class="subtitle">
        Total: <?php echo $stats['total']; ?> | 
        Active: <?php echo $stats['active']; ?> | 
        Inactive: <?php echo $stats['inactive']; ?> | 
        Principal: <?php echo $stats['principal']; ?> | 
        Vice Principal: <?php echo $stats['vice_principal']; ?> | 
        Coordinator: <?php echo $stats['coordinator']; ?> | 
        Staff: <?php echo $stats['staff']; ?>
    </p>
</div>

<div class="dashboard-card mb-20">
    <div class="card-body">
        <form method="GET" action="" class="form-row">
            <div class="form-group" style="flex: 1;">
                <input type="text" name="search" class="form-control" placeholder="Search by Username, Name, or Email"
                       value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Search
                </button>
                <a href="staff.php" class="btn btn-secondary">
                    <i class="fas fa-redo"></i> Reset
                </a>
                <a href="add_staff.php" class="btn btn-success">
                    <i class="fas fa-plus"></i> Add Staff
                </a>
            </div>
        </form>
    </div>
</div>

<div class="dashboard-card">
    <div class="card-body">
        <?php if (count($staff_list) > 0): ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($staff_list as $staff): ?>
                    <tr>
                        <td>
                            <strong><?php echo htmlspecialchars($staff['username']); ?></strong>
                        </td>
                        <td><?php echo htmlspecialchars($staff['email'] ?: 'N/A'); ?></td>
                        <td>
                            <?php
                            $roleBadges = [
                                'principal' => 'badge-danger',
                                'vice_principal' => 'badge-warning',
                                'coordinator' => 'badge-primary',
                                'staff' => 'badge-info'
                            ];
                            $roleLabels = [
                                'principal' => 'Principal',
                                'vice_principal' => 'Vice Principal',
                                'coordinator' => 'Coordinator',
                                'staff' => 'Staff'
                            ];
                            $badgeClass = $roleBadges[$staff['role']] ?? 'badge-secondary';
                            $roleLabel = $roleLabels[$staff['role']] ?? ucfirst(str_replace('_', ' ', $staff['role']));
                            ?>
                            <span class="badge <?php echo $badgeClass; ?>"><?php echo $roleLabel; ?></span>
                        </td>
                        <td>
                            <?php
                            $badge_class = [
                                'active' => 'badge-success',
                                'inactive' => 'badge-secondary'
                            ];
                            ?>
                            <span class="badge <?php echo $badge_class[$staff['status']] ?? 'badge-secondary'; ?>">
                                <?php echo ucfirst($staff['status']); ?>
                            </span>
                        </td>
                        <td><?php echo formatDate($staff['created_at']); ?></td>
                        <td>
                            <a href="view_staff.php?id=<?php echo $staff['id']; ?>" class="btn btn-sm btn-info" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="edit_staff.php?id=<?php echo $staff['id']; ?>" class="btn btn-sm btn-primary" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="delete_staff.php?id=<?php echo $staff['id']; ?>" 
                               class="btn btn-sm btn-danger" 
                               title="Delete"
                               onclick="return confirm('Are you sure you want to delete this staff member?')">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
        <div class="pagination">
            <?php
            $query_string = $_SERVER['QUERY_STRING'];
            $query_string = preg_replace('/&?page=\d+/', '', $query_string);
            $query_string = $query_string ? $query_string . '&' : '';
            
            if ($page > 1): ?>
            <a href="?<?php echo $query_string; ?>page=<?php echo $page - 1; ?>" class="btn btn-sm">Previous</a>
            <?php endif; ?>
            
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="?<?php echo $query_string; ?>page=<?php echo $i; ?>" class="btn btn-sm <?php echo $i == $page ? 'btn-primary' : ''; ?>"><?php echo $i; ?></a>
            <?php endfor; ?>
            
            <?php if ($page < $total_pages): ?>
            <a href="?<?php echo $query_string; ?>page=<?php echo $page + 1; ?>" class="btn btn-sm">Next</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        <?php else: ?>
        <p class="text-center text-muted">No staff members found</p>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>

