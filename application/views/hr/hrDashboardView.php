<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/* ── Calculations ── */
$total_staff     = isset($total_staff)     ? $total_staff     : 0;
$pending_count   = isset($pending_count)   ? $pending_count   : 0;
$new_applicants  = isset($new_applicants)  ? $new_applicants  : 0;
$present_today   = isset($present_today)   ? $present_today   : 0;
$absent_today    = $total_staff - $present_today;
$attendance_rate = ($total_staff > 0) ? round(($present_today / $total_staff) * 100) : 0;
$att_circ        = 251.2;
$att_dash        = round(($attendance_rate / 100) * $att_circ, 1);
$att_remain      = $att_circ - $att_dash;

/* Leave approval rate from recent_leaves */
$approved_count  = 0;
$total_req_count = 0;
if (!empty($recent_leaves)) {
    foreach ($recent_leaves as $l) {
        $total_req_count++;
        if (isset($l['seemrq_status']) && $l['seemrq_status'] == 'approved') $approved_count++;
    }
}
$reminder_count = 0;
if (!empty($recent_leaves)) {
    foreach ($recent_leaves as $l) {
        if (isset($l['seemrq_reminder']) && $l['seemrq_reminder'] == 1) $reminder_count++;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>HR Dashboard | Suropriyo Enterprise</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= base_url() ?>css/hr/hrDashboardView.css">
</head>
<body>

<div class="main-content">

  <!-- ══════════════ PAGE HEADER ══════════════ -->
  <div class="page-header">
    <div>
      <h1>HR <span>Operations Center</span></h1>
      <div class="page-subtitle">
        <span class="badge-live">Active</span>
        <?= date('l, d M Y') ?>
      </div>
    </div>
    <a href="<?= base_url('Employee/RegisterEmployee') ?>" class="btn-brand">
      <i class="fas fa-user-plus"></i> Add Employee
    </a>
  </div>

  <!-- ══════════════ REMINDER BANNER ══════════════ -->
  <?php if ($reminder_count > 0): ?>
  <div class="reminder-banner">
    <span class="reminder-icon"><i class="fas fa-bell"></i></span>
    <div>
      <strong>Action Required</strong> — You have
      <strong><?= $reminder_count ?></strong> leave request<?= $reminder_count > 1 ? 's' : '' ?> with pending reminders.
    </div>
    <a href="<?= base_url('Employee/viewEmployeeLeaveRequests') ?>" class="reminder-link">Review →</a>
  </div>
  <?php endif; ?>

  <!-- ══════════════ KPI CARDS ══════════════ -->
  <div class="kpi-grid">

    <!-- Total Employees -->
    <div class="kpi-card v-purple">
      <div class="kpi-icon"><i class="fas fa-users"></i></div>
      <div class="kpi-label">Total Employees</div>
      <div class="kpi-value"><?= $total_staff ?></div>
      <div class="kpi-meta info"><i class="fas fa-building me-1"></i>Active workforce</div>
      <div class="kpi-track"><div class="kpi-track-fill" style="width:100%"></div></div>
    </div>

    <!-- Present Today -->
    <div class="kpi-card v-green">
      <div class="kpi-icon"><i class="fas fa-user-check"></i></div>
      <div class="kpi-label">Present Today</div>
      <div class="kpi-value"><?= $present_today ?><span style="font-size:1rem;font-weight:500;color:var(--text-muted)"> /<?= $total_staff ?></span></div>
      <div class="kpi-meta up"><i class="fas fa-chart-line me-1"></i><?= $attendance_rate ?>% attendance rate</div>
      <div class="kpi-track"><div class="kpi-track-fill" style="width:<?= $attendance_rate ?>%"></div></div>
    </div>

    <!-- Pending Leave Requests -->
    <div class="kpi-card v-red">
      <div class="kpi-icon"><i class="fas fa-calendar-times"></i></div>
      <div class="kpi-label">Pending Leaves</div>
      <div class="kpi-value"><?= $pending_count ?></div>
      <div class="kpi-meta down"><i class="fas fa-exclamation-circle me-1"></i>Awaiting approval</div>
    </div>

    <!-- New Applicants -->
    <div class="kpi-card v-blue">
      <div class="kpi-icon"><i class="fas fa-user-tie"></i></div>
      <div class="kpi-label">New Applicants</div>
      <div class="kpi-value"><?= $new_applicants ?></div>
      <div class="kpi-meta info"><i class="fas fa-envelope me-1"></i>Pending review</div>
    </div>

    <!-- Absent Today -->
    <div class="kpi-card v-amber">
      <div class="kpi-icon"><i class="fas fa-user-times"></i></div>
      <div class="kpi-label">Absent Today</div>
      <div class="kpi-value"><?= $absent_today ?></div>
      <div class="kpi-meta warn"><i class="fas fa-clock me-1"></i><?= $attendance_rate ?>% present</div>
      <div class="kpi-track"><div class="kpi-track-fill" style="width:<?= $total_staff > 0 ? round(($absent_today/$total_staff)*100) : 0 ?>%"></div></div>
    </div>

  </div><!-- /kpi-grid -->

  <!-- ══════════════ PANEL GRID ══════════════ -->
  <div class="dash-grid">

    <!-- ① ATTENDANCE SNAPSHOT -->
    <div class="panel">
      <div class="panel-head">
        <h6>
          <span class="ph-icon" style="--ph-bg:#d1fae5;--ph-color:#059669"><i class="fas fa-calendar-check"></i></span>
          Attendance Snapshot
        </h6>
        <a href="<?= base_url('Employee/viewAttendance') ?>">View All →</a>
      </div>

      <div class="att-donut-wrap">
        <svg viewBox="0 0 100 100" style="width:82px;height:82px;flex-shrink:0">
          <circle cx="50" cy="50" r="40" fill="none" stroke="#fee2e2" stroke-width="13"/>
          <circle cx="50" cy="50" r="40" fill="none" stroke="#10b981" stroke-width="13"
            stroke-dasharray="<?= $att_dash ?> <?= $att_remain ?>" stroke-dashoffset="0"
            stroke-linecap="round" style="transform:rotate(-90deg);transform-origin:center"/>
          <text x="50" y="46" text-anchor="middle" font-size="13" font-weight="800"
            fill="#1a1340" font-family="Plus Jakarta Sans,sans-serif"><?= $attendance_rate ?>%</text>
          <text x="50" y="58" text-anchor="middle" font-size="7"
            fill="#7c82aa" font-family="Plus Jakarta Sans,sans-serif">PRESENT</text>
        </svg>
        <div class="att-stat">
          <div class="att-row">
            <span class="ar-label"><i class="fas fa-circle me-1" style="color:#10b981;font-size:.55rem"></i> Present</span>
            <span class="ar-val" style="color:#059669"><?= $present_today ?></span>
          </div>
          <div class="att-row">
            <span class="ar-label"><i class="fas fa-circle me-1" style="color:#f87171;font-size:.55rem"></i> Absent</span>
            <span class="ar-val" style="color:#dc2626"><?= $absent_today ?></span>
          </div>
        </div>
      </div>

      <div class="workforce-box">
        <span class="wb-label"><i class="fas fa-building me-1"></i> Total Workforce</span>
        <span class="wb-val"><?= $total_staff ?> <span style="font-size:.82rem;font-weight:500;color:var(--brand-400)">staff</span></span>
      </div>
    </div>
    <!-- ③ RECENT APPLICANTS -->
    <div class="panel">
      <div class="panel-head">
        <h6>
          <span class="ph-icon" style="--ph-bg:#dbeafe;--ph-color:#2563eb"><i class="fas fa-user-tie"></i></span>
          Recent Hiring
        </h6>
        <a href="<?= base_url('Employee/viewJobApplicants') ?>">View All →</a>
      </div>

      <?php if (!empty($recent_applicants)): ?>
        <?php foreach ($recent_applicants as $app):
          $state = strtolower($app['sejoba_state'] ?? 'applied');
          $badge = 'badge-pending';
          if ($state == 'selected' || $state == 'hired') $badge = 'badge-active';
          elseif ($state == 'rejected') $badge = 'badge-inactive';
          elseif ($state == 'applied') $badge = 'badge-info';
        ?>
        <div class="applicant-row">
          <div>
            <div class="ap-name"><?= htmlspecialchars($app['sejoba_name'] ?? '') ?></div>
            <div class="ap-pos"><?= htmlspecialchars($app['sejoba_position'] ?? '') ?></div>
          </div>
          <span class="<?= $badge ?>" style="text-transform:capitalize"><?= $state ?></span>
        </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p style="font-size:.82rem;color:var(--text-muted);padding:8px 0">No recent applications.</p>
      <?php endif; ?>

      <a href="<?= base_url('Employee/viewJobApplicants') ?>" class="btn-brand mt-3" style="width:100%;justify-content:center;padding:10px">
        <i class="fas fa-briefcase"></i> Manage Applications
      </a>
    </div>

    <!-- ② LEAVE REQUESTS PANEL -->
    <div class="panel" style="grid-column: span 2;">
      <div class="panel-head">
        <h6>
          <span class="ph-icon" style="--ph-bg:#fee2e2;--ph-color:#dc2626"><i class="fas fa-calendar-times"></i></span>
          Pending Leave Requests
        </h6>
        <a href="<?= base_url('Employee/viewEmployeeLeaveRequests') ?>">Manage All →</a>
      </div>

      <div class="table-responsive">
        <table class="se-table table align-middle mb-0">
          <thead>
            <tr>
              <th>Employee</th>
              <th>Reason</th>
              <th>Applied</th>
              <th>Duration</th>
              <th>Leave Balance</th>
              <th class="text-center">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($recent_leaves)): ?>
              <?php foreach ($recent_leaves as $leave):
                $taken     = isset($leave['total_taken']) ? (int)$leave['total_taken'] : 0;
                $remaining = 20 - $taken;
                $bal_color = ($taken >= 18) ? 'var(--danger)' : (($taken >= 14) ? 'var(--warning)' : 'var(--success)');
              ?>
              <tr class="<?= (isset($leave['seemrq_reminder']) && $leave['seemrq_reminder'] == 1) ? 'row-reminder' : '' ?>">
                <td>
                  <div class="fw-bold" style="color:var(--text-primary)">
                    <?= htmlspecialchars($leave['seempd_name'] ?? 'N/A') ?>
                    <?php if (isset($leave['seemrq_reminder']) && $leave['seemrq_reminder'] == 1): ?>
                      <span class="badge-pending ms-1" style="font-size:.62rem">REMINDER</span>
                    <?php endif; ?>
                  </div>
                  <small style="color:var(--text-muted)"><?= $leave['seemrq_empid'] ?? '' ?></small>
                </td>
                <td>
                  <span class="badge-info"><?= htmlspecialchars($leave['seemrq_reason'] ?? '') ?></span>
                  <?php if (!empty($leave['seemrq_summary'])): ?>
                  <br><a href="javascript:void(0)" class="detail-link mt-1"
                    onclick="showLeaveDetails(
                      '<?= htmlspecialchars($leave['seempd_name'] ?? '', ENT_QUOTES) ?>',
                      '<?= htmlspecialchars($leave['seemrq_reason'] ?? '', ENT_QUOTES) ?>',
                      '<?= htmlspecialchars(json_encode($leave['seemrq_summary']), ENT_QUOTES) ?>',
                      '<?= $leave['seemrq_fromdate'] ?? '' ?>',
                      '<?= $leave['seemrq_todate'] ?? '' ?>')">
                    <i class="fas fa-info-circle me-1"></i>Details
                  </a>
                  <?php endif; ?>
                </td>
                <td style="color:var(--text-secondary);font-size:.82rem">
                  <?= isset($leave['seemrq_reqdate']) ? date('d M Y', strtotime($leave['seemrq_reqdate'])) : '—' ?>
                </td>
                <td style="font-weight:700;color:var(--brand-600)">
                  <?= $leave['seemrq_days'] ?? 0 ?> Days
                </td>
                <td>
                  <div style="font-size:.78rem;color:var(--text-muted);margin-bottom:4px"><?= $taken ?> / 20 used</div>
                  <div class="kpi-track" style="width:90px">
                    <div class="kpi-track-fill" style="width:<?= min(100, ($taken/20)*100) ?>%;background:<?= $bal_color ?>"></div>
                  </div>
                </td>
                <td class="text-center">
                  <div class="d-flex justify-content-center gap-2">
                    <a href="<?= base_url('Employee/updateLeaveStatus/' . ($leave['seemrq_id'] ?? '') . '/approved') ?>"
                       class="action-approve" title="Approve">
                      <i class="fas fa-check"></i>
                    </a>
                    <a href="<?= base_url('Employee/updateLeaveStatus/' . ($leave['seemrq_id'] ?? '') . '/rejected') ?>"
                       class="action-reject" title="Reject">
                      <i class="fas fa-times"></i>
                    </a>
                  </div>
                </td>
              </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="6" class="text-center py-4" style="color:var(--text-muted)">
                  <i class="fas fa-inbox fa-2x mb-2 d-block" style="opacity:.25"></i>
                  No pending leave requests.
                </td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

     

    <!-- ④ QUICK ACTIONS -->
    <!-- <div class="panel">
      <div class="panel-head">
        <h6>
          <span class="ph-icon"><i class="fas fa-bolt"></i></span>
          Quick Actions
        </h6>
      </div>

      <a href="<?= base_url('Employee/RegisterEmployee') ?>" class="shortcut-btn">
        <span class="s-icon" style="--si-bg:#d1fae5;--si-color:#059669"><i class="fas fa-user-plus"></i></span>
        Add New Employee
      </a>
      <a href="<?= base_url('Employee/viewJobApplicants') ?>" class="shortcut-btn">
        <span class="s-icon" style="--si-bg:#ede9fe;--si-color:#461bb9"><i class="fas fa-user-tie"></i></span>
        Review Candidates
        <?php if ($new_applicants > 0): ?>
          <span class="s-badge"><?= $new_applicants ?></span>
        <?php endif; ?>
      </a>
      <a href="<?= base_url('Employee/viewEmployeeLeaveRequests') ?>" class="shortcut-btn">
        <span class="s-icon" style="--si-bg:#fee2e2;--si-color:#dc2626"><i class="fas fa-calendar-check"></i></span>
        Leave Requests
        <?php if ($pending_count > 0): ?>
          <span class="s-badge" style="background:#dc2626"><?= $pending_count ?></span>
        <?php endif; ?>
      </a>
      <a href="<?= base_url('Employee/viewJobs') ?>" class="shortcut-btn">
        <span class="s-icon" style="--si-bg:#dbeafe;--si-color:#2563eb"><i class="fas fa-list-alt"></i></span>
        Manage Jobs
      </a>
      <a href="<?= base_url('Employee/salaryManagement') ?>" class="shortcut-btn">
        <span class="s-icon" style="--si-bg:#fef3c7;--si-color:#d97706"><i class="fas fa-file-invoice-dollar"></i></span>
        Salary Setup
      </a>
      <a href="<?= base_url('Employee/viewEmployee') ?>" class="shortcut-btn">
        <span class="s-icon" style="--si-bg:#ede9fe;--si-color:#461bb9"><i class="fas fa-users-cog"></i></span>
        Manage Employees
      </a>
    </div> -->

  </div><!-- /dash-grid -->

