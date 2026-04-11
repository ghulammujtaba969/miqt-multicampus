<?php
/**
 * System Settings
 * MIQT System
 */

require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../includes/functions.php';

if (!hasPermission(['principal', 'vice_principal'])) {
    setFlash('danger', 'You do not have permission to access this page');
    redirect(SITE_URL . '/modules/dashboard/index.php');
}

$pageTitle = 'System Settings';

// Get current settings
$sql = "SELECT * FROM settings LIMIT 1";
$stmt = $db->prepare($sql);
$stmt->execute();
$settings = $stmt->fetch();

// If no settings exist, create default
if (!$settings) {
    $sql = "INSERT INTO settings (school_name, school_address, school_phone, school_email, academic_year, created_at)
            VALUES ('MINHAJ INSTITUTE OF QIRAT & TAJWEED', '', '', '', ?, NOW())";
    $stmt = $db->prepare($sql);
    $stmt->execute([date('Y')]);

    $sql = "SELECT * FROM settings LIMIT 1";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $settings = $stmt->fetch();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $school_name = sanitize($_POST['school_name']);
    $school_address = sanitize($_POST['school_address']);
    $school_phone = sanitize($_POST['school_phone']);
    $school_email = sanitize($_POST['school_email']);
    $academic_year = sanitize($_POST['academic_year']);
    $attendance_required = isset($_POST['attendance_required']) ? 1 : 0;
    $progress_required = isset($_POST['progress_required']) ? 1 : 0;

    try {
        $sql = "UPDATE settings SET
                school_name = ?,
                school_address = ?,
                school_phone = ?,
                school_email = ?,
                academic_year = ?,
                attendance_required = ?,
                progress_required = ?,
                updated_at = NOW()
                WHERE id = ?";

        $stmt = $db->prepare($sql);
        $stmt->execute([
            $school_name, $school_address, $school_phone, $school_email,
            $academic_year, $attendance_required, $progress_required,
            $settings['id']
        ]);

        logActivity($db, 'Update Settings', 'Settings', 'Updated system settings');
        setFlash('success', 'Settings updated successfully!');
        redirect(SITE_URL . '/modules/settings/settings.php');
    } catch(PDOException $e) {
        $error = 'Error updating settings: ' . $e->getMessage();
    }
}

// Get system statistics
$sql = "SELECT COUNT(*) FROM students WHERE status = 'active'";
$total_students = $db->query($sql)->fetchColumn();

$sql = "SELECT COUNT(*) FROM teachers WHERE status = 'active'";
$total_teachers = $db->query($sql)->fetchColumn();

$sql = "SELECT COUNT(*) FROM classes WHERE status = 'active'";
$total_classes = $db->query($sql)->fetchColumn();

$sql = "SELECT COUNT(*) FROM users";
$total_users = $db->query($sql)->fetchColumn();

$roleCounts = [];
$rcStmt = $db->query("SELECT role, COUNT(*) AS cnt FROM users GROUP BY role");
while ($row = $rcStmt->fetch(PDO::FETCH_ASSOC)) {
    $roleCounts[$row['role']] = (int)$row['cnt'];
}

$lastSaved = !empty($settings['updated_at'])
    ? date('M j, Y g:i A', strtotime($settings['updated_at']))
    : (!empty($settings['created_at']) ? date('M j, Y g:i A', strtotime($settings['created_at'])) : '—');

require_once '../../includes/header.php';
?>

