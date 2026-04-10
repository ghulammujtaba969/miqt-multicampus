<?php
/**
 * Monthly Report
 * MIQT System
 */

require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../includes/functions.php';

$pageTitle = 'Monthly Report';

// Get selected month (default to current month)
$selected_month = isset($_GET['month']) ? $_GET['month'] : date('Y-m');
$month_start = $selected_month . '-01';
$month_end = date('Y-m-t', strtotime($month_start));

// Get class filter
$class_id = isset($_GET['class']) ? (int)$_GET['class'] : 0;

// Get classes for dropdown
$sql = "SELECT * FROM classes WHERE status = 'active' ORDER BY class_name";
$stmt = $db->prepare($sql);
$stmt->execute();
$classes = $stmt->fetchAll();

// Student Statistics
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
$params = [$month_start, $month_end];

if ($class_id > 0) {
    $where .= " AND s.class_id = ?";
    $params[] = $class_id;
}

$sql = "SELECT
        COUNT(*) as total_records,
        SUM(CASE WHEN sa.status = 'present' THEN 1 ELSE 0 END) as present_count,
        SUM(CASE WHEN sa.status = 'absent' THEN 1 ELSE 0 END) as absent_count,
        SUM(CASE WHEN sa.status = 'late' THEN 1 ELSE 0 END) as late_count,
        SUM(CASE WHEN sa.status = 'leave' THEN 1 ELSE 0 END) as leave_count
        FROM student_attendance sa
        JOIN students s ON sa.student_id = s.id
        $where";
$stmt = $db->prepare($sql);
$stmt->execute($params);
$attendance_stats = $stmt->fetch();

// Progress Summary
$where = "WHERE qp.progress_date BETWEEN ? AND ?";
$params = [$month_start, $month_end];

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

// Top Performers (by attendance)
$where = "WHERE sa.attendance_date BETWEEN ? AND ?";
$params = [$month_start, $month_end];

if ($class_id > 0) {
    $where .= " AND s.class_id = ?";
    $params[] = $class_id;
}

$sql = "SELECT s.student_id, s.first_name, s.last_name, c.class_name,
        COUNT(*) as total_days,
        SUM(CASE WHEN sa.status = 'present' THEN 1 ELSE 0 END) as present_days,
        ROUND((SUM(CASE WHEN sa.status = 'present' THEN 1 ELSE 0 END) / COUNT(*)) * 100, 2) as attendance_percentage
        FROM student_attendance sa
        JOIN students s ON sa.student_id = s.id
        LEFT JOIN classes c ON s.class_id = c.id
        $where
        GROUP BY s.id
        ORDER BY attendance_percentage DESC, present_days DESC
        LIMIT 10";
$stmt = $db->prepare($sql);
$stmt->execute($params);
$top_attendance = $stmt->fetchAll();

// Top Performers (by progress)
$where = "WHERE qp.progress_date BETWEEN ? AND ?";
$params = [$month_start, $month_end];

if ($class_id > 0) {
    $where .= " AND s.class_id = ?";
    $params[] = $class_id;
}

$sql = "SELECT s.student_id, s.first_name, s.last_name, c.class_name,
        COUNT(*) as total_progress,
        SUM(CASE WHEN qp.sabak_type = 'new_lesson' THEN 1 ELSE 0 END) as sabak_count
        FROM quran_progress qp
        JOIN students s ON qp.student_id = s.id
        LEFT JOIN classes c ON s.class_id = c.id
        $where
        GROUP BY s.id
        ORDER BY total_progress DESC, sabak_count DESC
        LIMIT 10";
$stmt = $db->prepare($sql);
$stmt->execute($params);
$top_progress = $stmt->fetchAll();

// Teacher Attendance
$sql = "SELECT
        COUNT(*) as total_records,
        SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present_count,
        SUM(CASE WHEN status = 'absent' THEN 1 ELSE 0 END) as absent_count
        FROM teacher_attendance
        WHERE attendance_date BETWEEN ? AND ?";
$stmt = $db->prepare($sql);
$stmt->execute([$month_start, $month_end]);
$teacher_attendance = $stmt->fetch();

$attendance_percentage = $attendance_stats['total_records'] > 0
    ? round(($attendance_stats['present_count'] / $attendance_stats['total_records']) * 100, 2)
    : 0;

require_once '../../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title"><i class="fas fa-calendar-alt"></i> Monthly Report</h1>
    <p class="subtitle">Month: <?php echo date('F Y', strtotime($month_start)); ?></p>
</div>

<!-- Filters -->
<div class="dashboard-card mb-20">
    <div class="card-body">
        <form method="GET" action="" class="form-row">
            <div class="form-group">
                <label for="month">Select Month</label>
                <input type="month" name="month" id="month" class="form-control"
                       value="<?php echo $selected_month; ?>" onchange="this.form.submit()">
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
                <a href="monthly_report.php" class="btn btn-secondary">
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
            <p>Attendance Rate</p>
        </div>
    </div>

    <div class="stat-card stat-info">
        <div class="stat-icon"><i class="fas fa-book-quran"></i></div>
        <div class="stat-info">
            <h3><?php echo $progress_stats['total_records'] ?? 0; ?></h3>
            <p>Progress Records</p>
        </div>
    </div>

    <div class="stat-card stat-warning">
        <div class="stat-icon"><i class="fas fa-chalkboard-teacher"></i></div>
        <div class="stat-info">
            <h3><?php echo $teacher_attendance['total_records'] ?? 0; ?></h3>
            <p>Teacher Attendance</p>
        </div>
    </div>
