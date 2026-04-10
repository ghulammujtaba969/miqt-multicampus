<?php
/**
 * Print Result Cards
 * MIQT System
 */

require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../includes/functions.php';

$exam_id = isset($_GET['exam_id']) ? (int)$_GET['exam_id'] : 0;

if (!$exam_id) {
    die('No exam selected');
}

// Get exam details
$sql = "SELECT e.*, c.class_name, et.exam_name AS type_name, et.exam_type AS type_code
        FROM exams e
        LEFT JOIN classes c ON e.class_id = c.id
        LEFT JOIN exam_types et ON e.exam_type_id = et.id
        WHERE e.id = ?";
$stmt = $db->prepare($sql);
$stmt->execute([$exam_id]);
$exam = $stmt->fetch();

if (!$exam) {
    die('Exam not found');
}

// Get results
$sql = "SELECT er.*, s.student_id, s.first_name, s.last_name, s.father_name, s.photo, c.class_name
        FROM exam_results er
        JOIN students s ON er.student_id = s.id
        LEFT JOIN classes c ON s.class_id = c.id
        WHERE er.exam_id = ?
        ORDER BY s.first_name, s.last_name";
$stmt = $db->prepare($sql);
$stmt->execute([$exam_id]);
$results = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Result Cards - <?php echo ($exam['exam_title'] ?? ($exam['exam_name'] ?? 'Exam')); ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
        }

        .result-card {
            width: 21cm;
            min-height: 29.7cm;
            background: white;
            margin: 20px auto;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            page-break-after: always;
        }

        .result-card:last-child {
            page-break-after: auto;
        }

        .header {
            text-align: center;
            border-bottom: 3px solid #2e7d32;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .header h1 {
            color: #2e7d32;
            font-size: 28px;
            margin-bottom: 5px;
        }

        .header h2 {
            color: #555;
            font-size: 18px;
            font-weight: normal;
        }

        .student-info {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 20px;
            margin-bottom: 30px;
        }

        .student-details {
            display: grid;
            grid-template-columns: 150px 1fr;
            gap: 10px;
        }

        .student-details div {
            padding: 8px 0;
        }

        .label {
            font-weight: bold;
            color: #333;
        }

        .value {
            color: #555;
        }

        .student-photo {
            width: 120px;
            height: 120px;
            border: 3px solid #2e7d32;
            border-radius: 5px;
            object-fit: cover;
        }

        .result-table {
            width: 100%;
            border-collapse: collapse;
            margin: 30px 0;
        }

        .result-table th,
        .result-table td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
        }

        .result-table th {
            background: #2e7d32;
            color: white;
            font-weight: bold;
        }

        .result-table tr:nth-child(even) {
            background: #f9f9f9;
        }

        .result-summary {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin: 30px 0;
            padding: 20px;
            background: #f5f5f5;
            border-radius: 5px;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            padding: 10px;
            background: white;
            border-radius: 5px;
        }

        .summary-label {
            font-weight: bold;
            color: #333;
        }

        .summary-value {
            color: #2e7d32;
            font-weight: bold;
            font-size: 18px;
        }

        .pass {
            color: #4caf50;
            font-weight: bold;
        }

        .fail {
            color: #f44336;
            font-weight: bold;
        }

        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 2px solid #ddd;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            text-align: center;
        }

        .signature {
            padding-top: 40px;
            border-top: 1px solid #333;
        }

        @media print {
            body {
                background: white;
            }

            .result-card {
                box-shadow: none;
                margin: 0;
                page-break-after: always;
            }

            .no-print {
                display: none;
            }
        }

        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 30px;
            background: #2e7d32;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }

        .print-button:hover {
            background: #1b5e20;
        }
    </style>
