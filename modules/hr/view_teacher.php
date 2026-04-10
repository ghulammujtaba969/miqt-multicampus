<?php
/**
 * View Teacher Profile
 * MIQT System
 */

require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../includes/functions.php';

if (!hasPermission(['principal', 'vice_principal', 'coordinator'])) {
    setFlash('danger', 'You do not have permission to access this page');
    redirect(SITE_URL . '/modules/dashboard/index.php');
}

$pageTitle = 'Teacher Profile';

$teacher_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get teacher details
$sql = "SELECT t.*, u.username FROM teachers t
        LEFT JOIN users u ON t.user_id = u.id
        WHERE t.id = ?";
$stmt = $db->prepare($sql);
$stmt->execute([$teacher_id]);
$teacher = $stmt->fetch();

if (!$teacher) {
    setFlash('danger', 'Teacher not found');
    redirect(SITE_URL . '/modules/hr/teachers.php');
}

require_once '../../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title"><i class="fas fa-chalkboard-teacher"></i> Teacher Profile</h1>
    <p class="subtitle"><?php echo $teacher['teacher_id']; ?></p>
</div>

<div class="dashboard-grid mb-20">
    <div class="dashboard-card">
        <div class="card-body" style="text-align: center;">
            <?php if ($teacher['photo']): ?>
            <img src="<?php echo SITE_URL . '/uploads/photos/' . $teacher['photo']; ?>"
                 alt="Photo" style="width: 150px; height: 150px; border-radius: 50%; object-fit: cover; margin-bottom: 20px;">
            <?php else: ?>
            <i class="fas fa-user-circle" style="font-size: 150px; color: #ccc; margin-bottom: 20px;"></i>
            <?php endif; ?>

            <h2><?php echo $teacher['first_name'] . ' ' . $teacher['last_name']; ?></h2>
            <p class="text-muted"><?php echo $teacher['teacher_id']; ?></p>
            <p><strong><?php echo $teacher['specialization'] ?? 'Teacher'; ?></strong></p>

            <div style="margin-top: 20px;">
                <a href="edit_teacher.php?id=<?php echo $teacher['id']; ?>" class="btn btn-primary">
                    <i class="fas fa-edit"></i> Edit Profile
                </a>
                <a href="delete_teacher.php?id=<?php echo $teacher['id']; ?>" class="btn btn-danger"
                   onclick="return confirm('Are you sure you want to delete this teacher?')">
                    <i class="fas fa-trash"></i> Delete
                </a>
                <a href="teachers.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
        </div>
    </div>

    <div class="dashboard-card">
        <div class="card-header">
            <h3><i class="fas fa-info-circle"></i> Basic Information</h3>
        </div>
        <div class="card-body">
            <table class="table">
                <tr>
                    <th>CNIC:</th>
                    <td><?php echo $teacher['cnic']; ?></td>
                </tr>
                <tr>
                    <th>Father Name:</th>
                    <td><?php echo $teacher['father_name'] ?: 'N/A'; ?></td>
                </tr>
                <tr>
                    <th>Date of Birth:</th>
                    <td><?php echo formatDate($teacher['date_of_birth']); ?></td>
                </tr>
                <tr>
                    <th>Gender:</th>
                    <td><?php echo ucfirst($teacher['gender']); ?></td>
                </tr>
                <tr>
                    <th>Status:</th>
                    <td>
                        <?php
                        $badges = [
                            'active' => 'badge-success',
                            'inactive' => 'badge-secondary',
                            'on_leave' => 'badge-warning'
                        ];
                        $badge = $badges[$teacher['status']] ?? 'badge-secondary';
                        ?>
                        <span class="badge <?php echo $badge; ?>">
                            <?php echo ucfirst(str_replace('_', ' ', $teacher['status'])); ?>
                        </span>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>

<div class="dashboard-grid">
    <div class="dashboard-card">
        <div class="card-header">
            <h3><i class="fas fa-phone"></i> Contact Information</h3>
        </div>
        <div class="card-body">
            <table class="table">
                <tr>
                    <th>Phone:</th>
                    <td><?php echo $teacher['phone']; ?></td>
                </tr>
                <tr>
                    <th>Email:</th>
                    <td><?php echo $teacher['email'] ?: 'N/A'; ?></td>
                </tr>
                <tr>
                    <th>Address:</th>
                    <td><?php echo $teacher['address'] ?: 'N/A'; ?></td>
                </tr>
                <tr>
                    <th>City:</th>
                    <td><?php echo $teacher['city'] ?: 'N/A'; ?></td>
                </tr>
            </table>
        </div>
    </div>

    <div class="dashboard-card">
        <div class="card-header">
            <h3><i class="fas fa-graduation-cap"></i> Professional Information</h3>
        </div>
        <div class="card-body">
            <table class="table">
                <tr>
                    <th>Qualification:</th>
                    <td><?php echo $teacher['qualification']; ?></td>
                </tr>
                <tr>
                    <th>Specialization:</th>
                    <td><?php echo $teacher['specialization'] ?: 'N/A'; ?></td>
                </tr>
                <tr>
                    <th>Joining Date:</th>
                    <td><?php echo formatDate($teacher['joining_date']); ?></td>
                </tr>
                <tr>
                    <th>Employment Type:</th>
                    <td><?php echo ucfirst(str_replace('_', ' ', $teacher['employment_type'])); ?></td>
                </tr>
                <?php if ($teacher['salary']): ?>
                <tr>
                    <th>Salary:</th>
                    <td>Rs. <?php echo number_format($teacher['salary'], 2); ?></td>
                </tr>
                <?php endif; ?>
            </table>
        </div>
    </div>

    <div class="dashboard-card">
        <div class="card-header">
            <h3><i class="fas fa-user-friends"></i> Reference Information</h3>
        </div>
        <div class="card-body">
            <table class="table">
                <tr>
                    <th>Reference Name:</th>
                    <td><?php echo !empty($teacher['reference_name']) ? htmlspecialchars($teacher['reference_name']) : 'N/A'; ?></td>
                </tr>
                <tr>
                    <th>Reference Contact:</th>
                    <td><?php echo !empty($teacher['reference_number']) ? htmlspecialchars($teacher['reference_number']) : 'N/A'; ?></td>
                </tr>
                <tr>
                    <th>Past History:</th>
                    <td><?php echo !empty($teacher['past_history']) ? nl2br(htmlspecialchars($teacher['past_history'])) : 'N/A'; ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>
