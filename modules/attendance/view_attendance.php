<?php
/**
 * View Attendance Reports
 * MIQT System
 */

require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../includes/functions.php';

$pageTitle = 'Attendance Reports';

// Get filter parameters
$report_type = isset($_GET['type']) ? sanitize($_GET['type']) : 'student';
$class_id = isset($_GET['class']) ? (int)$_GET['class'] : 0;
$date_from = isset($_GET['date_from']) ? sanitize($_GET['date_from']) : date('Y-m-01');
$date_to = isset($_GET['date_to']) ? sanitize($_GET['date_to']) : date('Y-m-d');

// Get classes for filter
$sql = "SELECT * FROM classes WHERE status = 'active' ORDER BY class_name";
$stmt = $db->prepare($sql);
$stmt->execute();
$classes = $stmt->fetchAll();

// Build attendance query based on type
if ($report_type == 'student') {
    $where = "WHERE sa.attendance_date BETWEEN ? AND ?";
    $params = [$date_from, $date_to];

    if ($class_id > 0) {
        $where .= " AND s.class_id = ?";
        $params[] = $class_id;
    }

    $sql = "SELECT sa.*, s.student_id, s.first_name, s.last_name, c.class_name
            FROM student_attendance sa
            JOIN students s ON sa.student_id = s.id
            LEFT JOIN classes c ON s.class_id = c.id
            $where
            ORDER BY sa.attendance_date DESC, s.first_name";
} else {
    $where = "WHERE ta.attendance_date BETWEEN ? AND ?";
    $params = [$date_from, $date_to];

    $sql = "SELECT ta.*, t.teacher_id, t.first_name, t.last_name, t.specialization
            FROM teacher_attendance ta
            JOIN teachers t ON ta.teacher_id = t.id
            $where
            ORDER BY ta.attendance_date DESC, t.first_name";
}

$stmt = $db->prepare($sql);
$stmt->execute($params);
$attendance_records = $stmt->fetchAll();

// Calculate statistics
$total_records = count($attendance_records);
$present_count = 0;
$absent_count = 0;
$late_count = 0;
$leave_count = 0;

foreach ($attendance_records as $record) {
    switch ($record['status']) {
        case 'present': $present_count++; break;
        case 'absent': $absent_count++; break;
        case 'late': $late_count++; break;
        case 'leave': $leave_count++; break;
    }
}

$attendance_percentage = $total_records > 0 ? round(($present_count / $total_records) * 100, 2) : 0;

require_once '../../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title"><i class="fas fa-chart-line"></i> Attendance Reports</h1>
    <p class="subtitle">View and analyze attendance data</p>
</div>

