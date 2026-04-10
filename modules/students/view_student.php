<?php
/**
 * View Student Profile
 * MIQT System
 */

require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../includes/functions.php';

$pageTitle = 'Student Profile';

$student_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get student details with user info
$sql = "SELECT s.*, c.class_name, csc.class_name as current_school_class_name, u.username, u.email as user_email, u.status as user_status 
        FROM students s
        LEFT JOIN classes c ON s.class_id = c.id
        LEFT JOIN classes csc ON s.current_school_class = csc.id
        LEFT JOIN users u ON s.user_id = u.id
        WHERE s.id = ?";
$stmt = $db->prepare($sql);
$stmt->execute([$student_id]);
$student = $stmt->fetch();

if (!$student) {
    setFlash('danger', 'Student not found');
    redirect(SITE_URL . '/modules/students/students.php');
}

// Get attendance summary
$sql = "SELECT
        COUNT(*) as total_days,
        SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present_days,
        SUM(CASE WHEN status = 'absent' THEN 1 ELSE 0 END) as absent_days
        FROM student_attendance WHERE student_id = ?";
$stmt = $db->prepare($sql);
$stmt->execute([$student_id]);
$attendance = $stmt->fetch();

// Get progress summary
$sql = "SELECT
        COUNT(*) as total_records,
        SUM(CASE WHEN sabak_type = 'new_lesson' THEN 1 ELSE 0 END) as sabak_count,
        SUM(CASE WHEN sabak_type = 'revision' THEN 1 ELSE 0 END) as sabqi_count
        FROM quran_progress WHERE student_id = ?";
$stmt = $db->prepare($sql);
$stmt->execute([$student_id]);
$progress = $stmt->fetch();

// Get recent progress
$sql = "SELECT * FROM quran_progress
        WHERE student_id = ?
        ORDER BY progress_date DESC LIMIT 5";
$stmt = $db->prepare($sql);
$stmt->execute([$student_id]);
$recent_progress = $stmt->fetchAll();

// Get last 10 attendance records
$sql = "SELECT sa.*, c.class_name, u.full_name as marked_by_name
        FROM student_attendance sa
        LEFT JOIN classes c ON sa.class_id = c.id
        LEFT JOIN users u ON sa.marked_by = u.id
        WHERE sa.student_id = ?
        ORDER BY sa.attendance_date DESC, sa.created_at DESC
        LIMIT 10";
$stmt = $db->prepare($sql);
$stmt->execute([$student_id]);
$attendance_records = $stmt->fetchAll();

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

// Get progress records grouped by date (Sabak, Sabqi, Manzil)
// Get all unique dates with progress records
$sql = "SELECT DISTINCT record_date as progress_date FROM sabak_records WHERE student_id = ?
        UNION
        SELECT DISTINCT record_date as progress_date FROM sabqi_records WHERE student_id = ?
        UNION
        SELECT DISTINCT record_date as progress_date FROM manzil_records WHERE student_id = ?
        ORDER BY progress_date DESC LIMIT 30";
$stmt = $db->prepare($sql);
$stmt->execute([$student_id, $student_id, $student_id]);
$progress_dates = $stmt->fetchAll();

// Helper function to parse remark parts
function parseRemarkPart($remark, $key) {
    if (!$remark) return '';
    $parts = explode('|', $remark);
    foreach ($parts as $p) {
        $p = trim($p);
        if (stripos($p, $key . ':') === 0) {
            return trim(substr($p, strlen($key . ':')));
        }
    }
    return '';
}

// Helper function for manzil quantity label
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