</div><!-- /main-content -->

<!-- ══ LEAVE DETAIL MODAL ══ -->
<div class="modal fade" id="leaveDetailModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg" style="border-radius:20px;overflow:hidden">
      <div class="modal-header" style="background:linear-gradient(135deg,var(--brand-600),var(--brand-500));border:none">
        <h5 class="modal-title fw-bold" style="color:#fff" id="modalEmpName">Employee Name</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" style="padding:24px">
        <div class="mb-3">
          <label class="modal-field-label">Reason Type</label>
          <p id="modalReason" class="mb-0 fw-semibold" style="color:var(--text-primary)"></p>
        </div>
        <div class="mb-3">
          <label class="modal-field-label">Duration</label>
          <p class="mb-0" style="color:var(--text-primary)">
            <i class="far fa-calendar-alt me-2" style="color:var(--brand-400)"></i>
            <span id="modalDates"></span>
          </p>
        </div>
        <hr style="opacity:.1">
        <div>
          <label class="modal-field-label">Request Summary</label>
          <div id="modalSummary" style="background:var(--surface-2);border:1px solid var(--border);border-radius:var(--r-md);padding:14px;min-height:90px;line-height:1.65;color:var(--text-secondary);margin-top:8px;font-size:.875rem"></div>
        </div>
      </div>
      <div class="modal-footer" style="border:none;padding:16px 24px">
        <button type="button" class="btn-brand" data-bs-dismiss="modal" style="padding:9px 24px">Close</button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  function showLeaveDetails(name, reason, summary, from, to) {
    let cleanSummary = summary;
    try {
      if (summary.startsWith('"') && summary.endsWith('"')) {
        cleanSummary = JSON.parse(summary);
      }
    } catch(e) { cleanSummary = summary; }
    document.getElementById('modalEmpName').innerText = name;
    document.getElementById('modalReason').innerText  = reason;
    document.getElementById('modalDates').innerText   = from + ' → ' + to;
    document.getElementById('modalSummary').innerText = cleanSummary || 'No additional details provided.';
    new bootstrap.Modal(document.getElementById('leaveDetailModal')).show();
  }
</script>
</body>
</html>
