<?php
/**
 * Internal email (demo UI — theme from themes/primary/email-miqt.html)
 */

require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../includes/functions.php';

if (!hasPermission(['principal', 'vice_principal', 'coordinator'])) {
    setFlash('danger', 'You do not have permission to access this page');
    redirect(SITE_URL . '/modules/dashboard/index.php');
}

$pageTitle = 'Email';

require_once '../../includes/header.php';
?>

<p class="text-muted small mb-3">Demo layout for future messaging. This screen is not connected to a mail server yet.</p>

<div class="miqt-email-layout">
    <div class="miqt-email-folders">
        <button type="button" class="miqt-folder-compose" id="miqtEmailComposeOpen"><i class="fas fa-pen me-1"></i> Compose</button>
        <div class="miqt-folder-item active"><i class="fas fa-inbox me-1"></i> Inbox <span class="miqt-folder-badge">12</span></div>
        <div class="miqt-folder-item"><i class="far fa-star me-1"></i> Starred</div>
        <div class="miqt-folder-item"><i class="fas fa-paper-plane me-1"></i> Sent</div>
        <div class="miqt-folder-item"><i class="far fa-file-alt me-1"></i> Drafts <span class="miqt-folder-badge gold">3</span></div>
        <div class="miqt-folder-item"><i class="far fa-trash-alt me-1"></i> Trash</div>
        <hr class="miqt-folder-sep">
        <div class="miqt-folder-item"><i class="fas fa-users me-1"></i> Parents</div>
        <div class="miqt-folder-item"><i class="fas fa-chalkboard-teacher me-1"></i> Teachers</div>
        <div class="miqt-folder-item"><i class="fas fa-school me-1"></i> Management</div>
        <div class="miqt-folder-item"><i class="fas fa-bullhorn me-1"></i> Announcements</div>
    </div>

    <div class="miqt-email-list-panel">
        <div class="miqt-list-toolbar">
            <input type="search" class="miqt-list-search" placeholder="Search messages…" aria-label="Search messages">
            <button type="button" class="btn btn-sm btn-outline-secondary" title="Filter"><i class="fas fa-sliders-h"></i></button>
        </div>

        <div class="miqt-email-item unread active" data-email-index="0">
            <div class="miqt-em-header"><span class="miqt-em-from">Qari Bilal Ahmad</span><span class="miqt-em-time">9:42 AM</span></div>
            <div class="miqt-em-subject">Tajweed Assessment Results – July Batch</div>
            <div class="miqt-em-preview">Please find attached the compiled results for all students in the July morning batch…</div>
            <div class="miqt-em-tags"><span class="miqt-em-tag blue">Teacher</span></div>
        </div>
        <div class="miqt-email-item unread" data-email-index="1">
            <div class="miqt-em-header"><span class="miqt-em-from">Mrs. Rukhsana Khan</span><span class="miqt-em-time">8:15 AM</span></div>
            <div class="miqt-em-subject">Fee Waiver Request – My Son Hamza</div>
            <div class="miqt-em-preview">Assalam o Alaikum, I am writing to respectfully request a temporary fee waiver for…</div>
            <div class="miqt-em-tags"><span class="miqt-em-tag gold">Parent</span></div>
        </div>
        <div class="miqt-email-item" data-email-index="2">
            <div class="miqt-em-header"><span class="miqt-em-from">Hafiz Zubair Hassan</span><span class="miqt-em-time">Yesterday</span></div>
            <div class="miqt-em-subject">Attendance Report – Week of June 30</div>
            <div class="miqt-em-preview">Dear Principal Sahib, attached is the weekly attendance summary for Batch C…</div>
            <div class="miqt-em-tags"><span class="miqt-em-tag blue">Teacher</span></div>
        </div>
        <div class="miqt-email-item unread" data-email-index="3">
            <div class="miqt-em-header"><span class="miqt-em-from">MIQT Admin System</span><span class="miqt-em-time">Yesterday</span></div>
            <div class="miqt-em-subject">3 Students Below Minimum Attendance</div>
            <div class="miqt-em-preview">Automated alert: The following students have attendance below the 75% threshold…</div>
            <div class="miqt-em-tags"><span class="miqt-em-tag red">Alert</span></div>
        </div>
    </div>

    <div class="miqt-email-read-panel">
        <div class="miqt-read-toolbar">
            <button type="button" class="miqt-read-tool-btn"><i class="fas fa-reply me-1"></i> Reply</button>
            <button type="button" class="miqt-read-tool-btn"><i class="fas fa-share me-1"></i> Forward</button>
            <button type="button" class="miqt-read-tool-btn"><i class="far fa-star me-1"></i> Star</button>
            <button type="button" class="miqt-read-tool-btn"><i class="fas fa-folder me-1"></i> Move</button>
            <button type="button" class="miqt-read-tool-btn"><i class="fas fa-print me-1"></i> Print</button>
            <button type="button" class="miqt-read-tool-btn danger ms-auto"><i class="far fa-trash-alt me-1"></i> Delete</button>
        </div>
        <div class="miqt-read-body" id="miqtReadBody">
            <div class="miqt-email-subject-h" id="miqtEmailSubject">Tajweed Assessment Results – July Batch</div>
            <div class="miqt-email-sender-row">
                <div class="miqt-sender-av" id="miqtSenderAv">Q</div>
                <div>
                    <div class="fw-bold" id="miqtSenderName" style="font-size:0.9rem">Qari Bilal Ahmad</div>
                    <div class="miqt-email-meta">
                        <span>From: <strong>q.bilal@miqt.edu.pk</strong></span>
                        <span>To: <strong>principal@miqt.edu.pk</strong></span>
                        <span>· <strong>Jul 7, 2025 · 9:42 AM</strong></span>
                    </div>
                </div>
            </div>
            <div class="miqt-email-body-text" id="miqtEmailBodyHtml">
                <p>Assalam o Alaikum Wa Rahmatullahi Wa Barakatuhu,</p>
                <p>Respected Principal Sahib, I hope this message finds you in the best of health and Iman. Please find attached the compiled Tajweed assessment results for all 38 students enrolled in the July Morning Batch.</p>
                <div class="miqt-highlight-box">
                    <p><strong>Summary:</strong> 31 students passed with distinction, 5 require additional coaching on Sifaat-ul-Huroof, and 2 need to repeat the Makhaarij module before promotion to the next level.</p>
                </div>
                <p>I have prepared individual progress cards for each student which are attached in PDF format. I recommend scheduling parent-teacher meetings for the students who need extra attention.</p>
                <p>JazakAllah Khair,<br><strong>Qari Bilal Ahmad</strong><br>Senior Tajweed Instructor, MIQT</p>
            </div>
            <div class="miqt-attachments">
                <h6><i class="fas fa-paperclip me-1"></i> Attachments (3)</h6>
                <div class="miqt-attach-list">
                    <div class="miqt-attach-item"><i class="fas fa-file-excel text-success"></i><div><div class="fw-semibold small" style="color:var(--miqt-cobalt-deep)">July_Batch_Results.xlsx</div><div class="small text-muted">245 KB</div></div></div>
                    <div class="miqt-attach-item"><i class="fas fa-file-pdf text-danger"></i><div><div class="fw-semibold small" style="color:var(--miqt-cobalt-deep)">Progress_Cards_July.pdf</div><div class="small text-muted">1.2 MB</div></div></div>
                    <div class="miqt-attach-item"><i class="fas fa-file-alt text-primary"></i><div><div class="fw-semibold small" style="color:var(--miqt-cobalt-deep)">Tajweed_Rubric_2025.pdf</div><div class="small text-muted">88 KB</div></div></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="miqt-compose-modal" id="miqtComposeModal" aria-hidden="true">
    <div class="miqt-compose-header">
        <h6><i class="fas fa-pen me-1"></i> New message</h6>
        <button type="button" class="miqt-compose-close" id="miqtEmailComposeClose" aria-label="Close">&times;</button>
    </div>
    <div class="miqt-compose-field">
        <label>To</label>
        <input type="text" placeholder="Recipient name or email…">
    </div>
    <div class="miqt-compose-field">
        <label>CC</label>
        <input type="text" placeholder="Optional">
    </div>
    <div class="miqt-compose-field">
        <label>Re</label>
        <input type="text" placeholder="Subject…">
    </div>
    <div class="miqt-compose-body">
        <textarea placeholder="Write your message here…"></textarea>
    </div>
    <div class="miqt-compose-footer">
        <button type="button" class="btn btn-primary btn-sm"><i class="fas fa-paper-plane me-1"></i> Send</button>
        <span class="small text-muted">Demo only — not sent</span>
    </div>