// Build progress data by date
$progress_by_date = [];
foreach ($progress_dates as $date_row) {
    $date = $date_row['progress_date'];
    $progress_by_date[$date] = [
        'sabak' => null,
        'sabqi' => null,
        'manzil' => null
    ];
    
    // Get Sabak for this date
    $sql = "SELECT sr.*, t.first_name as teacher_first, t.last_name as teacher_last
            FROM sabak_records sr
            LEFT JOIN teachers t ON sr.teacher_id = t.id
            WHERE sr.student_id = ? AND sr.record_date = ?
            ORDER BY sr.created_at DESC LIMIT 1";
    $stmt = $db->prepare($sql);
    $stmt->execute([$student_id, $date]);
    $sabak = $stmt->fetch();
    if ($sabak) {
        $progress_by_date[$date]['sabak'] = $sabak;
    }
    
    // Get Sabqi for this date
    $sql = "SELECT sr.*, t.first_name as teacher_first, t.last_name as teacher_last
            FROM sabqi_records sr
            LEFT JOIN teachers t ON sr.teacher_id = t.id
            WHERE sr.student_id = ? AND sr.record_date = ?
            ORDER BY sr.created_at DESC LIMIT 1";
    $stmt = $db->prepare($sql);
    $stmt->execute([$student_id, $date]);
    $sabqi = $stmt->fetch();
    if ($sabqi) {
        $progress_by_date[$date]['sabqi'] = $sabqi;
    }
    
    // Get Manzil for this date
    $sql = "SELECT mr.*, t.first_name as teacher_first, t.last_name as teacher_last
            FROM manzil_records mr
            LEFT JOIN teachers t ON mr.teacher_id = t.id
            WHERE mr.student_id = ? AND mr.record_date = ?
            ORDER BY mr.created_at DESC LIMIT 1";
    $stmt = $db->prepare($sql);
    $stmt->execute([$student_id, $date]);
    $manzil = $stmt->fetch();
    if ($manzil) {
        $progress_by_date[$date]['manzil'] = $manzil;
    }
}

// Get exam results
$sql = "SELECT er.*, e.exam_title, e.exam_date, e.total_marks, e.passing_marks,
               et.exam_name as exam_type_name, c.class_name,
               CASE WHEN e.total_marks IS NOT NULL AND e.total_marks > 0 AND er.obtained_marks IS NOT NULL
                    THEN (er.obtained_marks / e.total_marks) * 100 ELSE NULL END AS percentage
        FROM exam_results er
        JOIN exams e ON er.exam_id = e.id
        LEFT JOIN exam_types et ON e.exam_type_id = et.id
        LEFT JOIN classes c ON e.class_id = c.id
        WHERE er.student_id = ?
        ORDER BY e.exam_date DESC, er.created_at DESC
        LIMIT 20";
$stmt = $db->prepare($sql);
$stmt->execute([$student_id]);
$exam_results = $stmt->fetchAll();

// Calculate grades for exam results
foreach ($exam_results as &$result) {
    if (isset($result['percentage']) && $result['percentage'] !== null) {
        $result['grade'] = $result['grade'] ?? calculateGrade((float)$result['percentage']);
    }
}
unset($result);

// Get behavior reports
$sql = "SELECT br.*, u.full_name as created_by_name, c.class_name
        FROM student_behavior_reports br
        LEFT JOIN users u ON br.created_by = u.id
        LEFT JOIN classes c ON br.class_id = c.id
        WHERE br.student_id = ?
        ORDER BY br.report_date DESC, br.created_at DESC
        LIMIT 20";
$stmt = $db->prepare($sql);
$stmt->execute([$student_id]);
$behavior_reports = $stmt->fetchAll();

require_once '../../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title"><i class="fas fa-user"></i> Student Profile</h1>
    <p class="subtitle"><?php echo $student['student_id']; ?></p>
</div>

