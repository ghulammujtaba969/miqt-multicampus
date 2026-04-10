<?php
/**
 * Daily Quran Progress Entry (Unified Page)
 * MIQT System - Abu Bakar House Format
 */

require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../includes/functions.php';

$pageTitle = 'Daily Quran Progress';

// Get selected parameters
$progress_date = isset($_POST['progress_date']) ? $_POST['progress_date'] : date('Y-m-d');
$class_id = isset($_POST['class_id']) ? (int)$_POST['class_id'] : 0;
$teacher_id = isset($_POST['teacher_id']) ? (int)$_POST['teacher_id'] : 0;
$prepared_by = isset($_POST['prepared_by']) ? (int)$_POST['prepared_by'] : $_SESSION['user_id'];

// Get all classes for dropdown
$sql = "SELECT id, class_name FROM classes WHERE status = 'active' ORDER BY class_name";
$stmt = $db->prepare($sql);
$stmt->execute();
$classes = $stmt->fetchAll();

// Get all teachers for dropdown
$sql = "SELECT id, teacher_id, first_name, last_name FROM teachers WHERE status = 'active' ORDER BY first_name, last_name";
$stmt = $db->prepare($sql);
$stmt->execute();
$teachers = $stmt->fetchAll();

// Get all users for "Prepared By" dropdown
$sql = "SELECT id, full_name, role FROM users WHERE status = 'active' ORDER BY full_name";
$stmt = $db->prepare($sql);
$stmt->execute();
$users = $stmt->fetchAll();

// Get class teacher if class is selected
$class_teacher_id = null;
$class_teacher_name = '';
if ($class_id > 0) {
    $sql = "SELECT c.class_teacher_id, t.first_name, t.last_name
            FROM classes c
            LEFT JOIN teachers t ON c.class_teacher_id = t.id
            WHERE c.id = ?";
    $stmt = $db->prepare($sql);
    $stmt->execute([$class_id]);
    $class_info = $stmt->fetch();
    if ($class_info && $class_info['class_teacher_id']) {
        $class_teacher_id = $class_info['class_teacher_id'];
        $class_teacher_name = $class_info['first_name'] . ' ' . $class_info['last_name'];
    }
}

// Get students for selected class
$students = [];
if ($class_id > 0) {
    $sql = "SELECT s.id, s.student_id, s.first_name, s.last_name, s.father_name, s.admission_date,
            (SELECT CONCAT('Para ', juz_number, ' - ', surah_name)
             FROM sabak_records
             WHERE student_id = s.id
             ORDER BY record_date DESC LIMIT 1) as current_progress
            FROM students s
            WHERE s.class_id = ? AND s.status = 'active'
            ORDER BY s.first_name, s.last_name";
    $stmt = $db->prepare($sql);
    $stmt->execute([$class_id]);
    $students = $stmt->fetchAll();
}

// Get Juz list for dropdowns from juz_reference with Arabic names and number of lines
$sql = "SELECT id, juz_number, juz_name_arabic, juz_name_english, number_of_lines FROM juz_reference ORDER BY juz_number";
$stmt = $db->prepare($sql);
$stmt->execute();
$juz_list = $stmt->fetchAll();

