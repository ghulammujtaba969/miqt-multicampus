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

$h = (int) date('G');
$greet = $h < 12 ? 'Good Morning' : ($h < 17 ? 'Good Afternoon' : 'Good Evening');

require_once '../../includes/header.php';
?>

<div class="dashboard">
    <div class="miqt-welcome-banner">
        <div class="wb-text">
            <div class="greeting"><?php echo htmlspecialchars($greet); ?></div>
            <div class="headline">Welcome back, <?php echo htmlspecialchars(getUserName() ?? 'User'); ?>!</div>
            <div class="sub">Here's what's happening at MIQT today.</div>
        </div>
        <div class="wb-badge">
            <div class="wb-num"><?php echo (int) $stats['total_students']; ?></div>
            <div class="wb-lbl">Total Students</div>
        </div>
    </div>

    <div class="miqt-stat-row">
        <div class="miqt-sc c1">
            <div class="sc-top">
                <div class="sc-icon"><i class="fas fa-user-graduate"></i></div>
                <span class="sc-trend up">Active</span>
            </div>
            <div class="sc-num"><?php echo $stats['total_students']; ?></div>
            <div class="sc-lbl">Total Students</div>
        </div>
        <div class="miqt-sc c2">
            <div class="sc-top">
                <div class="sc-icon"><i class="fas fa-chalkboard-teacher"></i></div>
                <span class="sc-trend up">Active</span>
            </div>
            <div class="sc-num"><?php echo $stats['total_teachers']; ?></div>
            <div class="sc-lbl">Total Teachers</div>
        </div>
        <div class="miqt-sc c3">
            <div class="sc-top">
                <div class="sc-icon"><i class="fas fa-calendar-check"></i></div>
                <span class="sc-trend neutral">Today</span>
            </div>
            <div class="sc-num"><?php echo $stats['present_today']; ?></div>
            <div class="sc-lbl">Present Today</div>
        </div>
        <div class="miqt-sc c4">
            <div class="sc-top">
                <div class="sc-icon"><i class="fas fa-book-quran"></i></div>
                <span class="sc-trend neutral">Today</span>
            </div>
            <div class="sc-num"><?php echo $stats['today_progress']; ?></div>
            <div class="sc-lbl">Today's Progress</div>
        </div>
    </div>

    <div class="miqt-panels-row">
        <div class="miqt-panel">
            <div class="panel-top">
                <div class="panel-title">Recent Students <span class="pill">Latest</span></div>
                <a href="<?php echo SITE_URL; ?>/modules/students/students.php" class="btn-sm-blue">View All</a>
            </div>
            <?php if (count($recent_students) > 0): ?>
            <table class="miqt-stud-table">
                <thead>
                    <tr>
                        <th>Student ID</th>
                        <th>Name</th>
                        <th>Class</th>
                        <th>Admission Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent_students as $student): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($student['student_id']); ?></td>
                        <td><?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></td>
                        <td><?php echo htmlspecialchars($student['class_name'] ?? 'N/A'); ?></td>
                        <td><?php echo formatDate($student['admission_date']); ?></td>
                        <td><span class="miqt-status-badge">Active</span></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="miqt-empty-panel">
                <div class="e-icon"><i class="fas fa-users"></i></div>
                <div class="e-text">No students found</div>
            </div>
            <?php endif; ?>
        </div>

        <div class="miqt-panel">
            <div class="panel-top">
                <div class="panel-title">Upcoming Exams</div>
                <a href="<?php echo SITE_URL; ?>/modules/exams/manage_exams.php" class="btn-sm-blue">View All</a>
            </div>
            <?php if (count($upcoming_exams) > 0): ?>
            <table class="miqt-stud-table">
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
                        <td><?php echo htmlspecialchars($exam['exam_title']); ?></td>
                        <td><?php echo htmlspecialchars($exam['exam_name']); ?></td>
                        <td><?php echo formatDate($exam['exam_date']); ?></td>
                        <td><span class="badge badge-info"><?php echo ucfirst($exam['status']); ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="miqt-empty-panel">
                <div class="e-icon"><i class="fas fa-file-alt"></i></div>
                <div class="e-text">No upcoming exams scheduled</div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="miqt-panel miqt-panel-full">
        <div class="panel-top">
            <div class="panel-title"><i class="fas fa-calendar-alt me-1"></i> Upcoming Events</div>
            <a href="<?php echo SITE_URL; ?>/modules/calendar/index.php" class="btn-sm-blue">View Calendar</a>
        </div>
        <?php if (count($upcoming_events) > 0): ?>
        <table class="miqt-stud-table">
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
        <div class="miqt-empty-panel">
            <div class="e-icon"><i class="fas fa-calendar-alt"></i></div>
            <div class="e-text">No upcoming events</div>
        </div>
        <?php endif; ?>
    </div>

    <div class="miqt-panel miqt-panel-full">
        <div class="panel-top">
            <div class="panel-title"><i class="fas fa-history me-1"></i> Recent Activity</div>
        </div>
        <?php if (count($recent_activities) > 0): ?>
            <div class="activity-list">
                <?php foreach ($recent_activities as $activity): ?>
                <div class="activity-item">
                    <div class="activity-icon">
                        <i class="fas fa-circle"></i>
                    </div>
                    <div class="activity-details">
                        <p class="activity-text">
                            <strong><?php echo htmlspecialchars($activity['full_name'] ?? 'System'); ?></strong>
                            <?php echo htmlspecialchars(strtolower($activity['action'])); ?> in <?php echo htmlspecialchars($activity['module']); ?>
                        </p>
                        <p class="activity-time">
                            <i class="fas fa-clock"></i> <?php echo date('M d, Y h:i A', strtotime($activity['created_at'])); ?>
                        </p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
        <div class="miqt-empty-panel">
            <div class="e-icon"><i class="fas fa-history"></i></div>
            <div class="e-text">No recent activity</div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>