</head>
<body>
    <button class="print-button no-print" onclick="window.print()">
        Print Result Cards
    </button>

    <?php foreach ($results as $result): ?>
    <div class="result-card">
        <div class="header">
            <h1>MINHAJ INSTITUTE OF QIRAT & TAJWEED</h1>
            <h2>Result Card - <?php echo ($exam['exam_title'] ?? $exam['exam_name']); ?></h2>
            <p><?php echo formatDate($exam['exam_date']); ?></p>
        </div>

        <div class="student-info">
            <div class="student-details">
                <div class="label">Student Name:</div>
                <div class="value"><?php echo $result['first_name'] . ' ' . $result['last_name']; ?></div>

                <div class="label">Father Name:</div>
                <div class="value"><?php echo $result['father_name']; ?></div>

                <div class="label">Student ID:</div>
                <div class="value"><?php echo $result['student_id']; ?></div>

                <div class="label">Class:</div>
                <div class="value"><?php echo $result['class_name'] ?? 'N/A'; ?></div>
            </div>

            <?php if ($result['photo']): ?>
            <div>
                <img src="<?php echo SITE_URL . '/uploads/photos/' . $result['photo']; ?>"
                     alt="Student Photo" class="student-photo">
            </div>
            <?php endif; ?>
        </div>

        <table class="result-table">
            <thead>
                <tr>
                    <th>Exam Details</th>
                    <th>Value</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Exam Type</td>
                    <td>
                        <?php
                        $typeName = isset($exam['type_name']) && $exam['type_name'] !== null && $exam['type_name'] !== ''
                            ? $exam['type_name'] : null;
                        $typeCode = isset($exam['type_code']) && $exam['type_code'] !== null && $exam['type_code'] !== ''
                            ? ucfirst(str_replace('_', ' ', $exam['type_code'])) : null;
                        echo $typeName ?? ($typeCode ?? 'N/A');
                        ?>
                    </td>
                </tr>
                <tr>
                    <td>Total Marks</td>
                    <td><?php echo ($exam['total_marks'] === null || $exam['total_marks'] === '') ? 'N/A' : $exam['total_marks']; ?></td>
                </tr>
                <tr>
                    <td>Passing Marks</td>
                    <td><?php echo ($exam['passing_marks'] === null || $exam['passing_marks'] === '') ? 'N/A' : $exam['passing_marks']; ?></td>
                </tr>
                <tr>
                    <td><strong>Obtained Marks</strong></td>
                    <td><strong><?php echo $result['obtained_marks']; ?></strong></td>
                </tr>
            </tbody>
        </table>

        <div class="result-summary">
            <div class="summary-item">
                <span class="summary-label">Percentage:</span>
                <span class="summary-value"><?php echo (isset($result['percentage']) && $result['percentage'] !== null && $result['percentage'] !== '') ? (round($result['percentage'], 2) . '%') : 'N/A'; ?></span>
            </div>

            <div class="summary-item">
                <span class="summary-label">Grade:</span>
                <span class="summary-value"><?php echo $result['grade']; ?></span>
            </div>

            <div class="summary-item">
                <span class="summary-label">Status:</span>
                <?php if (isset($result['status']) && $result['status'] !== null && $result['status'] !== ''): ?>
                <span class="summary-value <?php echo ($result['status'] == 'pass') ? 'pass' : 'fail'; ?>"><?php echo strtoupper($result['status']); ?></span>
                <?php else: ?>
                <span class="summary-value">N/A</span>
                <?php endif; ?>
            </div>

            <div class="summary-item">
                <span class="summary-label">Remarks:</span>
                <span class="summary-value" style="font-size: 14px;">
                    <?php echo $result['remarks'] ?: 'N/A'; ?>
                </span>
            </div>
        </div>

        <div class="footer">
            <div>
                <div class="signature">Class Teacher</div>
            </div>
            <div>
                <div class="signature">Principal</div>
            </div>
            <div>
                <div class="signature">Parent Signature</div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>

    <?php if (count($results) == 0): ?>
    <div class="result-card">
        <p style="text-align: center; color: #999; padding: 50px;">
            No results available for this exam
        </p>
    </div>
    <?php endif; ?>
</body>
</html>