</div>

<script>
(function () {
  const emails = [
    { subject: 'Tajweed Assessment Results – July Batch', from: 'Qari Bilal Ahmad', av: 'Q', body: '<p>Assalam o Alaikum Wa Rahmatullahi Wa Barakatuhu,</p><p>Respected Principal Sahib, please find attached the compiled Tajweed assessment results for all 38 students in the July Morning Batch.</p><div class="miqt-highlight-box"><p><strong>Summary:</strong> 31 students passed with distinction, 5 require additional coaching on Sifaat-ul-Huroof, and 2 need to repeat the Makhaarij module before promotion.</p></div><p>I recommend scheduling parent-teacher meetings for students who need extra attention.</p><p>JazakAllah Khair,<br><strong>Qari Bilal Ahmad</strong><br>Senior Tajweed Instructor</p>' },
    { subject: 'Fee Waiver Request – My Son Hamza', from: 'Mrs. Rukhsana Khan', av: 'R', body: '<p>Assalam o Alaikum,</p><p>Respected Principal Sahib, I am writing to respectfully request a temporary fee waiver for my son Hamza Khan (MIQT-2024-087) for the month of July 2025.</p><div class="miqt-highlight-box"><p><strong>Reason:</strong> My husband has been hospitalised since last month and we are facing genuine financial hardship. We sincerely request a one-month concession.</p></div><p>We have always paid fees on time and are committed to continuing Hamza\'s Hifz education.</p><p>Jazak Allah Khair,<br><strong>Mrs. Rukhsana Khan</strong></p>' },
    { subject: 'Attendance Report – Week of June 30', from: 'Hafiz Zubair Hassan', av: 'Z', body: '<p>Assalam o Alaikum,</p><p>Dear Principal Sahib, please find attached the weekly attendance summary for Batch C (Evening Session) for the week of June 30 – July 4, 2025.</p><div class="miqt-highlight-box"><p><strong>Overall Attendance: 91.4%</strong> — 3 students were absent more than twice; I have noted their names for follow-up with parents.</p></div><p>JazakAllah,<br><strong>Hafiz Zubair Hassan</strong></p>' },
    { subject: '3 Students Below Minimum Attendance', from: 'MIQT Admin System', av: 'A', body: '<p>This is an automated alert.</p><div class="miqt-highlight-box"><p>The following students have attendance below the 75% threshold. Please review and contact guardians.</p></div><p>You can open the attendance reports module for full detail.</p>' },
  ];

  function showEmail(i) {
    document.querySelectorAll('.miqt-email-item').forEach(function (el, idx) {
      el.classList.toggle('active', idx === i);
    });
    var e = emails[i] || emails[0];
    document.getElementById('miqtEmailSubject').textContent = e.subject;
    document.getElementById('miqtSenderName').textContent = e.from;
    document.getElementById('miqtSenderAv').textContent = e.av;
    document.getElementById('miqtEmailBodyHtml').innerHTML = e.body;
  }

  document.querySelectorAll('.miqt-email-item').forEach(function (el, idx) {
    el.addEventListener('click', function () { showEmail(idx); });
  });

  document.querySelectorAll('.miqt-folder-item').forEach(function (f) {
    f.addEventListener('click', function () {
      document.querySelectorAll('.miqt-folder-item').forEach(function (x) { x.classList.remove('active'); });
      f.classList.add('active');
    });
  });

  var modal = document.getElementById('miqtComposeModal');
  document.getElementById('miqtEmailComposeOpen').addEventListener('click', function () {
    modal.classList.add('open');
    modal.setAttribute('aria-hidden', 'false');
  });
  document.getElementById('miqtEmailComposeClose').addEventListener('click', function () {
    modal.classList.remove('open');
    modal.setAttribute('aria-hidden', 'true');
  });
})();
</script>

<?php require_once '../../includes/footer.php'; ?>
