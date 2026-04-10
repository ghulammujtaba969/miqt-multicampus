<?php
/**
 * Monthly Progress Report Card (Hifz Format)
 * MIQT System
 */

require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../includes/functions.php';

$pageTitle = 'Monthly Progress Report Card';

// Get all classes/houses
$sql = "SELECT * FROM classes WHERE status = 'active' ORDER BY class_name";
$stmt = $db->prepare($sql);
$stmt->execute();
$classes = $stmt->fetchAll();

// Get selected parameters
$class_id = isset($_GET['class_id']) ? (int)$_GET['class_id'] : 0;
$student_id = isset($_GET['student_id']) ? (int)$_GET['student_id'] : 0;
$month = isset($_GET['month']) ? $_GET['month'] : date('Y-m');

$students = [];
$class_info = null;
$student_data = null;
$monthly_data = [];

if ($class_id > 0) {
    // Get class info
    $sql = "SELECT * FROM classes WHERE id = ?";
    $stmt = $db->prepare($sql);
    $stmt->execute([$class_id]);
    $class_info = $stmt->fetch();

    // Get students in this class
    $sql = "SELECT * FROM students WHERE class_id = ? AND status = 'active' ORDER BY first_name, last_name";
    $stmt = $db->prepare($sql);
    $stmt->execute([$class_id]);
    $students = $stmt->fetchAll();
}

if ($student_id > 0 && $month) {
    // Get student details
    $sql = "SELECT s.*, c.class_name FROM students s
            LEFT JOIN classes c ON s.class_id = c.id
            WHERE s.id = ?";
    $stmt = $db->prepare($sql);
    $stmt->execute([$student_id]);
    $student_data = $stmt->fetch();

    if ($student_data) {
        $month_start = $month . '-01';
        $month_end = date('Y-m-t', strtotime($month_start));

        // Get attendance summary
        $sql = "SELECT
                COUNT(*) as total_days,
                SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present_days,
                SUM(CASE WHEN status = 'absent' THEN 1 ELSE 0 END) as absent_days,
                SUM(CASE WHEN status = 'leave' THEN 1 ELSE 0 END) as leave_days
                FROM student_attendance
                WHERE student_id = ? AND attendance_date BETWEEN ? AND ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$student_id, $month_start, $month_end]);
        $monthly_data['attendance'] = $stmt->fetch();

        // Get progress records
        $sql = "SELECT * FROM quran_progress
                WHERE student_id = ? AND progress_date BETWEEN ? AND ?
                ORDER BY progress_date DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute([$student_id, $month_start, $month_end]);
        $monthly_data['progress'] = $stmt->fetchAll();

        // Get progress summary
        $sql = "SELECT
                COUNT(*) as total_records,
                SUM(CASE WHEN sabak_type = 'new_lesson' THEN 1 ELSE 0 END) as sabak_count,
                SUM(CASE WHEN sabak_type = 'revision' THEN 1 ELSE 0 END) as sabqi_count,
                SUM(CASE WHEN sabak_type = 'manzil' THEN 1 ELSE 0 END) as manzil_count
                FROM quran_progress
                WHERE student_id = ? AND progress_date BETWEEN ? AND ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$student_id, $month_start, $month_end]);
        $monthly_data['summary'] = $stmt->fetch();
    }
}

require_once '../../includes/header.php';
?>

<style>
@import url('https://fonts.googleapis.com/css2?family=Noto+Nastaliq+Urdu:wght@400;700&display=swap');

.urdu-text {
    font-family: 'Jameel Noori Nastaleeq', 'Noto Nastaliq Urdu', 'Al Qalam Quran', Arial;
    font-size: 18px;
    direction: rtl;
    text-align: right;
    line-height: 2;
}

.report-card {
    max-width: 21cm;
    margin: 20px auto;
    background: white;
    padding: 30px;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
}

.report-header {
    text-align: center;
    border-bottom: 3px solid #2e7d32;
    padding-bottom: 15px;
    margin-bottom: 20px;
}

.report-header h1 {
    color: #2e7d32;
    margin: 0;
    font-size: 24px;
}

.report-header h2 {
    margin: 5px 0;
    font-size: 20px;
}

.report-header .urdu-text {
    font-size: 28px;
    font-weight: bold;
}

.student-info {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
    margin: 20px 0;
}

.info-row {
    display: flex;
    border-bottom: 1px solid #ddd;
    padding: 10px 0;
}

.info-label {
    font-weight: bold;
    min-width: 150px;
}

.progress-table {
    width: 100%;
    border-collapse: collapse;
    margin: 20px 0;
}

