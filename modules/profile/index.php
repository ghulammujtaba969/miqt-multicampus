<?php
/**
 * User profile (theme from themes/primary/profile-miqt.html)
 */

require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../includes/functions.php';

$pageTitle = 'My Profile';

$uid = getUserId();
$stmt = $db->prepare('SELECT * FROM users WHERE id = ? LIMIT 1');
$stmt->execute([$uid]);
$user = $stmt->fetch();
if (!$user) {
    setFlash('danger', 'User not found.');
    redirect(SITE_URL . '/modules/dashboard/index.php');
}

$tstmt = $db->prepare('SELECT * FROM teachers WHERE user_id = ? LIMIT 1');
$tstmt->execute([$uid]);
$teacher = $tstmt->fetch();

$roleLabel = ucfirst(str_replace('_', ' ', $user['role']));
$displayName = $user['full_name'];
$initials = getUserInitials($displayName);
$email = $user['email'] ?? '';
$phone = $teacher['phone'] ?? '';
$address = $teacher['address'] ?? '';
$city = $teacher['city'] ?? '';
$joined = $teacher['joining_date'] ?? ($user['created_at'] ?? '');
$joinedFmt = $joined ? formatDate($joined) : '—';

$studentCount = (int)$db->query("SELECT COUNT(*) FROM students WHERE status = 'active'")->fetchColumn();
$teacherCount = (int)$db->query("SELECT COUNT(*) FROM teachers WHERE status = 'active'")->fetchColumn();

$photoUrl = '';
if ($teacher && !empty($teacher['photo'])) {
    $photoUrl = SITE_URL . 'uploads/photos/' . rawurlencode($teacher['photo']);
}

require_once '../../includes/header.php';
?>

<div class="miqt-profile-hero">
    <div class="miqt-hero-inner">
        <div class="miqt-avatar-wrap">
            <div class="miqt-avatar-img">
                <?php if ($photoUrl): ?>
                <img src="<?php echo htmlspecialchars($photoUrl); ?>" alt="">
                <?php else: ?>
                <?php echo htmlspecialchars($initials); ?>
                <?php endif; ?>
            </div>
        </div>
        <div class="miqt-hero-info">
            <h2><?php echo htmlspecialchars($displayName); ?></h2>
            <span class="miqt-role-tag"><i class="fas fa-user-shield me-1"></i><?php echo htmlspecialchars($roleLabel); ?></span>
            <div class="miqt-meta-row">
                <?php if ($email): ?><div class="miqt-meta-item"><i class="fas fa-envelope me-1"></i><?php echo htmlspecialchars($email); ?></div><?php endif; ?>
                <?php if ($phone): ?><div class="miqt-meta-item"><i class="fas fa-phone me-1"></i><?php echo htmlspecialchars($phone); ?></div><?php endif; ?>
                <?php if ($city): ?><div class="miqt-meta-item"><i class="fas fa-map-marker-alt me-1"></i><?php echo htmlspecialchars($city); ?></div><?php endif; ?>
                <div class="miqt-meta-item"><i class="far fa-calendar me-1"></i>Joined <?php echo htmlspecialchars($joinedFmt); ?></div>
            </div>
        </div>
        <div class="miqt-hero-actions">
            <?php if ($teacher && hasPermission(['principal', 'vice_principal', 'coordinator'])): ?>
            <a class="miqt-btn-edit-profile text-decoration-none text-center" href="<?php echo SITE_URL; ?>/modules/hr/view_teacher.php?id=<?php echo (int)$teacher['id']; ?>"><i class="fas fa-id-card me-1"></i> Teacher record</a>
            <?php elseif (hasPermission(['principal', 'vice_principal', 'coordinator'])): ?>
            <a class="miqt-btn-edit-profile text-decoration-none text-center" href="<?php echo SITE_URL; ?>/modules/hr/view_staff.php?id=<?php echo (int)$uid; ?>"><i class="fas fa-id-card me-1"></i> Staff record</a>
            <?php endif; ?>
            <a class="miqt-btn-edit-profile text-decoration-none text-center" href="<?php echo SITE_URL; ?>/modules/dashboard/index.php"><i class="fas fa-home me-1"></i> Dashboard</a>
        </div>
    </div>
    <div class="miqt-hero-stats">
        <div class="miqt-hero-stat"><div class="val"><?php echo $studentCount; ?></div><div class="lbl">Active students</div></div>
        <div class="miqt-hero-stat"><div class="val"><?php echo $teacherCount; ?></div><div class="lbl">Staff members</div></div>
        <div class="miqt-hero-stat"><div class="val">—</div><div class="lbl">Your department</div></div>
        <div class="miqt-hero-stat"><div class="val"><?php echo htmlspecialchars($user['status'] ?? 'active'); ?></div><div class="lbl">Account status</div></div>
    </div>
</div>

<div class="miqt-profile-tabs" role="tablist">
    <button type="button" class="miqt-profile-tab active" data-miqt-tab="info">Personal info</button>
    <button type="button" class="miqt-profile-tab" data-miqt-tab="account">Account</button>
    <?php if ($teacher): ?>
    <button type="button" class="miqt-profile-tab" data-miqt-tab="professional">Professional</button>
    <?php endif; ?>
