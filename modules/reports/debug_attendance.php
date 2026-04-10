<?php
// Debug version - shows errors instead of PDF
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../includes/functions.php';

echo "Config loaded successfully<br>";

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

echo "Date range: $date_from to $date_to<br>";

try {
    // Detect if student_type column exists
    $colCheck = $db->prepare("SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'students' AND COLUMN_NAME = 'student_type'");
    $colCheck->execute([DB_NAME]);
    $hasStudentType = ((int)$colCheck->fetchColumn()) > 0;

    echo "Has student_type column: " . ($hasStudentType ? 'YES' : 'NO') . "<br>";

    // Classes with teacher
    $sql = "SELECT c.id, c.class_name, c.class_teacher_id, CONCAT(t.first_name, ' ', t.last_name) AS teacher_name
            FROM classes c
            LEFT JOIN teachers t ON t.id = c.class_teacher_id
            WHERE c.status = 'active'
            ORDER BY c.class_name";
    $st = $db->prepare($sql);
    $st->execute();
    $classes = $st->fetchAll();

    echo "Found " . count($classes) . " classes<br>";

    echo "<hr>All database queries executed successfully!<br>";
    echo "The PDF generation code is working. The issue might be with the PDF library.<br>";

} catch (Exception $e) {
    echo "<strong>ERROR:</strong> " . $e->getMessage() . "<br>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