<div class="dashboard-grid mb-20">
    <div class="dashboard-card">
        <div class="card-body" style="text-align: center;">
            <?php if ($student['photo']): ?>
            <img src="<?php echo SITE_URL . '/uploads/photos/' . $student['photo']; ?>"
                 alt="Photo" style="width: 150px; height: 150px; border-radius: 50%; object-fit: cover; margin-bottom: 20px;">
            <?php else: ?>
            <i class="fas fa-user-circle" style="font-size: 150px; color: #ccc; margin-bottom: 20px;"></i>
            <?php endif; ?>

            <h2><?php echo $student['first_name'] . ' ' . $student['last_name']; ?></h2>
            <p class="text-muted"><?php echo $student['student_id']; ?></p>

            <div style="margin-top: 20px;">
                <?php if (hasPermission(['principal', 'vice_principal', 'coordinator'])): ?>
                <a href="edit_student.php?id=<?php echo $student['id']; ?>" class="btn btn-primary">
                    <i class="fas fa-edit"></i> Edit Profile
                </a>
                <a href="delete_student.php?id=<?php echo $student['id']; ?>" class="btn btn-danger"
                   onclick="return confirm('Are you sure you want to delete this student?')">
                    <i class="fas fa-trash"></i> Delete
                </a>
                <?php endif; ?>
                <a href="students.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
        </div>
    </div>

    <div class="dashboard-card">
        <div class="card-header">
            <h3><i class="fas fa-info-circle"></i> Basic Information</h3>
        </div>
        <div class="card-body">
            <table class="table">
                <tr>
                    <th>Admission No:</th>
                    <td><?php echo htmlspecialchars($student['admission_no']); ?></td>
                </tr>
                <tr>
                    <th>Admission Date:</th>
                    <td><?php echo formatDate($student['admission_date']); ?></td>
                </tr>
                <tr>
                    <th>Student Type:</th>
                    <td><?php echo htmlspecialchars(str_replace('_', ' ', (string)($student['student_type'] ?? ''))); ?></td>
                </tr>
                <tr>
                    <th>Father Name:</th>
                    <td><?php echo htmlspecialchars($student['father_name']); ?></td>
                </tr>
                <?php if (!empty($student['mother_name'])): ?>
                <tr>
                    <th>Mother Name:</th>
                    <td><?php echo htmlspecialchars($student['mother_name']); ?></td>
                </tr>
                <?php endif; ?>
                <tr>
                    <th>CNIC / B-Form (student):</th>
                    <td><?php echo $student['cnic_bform'] ? htmlspecialchars($student['cnic_bform']) : 'N/A'; ?></td>
                </tr>
                <?php if (!empty($student['father_cnic'])): ?>
                <tr>
                    <th>Father / Guardian CNIC:</th>
                    <td><?php echo htmlspecialchars($student['father_cnic']); ?></td>
                </tr>
                <?php endif; ?>
                <?php if (!empty($student['father_profession'])): ?>
                <tr>
                    <th>Father Profession:</th>
                    <td><?php echo htmlspecialchars($student['father_profession']); ?></td>
                </tr>
                <?php endif; ?>
                <?php if (!empty($student['admission_challan_no'])): ?>
                <tr>
                    <th>Admission Challan No:</th>
                    <td><?php echo htmlspecialchars($student['admission_challan_no']); ?></td>
                </tr>
                <?php endif; ?>
                <tr>
                    <th>Date of Birth:</th>
                    <td><?php echo formatDate($student['date_of_birth']); ?></td>
                </tr>
                <tr>
                    <th>Gender:</th>
                    <td><?php echo ucfirst($student['gender']); ?></td>
                </tr>
                <tr>
                    <th>Class:</th>
                    <td><?php echo $student['class_name'] ?? 'N/A'; ?></td>
                </tr>
                <?php if (!empty($student['previous_school_class'])): ?>
                <tr>
                    <th>Previous school class:</th>
                    <td><?php echo htmlspecialchars($student['previous_school_class']); ?></td>
                </tr>
                <?php endif; ?>
                <?php if (!empty($student['current_school_class'])): ?>
                <tr>
                    <th>Current school class:</th>
                    <td><?php echo htmlspecialchars($student['current_school_class_name'] ?? $student['current_school_class']); ?></td>
                </tr>
                <?php endif; ?>
                <?php if (($student['total_marks'] ?? null) !== null || ($student['obtained_marks'] ?? null) !== null): ?>
                <tr>
                    <th>Last result (marks):</th>
                    <td><?php echo (int)($student['obtained_marks'] ?? 0); ?> / <?php echo (int)($student['total_marks'] ?? 0); ?>
                        <?php if (!empty($student['class_status'])): ?>(<?php echo htmlspecialchars($student['class_status']); ?>)<?php endif; ?></td>
                </tr>
                <?php endif; ?>
                <?php if (!empty($student['previous_result_card'])): ?>
                <tr>
                    <th>Previous result card:</th>
                    <td><a href="<?php echo SITE_URL . '/uploads/photos/' . htmlspecialchars($student['previous_result_card']); ?>" target="_blank">View file</a></td>
                </tr>
                <?php endif; ?>
                <tr>
                    <th>Status:</th>
                    <td><span class="badge badge-success"><?php echo ucfirst($student['status']); ?></span></td>
                </tr>
                <?php if (!empty($student['date_of_leaving']) || !empty($student['reason_of_leaving'])): ?>
                <tr>
                    <th>Date of leaving:</th>
                    <td><?php echo !empty($student['date_of_leaving']) ? formatDate($student['date_of_leaving']) : 'N/A'; ?></td>
                </tr>
                <tr>
                    <th>Reason of leaving:</th>
                    <td><?php echo !empty($student['reason_of_leaving']) ? htmlspecialchars($student['reason_of_leaving']) : 'N/A'; ?></td>
                </tr>
                <?php endif; ?>
                <?php if ($student['user_email']): ?>
                <tr>
                    <th>Email:</th>
                    <td><?php echo htmlspecialchars($student['user_email']); ?></td>
                </tr>
                <?php endif; ?>
                <?php if ($student['username']): ?>
                <tr>
                    <th>Login Username:</th>
                    <td>
                        <span class="badge badge-success"><?php echo htmlspecialchars($student['username']); ?></span>
                        <?php if (hasPermission(['principal', 'vice_principal'])): ?>
                        <a href="<?php echo SITE_URL; ?>/modules/users/edit_user.php?id=<?php echo $student['user_id']; ?>" class="btn btn-sm btn-primary" style="margin-left: 10px;">
                            <i class="fas fa-edit"></i> Edit Account
                        </a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endif; ?>
            </table>
        </div>
    </div>
