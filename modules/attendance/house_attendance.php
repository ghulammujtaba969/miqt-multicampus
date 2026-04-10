<?php
/**
 * House-Based Attendance (Abubakar House Format)
 * MIQT System
 */

require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../includes/functions.php';

$pageTitle = 'House Attendance Register';

// Get all classes/houses
$sql = "SELECT * FROM classes WHERE status = 'active' ORDER BY class_name";
$stmt = $db->prepare($sql);
$stmt->execute();
$classes = $stmt->fetchAll();

// Get selected class
$class_id = isset($_GET['class_id']) ? (int)$_GET['class_id'] : 0;
$selected_date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

$students = [];
$class_info = null;

if ($class_id > 0) {
    // Get class info
    $sql = "SELECT * FROM classes WHERE id = ?";
    $stmt = $db->prepare($sql);
    $stmt->execute([$class_id]);
    $class_info = $stmt->fetch();

    // Get students in this class with their progress
    $sql = "SELECT s.*,
            (SELECT COUNT(*) FROM quran_progress WHERE student_id = s.id AND sabak_type = 'new_lesson') as sabak_count,
            (SELECT COUNT(*) FROM quran_progress WHERE student_id = s.id AND sabak_type = 'revision') as sabqi_count,
            (SELECT COUNT(*) FROM quran_progress WHERE student_id = s.id AND sabak_type = 'manzil') as manzil_count,
            (SELECT juz_number FROM quran_progress WHERE student_id = s.id ORDER BY progress_date DESC LIMIT 1) as current_juz,
            (SELECT surah_name FROM quran_progress WHERE student_id = s.id ORDER BY progress_date DESC LIMIT 1) as current_surah
            FROM students s
            WHERE s.class_id = ? AND s.status = 'active'
            ORDER BY s.first_name, s.last_name";
    $stmt = $db->prepare($sql);
    $stmt->execute([$class_id]);
    $students = $stmt->fetchAll();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_attendance'])) {
    $attendance_date = sanitize($_POST['attendance_date']);
    $class_id = (int)$_POST['class_id'];

    try {
        $db->beginTransaction();

        foreach ($_POST['attendance'] as $student_id => $status) {
            // Check if attendance exists
            $sql = "SELECT id FROM student_attendance WHERE student_id = ? AND attendance_date = ?";
            $stmt = $db->prepare($sql);
            $stmt->execute([$student_id, $attendance_date]);

            if ($stmt->fetch()) {
                // Update
                $sql = "UPDATE student_attendance SET status = ? WHERE student_id = ? AND attendance_date = ?";
                $stmt = $db->prepare($sql);
                $stmt->execute([$status, $student_id, $attendance_date]);
            } else {
                // Insert
                $sql = "INSERT INTO student_attendance (student_id, class_id, attendance_date, status) VALUES (?, ?, ?, ?)";
                $stmt = $db->prepare($sql);
                $stmt->execute([$student_id, $class_id, $attendance_date, $status]);
            }
        }

        $db->commit();
        logActivity($db, 'House Attendance', 'Attendance', "Marked attendance for class: {$class_info['class_name']}");
        setFlash('success', 'حاضری کامیابی سے محفوظ ہو گئی / Attendance saved successfully!');
        redirect(SITE_URL . '/modules/attendance/house_attendance.php?class_id=' . $class_id . '&date=' . $attendance_date);
    } catch(PDOException $e) {
        $db->rollBack();
        $error = 'Error saving attendance: ' . $e->getMessage();
    }
}

require_once '../../includes/header.php';
?>

<style>
.urdu-text {
    font-family: 'Jameel Noori Nastaleeq', 'Noto Nastalikh Urdu', 'Al Qalam Quran', Arial;
    font-size: 16px;
    direction: rtl;
    text-align: right;
}

.attendance-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

.attendance-table th,
.attendance-table td {
    border: 1px solid #2e7d32;
    padding: 8px;
    text-align: center;
}

.attendance-table th {
    background: #2e7d32;
    color: white;
    font-weight: bold;
}

.attendance-table tr:nth-child(even) {
    background: #f5f5f5;
}

.house-header {
    text-align: center;
    padding: 20px;
    background: #2e7d32;
    color: white;
    border-radius: 5px;
    margin-bottom: 20px;
}

.print-btn {
    float: right;
    margin-bottom: 10px;
}

@media print {
    .no-print {
        display: none !important;
    }

    .attendance-table {
        page-break-inside: avoid;
    }
}
</style>

<div class="page-header no-print">
    <h1 class="page-title"><i class="fas fa-home"></i> حاضری رجسٹر / House Attendance Register</h1>
    <p class="subtitle">Select house/class to mark attendance</p>
</div>