// Get all Surahs with Arabic names
$sql = "SELECT id, surah_number, surah_name_ar, surah_name_en FROM quran_surahs ORDER BY surah_number";
$stmt = $db->prepare($sql);
$stmt->execute();
$surah_list = $stmt->fetchAll();

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_progress'])) {
    $progress_date = sanitize($_POST['progress_date']);
    $class_id = (int)$_POST['class_id'];

    try {
        $db->beginTransaction();

        // Loop through students
        foreach ($_POST['students'] as $student_id => $data) {
            $student_id = (int)$student_id;

            // Save Sabak (New Lesson)
            if (!empty($data['sabak']['juz_number'])) {
                $page_range = isset($data['sabak']['page_from']) ? explode('-', $data['sabak']['page_from']) : [];
                $page_from = isset($page_range[0]) ? trim($page_range[0]) : null;
                $page_to = isset($page_range[1]) ? trim($page_range[1]) : $page_from;

                $sql = "INSERT INTO sabak_records
                        (student_id, record_date, juz_number, surah_name, page_from, page_to, lines_memorized, performance_rating, teacher_id, remarks)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $db->prepare($sql);
                $stmt->execute([
                    $student_id,
                    $progress_date,
                    $data['sabak']['juz_number'],
                    $data['sabak']['surah_name'] ?? null,
                    $page_from,
                    $page_to,
                    isset($data['sabak']['lines']) && $data['sabak']['lines'] !== '' ? (int)$data['sabak']['lines'] : null,
                    null,
                    $_POST['teacher_id'],
                    null
                ]);
            }

            // Save Sabqi (Revision) - Para + Amount (quarter/half/three_quarter/full)
            if (!empty($data['sabqi']['juz_number'])) {
                $amountKey = isset($data['sabqi']['amount']) ? trim($data['sabqi']['amount']) : '';
                $amountMap = [
                    'quarter' => ['val' => 1, 'label' => 'Quarter'],
                    'half' => ['val' => 2, 'label' => 'Half'],
                    'three_quarter' => ['val' => 3, 'label' => 'Three Quarter'],
                    'full' => ['val' => 4, 'label' => 'Full'],
                ];
                $mapped = $amountMap[$amountKey] ?? ['val' => 0, 'label' => ''];
                $page_from = $mapped['val'];
                $page_to = $mapped['val'];
                $remarks = $mapped['label'] ? ('Amount: ' . $mapped['label']) : null;

                $sql = "INSERT INTO sabqi_records
                        (student_id, record_date, juz_number, surah_name, page_from, page_to, accuracy_percentage, mistakes_count, teacher_id, remarks)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $db->prepare($sql);
                $stmt->execute([
                    $student_id,
                    $progress_date,
                    $data['sabqi']['juz_number'],
                    '',
                    $page_from,
                    $page_to,
                    null,
                    0,
                    $_POST['teacher_id'],
                    $remarks
                ]);
            }

            // Save Manzil (Complete Revision) - Para + Amount select + Heard By
            if (!empty($data['manzil']['juz_start'])) {
                $start = (int)$data['manzil']['juz_start'];
                $amountKey = isset($data['manzil']['amount']) ? trim($data['manzil']['amount']) : '';
                $amountMap = [
                    'quarter' => 'Quarter',
                    'half' => 'Half',
                    'three_quarter' => 'Three Quarter',
                    'full' => 'Full',
                ];
                $amountLabel = $amountMap[$amountKey] ?? '';
                $heardBy = isset($data['manzil']['heard_by']) ? trim($data['manzil']['heard_by']) : '';
                $remarksParts = [];
                if ($amountLabel) { $remarksParts[] = 'Amount: ' . $amountLabel; }
                if ($heardBy) { $remarksParts[] = 'Heard by: ' . $heardBy; }
                $remarks = $remarksParts ? implode(' | ', $remarksParts) : null;

                // Keep range as same para since amount is fractional selection
                $juz_to = $start;

                $sql = "INSERT INTO manzil_records
                        (student_id, record_date, juz_from, juz_to, completion_time, accuracy_percentage, teacher_id, remarks)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $db->prepare($sql);
                $stmt->execute([
                    $student_id,
                    $progress_date,
                    $start,
                    $juz_to,
                    null,
                    null,
                    $_POST['teacher_id'],
                    $remarks
                ]);
            }
        }

        $db->commit();
        logActivity($db, 'Save Daily Progress', 'Progress', "Saved daily progress for date: $progress_date");
        setFlash('success', 'Daily progress saved successfully!');
        redirect(SITE_URL . '/modules/progress/daily_progress.php');
    } catch(PDOException $e) {
        $db->rollBack();
        $error = 'Error saving progress: ' . $e->getMessage();
    }
}

require_once '../../includes/header.php';
?>

<style>
/* Print-friendly styles */
@media print {
    .sidebar, .topbar, .no-print { display: none !important; }
    .main-content { margin-left: 0 !important; width: 100% !important; }
    .progress-table { font-size: 11px; }
    .progress-table th, .progress-table td { padding: 3px 5px; }
}

.progress-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    font-size: 12px;
}

.progress-table th,
.progress-table td {
    border: 1px solid #333;
    padding: 8px 5px;
    text-align: center;
    vertical-align: middle;
}

.progress-table th {
    background-color: var(--primary-color);
    color: white;
    font-weight: 600;
}

.progress-table input[type="text"],
.progress-table input[type="number"],
.progress-table select {
    width: 100%;
    padding: 4px;
    border: 1px solid #ddd;
    font-size: 11px;
}

