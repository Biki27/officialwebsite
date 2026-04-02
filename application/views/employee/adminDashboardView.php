<?php
defined('BASEPATH') or exit('No direct script access allowed');

/* ── Calculations ── */
$total_all_projects = $projpending + $projrunning + $projcompleted;
$completion_rate    = ($total_all_projects > 0) ? round(($projcompleted / $total_all_projects) * 100) : 0;
$attendance_rate    = ($total_staff > 0) ? round(($present_today / $total_staff) * 100) : 0;
$absent_today       = $total_staff - $present_today;

/* SVG donut math  (r=40 → circumference ≈ 251.2) */
$circ      = 251.2;
$comp_dash = round(($projcompleted / max($total_all_projects,1)) * $circ, 1);
$run_dash  = round(($projrunning   / max($total_all_projects,1)) * $circ, 1);
$pend_dash = round(($projpending  / max($total_all_projects,1)) * $circ, 1);
$comp_offset = 0;
$run_offset  = -$comp_dash;
$pend_offset = -($comp_dash + $run_dash);

/* Attendance donut */
$att_dash   = round(($attendance_rate / 100) * $circ, 1);
$att_remain = $circ - $att_dash;

/*
 * Payroll vars come from salaryManagement():
 *   $processed_count  → Slips Generated  (PAID)
 *   $pending_count    → Pending Processing (UNPAID)
 *   $total_emps       → total employee count used there
 * On the dashboard we reuse $total_staff for total count.
 * Guard with isset() so the dashboard never crashes if controller
 * hasn't been updated yet.
 */
$paid_count   = isset($processed_count) ? $processed_count : '—';
$unpaid_count = isset($pending_count)   ? $pending_count   : '—';

/*
 * Leave vars — add these to AdminDashboard() in Employee.php:
 *   $data['leave_pending']  = $this->RequestsModel->get_pending_requests_count();
 *   $data['leave_approved'] = $this->RequestsModel->get_approved_leaves_count();  // optional
 * Guard here so view never crashes:
 */
$leave_pending  = isset($leave_pending)  ? $leave_pending  : '—';
$leave_approved = isset($leave_approved) ? $leave_approved : '—';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard | Suropriyo Enterprise</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= base_url() ?>css/admin/adminDashboardView.css">
</head>
<body>

