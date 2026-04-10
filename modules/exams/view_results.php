<?php
/**
 * View Exam Results
 * MIQT System
 */

require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../includes/functions.php';

$pageTitle = 'View Exam Results';

// Get filter parameters
$exam_id = isset($_GET['exam_id']) ? (int)$_GET['exam_id'] : 0;
$class_id = isset($_GET['class']) ? (int)$_GET['class'] : 0;

// Get all exams for dropdown
$sql = "SELECT e.*, c.class_name FROM exams e
        LEFT JOIN classes c ON e.class_id = c.id
        ORDER BY e.exam_date DESC";
$stmt = $db->prepare($sql);
$stmt->execute();
$exams = $stmt->fetchAll();

// Get classes for filter
$sql = "SELECT * FROM classes WHERE status = 'active' ORDER BY class_name";
$stmt = $db->prepare($sql);
$stmt->execute();
$classes = $stmt->fetchAll();

// Get exam details and results
if ($exam_id > 0) {
    $sql = "SELECT e.*, c.class_name FROM exams e
            LEFT JOIN classes c ON e.class_id = c.id
            WHERE e.id = ?";
    $stmt = $db->prepare($sql);
    $stmt->execute([$exam_id]);
    $exam = $stmt->fetch();

    if ($exam) {
        // Get results
        $sql = "SELECT er.*, s.student_id, s.first_name, s.last_name, c.class_name,
                       e.total_marks, e.passing_marks,
                       CASE WHEN e.total_marks IS NOT NULL AND e.total_marks > 0 AND er.obtained_marks IS NOT NULL
                            THEN (er.obtained_marks / e.total_marks) * 100 ELSE NULL END AS calc_percentage,
                       CASE WHEN e.passing_marks IS NOT NULL AND e.total_marks IS NOT NULL AND e.total_marks > 0 AND er.obtained_marks IS NOT NULL
                            THEN CASE WHEN er.obtained_marks >= e.passing_marks THEN 'pass' ELSE 'fail' END
                            ELSE NULL END AS calc_status
                FROM exam_results er
                JOIN students s ON er.student_id = s.id
                LEFT JOIN classes c ON s.class_id = c.id
                JOIN exams e ON e.id = er.exam_id
                WHERE er.exam_id = ?
                ORDER BY calc_percentage DESC, s.first_name";
        $stmt = $db->prepare($sql);
        $stmt->execute([$exam_id]);
        $results = $stmt->fetchAll();

        // Fallback-fill percentage/grade/status if columns are missing in DB
        for ($i = 0; $i < count($results); $i++) {
            if (!isset($results[$i]['percentage']) || $results[$i]['percentage'] === null || $results[$i]['percentage'] === '') {
                $results[$i]['percentage'] = $results[$i]['calc_percentage'];
            }
            if ((!isset($results[$i]['grade']) || $results[$i]['grade'] === null || $results[$i]['grade'] === '') && $results[$i]['percentage'] !== null) {
                $results[$i]['grade'] = calculateGrade((float)$results[$i]['percentage']);
            }
            if (!isset($results[$i]['status']) || $results[$i]['status'] === null || $results[$i]['status'] === '') {
                $results[$i]['status'] = $results[$i]['calc_status'];
            }
        }

        // Calculate statistics
        $total_students = count($results);
        $passed = 0; $failed = 0; $countWithPct = 0; $total_percentage = 0;

        foreach ($results as $result) {
            if (isset($result['status']) && $result['status'] !== null && $result['status'] !== '') {
                if ($result['status'] == 'pass') $passed++; else $failed++;
            }
            if (isset($result['percentage']) && $result['percentage'] !== null && $result['percentage'] !== '') {
                $total_percentage += (float)$result['percentage'];
                $countWithPct++;
            }
        }

        $average_percentage = $countWithPct > 0 ? round($total_percentage / $countWithPct, 2) : 0;
        $evaluated = $passed + $failed;
        $pass_rate = $evaluated > 0 ? round(($passed / $evaluated) * 100, 2) : 0;
    }
}

require_once '../../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title"><i class="fas fa-chart-bar"></i> View Exam Results</h1>
    <p class="subtitle">View and analyze examination results</p>
</div>

<!-- Filters -->
<div class="dashboard-card mb-20">
    <div class="card-body">
        <form method="GET" action="" class="form-row">
            <div class="form-group">
                <label for="exam_id">Select Exam</label>
                <select name="exam_id" id="exam_id" class="form-control" onchange="this.form.submit()">
                    <option value="">Choose an exam</option>
                    <?php foreach ($exams as $e): ?>
                    <option value="<?php echo $e['id']; ?>" <?php echo ($exam_id == $e['id']) ? 'selected' : ''; ?>>
                        <?php echo ($e['exam_title'] ?? $e['exam_name']) . ' - ' . formatDate($e['exam_date']); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>&nbsp;</label>
                <a href="view_results.php" class="btn btn-secondary">
                    <i class="fas fa-redo"></i> Reset
                </a>
            </div>
        </form>
    </div>
</div>

<?php if (isset($exam) && isset($results)): ?>

