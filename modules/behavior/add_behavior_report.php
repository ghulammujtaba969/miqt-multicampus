<?php
/**
 * Add Student Behaviour Report
 * MIQT System
 */

require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../includes/functions.php';

$allowedRoles = ['principal', 'vice_principal', 'coordinator', 'teacher', 'admin'];
if (!hasPermission($allowedRoles)) {
    setFlash('danger', 'You do not have permission to add behaviour reports.');
    redirect(SITE_URL . '/modules/dashboard/index.php');
}

$pageTitle = 'Add Behaviour Report';
$errors    = [];

// Fetch students for dropdown
$sql = "SELECT s.id, s.student_id, s.first_name, s.last_name, c.class_name
        FROM students s
        LEFT JOIN classes c ON s.class_id = c.id
        WHERE s.status = 'active'
        ORDER BY c.class_name, s.first_name, s.last_name";
$stmt = $db->prepare($sql);
$stmt->execute();
$students = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $studentId  = isset($_POST['student_id']) ? (int)$_POST['student_id'] : 0;
    $reportType = isset($_POST['report_type']) ? sanitize($_POST['report_type']) : '';
    $reportDate = isset($_POST['report_date']) ? sanitize($_POST['report_date']) : date('Y-m-d');
    $summary    = isset($_POST['summary']) ? trim(sanitize($_POST['summary'])) : '';
    $details    = isset($_POST['details']) ? trim(sanitize($_POST['details'])) : '';

    if ($studentId <= 0) {
        $errors[] = 'Please select a student.';
    }

    $validTypes = ['internal', 'parent', 'ptm'];
    if (!in_array($reportType, $validTypes, true)) {
        $errors[] = 'Please select a valid report type.';
    }

    if ($reportDate === '') {
        $errors[] = 'Please select a report date.';
    }

    if ($summary === '') {
        $errors[] = 'Please enter a short summary.';
    }

    if (empty($errors)) {
        // Get student's current class
        $stmt = $db->prepare('SELECT class_id FROM students WHERE id = ?');
        $stmt->execute([$studentId]);
        $studentRow = $stmt->fetch();
        $classId    = $studentRow ? (int)$studentRow['class_id'] : null;

        $sql = "INSERT INTO student_behavior_reports
                    (student_id, class_id, report_date, report_type, summary, details, created_by)
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $db->prepare($sql);
        $stmt->execute([
            $studentId,
            $classId,
            $reportDate,
            $reportType,
            $summary,
            $details,
            getUserId()
        ]);

        setFlash('success', 'Behaviour report added successfully.');
        redirect(SITE_URL . '/modules/behavior/behavior_reports.php');
    }
}

require_once '../../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title"><i class="fas fa-user-check"></i> Add Behaviour Report</h1>
    <p class="subtitle">Record internal, parent-facing or PTM notes for a student.</p>
</div>

<div class="dashboard-card">
    <div class="card-body">
        <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-3">
                <label for="student_id" class="form-label">Student</label>
                <select name="student_id" id="student_id" class="form-control" required>
                    <option value="">Select Student</option>
                    <?php foreach ($students as $student): ?>
                    <option value="<?php echo (int)$student['id']; ?>"
                        <?php echo (isset($studentId) && $studentId === (int)$student['id']) ? 'selected' : ''; ?>>
                        <?php
                        echo htmlspecialchars($student['student_id']) . ' - ' .
                             htmlspecialchars($student['first_name'] . ' ' . $student['last_name']);
                        if (!empty($student['class_name'])) {
                            echo ' (' . htmlspecialchars($student['class_name']) . ')';
                        }
                        ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="report_type" class="form-label">Report Type</label>
                <select name="report_type" id="report_type" class="form-control" required>
                    <option value="">Select Type</option>
                    <option value="internal" <?php echo isset($reportType) && $reportType === 'internal' ? 'selected' : ''; ?>>Internal Report</option>
                    <option value="parent" <?php echo isset($reportType) && $reportType === 'parent' ? 'selected' : ''; ?>>For Parents</option>
                    <option value="ptm" <?php echo isset($reportType) && $reportType === 'ptm' ? 'selected' : ''; ?>>PTM Report</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="report_date" class="form-label">Report Date</label>
                <input type="date" name="report_date" id="report_date" class="form-control"
                       value="<?php echo isset($reportDate) ? htmlspecialchars($reportDate) : date('Y-m-d'); ?>" required>
            </div>

            <div class="mb-3">
                <label for="summary" class="form-label">Short Summary</label>
                <input type="text" name="summary" id="summary" class="form-control"
                       maxlength="255"
                       value="<?php echo isset($summary) ? htmlspecialchars($summary) : ''; ?>" required>
            </div>

            <div class="mb-3">
                <label for="details" class="form-label">Detailed Notes (optional)</label>
                <textarea name="details" id="details" rows="5" class="form-control"><?php
                    echo isset($details) ? htmlspecialchars($details) : '';
                ?></textarea>
            </div>

            <div class="mt-3">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Report
                </button>
                <a href="behavior_reports.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Reports
                </a>
            </div>
        </form>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>