<div class="main-content">

  <!-- ══════════════ PAGE HEADER ══════════════ -->
  <div class="page-header">
    <div>
      <h1>Enterprise <span>Command Center</span></h1>
      <div class="page-subtitle">
        <span class="badge-live">Operational</span>
        <?= date('l, d M Y') ?>
      </div>
    </div>
    <a href="<?= base_url('Employee/addProjectPage') ?>" class="btn-brand">
      <i class="fas fa-plus-circle"></i> Initialize Project
    </a>
  </div>

  <!-- ══════════════ KPI CARDS ══════════════ -->
  <div class="kpi-grid">

    <!-- Active Projects -->
    <div class="kpi-card v-purple">
      <div class="kpi-icon"><i class="fas fa-rocket"></i></div>
      <div class="kpi-label">Active Projects</div>
      <div class="kpi-value"><?= $projrunning ?></div>
      <div class="kpi-meta info"><i class="fas fa-check-circle me-1"></i><?= $completion_rate ?>% overall completed</div>
      <div class="kpi-track"><div class="kpi-track-fill" style="width:<?= $completion_rate ?>%"></div></div>
    </div>

    <!-- On-Site Staff -->
    <div class="kpi-card v-green">
      <div class="kpi-icon"><i class="fas fa-users"></i></div>
      <div class="kpi-label">On-Site Today</div>
      <div class="kpi-value"><?= $present_today ?><span style="font-size:1rem;font-weight:500;color:var(--text-muted)"> /<?= $total_staff ?></span></div>
      <div class="kpi-meta up"><i class="fas fa-chart-line me-1"></i><?= $attendance_rate ?>% workforce active</div>
      <div class="kpi-track"><div class="kpi-track-fill" style="width:<?= $attendance_rate ?>%"></div></div>
    </div>

    <!-- Pending Projects -->
    <div class="kpi-card v-amber">
      <div class="kpi-icon"><i class="fas fa-hourglass-half"></i></div>
      <div class="kpi-label">Pending Projects</div>
      <div class="kpi-value"><?= $projpending ?></div>
      <div class="kpi-meta warn"><i class="fas fa-clock me-1"></i>Awaiting kickoff</div>
    </div>

    <!-- New Applicants -->
    <div class="kpi-card v-blue">
      <div class="kpi-icon"><i class="fas fa-user-tie"></i></div>
      <div class="kpi-label">New Applicants</div>
      <div class="kpi-value"><?= $new_apps ?></div>
      <div class="kpi-meta info"><i class="fas fa-envelope me-1"></i>Pending review</div>
    </div>

    <!-- Leave Requests (KPI) -->
    <div class="kpi-card v-red">
      <div class="kpi-icon"><i class="fas fa-calendar-times"></i></div>
      <div class="kpi-label">Leave Requests</div>
      <div class="kpi-value"><?= $leave_pending ?></div>
      <div class="kpi-meta down"><i class="fas fa-exclamation-circle me-1"></i>Pending approval</div>
    </div>

  </div><!-- /kpi-grid -->

  <!-- ══════════════ PANEL GRID ══════════════ -->
  <div class="dash-grid">

    <!-- ① PROJECT STATUS -->
    <div class="panel">
      <div class="panel-head">
        <h6>
          <span class="ph-icon" style="--ph-bg:#ede9fe;--ph-color:#461bb9"><i class="fas fa-project-diagram"></i></span>
          Project Status
        </h6>
        <a href="<?= base_url('Employee/viewProjects') ?>">View All →</a>
      </div>

      <div class="proj-donut-wrap">
        <svg class="donut-svg" viewBox="0 0 100 100">
          <circle cx="50" cy="50" r="40" fill="none" stroke="#eee" stroke-width="13"/>
          <!-- Pending -->
          <circle cx="50" cy="50" r="40" fill="none" stroke="#f59e0b" stroke-width="13"
            stroke-dasharray="<?= $pend_dash ?> <?= $circ-$pend_dash ?>"
            stroke-dashoffset="<?= $pend_offset ?>" stroke-linecap="round"
            style="transform:rotate(-90deg);transform-origin:center"/>
          <!-- Running -->
          <circle cx="50" cy="50" r="40" fill="none" stroke="#3b82f6" stroke-width="13"
            stroke-dasharray="<?= $run_dash ?> <?= $circ-$run_dash ?>"
            stroke-dashoffset="<?= $run_offset ?>" stroke-linecap="round"
            style="transform:rotate(-90deg);transform-origin:center"/>
          <!-- Completed -->
          <circle cx="50" cy="50" r="40" fill="none" stroke="#10b981" stroke-width="13"
            stroke-dasharray="<?= $comp_dash ?> <?= $circ-$comp_dash ?>"
            stroke-dashoffset="<?= $comp_offset ?>" stroke-linecap="round"
            style="transform:rotate(-90deg);transform-origin:center"/>
          <text x="50" y="46" text-anchor="middle" font-size="13" font-weight="800"
            fill="#1a1340" font-family="Plus Jakarta Sans,sans-serif"><?= $total_all_projects ?></text>
          <text x="50" y="58" text-anchor="middle" font-size="7"
            fill="#7c82aa" font-family="Plus Jakarta Sans,sans-serif">TOTAL</text>
        </svg>
        <div class="legend-list">
          <div class="legend-row">
            <span class="l-name"><span class="legend-dot" style="background:#10b981"></span>Completed</span>
            <span class="l-val"><?= $projcompleted ?></span>
          </div>
          <div class="legend-row">
            <span class="l-name"><span class="legend-dot" style="background:#3b82f6"></span>Running</span>
            <span class="l-val"><?= $projrunning ?></span>
          </div>
          <div class="legend-row">
            <span class="l-name"><span class="legend-dot" style="background:#f59e0b"></span>Pending</span>
            <span class="l-val"><?= $projpending ?></span>
          </div>
        </div>
      </div>

      <div class="comp-bar-wrap">
        <div class="label-row">
          <span>Completion Rate</span>
          <span><?= $completion_rate ?>%</span>
        </div>
        <div class="comp-bar"><div class="comp-bar-fill" style="width:<?= $completion_rate ?>%"></div></div>
      </div>
    </div>

    <!-- ② ATTENDANCE SNAPSHOT -->
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

    <!-- ③ LEAVE REQUESTS -->
    <!-- <div class="panel" style="border-top:3px solid #dc2626">
      <div class="panel-head">
        <h6>
          <span class="ph-icon" style="--ph-bg:#fee2e2;--ph-color:#dc2626"><i class="fas fa-calendar-times"></i></span>
          Leave Requests
        </h6>
        <a href="<?= base_url('Employee/viewEmployeeLeaveRequests') ?>">Manage →</a>
      </div>

      <div class="leave-stat-grid">
        <div class="leave-box">
          <div class="lb-num" style="color:#dc2626"><?= $leave_pending ?></div>
          <div class="lb-lbl">Pending</div>
        </div>
        <div class="leave-box">
          <div class="lb-num" style="color:#059669"><?= $leave_approved ?></div>
          <div class="lb-lbl">Approved</div>
        </div>
      </div>

       
      <div class="leave-type-row" style="margin-top:4px">
        <a href="<?= base_url('Employee/viewEmployeeLeaveRequests') ?>" class="btn-brand" style="width:100%;justify-content:center;padding:9px;font-size:.82rem;margin-top:10px">
          <i class="fas fa-tasks"></i> Review All Requests
        </a>
      </div>
    </div> -->

    <!-- ④ PAYROLL SUMMARY -->
    <div class="panel">
      <div class="panel-head">
        <h6>
          <span class="ph-icon" style="--ph-bg:#dbeafe;--ph-color:#2563eb"><i class="fas fa-file-invoice-dollar"></i></span>
          Payroll — <?= date('F Y') ?>
        </h6>
        <a href="<?= base_url('Employee/salaryManagement') ?>">Manage →</a>
      </div>

      <div class="payroll-row">
        <span class="pr-label">Total Employees</span>
        <span class="pr-val"><?= $total_staff ?></span>
      </div>
      <div class="payroll-row">
        <span class="pr-label">
          <i class="fas fa-check-circle me-1" style="color:var(--success)"></i>
          Slips Generated (Paid)
        </span>
        <span class="pr-val green"><?= $paid_count ?></span>
      </div>
      <div class="payroll-row">
        <span class="pr-label">
          <i class="fas fa-hourglass-half me-1" style="color:var(--warning)"></i>
          Pending Processing
        </span>
        <span class="pr-val amber"><?= $unpaid_count ?></span>
      </div>
      <div class="payroll-row">
        <span class="pr-label">Period</span>
        <span class="pr-val"><?= date('F Y') ?></span>
      </div>

      <a href="<?= base_url('Employee/salaryManagement') ?>" class="btn-payroll">
        <i class="fas fa-paper-plane"></i> Process Payroll
      </a>
    </div>

    <!-- ⑤ UPCOMING DEADLINES -->
    <div class="panel" style="border-top:3px solid #d97706">
      <div class="panel-head">
        <h6>
          <span class="ph-icon" style="--ph-bg:#fef3c7;--ph-color:#d97706"><i class="fas fa-exclamation-triangle"></i></span>
          Upcoming Deadlines
        </h6>
      </div>

      <?php if (!empty($deadlines)): ?>
        <?php foreach ($deadlines as $dl):
          $days_left   = (int) date_diff(date_create(), date_create($dl->seproj_deadline))->format('%r%a');
          $badge_class = ($days_left <= 0) ? 'danger' : (($days_left <= 3) ? 'warning' : 'safe');
          $badge_label = ($days_left < 0) ? 'Overdue' : ($days_left === 0 ? 'Due today' : $days_left . 'd left');
        ?>
          <div class="deadline-item">
            <div>
              <div class="dl-name"><?= htmlspecialchars($dl->seproj_name) ?></div>
              <div class="dl-date"><i class="far fa-calendar-alt"></i><?= date('d M, Y', strtotime($dl->seproj_deadline)) ?></div>
            </div>
            <span class="dl-badge <?= $badge_class ?>"><?= $badge_label ?></span>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p style="font-size:.82rem;color:var(--text-muted);padding:8px 0">All project deadlines are clear.</p>
      <?php endif; ?>
    </div>

    <!-- ⑥ QUICK ACTIONS -->
    <!-- <div class="panel">
      <div class="panel-head">
        <h6>
          <span class="ph-icon"><i class="fas fa-bolt"></i></span>
          Quick Actions
        </h6>
      </div>

      <a href="<?= base_url('Employee/salaryManagement') ?>" class="shortcut-btn">
        <span class="s-icon" style="--si-bg:#dbeafe;--si-color:#2563eb"><i class="fas fa-file-invoice-dollar"></i></span>
        Process Payroll
      </a>
      <a href="<?= base_url('Employee/viewJobApplicants') ?>" class="shortcut-btn">
        <span class="s-icon" style="--si-bg:#ede9fe;--si-color:#461bb9"><i class="fas fa-user-tie"></i></span>
        Review Candidates
        <?php if ($new_apps > 0): ?>
          <span class="s-badge"><?= $new_apps ?></span>
        <?php endif; ?>
      </a>
      <a href="<?= base_url('Employee/RegisterEmployee') ?>" class="shortcut-btn">
        <span class="s-icon" style="--si-bg:#d1fae5;--si-color:#059669"><i class="fas fa-user-plus"></i></span>
        Add Employee
      </a>
      <a href="<?= base_url('Employee/viewEmployee') ?>" class="shortcut-btn">
        <span class="s-icon" style="--si-bg:#fef3c7;--si-color:#d97706"><i class="fas fa-users-cog"></i></span>
        Manage Employees
      </a>
      <a href="<?= base_url('Employee/products') ?>" class="shortcut-btn">
        <span class="s-icon" style="--si-bg:#fee2e2;--si-color:#dc2626"><i class="fas fa-box"></i></span>
        Products
      </a>
      <a href="<?= base_url('Employee/viewEmployeeLeaveRequests') ?>" class="shortcut-btn">
        <span class="s-icon" style="--si-bg:#e0f2fe;--si-color:#0284c7"><i class="fas fa-calendar-check"></i></span>
        Leave Requests
        <?php if (is_numeric($leave_pending) && $leave_pending > 0): ?>
          <span class="s-badge" style="background:#dc2626"><?= $leave_pending ?></span>
        <?php endif; ?>
      </a>
    </div> -->

  </div><!-- /dash-grid -->

</div><!-- /main-content -->

</body>
</html>
