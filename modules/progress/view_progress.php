<?php
/**
 * Read-only Daily Progress Viewer
 * Mirrors the Daily Progress table but shows saved values.
 */

require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../includes/functions.php';

$pageTitle = 'View Daily Quran Progress';

// Filters (GET or POST to allow direct links)
$progress_date = isset($_GET['date']) ? $_GET['date'] : (isset($_POST['progress_date']) ? $_POST['progress_date'] : date('Y-m-d'));
$class_id = isset($_GET['class_id']) ? (int)$_GET['class_id'] : (isset($_POST['class_id']) ? (int)$_POST['class_id'] : 0);
$teacher_id = isset($_GET['teacher_id']) ? (int)$_GET['teacher_id'] : (isset($_POST['teacher_id']) ? (int)$_POST['teacher_id'] : 0);

// Dropdowns
$stmt = $db->prepare("SELECT id, class_name FROM classes WHERE status = 'active' ORDER BY class_name");
$stmt->execute();
$classes = $stmt->fetchAll();

$stmt = $db->prepare("SELECT id, first_name, last_name FROM teachers WHERE status = 'active' ORDER BY first_name, last_name");
$stmt->execute();
$teachers = $stmt->fetchAll();

// Students for class
$students = [];
if ($class_id > 0) {
    $stmt = $db->prepare("SELECT id, student_id, first_name, last_name, father_name, admission_date FROM students WHERE class_id = ? AND status = 'active' ORDER BY first_name, last_name");
    $stmt->execute([$class_id]);
    $students = $stmt->fetchAll();
}

// Get Juz list from juz_reference with Arabic names and number of lines
$sql = "SELECT id, juz_number, juz_name_arabic, juz_name_english, number_of_lines FROM juz_reference ORDER BY juz_number";
$stmt = $db->prepare($sql);
$stmt->execute();
$juz_list = $stmt->fetchAll();
// Create a map for quick lookup
$juz_map = [];
foreach ($juz_list as $juz) {
    $juz_map[$juz['juz_number']] = $juz;
}

// Get all Surahs with Arabic names
$sql = "SELECT id, surah_number, surah_name_ar, surah_name_en FROM quran_surahs ORDER BY surah_number";
$stmt = $db->prepare($sql);
$stmt->execute();
$surah_list = $stmt->fetchAll();

// Helper: map by student_id from a list (keeping latest by id)
function indexByStudent($rows, $key = 'student_id') {
    $map = [];
    foreach ($rows as $r) {
        $map[$r[$key]] = $r;
    }
    return $map;
}

