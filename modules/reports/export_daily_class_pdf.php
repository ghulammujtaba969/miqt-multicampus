<?php
require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../includes/functions.php';
require_once '../../libs/PdfService.php';

$class_id = isset($_GET['class_id']) ? (int)$_GET['class_id'] : 0;
$date = $_GET['date'] ?? date('Y-m-d');
if ($class_id <= 0) { die('class_id is required'); }

$st = $db->prepare("SELECT class_name FROM classes WHERE id=?");
$st->execute([$class_id]);
$class = $st->fetch();
if (!$class) { die('Class not found'); }

$st = $db->prepare("SELECT id, student_id, first_name, last_name, father_name, admission_date FROM students WHERE class_id=? AND status='active' ORDER BY first_name, last_name");
$st->execute([$class_id]);
$students = $st->fetchAll();

ob_start();
?>
<h2>Daily Quran Progress</h2>
<div class="meta">Class: <strong><?php echo htmlspecialchars($class['class_name']); ?></strong> &nbsp; | &nbsp; Date: <?php echo htmlspecialchars(date('d M Y', strtotime($date))); ?></div>

<table>
  <thead>
    <tr>
      <th>S.No</th><th>Student</th><th>Father</th><th>Admission</th>
      <th>Sabak Para</th><th>Surah</th><th>Ayat (From-To)</th><th>Lines</th>
      <th>Sabqi Para</th><th>Amount</th>
      <th>Manzil From</th><th>Amount/Heard</th>
    </tr>
  </thead>
  <tbody>
  <?php $i=1; foreach($students as $s):
      $sab = $db->prepare("SELECT * FROM sabak_records WHERE student_id=? AND record_date=? ORDER BY id DESC LIMIT 1");
      $sab->execute([$s['id'],$date]); $r1 = $sab->fetch();
      $sbq = $db->prepare("SELECT * FROM sabqi_records WHERE student_id=? AND record_date=? ORDER BY id DESC LIMIT 1");
      $sbq->execute([$s['id'],$date]); $r2 = $sbq->fetch();
      $mz = $db->prepare("SELECT * FROM manzil_records WHERE student_id=? AND record_date=? ORDER BY id DESC LIMIT 1");
      $mz->execute([$s['id'],$date]); $r3 = $mz->fetch();
      $range = $r1 ? trim(($r1['page_from']??'').(($r1['page_to']??'') && $r1['page_to']!=$r1['page_from']?('-'.$r1['page_to']):'')) : '';
  ?>
    <tr>
      <td><?php echo $i++; ?></td>
      <td><?php echo htmlspecialchars($s['first_name'].' '.$s['last_name']); ?></td>
      <td><?php echo htmlspecialchars($s['father_name']); ?></td>
      <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($s['admission_date']))); ?></td>
      <td><?php echo $r1 ? htmlspecialchars($r1['juz_number']) : '—'; ?></td>
      <td><?php echo $r1 ? htmlspecialchars($r1['surah_name']) : '—'; ?></td>
      <td><?php echo $r1 ? htmlspecialchars($range) : '—'; ?></td>
      <td><?php echo ($r1 && $r1['lines_memorized'] !== null) ? (int)$r1['lines_memorized'] : '—'; ?></td>
      <td><?php echo $r2 ? htmlspecialchars($r2['juz_number']) : '—'; ?></td>
      <td><?php echo $r2 ? htmlspecialchars($r2['remarks'] ?? '—') : '—'; ?></td>
      <td><?php echo $r3 ? htmlspecialchars($r3['juz_from']) : '—'; ?></td>
      <td><?php echo $r3 ? htmlspecialchars($r3['remarks'] ?? '—') : '—'; ?></td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
<?php
$html = ob_get_clean();
PdfService::outputHtml($html, 'Daily Progress - '.$class['class_name'].' ('.date('d M Y', strtotime($date)).').pdf', 'L');

