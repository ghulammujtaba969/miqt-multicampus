<?php
/**
 * Add Sabak (New Lesson)
 * MIQT System
 */

require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../includes/functions.php';

$pageTitle = 'Add Sabak (New Lesson)';

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
    $lines_memorized = sanitize($_POST['lines_memorized']);
    $performance_rating = sanitize($_POST['performance_rating']);
    $remarks = sanitize($_POST['remarks']);
    $teacher_id = getUserId();

    try {
        $sql = "INSERT INTO sabak_records (student_id, record_date, juz_number, surah_name,
                page_from, page_to, lines_memorized, performance_rating, teacher_id, remarks)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $db->prepare($sql);
        $stmt->execute([
            $student_id, $record_date, $juz_number, $surah_name,
            $page_from, $page_to, $lines_memorized, $performance_rating,
            $teacher_id, $remarks
        ]);

        // Also add to general progress table
        $sql = "INSERT INTO quran_progress (student_id, progress_date, sabak_type, juz_number,
                surah_name, page_from, page_to, performance, teacher_id, remarks)
                VALUES (?, ?, 'new_lesson', ?, ?, ?, ?, ?, ?, ?)";

        $performance_map = [
            '5.00' => 'excellent',
            '4.00' => 'good',
            '3.00' => 'average',
            '2.00' => 'needs_improvement'
        ];
        $performance = $performance_map[$performance_rating] ?? 'average';

        $stmt = $db->prepare($sql);
        $stmt->execute([
            $student_id, $record_date, $juz_number, $surah_name,
            $page_from, $page_to, $performance, $teacher_id, $remarks
        ]);

        logActivity($db, 'Add Sabak', 'Progress', "Added new lesson for student ID: $student_id");

        setFlash('success', 'Sabak (New Lesson) added successfully!');
        redirect(SITE_URL . '/modules/progress/view_progress.php');
    } catch(PDOException $e) {
        $error = 'Error adding record: ' . $e->getMessage();
    }
}

require_once '../../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title"><i class="fas fa-book-open"></i> Add Sabak (New Lesson)</h1>
    <p class="subtitle">Record daily new memorization</p>
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
                    <small class="text-muted">Quran has 604 pages</small>
                </div>

                <div class="form-group">
                    <label for="page_to">Page To *</label>
                    <input type="number" name="page_to" id="page_to" class="form-control"
                           min="1" max="604" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="lines_memorized">Lines Memorized</label>
                    <input type="number" name="lines_memorized" id="lines_memorized" class="form-control"
                           min="1" placeholder="Number of lines">
                </div>

                <div class="form-group">
                    <label for="performance_rating">Performance Rating *</label>
                    <select name="performance_rating" id="performance_rating" class="form-control" required>
                        <option value="">Select Rating</option>
                        <option value="5.00">Excellent (5/5) - Perfect memorization</option>
                        <option value="4.00">Good (4/5) - Minor mistakes</option>
                        <option value="3.00">Average (3/5) - Some mistakes</option>
                        <option value="2.00">Needs Improvement (2/5) - Many mistakes</option>
                        <option value="1.00">Poor (1/5) - Not memorized properly</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="remarks">Teacher Remarks</label>
                <textarea name="remarks" id="remarks" class="form-control" rows="4"
                          placeholder="Add any comments or observations..."></textarea>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Save Sabak
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
        <h3><i class="fas fa-info-circle"></i> What is Sabak?</h3>
    </div>
    <div class="card-body">
        <p><strong>Sabak</strong> (سبق) refers to the new lesson or new portion of Quran that a student memorizes each day.</p>
        <ul>
            <li>This is the daily fresh memorization</li>
            <li>Typically ranges from a few lines to a full page</li>
            <li>Student recites the new portion to the teacher</li>
            <li>Teacher evaluates and provides feedback</li>
            <li>Performance is recorded for progress tracking</li>
        </ul>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>
