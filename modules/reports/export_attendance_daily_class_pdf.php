<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Daily Attendance Report</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style type="text/css">
    body {
      font-family: dejavusans, sans-serif;
      margin: 16px;
      padding-bottom: 90px; /* space for signatures fixed at bottom */
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 8px;
      font-size: 11px;
    }

    th,
    td {
      border: 1px solid #999;
      padding: 6px;
      text-align: center;
    }

    th {
      background: #f1f5f4;
      font-weight: 700;
    }

    .left {
      text-align: left
    }
    
  </style>
</head>
<?php
require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../includes/functions.php';
require_once '../../libs/PdfService.php';

// Inputs
$range = $_GET['range'] ?? 'daily';
$from  = $_GET['from']  ?? '';
$to    = $_GET['to']    ?? '';

function _resolveRange($r, $f, $t)
{
  $d = date('Y-m-d');
  if ($r === 'weekly') return [date('Y-m-d', strtotime('monday this week')), date('Y-m-d', strtotime('sunday this week'))];
  if ($r === 'monthly') return [date('Y-m-01'), date('Y-m-t')];
  if ($r === 'yearly') return [date('Y-01-01'), date('Y-12-31')];
  if ($r === 'custom' && $f && $t) return [$f, $t];
  return [$d, $d];
}
[$date_from, $date_to] = _resolveRange($range, $from, $to);

// Detect if student_type column exists
$colCheck = $db->prepare("SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'students' AND COLUMN_NAME = 'student_type'");
$colCheck->execute([DB_NAME]);
$hasStudentType = ((int)$colCheck->fetchColumn()) > 0;

// Classes with teacher
$sql = "SELECT c.id, c.class_name, c.class_teacher_id, CONCAT(t.first_name, ' ', t.last_name) AS teacher_name
        FROM classes c
        LEFT JOIN teachers t ON t.id = c.class_teacher_id
        WHERE c.status = 'active'
        ORDER BY c.class_name";
$st = $db->prepare($sql);
$st->execute();
$classes = $st->fetchAll();

// Student counts per type per class
if ($hasStudentType) {
  $countStudentsStmt = $db->prepare("SELECT student_type, COUNT(*) AS cnt FROM students WHERE status='active' AND class_id=? GROUP BY student_type");
} else {
  $countStudentsTotalStmt = $db->prepare("SELECT COUNT(*) AS cnt FROM students WHERE status='active' AND class_id=?");
}