</div>

<div class="stats-grid mb-20">
    <div class="stat-card stat-success">
        <div class="stat-icon"><i class="fas fa-calendar-check"></i></div>
        <div class="stat-info">
            <h3><?php echo $attendance['present_days'] ?? 0; ?> / <?php echo $attendance['total_days'] ?? 0; ?></h3>
            <p>Attendance Record</p>
        </div>
    </div>

    <div class="stat-card stat-primary">
        <div class="stat-icon"><i class="fas fa-book-open"></i></div>
        <div class="stat-info">
            <h3><?php echo $progress['sabak_count'] ?? 0; ?></h3>
            <p>Total Sabak (New Lessons)</p>
        </div>
    </div>

    <div class="stat-card stat-info">
        <div class="stat-icon"><i class="fas fa-redo"></i></div>
        <div class="stat-info">
            <h3><?php echo $progress['sabqi_count'] ?? 0; ?></h3>
            <p>Total Sabqi (Revisions)</p>
        </div>
    </div>

    <div class="stat-card stat-warning">
        <div class="stat-icon"><i class="fas fa-chart-line"></i></div>
        <div class="stat-info">
            <h3><?php echo $progress['total_records'] ?? 0; ?></h3>
            <p>Total Progress Records</p>
        </div>
    </div>
</div>

<div class="dashboard-grid">
    <div class="dashboard-card">
        <div class="card-header">
            <h3><i class="fas fa-phone"></i> Contact Information</h3>
        </div>
        <div class="card-body">
            <table class="table">
                <tr>
                    <th>Guardian Name:</th>
                    <td><?php echo $student['guardian_name']; ?></td>
                </tr>
                <tr>
                    <th>Guardian Phone:</th>
                    <td><?php echo htmlspecialchars($student['guardian_phone']); ?></td>
                </tr>
                <?php if (!empty($student['guardian_phone_2'])): ?>
                <tr>
                    <th>Guardian Phone 2:</th>
                    <td><?php echo htmlspecialchars($student['guardian_phone_2']); ?></td>
                </tr>
                <?php endif; ?>
                <?php if (!empty($student['whatsapp_no'])): ?>
                <tr>
                    <th>WhatsApp:</th>
                    <td><?php echo htmlspecialchars($student['whatsapp_no']); ?></td>
                </tr>
                <?php endif; ?>
                <tr>
                    <th>Student Phone:</th>
                    <td><?php echo $student['phone'] ? htmlspecialchars($student['phone']) : 'N/A'; ?></td>
                </tr>
                <tr>
                    <th>Address:</th>
                    <td><?php echo $student['address'] ?: 'N/A'; ?></td>
                </tr>
                <tr>
                    <th>City:</th>
                    <td><?php echo $student['city'] ?: 'N/A'; ?></td>
                </tr>
            </table>
        </div>
    </div>

    <div class="dashboard-card">
        <div class="card-header">
            <h3><i class="fas fa-book-quran"></i> Recent Progress</h3>
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
                        <td><?php echo $record['surah_name']; ?></td>
                        <td><?php echo $record['page_from'] . '-' . $record['page_to']; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <p class="text-center text-muted">No progress records yet</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if ($student['previous_education'] || $student['medical_info']): ?>
<div class="dashboard-card mt-20">
    <div class="card-header">
        <h3><i class="fas fa-file-alt"></i> Additional Information</h3>
    </div>
    <div class="card-body">
        <?php if ($student['previous_education']): ?>
        <h4>Previous Education</h4>
        <p><?php echo ($student['previous_education']); ?></p>
        <?php endif; ?>

        <?php if ($student['medical_info']): ?>
        <h4 class="mt-20">Medical Information</h4>
        <p><?php echo ($student['medical_info']); ?></p>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

