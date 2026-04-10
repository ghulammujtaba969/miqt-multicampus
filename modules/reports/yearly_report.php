<?php
/**
 * Yearly Report
 * MIQT System
 */

require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../includes/functions.php';

$pageTitle = 'Yearly Report';

// Get selected year (default to current year)
$selected_year = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');
$year_start = $selected_year . '-01-01';
$year_end = $selected_year . '-12-31';

// Get class filter
$class_id = isset($_GET['class']) ? (int)$_GET['class'] : 0;

// Get classes for dropdown
$sql = "SELECT * FROM classes WHERE status = 'active' ORDER BY class_name";
$stmt = $db->prepare($sql);
$stmt->execute();
$classes = $stmt->fetchAll();

// Overall Statistics
$where = "";
$params = [];
if ($class_id > 0) {
    $where = "WHERE class_id = ?";
    $params = [$class_id];
}

$sql = "SELECT COUNT(*) as total_students FROM students $where";
$stmt = $db->prepare($sql);
$stmt->execute($params);
$total_students = $stmt->fetchColumn();

// Attendance Summary
$where = "WHERE sa.attendance_date BETWEEN ? AND ?";
$params = [$year_start, $year_end];

if ($class_id > 0) {
    $where .= " AND s.class_id = ?";
    $params[] = $class_id;
}

$sql = "SELECT
        COUNT(*) as total_records,
        SUM(CASE WHEN sa.status = 'present' THEN 1 ELSE 0 END) as present_count,
        SUM(CASE WHEN sa.status = 'absent' THEN 1 ELSE 0 END) as absent_count
        FROM student_attendance sa
        JOIN students s ON sa.student_id = s.id
        $where";
$stmt = $db->prepare($sql);
$stmt->execute($params);
$attendance_stats = $stmt->fetch();

// Progress Summary
$where = "WHERE qp.progress_date BETWEEN ? AND ?";
$params = [$year_start, $year_end];

if ($class_id > 0) {
    $where .= " AND s.class_id = ?";
    $params[] = $class_id;
}

$sql = "SELECT
        COUNT(*) as total_records,
        SUM(CASE WHEN qp.sabak_type = 'new_lesson' THEN 1 ELSE 0 END) as sabak_count,
        SUM(CASE WHEN qp.sabak_type = 'revision' THEN 1 ELSE 0 END) as sabqi_count,
        SUM(CASE WHEN qp.sabak_type = 'manzil' THEN 1 ELSE 0 END) as manzil_count
        FROM quran_progress qp
        JOIN students s ON qp.student_id = s.id
        $where";
$stmt = $db->prepare($sql);
$stmt->execute($params);
$progress_stats = $stmt->fetch();

// Monthly breakdown
$monthly_data = [];
for ($month = 1; $month <= 12; $month++) {
    $month_start = sprintf('%04d-%02d-01', $selected_year, $month);
    $month_end = date('Y-m-t', strtotime($month_start));
    $month_name = date('F', strtotime($month_start));

    // Attendance
    $where = "WHERE sa.attendance_date BETWEEN ? AND ?";
    $params = [$month_start, $month_end];

    if ($class_id > 0) {
        $where .= " AND s.class_id = ?";
        $params[] = $class_id;
    }

    $sql = "SELECT
            COUNT(*) as total,
            SUM(CASE WHEN sa.status = 'present' THEN 1 ELSE 0 END) as present
            FROM student_attendance sa
            JOIN students s ON sa.student_id = s.id
            $where";
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $month_attendance = $stmt->fetch();

    // Progress
    $where = "WHERE qp.progress_date BETWEEN ? AND ?";
    $params = [$month_start, $month_end];

    if ($class_id > 0) {
        $where .= " AND s.class_id = ?";
        $params[] = $class_id;
    }

    $sql = "SELECT COUNT(*) as count
            FROM quran_progress qp
            JOIN students s ON qp.student_id = s.id
            $where";
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $month_progress = $stmt->fetch();

    $monthly_data[] = [
        'month' => $month_name,
        'attendance_total' => $month_attendance['total'] ?? 0,
        'attendance_present' => $month_attendance['present'] ?? 0,
        'progress_count' => $month_progress['count'] ?? 0
    ];
}

// Student enrollment trends
$sql = "SELECT
        DATE_FORMAT(admission_date, '%Y-%m') as month,
        COUNT(*) as count
        FROM students
        WHERE YEAR(admission_date) = ?
        GROUP BY month
        ORDER BY month";
$stmt = $db->prepare($sql);
$stmt->execute([$selected_year]);
$enrollment_data = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

$attendance_percentage = $attendance_stats['total_records'] > 0
    ? round(($attendance_stats['present_count'] / $attendance_stats['total_records']) * 100, 2)
    : 0;

require_once '../../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title"><i class="fas fa-calendar"></i> Yearly Report</h1>
    <p class="subtitle">Year: <?php echo $selected_year; ?></p>
</div>

