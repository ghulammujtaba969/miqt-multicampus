<?php
/**
 * Left / Expelled Students List
 * MIQT System
 */

require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../includes/functions.php';

$pageTitle = 'Left / Expelled';

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * RECORDS_PER_PAGE;

// Search
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$class_filter = isset($_GET['class']) ? sanitize($_GET['class']) : '';

// Build query for Left/Expelled
$where = "WHERE s.status IN ('left','expelled')";
$params = [];

if (!empty($search)) {
    $where .= " AND (s.student_id LIKE ? OR s.first_name LIKE ? OR s.last_name LIKE ? OR s.admission_no LIKE ?)";
    $searchTerm = "%$search%";
    $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
}

if (!empty($class_filter)) {
    $where .= " AND s.class_id = ?";
    $params[] = $class_filter;
}

// Get total records
$sql = "SELECT COUNT(*) as total FROM students s $where";
$stmt = $db->prepare($sql);
$stmt->execute($params);
$total = $stmt->fetch()['total'];
$total_pages = ceil($total / RECORDS_PER_PAGE);

// Get students
$sql = "SELECT s.*, c.class_name FROM students s
        LEFT JOIN classes c ON s.class_id = c.id
        $where
        ORDER BY s.created_at DESC
        LIMIT " . RECORDS_PER_PAGE . " OFFSET $offset";
$stmt = $db->prepare($sql);
$stmt->execute($params);
$students = $stmt->fetchAll();

// Get classes for filter
$sql = "SELECT * FROM classes WHERE status = 'active' ORDER BY class_name";
$stmt = $db->prepare($sql);
$stmt->execute();
$classes = $stmt->fetchAll();

require_once '../../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title"><i class="fas fa-user-slash"></i> Left / Expelled</h1>
    <p class="subtitle">Total Left/Expelled: <?php echo $total; ?></p>
    <p><a href="<?php echo SITE_URL; ?>/modules/students/students.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back to Active Students</a></p>
</div>

<div class="dashboard-card mb-20">
    <div class="card-body">
        <form method="GET" action="" class="form-row">
            <div class="form-group">
                <input type="text" name="search" class="form-control" placeholder="Search by ID, Name, or Admission No"
                       value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <div class="form-group">
                <select name="class" class="form-control">
                    <option value="">All Classes</option>
                    <?php foreach ($classes as $class): ?>
                    <option value="<?php echo $class['id']; ?>" <?php echo ($class_filter == $class['id']) ? 'selected' : ''; ?>>
                        <?php echo $class['class_name']; ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Search
                </button>
                <a href="left_expelled.php" class="btn btn-secondary">
                    <i class="fas fa-redo"></i> Reset
                </a>
            </div>
        </form>
    </div>
</div>

<div class="dashboard-card">
    <div class="card-body">
        <?php if (count($students) > 0): ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Photo</th>
                    <th>Student ID</th>
                    <th>Name</th>
                    <th>Father Name</th>
                    <th>Class</th>
                    <th>Admission Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($students as $student): ?>
                <tr>
                    <td>
                        <?php if ($student['photo']): ?>
                        <img src="<?php echo SITE_URL . '/uploads/photos/' . $student['photo']; ?>"
                             alt="Photo" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                        <?php else: ?>
                        <i class="fas fa-user-circle" style="font-size: 40px; color: #ccc;"></i>
                        <?php endif; ?>
                    </td>
                    <td><?php echo $student['student_id']; ?></td>
                    <td><?php echo $student['first_name'] . ' ' . $student['last_name']; ?></td>
                    <td><?php echo $student['father_name']; ?></td>
                    <td><?php echo $student['class_name'] ?? 'N/A'; ?></td>
                    <td><?php echo formatDate($student['admission_date']); ?></td>
                    <td>
                        <span class="badge badge-danger">
                            <?php echo ucfirst($student['status']); ?>
                        </span>
                    </td>
                    <td>
                        <a href="view_student.php?id=<?php echo $student['id']; ?>" class="btn btn-sm btn-info"
                           title="View Profile">
                            <i class="fas fa-eye"></i>
                        </a>
                        <?php if (hasPermission(['principal', 'vice_principal', 'coordinator'])): ?>
                        <a href="edit_student.php?id=<?php echo $student['id']; ?>" class="btn btn-sm btn-primary"
                           title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php if ($total_pages > 1): ?>
        <div class="pagination">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&class=<?php echo urlencode($class_filter); ?>"
               class="<?php echo ($page == $i) ? 'active' : ''; ?>">
                <?php echo $i; ?>
            </a>
            <?php endfor; ?>
        </div>
        <?php endif; ?>

        <?php else: ?>
        <p class="text-center text-muted">No left/expelled students found</p>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>