.header-row {
    background: #f8f9fa;
    padding: 15px;
    margin-bottom: 20px;
    border: 1px solid #dee2e6;
    border-radius: 5px;
}

.form-row-inline {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
    align-items: flex-end;
}

.form-row-inline .form-group {
    flex: 1;
    min-width: 200px;
}

/* Hide extra columns to match simplified layout */
/* Table layout now matches required columns; no column hiding needed. */
</style>

<div class="page-header no-print">
    <h1 class="page-title"><i class="fas fa-book-quran"></i> Daily Quran Progress Entry</h1>
    <p class="subtitle">Record daily Sabak, Sabqi, and Manzil in one place</p>
</div>

<?php if (isset($error)): ?>
<div class="alert alert-danger no-print"><?php echo $error; ?></div>
<?php endif; ?>

<div class="dashboard-card">
    <div class="card-body">
        <form method="POST" action="">
            <!-- Header Row -->
            <div class="header-row no-print">
                <div class="form-row-inline">
                    <div class="form-group">
                        <label for="progress_date">تاریخ / Date *</label>
                        <input type="date" name="progress_date" id="progress_date" class="form-control"
                               value="<?php echo $progress_date; ?>" required onchange="this.form.submit()">
                    </div>

                    <div class="form-group">
                        <label for="class_id">گریڈ / Class *</label>
                        <select name="class_id" id="class_id" class="form-control" required onchange="this.form.submit()">
                            <option value="">-- Select Class --</option>
                            <?php foreach ($classes as $class): ?>
                            <option value="<?php echo $class['id']; ?>" <?php echo ($class_id == $class['id']) ? 'selected' : ''; ?>>
                                <?php echo $class['class_name']; ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="teacher_id">نام ہاؤس انچارج / Teacher *</label>
                        <select name="teacher_id" id="teacher_id" class="form-control" required>
                            <option value="">-- Select Teacher --</option>
                            <?php foreach ($teachers as $teacher): ?>
                            <option value="<?php echo $teacher['id']; ?>"
                                <?php echo ($teacher_id == $teacher['id'] || $class_teacher_id == $teacher['id']) ? 'selected' : ''; ?>>
                                <?php echo $teacher['first_name'] . ' ' . $teacher['last_name']; ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="prepared_by">Prepared By *</label>
                        <select name="prepared_by" id="prepared_by" class="form-control" required>
                            <?php foreach ($users as $user): ?>
                            <option value="<?php echo $user['id']; ?>" <?php echo ($prepared_by == $user['id']) ? 'selected' : ''; ?>>
                                <?php echo $user['full_name'] . ' (' . ucfirst($user['role']) . ')'; ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <?php if ($class_id > 0 && count($students) > 0): ?>
            <!-- Progress Table -->
            <div style="overflow-x: auto;">
                <table class="progress-table">
                    <thead>
                        <tr>
                            <th rowspan="2" style="width: 30px;">نمبر شمار<br>S.No</th>
                            <th rowspan="2" style="width: 120px;">نام طالب علم<br>Student Name</th>
                            <th rowspan="2" style="width: 100px;">ولدیت<br>Father Name</th>
                            <th rowspan="2" style="width: 80px;">تاریخ داخلہ<br>Admission</th>
                            <th rowspan="2" style="width: 100px;">پراگرس<br>Progress</th>
                            <th colspan="4">سبق (New Lesson)</th>
                            <th colspan="1">سبقی (Revision)</th>
                            <th colspan="3">منزل (Complete Revision)</th>
                        </tr>
                        <tr>
                            <!-- Sabak -->
                            <th style="width: 60px;">پارہ نمبر<br>Para#</th>
                            <th style="width: 100px;">نام سورۃ<br>Surah</th>
                            <th style="width: 80px;">تعین (آیات)<br>Ayat #</th>
                            <th style="width: 50px;">لائنیں<br>Lines</th>

                            <!-- Sabqi -->
                            <th style="width: 60px;">پارہ<br>Para</th>

                            <!-- Manzil -->
                            <th style="width: 80px;">مقدار<br>Quantity</th>
                            <th style="width: 80px;">پارہ<br>Para</th>
                            <th style="width: 100px;">کس کو سنائی<br>Heard By</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $serial = 1;
                        foreach ($students as $student):
                        ?>
                        <tr>
                            <td><?php echo $serial++; ?></td>
                            <td style="text-align: left;">
                                <strong><?php echo $student['first_name'] . ' ' . $student['last_name']; ?></strong><br>
                                <small><?php echo $student['student_id']; ?></small>
                            </td>
                            <td style="text-align: left;"><?php echo $student['father_name']; ?></td>
                            <td><?php echo date('d/m/Y', strtotime($student['admission_date'])); ?></td>
                            <td><?php echo $student['current_progress'] ?? 'N/A'; ?></td>

                            <!-- Sabak Fields -->
                            <td>
                                <select name="students[<?php echo $student['id']; ?>][sabak][juz_number]" class="form-control sabak-juz-select" data-student-id="<?php echo $student['id']; ?>">
                                    <option value="">-</option>
                                    <?php foreach ($juz_list as $juz): ?>
                                    <option value="<?php echo $juz['juz_number']; ?>" data-lines="<?php echo $juz['number_of_lines'] ?? 0; ?>">
                                        <?php echo $juz['juz_number'] . ' - ' . ($juz['juz_name_arabic'] ?? ''); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td>
                                <select name="students[<?php echo $student['id']; ?>][sabak][surah_name]" class="form-control">
                                    <option value="">-</option>
                                    <?php foreach ($surah_list as $surah): ?>
                                    <option value="<?php echo htmlspecialchars($surah['surah_name_en']); ?>">
                                        <?php echo $surah['surah_number'] . '. ' . $surah['surah_name_ar'] . ' (' . $surah['surah_name_en'] . ')'; ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td><input type="text" name="students[<?php echo $student['id']; ?>][sabak][page_from]" class="form-control" placeholder="From-To"></td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 2px;">
                                    <input type="number" name="students[<?php echo $student['id']; ?>][sabak][lines]"
                                           class="form-control sabak-lines-input"
                                           data-student-id="<?php echo $student['id']; ?>"
                                           min="0"
                                           style="width: 50%; padding: 2px;"
                                           placeholder="0">
                                    <span style="font-weight: bold;">/</span>
                                    <span class="sabak-total-lines" data-student-id="<?php echo $student['id']; ?>" style="width: 45%; text-align: center; font-weight: bold;">-</span>
                                </div>
                                <div class="progress" style="height: 8px; margin-top: 4px; background-color: #e0e0e0; border-radius: 4px; overflow: hidden;">
                                    <div class="sabak-progress-bar" 
                                         data-student-id="<?php echo $student['id']; ?>"
                                         style="height: 100%; width: 0%; background-color: #28a745; transition: width 0.3s ease;">
                                    </div>
                                </div>
                                <small class="sabak-progress-text" data-student-id="<?php echo $student['id']; ?>" style="display: block; text-align: center; margin-top: 2px; font-size: 10px; color: #666;">0%</small>
                            </td>

                            <!-- Sabqi Fields -->
                            <td>
                                <input type="text"
                                       name="students[<?php echo $student['id']; ?>][sabqi][juz_number]"
                                       class="form-control sabqi-juz-display"
                                       data-student-id="<?php echo $student['id']; ?>"
                                       readonly
                                       style="background-color: #f0f0f0; text-align: center;"
                                       placeholder="-">
                            </td>

                            <!-- Manzil Fields -->
                            <td>
                                <select name="students[<?php echo $student['id']; ?>][manzil][amount]" class="form-control">
                                    <option value="">-</option>
                                    <option value="half">نصف / Half</option>
                                    <option value="1">ایک / 1</option>
                                    <option value="1.5">ڈیڑھ / 1½</option>
                                    <option value="2">دو / 2</option>
                                </select>
                            </td>
                            <td>
                                <input type="text"
                                       name="students[<?php echo $student['id']; ?>][manzil][juz_start]"
                                       class="form-control"
                                       placeholder="e.g. 1, 2-3"
                                       style="text-align: center;">
                            </td>
                            <td>
                                <select name="students[<?php echo $student['id']; ?>][manzil][heard_by]" class="form-control">
                                    <option value="">-- Select --</option>
                                    <optgroup label="Teachers">
                                        <?php foreach ($teachers as $teacher): ?>
                                        <option value="<?php echo htmlspecialchars($teacher['first_name'] . ' ' . $teacher['last_name'] . ' (Teacher)'); ?>">
                                            <?php echo $teacher['first_name'] . ' ' . $teacher['last_name']; ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </optgroup>
                                    <optgroup label="Students">
                                        <?php foreach ($students as $s2): ?>
                                        <option value="<?php echo htmlspecialchars($s2['first_name'] . ' ' . $s2['last_name']); ?>">
                                            <?php echo $s2['first_name'] . ' ' . $s2['last_name']; ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </optgroup>
                                </select>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Action Buttons -->
            <div class="form-group mt-20 no-print">
                <button type="submit" name="save_progress" class="btn btn-success">
                    <i class="fas fa-save"></i> Save Progress
                </button>
                <button type="button" class="btn btn-info" onclick="window.print()">
                    <i class="fas fa-print"></i> Print
                </button>
                <a href="progress_list.php" class="btn btn-secondary">
                    <i class="fas fa-list"></i> Records List
                </a>
                <?php if ($class_id > 0): ?>
                <a href="<?php echo SITE_URL; ?>/modules/reports/export_daily_class_pdf.php?class_id=<?php echo (int)$class_id; ?>&date=<?php echo urlencode($progress_date); ?>" class="btn btn-danger">
                    <i class="fas fa-file-pdf"></i> Export Daily PDF
                </a>
                <?php endif; ?>
            </div>

            <?php elseif ($class_id > 0): ?>
            <p class="text-center text-muted">No students found in the selected class.</p>
            <?php else: ?>
            <p class="text-center text-muted">Please select a date and class to begin.</p>
            <?php endif; ?>
        </form>
    </div>
