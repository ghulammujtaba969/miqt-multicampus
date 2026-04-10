<?php
/**
 * Dashboard
 * MIQT System
 */

require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../includes/functions.php';

$pageTitle = 'Dashboard';

// Get statistics
$stats = [];

// Total Students
$sql = "SELECT COUNT(*) as total FROM students WHERE status = 'active'";
$stmt = $db->prepare($sql);
$stmt->execute();
$stats['total_students'] = $stmt->fetch()['total'];

// Total Teachers
$sql = "SELECT COUNT(*) as total FROM teachers WHERE status = 'active'";
$stmt = $db->prepare($sql);
$stmt->execute();
$stats['total_teachers'] = $stmt->fetch()['total'];

// Today's Attendance
$sql = "SELECT COUNT(*) as total FROM student_attendance
        WHERE attendance_date = CURDATE() AND status = 'present'";
$stmt = $db->prepare($sql);
$stmt->execute();
$stats['present_today'] = $stmt->fetch()['total'];

// Today's Progress Records
$sql = "SELECT COUNT(*) as total FROM quran_progress WHERE progress_date = CURDATE()";
$stmt = $db->prepare($sql);
$stmt->execute();
$stats['today_progress'] = $stmt->fetch()['total'];

// Recent Activities
$sql = "SELECT al.*, u.full_name
        FROM activity_logs al
        LEFT JOIN users u ON al.user_id = u.id
        ORDER BY al.created_at DESC LIMIT 10";
$stmt = $db->prepare($sql);
$stmt->execute();
$recent_activities = $stmt->fetchAll();

// Recent Students
$sql = "SELECT s.*, c.class_name
        FROM students s
        LEFT JOIN classes c ON s.class_id = c.id
        ORDER BY s.created_at DESC LIMIT 5";
$stmt = $db->prepare($sql);
$stmt->execute();
$recent_students = $stmt->fetchAll();

// Upcoming Exams
$sql = "SELECT e.*, et.exam_name
        FROM exams e
        INNER JOIN exam_types et ON e.exam_type_id = et.id
        WHERE e.exam_date >= CURDATE() AND e.status = 'scheduled'
        ORDER BY e.exam_date ASC LIMIT 5";
$stmt = $db->prepare($sql);
$stmt->execute();
$upcoming_exams = $stmt->fetchAll();

$sql = "SELECT * FROM academic_events 
        WHERE event_date >= CURDATE() 
        ORDER BY event_date ASC LIMIT 5";
$stmt = $db->prepare($sql);
$stmt->execute();
$upcoming_events = $stmt->fetchAll();

require_once '../../includes/header.php';
?>

<div class="dashboard">
    <h1 class="page-title">Dashboard</h1>
    <p class="subtitle">Welcome back, <?php echo getUserName(); ?>!</p>

    <!-- Statistics Cards -->
    <div class="stats-grid">
        <div class="stat-card stat-primary">
            <div class="stat-icon">
                <i class="fas fa-user-graduate"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $stats['total_students']; ?></h3>
                <p>Total Students</p>
            </div>
        </div>

        <div class="stat-card stat-success">
            <div class="stat-icon">
                <i class="fas fa-chalkboard-teacher"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $stats['total_teachers']; ?></h3>
                <p>Total Teachers</p>
            </div>
        </div>

        <div class="stat-card stat-info">
            <div class="stat-icon">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $stats['present_today']; ?></h3>
                <p>Present Today</p>
            </div>
        </div>

        <div class="stat-card stat-warning">
            <div class="stat-icon">
                <i class="fas fa-book-quran"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $stats['today_progress']; ?></h3>
                <p>Today's Progress</p>
            </div>
        </div>
    </div>

    <div class="dashboard-grid">
        <!-- Recent Students -->
        <div class="dashboard-card">
            <div class="card-header">
                <h3><i class="fas fa-users"></i> Recent Students</h3>
                <a href="<?php echo SITE_URL; ?>/modules/students/students.php" class="btn btn-sm">View All</a>
            </div>
            <div class="card-body">
                <?php if (count($recent_students) > 0): ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Name</th>
                            <th>Class</th>
                            <th>Admission Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_students as $student): ?>
                        <tr>
                            <td><?php echo $student['student_id']; ?></td>
                            <td><?php echo $student['first_name'] . ' ' . $student['last_name']; ?></td>
                            <td><?php echo $student['class_name'] ?? 'N/A'; ?></td>
                            <td><?php echo formatDate($student['admission_date']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <p class="text-center text-muted">No students found</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Upcoming Exams -->
        <div class="dashboard-card">
            <div class="card-header">
                <h3><i class="fas fa-file-alt"></i> Upcoming Exams</h3>
                <a href="<?php echo SITE_URL; ?>/modules/exams/manage_exams.php" class="btn btn-sm">View All</a>
            </div>
            <div class="card-body">
                <?php if (count($upcoming_exams) > 0): ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Exam</th>
                            <th>Type</th>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($upcoming_exams as $exam): ?>
                        <tr>
                            <td><?php echo $exam['exam_title']; ?></td>
                            <td><?php echo $exam['exam_name']; ?></td>
                            <td><?php echo formatDate($exam['exam_date']); ?></td>
                            <td><span class="badge badge-info"><?php echo ucfirst($exam['status']); ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <p class="text-center text-muted">No upcoming exams</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="dashboard-card">
        <div class="card-header">
            <h3><i class="fas fa-calendar-alt"></i> Upcoming Events</h3>
            <a href="<?php echo SITE_URL; ?>/modules/calendar/index.php" class="btn btn-sm">View Calendar</a>
        </div>
        <div class="card-body">
            <?php if (count($upcoming_events) > 0): ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Event</th>
                        <th>Type</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($upcoming_events as $event):
                        $typeClass = [
                            'holiday' => 'badge-success',
                            'exam' => 'badge-danger',
                            'event' => 'badge-primary',
                            'meeting' => 'badge-warning',
                            'other' => 'badge-secondary'
                        ][$event['event_type']] ?? 'badge-secondary';
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($event['title']); ?></td>
                        <td><span class="badge <?php echo $typeClass; ?>"><?php echo ucfirst($event['event_type']); ?></span></td>
                        <td><?php echo formatDate($event['event_date']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <p class="text-center text-muted">No upcoming events</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="dashboard-card">
        <div class="card-header">
            <h3><i class="fas fa-history"></i> Recent Activity</h3>
        </div>
        <div class="card-body">
            <?php if (count($recent_activities) > 0): ?>
            <div class="activity-list">
                <?php foreach ($recent_activities as $activity): ?>
                <div class="activity-item">
                    <div class="activity-icon">
                        <i class="fas fa-circle"></i>
                    </div>
                    <div class="activity-details">
                        <p class="activity-text">
                            <strong><?php echo $activity['full_name'] ?? 'System'; ?></strong>
                            <?php echo strtolower($activity['action']); ?> in <?php echo $activity['module']; ?>
                        </p>
                        <p class="activity-time">
                            <i class="fas fa-clock"></i> <?php echo date('M d, Y h:i A', strtotime($activity['created_at'])); ?>
                        </p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <p class="text-center text-muted">No recent activity</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>