<!-- Statistics -->
<div class="stats-grid mb-20">
    <div class="stat-card stat-success">
        <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
        <div class="stat-info">
            <h3><?php echo $present_count; ?></h3>
            <p>Present</p>
        </div>
    </div>

    <div class="stat-card stat-danger">
        <div class="stat-icon"><i class="fas fa-times-circle"></i></div>
        <div class="stat-info">
            <h3><?php echo $absent_count; ?></h3>
            <p>Absent</p>
        </div>
    </div>

    <div class="stat-card stat-warning">
        <div class="stat-icon"><i class="fas fa-clock"></i></div>
        <div class="stat-info">
            <h3><?php echo $late_count; ?></h3>
            <p>Late</p>
        </div>
    </div>

    <div class="stat-card stat-info">
        <div class="stat-icon"><i class="fas fa-percentage"></i></div>
        <div class="stat-info">
            <h3><?php echo $attendance_percentage; ?>%</h3>
            <p>Attendance Rate</p>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="dashboard-card mb-20">
    <div class="card-body">
        <form method="GET" action="" class="form-row">
            <div class="form-group">
                <label for="type">Report Type</label>
                <select name="type" id="type" class="form-control">
                    <option value="student" <?php echo ($report_type == 'student') ? 'selected' : ''; ?>>Student Attendance</option>
                    <option value="teacher" <?php echo ($report_type == 'teacher') ? 'selected' : ''; ?>>Teacher Attendance</option>
                </select>
            </div>

            <?php if ($report_type == 'student'): ?>
            <div class="form-group">
                <label for="class">Class</label>
                <select name="class" id="class" class="form-control">
                    <option value="0">All Classes</option>
                    <?php foreach ($classes as $class): ?>
                    <option value="<?php echo $class['id']; ?>" <?php echo ($class_id == $class['id']) ? 'selected' : ''; ?>>
                        <?php echo $class['class_name']; ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php endif; ?>

            <div class="form-group">
                <label for="date_from">From Date</label>
                <input type="date" name="date_from" id="date_from" class="form-control" value="<?php echo $date_from; ?>">
            </div>

            <div class="form-group">
                <label for="date_to">To Date</label>
                <input type="date" name="date_to" id="date_to" class="form-control" value="<?php echo $date_to; ?>">
            </div>

            <div class="form-group">
                <label>&nbsp;</label>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter"></i> Filter
                </button>
                <a href="view_attendance.php" class="btn btn-secondary">
                    <i class="fas fa-redo"></i> Reset
                </a>
            </div>
        </form>

        <?php if ($report_type == 'student'): ?>
        <div style="margin-top: 15px; display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="<?php echo SITE_URL; ?>/modules/reports/attendance_classwise.php?range=custom&from=<?php echo urlencode($date_from); ?>&to=<?php echo urlencode($date_to); ?>&class_id=<?php echo (int)$class_id; ?>"
               class="btn btn-success" target="_blank">
                <i class="fas fa-file-alt"></i> Class Wise Report
            </a>
            <a href="<?php echo SITE_URL; ?>/modules/reports/attendance_studentwise.php?range=custom&from=<?php echo urlencode($date_from); ?>&to=<?php echo urlencode($date_to); ?>&class_id=<?php echo (int)$class_id; ?>"
               class="btn btn-info" target="_blank">
                <i class="fas fa-user"></i> Student Wise Report
            </a>
            <?php if (hasPermission(['principal', 'vice_principal', 'coordinator'])): ?>
            <a href="<?php echo SITE_URL; ?>/modules/attendance/export_attendance_class.php?date_from=<?php echo urlencode($date_from); ?>&date_to=<?php echo urlencode($date_to); ?>&class=<?php echo (int)$class_id; ?>"
               class="btn btn-outline-secondary">
                <i class="fas fa-download"></i> Export CSV (class range)
            </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Attendance Records -->
<div class="dashboard-card">
    <div class="card-header">
        <h3><i class="fas fa-table"></i> Attendance Records (<?php echo $total_records; ?>)</h3>
    </div>
    <div class="card-body">
        <?php if (count($attendance_records) > 0): ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th><?php echo ($report_type == 'student') ? 'Student ID' : 'Teacher ID'; ?></th>
                        <th>Name</th>
                        <?php if ($report_type == 'student'): ?>
                        <th>Class</th>
                        <?php else: ?>
                        <th>Specialization</th>
                        <th>Check-In</th>
                        <th>Check-Out</th>
                        <?php endif; ?>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($attendance_records as $record): ?>
                    <tr>
                        <td><?php echo formatDate($record['attendance_date']); ?></td>
                        <td><?php echo $report_type == 'student' ? $record['student_id'] : $record['teacher_id']; ?></td>
                        <td><?php echo $record['first_name'] . ' ' . $record['last_name']; ?></td>
                        <?php if ($report_type == 'student'): ?>
                        <td><?php echo $record['class_name'] ?? 'N/A'; ?></td>
                        <?php else: ?>
                        <td><?php echo $record['specialization'] ?? 'N/A'; ?></td>
                        <td><?php echo $record['check_in'] ? date('h:i A', strtotime($record['check_in'])) : 'N/A'; ?></td>
                        <td><?php echo $record['check_out'] ? date('h:i A', strtotime($record['check_out'])) : 'N/A'; ?></td>
                        <?php endif; ?>
                        <td>
                            <?php
                            $badges = [
                                'present' => 'badge-success',
                                'absent' => 'badge-danger',
                                'late' => 'badge-warning',
                                'leave' => 'badge-info',
                                'half_day' => 'badge-secondary'
                            ];
                            $badge = $badges[$record['status']] ?? 'badge-secondary';
                            ?>
                            <span class="badge <?php echo $badge; ?>">
                                <?php echo ucfirst(str_replace('_', ' ', $record['status'])); ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <p class="text-center text-muted">No attendance records found for the selected criteria</p>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>