<?php if (isset($error)): ?>
<div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<div class="miqt-settings-wrap">
    <div class="miqt-settings-nav">
        <div class="miqt-settings-nav-card">
            <div class="miqt-settings-nav-group">
                <div class="miqt-settings-nav-group-title">General</div>
                <button type="button" class="miqt-settings-nav-item active" onclick="miqtSwitchSettingsSection('general', this)"><i class="fas fa-school me-1"></i> Institute</button>
                <button type="button" class="miqt-settings-nav-item" onclick="miqtSwitchSettingsSection('appearance', this)"><i class="fas fa-palette me-1"></i> Appearance</button>
                <button type="button" class="miqt-settings-nav-item" onclick="miqtSwitchSettingsSection('language', this)"><i class="fas fa-globe me-1"></i> Language &amp; region</button>
            </div>
            <hr class="miqt-settings-nav-divider">
            <div class="miqt-settings-nav-group">
                <div class="miqt-settings-nav-group-title">Account</div>
                <button type="button" class="miqt-settings-nav-item" onclick="miqtSwitchSettingsSection('account', this)"><i class="fas fa-user me-1"></i> Account info</button>
                <button type="button" class="miqt-settings-nav-item" onclick="miqtSwitchSettingsSection('security', this)"><i class="fas fa-lock me-1"></i> Security</button>
                <button type="button" class="miqt-settings-nav-item" onclick="miqtSwitchSettingsSection('notifications', this)"><i class="fas fa-bell me-1"></i> Notifications</button>
            </div>
            <hr class="miqt-settings-nav-divider">
            <div class="miqt-settings-nav-group">
                <div class="miqt-settings-nav-group-title">System</div>
                <button type="button" class="miqt-settings-nav-item" onclick="miqtSwitchSettingsSection('roles', this)"><i class="fas fa-users me-1"></i> Roles &amp; access</button>
                <button type="button" class="miqt-settings-nav-item" onclick="miqtSwitchSettingsSection('backup', this)"><i class="fas fa-database me-1"></i> Backup &amp; data</button>
                <button type="button" class="miqt-settings-nav-item danger" onclick="miqtSwitchSettingsSection('danger', this)"><i class="fas fa-exclamation-triangle me-1"></i> Danger zone</button>
            </div>
        </div>
    </div>

    <div class="miqt-settings-main">
        <div class="miqt-settings-page-header">
            <h2><i class="fas fa-cog me-2"></i>Settings</h2>
            <div class="small text-muted">Last saved: <?php echo htmlspecialchars($lastSaved); ?></div>
        </div>

        <div class="stats-grid mb-4">
            <div class="stat-card stat-primary">
                <div class="stat-icon"><i class="fas fa-user-graduate"></i></div>
                <div class="stat-info">
                    <h3><?php echo (int)$total_students; ?></h3>
                    <p>Active students</p>
                </div>
            </div>
            <div class="stat-card stat-success">
                <div class="stat-icon"><i class="fas fa-chalkboard-teacher"></i></div>
                <div class="stat-info">
                    <h3><?php echo (int)$total_teachers; ?></h3>
                    <p>Active teachers</p>
                </div>
            </div>
            <div class="stat-card stat-info">
                <div class="stat-icon"><i class="fas fa-school"></i></div>
                <div class="stat-info">
                    <h3><?php echo (int)$total_classes; ?></h3>
                    <p>Active classes</p>
                </div>
            </div>
            <div class="stat-card stat-warning">
                <div class="stat-icon"><i class="fas fa-users-cog"></i></div>
                <div class="stat-info">
                    <h3><?php echo (int)$total_users; ?></h3>
                    <p>System users</p>
                </div>
            </div>
        </div>

        <div id="miqt-section-general">
            <div class="miqt-settings-card">
                <div class="miqt-settings-card-header">
                    <div class="miqt-sh-icon"><i class="fas fa-school"></i></div>
                    <div>
                        <h3>Institute information</h3>
                        <p>Details shown across reports and the application shell</p>
                    </div>
                </div>
                <div class="miqt-settings-card-body">
                    <form method="POST" action="">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="miqt-form-label-sm" for="school_name">School name</label>
                                <input type="text" name="school_name" id="school_name" class="miqt-form-control-s"
                                       value="<?php echo htmlspecialchars($settings['school_name']); ?>" required>
                            </div>
                            <div class="col-12">
                                <label class="miqt-form-label-sm" for="school_address">Address</label>
                                <textarea name="school_address" id="school_address" class="miqt-form-control-s" rows="3"><?php echo htmlspecialchars($settings['school_address']); ?></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="miqt-form-label-sm" for="school_phone">Phone</label>
                                <input type="tel" name="school_phone" id="school_phone" class="miqt-form-control-s"
                                       value="<?php echo htmlspecialchars($settings['school_phone']); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="miqt-form-label-sm" for="school_email">Email</label>
                                <input type="email" name="school_email" id="school_email" class="miqt-form-control-s"
                                       value="<?php echo htmlspecialchars($settings['school_email']); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="miqt-form-label-sm" for="academic_year">Academic year</label>
                                <input type="number" name="academic_year" id="academic_year" class="miqt-form-control-s"
                                       value="<?php echo htmlspecialchars((string)$settings['academic_year']); ?>" min="2020" max="2100" required>
                            </div>
                            <div class="col-12">
                                <label class="miqt-form-label-sm d-block mb-2">Operational preferences</label>
                                <div class="miqt-setting-row border-0 pt-0">
                                    <div class="miqt-setting-info">
                                        <div class="miqt-s-label">Require daily attendance</div>
                                        <div class="miqt-s-desc">When enabled, staff must record attendance on schedule</div>
                                    </div>
                                    <label class="miqt-toggle-wrap">
                                        <input type="checkbox" name="attendance_required" value="1" <?php echo ($settings['attendance_required'] ?? 1) ? 'checked' : ''; ?>>
                                        <span class="miqt-toggle-slider"></span>
                                    </label>
                                </div>
                                <div class="miqt-setting-row border-0">
                                    <div class="miqt-setting-info">
                                        <div class="miqt-s-label">Require daily progress</div>
                                        <div class="miqt-s-desc">Track Quranic progress entries per student</div>
                                    </div>
                                    <label class="miqt-toggle-wrap">
                                        <input type="checkbox" name="progress_required" value="1" <?php echo ($settings['progress_required'] ?? 1) ? 'checked' : ''; ?>>
                                        <span class="miqt-toggle-slider"></span>
                                    </label>
                                </div>
                            </div>
                            <div class="col-12 d-flex flex-wrap gap-2 pt-2">
                                <button type="submit" class="miqt-btn-save-settings"><i class="fas fa-save me-1"></i> Save changes</button>
                                <a href="<?php echo SITE_URL; ?>/modules/dashboard/index.php" class="miqt-btn-cancel-settings text-decoration-none d-inline-flex align-items-center">Back to dashboard</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div id="miqt-section-appearance" style="display:none">
            <div class="miqt-settings-card">
                <div class="miqt-settings-card-header">
                    <div class="miqt-sh-icon"><i class="fas fa-palette"></i></div>
                    <div>
                        <h3>Appearance</h3>
                        <p>Preview controls — the live app uses the MIQT Primary theme</p>
                    </div>
                </div>
                <div class="miqt-settings-card-body">
                    <label class="miqt-form-label-sm">Accent preview</label>
                    <div class="d-flex gap-2 flex-wrap mb-3">
                        <button type="button" class="miqt-palette-swatch active" style="background:#2176C7" title="Cobalt"></button>
                        <button type="button" class="miqt-palette-swatch" style="background:#0d3d73" title="Navy"></button>
                        <button type="button" class="miqt-palette-swatch" style="background:#C8902A" title="Gold"></button>
                        <button type="button" class="miqt-palette-swatch" style="background:#198754" title="Green"></button>
 <button type="button" class="miqt-palette-swatch" style="background:#6a1f9e" title="Purple"></button>
                    </div>
                    <p class="small text-muted mb-0">Per-user theme switching is not stored in the database yet; this panel matches the design reference only.</p>
                </div>
            </div>
        </div>

        <div id="miqt-section-language" style="display:none">
            <div class="miqt-settings-card">
                <div class="miqt-settings-card-header">
                    <div class="miqt-sh-icon"><i class="fas fa-globe"></i></div>
                    <div>
                        <h3>Language &amp; region</h3>
                        <p>Planned localisation — English is the default today</p>
                    </div>
                </div>
                <div class="miqt-settings-card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="miqt-form-label-sm">Display language</label>
                            <select class="miqt-form-select-s" disabled>
                                <option selected>English</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="miqt-form-label-sm">Timezone</label>
                            <select class="miqt-form-select-s" disabled>
                                <option selected>Asia/Karachi</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="miqt-section-account" style="display:none">
            <div class="miqt-settings-card">
                <div class="miqt-settings-card-header">
                    <div class="miqt-sh-icon"><i class="fas fa-user"></i></div>
                    <div>
                        <h3>Signed-in account</h3>
                        <p>Read-only snapshot from your session</p>
                    </div>
                </div>
                <div class="miqt-settings-card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="miqt-form-label-sm">Display name</label>
                            <input type="text" class="miqt-form-control-s" value="<?php echo htmlspecialchars(getUserName() ?? ''); ?>" readonly style="background:#f8f9fa">
                        </div>
                        <div class="col-md-6">
                            <label class="miqt-form-label-sm">Role</label>
                            <input type="text" class="miqt-form-control-s" value="<?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', getUserRole() ?? ''))); ?>" readonly style="background:#f8f9fa">
                        </div>
                    </div>
                    <p class="small text-muted mt-3 mb-0"><a href="<?php echo SITE_URL; ?>/modules/profile/index.php">Open profile</a> for more detail.</p>
                </div>
            </div>
        </div>

        <div id="miqt-section-security" style="display:none">
            <div class="miqt-settings-card">
                <div class="miqt-settings-card-header">
                    <div class="miqt-sh-icon"><i class="fas fa-lock"></i></div>
                    <div>
                        <h3>Password strength (demo)</h3>
                        <p>Use HR / user management to change staff passwords today</p>
                    </div>
                </div>
                <div class="miqt-settings-card-body">
                    <label class="miqt-form-label-sm" for="miqtDemoPw">Sample new password</label>
                    <input type="password" id="miqtDemoPw" class="miqt-form-control-s" autocomplete="new-password" placeholder="Type to see meter" oninput="miqtCheckPwDemo(this.value)">
                    <div class="miqt-pw-strength"><div class="miqt-pw-bar" id="miqtPwBar" style="width:0%"></div></div>
                    <div class="small mt-1" id="miqtPwLabel"></div>
                </div>
            </div>
        </div>

        <div id="miqt-section-notifications" style="display:none">
            <div class="miqt-settings-card">
                <div class="miqt-settings-card-header">
                    <div class="miqt-sh-icon"><i class="fas fa-bell"></i></div>
                    <div>
                        <h3>Notifications</h3>
                        <p>UI preview — not wired to outbound email or SMS yet</p>
                    </div>
                </div>
                <div class="miqt-settings-card-body">
                    <div class="miqt-setting-row">
                        <div class="miqt-setting-info"><div class="miqt-s-label">Attendance alerts</div><div class="miqt-s-desc">Low attendance warnings</div></div>
                        <label class="miqt-toggle-wrap"><input type="checkbox" checked disabled><span class="miqt-toggle-slider"></span></label>
                    </div>
                    <div class="miqt-setting-row">
                        <div class="miqt-setting-info"><div class="miqt-s-label">Fee reminders</div><div class="miqt-s-desc">Scheduled fee notices</div></div>
                        <label class="miqt-toggle-wrap"><input type="checkbox" checked disabled><span class="miqt-toggle-slider"></span></label>
                    </div>
                </div>
            </div>
        </div>

        <div id="miqt-section-roles" style="display:none">
            <div class="miqt-settings-card">
                <div class="miqt-settings-card-header">
                    <div class="miqt-sh-icon"><i class="fas fa-users"></i></div>
                    <div>
                        <h3>Roles &amp; access</h3>
                        <p>User counts from the database</p>
                    </div>
                </div>
                <div class="miqt-settings-card-body">
                    <div class="d-flex flex-column gap-3">
                        <?php
                        $roleLabels = [
                            'principal' => ['label' => 'Principal / Admin', 'icon' => 'fa-crown', 'style' => 'background:linear-gradient(135deg,var(--miqt-cobalt),var(--miqt-sky))'],
                            'vice_principal' => ['label' => 'Vice principal', 'icon' => 'fa-user-tie', 'style' => 'background:linear-gradient(135deg,var(--miqt-gold),#e6a82e)'],
                            'coordinator' => ['label' => 'Coordinator', 'icon' => 'fa-tasks', 'style' => 'background:linear-gradient(135deg,#198754,#22c55e)'],
                            'teacher' => ['label' => 'Teacher', 'icon' => 'fa-chalkboard-teacher', 'style' => 'background:linear-gradient(135deg,#198754,#22c55e)'],
                            'admin' => ['label' => 'Admin', 'icon' => 'fa-user-shield', 'style' => 'background:linear-gradient(135deg,#6a1f9e,#9b59b6)'],
                        ];
                        foreach ($roleLabels as $roleKey => $meta):
                            $cnt = $roleCounts[$roleKey] ?? 0;
                        ?>
                        <div class="miqt-role-row">
                            <div class="d-flex align-items-center gap-3">
                                <div class="miqt-role-icon" style="<?php echo $meta['style']; ?>"><i class="fas <?php echo $meta['icon']; ?>"></i></div>
                                <div>
                                    <div class="fw-bold" style="color:var(--miqt-cobalt-deep)"><?php echo htmlspecialchars($meta['label']); ?></div>
                                    <div class="small text-muted"><?php echo (int)$cnt; ?> user<?php echo $cnt === 1 ? '' : 's'; ?></div>
                                </div>
                            </div>
                            <span class="badge rounded-pill" style="background:var(--miqt-sky-light);color:var(--miqt-cobalt-dark)"><?php echo htmlspecialchars($roleKey); ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <div id="miqt-section-backup" style="display:none">
            <div class="miqt-settings-card">
                <div class="miqt-settings-card-header">
                    <div class="miqt-sh-icon"><i class="fas fa-database"></i></div>
                    <div>
                        <h3>Environment</h3>
                        <p>Connection details for administrators</p>
                    </div>
                    <span class="miqt-header-badge">Read-only</span>
                </div>
                <div class="miqt-settings-card-body">
                    <table class="table table-sm mb-0">
                        <tr><th>Database</th><td><?php echo htmlspecialchars(DB_NAME); ?></td></tr>
                        <tr><th>Host</th><td><?php echo htmlspecialchars(DB_HOST); ?></td></tr>
                        <tr><th>PHP</th><td><?php echo htmlspecialchars(phpversion()); ?></td></tr>
                        <tr><th>Site URL</th><td><?php echo htmlspecialchars(SITE_URL); ?></td></tr>
                        <tr><th>System version</th><td>1.0.0</td></tr>
                        <tr><th>Record last updated</th><td><?php echo htmlspecialchars(formatDate($settings['updated_at'] ?? $settings['created_at'])); ?></td></tr>
                    </table>
                </div>
            </div>
        </div>

        <div id="miqt-section-danger" style="display:none">
            <div class="miqt-settings-card miqt-settings-card-danger">
                <div class="miqt-settings-card-header miqt-sh-danger">
                    <div class="miqt-sh-icon danger"><i class="fas fa-exclamation-triangle"></i></div>
                    <div>
                        <h3 class="text-danger">Danger zone</h3>
                        <p>Destructive actions are not implemented — do not use in production without backups</p>
                    </div>
                </div>
                <div class="miqt-settings-card-body">
                    <div class="miqt-danger-zone">
                        <div class="miqt-danger-title">Irreversible operations</div>
                        <div class="miqt-danger-item">
                            <div><div class="fw-semibold text-danger">Reset academic progress data</div><div class="small text-muted">Not available in this build</div></div>
                            <button type="button" class="miqt-btn-danger-s" disabled>Unavailable</button>
                        </div>
                        <div class="miqt-danger-item">
                            <div><div class="fw-semibold text-danger">Archive academic year</div><div class="small text-muted">Requires a dedicated migration workflow</div></div>
                            <button type="button" class="miqt-btn-danger-s" disabled>Unavailable</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
  var sections = ['general','appearance','language','account','security','notifications','roles','backup','danger'];
  window.miqtSwitchSettingsSection = function (name, el) {
    sections.forEach(function (s) {
      var n = document.getElementById('miqt-section-' + s);
      if (n) n.style.display = 'none';
    });
    var t = document.getElementById('miqt-section-' + name);
    if (t) t.style.display = 'block';
    document.querySelectorAll('.miqt-settings-nav-item').forEach(function (i) { i.classList.remove('active'); });
    if (el) el.classList.add('active');
  };
  document.querySelectorAll('.miqt-palette-swatch').forEach(function (sw) {
    sw.addEventListener('click', function () {
      document.querySelectorAll('.miqt-palette-swatch').forEach(function (x) { x.classList.remove('active'); });
      sw.classList.add('active');
    });
  });
  window.miqtCheckPwDemo = function (val) {
    var bar = document.getElementById('miqtPwBar');
    var lbl = document.getElementById('miqtPwLabel');
    if (!bar || !lbl) return;
    if (!val) { bar.style.width = '0%'; bar.className = 'miqt-pw-bar'; lbl.textContent = ''; return; }
    var strength = 0;
    if (val.length >= 8) strength++;
    if (/[A-Z]/.test(val) && /[0-9]/.test(val)) strength++;
    if (/[^A-Za-z0-9]/.test(val)) strength++;
    if (strength === 1) { bar.className = 'miqt-pw-bar weak'; bar.style.width = '25%'; lbl.textContent = 'Weak'; lbl.className = 'small text-danger mt-1'; }
    else if (strength === 2) { bar.className = 'miqt-pw-bar fair'; bar.style.width = '60%'; lbl.textContent = 'Fair'; lbl.className = 'small mt-1'; lbl.style.color = 'var(--miqt-gold)'; }
    else { bar.className = 'miqt-pw-bar strong'; bar.style.width = '100%'; lbl.textContent = 'Strong'; lbl.className = 'small text-success mt-1'; }
  };
})();
</script>

<?php require_once '../../includes/footer.php'; ?>
