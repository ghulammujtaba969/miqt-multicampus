<?php
/**
 * Add Manzil (Complete Revision)
 * MIQT System
 */

require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../includes/functions.php';

$pageTitle = 'Add Manzil (Complete Revision)';

// Get active students
$sql = "SELECT s.*, c.class_name FROM students s
        LEFT JOIN classes c ON s.class_id = c.id
        WHERE s.status = 'active' ORDER BY s.first_name";
$stmt = $db->prepare($sql);
$stmt->execute();
$students = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_id = sanitize($_POST['student_id']);
    $record_date = sanitize($_POST['record_date']);
    $juz_from = sanitize($_POST['juz_from']);
    $juz_to = sanitize($_POST['juz_to']);
    $completion_time = sanitize($_POST['completion_time']);
    $accuracy_percentage = sanitize($_POST['accuracy_percentage']);
    $remarks = sanitize($_POST['remarks']);
    $teacher_id = getUserId();

    try {
        $sql = "INSERT INTO manzil_records (student_id, record_date, juz_from, juz_to,
                completion_time, accuracy_percentage, teacher_id, remarks)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $db->prepare($sql);
        $stmt->execute([
            $student_id, $record_date, $juz_from, $juz_to,
            $completion_time, $accuracy_percentage, $teacher_id, $remarks
        ]);

        // Also add to general progress table
        $performance = 'excellent';
        if ($accuracy_percentage < 90) $performance = 'good';
        if ($accuracy_percentage < 70) $performance = 'average';
        if ($accuracy_percentage < 50) $performance = 'needs_improvement';

        $sql = "INSERT INTO quran_progress (student_id, progress_date, sabak_type, juz_number,
                page_from, page_to, performance, teacher_id, remarks)
                VALUES (?, ?, 'manzil', ?, ?, ?, ?, ?, ?)";

        $stmt = $db->prepare($sql);
        $stmt->execute([
            $student_id, $record_date, $juz_from,
            ($juz_from * 20), ($juz_to * 20), $performance, $teacher_id, $remarks
        ]);

        logActivity($db, 'Add Manzil', 'Progress', "Added Manzil for student ID: $student_id");

        setFlash('success', 'Manzil (Complete Revision) added successfully!');
        redirect(SITE_URL . '/modules/progress/view_progress.php');
    } catch(PDOException $e) {
        $error = 'Error adding record: ' . $e->getMessage();
    }
}

require_once '../../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title"><i class="fas fa-quran"></i> Add Manzil (Complete Revision)</h1>
    <p class="subtitle">Record complete Quran revision</p>
</div>

<?php if (isset($error)): ?>
<div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<div class="dashboard-card">
    <div class="card-body">
        <form method="POST" action="">
            <div class="form-row">
                <div class="form-group">
                    <label for="student_id">Select Student *</label>
                    <select name="student_id" id="student_id" class="form-control" required>
                        <option value="">Choose Student</option>
                        <?php foreach ($students as $student): ?>
                        <option value="<?php echo $student['id']; ?>">
                            <?php echo $student['student_id'] . ' - ' . $student['first_name'] . ' ' . $student['last_name']; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="record_date">Date *</label>
                    <input type="date" name="record_date" id="record_date" class="form-control"
                           value="<?php echo date('Y-m-d'); ?>" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="juz_from">From Juz *</label>
                    <select name="juz_from" id="juz_from" class="form-control" required>
                        <?php for($i=1; $i<=30; $i++): ?>
                        <option value="<?php echo $i; ?>">Juz <?php echo $i; ?></option>
                        <?php endfor; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="juz_to">To Juz *</label>
                    <select name="juz_to" id="juz_to" class="form-control" required>
                        <?php for($i=1; $i<=30; $i++): ?>
                        <option value="<?php echo $i; ?>" <?php echo ($i==30) ? 'selected' : ''; ?>>
                            Juz <?php echo $i; ?>
                        </option>
                        <?php endfor; ?>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="completion_time">Completion Time (Minutes)</label>
                    <input type="number" name="completion_time" id="completion_time" class="form-control"
                           min="1" placeholder="e.g., 60">
                </div>

                <div class="form-group">
                    <label for="accuracy_percentage">Accuracy Percentage *</label>
                    <input type="number" name="accuracy_percentage" id="accuracy_percentage" class="form-control"
                           min="0" max="100" step="0.01" required>
                </div>
            </div>

            <div class="form-group">
                <label for="remarks">Teacher Remarks</label>
                <textarea name="remarks" id="remarks" class="form-control" rows="4"
                          placeholder="Overall performance, areas of improvement..."></textarea>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Save Manzil
                </button>
                <a href="view_progress.php" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<div class="dashboard-card mt-20">
    <div class="card-header">
        <h3><i class="fas fa-info-circle"></i> What is Manzil?</h3>
    </div>
    <div class="card-body">
        <p><strong>Manzil</strong> (منزل) refers to complete revision of the entire Quran or large portions.</p>
        <ul>
            <li>Complete review of previously memorized Quran</li>
            <li>Can be full 30 Juz or specific range</li>
            <li>Ensures long-term retention</li>
            <li>Tests overall memorization quality</li>
            <li>Regular Manzil is essential for Hafiz students</li>
        </ul>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>
