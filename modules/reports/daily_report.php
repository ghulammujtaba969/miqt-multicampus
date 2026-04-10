<?php
/**
 * Daily Report
 * MIQT System
 */

require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../includes/functions.php';

$pageTitle = 'Daily Report';

$report_date = isset($_GET['date']) ? sanitize($_GET['date']) : date('Y-m-d');
$class_filter = isset($_GET['class']) ? sanitize($_GET['class']) : '';

// Get classes
$sql = "SELECT * FROM classes WHERE status = 'active' ORDER BY class_name";
$stmt = $db->prepare($sql);
$stmt->execute();
$classes = $stmt->fetchAll();

// Attendance Summary
$where_class = $class_filter ? "AND s.class_id = $class_filter" : "";

$sql = "SELECT
        COUNT(DISTINCT sa.student_id) as total_marked,
        SUM(CASE WHEN sa.status = 'present' THEN 1 ELSE 0 END) as present,
        SUM(CASE WHEN sa.status = 'absent' THEN 1 ELSE 0 END) as absent,
        SUM(CASE WHEN sa.status = 'late' THEN 1 ELSE 0 END) as late,
        SUM(CASE WHEN sa.status = 'leave' THEN 1 ELSE 0 END) as on_leave
        FROM student_attendance sa
        INNER JOIN students s ON sa.student_id = s.id
        WHERE sa.attendance_date = ? $where_class";
$stmt = $db->prepare($sql);
$stmt->execute([$report_date]);
$attendance_stats = $stmt->fetch();

// Progress Summary
$sql = "SELECT
        COUNT(*) as total_records,
        SUM(CASE WHEN sabak_type = 'new_lesson' THEN 1 ELSE 0 END) as sabak_count,
        SUM(CASE WHEN sabak_type = 'revision' THEN 1 ELSE 0 END) as sabqi_count,
        SUM(CASE WHEN sabak_type = 'manzil' THEN 1 ELSE 0 END) as manzil_count
        FROM quran_progress qp
        INNER JOIN students s ON qp.student_id = s.id
        WHERE qp.progress_date = ? $where_class";
$stmt = $db->prepare($sql);
$stmt->execute([$report_date]);
$progress_stats = $stmt->fetch();

// Detailed Attendance
$sql = "SELECT s.student_id, s.first_name, s.last_name, c.class_name,
        sa.status, sa.remarks
        FROM student_attendance sa
        INNER JOIN students s ON sa.student_id = s.id
        LEFT JOIN classes c ON s.class_id = c.id
        WHERE sa.attendance_date = ? $where_class
        ORDER BY c.class_name, s.first_name";
$stmt = $db->prepare($sql);
$stmt->execute([$report_date]);
$attendance_details = $stmt->fetchAll();

// Detailed Progress
$sql = "SELECT s.student_id, s.first_name, s.last_name, c.class_name,
        qp.sabak_type, qp.surah_name, qp.page_from, qp.page_to,
        qp.performance, qp.remarks
        FROM quran_progress qp
        INNER JOIN students s ON qp.student_id = s.id
        LEFT JOIN classes c ON s.class_id = c.id
        WHERE qp.progress_date = ? $where_class
        ORDER BY c.class_name, s.first_name";
$stmt = $db->prepare($sql);
$stmt->execute([$report_date]);
$progress_details = $stmt->fetchAll();

require_once '../../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title"><i class="fas fa-file-alt"></i> Daily Report</h1>
    <p class="subtitle"><?php echo formatDate($report_date); ?></p>
</div>