// Attendance sums per class
$attStmt = $db->prepare("SELECT
    SUM(CASE WHEN status='present' THEN 1 ELSE 0 END) AS present,
    SUM(CASE WHEN status='absent'  THEN 1 ELSE 0 END) AS absent,
    SUM(CASE WHEN status='leave'   THEN 1 ELSE 0 END) AS `leave`
    FROM student_attendance
    WHERE class_id = ? AND attendance_date BETWEEN ? AND ?");

$rows = [];
$totals = ['TS' => 0, 'BD' => 0, 'DS' => 0, 'Orp' => 0, 'P' => 0, 'A' => 0, 'L' => 0, 'Total' => 0];

foreach ($classes as $c) {
  // Students composition
  $counts = ['day_scholar' => 0, 'boarder' => 0, 'orphan' => 0];
  if ($hasStudentType) {
    $countStudentsStmt->execute([$c['id']]);
    foreach ($countStudentsStmt->fetchAll() as $r) {
      $tk = $r['student_type'];
      if (isset($counts[$tk])) $counts[$tk] = (int)$r['cnt'];
    }
    $TS = $counts['day_scholar'] + $counts['boarder'] + $counts['orphan'];
  } else {
    $countStudentsTotalStmt->execute([$c['id']]);
    $TS = (int)$countStudentsTotalStmt->fetchColumn();
    $counts['day_scholar'] = $TS; // fallback
  }

  // Attendance
  $attStmt->execute([$c['id'], $date_from, $date_to]);
  $ar = $attStmt->fetch();
  $present = (int)($ar['present'] ?? 0);
  $absent  = (int)($ar['absent'] ?? 0);
  $leave   = (int)($ar['leave'] ?? 0);
  $totalA  = $present + $absent + $leave;

  $rows[] = [
    'class_name' => $c['class_name'],
    'teacher'    => $c['teacher_name'] ?: '-',
    'grade'      => '-',
    'TS'         => $TS,
    'BD'         => $counts['boarder'],
    'DS'         => $counts['day_scholar'],
    'Orp'        => $counts['orphan'],
    'P'          => $present,
    'A'          => $absent,
    'L'          => $leave,
    'Total'      => $totalA,
  ];

  $totals['TS']   += $TS;
  $totals['BD']   += $counts['boarder'];
  $totals['DS']   += $counts['day_scholar'];
  $totals['Orp']  += $counts['orphan'];
  $totals['P']    += $present;
  $totals['A']    += $absent;
  $totals['L']    += $leave;
  $totals['Total'] += $totalA;
}

// Type summary with fallback
$typeAgg = [
  'boarder'     => ['present' => 0, 'absent' => 0, 'leave' => 0, 'students' => 0],
  'day_scholar' => ['present' => 0, 'absent' => 0, 'leave' => 0, 'students' => 0],
  'orphan'      => ['present' => 0, 'absent' => 0, 'leave' => 0, 'students' => 0],
];
if ($hasStudentType) {
  $studTypeSummaryStmt = $db->prepare("SELECT s.student_type,
        SUM(CASE WHEN sa.status='present' THEN 1 ELSE 0 END) AS present,
        SUM(CASE WHEN sa.status='absent'  THEN 1 ELSE 0 END) AS absent,
        SUM(CASE WHEN sa.status='leave'   THEN 1 ELSE 0 END) AS `leave`,
        COUNT(CASE WHEN s.status='active' THEN s.id END) AS students
        FROM students s
        LEFT JOIN student_attendance sa ON sa.student_id = s.id AND sa.attendance_date BETWEEN ? AND ?
        WHERE s.status='active'
        GROUP BY s.student_type");
  $studTypeSummaryStmt->execute([$date_from, $date_to]);
  foreach ($studTypeSummaryStmt->fetchAll() as $r) {
    $t = $r['student_type'] ?: 'day_scholar';
    if (!isset($typeAgg[$t])) continue;
    $typeAgg[$t]['present']  = (int)$r['present'];
    $typeAgg[$t]['absent']   = (int)$r['absent'];
    $typeAgg[$t]['leave']    = (int)$r['leave'];
    $typeAgg[$t]['students'] = (int)$r['students'];
  }
} else {
  $overallStmt = $db->prepare("SELECT
        SUM(CASE WHEN status='present' THEN 1 ELSE 0 END) AS present,
        SUM(CASE WHEN status='absent'  THEN 1 ELSE 0 END) AS absent,
        SUM(CASE WHEN status='leave'   THEN 1 ELSE 0 END) AS `leave`
        FROM student_attendance
        WHERE attendance_date BETWEEN ? AND ?");
  $overallStmt->execute([$date_from, $date_to]);
  $o = $overallStmt->fetch() ?: ['present' => 0, 'absent' => 0, 'leave' => 0];
  $countStudentsAll = (int)$db->query("SELECT COUNT(*) FROM students WHERE status='active'")->fetchColumn();
  $typeAgg['day_scholar'] = [
    'present' => (int)$o['present'],
    'absent' => (int)$o['absent'],
    'leave'  => (int)$o['leave'],
    'students' => $countStudentsAll,
  ];
}
$overall = [
  'present' => $typeAgg['boarder']['present'] + $typeAgg['day_scholar']['present'] + $typeAgg['orphan']['present'],
  'absent'  => $typeAgg['boarder']['absent']  + $typeAgg['day_scholar']['absent']  + $typeAgg['orphan']['absent'],
  'leave'   => $typeAgg['boarder']['leave']   + $typeAgg['day_scholar']['leave']   + $typeAgg['orphan']['leave'],
  'students' => $typeAgg['boarder']['students'] + $typeAgg['day_scholar']['students'] + $typeAgg['orphan']['students'],
];

// Teacher summary
$teaStmt = $db->prepare("SELECT
    SUM(CASE WHEN status='present' THEN 1 ELSE 0 END) AS present,
    SUM(CASE WHEN status='absent'  THEN 1 ELSE 0 END) AS absent,
    SUM(CASE WHEN status='leave'   THEN 1 ELSE 0 END) AS `leave`,
    COUNT(*) AS total
    FROM teacher_attendance
    WHERE attendance_date BETWEEN ? AND ?");
$teaStmt->execute([$date_from, $date_to]);
$tea = $teaStmt->fetch() ?: ['present' => 0, 'absent' => 0, 'leave' => 0, 'total' => 0];

// HTML -> PDF
ob_start();
?>

<body>
  <h1 style="text-align:center;margin:0 0 6px 0;">Daily Attendance Report</h1>
  <div style="font-size:12px;margin-bottom:6px;">Date: <?php echo htmlspecialchars($date_from); ?><?php echo ($date_from !== $date_to) ? (' to ' . htmlspecialchars($date_to)) : ''; ?></div>

  <h3>Class-wise Attendance</h3>
  <table>
    <thead>
      <tr>
        <th>S. No</th>
        <th class="left">Name of House</th>
        <th class="left">Teacher</th>
        <th>Grade</th>
        <th>T. S</th>
        <th>B / D</th>
        <th>D / S</th>
        <th>Orp</th>
        <th>P</th>
        <th>A</th>
        <th>L</th>
        <th>Total</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($rows as $i => $row): ?>
        <tr>
          <td><?php echo $i + 1; ?></td>
          <td class="left"><?php echo htmlspecialchars($row['class_name']); ?></td>
          <td class="left"><?php echo htmlspecialchars($row['teacher']); ?></td>
          <td><?php echo htmlspecialchars($row['grade']); ?></td>
          <td><?php echo (int)$row['TS']; ?></td>
          <td><?php echo (int)$row['BD']; ?></td>
          <td><?php echo (int)$row['DS']; ?></td>
          <td><?php echo (int)$row['Orp']; ?></td>
          <td><?php echo (int)$row['P']; ?></td>
          <td><?php echo (int)$row['A']; ?></td>
          <td><?php echo (int)$row['L']; ?></td>
          <td><?php echo (int)$row['Total']; ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <h3>Student Summary</h3>
  <table>
    <thead>
      <tr>
        <th>Type</th>
        <th>Present</th>
        <th>Absent</th>
        <th>Leave</th>
        <th>Total</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>Boarder</td>
        <td><?php echo (int)$typeAgg['boarder']['present']; ?></td>
        <td><?php echo (int)$typeAgg['boarder']['absent']; ?></td>
        <td><?php echo (int)$typeAgg['boarder']['leave']; ?></td>
        <td><?php echo (int)$typeAgg['boarder']['students']; ?></td>
      </tr>
      <tr>
        <td>Day Scholar</td>
        <td><?php echo (int)$typeAgg['day_scholar']['present']; ?></td>
        <td><?php echo (int)$typeAgg['day_scholar']['absent']; ?></td>
        <td><?php echo (int)$typeAgg['day_scholar']['leave']; ?></td>
        <td><?php echo (int)$typeAgg['day_scholar']['students']; ?></td>
      </tr>
      <tr>
        <td>Orphan</td>
        <td><?php echo (int)$typeAgg['orphan']['present']; ?></td>
        <td><?php echo (int)$typeAgg['orphan']['absent']; ?></td>
        <td><?php echo (int)$typeAgg['orphan']['leave']; ?></td>
        <td><?php echo (int)$typeAgg['orphan']['students']; ?></td>
      </tr>
      <tr>
        <td>Total</td>
        <td><?php echo (int)$overall['present']; ?></td>
        <td><?php echo (int)$overall['absent']; ?></td>
        <td><?php echo (int)$overall['leave']; ?></td>
        <td><?php echo (int)$overall['students']; ?></td>
      </tr>
    </tbody>
  </table>

  <h3>Teacher Summary</h3>
  <table>
    <thead>
      <tr>
        <th>Teacher</th>
        <th>Present</th>
        <th>Absent</th>
        <th>Leave</th>
        <th>Total</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>Teachers</td>
        <td><?php echo (int)$tea['present']; ?></td>
        <td><?php echo (int)$tea['absent']; ?></td>
        <td><?php echo (int)$tea['leave']; ?></td>
        <td><?php echo (int)$tea['total']; ?></td>
      </tr>
    </tbody>
  </table>

<table style="margin-top:100px; border-collapse:separate; border-spacing:15px 0;">
  <thead style="text-align:center;">
    <tr>
      <td style="border:0; border-top:2px solid black;">Hostel Warden</td>
      <td style="border:0; border-top:2px solid black;">Coordinator</td>
      <td style="border:0; border-top:2px solid black;">V. Principal</td>
      <td style="border:0; border-top:2px solid black;">Principal</td>
    </tr>
  </thead>
</table>



</body>

</html>
<?php
$html = ob_get_clean();
$filename = isset($_GET['filename']) ? $_GET['filename'] : ('Daily_Attendance_' . date('d_m_Y', strtotime($date_from)) . '.pdf');
PdfService::outputHtml($html, $filename, 'P');
?>
