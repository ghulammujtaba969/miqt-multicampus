<?php
require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../includes/functions.php';
require_once '../../libs/DocxTemplate.php';

$class_id = isset($_GET['class_id']) ? (int)$_GET['class_id'] : 0;
$from = $_GET['from'] ?? date('Y-m-01');
$to = $_GET['to'] ?? date('Y-m-t');
$template = $_GET['template'] ?? (dirname(__DIR__,2) . DIRECTORY_SEPARATOR . 'ماہانہ پراگرس جائزہ.docx');

if ($class_id <= 0) { die('class_id is required'); }

// Class and students
$st = $db->prepare("SELECT class_name FROM classes WHERE id=?");
$st->execute([$class_id]);
$class = $st->fetch();
if (!$class) { die('Class not found'); }

$st = $db->prepare("SELECT id, student_id, first_name, last_name FROM students WHERE class_id=? AND status='active' ORDER BY first_name, last_name");
$st->execute([$class_id]);
$students = $st->fetchAll();

function cnt($db,$t,$sid,$f,$to){$q=$db->prepare("SELECT COUNT(*) c FROM {$t} WHERE student_id=? AND record_date BETWEEN ? AND ?");$q->execute([$sid,$f,$to]);return (int)$q->fetch()['c'];}

// Build OOXML table rows for placeholder {{TABLE_ROWS}}
$rowsXml = '';
foreach ($students as $i => $s) {
    $sab = cnt($db,'sabak_records',$s['id'],$from,$to);
    $sbq = cnt($db,'sabqi_records',$s['id'],$from,$to);
    $mz  = cnt($db,'manzil_records',$s['id'],$from,$to);
    $total = $sab + $sbq + $mz;
    $cells = [
        (string)($i+1),
        $s['first_name'].' '.$s['last_name'],
        $s['student_id'],
        (string)$sab,
        (string)$sbq,
        (string)$mz,
        (string)$total
    ];
    $rowsXml .= tr($cells);
}

// Helper to create table row OOXML
function esc($t){return htmlspecialchars($t, ENT_QUOTES|ENT_SUBSTITUTE, 'UTF-8');}
function tc($text){return '<w:tc><w:p><w:r><w:t>'.esc($text).'</w:t></w:r></w:p></w:tc>';}
function tr(array $cells){ $x='<w:tr>'; foreach($cells as $c){ $x .= tc($c);} return $x.'</w:tr>'; }
// alias for scope
function _tr($cells){ return tr($cells);} 
// rebuild rowsXml with proper function
$rowsXml = '';
foreach ($students as $i => $s) {
    $sab = cnt($db,'sabak_records',$s['id'],$from,$to);
    $sbq = cnt($db,'sabqi_records',$s['id'],$from,$to);
    $mz  = cnt($db,'manzil_records',$s['id'],$from,$to);
    $total = $sab + $sbq + $mz;
    $rowsXml .= _tr([(string)($i+1), $s['first_name'].' '.$s['last_name'], $s['student_id'], (string)$sab, (string)$sbq, (string)$mz, (string)$total]);
}

try {
    $doc = new DocxTemplate($template);
    $doc->replace([
        'CLASS_NAME' => $class['class_name'],
        'PERIOD_FROM' => date('d/m/Y', strtotime($from)),
        'PERIOD_TO' => date('d/m/Y', strtotime($to)),
    ]);
    // Replace table rows marker. Ensure template contains {{TABLE_ROWS}} where the rows must go inside a single-row table.
    $doc->replaceRaw('{{TABLE_ROWS}}', $rowsXml);
    $fname = 'Monthly Progress - '.$class['class_name'].' ('.date('M Y', strtotime($from)).').docx';
    $doc->output($fname);
} catch (Exception $e) {
    die('Export error: '.$e->getMessage());
}
