<?php
/**
 * Bulk Assign School Classes
 * MIQT System
 */

require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../includes/functions.php';

if (!hasPermission(['principal', 'vice_principal', 'coordinator'])) {
    setFlash('danger', 'You do not have permission to access this page');
    redirect(SITE_URL . '/modules/dashboard/index.php');
}

$pageTitle = 'Bulk Assign School Classes';
$error = '';

$classesStmt = $db->prepare("SELECT id, class_name FROM classes WHERE status = 'active' ORDER BY class_name");
$classesStmt->execute();
$classes = $classesStmt->fetchAll();

$classMap = [];
foreach ($classes as $class) {
    $classMap[(int) $class['id']] = $class['class_name'];
}

$allowedAdvancedFields = [
    'student_id' => 'Student ID',
    'admission_no' => 'Admission No',
    'first_name' => 'First Name',
    'last_name' => 'Last Name',
    'father_name' => 'Father Name',
    'cnic_bform' => 'CNIC / B-Form',
    'gender' => 'Gender',
    'student_type' => 'Student Type',
    'admission_date' => 'Admission Date',
    'guardian_name' => 'Guardian Name',
    'guardian_phone' => 'Guardian Phone',
    'phone' => 'Student Phone',
    'address' => 'Address',
    'city' => 'City',
    'previous_education' => 'Previous Education',
    'medical_info' => 'Medical Info',
    'status' => 'Status',
    'mother_name' => 'Mother Name',
    'date_of_admission' => 'Date of Admission',
    'date_of_leaving' => 'Date of Leaving',
    'reason_of_leaving' => 'Reason of Leaving',
    'father_profession' => 'Father Profession',
    'father_cnic' => 'Father / Guardian CNIC',
    'admission_challan_no' => 'Admission Challan No',
    'guardian_phone_2' => 'Guardian Phone 2',
    'whatsapp_no' => 'WhatsApp No',
    'class_status' => 'Class Status',
    'previous_school_class' => 'Previous School Class',
    'current_school_class' => 'Current School Class'
];

$allowedOperators = [
    'contains' => 'Contains',
    'exact' => 'Exact Match',
    'starts_with' => 'Starts With',
    'empty' => 'Is Empty',
    'not_empty' => 'Is Not Empty'
];

function getBulkSchoolRuleFromRequest($index, $allowedFields, $allowedOperators) {
    $fieldKey = 'adv_field_' . $index;
    $operatorKey = 'adv_operator_' . $index;
    $valueKey = 'adv_value_' . $index;

    $field = isset($_GET[$fieldKey]) ? sanitize($_GET[$fieldKey]) : '';
    $operator = isset($_GET[$operatorKey]) ? sanitize($_GET[$operatorKey]) : 'contains';
    $value = isset($_GET[$valueKey]) ? trim(sanitize($_GET[$valueKey])) : '';

    if (!isset($allowedFields[$field])) {
        $field = '';
    }

    if (!isset($allowedOperators[$operator])) {
        $operator = 'contains';
    }

    return [
        'field' => $field,
        'operator' => $operator,
        'value' => $value
    ];
}

function appendBulkSchoolRule(&$whereParts, &$params, $rule, $allowedFields) {
    if (empty($rule['field']) || !isset($allowedFields[$rule['field']])) {
        return false;
    }

    $column = 's.' . $rule['field'];

    switch ($rule['operator']) {
        case 'empty':
            $whereParts[] = "($column IS NULL OR TRIM(CAST($column AS CHAR)) = '')";
            return true;
        case 'not_empty':
            $whereParts[] = "($column IS NOT NULL AND TRIM(CAST($column AS CHAR)) <> '')";
            return true;
        case 'exact':
            if ($rule['value'] === '') {
                return false;
            }
            $whereParts[] = "CAST($column AS CHAR) = ?";
            $params[] = $rule['value'];
            return true;
        case 'starts_with':
            if ($rule['value'] === '') {
                return false;
            }
            $whereParts[] = "CAST($column AS CHAR) LIKE ?";
            $params[] = $rule['value'] . '%';
            return true;
        case 'contains':
        default:
            if ($rule['value'] === '') {
                return false;
            }
            $whereParts[] = "CAST($column AS CHAR) LIKE ?";
            $params[] = '%' . $rule['value'] . '%';
            return true;
    }
}

