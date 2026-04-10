<?php
require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../includes/functions.php';
require_once '../../libs/PdfService.php';

$class_id = isset($_GET['class_id']) ? (int)$_GET['class_id'] : 0;
$from = $_GET['from'] ?? date('Y-m-01');
$to = $_GET['to'] ?? date('Y-m-t');
if ($class_id <= 0) { die('class_id is required'); }

$st = $db->prepare("SELECT class_name FROM classes WHERE id=?");
$st->execute([$class_id]);
$class = $st->fetch();
if (!$class) { die('Class not found'); }

$st = $db->prepare("SELECT id, student_id, first_name, last_name FROM students WHERE class_id=? AND status='active' ORDER BY first_name, last_name");
$st->execute([$class_id]);
$students = $st->fetchAll();

function cnt($db,$t,$sid,$f,$to){$q=$db->prepare("SELECT COUNT(*) c FROM {$t} WHERE student_id=? AND record_date BETWEEN ? AND ?");$q->execute([$sid,$f,$to]);return (int)$q->fetch()['c'];}

$period = date('d M Y', strtotime($from)) . ' - ' . date('d M Y', strtotime($to));

ob_start();
?>
<h2>Monthly Progress (Class)</h2>
<div class="meta">Class: <strong><?php echo htmlspecialchars($class['class_name']); ?></strong> &nbsp; | &nbsp; Period: <?php echo htmlspecialchars($period); ?></div>

<table>
  <thead>
    <tr>
      <th>S.No</th><th>Student</th><th>Student ID</th><th>Sabak</th><th>Sabqi</th><th>Manzil</th><th>Total</th>
    </tr>
  </thead>
  <tbody>
  <?php $i=1; foreach ($students as $s): $sab=cnt($db,'sabak_records',$s['id'],$from,$to); $sbq=cnt($db,'sabqi_records',$s['id'],$from,$to); $mz=cnt($db,'manzil_records',$s['id'],$from,$to); $tot=$sab+$sbq+$mz; ?>
    <tr>
      <td><?php echo $i++; ?></td>
      <td><?php echo htmlspecialchars($s['first_name'].' '.$s['last_name']); ?></td>
      <td><?php echo htmlspecialchars($s['student_id']); ?></td>
      <td><?php echo $sab; ?></td>
      <td><?php echo $sbq; ?></td>
      <td><?php echo $mz; ?></td>
      <td><?php echo $tot; ?></td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
<?php
$html = ob_get_clean();
PdfService::outputHtml($html, 'Monthly Progress - '.$class['class_name'].' ('.date('M Y', strtotime($from)).').pdf');

