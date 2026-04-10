<?php
/**
 * Export Students to CSV (Excel-compatible)
 * MIQT System
 */

require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../includes/functions.php';

// Require login
if (!isLoggedIn()) {
    http_response_code(403);
    echo 'Unauthorized';
    exit;
}

// Filters (align with students list)
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$class_filter = isset($_GET['class']) ? sanitize($_GET['class']) : '';
$status = isset($_GET['status']) ? sanitize($_GET['status']) : 'active'; // default to active, use 'all' to export all

$where = 'WHERE 1=1';
$params = [];

if ($status !== 'all' && $status !== '') {
    $where .= ' AND s.status = ?';
    $params[] = $status;
}

if (!empty($search)) {
    $where .= " AND (s.student_id LIKE ? OR s.first_name LIKE ? OR s.last_name LIKE ? OR s.admission_no LIKE ?)";
    $searchTerm = "%$search%";
    $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
}

if (!empty($class_filter)) {
    $where .= ' AND s.class_id = ?';
    $params[] = $class_filter;
}

// Fetch
$sql = "SELECT s.*, c.class_name FROM students s
        LEFT JOIN classes c ON s.class_id = c.id
        $where
        ORDER BY s.created_at DESC";
$stmt = $db->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();

// Output CSV
$filename = 'students_' . date('Ymd_His') . '.csv';
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=' . $filename);

$output = fopen('php://output', 'w');

// Optional BOM to help Excel detect UTF-8
fwrite($output, chr(239) . chr(187) . chr(191));

// Header
fputcsv($output, [
    'Student ID', 'Admission No', 'First Name', 'Last Name', 'Father Name',
    'CNIC/B-Form', 'Date of Birth', 'Gender', 'Student Type', 'Class Name', 'Admission Date',
    'Guardian Name', 'Guardian Phone', 'Phone', 'Address', 'City',
    'Previous Education', 'Medical Info', 'Status'
]);

foreach ($rows as $r) {
    fputcsv($output, [
        $r['student_id'],
        $r['admission_no'],
        $r['first_name'],
        $r['last_name'],
        $r['father_name'],
        $r['cnic_bform'],
        $r['date_of_birth'],
        $r['gender'],
        $r['student_type'],
        $r['class_name'],
        $r['admission_date'],
        $r['guardian_name'],
        $r['guardian_phone'],
        $r['phone'],
        $r['address'],
        $r['city'],
        $r['previous_education'],
        $r['medical_info'],
        $r['status'],
    ]);
}

fclose($output);
exit;