$search = isset($_GET['search']) ? trim(sanitize($_GET['search'])) : '';
$currentSchoolClassFilter = isset($_GET['current_school_class']) ? (int) $_GET['current_school_class'] : 0;
$actualClassFilter = isset($_GET['class']) ? (int) $_GET['class'] : 0;
$statusFilter = isset($_GET['status']) ? sanitize($_GET['status']) : '';
$genderFilter = isset($_GET['gender']) ? sanitize($_GET['gender']) : '';
$studentTypeFilter = isset($_GET['student_type']) ? sanitize($_GET['student_type']) : '';
$unassignedOnly = isset($_GET['unassigned_only']) ? '1' : '';
$showAdvanced = isset($_GET['show_advanced']) ? '1' : '';
$advancedLogic = isset($_GET['advanced_logic']) ? strtoupper(sanitize($_GET['advanced_logic'])) : 'AND';
$advancedLogic = in_array($advancedLogic, ['AND', 'OR'], true) ? $advancedLogic : 'AND';

$ruleOne = getBulkSchoolRuleFromRequest(1, $allowedAdvancedFields, $allowedOperators);
$ruleTwo = getBulkSchoolRuleFromRequest(2, $allowedAdvancedFields, $allowedOperators);

if ($ruleOne['field'] || $ruleTwo['field']) {
    $showAdvanced = '1';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assign_school_class_bulk'])) {
    $assignClassId = isset($_POST['assign_school_class_id']) && $_POST['assign_school_class_id'] !== '' ? (int) $_POST['assign_school_class_id'] : null;
    $selectedStudents = isset($_POST['selected_students']) && is_array($_POST['selected_students']) ? $_POST['selected_students'] : [];
    $selectedStudents = array_values(array_unique(array_filter(array_map('intval', $selectedStudents))));

    if (empty($selectedStudents)) {
        $error = 'Please select at least one student.';
    } elseif ($assignClassId !== null && !isset($classMap[$assignClassId])) {
        $error = 'Selected school class is invalid.';
    } else {
        try {
            $placeholders = implode(',', array_fill(0, count($selectedStudents), '?'));
            $sql = "UPDATE students SET current_school_class = ? WHERE id IN ($placeholders)";
            $params = array_merge([$assignClassId], $selectedStudents);
            $stmt = $db->prepare($sql);
            $stmt->execute($params);

            $assignedClassLabel = $assignClassId === null ? 'Unassigned' : $classMap[$assignClassId];
            logActivity($db, 'Bulk Assign School Classes', 'Students', 'Assigned ' . count($selectedStudents) . ' students to school class: ' . $assignedClassLabel);
            setFlash('success', count($selectedStudents) . ' student(s) school class updated successfully.');
            redirect(SITE_URL . '/modules/students/bulk_assign_school_classes.php?' . http_build_query($_GET));
        } catch (PDOException $e) {
            $error = 'Error assigning school class: ' . $e->getMessage();
        }
    }
}

$whereParts = ['1=1'];
$params = [];

if ($search !== '') {
    $searchTerm = '%' . $search . '%';
    $whereParts[] = "(s.student_id LIKE ? OR s.admission_no LIKE ? OR s.first_name LIKE ? OR s.last_name LIKE ? OR s.father_name LIKE ? OR s.guardian_name LIKE ? OR s.guardian_phone LIKE ? OR s.phone LIKE ? OR s.city LIKE ? OR s.cnic_bform LIKE ?)";
    $params = array_merge($params, array_fill(0, 10, $searchTerm));
}

if ($currentSchoolClassFilter > 0) {
    $whereParts[] = "s.current_school_class = ?";
    $params[] = $currentSchoolClassFilter;
}

if ($actualClassFilter > 0) {
    $whereParts[] = "s.class_id = ?";
    $params[] = $actualClassFilter;
}

if ($statusFilter !== '') {
    $whereParts[] = "s.status = ?";
    $params[] = $statusFilter;
}

if ($genderFilter !== '') {
    $whereParts[] = "s.gender = ?";
    $params[] = $genderFilter;
}

if ($studentTypeFilter !== '') {
    $whereParts[] = "s.student_type = ?";
    $params[] = $studentTypeFilter;
}

if ($unassignedOnly === '1') {
    $whereParts[] = "s.current_school_class IS NULL";
}

