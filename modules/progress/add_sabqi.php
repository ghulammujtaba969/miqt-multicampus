<?php
/**
 * Add Sabqi (Revision)
 * MIQT System
 */

require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../includes/functions.php';

$pageTitle = 'Add Sabqi (Revision)';

// Get active students
$sql = "SELECT s.*, c.class_name FROM students s
        LEFT JOIN classes c ON s.class_id = c.id
        WHERE s.status = 'active' ORDER BY s.first_name";
$stmt = $db->prepare($sql);
$stmt->execute();
$students = $stmt->fetchAll();

// Get Quran Juz data
$sql = "SELECT * FROM quran_juz ORDER BY juz_number";
$stmt = $db->prepare($sql);
$stmt->execute();
$juz_list = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_id = sanitize($_POST['student_id']);
    $record_date = sanitize($_POST['record_date']);
    $juz_number = sanitize($_POST['juz_number']);
    $surah_name = sanitize($_POST['surah_name']);
    $page_from = sanitize($_POST['page_from']);
    $page_to = sanitize($_POST['page_to']);
    $accuracy_percentage = sanitize($_POST['accuracy_percentage']);
    $mistakes_count = sanitize($_POST['mistakes_count']);
    $remarks = sanitize($_POST['remarks']);
    $teacher_id = getUserId();

    try {
        $sql = "INSERT INTO sabqi_records (student_id, record_date, juz_number, surah_name,
                page_from, page_to, accuracy_percentage, mistakes_count, teacher_id, remarks)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $db->prepare($sql);
        $stmt->execute([
            $student_id, $record_date, $juz_number, $surah_name,
            $page_from, $page_to, $accuracy_percentage, $mistakes_count,
            $teacher_id, $remarks
        ]);

        // Also add to general progress table
        $performance = 'good';
        if ($accuracy_percentage >= 90) $performance = 'excellent';
        elseif ($accuracy_percentage >= 70) $performance = 'good';
        elseif ($accuracy_percentage >= 50) $performance = 'average';
        else $performance = 'needs_improvement';

        $sql = "INSERT INTO quran_progress (student_id, progress_date, sabak_type, juz_number,
                surah_name, page_from, page_to, performance, mistakes, teacher_id, remarks)
                VALUES (?, ?, 'revision', ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $db->prepare($sql);
        $stmt->execute([
            $student_id, $record_date, $juz_number, $surah_name,
            $page_from, $page_to, $performance, $mistakes_count, $teacher_id, $remarks
        ]);

        logActivity($db, 'Add Sabqi', 'Progress', "Added Sabqi for student ID: $student_id");

        setFlash('success', 'Sabqi (Revision) added successfully!');
        redirect(SITE_URL . '/modules/progress/view_progress.php');
    } catch(PDOException $e) {
        $error = 'Error adding record: ' . $e->getMessage();
    }
}

require_once '../../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title"><i class="fas fa-redo"></i> Add Sabqi (Revision)</h1>
    <p class="subtitle">Record recent revision progress</p>
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
                            <?php if ($student['class_name']): echo ' (' . $student['class_name'] . ')'; endif; ?>
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
                    <label for="juz_number">Juz/Para Number *</label>
                    <select name="juz_number" id="juz_number" class="form-control" required>
                        <option value="">Select Juz</option>
                        <?php foreach ($juz_list as $juz): ?>
                        <option value="<?php echo $juz['id']; ?>">
                            Juz <?php echo $juz['juz_number']; ?> - <?php echo $juz['juz_name']; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="surah_name">Surah Name *</label>
                    <input type="text" name="surah_name" id="surah_name" class="form-control"
                           placeholder="e.g., Al-Baqarah" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="page_from">Page From *</label>
                    <input type="number" name="page_from" id="page_from" class="form-control"
                           min="1" max="604" required>
                </div>

                <div class="form-group">
                    <label for="page_to">Page To *</label>
                    <input type="number" name="page_to" id="page_to" class="form-control"
                           min="1" max="604" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="accuracy_percentage">Accuracy Percentage *</label>
                    <input type="number" name="accuracy_percentage" id="accuracy_percentage" class="form-control"
                           min="0" max="100" step="0.01" placeholder="e.g., 85.50" required>
                    <small class="text-muted">Enter accuracy percentage (0-100)</small>
                </div>

                <div class="form-group">
                    <label for="mistakes_count">Number of Mistakes *</label>
                    <input type="number" name="mistakes_count" id="mistakes_count" class="form-control"
                           min="0" placeholder="0" required>
                </div>
            </div>

            <div class="form-group">
                <label for="remarks">Teacher Remarks</label>
                <textarea name="remarks" id="remarks" class="form-control" rows="4"
                          placeholder="Add any comments or observations..."></textarea>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Save Sabqi
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
        <h3><i class="fas fa-info-circle"></i> What is Sabqi?</h3>
    </div>
    <div class="card-body">
        <p><strong>Sabqi</strong> (سبقی) refers to the recent revision of previously memorized portions.</p>
        <ul>
            <li>This is the revision of recently learned pages</li>
            <li>Helps reinforce memorization</li>
            <li>Typically covers last few days/weeks of memorization</li>
            <li>Teacher evaluates accuracy and mistakes</li>
            <li>Regular Sabqi ensures retention</li>
        </ul>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>