// Bulk fetch records for the selected date & class
$sabak_map = $sabqi_map = $manzil_map = [];
if ($class_id > 0 && $progress_date) {
    // Collect student IDs
    $sids = array_map(function($s){ return (int)$s['id']; }, $students);
    if (count($sids) > 0) {
        $placeholders = implode(',', array_fill(0, count($sids), '?'));

        // Sabak
        $params = array_merge([$progress_date], $sids);
        $teacherFilter = '';
        if ($teacher_id > 0) { $teacherFilter = ' AND r.teacher_id = ? '; $params[] = $teacher_id; }
        $sql = "SELECT r.*, r.student_id FROM sabak_records r WHERE r.record_date = ? AND r.student_id IN ($placeholders) {$teacherFilter} ORDER BY r.id DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll();
        $sabak_map = indexByStudent($rows);

        // Sabqi
        $params = array_merge([$progress_date], $sids);
        if ($teacher_id > 0) { $teacherFilter = ' AND r.teacher_id = ? '; $params[] = $teacher_id; } else { $teacherFilter = ''; }
        $sql = "SELECT r.*, r.student_id FROM sabqi_records r WHERE r.record_date = ? AND r.student_id IN ($placeholders) {$teacherFilter} ORDER BY r.id DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll();
        $sabqi_map = indexByStudent($rows);

        // Manzil
        $params = array_merge([$progress_date], $sids);
        if ($teacher_id > 0) { $teacherFilter = ' AND r.teacher_id = ? '; $params[] = $teacher_id; } else { $teacherFilter = ''; }
        $sql = "SELECT r.*, r.student_id FROM manzil_records r WHERE r.record_date = ? AND r.student_id IN ($placeholders) {$teacherFilter} ORDER BY r.id DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll();
        $manzil_map = indexByStudent($rows);

        // Fallbacks from general table (quran_progress) for legacy data
        // Sabak fallback: sabak_type = 'new_lesson'
        $params = array_merge([$progress_date], $sids);
        $teacherFilter = '';
        if ($teacher_id > 0) { $teacherFilter = ' AND qp.teacher_id = ? '; $params[] = $teacher_id; }
        $sql = "SELECT qp.student_id, qp.juz_number, qp.surah_name, qp.page_from, qp.page_to
                FROM quran_progress qp
                WHERE qp.progress_date = ? AND qp.sabak_type = 'new_lesson'
                AND qp.student_id IN ($placeholders) {$teacherFilter}
                ORDER BY qp.id DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll();
        foreach ($rows as $r) {
            $sid = (int)$r['student_id'];
            if (!isset($sabak_map[$sid])) {
                $sabak_map[$sid] = [
                    'student_id' => $sid,
                    'juz_number' => $r['juz_number'],
                    'surah_name' => $r['surah_name'],
                    'page_from' => $r['page_from'],
                    'page_to' => $r['page_to'],
                    'lines_memorized' => null
                ];
            }
        }

        // Sabqi fallback: sabak_type = 'revision'
        $params = array_merge([$progress_date], $sids);
        $teacherFilter = '';
        if ($teacher_id > 0) { $teacherFilter = ' AND qp.teacher_id = ? '; $params[] = $teacher_id; }
        $sql = "SELECT qp.student_id, qp.juz_number, qp.page_from, qp.page_to, qp.remarks
                FROM quran_progress qp
                WHERE qp.progress_date = ? AND qp.sabak_type = 'revision'
                AND qp.student_id IN ($placeholders) {$teacherFilter}
                ORDER BY qp.id DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll();
        foreach ($rows as $r) {
            $sid = (int)$r['student_id'];
            if (!isset($sabqi_map[$sid])) {
                $sabqi_map[$sid] = [
                    'student_id' => $sid,
                    'juz_number' => $r['juz_number'],
                    'page_from' => $r['page_from'],
                    'page_to' => $r['page_to'],
                    'remarks' => $r['remarks']
                ];
            }
        }

        // Manzil fallback: sabak_type = 'manzil'
        $params = array_merge([$progress_date], $sids);
        $teacherFilter = '';
        if ($teacher_id > 0) { $teacherFilter = ' AND qp.teacher_id = ? '; $params[] = $teacher_id; }
        $sql = "SELECT qp.student_id, qp.juz_number, qp.page_from, qp.page_to, qp.remarks
                FROM quran_progress qp
                WHERE qp.progress_date = ? AND qp.sabak_type = 'manzil'
                AND qp.student_id IN ($placeholders) {$teacherFilter}
                ORDER BY qp.id DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll();
        foreach ($rows as $r) {
            $sid = (int)$r['student_id'];
            if (!isset($manzil_map[$sid])) {
                $manzil_map[$sid] = [
                    'student_id' => $sid,
                    'juz_from' => $r['juz_number'],
                    'juz_to' => $r['juz_number'],
                    'remarks' => $r['remarks']
                ];
            }
        }
    }
}

// Mapping helpers
function manzilQuantityLabel($val) {
    $val = trim($val);
    switch ($val) {
        case 'half': return 'نصف / Half';
        case '1': return 'ایک / 1';
        case '1.5': return 'ڈیڑھ / 1½';
        case '2': return 'دو / 2';
        default: return $val;
    }
}

function parseRemarkPart($remark, $key) {
    if (!$remark) return '';
    // Expect parts like "Amount: Label | Heard by: Name" or "Quantity: Label | Heard by: Name"
    $parts = explode('|', $remark);
    foreach ($parts as $p) {
        $p = trim($p);
        if (stripos($p, $key . ':') === 0) {
            return trim(substr($p, strlen($key . ':')));
        }
    }
    return '';
}

require_once '../../includes/header.php';
?>

