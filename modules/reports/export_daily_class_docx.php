<?php
require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../includes/functions.php';
require_once '../../libs/DocxTemplate.php';

$class_id = isset($_GET['class_id']) ? (int)$_GET['class_id'] : 0;
$date = $_GET['date'] ?? date('Y-m-d');
$template = $_GET['template'] ?? (dirname(__DIR__,2) . DIRECTORY_SEPARATOR . 'Abubakar House.docx');

if ($class_id <= 0) { die('class_id is required'); }

// Class and students
$st = $db->prepare("SELECT class_name FROM classes WHERE id=?");
$st->execute([$class_id]);
$class = $st->fetch();
if (!$class) { die('Class not found'); }

$st = $db->prepare("SELECT id, student_id, first_name, last_name, father_name, admission_date FROM students WHERE class_id=? AND status='active' ORDER BY first_name, last_name");
$st->execute([$class_id]);
$students = $st->fetchAll();

// For each student, get records on the date
function latest($v){ return $v ?: '—'; }
function esc($t){return htmlspecialchars($t, ENT_QUOTES|ENT_SUBSTITUTE, 'UTF-8');}
function tc($text){return '<w:tc><w:p><w:r><w:t>'.esc($text).'</w:t></w:r></w:p></w:tc>';}
function tr(array $cells){ $x='<w:tr>'; foreach($cells as $c){ $x .= tc($c);} return $x.'</w:tr>'; }

$rowsXml = '';
foreach ($students as $i => $s) {
    // Sabak
    $sab = $db->prepare("SELECT * FROM sabak_records WHERE student_id=? AND record_date=? ORDER BY id DESC LIMIT 1");
    $sab->execute([$s['id'],$date]);
    $r1 = $sab->fetch();
    // Sabqi
    $sbq = $db->prepare("SELECT * FROM sabqi_records WHERE student_id=? AND record_date=? ORDER BY id DESC LIMIT 1");
    $sbq->execute([$s['id'],$date]);
    $r2 = $sbq->fetch();
    // Manzil
    $mz = $db->prepare("SELECT * FROM manzil_records WHERE student_id=? AND record_date=? ORDER BY id DESC LIMIT 1");
    $mz->execute([$s['id'],$date]);
    $r3 = $mz->fetch();

    $cells = [
        (string)($i+1),
        $s['first_name'].' '.$s['last_name'],
        $s['father_name'],
        date('d/m/Y', strtotime($s['admission_date'])),
        $r1 ? ($r1['juz_number'] ?? '') : '—',
        $r1 ? ($r1['surah_name'] ?? '') : '—',
        $r1 ? (trim(($r1['page_from']??'').(($r1['page_to']??'') && $r1['page_to']!=$r1['page_from']?('-'.$r1['page_to']):''))) : '—',
        $r1 && $r1['lines_memorized'] !== null ? (string)$r1['lines_memorized'] : '—',
        $r2 ? ($r2['juz_number'] ?? '—') : '—',
        $r2 ? ($r2['remarks'] ?? '—') : '—',
        $r3 ? ($r3['juz_from'] ?? '—') : '—',
        $r3 ? ($r3['remarks'] ?? '—') : '—',
    ];
    $rowsXml .= tr($cells);
}

try {
    $doc = new DocxTemplate($template);
    $doc->replace([
        'CLASS_NAME' => $class['class_name'],
        'REPORT_DATE' => date('d/m/Y', strtotime($date)),
    ]);
    $doc->replaceRaw('{{TABLE_ROWS}}', $rowsXml);
    $fname = 'Daily Progress - '.$class['class_name'].' ('.date('d M Y', strtotime($date)).').docx';
    $doc->output($fname);
} catch (Exception $e) {
    die('Export error: '.$e->getMessage());
}