.progress-table th,
.progress-table td {
    border: 1px solid #2e7d32;
    padding: 10px;
    text-align: center;
}

.progress-table th {
    background: #2e7d32;
    color: white;
}

.signature-section {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
    margin-top: 50px;
    text-align: center;
}

.signature-box {
    border-top: 2px solid #333;
    padding-top: 10px;
}

@media print {
    .no-print {
        display: none !important;
    }

    .report-card {
        box-shadow: none;
        padding: 0;
    }

    @page {
        margin: 1cm;
    }
}
</style>

<div class="no-print">
    <div class="page-header">
        <h1 class="page-title"><i class="fas fa-file-alt"></i> ماہانہ پراگرس رپورٹ / Monthly Progress Report</h1>
        <p class="subtitle">Generate monthly progress report card</p>
    </div>

    <!-- Selection Form -->
    <div class="dashboard-card mb-20">
        <div class="card-body">
            <form method="GET" action="">
                <div class="form-row">
                    <div class="form-group">
                        <label for="class_id">ہاوس / کلاس / House/Class *</label>
                        <select name="class_id" id="class_id" class="form-control" required onchange="this.form.submit()">
                            <option value="">-- Select --</option>
                            <?php foreach ($classes as $class): ?>
                            <option value="<?php echo $class['id']; ?>" <?php echo ($class_id == $class['id']) ? 'selected' : ''; ?>>
                                <?php echo $class['class_name']; ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <?php if ($class_id > 0 && count($students) > 0): ?>
                    <div class="form-group">
                        <label for="student_id">طالب علم / Student *</label>
                        <select name="student_id" id="student_id" class="form-control" required>
                            <option value="">-- Select Student --</option>
                            <?php foreach ($students as $student): ?>
                            <option value="<?php echo $student['id']; ?>" <?php echo ($student_id == $student['id']) ? 'selected' : ''; ?>>
                                <?php echo $student['student_id'] . ' - ' . $student['first_name'] . ' ' . $student['last_name']; ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="month">مہینہ / Month *</label>
                        <input type="month" name="month" id="month" class="form-control"
                               value="<?php echo $month; ?>" required>
                    </div>

                    <div class="form-group">
                        <label>&nbsp;</label>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Generate Report
                        </button>
                    </div>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>
</div>

<?php if ($student_data && $monthly_data): ?>

<button onclick="window.print()" class="btn btn-primary no-print" style="margin-bottom: 20px;">
    <i class="fas fa-print"></i> Print Report / پرنٹ کریں
</button>

