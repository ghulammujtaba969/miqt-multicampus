<?php
require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../includes/functions.php';
require_once '../../libs/PdfService.php';

$range = $_GET['range'] ?? 'daily';
$from = $_GET['from'] ?? '';
$to = $_GET['to'] ?? '';
$class_id = isset($_GET['class_id']) ? (int)$_GET['class_id'] : 0;

function rrange($r,$f,$t){$d=date('Y-m-d');if($r=='weekly')return [date('Y-m-d',strtotime('monday this week')),date('Y-m-d',strtotime('sunday this week'))];if($r=='monthly')return [date('Y-m-01'),date('Y-m-t')];if($r=='yearly')return [date('Y-01-01'),date('Y-12-31')];if($r=='custom'&&$f&&$t)return [$f,$t];return [$d,$d];}
[$date_from,$date_to]=rrange($range,$from,$to);

// Class-wise summary
$params = [$date_from, $date_to];
$whereClass = '';
if ($class_id > 0) { $whereClass = ' AND sa.class_id = ?'; $params[] = $class_id; }
$sql = "SELECT c.class_name,
               SUM(CASE WHEN sa.status='present' THEN 1 ELSE 0 END) AS present,
               SUM(CASE WHEN sa.status='absent' THEN 1 ELSE 0 END) AS absent,
               SUM(CASE WHEN sa.status='leave' THEN 1 ELSE 0 END) AS `leave`,
               COUNT(*) AS total
        FROM student_attendance sa
        JOIN classes c ON c.id = sa.class_id
        WHERE sa.attendance_date BETWEEN ? AND ? {$whereClass}
        GROUP BY c.class_name ORDER BY c.class_name";
$stmt = $db->prepare($sql);
$stmt->execute($params);
$classRows = $stmt->fetchAll();

// Student-wise (always include class name column for uniform layout)
if ($class_id > 0) {
    $sql = "SELECT c.class_name, s.student_id, s.first_name, s.last_name,
                   SUM(CASE WHEN sa.status='present' THEN 1 ELSE 0 END) AS present,
                   SUM(CASE WHEN sa.status='absent' THEN 1 ELSE 0 END) AS absent,
                   SUM(CASE WHEN sa.status='leave' THEN 1 ELSE 0 END) AS `leave`,
                   COUNT(*) AS total
            FROM students s
            JOIN classes c ON c.id = s.class_id
            LEFT JOIN student_attendance sa ON sa.student_id = s.id AND sa.attendance_date BETWEEN ? AND ?
            WHERE s.class_id = ? AND s.status='active'
            GROUP BY s.id, c.class_name
            ORDER BY s.first_name, s.last_name";
    $stmt = $db->prepare($sql);
    $stmt->execute([$date_from,$date_to,$class_id]);
} else {
    $sql = "SELECT c.class_name, s.student_id, s.first_name, s.last_name,
                   SUM(CASE WHEN sa.status='present' THEN 1 ELSE 0 END) AS present,
                   SUM(CASE WHEN sa.status='absent' THEN 1 ELSE 0 END) AS absent,
                   SUM(CASE WHEN sa.status='leave' THEN 1 ELSE 0 END) AS `leave`,
                   COUNT(*) AS total
            FROM students s
            JOIN classes c ON c.id = s.class_id
            LEFT JOIN student_attendance sa ON sa.student_id = s.id AND sa.attendance_date BETWEEN ? AND ?
            WHERE s.status='active'
            GROUP BY s.id, c.class_name
            ORDER BY c.class_name, s.first_name, s.last_name";
    $stmt = $db->prepare($sql);
    $stmt->execute([$date_from,$date_to]);
}
$studRows = $stmt->fetchAll();

$period = date('d M Y', strtotime($date_from)) . ' - ' . date('d M Y', strtotime($date_to));

ob_start();
?>
<h2>Attendance Report</h2>
<div class="meta">Period: <?php echo htmlspecialchars($period); ?></div>

<table>
    <thead>
        <tr>
            <th>Sr No</th>
            <th>Class</th>
            <th>Student</th>
            <th>Status (Absent/Leave)</th>
            <th>Days</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $sr = 1;
        foreach ($studRows as $r):
        ?>
            <tr>
                <td><?php echo $sr++; ?></td>
                <td><?php echo htmlspecialchars($r['class_name']); ?></td>
                <td><?php echo htmlspecialchars($r['first_name'] . ' ' . $r['last_name']); ?></td>
                <td>Absent</td>
                <td><?php echo (int)$r['absent']; ?></td>
            </tr>
            <tr>
                <td><?php echo $sr++; ?></td>
                <td><?php echo htmlspecialchars($r['class_name']); ?></td>
                <td><?php echo htmlspecialchars($r['first_name'] . ' ' . $r['last_name']); ?></td>
                <td>Leave</td>
                <td><?php echo (int)$r['leave']; ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php
$html = ob_get_clean();
PdfService::outputHtml($html, (isset($_GET['filename'])? $_GET['filename'] : (date('d F Y') . '.pdf')));
