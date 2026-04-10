<?php
/**
 * Add Exam Results
 * MIQT System
 */

require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../includes/functions.php';

$pageTitle = 'Add Exam Results';

$exam_id = isset($_GET['exam_id']) ? (int)$_GET['exam_id'] : 0;
$selected_subject_id = isset($_GET['subject_id']) ? (int)$_GET['subject_id'] : 0;

// Get exam details
if ($exam_id > 0) {
    $sql = "SELECT e.*, c.class_name FROM exams e
            LEFT JOIN classes c ON e.class_id = c.id
            WHERE e.id = ?";
    $stmt = $db->prepare($sql);
    $stmt->execute([$exam_id]);
    $exam = $stmt->fetch();

    if (!$exam) {
        setFlash('danger', 'Exam not found');
        redirect(SITE_URL . '/modules/exams/manage_exams.php');
    }

    // Detect schema: does exam_results have subject_id?
    $hasSubjectId = false;
    try {
        $stc = $db->prepare("SHOW COLUMNS FROM exam_results LIKE 'subject_id'");
        $stc->execute();
        $hasSubjectId = (bool)$stc->fetch();
    } catch (PDOException $e) { /* ignore */ }

    // Load subjects for this exam (if table exists)
    $subjects = [];
    try {
        $st = $db->prepare("SELECT id, subject_name, total_marks, passing_marks FROM exam_subjects WHERE exam_id = ? ORDER BY id");
        $st->execute([$exam_id]);
        $subjects = $st->fetchAll();
    } catch (PDOException $e) { /* ignore */ }

    if ($hasSubjectId && $selected_subject_id === 0 && count($subjects) == 1) {
        $selected_subject_id = (int)$subjects[0]['id'];
    }

    // Get students for this class
    if ($exam['class_id']) {
        $sql = "SELECT * FROM students WHERE class_id = ? AND status = 'active' ORDER BY first_name, last_name";
        $stmt = $db->prepare($sql);
        $stmt->execute([$exam['class_id']]);
        $students = $stmt->fetchAll();
    } else {
        $sql = "SELECT * FROM students WHERE status = 'active' ORDER BY first_name, last_name";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $students = $stmt->fetchAll();
    }

    // Check if status column exists in exam_results
    $hasStatusColumn = false;
    try {
        $stc = $db->prepare("SHOW COLUMNS FROM exam_results LIKE 'status'");
        $stc->execute();
        $hasStatusColumn = (bool)$stc->fetch();
    } catch (PDOException $e) { /* ignore */ }

    // Get existing results (for the selected subject, when applicable)
    $existing_results = [];
    $selectFields = $hasStatusColumn ? "student_id, obtained_marks, status, remarks" : "student_id, obtained_marks, remarks";

    if ($hasSubjectId && $selected_subject_id > 0) {
        $sql = "SELECT {$selectFields} FROM exam_results WHERE exam_id = ? AND subject_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$exam_id, $selected_subject_id]);
    } else {
        $sql = "SELECT {$selectFields} FROM exam_results WHERE exam_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$exam_id]);
    }
    while ($row = $stmt->fetch()) {
        if (!isset($row['status'])) {
            $row['status'] = ''; // Set default if column doesn't exist
        }
        $existing_results[$row['student_id']] = $row;
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_results'])) {
    $exam_id = sanitize($_POST['exam_id']);
    $selected_subject_id = isset($_POST['subject_id']) ? (int)$_POST['subject_id'] : 0;
    $entry_mode = $_POST['entry_mode'] ?? 'marks'; // 'marks' or 'status'
    $marks = $_POST['marks'] ?? [];
    $status_values = $_POST['status'] ?? [];
    $remarks = $_POST['remarks'] ?? [];

    try {
        $db->beginTransaction();

        // Detect available columns in exam_results (for compatibility)
        $cols = [];
        try {
            $stc = $db->prepare("SHOW COLUMNS FROM exam_results");
            $stc->execute();
            $meta = $stc->fetchAll();
            foreach ($meta as $m) { if (isset($m['Field'])) { $cols[] = $m['Field']; } }
        } catch (PDOException $e) { /* ignore */ }
        $hasPercentage = in_array('percentage', $cols, true);
        $hasStatus = in_array('status', $cols, true);
        $hasGrade = in_array('grade', $cols, true);
        $hasRemarks = in_array('remarks', $cols, true);
        $hasSubject = in_array('subject_id', $cols, true);

        // Ensure subject exists when schema requires it
        if ($hasSubject && $selected_subject_id === 0) {
            // If exam has a subject, pick the first; else create a default "Overall" subject
            $st = $db->prepare("SELECT id FROM exam_subjects WHERE exam_id = ? ORDER BY id LIMIT 1");
            $st->execute([$exam_id]);
            $row = $st->fetch();
            if ($row && isset($row['id'])) {
                $selected_subject_id = (int)$row['id'];
            } else {
                // Create default Overall subject using exam totals if available
                $totalDefault = (isset($exam['total_marks']) && is_numeric($exam['total_marks'])) ? (int)$exam['total_marks'] : null;
                $passingDefault = (isset($exam['passing_marks']) && is_numeric($exam['passing_marks'])) ? (int)$exam['passing_marks'] : null;
                try {
                    $ins = $db->prepare("INSERT INTO exam_subjects (exam_id, subject_name, total_marks, passing_marks) VALUES (?,?,?,?)");
                    $ins->execute([$exam_id, 'Overall', $totalDefault, $passingDefault]);
                    $selected_subject_id = (int)$db->lastInsertId();
                } catch (PDOException $e) {
                    // If table/columns differ, try minimal insert
                    try {
                        $ins = $db->prepare("INSERT INTO exam_subjects (exam_id, subject_name) VALUES (?,?)");
                        $ins->execute([$exam_id, 'Overall']);
                        $selected_subject_id = (int)$db->lastInsertId();
                    } catch (PDOException $e2) {
                        // Give up on creating subject; will likely fail downstream
                    }
                }
            }
        }

        // Determine which students to process based on entry mode
        $students_to_process = [];
        if ($entry_mode === 'status') {
            // Status mode: process students with status values
            foreach ($status_values as $student_id => $status_val) {
                if ($status_val !== '' && in_array($status_val, ['pass', 'fail'])) {
                    $students_to_process[$student_id] = [
                        'mode' => 'status',
                        'status' => $status_val,
                        'obtained_marks' => null,
                        'percentage' => null,
                        'grade' => null,
                        'remarks' => $remarks[$student_id] ?? ''
                    ];
                }
            }
        } else {
            // Marks mode: process students with marks
            foreach ($marks as $student_id => $obtained_marks) {
                if ($obtained_marks === '' || $obtained_marks === null) continue;

                $obtained_marks = (float)$obtained_marks;
                $hasTotal = isset($exam['total_marks']) && is_numeric($exam['total_marks']) && (float)$exam['total_marks'] > 0;
                $hasPassing = isset($exam['passing_marks']) && is_numeric($exam['passing_marks']);
                $percentage = $hasTotal ? (($obtained_marks / (float)$exam['total_marks']) * 100) : null;
                $grade = ($percentage !== null) ? calculateGrade($percentage) : null;
                $status = ($hasPassing && $percentage !== null) ? (($obtained_marks >= (float)$exam['passing_marks']) ? 'pass' : 'fail') : null;

                $students_to_process[$student_id] = [
                    'mode' => 'marks',
                    'obtained_marks' => $obtained_marks,
                    'percentage' => $percentage,
                    'grade' => $grade,
                    'status' => $status,
                    'remarks' => $remarks[$student_id] ?? ''
                ];
            }
        }

        // Process each student
        foreach ($students_to_process as $student_id => $data) {
            // Check if result already exists
            if ($hasSubject) {
                $sql = "SELECT id FROM exam_results WHERE exam_id = ? AND student_id = ? AND subject_id = ?";
                $stmt = $db->prepare($sql);
                $stmt->execute([$exam_id, $student_id, $selected_subject_id]);
            } else {
                $sql = "SELECT id FROM exam_results WHERE exam_id = ? AND student_id = ?";
                $stmt = $db->prepare($sql);
                $stmt->execute([$exam_id, $student_id]);
            }

            if ($stmt->fetch()) {
                // Dynamic UPDATE based on available columns
                $sets = [];
                $paramsUpd = [];

                if ($data['mode'] === 'marks') {
                    $sets[] = 'obtained_marks = ?';
                    $paramsUpd[] = $data['obtained_marks'];
                    if ($hasPercentage) { $sets[] = 'percentage = ?'; $paramsUpd[] = $data['percentage']; }
                    if ($hasGrade) { $sets[] = 'grade = ?'; $paramsUpd[] = $data['grade']; }
                    if ($hasStatus) { $sets[] = 'status = ?'; $paramsUpd[] = $data['status']; }
                } else {
                    // Status mode - only update status
                    if ($hasStatus) { $sets[] = 'status = ?'; $paramsUpd[] = $data['status']; }
                    // Set marks/percentage/grade to NULL in status-only mode
                    $sets[] = 'obtained_marks = ?';
                    $paramsUpd[] = null;
                    if ($hasPercentage) { $sets[] = 'percentage = ?'; $paramsUpd[] = null; }
                    if ($hasGrade) { $sets[] = 'grade = ?'; $paramsUpd[] = null; }
                }

                if ($hasRemarks) { $sets[] = 'remarks = ?'; $paramsUpd[] = $data['remarks']; }
                if ($hasSubject) { $sets[] = 'subject_id = ?'; $paramsUpd[] = $selected_subject_id; }

                $sql = 'UPDATE exam_results SET ' . implode(', ', $sets) . ' WHERE exam_id = ? AND student_id = ?' . ($hasSubject ? ' AND subject_id = ?' : '');
                $paramsUpd[] = $exam_id; $paramsUpd[] = $student_id; if ($hasSubject) { $paramsUpd[] = $selected_subject_id; }
                $stmt2 = $db->prepare($sql);
                $stmt2->execute($paramsUpd);
            } else {
                // Dynamic INSERT based on available columns
                $colsIns = ['exam_id', 'student_id'];
                $valsIns = [$exam_id, $student_id];

                if ($data['mode'] === 'marks') {
                    $colsIns[] = 'obtained_marks';
                    $valsIns[] = $data['obtained_marks'];
                    if ($hasPercentage) { $colsIns[] = 'percentage'; $valsIns[] = $data['percentage']; }
                    if ($hasGrade) { $colsIns[] = 'grade'; $valsIns[] = $data['grade']; }
                    if ($hasStatus) { $colsIns[] = 'status'; $valsIns[] = $data['status']; }
                } else {
                    // Status mode
                    $colsIns[] = 'obtained_marks';
                    $valsIns[] = null;
                    if ($hasStatus) { $colsIns[] = 'status'; $valsIns[] = $data['status']; }
                    if ($hasPercentage) { $colsIns[] = 'percentage'; $valsIns[] = null; }
                    if ($hasGrade) { $colsIns[] = 'grade'; $valsIns[] = null; }
                }

                if ($hasSubject) { $colsIns[] = 'subject_id'; $valsIns[] = $selected_subject_id; }
                if ($hasRemarks) { $colsIns[] = 'remarks'; $valsIns[] = $data['remarks']; }

                $placeholders = implode(', ', array_fill(0, count($colsIns), '?'));
                $sql = 'INSERT INTO exam_results (' . implode(', ', $colsIns) . ') VALUES (' . $placeholders . ')';
                $stmt2 = $db->prepare($sql);
                $stmt2->execute($valsIns);
            }
        }

        $db->commit();
        $examLabel = $exam['exam_title'] ?? ($exam['exam_name'] ?? ('#'.$exam_id));
        logActivity($db, 'Add Results', 'Exams', "Added results for exam: {$examLabel}");
        setFlash('success', 'Exam results saved successfully!');
        redirect(SITE_URL . '/modules/exams/view_results.php?exam_id=' . $exam_id);
    } catch(PDOException $e) {
        $db->rollBack();
        $error = 'Error saving results: ' . $e->getMessage();
    }
}

require_once '../../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title"><i class="fas fa-plus-circle"></i> Add Exam Results</h1>
    <?php if (isset($exam)): ?>
    <p class="subtitle"><?php echo ($exam['exam_title'] ?? $exam['exam_name']); ?> - <?php echo $exam['class_name'] ?? 'All Classes'; ?></p>
    <?php if (isset($hasSubjectId) && $hasSubjectId): ?>
        <p>
            <?php if (!empty($subjects) && count($subjects) > 1): ?>
                <form method="GET" action="" class="form-inline" style="display:inline-flex; gap:8px;">
                    <input type="hidden" name="exam_id" value="<?php echo (int)$exam_id; ?>">
                    <label for="subject_id"><strong>Subject:</strong></label>
                    <select name="subject_id" id="subject_id" class="form-control" onchange="this.form.submit()">
                        <option value="0">-- Select Subject --</option>
                        <?php foreach ($subjects as $sub): ?>
                        <option value="<?php echo $sub['id']; ?>" <?php echo ($selected_subject_id == $sub['id']) ? 'selected' : ''; ?>>
                            <?php echo $sub['subject_name']; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </form>
            <?php elseif (!empty($subjects) && count($subjects) == 1): ?>
                <span><strong>Subject:</strong> <?php echo $subjects[0]['subject_name']; ?></span>
            <?php else: ?>
                <span><strong>Subject:</strong> Overall (will be created)</span>
            <?php endif; ?>
        </p>
    <?php endif; ?>
    <?php endif; ?>
</div>

<?php if (isset($error)): ?>
<div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<?php if (!$exam_id || !isset($exam)): ?>
<div class="dashboard-card">
    <div class="card-body">
        <p class="text-center text-muted">Please select an exam from the manage exams page</p>
        <div class="text-center mt-20">
            <a href="manage_exams.php" class="btn btn-primary">
                <i class="fas fa-arrow-left"></i> Go to Manage Exams
            </a>
        </div>
    </div>
</div>
<?php else: ?>

<div class="dashboard-card mb-20">
    <div class="card-body">
        <div class="form-row">
            <div class="form-group">
                <strong>Total Marks:</strong> <?php echo ($exam['total_marks'] === null || $exam['total_marks'] === '') ? 'N/A' : $exam['total_marks']; ?>
            </div>
            <div class="form-group">
                <strong>Passing Marks:</strong> <?php echo ($exam['passing_marks'] === null || $exam['passing_marks'] === '') ? 'N/A' : $exam['passing_marks']; ?>
            </div>
            <div class="form-group">
                <strong>Exam Date:</strong> <?php echo formatDate($exam['exam_date']); ?>
            </div>
        </div>
    </div>
</div>

<div class="dashboard-card">
    <div class="card-body">
        <form method="POST" action="" id="resultsForm">
            <input type="hidden" name="exam_id" value="<?php echo $exam_id; ?>">
            <?php if (isset($hasSubjectId) && $hasSubjectId): ?>
                <?php if ($selected_subject_id > 0): ?>
                <input type="hidden" name="subject_id" value="<?php echo (int)$selected_subject_id; ?>">
                <?php endif; ?>
            <?php endif; ?>

            <?php if (count($students) > 0): ?>

            <!-- Entry Mode Selector -->
            <div class="form-group mb-20" style="background: #f8f9fa; padding: 15px; border-radius: 5px; border: 2px solid #dee2e6;">
                <label style="font-weight: 600; margin-bottom: 10px; display: block;">
                    <i class="fas fa-clipboard-check"></i> Entry Mode:
                </label>
                <div style="display: flex; gap: 20px; align-items: center;">
                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-weight: normal;">
                        <input type="radio" name="entry_mode" value="marks" class="entry-mode-radio" checked>
                        <span><i class="fas fa-list-ol"></i> Enter Marks (Detailed)</span>
                    </label>
                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-weight: normal;">
                        <input type="radio" name="entry_mode" value="status" class="entry-mode-radio">
                        <span><i class="fas fa-check-circle"></i> Pass/Fail Only (Quick)</span>
                    </label>
                </div>
                <p style="margin-top: 10px; margin-bottom: 0; font-size: 13px; color: #666;">
                    <strong>Marks Mode:</strong> Enter numerical marks for each student. Pass/Fail will be calculated automatically.<br>
                    <strong>Pass/Fail Mode:</strong> Simply select Pass or Fail status without entering marks.
                </p>
            </div>

            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Name</th>
                            <th class="marks-column">Obtained Marks<?php echo ($exam['total_marks'] !== null && $exam['total_marks'] !== '') ? (' (out of '.$exam['total_marks'].')') : ''; ?></th>
                            <th class="status-column" style="display: none;">Pass/Fail Status</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $student):
                            $existing = $existing_results[$student['id']] ?? null;
                            $current_marks = $existing['obtained_marks'] ?? '';
                            $current_status = $existing['status'] ?? '';
                            $current_remarks = $existing['remarks'] ?? '';
                        ?>
                        <tr>
                            <td><?php echo $student['student_id']; ?></td>
                            <td>
                                <strong><?php echo $student['first_name'] . ' ' . $student['last_name']; ?></strong>
                            </td>
                            <td class="marks-column">
                                <input type="number" name="marks[<?php echo $student['id']; ?>]"
                                       class="form-control marks-input" min="0" <?php echo ($exam['total_marks'] !== null && $exam['total_marks'] !== '') ? ('max="'.$exam['total_marks'].'"') : ''; ?>
                                       step="0.5" value="<?php echo $current_marks; ?>"
                                       placeholder="Enter marks">
                            </td>
                            <td class="status-column" style="display: none;">
                                <select name="status[<?php echo $student['id']; ?>]" class="form-control status-input">
                                    <option value="">-- Select --</option>
                                    <option value="pass" <?php echo ($current_status === 'pass') ? 'selected' : ''; ?>>✓ Pass</option>
                                    <option value="fail" <?php echo ($current_status === 'fail') ? 'selected' : ''; ?>>✗ Fail</option>
                                </select>
                            </td>
                            <td>
                                <input type="text" name="remarks[<?php echo $student['id']; ?>]"
                                       class="form-control" value="<?php echo htmlspecialchars($current_remarks); ?>"
                                       placeholder="Optional remarks">
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <script>
            document.addEventListener('DOMContentLoaded', function() {
                const modeRadios = document.querySelectorAll('.entry-mode-radio');
                const marksColumns = document.querySelectorAll('.marks-column');
                const statusColumns = document.querySelectorAll('.status-column');

                modeRadios.forEach(radio => {
                    radio.addEventListener('change', function() {
                        if (this.value === 'marks') {
                            // Show marks, hide status
                            marksColumns.forEach(col => col.style.display = '');
                            statusColumns.forEach(col => col.style.display = 'none');
                        } else {
                            // Show status, hide marks
                            marksColumns.forEach(col => col.style.display = 'none');
                            statusColumns.forEach(col => col.style.display = '');
                        }
                    });
                });
            });
            </script>

            <div class="form-group mt-20">
                <button type="submit" name="submit_results" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Results
                </button>
                <a href="view_results.php?exam_id=<?php echo $exam_id; ?>" class="btn btn-secondary">
                    <i class="fas fa-eye"></i> View Results
                </a>
                <a href="manage_exams.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Exams
                </a>
            </div>

            <?php else: ?>
            <p class="text-center text-muted">No students found for this class</p>
            <?php endif; ?>
        </form>
    </div>
</div>

<?php endif; ?>

<?php require_once '../../includes/footer.php'; ?>