<div class="dashboard-card mb-20">
    <div class="card-body">
        <form method="GET" action="" class="form-row">
            <div class="form-group">
                <label>Select Date</label>
                <input type="date" name="date" class="form-control" value="<?php echo $report_date; ?>">
            </div>
            <div class="form-group">
                <label>Filter by Class</label>
                <select name="class" class="form-control">
                    <option value="">All Classes</option>
                    <?php foreach ($classes as $class): ?>
                    <option value="<?php echo $class['id']; ?>" <?php echo ($class_filter == $class['id']) ? 'selected' : ''; ?>>
                        <?php echo $class['class_name']; ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group" style="align-self: flex-end;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter"></i> Filter
                </button>
                <button type="button" onclick="window.print()" class="btn btn-secondary">
                    <i class="fas fa-print"></i> Print
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Summary Statistics -->
<div class="stats-grid mb-20">
    <div class="stat-card stat-success">
        <div class="stat-icon"><i class="fas fa-user-check"></i></div>
        <div class="stat-info">
            <h3><?php echo $attendance_stats['present'] ?? 0; ?></h3>
            <p>Present</p>
        </div>
    </div>

    <div class="stat-card stat-danger">
        <div class="stat-icon"><i class="fas fa-user-times"></i></div>
        <div class="stat-info">
            <h3><?php echo $attendance_stats['absent'] ?? 0; ?></h3>
            <p>Absent</p>
        </div>
    </div>

    <div class="stat-card stat-info">
        <div class="stat-icon"><i class="fas fa-book-open"></i></div>
        <div class="stat-info">
            <h3><?php echo $progress_stats['sabak_count'] ?? 0; ?></h3>
            <p>New Lessons (Sabak)</p>
        </div>
    </div>

    <div class="stat-card stat-warning">
        <div class="stat-icon"><i class="fas fa-redo"></i></div>
        <div class="stat-info">
            <h3><?php echo $progress_stats['sabqi_count'] ?? 0; ?></h3>
            <p>Revisions (Sabqi)</p>
        </div>
    </div>
</div>

<!-- Attendance Details -->
<div class="dashboard-card mb-20">
    <div class="card-header">
        <h3><i class="fas fa-calendar-check"></i> Attendance Details</h3>
    </div>
    <div class="card-body">
        <?php if (count($attendance_details) > 0): ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Student ID</th>
                    <th>Name</th>
                    <th>Class</th>
                    <th>Status</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($attendance_details as $record): ?>
                <tr>
                    <td><?php echo $record['student_id']; ?></td>
                    <td><?php echo $record['first_name'] . ' ' . $record['last_name']; ?></td>
                    <td><?php echo $record['class_name']; ?></td>
                    <td>
                        <?php
                        $badges = [
                            'present' => '<span class="badge badge-success">Present</span>',
                            'absent' => '<span class="badge badge-danger">Absent</span>',
                            'late' => '<span class="badge badge-warning">Late</span>',
                            'leave' => '<span class="badge badge-info">Leave</span>'
                        ];
                        echo $badges[$record['status']] ?? $record['status'];
                        ?>
                    </td>
                    <td><?php echo $record['remarks'] ?: '-'; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <p class="text-center text-muted">No attendance records for this date</p>
        <?php endif; ?>
    </div>
</div>

<!-- Progress Details -->
<div class="dashboard-card">
    <div class="card-header">
        <h3><i class="fas fa-book-quran"></i> Quran Progress Details</h3>
    </div>
    <div class="card-body">
        <?php if (count($progress_details) > 0): ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Student ID</th>
                    <th>Name</th>
                    <th>Class</th>
                    <th>Type</th>
                    <th>Surah</th>
                    <th>Pages</th>
                    <th>Performance</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($progress_details as $record): ?>
                <tr>
                    <td><?php echo $record['student_id']; ?></td>
                    <td><?php echo $record['first_name'] . ' ' . $record['last_name']; ?></td>
                    <td><?php echo $record['class_name']; ?></td>
                    <td>
                        <?php
                        $type_labels = [
                            'new_lesson' => '<span class="badge badge-primary">Sabak</span>',
                            'revision' => '<span class="badge badge-info">Sabqi</span>',
                            'manzil' => '<span class="badge badge-success">Manzil</span>'
                        ];
                        echo $type_labels[$record['sabak_type']] ?? $record['sabak_type'];
                        ?>
                    </td>
                    <td><?php echo $record['surah_name']; ?></td>
                    <td><?php echo $record['page_from'] . '-' . $record['page_to']; ?></td>
                    <td>
                        <?php
                        $perf_badges = [
                            'excellent' => '<span class="badge badge-success">Excellent</span>',
                            'good' => '<span class="badge badge-info">Good</span>',
                            'average' => '<span class="badge badge-warning">Average</span>',
                            'needs_improvement' => '<span class="badge badge-danger">Needs Improvement</span>'
                        ];
                        echo $perf_badges[$record['performance']] ?? $record['performance'];
                        ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <p class="text-center text-muted">No progress records for this date</p>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>