<!-- Filters -->
<div class="dashboard-card mb-20">
    <div class="card-body">
        <form method="GET" action="" class="form-row">
            <div class="form-group">
                <label for="year">Select Year</label>
                <select name="year" id="year" class="form-control" onchange="this.form.submit()">
                    <?php
                    $current_year = date('Y');
                    for ($y = $current_year; $y >= $current_year - 5; $y--):
                    ?>
                    <option value="<?php echo $y; ?>" <?php echo ($selected_year == $y) ? 'selected' : ''; ?>>
                        <?php echo $y; ?>
                    </option>
                    <?php endfor; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="class">Class</label>
                <select name="class" id="class" class="form-control" onchange="this.form.submit()">
                    <option value="0">All Classes</option>
                    <?php foreach ($classes as $class): ?>
                    <option value="<?php echo $class['id']; ?>" <?php echo ($class_id == $class['id']) ? 'selected' : ''; ?>>
                        <?php echo $class['class_name']; ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>&nbsp;</label>
                <a href="yearly_report.php" class="btn btn-secondary">
                    <i class="fas fa-redo"></i> Reset
                </a>
                <button type="button" class="btn btn-primary" onclick="window.print()">
                    <i class="fas fa-print"></i> Print
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Overall Statistics -->
<div class="stats-grid mb-20">
    <div class="stat-card stat-primary">
        <div class="stat-icon"><i class="fas fa-users"></i></div>
        <div class="stat-info">
            <h3><?php echo $total_students; ?></h3>
            <p>Total Students</p>
        </div>
    </div>

    <div class="stat-card stat-success">
        <div class="stat-icon"><i class="fas fa-percentage"></i></div>
        <div class="stat-info">
            <h3><?php echo $attendance_percentage; ?>%</h3>
            <p>Annual Attendance Rate</p>
        </div>
    </div>

    <div class="stat-card stat-info">
        <div class="stat-icon"><i class="fas fa-book-quran"></i></div>
        <div class="stat-info">
            <h3><?php echo $progress_stats['total_records'] ?? 0; ?></h3>
            <p>Total Progress Records</p>
        </div>
    </div>

    <div class="stat-card stat-warning">
        <div class="stat-icon"><i class="fas fa-graduation-cap"></i></div>
        <div class="stat-info">
            <h3><?php echo $progress_stats['manzil_count'] ?? 0; ?></h3>
            <p>Manzil Completed</p>
        </div>
    </div>
</div>

<!-- Progress Summary -->
<div class="dashboard-card mb-20">
    <div class="card-header">
        <h3><i class="fas fa-chart-pie"></i> Quran Progress Summary</h3>
    </div>
    <div class="card-body">
        <div class="stats-grid">
            <div class="stat-card stat-primary">
                <div class="stat-info">
                    <h3><?php echo $progress_stats['sabak_count'] ?? 0; ?></h3>
                    <p>New Lessons (Sabak)</p>
                </div>
            </div>

            <div class="stat-card stat-info">
                <div class="stat-info">
                    <h3><?php echo $progress_stats['sabqi_count'] ?? 0; ?></h3>
                    <p>Revisions (Sabqi)</p>
                </div>
            </div>

            <div class="stat-card stat-success">
                <div class="stat-info">
                    <h3><?php echo $progress_stats['manzil_count'] ?? 0; ?></h3>
                    <p>Manzil (Complete Revision)</p>
                </div>
            </div>

            <div class="stat-card stat-secondary">
                <div class="stat-info">
                    <h3><?php echo $progress_stats['total_records'] ?? 0; ?></h3>
                    <p>Total Progress Records</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Monthly Breakdown -->
<div class="dashboard-card">
    <div class="card-header">
        <h3><i class="fas fa-table"></i> Monthly Breakdown</h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Month</th>
                        <th>Total Attendance</th>
                        <th>Present</th>
                        <th>Attendance %</th>
                        <th>Progress Records</th>
                        <th>New Enrollments</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $month_num = 1;
                    foreach ($monthly_data as $data):
                        $enrollment_month = sprintf('%04d-%02d', $selected_year, $month_num);
                        $enrollments = $enrollment_data[$enrollment_month] ?? 0;
                    ?>
                    <tr>
                        <td><strong><?php echo $data['month']; ?></strong></td>
                        <td><?php echo $data['attendance_total']; ?></td>
                        <td><?php echo $data['attendance_present']; ?></td>
                        <td>
                            <?php
                            $percentage = $data['attendance_total'] > 0
                                ? round(($data['attendance_present'] / $data['attendance_total']) * 100, 2)
                                : 0;
                            echo $percentage . '%';
                            ?>
                        </td>
                        <td><?php echo $data['progress_count']; ?></td>
                        <td><?php echo $enrollments; ?></td>
                    </tr>
                    <?php
                    $month_num++;
                    endforeach;
                    ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th>Total</th>
                        <th><?php echo $attendance_stats['total_records'] ?? 0; ?></th>
                        <th><?php echo $attendance_stats['present_count'] ?? 0; ?></th>
                        <th><?php echo $attendance_percentage; ?>%</th>
                        <th><?php echo $progress_stats['total_records'] ?? 0; ?></th>
                        <th><?php echo array_sum($enrollment_data); ?></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>
