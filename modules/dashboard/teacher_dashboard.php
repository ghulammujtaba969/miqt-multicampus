<?php
/**
 * Teacher Dashboard
 * MIQT System
 */

require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../includes/functions.php';

// Check if user is logged in and is a teacher
if (!isLoggedIn() || getUserRole() != 'teacher') {
    setFlash('danger', 'Access denied. Teacher login required.');
    redirect(SITE_URL . '/index.php');
}

$pageTitle = 'Teacher Dashboard';

$teacher_id = isset($_SESSION['teacher_id']) ? $_SESSION['teacher_id'] : null;

// Get teacher details
$teacher = null;
if ($teacher_id) {
    $sql = "SELECT t.*, c.class_name 
            FROM teachers t
            LEFT JOIN classes c ON t.id = c.class_teacher_id
            WHERE t.id = ?";
    $stmt = $db->prepare($sql);
    $stmt->execute([$teacher_id]);
    $teacher = $stmt->fetch();
}

// Get statistics for teacher
$stats = [];

// Get assigned class
$assigned_class = null;
if ($teacher_id) {
    $sql = "SELECT c.* FROM classes c WHERE c.class_teacher_id = ? AND c.status = 'active' LIMIT 1";
    $stmt = $db->prepare($sql);
    $stmt->execute([$teacher_id]);
    $assigned_class = $stmt->fetch();
}

// Students in assigned class
if ($assigned_class) {
    $sql = "SELECT COUNT(*) as total FROM students WHERE class_id = ? AND status = 'active'";
    $stmt = $db->prepare($sql);
    $stmt->execute([$assigned_class['id']]);
    $stats['my_students'] = $stmt->fetch()['total'];
} else {
    $stats['my_students'] = 0;
}

// Today's attendance marked by this teacher
$sql = "SELECT COUNT(*) as total FROM student_attendance 
        WHERE attendance_date = CURDATE() AND marked_by = ?";
$stmt = $db->prepare($sql);
$stmt->execute([getUserId()]);
$stats['attendance_today'] = $stmt->fetch()['total'];

// Today's progress records added by this teacher
$sql = "SELECT COUNT(*) as total FROM sabak_records 
        WHERE record_date = CURDATE() AND teacher_id = ?
        UNION ALL
        SELECT COUNT(*) FROM sabqi_records 
        WHERE record_date = CURDATE() AND teacher_id = ?
        UNION ALL
        SELECT COUNT(*) FROM manzil_records 
        WHERE record_date = CURDATE() AND teacher_id = ?";
$stmt = $db->prepare($sql);
$stmt->execute([$teacher_id, $teacher_id, $teacher_id]);
$progress_counts = $stmt->fetchAll();
$stats['progress_today'] = array_sum(array_column($progress_counts, 'total'));

// Recent students in assigned class
$recent_students = [];
if ($assigned_class) {
    $sql = "SELECT s.*, c.class_name
            FROM students s
            LEFT JOIN classes c ON s.class_id = c.id
            WHERE s.class_id = ? AND s.status = 'active'
            ORDER BY s.first_name, s.last_name
            LIMIT 10";
    $stmt = $db->prepare($sql);
    $stmt->execute([$assigned_class['id']]);
    $recent_students = $stmt->fetchAll();
}

require_once '../../includes/header_teacher.php';
?>

<div class="dashboard">
    <h1 class="page-title"><i class="fas fa-chalkboard-teacher"></i> Teacher Dashboard</h1>
    <p class="subtitle">Welcome back, <?php echo getUserName(); ?>!</p>

    <?php if ($teacher): ?>
    <div class="dashboard-card mb-20">
        <div class="card-body">
            <div class="form-row">
                <div class="form-group">
                    <strong>Teacher ID:</strong> <?php echo $teacher['teacher_id']; ?>
                </div>
                <div class="form-group">
                    <strong>Name:</strong> <?php echo $teacher['first_name'] . ' ' . $teacher['last_name']; ?>
                </div>
                <?php if ($assigned_class): ?>
                <div class="form-group">
                    <strong>Assigned Class:</strong> <?php echo $assigned_class['class_name']; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Statistics Cards -->
    <div class="stats-grid">
        <div class="stat-card stat-primary">
            <div class="stat-icon">
                <i class="fas fa-user-graduate"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $stats['my_students']; ?></h3>
                <p>My Students</p>
            </div>
        </div>

        <div class="stat-card stat-success">
            <div class="stat-icon">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $stats['attendance_today']; ?></h3>
                <p>Attendance Marked Today</p>
            </div>
        </div>

        <div class="stat-card stat-info">
            <div class="stat-icon">
                <i class="fas fa-book-quran"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $stats['progress_today']; ?></h3>
                <p>Progress Records Today</p>
            </div>
        </div>

        <?php if ($assigned_class): ?>
        <div class="stat-card stat-warning">
            <div class="stat-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $assigned_class['capacity']; ?></h3>
                <p>Class Capacity</p>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <div class="dashboard-grid">
        <!-- My Students -->
        <div class="dashboard-card">
            <div class="card-header">
                <h3><i class="fas fa-users"></i> My Students</h3>
                <?php if ($assigned_class): ?>
                <a href="<?php echo SITE_URL; ?>/modules/students/students.php?class_id=<?php echo $assigned_class['id']; ?>" class="btn btn-sm">View All</a>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <?php if (count($recent_students) > 0): ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Name</th>
                            <th>Class</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_students as $student): ?>
                        <tr>
                            <td><?php echo $student['student_id']; ?></td>
                            <td><?php echo $student['first_name'] . ' ' . $student['last_name']; ?></td>
                            <td><?php echo $student['class_name'] ?? 'N/A'; ?></td>
                            <td>
                                <a href="<?php echo SITE_URL; ?>/modules/students/view_student.php?id=<?php echo $student['id']; ?>" class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <p class="text-center text-muted">No students assigned</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="dashboard-card">
            <div class="card-header">
                <h3><i class="fas fa-bolt"></i> Quick Actions</h3>
            </div>
            <div class="card-body">
                <div class="dashboard-grid" style="grid-template-columns: 1fr;">
                    <?php if ($assigned_class): ?>
                    <a href="<?php echo SITE_URL; ?>/modules/attendance/student_attendance.php?class_id=<?php echo $assigned_class['id']; ?>" class="btn btn-primary btn-block">
                        <i class="fas fa-calendar-check"></i> Mark Attendance
                    </a>
                    <a href="<?php echo SITE_URL; ?>/modules/progress/daily_progress.php?class_id=<?php echo $assigned_class['id']; ?>" class="btn btn-success btn-block">
                        <i class="fas fa-book-quran"></i> Add Daily Progress
                    </a>
                    <?php endif; ?>
                    <a href="<?php echo SITE_URL; ?>/modules/progress/add_sabak.php" class="btn btn-info btn-block">
                        <i class="fas fa-book-open"></i> Add Sabak
                    </a>
                    <a href="<?php echo SITE_URL; ?>/modules/progress/add_sabqi.php" class="btn btn-warning btn-block">
                        <i class="fas fa-redo"></i> Add Sabqi
                    </a>
                    <a href="<?php echo SITE_URL; ?>/modules/progress/add_manzil.php" class="btn btn-secondary btn-block">
                        <i class="fas fa-check-circle"></i> Add Manzil
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>

