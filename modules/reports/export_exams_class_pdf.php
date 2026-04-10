<?php
require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../includes/functions.php';
require_once '../../libs/PdfService.php';

$range = $_GET['range'] ?? 'monthly';
$from = $_GET['from'] ?? '';
$to = $_GET['to'] ?? '';

function rngE($r,$f,$t){$d=date('Y-m-d');if($r=='weekly')return [date('Y-m-d',strtotime('monday this week')),date('Y-m-d',strtotime('sunday this week'))];if($r=='monthly')return [date('Y-m-01'),date('Y-m-t')];if($r=='yearly')return [date('Y-01-01'),date('Y-12-31')];if($r=='custom'&&$f&&$t)return [$f,$t];return [$d,$d];}
[$date_from,$date_to]=rngE($range,$from,$to);

$sql = "
SELECT c.class_name,
       COUNT(DISTINCT e.id) AS exams_count,
       COUNT(er.id) AS results_count,
       AVG(CASE WHEN e.total_marks IS NOT NULL AND e.total_marks>0 AND er.obtained_marks IS NOT NULL
                THEN (er.obtained_marks/e.total_marks)*100 ELSE NULL END) AS avg_percentage,
       SUM(CASE WHEN e.passing_marks IS NOT NULL AND e.total_marks IS NOT NULL AND e.total_marks>0 AND er.obtained_marks IS NOT NULL AND er.obtained_marks >= e.passing_marks
                THEN 1 ELSE 0 END) AS passes,
       SUM(CASE WHEN er.id IS NOT NULL THEN 1 ELSE 0 END) AS evaluated
FROM exams e
LEFT JOIN classes c ON c.id = e.class_id
LEFT JOIN exam_results er ON er.exam_id = e.id
LEFT JOIN students s ON s.id = er.student_id
WHERE e.exam_date BETWEEN ? AND ?
GROUP BY c.class_name
ORDER BY c.class_name";
$st = $db->prepare($sql);
$st->execute([$date_from,$date_to]);
$rows = $st->fetchAll();

$period = date('d M Y',strtotime($date_from)).' - '.date('d M Y',strtotime($date_to));

ob_start();
?>
<h2>Exams Summary (Class-wise)</h2>
<div class="meta">Period: <?php echo htmlspecialchars($period); ?></div>
<table>
  <thead>
    <tr>
      <th>Class</th><th>Exams</th><th>Results</th><th>Avg %</th><th>Pass Rate</th>
    </tr>
  </thead>
  <tbody>
  <?php foreach($rows as $r): $evaluated=(int)$r['evaluated']; $passes=(int)$r['passes']; $rate= $evaluated>0? round(($passes/$evaluated)*100,2):0; ?>
    <tr>
      <td><?php echo htmlspecialchars($r['class_name'] ?? 'All Classes'); ?></td>
      <td><?php echo (int)$r['exams_count']; ?></td>
      <td><?php echo (int)$r['results_count']; ?></td>
      <td><?php echo ($r['avg_percentage']!==null)? round($r['avg_percentage'],2).'%' : 'N/A'; ?></td>
      <td><?php echo $rate; ?>%</td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
<?php
$html = ob_get_clean();
PdfService::outputHtml($html, 'Exams Class-wise - '.date('d M Y',strtotime($date_to)).'.pdf');

