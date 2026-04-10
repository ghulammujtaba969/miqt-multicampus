<?php
/**
 * Parents List
 * MIQT System
 */

require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../includes/functions.php';

// Check permission
if (!hasPermission(['principal', 'vice_principal', 'coordinator'])) {
    setFlash('danger', 'You do not have permission to access this page');
    redirect(SITE_URL . '/modules/dashboard/index.php');
}

$pageTitle = 'All Parents';

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * RECORDS_PER_PAGE;

// Search
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';

// Build query
$where = "WHERE 1=1";
$params = [];

if (!empty($search)) {
    $where .= " AND (p.parent_id LIKE ? OR p.first_name LIKE ? OR p.last_name LIKE ? OR p.phone LIKE ? OR p.email LIKE ?)";
    $searchTerm = "%$search%";
    $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm]);
}

// Get total records
$sql = "SELECT COUNT(*) as total FROM parents p $where";
$stmt = $db->prepare($sql);
$stmt->execute($params);
$total = $stmt->fetch()['total'];
$total_pages = ceil($total / RECORDS_PER_PAGE);

// Get parents
$sql = "SELECT p.*, u.username, u.status as user_status,
        (SELECT COUNT(*) FROM parent_student_relation psr WHERE psr.parent_id = p.id) as children_count
        FROM parents p
        LEFT JOIN users u ON p.user_id = u.id
        $where
        ORDER BY p.created_at DESC
        LIMIT " . RECORDS_PER_PAGE . " OFFSET $offset";
$stmt = $db->prepare($sql);
$stmt->execute($params);
$parents = $stmt->fetchAll();

require_once '../../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title"><i class="fas fa-users"></i> All Parents</h1>
    <p class="subtitle">Total Parents: <?php echo $total; ?></p>
</div>

<div class="dashboard-card mb-20">
    <div class="card-body">
        <form method="GET" action="" class="form-inline">
            <div class="form-row">
                <div class="form-group" style="flex: 1;">
                    <input type="text" name="search" class="form-control" placeholder="Search by Parent ID, Name, Phone, Email..." value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Search
                    </button>
                    <?php if ($search): ?>
                    <a href="<?php echo SITE_URL; ?>/modules/students/parents.php" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Clear
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="dashboard-card">
    <div class="card-header">
        <h3>Parents List</h3>
        <a href="<?php echo SITE_URL; ?>/modules/students/add_parent.php" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Parent
        </a>
    </div>
    <div class="card-body">
        <?php if (count($parents) > 0): ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Parent ID</th>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Relation</th>
                        <th>Children</th>
                        <th>Status</th>
                        <th>Login Account</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($parents as $parent): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($parent['parent_id']); ?></td>
                        <td><?php echo htmlspecialchars($parent['first_name'] . ' ' . $parent['last_name']); ?></td>
                        <td><?php echo htmlspecialchars($parent['phone']); ?></td>
                        <td><?php echo htmlspecialchars($parent['email'] ?: 'N/A'); ?></td>
                        <td>
                            <span class="badge badge-info"><?php echo ucfirst($parent['relation']); ?></span>
                        </td>
                        <td>
                            <span class="badge badge-primary"><?php echo $parent['children_count']; ?> Child(ren)</span>
                        </td>
                        <td>
                            <span class="badge <?php echo $parent['status'] == 'active' ? 'badge-success' : 'badge-danger'; ?>">
                                <?php echo ucfirst($parent['status']); ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($parent['username']): ?>
                                <span class="badge badge-success"><?php echo htmlspecialchars($parent['username']); ?></span>
                            <?php else: ?>
                                <span class="badge badge-secondary">No Account</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="<?php echo SITE_URL; ?>/modules/students/view_parent.php?id=<?php echo $parent['id']; ?>" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i> View
                            </a>
                            <a href="<?php echo SITE_URL; ?>/modules/students/edit_parent.php?id=<?php echo $parent['id']; ?>" class="btn btn-sm btn-primary">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="<?php echo SITE_URL; ?>/modules/students/delete_parent.php?id=<?php echo $parent['id']; ?>" 
                               class="btn btn-sm btn-danger" 
                               onclick="return confirm('Are you sure you want to delete this parent? This will also delete the login account if exists.')">
                                <i class="fas fa-trash"></i> Delete
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
        <p class="text-center text-muted">No parents found</p>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>