</div>

<div id="miqt-tab-info">
    <div class="miqt-info-card">
        <div class="miqt-info-card-header">
            <h4><i class="fas fa-user me-1"></i> Profile</h4>
        </div>
        <div class="miqt-info-card-body">
            <div class="miqt-info-grid">
                <div class="miqt-info-field"><label>Full name</label><div class="miqt-info-val"><?php echo htmlspecialchars($displayName); ?></div></div>
                <div class="miqt-info-field"><label>Username</label><div class="miqt-info-val"><?php echo htmlspecialchars($user['username']); ?></div></div>
                <div class="miqt-info-field"><label>Role</label><div class="miqt-info-val"><?php echo htmlspecialchars($roleLabel); ?></div></div>
                <?php if ($teacher): ?>
                <div class="miqt-info-field"><label>Gender</label><div class="miqt-info-val"><?php echo htmlspecialchars(ucfirst($teacher['gender'] ?? '')); ?></div></div>
                <div class="miqt-info-field"><label>Date of birth</label><div class="miqt-info-val"><?php echo !empty($teacher['date_of_birth']) ? htmlspecialchars(formatDate($teacher['date_of_birth'])) : '—'; ?></div></div>
                <div class="miqt-info-field"><label>CNIC</label><div class="miqt-info-val"><?php echo htmlspecialchars($teacher['cnic'] ?? '—'); ?></div></div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="miqt-info-card">
        <div class="miqt-info-card-header">
            <h4><i class="fas fa-address-book me-1"></i> Contact</h4>
        </div>
        <div class="miqt-info-card-body">
            <div class="miqt-info-grid">
                <div class="miqt-info-field"><label>Email</label><div class="miqt-info-val"><?php echo $email ? '<a href="mailto:' . htmlspecialchars($email) . '">' . htmlspecialchars($email) . '</a>' : '—'; ?></div></div>
                <div class="miqt-info-field"><label>Phone</label><div class="miqt-info-val"><?php echo htmlspecialchars($phone ?: '—'); ?></div></div>
                <div class="miqt-info-field miqt-span-3"><label>Address</label><div class="miqt-info-val"><?php echo htmlspecialchars(trim($address . ($city ? ', ' . $city : '')) ?: '—'); ?></div></div>
            </div>
        </div>
    </div>
</div>

<div id="miqt-tab-account" style="display:none">
    <div class="miqt-info-card">
        <div class="miqt-info-card-header">
            <h4><i class="fas fa-key me-1"></i> Account</h4>
        </div>
        <div class="miqt-info-card-body">
            <p class="text-muted small mb-0">Password changes are managed by your administrator from the HR / users modules. Contact the principal if you need to reset your login.</p>
        </div>
    </div>
</div>

<?php if ($teacher): ?>
<div id="miqt-tab-professional" style="display:none">
    <div class="miqt-info-card">
        <div class="miqt-info-card-header">
            <h4><i class="fas fa-briefcase me-1"></i> Professional details</h4>
            <?php if (hasPermission(['principal', 'vice_principal', 'coordinator'])): ?>
            <a class="btn btn-sm btn-outline-primary" href="<?php echo SITE_URL; ?>/modules/hr/edit_teacher.php?id=<?php echo (int)$teacher['id']; ?>">Edit teacher</a>
            <?php endif; ?>
        </div>
        <div class="miqt-info-card-body">
            <div class="miqt-info-grid">
                <div class="miqt-info-field"><label>Teacher ID</label><div class="miqt-info-val"><?php echo htmlspecialchars($teacher['teacher_id']); ?></div></div>
                <div class="miqt-info-field"><label>Joining date</label><div class="miqt-info-val"><?php echo htmlspecialchars(formatDate($teacher['joining_date'])); ?></div></div>
                <div class="miqt-info-field"><label>Employment</label><div class="miqt-info-val"><?php echo htmlspecialchars(str_replace('_', ' ', $teacher['employment_type'] ?? '')); ?></div></div>
                <div class="miqt-info-field miqt-span-3"><label>Qualification</label><div class="miqt-info-val"><?php echo htmlspecialchars($teacher['qualification'] ?? '—'); ?></div></div>
                <div class="miqt-info-field miqt-span-3"><label>Specialization</label><div class="miqt-info-val"><?php echo htmlspecialchars($teacher['specialization'] ?? '—'); ?></div></div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
(function () {
  var tabs = document.querySelectorAll('[data-miqt-tab]');
  var panels = {
    info: document.getElementById('miqt-tab-info'),
    account: document.getElementById('miqt-tab-account'),
    professional: document.getElementById('miqt-tab-professional')
  };
  tabs.forEach(function (btn) {
    btn.addEventListener('click', function () {
      var name = btn.getAttribute('data-miqt-tab');
      tabs.forEach(function (b) { b.classList.toggle('active', b === btn); });
      Object.keys(panels).forEach(function (k) {
        if (panels[k]) panels[k].style.display = (k === name) ? 'block' : 'none';
      });
    });
  });
})();
</script>

<?php require_once '../../includes/footer.php'; ?>