<!-- Class Selection -->
<div class="dashboard-card mb-20 no-print">
    <div class="card-body">
        <form method="GET" action="" class="form-row">
            <div class="form-group">
                <label for="class_id">ہاوس / کلاس منتخب کریں / Select House/Class *</label>
                <select name="class_id" id="class_id" class="form-control" required onchange="this.form.submit()">
                    <option value="">-- Select House/Class --</option>
                    <?php foreach ($classes as $class): ?>
                    <option value="<?php echo $class['id']; ?>" <?php echo ($class_id == $class['id']) ? 'selected' : ''; ?>>
                        <?php echo $class['class_name']; ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="date">تاریخ / Date *</label>
                <input type="date" name="date" id="date" class="form-control"
                       value="<?php echo $selected_date; ?>" required onchange="this.form.submit()">
            </div>
        </form>
    </div>
</div>

<?php if ($class_info && count($students) > 0): ?>

<!-- House Header -->
<div class="house-header">
    <h2><?php echo $class_info['class_name']; ?></h2>
    <h3 class="urdu-text">حاضری رجسٹر</h3>
    <p>Date / تاریخ: <?php echo date('d/m/Y', strtotime($selected_date)); ?></p>
</div>

<button onclick="window.print()" class="btn btn-primary print-btn no-print">
    <i class="fas fa-print"></i> Print / پرنٹ
</button>

<form method="POST" action="">
    <input type="hidden" name="class_id" value="<?php echo $class_id; ?>">
    <input type="hidden" name="attendance_date" value="<?php echo $selected_date; ?>">

    <table class="attendance-table">
        <thead>
            <tr>
                <th style="width: 50px;">نمبر شمار<br>Sr#</th>
                <th>نام طالب علم<br>Student Name</th>
                <th>ولدیت<br>Father Name</th>
                <th>تاریخ داخلہ<br>Admission Date</th>
                <th>پراگرس<br>Progress</th>
                <th class="urdu-text">سبق</th>
                <th class="urdu-text">سبقی</th>
                <th class="urdu-text">منزل</th>
                <th class="urdu-text">پارہ نمبر</th>
                <th class="urdu-text">نام سورۃ</th>
                <th class="no-print">حاضری<br>Attendance</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sr = 1;
            foreach ($students as $student):
                // Get today's attendance if exists
                $sql = "SELECT status FROM student_attendance WHERE student_id = ? AND attendance_date = ?";
                $stmt = $db->prepare($sql);
                $stmt->execute([$student['id'], $selected_date]);
                $current_attendance = $stmt->fetch();
                $attendance_status = $current_attendance ? $current_attendance['status'] : 'present';
            ?>
            <tr>
                <td><?php echo str_pad($sr++, 2, '0', STR_PAD_LEFT); ?></td>
                <td style="text-align: left;"><?php echo $student['first_name'] . ' ' . $student['last_name']; ?></td>
                <td style="text-align: left;"><?php echo $student['father_name']; ?></td>
                <td><?php echo formatDate($student['admission_date']); ?></td>
                <td>
                    <span class="badge badge-primary"><?php echo $student['sabak_count'] + $student['sabqi_count'] + $student['manzil_count']; ?></span>
                </td>
                <td><?php echo $student['sabak_count']; ?></td>
                <td><?php echo $student['sabqi_count']; ?></td>
                <td><?php echo $student['manzil_count']; ?></td>
                <td><?php echo $student['current_juz'] ?? '-'; ?></td>
                <td class="urdu-text" style="text-align: right;"><?php echo $student['current_surah'] ?? '-'; ?></td>
                <td class="no-print">
                    <select name="attendance[<?php echo $student['id']; ?>]" class="form-control" required>
                        <option value="present" <?php echo ($attendance_status == 'present') ? 'selected' : ''; ?>>حاضر / Present</option>
                        <option value="absent" <?php echo ($attendance_status == 'absent') ? 'selected' : ''; ?>>غائب / Absent</option>
                        <option value="leave" <?php echo ($attendance_status == 'leave') ? 'selected' : ''; ?>>رخصت / Leave</option>
                        <option value="late" <?php echo ($attendance_status == 'late') ? 'selected' : ''; ?>>Late</option>
                    </select>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="form-group mt-20 no-print">
        <button type="submit" name="save_attendance" class="btn btn-primary">
            <i class="fas fa-save"></i> محفوظ کریں / Save Attendance
        </button>
        <a href="view_attendance.php" class="btn btn-secondary">
            <i class="fas fa-eye"></i> View Reports
        </a>
    </div>
</form>

<?php elseif ($class_id > 0): ?>
<div class="alert alert-warning">
    No students found in this house/class. Please add students first.
</div>
<?php else: ?>
<div class="alert alert-info">
    Please select a house/class to view and mark attendance.
</div>
<?php endif; ?>

<?php require_once '../../includes/footer.php'; ?>
