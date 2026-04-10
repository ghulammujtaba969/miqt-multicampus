<?php
require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../includes/functions.php';
require_once '../../libs/PdfService.php';

$class_id = isset($_GET['class_id']) ? (int)$_GET['class_id'] : 0;
$range = $_GET['range'] ?? 'monthly';
$from = $_GET['from'] ?? '';
$to = $_GET['to'] ?? '';
if ($class_id <= 0) { die('class_id is required'); }

function rngE($r,$f,$t){$d=date('Y-m-d');if($r=='weekly')return [date('Y-m-d',strtotime('monday this week')),date('Y-m-d',strtotime('sunday this week'))];if($r=='monthly')return [date('Y-m-01'),date('Y-m-t')];if($r=='yearly')return [date('Y-01-01'),date('Y-12-31')];if($r=='custom'&&$f&&$t)return [$f,$t];return [$d,$d];}
[$date_from,$date_to]=rngE($range,$from,$to);

$st=$db->prepare("SELECT class_name FROM classes WHERE id=?");
$st->execute([$class_id]);
$class = $st->fetch();
if (!$class) { die('Class not found'); }

// Students in class
$st=$db->prepare("SELECT id, student_id, first_name, last_name FROM students WHERE class_id=? AND status='active' ORDER BY first_name, last_name");
$st->execute([$class_id]);
$students=$st->fetchAll();

// Summary per student
$rows=[];
if (count($students)>0) {
    $sql="SELECT s.id AS student_id,
                  AVG(CASE WHEN e.total_marks IS NOT NULL AND e.total_marks>0 AND er.obtained_marks IS NOT NULL THEN (er.obtained_marks/e.total_marks)*100 ELSE NULL END) AS avg_percentage,
                  SUM(CASE WHEN e.passing_marks IS NOT NULL AND e.total_marks IS NOT NULL AND e.total_marks>0 AND er.obtained_marks IS NOT NULL AND er.obtained_marks >= e.passing_marks THEN 1 ELSE 0 END) AS passes,
                  COUNT(er.id) AS attempts
           FROM students s
           LEFT JOIN exam_results er ON er.student_id = s.id
           LEFT JOIN exams e ON e.id = er.exam_id AND e.exam_date BETWEEN ? AND ?
           WHERE s.class_id = ?
           GROUP BY s.id";
    $st=$db->prepare($sql);
    $st->execute([$date_from,$date_to,$class_id]);
    while($r=$st->fetch()){ $rows[$r['student_id']]=$r; }
}

$period = date('d M Y',strtotime($date_from)).' - '.date('d M Y',strtotime($date_to));

ob_start();
?>
<h2>Exams Summary (Student-wise)</h2>
<div class="meta">Class: <strong><?php echo htmlspecialchars($class['class_name']); ?></strong> &nbsp; | &nbsp; Period: <?php echo htmlspecialchars($period); ?></div>
<table>
  <thead>
    <tr>
      <th>Student</th><th>Student ID</th><th>Attempts</th><th>Avg %</th><th>Passes</th>
    </tr>
  </thead>
  <tbody>
  <?php foreach($students as $s): $r=$rows[$s['id']]??['attempts'=>0,'avg_percentage'=>null,'passes'=>0]; ?>
    <tr>
      <td><?php echo htmlspecialchars($s['first_name'].' '.$s['last_name']); ?></td>
      <td><?php echo htmlspecialchars($s['student_id']); ?></td>
      <td><?php echo (int)$r['attempts']; ?></td>
      <td><?php echo ($r['avg_percentage']!==null)? round($r['avg_percentage'],2).'%' : 'N/A'; ?></td>
      <td><?php echo (int)$r['passes']; ?></td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
<?php
$html = ob_get_clean();
PdfService::outputHtml($html, 'Exams Student-wise - '.($class['class_name']).' '.date('d M Y',strtotime($date_to)).'.pdf');