<style>
@media print {
    .sidebar, .topbar, .no-print { display: none !important; }
    .main-content { margin-left: 0 !important; width: 100% !important; }
}
.progress-table { width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 12px; }
.progress-table th, .progress-table td { border: 1px solid #333; padding: 8px 5px; text-align: center; vertical-align: middle; }
.progress-table th { background-color: var(--primary-color); color: white; font-weight: 600; }
</style>

<div class="page-header no-print">
    <h1 class="page-title"><i class="fas fa-eye"></i> View Daily Quran Progress</h1>
    <p class="subtitle">Read-only view of Sabak, Sabqi, and Manzil</p>
    <p>
        <a class="btn btn-secondary" href="<?php echo SITE_URL; ?>/modules/progress/progress_list.php"><i class="fas fa-list"></i> All Records</a>
        <a class="btn btn-info" href="<?php echo SITE_URL; ?>/modules/progress/daily_progress.php"><i class="fas fa-edit"></i> Back to Entry</a>
    </p>
</div>

<div class="dashboard-card">
    <div class="card-body">
        <form method="GET" action="">
            <div class="header-row no-print" style="background: #f8f9fa; padding: 15px; margin-bottom: 20px; border: 1px solid #dee2e6; border-radius: 5px;">
                <div class="form-row" style="display:flex; gap:15px; flex-wrap:wrap; align-items:flex-end;">
                    <div class="form-group">
                        <label for="progress_date">Date</label>
                        <input type="date" name="date" id="progress_date" class="form-control" value="<?php echo htmlspecialchars($progress_date); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="class_id">Class</label>
                        <select name="class_id" id="class_id" class="form-control" required>
                            <option value="">-- Select Class --</option>
                            <?php foreach ($classes as $class): ?>
                            <option value="<?php echo $class['id']; ?>" <?php echo ($class_id == $class['id']) ? 'selected' : ''; ?>><?php echo $class['class_name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="teacher_id">Teacher (optional)</label>
                        <select name="teacher_id" id="teacher_id" class="form-control">
                            <option value="0">All Teachers</option>
                            <?php foreach ($teachers as $t): ?>
                            <option value="<?php echo $t['id']; ?>" <?php echo ($teacher_id == $t['id']) ? 'selected' : ''; ?>><?php echo $t['first_name'] . ' ' . $t['last_name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group" style="align-self:flex-end;">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Load</button>
                        <button type="button" class="btn btn-info" onclick="window.print()"><i class="fas fa-print"></i> Print</button>
                    </div>
                </div>
            </div>
        </form>

        <?php if ($class_id > 0 && count($students) > 0): ?>
        <div style="overflow-x:auto;">
            <table class="progress-table">
                <thead>
                    <tr>
                        <th rowspan="2" style="width:30px;">نمبر شمار<br>S.No</th>
                        <th rowspan="2" style="width:140px;">نام طالب علم<br>Student Name</th>
                        <th rowspan="2" style="width:120px;">ولدیت<br>Father Name</th>
                        <th rowspan="2" style="width:90px;">تاریخ داخلہ<br>Admission</th>
                        <th colspan="4">سبق (New Lesson)</th>
                        <th colspan="1">سبقی (Revision)</th>
                        <th colspan="3">منزل (Complete Revision)</th>
                    </tr>
                    <tr>
                        <!-- Sabak -->
                        <th style="width:80px;">پارہ نمبر<br>Para#</th>
                        <th style="width:120px;">نام سورۃ<br>Surah</th>
                        <th style="width:90px;">تعین (آیات)<br>Ayat #</th>
                        <th style="width:60px;">لائنیں<br>Lines</th>
                        <!-- Sabqi -->
                        <th style="width:80px;">پارہ<br>Para</th>
                        <!-- Manzil -->
                        <th style="width:100px;">مقدار<br>Quantity</th>
                        <th style="width:80px;">پارہ<br>Para</th>
                        <th style="width:140px;">کس کو سنائی<br>Heard By</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $serial = 1; foreach ($students as $s): ?>
                    <?php
                        $sid = (int)$s['id'];
                        $sabak = isset($sabak_map[$sid]) ? $sabak_map[$sid] : null;
                        $sabqi = isset($sabqi_map[$sid]) ? $sabqi_map[$sid] : null;
                        $manzil = isset($manzil_map[$sid]) ? $manzil_map[$sid] : null;

                        // Sabak: Display Juz with Arabic name
                        $sabakJuz = '—';
                        if ($sabak && $sabak['juz_number']) {
                            $juzNum = $sabak['juz_number'];
                            $sabakJuz = $juzNum;
                            if (isset($juz_map[$juzNum]) && $juz_map[$juzNum]['juz_name_arabic']) {
                                $sabakJuz = $juzNum . ' - ' . $juz_map[$juzNum]['juz_name_arabic'];
                            }
                        }

                        // Sabak: Page/Ayat range
                        $sabakRange = $sabak ? trim(($sabak['page_from'] !== null ? $sabak['page_from'] : '')) . (($sabak['page_to'] !== null && $sabak['page_to'] !== $sabak['page_from']) ? ('-' . $sabak['page_to']) : '') : '';

                        // Sabak: Lines display (covered / total)
                        $sabakLines = '—';
                        $sabakProgress = 0;
                        $sabakCovered = 0;
                        $sabakTotal = 0;
                        if ($sabak && isset($sabak['lines_memorized']) && $sabak['lines_memorized'] !== null) {
                            $covered = (int)$sabak['lines_memorized'];
                            $total = '—';
                            if ($sabak['juz_number'] && isset($juz_map[$sabak['juz_number']]) && $juz_map[$sabak['juz_number']]['number_of_lines']) {
                                $total = (int)$juz_map[$sabak['juz_number']]['number_of_lines'];
                            }
                            $sabakLines = $covered . ' / ' . $total;
                            $sabakCovered = $covered;
                            $sabakTotal = ($total !== '—' && $total > 0) ? $total : 0;
                            if ($sabakTotal > 0) {
                                $sabakProgress = min(100, round(($sabakCovered / $sabakTotal) * 100));
                            }
                        }

                        // Sabqi: Only Para (no amount)
                        $sabqiPara = $sabqi && $sabqi['juz_number'] ? htmlspecialchars($sabqi['juz_number']) : '—';

                        // Manzil: Quantity (try 'amount' field first, then parse from remarks for backward compatibility)
                        $manzilQty = '—';
                        if ($manzil) {
                            if (isset($manzil['amount']) && $manzil['amount']) {
                                $manzilQty = manzilQuantityLabel($manzil['amount']);
                            } else {
                                $qtyFromRemarks = parseRemarkPart($manzil['remarks'], 'Quantity');
                                if (!$qtyFromRemarks) {
                                    $qtyFromRemarks = parseRemarkPart($manzil['remarks'], 'Amount'); // backward compat
                                }
                                if ($qtyFromRemarks) {
                                    $manzilQty = manzilQuantityLabel($qtyFromRemarks);
                                }
                            }
                        }

                        // Manzil: Para (juz_start field or juz_from for backward compat)
                        $manzilPara = '—';
                        if ($manzil) {
                            if (isset($manzil['juz_start']) && $manzil['juz_start']) {
                                $manzilPara = htmlspecialchars($manzil['juz_start']);
                            } elseif (isset($manzil['juz_from']) && $manzil['juz_from']) {
                                $manzilPara = htmlspecialchars($manzil['juz_from']);
                            }
                        }

                        // Manzil: Heard By
                        $heardBy = '—';
                        if ($manzil) {
                            if (isset($manzil['heard_by']) && $manzil['heard_by']) {
                                $heardBy = htmlspecialchars($manzil['heard_by']);
                            } else {
                                $hbFromRemarks = parseRemarkPart($manzil['remarks'], 'Heard by');
                                if ($hbFromRemarks) {
                                    $heardBy = htmlspecialchars($hbFromRemarks);
                                }
                            }
                        }
                    ?>
                    <tr>
                        <td><?php echo $serial++; ?></td>
                        <td style="text-align:left;"><strong><?php echo $s['first_name'] . ' ' . $s['last_name']; ?></strong><br><small><?php echo $s['student_id']; ?></small></td>
                        <td style="text-align:left;"><?php echo $s['father_name']; ?></td>
                        <td><?php echo date('d/m/Y', strtotime($s['admission_date'])); ?></td>
                        <!-- Sabak -->
                        <td><?php echo $sabakJuz; ?></td>
                        <td style="text-align:left;">&nbsp;<?php echo $sabak ? htmlspecialchars($sabak['surah_name']) : '—'; ?></td>
                        <td><?php echo $sabak ? htmlspecialchars($sabakRange) : '—'; ?></td>
                        <td>
                            <?php echo $sabakLines; ?>
                            <?php if ($sabakTotal > 0): ?>
                            <div class="progress" style="height: 8px; margin-top: 4px; background-color: #e0e0e0; border-radius: 4px; overflow: hidden;">
                                <div style="height: 100%; width: <?php echo $sabakProgress; ?>%; background-color: <?php echo $sabakProgress >= 80 ? '#28a745' : ($sabakProgress >= 50 ? '#ffc107' : ($sabakProgress > 0 ? '#dc3545' : '#e0e0e0')); ?>; transition: width 0.3s ease;">
                                </div>
                            </div>
                            <small style="display: block; text-align: center; margin-top: 2px; font-size: 10px; color: #666;"><?php echo $sabakProgress; ?>%</small>
                            <?php endif; ?>
                        </td>
                        <!-- Sabqi -->
                        <td><?php echo $sabqiPara; ?></td>
                        <!-- Manzil -->
                        <td><?php echo $manzilQty; ?></td>
                        <td><?php echo $manzilPara; ?></td>
                        <td style="text-align:left;">&nbsp;<?php echo $heardBy; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php elseif ($class_id > 0): ?>
        <p class="text-center text-muted">No students found in the selected class.</p>
        <?php else: ?>
        <p class="text-center text-muted">Please select a date and class to view records.</p>
        <?php endif; ?>
    </div>
    <div class="card-footer no-print" style="padding: 10px 15px;">
        <?php if ($class_id > 0): ?>
        <a class="btn btn-info" href="#" onclick="window.print()"><i class="fas fa-print"></i> Print</a>
        <?php endif; ?>
        <a class="btn btn-secondary" href="<?php echo SITE_URL; ?>/modules/progress/progress_list.php"><i class="fas fa-list"></i> All Records</a>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>
