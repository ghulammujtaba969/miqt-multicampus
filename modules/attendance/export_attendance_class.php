<?php
/**
 * Export Class Wise Attendance Report (CSV)
 * MIQT System
 */

require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../includes/functions.php';

if (!hasPermission(['principal', 'vice_principal', 'coordinator'])) {
    http_response_code(403);
    echo 'Forbidden';
    exit;
}

$date_from = isset($_GET['date_from']) ? sanitize($_GET['date_from']) : date('Y-m-01');
$date_to = isset($_GET['date_to']) ? sanitize($_GET['date_to']) : date('Y-m-d');
$class_id = isset($_GET['class']) ? (int)$_GET['class'] : 0;

$sql = "SELECT sa.attendance_date, c.class_name, s.student_id, s.first_name, s.last_name, sa.status
        FROM student_attendance sa
        JOIN students s ON sa.student_id = s.id
        LEFT JOIN classes c ON s.class_id = c.id
        WHERE sa.attendance_date BETWEEN ? AND ?";
$params = [$date_from, $date_to];
if ($class_id > 0) {
    $sql .= " AND c.id = ?";
    $params[] = $class_id;
}
$sql .= " ORDER BY sa.attendance_date DESC, c.class_name, s.last_name";

$stmt = $db->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();

$filename = 'attendance_class_' . $date_from . '_to_' . $date_to . '.csv';
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
$output = fopen('php://output', 'w');
fputcsv($output, ['Date', 'Class', 'Student ID', 'Name', 'Status']);
foreach ($rows as $r) {
    $name = trim($r['first_name'] . ' ' . $r['last_name']);
    fputcsv($output, [$r['attendance_date'], $r['class_name'] ?? '', $r['student_id'], $name, $r['status']]);
}
fclose($output);
exit;