$advancedWhereParts = [];
$advancedParams = [];
$hasRuleOne = appendBulkSchoolRule($advancedWhereParts, $advancedParams, $ruleOne, $allowedAdvancedFields);
$hasRuleTwo = appendBulkSchoolRule($advancedWhereParts, $advancedParams, $ruleTwo, $allowedAdvancedFields);

if ($hasRuleOne && $hasRuleTwo) {
    $whereParts[] = '(' . $advancedWhereParts[0] . ' ' . $advancedLogic . ' ' . $advancedWhereParts[1] . ')';
    $params = array_merge($params, $advancedParams);
} elseif ($hasRuleOne || $hasRuleTwo) {
    $whereParts[] = '(' . $advancedWhereParts[0] . ')';
    $params = array_merge($params, $advancedParams);
}

$whereSql = 'WHERE ' . implode(' AND ', $whereParts);

$sql = "SELECT s.*, c.class_name, csc.class_name AS current_school_class_name
        FROM students s
        LEFT JOIN classes c ON c.id = s.class_id
        LEFT JOIN classes csc ON csc.id = s.current_school_class
        $whereSql
        ORDER BY
            CASE WHEN s.current_school_class IS NULL THEN 0 ELSE 1 END,
            csc.class_name ASC,
            s.first_name ASC,
            s.last_name ASC,
            s.id DESC";
$stmt = $db->prepare($sql);
$stmt->execute($params);
$students = $stmt->fetchAll();

require_once '../../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title"><i class="fas fa-school"></i> Bulk Assign School Classes</h1>
    <p class="subtitle">Search students, select multiple records, and assign school classes in one step</p>
</div>