<!-- Last 10 Attendance Records -->
<div class="dashboard-card mt-20" id="attendance">
    <div class="card-header">
        <h3><i class="fas fa-calendar-check"></i> Last 10 Attendance Records</h3>
    </div>
    <div class="card-body">
        <?php if (count($attendance_records) > 0): ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Class</th>
                        <th>Remarks</th>
                        <th>Marked By</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($attendance_records as $att): ?>
                    <tr>
                        <td><?php echo formatDate($att['attendance_date']); ?></td>
                        <td>
                            <?php
                            $statusClass = [
                                'present' => 'badge-success',
                                'absent' => 'badge-danger',
                                'leave' => 'badge-warning',
                                'late' => 'badge-info'
                            ];
                            $class = $statusClass[$att['status']] ?? 'badge-secondary';
                            ?>
                            <span class="badge <?php echo $class; ?>">
                                <?php echo ucfirst($att['status']); ?>
                            </span>
                        </td>
                        <td><?php echo $att['class_name'] ?? 'N/A'; ?></td>
                        <td><?php echo $att['remarks'] ?: '-'; ?></td>
                        <td><?php echo $att['marked_by_name'] ?? 'System'; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <p class="text-center text-muted">No attendance records found</p>
        <?php endif; ?>
    </div>
</div>

