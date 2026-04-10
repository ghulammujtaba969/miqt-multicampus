<?php
/**
 * Download sample CSV for importing students
 * MIQT System
 */

require_once '../../config/config.php';
require_once '../../includes/functions.php';

if (!isLoggedIn()) {
    http_response_code(403);
    echo 'Unauthorized';
    exit;
}

$filename = 'students_import_sample.csv';
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=' . $filename);

$out = fopen('php://output', 'w');
// BOM for Excel
fwrite($out, chr(239) . chr(187) . chr(191));

// Expected headers
$headers = [
    'Admission No', 'First Name', 'Last Name', 'Father Name', 'CNIC/B-Form',
    'Date of Birth (YYYY-MM-DD)', 'Gender (male/female)', 'Student Type (optional)', 'Class Name (optional)',
    'Admission Date (YYYY-MM-DD)', 'Guardian Name', 'Guardian Phone', 'Phone',
    'Address', 'City', 'Previous Education', 'Medical Info', 'Status (active/inactive/alumni/graduated/left/expelled)'
];
fputcsv($out, $headers);

// One example row
fputcsv($out, [
    'ADM-0001', 'Ahmed', 'Khan', 'Mohammad Khan', '12345-6789012-3',
    '2010-05-12', 'male', 'day_scholar', 'Class A', '2024-08-15', 'Khan Sb', '03001234567',
    '03007654321', 'Street 1, City', 'Lahore', 'Hifz basics', 'N/A', 'active'
]);

fclose($out);
exit;