<?php if ($error): ?>
<div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<div class="dashboard-card mb-20">
    <div class="card-body">
        <form method="GET" action="">
            <input type="hidden" name="show_advanced" id="show_advanced" value="<?php echo $showAdvanced; ?>">

            <div class="form-row">
                <div class="form-group">
                    <label for="search">Search Student</label>
                    <input type="text" name="search" id="search" class="form-control"
                           placeholder="ID, admission no, name, father, phone, city"
                           value="<?php echo htmlspecialchars($search); ?>">
                </div>

                <div class="form-group">
                    <label for="current_school_class">Search School Class</label>
                    <select name="current_school_class" id="current_school_class" class="form-control">
                        <option value="">All School Classes</option>
                        <?php foreach ($classes as $class): ?>
                        <option value="<?php echo $class['id']; ?>" <?php echo ($currentSchoolClassFilter === (int) $class['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($class['class_name']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group bulk-assign-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Search
                    </button>
                    <a href="bulk_assign_school_classes.php" class="btn btn-secondary">
                        <i class="fas fa-redo"></i> Reset
                    </a>
                    <button type="button" id="toggleAdvancedSearch" class="btn btn-info">
                        <i class="fas fa-sliders-h"></i> Advanced Search
                    </button>
                </div>
            </div>

            <div id="advancedSearchPanel" class="advanced-search-panel <?php echo ($showAdvanced === '1') ? 'is-visible' : ''; ?>">
                <div class="advanced-search-header">
                    <h3><i class="fas fa-filter"></i> Advanced Search</h3>
                    <p>Filter by student table fields, including missing values or school-class assignment gaps.</p>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="class">Current Class</label>
                        <select name="class" id="class" class="form-control">
                            <option value="">Any Current Class</option>
                            <?php foreach ($classes as $class): ?>
                            <option value="<?php echo $class['id']; ?>" <?php echo ($actualClassFilter === (int) $class['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($class['class_name']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="status">Status</label>
                        <select name="status" id="status" class="form-control">
                            <option value="">Any Status</option>
                            <?php foreach (['active', 'inactive', 'graduated', 'alumni', 'left', 'expelled'] as $status): ?>
                            <option value="<?php echo $status; ?>" <?php echo ($statusFilter === $status) ? 'selected' : ''; ?>>
                                <?php echo ucfirst($status); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="gender">Gender</label>
                        <select name="gender" id="gender" class="form-control">
                            <option value="">Any Gender</option>
                            <option value="male" <?php echo ($genderFilter === 'male') ? 'selected' : ''; ?>>Male</option>
                            <option value="female" <?php echo ($genderFilter === 'female') ? 'selected' : ''; ?>>Female</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="student_type">Student Type</label>
                        <select name="student_type" id="student_type" class="form-control">
                            <option value="">Any Type</option>
                            <?php foreach (['day_scholar', 'boarder', 'border', 'orphan', 'aghosh'] as $type): ?>
                            <option value="<?php echo $type; ?>" <?php echo ($studentTypeFilter === $type) ? 'selected' : ''; ?>>
                                <?php echo ucwords(str_replace('_', ' ', $type)); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group advanced-checkbox-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="unassigned_only" value="1" <?php echo ($unassignedOnly === '1') ? 'checked' : ''; ?>>
                            Show only students with no school class assigned
                        </label>
                    </div>
                </div>

                <div class="advanced-rule-grid">
                    <div class="advanced-rule-card">
                        <h4>Rule 1</h4>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="adv_field_1">Field</label>
                                <select name="adv_field_1" id="adv_field_1" class="form-control">
                                    <option value="">Select Field</option>
                                    <?php foreach ($allowedAdvancedFields as $field => $label): ?>
                                    <option value="<?php echo $field; ?>" <?php echo ($ruleOne['field'] === $field) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($label); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="adv_operator_1">Operator</label>
                                <select name="adv_operator_1" id="adv_operator_1" class="form-control">
                                    <?php foreach ($allowedOperators as $operator => $label): ?>
                                    <option value="<?php echo $operator; ?>" <?php echo ($ruleOne['operator'] === $operator) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($label); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="adv_value_1">Value</label>
                                <input type="text" name="adv_value_1" id="adv_value_1" class="form-control"
                                       placeholder="Leave blank for empty / not empty"
                                       value="<?php echo htmlspecialchars($ruleOne['value']); ?>">
                            </div>
                        </div>
                    </div>

                    <div class="advanced-rule-card">
                        <h4>Rule 2</h4>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="adv_field_2">Field</label>
                                <select name="adv_field_2" id="adv_field_2" class="form-control">
                                    <option value="">Select Field</option>
                                    <?php foreach ($allowedAdvancedFields as $field => $label): ?>
                                    <option value="<?php echo $field; ?>" <?php echo ($ruleTwo['field'] === $field) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($label); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="adv_operator_2">Operator</label>
                                <select name="adv_operator_2" id="adv_operator_2" class="form-control">
                                    <?php foreach ($allowedOperators as $operator => $label): ?>
                                    <option value="<?php echo $operator; ?>" <?php echo ($ruleTwo['operator'] === $operator) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($label); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="adv_value_2">Value</label>
                                <input type="text" name="adv_value_2" id="adv_value_2" class="form-control"
                                       placeholder="Leave blank for empty / not empty"
                                       value="<?php echo htmlspecialchars($ruleTwo['value']); ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="advanced_logic">Combine Rule 1 and Rule 2 With</label>
                        <select name="advanced_logic" id="advanced_logic" class="form-control">
                            <option value="AND" <?php echo ($advancedLogic === 'AND') ? 'selected' : ''; ?>>AND</option>
                            <option value="OR" <?php echo ($advancedLogic === 'OR') ? 'selected' : ''; ?>>OR</option>
                        </select>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="dashboard-card mb-20">
    <div class="card-body">
        <form method="POST" action="">
            <div class="form-row">
                <div class="form-group">
                    <label for="assign_school_class_id">Assign Selected Students To School Class</label>
                    <select name="assign_school_class_id" id="assign_school_class_id" class="form-control" required>
                        <option value="">Select School Class</option>
                        <?php foreach ($classes as $class): ?>
                        <option value="<?php echo $class['id']; ?>">
                            <?php echo htmlspecialchars($class['class_name']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group bulk-assign-actions">
                    <button type="submit" name="assign_school_class_bulk" class="btn btn-success">
                        <i class="fas fa-check-double"></i> Assign Selected Students
                    </button>
                </div>
            </div>

            <div class="bulk-selection-toolbar">
                <label class="checkbox-label">
                    <input type="checkbox" id="selectAllStudents">
                    Select all visible students
                </label>
                <span id="selectedCount">0 selected</span>
                <span class="text-muted"><?php echo count($students); ?> student(s) found</span>
            </div>

            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Select</th>
                            <th>Student ID</th>
                            <th>Admission No</th>
                            <th>Name</th>
                            <th>Father Name</th>
                            <th>Current Class</th>
                            <th>Current School Class</th>
                            <th>Admission Date</th>
                            <th>Guardian</th>
                            <th>Phone</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($students)): ?>
                        <tr>
                            <td colspan="11" class="text-center text-muted">No students found for the current filters.</td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($students as $student): ?>
                        <tr>
                            <td>
                                <input type="checkbox" class="student-checkbox" name="selected_students[]"
                                       value="<?php echo (int) $student['id']; ?>">
                            </td>
                            <td><?php echo htmlspecialchars($student['student_id'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($student['admission_no'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars(trim(($student['first_name'] ?? '') . ' ' . ($student['last_name'] ?? ''))); ?></td>
                            <td><?php echo htmlspecialchars($student['father_name'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($student['class_name'] ?? 'Not Assigned'); ?></td>
                            <td><?php echo htmlspecialchars($student['current_school_class_name'] ?? 'Not Assigned'); ?></td>
                            <td><?php echo !empty($student['admission_date']) ? formatDate($student['admission_date']) : 'Empty'; ?></td>
                            <td><?php echo htmlspecialchars($student['guardian_name'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($student['guardian_phone'] ?: ($student['phone'] ?? '')); ?></td>
                            <td>
                                <span class="badge badge-<?php echo ($student['status'] === 'active') ? 'success' : 'secondary'; ?>">
                                    <?php echo htmlspecialchars(ucfirst($student['status'] ?? '')); ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </form>
    </div>
</div>

<style>
.advanced-search-panel {
    display: none;
    margin-top: 20px;
    padding: 20px;
    border: 1px solid #d8dee8;
    border-radius: 12px;
    background: #f8fbff;
}

.advanced-search-panel.is-visible {
    display: block;
}

.advanced-search-header {
    margin-bottom: 16px;
}

.advanced-search-header h3 {
    margin-bottom: 6px;
}

.advanced-search-header p {
    margin: 0;
    color: #6b7280;
}

.advanced-rule-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 16px;
    margin-bottom: 16px;
}

.advanced-rule-card {
    padding: 16px;
    border-radius: 10px;
    background: #ffffff;
    border: 1px solid #e5e7eb;
}

.advanced-rule-card h4 {
    margin-bottom: 12px;
}

.advanced-checkbox-group {
    display: flex;
    align-items: flex-end;
}

.checkbox-label {
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 500;
}

.bulk-assign-actions {
    display: flex;
    align-items: flex-end;
    gap: 10px;
    flex-wrap: wrap;
}

.bulk-selection-toolbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    margin-bottom: 15px;
    padding: 12px 14px;
    border-radius: 10px;
    background: #f9fafb;
}

@media (max-width: 768px) {
    .bulk-selection-toolbar {
        flex-direction: column;
        align-items: flex-start;
    }
}
</style>

<script>
const advancedButton = document.getElementById('toggleAdvancedSearch');
const advancedPanel = document.getElementById('advancedSearchPanel');
const advancedInput = document.getElementById('show_advanced');
const selectAllCheckbox = document.getElementById('selectAllStudents');
const studentCheckboxes = document.querySelectorAll('.student-checkbox');
const selectedCount = document.getElementById('selectedCount');

function updateSelectedCount() {
    const checked = document.querySelectorAll('.student-checkbox:checked').length;
    selectedCount.textContent = checked + ' selected';
}

advancedButton.addEventListener('click', function () {
    const isVisible = advancedPanel.classList.toggle('is-visible');
    advancedInput.value = isVisible ? '1' : '0';
});

if (selectAllCheckbox) {
    selectAllCheckbox.addEventListener('change', function () {
        studentCheckboxes.forEach(function (checkbox) {
            checkbox.checked = selectAllCheckbox.checked;
        });
        updateSelectedCount();
    });
}

studentCheckboxes.forEach(function (checkbox) {
    checkbox.addEventListener('change', function () {
        const allChecked = document.querySelectorAll('.student-checkbox:checked').length === studentCheckboxes.length && studentCheckboxes.length > 0;
        if (selectAllCheckbox) {
            selectAllCheckbox.checked = allChecked;
        }
        updateSelectedCount();
    });
});

updateSelectedCount();
</script>

<?php require_once '../../includes/footer.php'; ?>
