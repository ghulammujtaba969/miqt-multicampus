<?php
/**
 * Student Dashboard
 * MIQT System
 */

require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../includes/functions.php';

// Check if user is logged in and is a student
if (!isLoggedIn() || getUserRole() != 'student') {
    setFlash('danger', 'Access denied. Student login required.');
    redirect(SITE_URL . '/index.php');
}

$pageTitle = 'Student Dashboard';

$student_id = isset($_SESSION['student_id']) ? $_SESSION['student_id'] : null;

// Get student details
$student = null;
if ($student_id) {
    $sql = "SELECT s.*, c.class_name FROM students s
            LEFT JOIN classes c ON s.class_id = c.id
            WHERE s.id = ?";
    $stmt = $db->prepare($sql);
    $stmt->execute([$student_id]);
    $student = $stmt->fetch();
}

if (!$student) {
    setFlash('danger', 'Student record not found');
    redirect(SITE_URL . '/login_student.php');
}

// Get statistics
$stats = [];

// Attendance summary
$sql = "SELECT
        COUNT(*) as total_days,
        SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present_days,
        SUM(CASE WHEN status = 'absent' THEN 1 ELSE 0 END) as absent_days
        FROM student_attendance WHERE student_id = ?";
$stmt = $db->prepare($sql);
$stmt->execute([$student_id]);
$attendance = $stmt->fetch();
$stats['attendance'] = $attendance;

// Progress summary
$sql = "SELECT
        COUNT(*) as total_records,
        SUM(CASE WHEN sabak_type = 'new_lesson' THEN 1 ELSE 0 END) as sabak_count,
        SUM(CASE WHEN sabak_type = 'revision' THEN 1 ELSE 0 END) as sabqi_count
        FROM quran_progress WHERE student_id = ?";
$stmt = $db->prepare($sql);
$stmt->execute([$student_id]);
$progress = $stmt->fetch();
$stats['progress'] = $progress;

// Recent progress (last 5)
$sql = "SELECT * FROM quran_progress
        WHERE student_id = ?
        ORDER BY progress_date DESC LIMIT 5";
$stmt = $db->prepare($sql);
$stmt->execute([$student_id]);
$recent_progress = $stmt->fetchAll();

// Recent exam results
$sql = "SELECT er.*, e.exam_title, e.exam_date, e.total_marks,
               CASE WHEN e.total_marks IS NOT NULL AND e.total_marks > 0 AND er.obtained_marks IS NOT NULL
                    THEN (er.obtained_marks / e.total_marks) * 100 ELSE NULL END AS percentage
        FROM exam_results er
        JOIN exams e ON er.exam_id = e.id
        WHERE er.student_id = ?
        ORDER BY e.exam_date DESC LIMIT 5";
$stmt = $db->prepare($sql);
$stmt->execute([$student_id]);
$recent_exams = $stmt->fetchAll();

// Calculate grades
foreach ($recent_exams as &$exam) {
    if (isset($exam['percentage']) && $exam['percentage'] !== null) {
        $exam['grade'] = $exam['grade'] ?? calculateGrade((float)$exam['percentage']);
    }
}
unset($exam);

require_once '../../includes/header_student.php';
?>

