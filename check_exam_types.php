<?php
/**
 * Diagnostic Script - Check Exam Types
 */

require_once 'config/config.php';
require_once 'config/database.php';

echo "<h2>Checking Exam Types Table...</h2>";

try {
    // Check if table exists
    $sql = "SHOW TABLES LIKE 'exam_types'";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $table_exists = $stmt->fetch();

    if ($table_exists) {
        echo "<p style='color: green;'>✓ Table 'exam_types' exists</p>";

        // Check records
        $sql = "SELECT * FROM exam_types";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $exam_types = $stmt->fetchAll();

        echo "<p><strong>Total Exam Types:</strong> " . count($exam_types) . "</p>";

        if (count($exam_types) > 0) {
            echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
            echo "<tr><th>ID</th><th>Exam Name</th><th>Exam Type</th><th>Status</th></tr>";
            foreach ($exam_types as $type) {
                echo "<tr>";
                echo "<td>" . $type['id'] . "</td>";
                echo "<td>" . $type['exam_name'] . "</td>";
                echo "<td>" . $type['exam_type'] . "</td>";
                echo "<td>" . $type['status'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";

            echo "<p style='color: green;'>✓ Exam types found! The dropdown should work now.</p>";
        } else {
            echo "<p style='color: red;'>✗ No exam types found in the database!</p>";
            echo "<h3>Solution: Run this SQL query in phpMyAdmin:</h3>";
            echo "<pre style='background: #f5f5f5; padding: 15px; border: 1px solid #ddd;'>";
            echo "INSERT INTO `exam_types` (`exam_name`, `exam_type`, `description`, `status`) VALUES
('Monthly Test', 'monthly', 'Monthly evaluation test', 'active'),
('Quarterly Examination', 'quarterly', 'Quarterly examination', 'active'),
('Half Yearly Examination', 'half_yearly', 'Half yearly examination', 'active'),
('Annual Examination', 'annual', 'Annual final examination', 'active'),
('Special Test', 'special', 'Special evaluation test', 'active');";
            echo "</pre>";
        }
    } else {
        echo "<p style='color: red;'>✗ Table 'exam_types' does not exist!</p>";
        echo "<p>Please re-import the database from db/miqt_database.sql</p>";
    }

} catch (PDOException $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>

<hr>
<p><a href="modules/exams/manage_exams.php">← Back to Manage Exams</a></p>