</div>

<script>
// Function to update progress bar
function updateProgressBar(studentId) {
    const linesInput = document.querySelector('.sabak-lines-input[data-student-id="' + studentId + '"]');
    const totalLinesSpan = document.querySelector('.sabak-total-lines[data-student-id="' + studentId + '"]');
    const progressBar = document.querySelector('.sabak-progress-bar[data-student-id="' + studentId + '"]');
    const progressText = document.querySelector('.sabak-progress-text[data-student-id="' + studentId + '"]');
    
    if (!linesInput || !totalLinesSpan || !progressBar || !progressText) return;
    
    const covered = parseFloat(linesInput.value) || 0;
    const total = parseFloat(totalLinesSpan.textContent) || 0;
    
    let percentage = 0;
    if (total > 0) {
        percentage = Math.min(100, Math.round((covered / total) * 100));
    }
    
    progressBar.style.width = percentage + '%';
    progressText.textContent = percentage + '%';
    
    // Change color based on percentage
    if (percentage >= 80) {
        progressBar.style.backgroundColor = '#28a745'; // Green
    } else if (percentage >= 50) {
        progressBar.style.backgroundColor = '#ffc107'; // Yellow
    } else if (percentage > 0) {
        progressBar.style.backgroundColor = '#dc3545'; // Red
    } else {
        progressBar.style.backgroundColor = '#e0e0e0'; // Gray
    }
}