<div class="report-card">
    <!-- Header -->
    <div class="report-header">
        <h1>Minhaj Institute of Qira'at and Tahfeez-ul-Quran</h1>
        <h2 class="urdu-text">منہاج انسٹیٹیوٹ آف قراءت و تحفیظ القرآن</h2>
        <h2>Monthly Progress Report</h2>
        <h3 class="urdu-text">ماہانہ پراگرس رپورٹ</h3>
        <p><strong><?php echo date('F, Y', strtotime($month . '-01')); ?></strong></p>
    </div>

    <!-- Student Information -->
    <div class="student-info">
        <div>
            <div class="info-row">
                <span class="info-label">Name / نام:</span>
                <span><?php echo $student_data['first_name'] . ' ' . $student_data['last_name']; ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">S/O / ولدیت:</span>
                <span><?php echo $student_data['father_name']; ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Date of Admission / تاریخ داخلہ:</span>
                <span><?php echo formatDate($student_data['admission_date']); ?></span>
            </div>
        </div>

        <div>
            <div class="info-row">
                <span class="info-label">House / ہاوس:</span>
                <span><?php echo $student_data['class_name']; ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Student ID:</span>
                <span><?php echo $student_data['student_id']; ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">School Class:</span>
                <span><?php echo $student_data['previous_education'] ?? 'N/A'; ?></span>
            </div>
        </div>
    </div>

    <!-- Attendance Summary -->
    <h3 style="color: #2e7d32; border-bottom: 2px solid #2e7d32; padding-bottom: 10px;">
        <span class="urdu-text">حاضری</span> / Attendance Summary
    </h3>
    <table class="progress-table">
        <tr>
            <th class="urdu-text">کل ایام کار</th>
            <th class="urdu-text">حاضری</th>
            <th class="urdu-text">غیر حاضری</th>
            <th class="urdu-text">رخصت</th>
            <th>حاضری فیصد<br>Attendance %</th>
        </tr>
        <tr>
            <td><?php echo $monthly_data['attendance']['total_days'] ?? 0; ?></td>
            <td><?php echo $monthly_data['attendance']['present_days'] ?? 0; ?></td>
            <td><?php echo $monthly_data['attendance']['absent_days'] ?? 0; ?></td>
            <td><?php echo $monthly_data['attendance']['leave_days'] ?? 0; ?></td>
            <td>
                <?php
                $total = $monthly_data['attendance']['total_days'] ?? 0;
                $present = $monthly_data['attendance']['present_days'] ?? 0;
                $percentage = $total > 0 ? round(($present / $total) * 100, 2) : 0;
                echo $percentage . '%';
                ?>
            </td>
        </tr>
    </table>

    <!-- Progress Summary -->
    <h3 style="color: #2e7d32; border-bottom: 2px solid #2e7d32; padding-bottom: 10px; margin-top: 30px;">
        <span class="urdu-text">حفظ القرآن پراگرس رپورٹ</span> / Quran Progress Report
    </h3>
    <table class="progress-table">
        <thead>
            <tr>
                <th class="urdu-text">تاریخ</th>
                <th class="urdu-text">قسم</th>
                <th class="urdu-text">پارہ نمبر</th>
                <th class="urdu-text">نام سورۃ</th>
                <th class="urdu-text">صفحہ سے</th>
                <th class="urdu-text">صفحہ تک</th>
                <th class="urdu-text">کارکردگی</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($monthly_data['progress']) > 0):
                foreach ($monthly_data['progress'] as $record):
                    $type_urdu = [
                        'new_lesson' => 'سبق',
                        'revision' => 'سبقی',
                        'manzil' => 'منزل'
                    ];
            ?>
            <tr>
                <td><?php echo date('d/m/Y', strtotime($record['progress_date'])); ?></td>
                <td class="urdu-text"><?php echo $type_urdu[$record['sabak_type']] ?? $record['sabak_type']; ?></td>
                <td><?php echo $record['juz_number']; ?></td>
                <td class="urdu-text"><?php echo $record['surah_name']; ?></td>
                <td><?php echo $record['page_from']; ?></td>
                <td><?php echo $record['page_to']; ?></td>
                <td><?php echo $record['performance_rating'] ?? '-'; ?>/10</td>
            </tr>
            <?php endforeach; else: ?>
            <tr>
                <td colspan="7" class="text-center">کوئی پراگرس ریکارڈ نہیں ملا / No progress records found</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Progress Test Summary -->
    <h3 style="color: #2e7d32; border-bottom: 2px solid #2e7d32; padding-bottom: 10px; margin-top: 30px;">
        <span class="urdu-text">پراگرس ٹیسٹ</span> / Progress Test
    </h3>
    <table class="progress-table">
        <tr>
            <th class="urdu-text">تاریخ ٹیسٹ</th>
            <th class="urdu-text">سبقی</th>
            <th class="urdu-text">پارہ نمبر</th>
            <th class="urdu-text">نتیجہ</th>
        </tr>
        <tr>
            <td><?php echo date('d/m/Y', strtotime($month_end)); ?></td>
            <td><?php echo $monthly_data['summary']['sabqi_count'] ?? 0; ?></td>
            <td>
                <?php
                if (count($monthly_data['progress']) > 0) {
                    echo $monthly_data['progress'][0]['juz_number'];
                } else {
                    echo '-';
                }
                ?>
            </td>
            <td>
                <?php
                $total_records = $monthly_data['summary']['total_records'] ?? 0;
                if ($total_records >= 20) echo 'ممتاز / Excellent';
                elseif ($total_records >= 15) echo 'اچھا / Good';
                elseif ($total_records >= 10) echo 'اوسط / Average';
                else echo 'کمزور / Weak';
                ?>
            </td>
        </tr>
    </table>

    <!-- Signatures -->
    <div class="signature-section">
        <div class="signature-box">
            <strong class="urdu-text">نام ہاوس انچارج</strong><br>
            House Incharge Name<br>
            <span class="urdu-text">رابطہ نمبر:</span> _____________
        </div>
        <div class="signature-box">
            <strong>Vice Principal</strong><br>
            <span class="urdu-text">نائب پرنسپل</span>
        </div>
        <div class="signature-box">
            <strong>Date / تاریخ</strong><br>
            <?php echo date('d/m/Y'); ?>
        </div>
    </div>
</div>

<?php elseif ($class_id > 0 && $student_id > 0): ?>
<div class="alert alert-warning no-print">
    No data found for selected student and month.
</div>
<?php else: ?>
<div class="alert alert-info no-print">
    Please select house/class and student to generate the monthly progress report.
</div>
<?php endif; ?>

<?php require_once '../../includes/footer.php'; ?>