</div>

<!-- Detailed Statistics -->
<div class="dashboard-grid mb-20">
    <!-- Attendance Breakdown -->
    <div class="dashboard-card">
        <div class="card-header">
            <h3><i class="fas fa-chart-bar"></i> Attendance Breakdown</h3>
        </div>
        <div class="card-body">
            <table class="table">
                <tr>
                    <th>Status</th>
                    <th>Count</th>
                    <th>Percentage</th>
                </tr>
                <tr>
                    <td>Present</td>
                    <td><?php echo $attendance_stats['present_count'] ?? 0; ?></td>
                    <td>
                        <?php
                        $percentage = $attendance_stats['total_records'] > 0
                            ? round(($attendance_stats['present_count'] / $attendance_stats['total_records']) * 100, 2)
                            : 0;
                        echo $percentage . '%';
                        ?>
                    </td>
                </tr>
                <tr>
                    <td>Absent</td>
                    <td><?php echo $attendance_stats['absent_count'] ?? 0; ?></td>
                    <td>
                        <?php
                        $percentage = $attendance_stats['total_records'] > 0
                            ? round(($attendance_stats['absent_count'] / $attendance_stats['total_records']) * 100, 2)
                            : 0;
                        echo $percentage . '%';
                        ?>
                    </td>
                </tr>
                <tr>
                    <td>Late</td>
                    <td><?php echo $attendance_stats['late_count'] ?? 0; ?></td>
                    <td>
                        <?php
                        $percentage = $attendance_stats['total_records'] > 0
                            ? round(($attendance_stats['late_count'] / $attendance_stats['total_records']) * 100, 2)
                            : 0;
                        echo $percentage . '%';
                        ?>
                    </td>
                </tr>
                <tr>
                    <td>Leave</td>
                    <td><?php echo $attendance_stats['leave_count'] ?? 0; ?></td>
                    <td>
                        <?php
                        $percentage = $attendance_stats['total_records'] > 0
                            ? round(($attendance_stats['leave_count'] / $attendance_stats['total_records']) * 100, 2)
                            : 0;
                        echo $percentage . '%';
                        ?>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Progress Breakdown -->
    <div class="dashboard-card">
        <div class="card-header">
            <h3><i class="fas fa-chart-pie"></i> Progress Breakdown</h3>
        </div>
        <div class="card-body">
            <table class="table">
                <tr>
                    <th>Type</th>
                    <th>Count</th>
                </tr>
                <tr>
                    <td>New Lessons (Sabak)</td>
                    <td><span class="badge badge-primary"><?php echo $progress_stats['sabak_count'] ?? 0; ?></span></td>
                </tr>
                <tr>
                    <td>Revisions (Sabqi)</td>
                    <td><span class="badge badge-info"><?php echo $progress_stats['sabqi_count'] ?? 0; ?></span></td>
                </tr>
                <tr>
                    <td>Manzil (Complete)</td>
                    <td><span class="badge badge-success"><?php echo $progress_stats['manzil_count'] ?? 0; ?></span></td>
                </tr>
                <tr>
                    <th>Total</th>
                    <th><span class="badge badge-secondary"><?php echo $progress_stats['total_records'] ?? 0; ?></span></th>
                </tr>
            </table>
        </div>
    </div>
</div>

<!-- Top Performers -->
<div class="dashboard-grid">
    <!-- Top by Attendance -->
    <div class="dashboard-card">
        <div class="card-header">
            <h3><i class="fas fa-trophy"></i> Top 10 by Attendance</h3>
        </div>
        <div class="card-body">
            <?php if (count($top_attendance) > 0): ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Student</th>
                            <th>Class</th>
                            <th>Present/Total</th>
                            <th>%</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $rank = 1; foreach ($top_attendance as $student): ?>
                        <tr>
                            <td><strong><?php echo $rank++; ?></strong></td>
                            <td><?php echo $student['first_name'] . ' ' . $student['last_name']; ?></td>
                            <td><?php echo $student['class_name'] ?? 'N/A'; ?></td>
                            <td><?php echo $student['present_days'] . '/' . $student['total_days']; ?></td>
                            <td><span class="badge badge-success"><?php echo $student['attendance_percentage']; ?>%</span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <p class="text-center text-muted">No attendance data available</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Top by Progress -->
    <div class="dashboard-card">
        <div class="card-header">
            <h3><i class="fas fa-star"></i> Top 10 by Progress</h3>
        </div>
        <div class="card-body">
            <?php if (count($top_progress) > 0): ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Student</th>
                            <th>Class</th>
                            <th>Total Records</th>
                            <th>New Lessons</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $rank = 1; foreach ($top_progress as $student): ?>
                        <tr>
                            <td><strong><?php echo $rank++; ?></strong></td>
                            <td><?php echo $student['first_name'] . ' ' . $student['last_name']; ?></td>
                            <td><?php echo $student['class_name'] ?? 'N/A'; ?></td>
                            <td><span class="badge badge-info"><?php echo $student['total_progress']; ?></span></td>
                            <td><span class="badge badge-primary"><?php echo $student['sabak_count']; ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <p class="text-center text-muted">No progress data available</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>