<!-- Progress Report -->
<div class="dashboard-card mt-20" id="progress">
    <div class="card-header">
        <h3><i class="fas fa-book-quran"></i> Progress Report</h3>
    </div>
    <div class="card-body">
        <style>
        .progress-table { width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 12px; }
        .progress-table th, .progress-table td { border: 1px solid #333; padding: 8px 5px; text-align: center; vertical-align: middle; }
        .progress-table th { background-color: var(--primary-color); color: white; font-weight: 600; }
        </style>
        
        <?php if (count($progress_by_date) > 0): ?>
        <div style="overflow-x:auto;">
            <table class="progress-table">
                <thead>
                    <tr>
                        <th rowspan="2" style="width:30px;">نمبر شمار<br>S.No</th>
                        <th rowspan="2" style="width:100px;">تاریخ<br>Date</th>
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
                    <?php $serial = 1; foreach ($progress_by_date as $date => $records): ?>
                    <?php
                        $sabak = $records['sabak'];
                        $sabqi = $records['sabqi'];
                        $manzil = $records['manzil'];

                        // Sabak: Display Juz with Arabic name
                        $sabakJuz = '—';
                        if ($sabak && $sabak['juz_number']) {
                            $juzNum = $sabak['juz_number'];
                            $sabakJuz = $juzNum;
                            if (isset($juz_map[$juzNum]) && $juz_map[$juzNum]['juz_name_arabic']) {
                                $sabakJuz = $juz_map[$juzNum]['juz_name_arabic'] . ' - ' . $juzNum;
                            }
                        }

                        // Sabak: Page/Ayat range
                        $sabakRange = '—';
                        if ($sabak) {
                            $from = $sabak['page_from'] ?? '';
                            $to = $sabak['page_to'] ?? '';
                            if ($from !== '' && $to !== '' && $to != $from) {
                                $sabakRange = $from . '-' . $to;
                            } elseif ($from !== '') {
                                $sabakRange = $from;
                            }
                        }

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

                        // Sabqi: Only Para
                        $sabqiPara = '—';
                        if ($sabqi && $sabqi['juz_number']) {
                            $sabqiPara = htmlspecialchars($sabqi['juz_number']);
                        }

                        // Manzil: Quantity
                        $manzilQty = '—';
                        if ($manzil) {
                            $qtyFromRemarks = parseRemarkPart($manzil['remarks'], 'Quantity');
                            if (!$qtyFromRemarks) {
                                $qtyFromRemarks = parseRemarkPart($manzil['remarks'], 'Amount');
                            }
                            if ($qtyFromRemarks) {
                                $manzilQty = manzilQuantityLabel($qtyFromRemarks);
                            }
                        }

                        // Manzil: Para
                        $manzilPara = '—';
                        if ($manzil) {
                            if (isset($manzil['juz_from']) && $manzil['juz_from']) {
                                $manzilPara = htmlspecialchars($manzil['juz_from']);
                                if (isset($manzil['juz_to']) && $manzil['juz_to'] && $manzil['juz_to'] != $manzil['juz_from']) {
                                    $manzilPara .= '-' . htmlspecialchars($manzil['juz_to']);
                                }
                            }
                        }

                        // Manzil: Heard By
                        $heardBy = '—';
                        if ($manzil) {
                            $hbFromRemarks = parseRemarkPart($manzil['remarks'], 'Heard by');
                            if ($hbFromRemarks) {
                                $heardBy = htmlspecialchars($hbFromRemarks);
                            } elseif ($manzil['teacher_first']) {
                                $heardBy = htmlspecialchars($manzil['teacher_first'] . ' ' . $manzil['teacher_last'] . ' (Teacher)');
                            }
                        }
                    ?>
                    <tr>
                        <td><?php echo $serial++; ?></td>
                        <td><?php echo formatDate($date); ?></td>
                        <!-- Sabak -->
                        <td><?php echo $sabakJuz; ?></td>
                        <td style="text-align:left;">&nbsp;<?php echo $sabak ? htmlspecialchars($sabak['surah_name']) : '—'; ?></td>
                        <td><?php echo $sabakRange; ?></td>
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
        <?php else: ?>
        <p class="text-center text-muted">No progress records found</p>
        <?php endif; ?>
    </div>
</div>

<!-- Exam Reports -->
<div class="dashboard-card mt-20" id="exams">
    <div class="card-header">
        <h3><i class="fas fa-file-alt"></i> Exam Reports</h3>
    </div>
    <div class="card-body">
        <?php if (count($exam_results) > 0): ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Exam Title</th>
                        <th>Type</th>
                        <th>Date</th>
                        <th>Class</th>
                        <th>Obtained Marks</th>
                        <th>Total Marks</th>
                        <th>Percentage</th>
                        <th>Grade</th>
                        <th>Status</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($exam_results as $exam): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($exam['exam_title']); ?></td>
                        <td><?php echo $exam['exam_type_name'] ?? 'N/A'; ?></td>
                        <td><?php echo formatDate($exam['exam_date']); ?></td>
                        <td><?php echo $exam['class_name'] ?? 'N/A'; ?></td>
                        <td><?php echo ($exam['obtained_marks'] !== null) ? $exam['obtained_marks'] : '-'; ?></td>
                        <td><?php echo ($exam['total_marks'] !== null) ? $exam['total_marks'] : 'N/A'; ?></td>
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
                        <td>
                            <?php if (isset($exam['status']) && $exam['status']): ?>
                                <span class="badge <?php echo ($exam['status'] == 'pass') ? 'badge-success' : 'badge-danger'; ?>">
                                    <?php echo ucfirst($exam['status']); ?>
                                </span>
                            <?php else: ?>
                                <span class="badge badge-secondary">N/A</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo $exam['remarks'] ?: '-'; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <p class="text-center text-muted">No exam results found</p>
        <?php endif; ?>
    </div>
</div>

<!-- Behavior Reports -->
<div class="dashboard-card mt-20" id="behavior">
    <div class="card-header">
        <h3><i class="fas fa-user-check"></i> Behavior Reports</h3>
    </div>
    <div class="card-body">
        <?php if (count($behavior_reports) > 0): ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Class</th>
                        <th>Summary</th>
                        <th>Details</th>
                        <th>Created By</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($behavior_reports as $report): ?>
                    <tr>
                        <td><?php echo formatDate($report['report_date']); ?></td>
                        <td>
                            <span class="badge badge-info"><?php echo ucfirst($report['report_type']); ?></span>
                        </td>
                        <td><?php echo $report['class_name'] ?? 'N/A'; ?></td>
                        <td><?php echo htmlspecialchars($report['summary']); ?></td>
                        <td>
                            <?php if ($report['details']): ?>
                                <button type="button" class="btn btn-sm btn-link" data-bs-toggle="modal" data-bs-target="#detailsModal<?php echo $report['id']; ?>">
                                    View Details
                                </button>
                                <!-- Modal for details -->
                                <div class="modal fade" id="detailsModal<?php echo $report['id']; ?>" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Report Details</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p><strong>Summary:</strong> <?php echo htmlspecialchars($report['summary']); ?></p>
                                                <p><strong>Details:</strong></p>
                                                <p><?php echo nl2br(htmlspecialchars($report['details'])); ?></p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td><?php echo $report['created_by_name'] ?? 'System'; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <p class="text-center text-muted">No behavior reports found</p>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>