<!-- Statistics -->
<div class="stats-grid mb-20">
    <div class="stat-card stat-primary">
        <div class="stat-icon"><i class="fas fa-users"></i></div>
        <div class="stat-info">
            <h3><?php echo $total_students; ?></h3>
            <p>Total Students</p>
        </div>
    </div>

    <div class="stat-card stat-success">
        <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
        <div class="stat-info">
            <h3><?php echo $passed; ?></h3>
            <p>Passed</p>
        </div>
    </div>

    <div class="stat-card stat-danger">
        <div class="stat-icon"><i class="fas fa-times-circle"></i></div>
        <div class="stat-info">
            <h3><?php echo $failed; ?></h3>
            <p>Failed</p>
        </div>
    </div>

    <div class="stat-card stat-info">
        <div class="stat-icon"><i class="fas fa-percentage"></i></div>
        <div class="stat-info">
            <h3><?php echo $average_percentage; ?>%</h3>
            <p>Average Percentage</p>
        </div>
    </div>
</div>

<!-- Exam Details -->
<div class="dashboard-card mb-20">
    <div class="card-header">
        <h3><i class="fas fa-info-circle"></i> Exam Details</h3>
    </div>
    <div class="card-body">
        <div class="form-row">
            <div class="form-group">
                <strong>Exam Name:</strong> <?php echo ($exam['exam_title'] ?? $exam['exam_name']); ?>
            </div>
            <div class="form-group">
                <strong>Class:</strong> <?php echo $exam['class_name'] ?? 'All Classes'; ?>
            </div>
            <div class="form-group">
                <strong>Date:</strong> <?php echo formatDate($exam['exam_date']); ?>
            </div>
            <div class="form-group">
                <strong>Total Marks:</strong> <?php echo ($exam['total_marks'] === null || $exam['total_marks'] === '') ? 'N/A' : $exam['total_marks']; ?>
            </div>
            <div class="form-group">
                <strong>Passing Marks:</strong> <?php echo ($exam['passing_marks'] === null || $exam['passing_marks'] === '') ? 'N/A' : $exam['passing_marks']; ?>
            </div>
            <div class="form-group">
                <strong>Pass Rate:</strong> <?php echo $pass_rate; ?>%
            </div>
        </div>
    </div>
</div>

<!-- Results Table -->
<div class="dashboard-card">
    <div class="card-header">
        <h3><i class="fas fa-table"></i> Student Results</h3>
    </div>
    <div class="card-body">
        <?php if (count($results) > 0): ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Rank</th>
                        <th>Student ID</th>
                        <th>Name</th>
                        <th>Class</th>
                        <th>Obtained Marks</th>
                        <th>Total Marks</th>
                        <th>Percentage</th>
                        <th>Grade</th>
                        <th>Status</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $rank = 1;
                    foreach ($results as $result):
                    ?>
                    <tr>
                        <td><strong><?php echo $rank++; ?></strong></td>
                        <td><?php echo $result['student_id']; ?></td>
                        <td><?php echo $result['first_name'] . ' ' . $result['last_name']; ?></td>
                        <td><?php echo $result['class_name'] ?? 'N/A'; ?></td>
                        <td><?php echo ($result['obtained_marks'] === null || $result['obtained_marks'] === '') ? '-' : $result['obtained_marks']; ?></td>
                        <td><?php echo ($exam['total_marks'] === null || $exam['total_marks'] === '') ? 'N/A' : $exam['total_marks']; ?></td>
                        <td><?php echo (isset($result['percentage']) && $result['percentage'] !== null && $result['percentage'] !== '') ? (round($result['percentage'], 2) . '%') : 'N/A'; ?></td>
                        <td>
                            <span class="badge badge-primary"><?php echo ($result['grade'] ?? 'N/A'); ?></span>
                        </td>
                        <td>
                            <?php if (isset($result['status']) && $result['status'] !== null && $result['status'] !== ''): ?>
                            <span class="badge <?php echo ($result['status'] == 'pass') ? 'badge-success' : 'badge-danger'; ?>">
                                <?php echo ucfirst($result['status']); ?>
                            </span>
                            <?php else: ?>
                            <span class="badge badge-secondary">N/A</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo $result['remarks'] ?: '-'; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="form-group mt-20">
            <a href="result_cards.php?exam_id=<?php echo $exam_id; ?>" class="btn btn-primary" target="_blank">
                <i class="fas fa-print"></i> Print Result Cards
            </a>
            <a href="add_results.php?exam_id=<?php echo $exam_id; ?>" class="btn btn-secondary">
                <i class="fas fa-edit"></i> Edit Results
            </a>
        </div>

        <?php else: ?>
        <p class="text-center text-muted">No results added yet for this exam</p>
        <div class="text-center mt-20">
            <a href="add_results.php?exam_id=<?php echo $exam_id; ?>" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add Results
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php else: ?>
<div class="dashboard-card">
    <div class="card-body">
        <p class="text-center text-muted">Please select an exam to view results</p>
    </div>
</div>
<?php endif; ?>

<?php require_once '../../includes/footer.php'; ?>