// Update total lines and Sabqi Para when Juz is selected for Sabak
document.addEventListener('DOMContentLoaded', function() {
    const juzSelects = document.querySelectorAll('.sabak-juz-select');

    juzSelects.forEach(function(select) {
        select.addEventListener('change', function() {
            const studentId = this.getAttribute('data-student-id');
            const selectedOption = this.options[this.selectedIndex];
            const totalLines = selectedOption.getAttribute('data-lines') || '-';
            const juzNumber = this.value;

            // Update the total lines display for Sabak
            const totalLinesSpan = document.querySelector('.sabak-total-lines[data-student-id="' + studentId + '"]');
            if (totalLinesSpan) {
                totalLinesSpan.textContent = totalLines;
            }

            // Auto-populate Sabqi Para with the same Juz number from Sabak
            const sabqiJuzInput = document.querySelector('.sabqi-juz-display[data-student-id="' + studentId + '"]');
            if (sabqiJuzInput) {
                sabqiJuzInput.value = juzNumber || '';
            }
            
            // Update progress bar
            updateProgressBar(studentId);
        });
    });
    
    // Update progress bar when lines input changes
    const linesInputs = document.querySelectorAll('.sabak-lines-input');
    linesInputs.forEach(function(input) {
        input.addEventListener('input', function() {
            const studentId = this.getAttribute('data-student-id');
            updateProgressBar(studentId);
        });
        
        // Also update on page load if there's a value
        const studentId = input.getAttribute('data-student-id');
        if (input.value) {
            updateProgressBar(studentId);
        }
    });
});
</script>

<?php require_once '../../includes/footer.php'; ?>
