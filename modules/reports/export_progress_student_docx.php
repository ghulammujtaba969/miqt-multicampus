<?php
require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../includes/functions.php';
require_once '../../libs/DocxTemplate.php';

$student_id = isset($_GET['student_id']) ? (int)$_GET['student_id'] : 0;
$from = $_GET['from'] ?? date('Y-m-01');
$to = $_GET['to'] ?? date('Y-m-t');
$template = $_GET['template'] ?? (dirname(__DIR__,2) . DIRECTORY_SEPARATOR . 'Result Card Hifz.docx');

if ($student_id <= 0) {
    die('student_id is required');
}

// Student details
$st = $db->prepare("SELECT s.*, c.class_name FROM students s LEFT JOIN classes c ON c.id=s.class_id WHERE s.id=?");
$st->execute([$student_id]);
$student = $st->fetch();
if (!$student) { die('Student not found'); }

// Progress counts
function countTable($db,$table,$sid,$from,$to){
    $st=$db->prepare("SELECT COUNT(*) c FROM {$table} WHERE student_id=? AND record_date BETWEEN ? AND ?");
    $st->execute([$sid,$from,$to]);
    return (int)$st->fetch()['c'];
}
$sabak = countTable($db,'sabak_records',$student_id,$from,$to);
$sabqi = countTable($db,'sabqi_records',$student_id,$from,$to);
$manzil = countTable($db,'manzil_records',$student_id,$from,$to);

// Build and output DOCX from template with placeholders
try {
    $doc = new DocxTemplate($template);
    $doc->replace([
        'STUDENT_NAME' => $student['first_name'].' '.$student['last_name'],
        'STUDENT_ID' => $student['student_id'],
        'CLASS_NAME' => $student['class_name'] ?? '—',
        'PERIOD_FROM' => date('d/m/Y', strtotime($from)),
        'PERIOD_TO' => date('d/m/Y', strtotime($to)),
        'SABAK' => (string)$sabak,
        'SABQI' => (string)$sabqi,
        'MANZIL' => (string)$manzil,
    ]);
    $fname = 'Result Card - '.$student['first_name'].' '.$student['last_name'].' ('.date('M Y', strtotime($from)).').docx';
    $doc->output($fname);
} catch (Exception $e) {
    die('Export error: '.$e->getMessage());
}

