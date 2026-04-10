<?php
/**
 * Student Behaviour Reports
 * MIQT System
 */

require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../includes/functions.php';

$allowedRoles = ['principal', 'vice_principal', 'coordinator', 'teacher', 'admin'];
if (!hasPermission($allowedRoles)) {
    setFlash('danger', 'You do not have permission to view behaviour reports.');
    redirect(SITE_URL . '/modules/dashboard/index.php');
}

$pageTitle = 'Student Behaviour Reports';

// Filters
$reportType = isset($_GET['type']) ? sanitize($_GET['type']) : '';
$classId    = isset($_GET['class_id']) ? (int)$_GET['class_id'] : 0;
$studentQ   = isset($_GET['student']) ? trim(sanitize($_GET['student'])) : '';
$from       = isset($_GET['from']) ? sanitize($_GET['from']) : '';
$to         = isset($_GET['to']) ? sanitize($_GET['to']) : '';

$where   = 'WHERE 1=1';
$params  = [];

if (in_array($reportType, ['internal', 'parent', 'ptm'], true)) {
    $where   .= ' AND b.report_type = ?';
    $params[] = $reportType;
}

if ($classId > 0) {
    $where   .= ' AND b.class_id = ?';
    $params[] = $classId;
}

if ($studentQ !== '') {
    $where   .= ' AND (s.student_id LIKE ? OR s.first_name LIKE ? OR s.last_name LIKE ?)';
    $term     = '%' . $studentQ . '%';
    $params[] = $term;
    $params[] = $term;
    $params[] = $term;
}

if ($from !== '' && $to !== '') {
    $where   .= ' AND b.report_date BETWEEN ? AND ?';
    $params[] = $from;
    $params[] = $to;
} elseif ($from !== '') {
    $where   .= ' AND b.report_date >= ?';
    $params[] = $from;
} elseif ($to !== '') {
    $where   .= ' AND b.report_date <= ?';
    $params[] = $to;
}

// Fetch reports
$sql = "SELECT b.*, s.student_id AS student_code, s.first_name, s.last_name,
               c.class_name, u.full_name AS created_by_name
        FROM student_behavior_reports b
        JOIN students s ON b.student_id = s.id
        LEFT JOIN classes c ON b.class_id = c.id
        LEFT JOIN users u ON b.created_by = u.id
        $where
        ORDER BY b.report_date DESC, c.class_name, s.first_name, s.last_name";
$stmt = $db->prepare($sql);
$stmt->execute($params);
$reports = $stmt->fetchAll();

// Classes for filter
$sql = "SELECT id, class_name FROM classes WHERE status = 'active' ORDER BY class_name";
$stmt = $db->prepare($sql);
$stmt->execute();
$classes = $stmt->fetchAll();

require_once '../../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title"><i class="fas fa-user-check"></i> Student Behaviour Reports</h1>
    <p class="subtitle">Internal, Parent and PTM reports by student and class.</p>
</div>

<div class="dashboard-card mb-20">
    <div class="card-body">
        <form method="GET" action="" class="form-row">
            <div class="form-group">
                <label for="type" class="form-label">Report Type</label>
                <select name="type" id="type" class="form-control">
                    <option value="">All Types</option>
                    <option value="internal" <?php echo $reportType === 'internal' ? 'selected' : ''; ?>>Internal</option>
                    <option value="parent" <?php echo $reportType === 'parent' ? 'selected' : ''; ?>>For Parents</option>
                    <option value="ptm" <?php echo $reportType === 'ptm' ? 'selected' : ''; ?>>PTM Report</option>
                </select>
            </div>
            <div class="form-group">
                <label for="class_id" class="form-label">Class</label>
                <select name="class_id" id="class_id" class="form-control">
                    <option value="0">All Classes</option>
                    <?php foreach ($classes as $class): ?>
                    <option value="<?php echo $class['id']; ?>" <?php echo $classId === (int)$class['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($class['class_name']); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="student" class="form-label">Student</label>
                <input type="text" name="student" id="student" class="form-control"
                       placeholder="Search by ID or name"
                       value="<?php echo htmlspecialchars($studentQ); ?>">
            </div>
            <div class="form-group">
                <label class="form-label">From</label>
                <input type="date" name="from" class="form-control" value="<?php echo htmlspecialchars($from); ?>">
            </div>
            <div class="form-group">
                <label class="form-label">To</label>
                <input type="date" name="to" class="form-control" value="<?php echo htmlspecialchars($to); ?>">
            </div>
            <div class="form-group align-self-end">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Filter
                </button>
                <a href="behavior_reports.php" class="btn btn-secondary">
                    <i class="fas fa-redo"></i> Reset
                </a>
                <?php if (hasPermission($allowedRoles)): ?>
                <a href="add_behavior_report.php" class="btn btn-success">
                    <i class="fas fa-plus"></i> Add Report
                </a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<div class="dashboard-card">
    <div class="card-body">
        <?php if (count($reports) > 0): ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Student</th>
                    <th>Class</th>
                    <th>Summary</th>
                    <th>Created By</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reports as $row): ?>
                <tr>
                    <td><?php echo formatDate($row['report_date']); ?></td>
                    <td><?php echo ucfirst($row['report_type']); ?></td>
                    <td>
                        <?php
                        echo htmlspecialchars($row['student_code']) . ' - ' .
                             htmlspecialchars($row['first_name'] . ' ' . $row['last_name']);
                        ?>
                    </td>
                    <td><?php echo htmlspecialchars($row['class_name'] ?? 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars($row['summary']); ?></td>
                    <td><?php echo htmlspecialchars($row['created_by_name'] ?? 'System'); ?></td>
                </tr>
                <?php if (!empty($row['details'])): ?>
                <tr>
                    <td></td>
                    <td colspan="5">
                        <strong>Details:</strong><br>
                        <?php echo nl2br(htmlspecialchars($row['details'])); ?>
                    </td>
                </tr>
                <?php endif; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <p class="text-center text-muted">No behaviour reports found for the selected criteria.</p>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>

