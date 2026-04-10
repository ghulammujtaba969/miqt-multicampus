<?php
require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../includes/functions.php';
require_once '../../libs/PdfService.php';

$student_id = isset($_GET['student_id']) ? (int)$_GET['student_id'] : 0;
$from = $_GET['from'] ?? date('Y-m-01');
$to = $_GET['to'] ?? date('Y-m-t');

if ($student_id <= 0) { die('student_id is required'); }

$st = $db->prepare("SELECT s.*, c.class_name FROM students s LEFT JOIN classes c ON c.id=s.class_id WHERE s.id=?");
$st->execute([$student_id]);
$student = $st->fetch();
if (!$student) { die('Student not found'); }

function cnt($db,$t,$sid,$f,$to){$q=$db->prepare("SELECT COUNT(*) c FROM {$t} WHERE student_id=? AND record_date BETWEEN ? AND ?");$q->execute([$sid,$f,$to]);return (int)$q->fetch()['c'];}
$sabak = cnt($db,'sabak_records',$student_id,$from,$to);
$sabqi = cnt($db,'sabqi_records',$student_id,$from,$to);
$manzil = cnt($db,'manzil_records',$student_id,$from,$to);

$period = date('d M Y', strtotime($from)) . ' - ' . date('d M Y', strtotime($to));

ob_start();
?>
<h2>Result Card (Hifz)</h2>
<div class="meta">Student: <strong><?php echo htmlspecialchars($student['first_name'].' '.$student['last_name']); ?></strong> (<?php echo htmlspecialchars($student['student_id']); ?>) &nbsp; | &nbsp; Class: <?php echo htmlspecialchars($student['class_name'] ?? '—'); ?> &nbsp; | &nbsp; Period: <?php echo htmlspecialchars($period); ?></div>

<table>
  <thead>
    <tr><th>Metric</th><th>Count</th></tr>
  </thead>
  <tbody>
    <tr><td>Sabak (New Lesson)</td><td><?php echo $sabak; ?></td></tr>
    <tr><td>Sabqi (Revision)</td><td><?php echo $sabqi; ?></td></tr>
    <tr><td>Manzil (Complete Revision)</td><td><?php echo $manzil; ?></td></tr>
    <tr><td><strong>Total</strong></td><td><strong><?php echo ($sabak+$sabqi+$manzil); ?></strong></td></tr>
  </tbody>
</table>
<?php
$html = ob_get_clean();
PdfService::outputHtml($html, 'Result Card - '.($student['first_name'].' '.$student['last_name']).' ('.date('M Y', strtotime($from)).').pdf');