<div class="dashboard">
    <h1 class="page-title"><i class="fas fa-user-graduate"></i> Student Dashboard</h1>
    <p class="subtitle">Welcome, <?php echo $student['first_name'] . ' ' . $student['last_name']; ?>!</p>

    <!-- Student Info Card -->
    <div class="dashboard-card mb-20">
        <div class="card-body">
            <div class="form-row">
                <div class="form-group">
                    <strong>Student ID:</strong> <?php echo $student['student_id']; ?>
                </div>
                <div class="form-group">
                    <strong>Class:</strong> <?php echo $student['class_name'] ?? 'N/A'; ?>
                </div>
                <div class="form-group">
                    <strong>Admission No:</strong> <?php echo $student['admission_no']; ?>
                </div>
                <div class="form-group">
                    <strong>Status:</strong> 
                    <span class="badge badge-success"><?php echo ucfirst($student['status']); ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="stats-grid">
        <div class="stat-card stat-success">
            <div class="stat-icon">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $stats['attendance']['present_days'] ?? 0; ?> / <?php echo $stats['attendance']['total_days'] ?? 0; ?></h3>
                <p>Attendance Record</p>
            </div>
        </div>

        <div class="stat-card stat-primary">
            <div class="stat-icon">
                <i class="fas fa-book-open"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $stats['progress']['sabak_count'] ?? 0; ?></h3>
                <p>Total Sabak</p>
            </div>
        </div>

        <div class="stat-card stat-info">
            <div class="stat-icon">
                <i class="fas fa-redo"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $stats['progress']['sabqi_count'] ?? 0; ?></h3>
                <p>Total Sabqi</p>
            </div>
        </div>

        <div class="stat-card stat-warning">
            <div class="stat-icon">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $stats['progress']['total_records'] ?? 0; ?></h3>
                <p>Total Progress</p>
            </div>
        </div>
    </div>

    <div class="dashboard-grid">
        <!-- Recent Progress -->
        <div class="dashboard-card">
            <div class="card-header">
                <h3><i class="fas fa-book-quran"></i> Recent Progress</h3>
                <a href="<?php echo SITE_URL; ?>/modules/students/view_student.php?id=<?php echo $student_id; ?>" class="btn btn-sm">View All</a>
            </div>
            <div class="card-body">
                <?php if (count($recent_progress) > 0): ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Surah</th>
                            <th>Pages</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_progress as $record): ?>
                        <tr>
                            <td><?php echo formatDate($record['progress_date']); ?></td>
                            <td>
                                <?php
                                $types = [
                                    'new_lesson' => '<span class="badge badge-primary">Sabak</span>',
                                    'revision' => '<span class="badge badge-info">Sabqi</span>',
                                    'manzil' => '<span class="badge badge-success">Manzil</span>'
                                ];
                                echo $types[$record['sabak_type']] ?? $record['sabak_type'];
                                ?>
                            </td>
                            <td><?php echo $record['surah_name'] ?: 'N/A'; ?></td>
                            <td><?php echo ($record['page_from'] && $record['page_to']) ? $record['page_from'] . '-' . $record['page_to'] : 'N/A'; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <p class="text-center text-muted">No progress records yet</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Recent Exam Results -->
        <div class="dashboard-card">
            <div class="card-header">
                <h3><i class="fas fa-file-alt"></i> Recent Exam Results</h3>
                <a href="<?php echo SITE_URL; ?>/modules/students/view_student.php?id=<?php echo $student_id; ?>" class="btn btn-sm">View All</a>
            </div>
            <div class="card-body">
                <?php if (count($recent_exams) > 0): ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Exam</th>
                            <th>Date</th>
                            <th>Marks</th>
                            <th>Grade</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_exams as $exam): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($exam['exam_title']); ?></td>
                            <td><?php echo formatDate($exam['exam_date']); ?></td>
                            <td>
                                <?php if (isset($exam['percentage']) && $exam['percentage'] !== null): ?>
                                    <?php echo number_format($exam['percentage'], 2) . '%'; ?>
                                <?php else: ?>
                                    N/A
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (isset($exam['grade']) && $exam['grade']): ?>
                                    <span class="badge badge-primary"><?php echo $exam['grade']; ?></span>
                                <?php else: ?>
                                    N/A
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <p class="text-center text-muted">No exam results yet</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="dashboard-card mt-20">
        <div class="card-header">
            <h3><i class="fas fa-link"></i> Quick Links</h3>
        </div>
        <div class="card-body">
            <div class="form-row">
                <a href="<?php echo SITE_URL; ?>/modules/students/view_student.php?id=<?php echo $student_id; ?>" class="btn btn-primary">
                    <i class="fas fa-user"></i> My Profile
                </a>
                <a href="<?php echo SITE_URL; ?>/modules/students/view_student.php?id=<?php echo $student_id; ?>#progress" class="btn btn-info">
                    <i class="fas fa-book-quran"></i> My Progress
                </a>
                <a href="<?php echo SITE_URL; ?>/modules/students/view_student.php?id=<?php echo $student_id; ?>#exams" class="btn btn-success">
                    <i class="fas fa-file-alt"></i> My Exam Results
                </a>
                <a href="<?php echo SITE_URL; ?>/modules/students/view_student.php?id=<?php echo $student_id; ?>#attendance" class="btn btn-warning">
                    <i class="fas fa-calendar-check"></i> My Attendance
                </a>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>

