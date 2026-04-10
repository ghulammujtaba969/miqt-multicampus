<?php
/**
 * Weekly Report
 * MIQT System
 */

require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../includes/functions.php';

$pageTitle = 'Weekly Report';

// Get selected week (default to current week)
$selected_date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
$week_start = date('Y-m-d', strtotime('monday this week', strtotime($selected_date)));
$week_end = date('Y-m-d', strtotime('sunday this week', strtotime($selected_date)));

// Get class filter
$class_id = isset($_GET['class']) ? (int)$_GET['class'] : 0;

// Get classes for dropdown
$sql = "SELECT * FROM classes WHERE status = 'active' ORDER BY class_name";
$stmt = $db->prepare($sql);
$stmt->execute();
$classes = $stmt->fetchAll();

// Student Attendance Summary
$where = "WHERE sa.attendance_date BETWEEN ? AND ?";
$params = [$week_start, $week_end];

if ($class_id > 0) {
    $where .= " AND s.class_id = ?";
    $params[] = $class_id;
}

$sql = "SELECT
        COUNT(DISTINCT s.id) as total_students,
        COUNT(sa.id) as total_records,
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
$params = [$week_start, $week_end];

if ($class_id > 0) {
    $where .= " AND s.class_id = ?";
    $params[] = $class_id;
}

$sql = "SELECT
        COUNT(qp.id) as total_records,
        SUM(CASE WHEN qp.sabak_type = 'new_lesson' THEN 1 ELSE 0 END) as sabak_count,
        SUM(CASE WHEN qp.sabak_type = 'revision' THEN 1 ELSE 0 END) as sabqi_count,
        SUM(CASE WHEN qp.sabak_type = 'manzil' THEN 1 ELSE 0 END) as manzil_count
        FROM quran_progress qp
        JOIN students s ON qp.student_id = s.id
        $where";
$stmt = $db->prepare($sql);
$stmt->execute($params);
$progress_stats = $stmt->fetch();

// Daily breakdown
$daily_data = [];
for ($i = 0; $i < 7; $i++) {
    $date = date('Y-m-d', strtotime($week_start . " +$i days"));
    $day_name = date('l', strtotime($date));

    // Attendance for this day
    $where = "WHERE sa.attendance_date = ?";
    $params = [$date];

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
    $day_attendance = $stmt->fetch();

    // Progress for this day
    $where = "WHERE qp.progress_date = ?";
    $params = [$date];

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
    $day_progress = $stmt->fetch();

    $daily_data[] = [
        'date' => $date,
        'day' => $day_name,
        'attendance_total' => $day_attendance['total'] ?? 0,
        'attendance_present' => $day_attendance['present'] ?? 0,
        'progress_count' => $day_progress['count'] ?? 0
    ];
}

require_once '../../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title"><i class="fas fa-calendar-week"></i> Weekly Report</h1>
    <p class="subtitle">Week: <?php echo formatDate($week_start); ?> to <?php echo formatDate($week_end); ?></p>
</div>

<!-- Filters -->
<div class="dashboard-card mb-20">
    <div class="card-body">
        <form method="GET" action="" class="form-row">
            <div class="form-group">
                <label for="date">Select Week</label>
                <input type="date" name="date" id="date" class="form-control"
                       value="<?php echo $selected_date; ?>" onchange="this.form.submit()">
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
                <a href="weekly_report.php" class="btn btn-secondary">
                    <i class="fas fa-redo"></i> Reset
                </a>
                <button type="button" class="btn btn-primary" onclick="window.print()">
                    <i class="fas fa-print"></i> Print
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Attendance Summary -->
<div class="stats-grid mb-20">
    <div class="stat-card stat-primary">
        <div class="stat-icon"><i class="fas fa-calendar-check"></i></div>
        <div class="stat-info">
            <h3><?php echo $attendance_stats['total_records'] ?? 0; ?></h3>
            <p>Total Attendance Records</p>
        </div>
    </div>

    <div class="stat-card stat-success">
        <div class="stat-icon"><i class="fas fa-check"></i></div>
        <div class="stat-info">
            <h3><?php echo $attendance_stats['present_count'] ?? 0; ?></h3>
            <p>Present</p>
        </div>
    </div>

    <div class="stat-card stat-danger">
        <div class="stat-icon"><i class="fas fa-times"></i></div>
        <div class="stat-info">
            <h3><?php echo $attendance_stats['absent_count'] ?? 0; ?></h3>
            <p>Absent</p>
        </div>
    </div>

    <div class="stat-card stat-info">
        <div class="stat-icon"><i class="fas fa-book-quran"></i></div>
        <div class="stat-info">
            <h3><?php echo $progress_stats['total_records'] ?? 0; ?></h3>
            <p>Progress Records</p>
        </div>
    </div>
</div>

<!-- Progress Summary -->
<div class="dashboard-card mb-20">
    <div class="card-header">
        <h3><i class="fas fa-chart-pie"></i> Quran Progress Summary</h3>
    </div>
    <div class="card-body">
        <div class="form-row">
            <div class="form-group">
                <strong>New Lessons (Sabak):</strong>
                <span class="badge badge-primary" style="font-size: 18px;">
                    <?php echo $progress_stats['sabak_count'] ?? 0; ?>
                </span>
            </div>
            <div class="form-group">
                <strong>Revisions (Sabqi):</strong>
                <span class="badge badge-info" style="font-size: 18px;">
                    <?php echo $progress_stats['sabqi_count'] ?? 0; ?>
                </span>
            </div>
            <div class="form-group">
                <strong>Manzil (Complete Revision):</strong>
                <span class="badge badge-success" style="font-size: 18px;">
                    <?php echo $progress_stats['manzil_count'] ?? 0; ?>
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Daily Breakdown -->
<div class="dashboard-card">
    <div class="card-header">
        <h3><i class="fas fa-table"></i> Daily Breakdown</h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Day</th>
                        <th>Date</th>
                        <th>Total Attendance</th>
                        <th>Present</th>
                        <th>Attendance %</th>
                        <th>Progress Records</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($daily_data as $day): ?>
                    <tr>
                        <td><strong><?php echo $day['day']; ?></strong></td>
                        <td><?php echo formatDate($day['date']); ?></td>
                        <td><?php echo $day['attendance_total']; ?></td>
                        <td><?php echo $day['attendance_present']; ?></td>
                        <td>
                            <?php
                            $percentage = $day['attendance_total'] > 0
                                ? round(($day['attendance_present'] / $day['attendance_total']) * 100, 2)
                                : 0;
                            echo $percentage . '%';
                            ?>
                        </td>
                        <td><?php echo $day['progress_count']; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>
