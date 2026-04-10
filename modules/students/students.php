<?php
/**
 * Students List
 * MIQT System
 */

require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../includes/functions.php';

$pageTitle = 'All Students';

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * RECORDS_PER_PAGE;

// Search
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$class_filter = isset($_GET['class']) ? sanitize($_GET['class']) : '';

// Build query
$where = "WHERE s.status = 'active'";
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

$sql = "SELECT COUNT(*) AS c FROM students WHERE status = 'active'";
$stmt = $db->prepare($sql);
$stmt->execute();
$stat_all_active = (int) $stmt->fetch()['c'];

$sql = "SELECT COUNT(*) AS c FROM students WHERE status = 'active' AND MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())";
$stmt = $db->prepare($sql);
$stmt->execute();
$stat_new_month = (int) $stmt->fetch()['c'];

$sql = "SELECT COUNT(*) AS c FROM classes WHERE status = 'active'";
$stmt = $db->prepare($sql);
$stmt->execute();
$stat_class_count = (int) $stmt->fetch()['c'];

$start_row = $total > 0 ? $offset + 1 : 0;
$end_row = min($offset + count($students), $total);

require_once '../../includes/header.php';
?>

<div class="miqt-stats-strip">
    <div class="miqt-ss-card">
        <div class="ss-ic"><i class="fas fa-user-graduate"></i></div>
        <div>
            <div class="ss-num"><?php echo $stat_all_active; ?></div>
            <div class="ss-lbl">Total Students</div>
        </div>
    </div>
    <div class="miqt-ss-card">
        <div class="ss-ic"><i class="fas fa-check-circle"></i></div>
        <div>
            <div class="ss-num"><?php echo $total; ?></div>
            <div class="ss-lbl">Matching filter</div>
        </div>
    </div>
    <div class="miqt-ss-card">
        <div class="ss-ic"><i class="fas fa-user-plus"></i></div>
        <div>
            <div class="ss-num"><?php echo $stat_new_month; ?></div>
            <div class="ss-lbl">New this month</div>
        </div>
    </div>
    <div class="miqt-ss-card">
        <div class="ss-ic"><i class="fas fa-school"></i></div>
        <div>
            <div class="ss-num"><?php echo $stat_class_count; ?></div>
            <div class="ss-lbl">Classes</div>
        </div>
    </div>
</div>

<form method="GET" action="" class="miqt-controls">
    <div class="ctrl-search" style="position:relative;flex:1;min-width:220px;">
        <span style="position:absolute;left:11px;top:50%;transform:translateY(-50%);color:var(--miqt-muted, #6b7d8f);"><i class="fas fa-search"></i></span>
        <input type="text" name="search" class="form-control" style="padding-left:36px;" placeholder="Search by ID, Name, or Admission No"
               value="<?php echo htmlspecialchars($search); ?>">
    </div>
    <select name="class" class="form-control" style="max-width:200px;">
        <option value="">All Classes</option>
        <?php foreach ($classes as $class): ?>
        <option value="<?php echo $class['id']; ?>" <?php echo ($class_filter == $class['id']) ? 'selected' : ''; ?>>
            <?php echo htmlspecialchars($class['class_name']); ?>
        </option>
        <?php endforeach; ?>
    </select>
    <div class="ctrl-btns d-flex flex-wrap gap-2 align-items-center">
        <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search"></i> Search</button>
        <a href="students.php" class="btn btn-secondary btn-sm"><i class="fas fa-redo"></i> Reset</a>
        <?php if (hasPermission(['principal', 'vice_principal', 'coordinator'])): ?>
        <a href="add_student.php" class="btn btn-success btn-sm"><i class="fas fa-plus"></i> Add Student</a>
        <a href="import_students.php" class="btn btn-warning btn-sm"><i class="fas fa-file-import"></i> Import</a>
        <?php endif; ?>
        <a href="export_students.php?status=active&search=<?php echo urlencode($search); ?>&class=<?php echo urlencode($class_filter); ?>" class="btn btn-info btn-sm"><i class="fas fa-file-export"></i> Export</a>
    </div>
</form>

<div class="miqt-table-card">
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
                        <?php
                        $status = strtolower($student['status'] ?? '');
                        $badgeClass = 'badge-secondary';
                        $useMiqtPill = ($status === 'active');
                        switch ($status) {
                            case 'active':
                                $badgeClass = 'badge-success';
                                break;
                            case 'inactive':
                                $badgeClass = 'badge-secondary';
                                break;
                            case 'graduated':
                            case 'alumni':
                                $badgeClass = 'badge-primary';
                                break;
                            case 'left':
                                $badgeClass = 'badge-warning';
                                break;
                            case 'expelled':
                                $badgeClass = 'badge-danger';
                                break;
                        }
                        ?>
                        <?php if ($useMiqtPill): ?>
                        <span class="miqt-status-badge"><?php echo ucfirst($student['status']); ?></span>
                        <?php else: ?>
                        <span class="badge <?php echo $badgeClass; ?>">
                            <?php echo ucfirst($student['status']); ?>
                        </span>
                        <?php endif; ?>
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

        <div class="miqt-pagination-wrap">
            <div class="pg-info">Showing <?php echo (int) $start_row; ?> – <?php echo (int) $end_row; ?> of <?php echo (int) $total; ?> students</div>
            <?php if ($total_pages > 1): ?>
            <div class="pagination" style="margin:0;">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&class=<?php echo urlencode($class_filter); ?>"
               class="<?php echo ($page == $i) ? 'active' : ''; ?>">
                <?php echo $i; ?>
            </a>
            <?php endfor; ?>
            </div>
            <?php endif; ?>
        </div>

        <?php else: ?>
        <p class="text-center text-muted p-4 mb-0">No students found</p>
        <?php endif; ?>
</div>

<?php require_once '../../includes/footer.php'; ?>
