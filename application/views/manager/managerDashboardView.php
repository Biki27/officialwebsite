<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/* ── Calculations ── */
$total_staff = isset($total_staff) ? $total_staff : 0;
$present_today = isset($present_today) ? $present_today : 0;
$absent_today = $total_staff - $present_today;
$attendance_rate = ($total_staff > 0) ? round(($present_today / $total_staff) * 100) : 0;
$att_circ = 251.2;
$att_dash = round(($attendance_rate / 100) * $att_circ, 1);
$att_remain = $att_circ - $att_dash;

$running_projects = isset($running_projects) ? $running_projects : 0;
$pending_projects = isset($pending_projects) ? $pending_projects : 0;
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manager Dashboard | Suropriyo Enterprise</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
    rel="stylesheet">
  <link rel="stylesheet" href="<?= base_url() ?>css/manager/managerDashboardView.css">
</head>

<body>

  <div class="main-content">

    <!-- ══ PAGE HEADER ══ -->
    <div class="page-header">
      <div>
        <h1><?= htmlspecialchars($branch_name) ?> <span>Branch Dashboard</span></h1>
        <div class="page-subtitle">
          <span class="badge-live">Active</span>
          <?= date('l, d M Y') ?>
        </div>
      </div>
      <a href="<?= base_url('Manager/RegisterEmployee') ?>" class="btn-brand">
        <i class="fas fa-user-plus"></i> Add Branch Employee
      </a>
    </div>

    <!-- ══ KPI CARDS ══ -->
    <div class="kpi-grid">

      <div class="kpi-card v-purple">
        <div class="kpi-icon"><i class="fas fa-users"></i></div>
        <div class="kpi-label">Branch Staff</div>
        <div class="kpi-value"><?= $total_staff ?></div>
        <div class="kpi-meta info"><i class="fas fa-map-marker-alt me-1"></i><?= htmlspecialchars($branch_name) ?>
          Location</div>
      </div>

      <div class="kpi-card v-green">
        <div class="kpi-icon"><i class="fas fa-user-check"></i></div>
        <div class="kpi-label">Present Today</div>
        <div class="kpi-value"><?= $present_today ?><span
            style="font-size:1rem;font-weight:500;color:var(--text-muted)"> /<?= $total_staff ?></span></div>
        <div class="kpi-meta up"><i class="fas fa-chart-line me-1"></i><?= $attendance_rate ?>% attendance</div>
      </div>

      <div class="kpi-card v-red">
        <div class="kpi-icon"><i class="fas fa-user-times"></i></div>
        <div class="kpi-label">Absent Today</div>
        <div class="kpi-value"><?= $absent_today ?></div>
        <div class="kpi-meta warn"><i class="fas fa-clock me-1"></i>Branch absences</div>
      </div>

      <div class="kpi-card v-blue">
        <div class="kpi-icon"><i class="fas fa-spinner fa-spin"></i></div>
        <div class="kpi-label">Running Projects</div>
        <div class="kpi-value"><?= $running_projects ?></div>
        <div class="kpi-meta info"><i class="fas fa-project-diagram me-1"></i>Active development</div>
      </div>

    </div><!-- /kpi-grid -->

    <!-- ══ DASH PANELS ══ -->
    <div class="dash-grid">

      <!-- Attendance Panel -->
      <div class="panel">
        <div class="panel-head">
          <h6>
            <span class="ph-icon" style="--ph-bg:#d1fae5;--ph-color:#059669">
              <i class="fas fa-calendar-check"></i>
            </span>
            Branch Attendance
          </h6>
          <!-- FIXED: was Employee/viewAttendance -->
          <a href="<?= base_url('Manager/viewAttendance') ?>">Search Records →</a>
        </div>

        <div class="att-donut-wrap">
          <svg viewBox="0 0 100 100" style="width:82px;height:82px;flex-shrink:0">
            <circle cx="50" cy="50" r="40" fill="none" stroke="#fee2e2" stroke-width="13" />
            <circle cx="50" cy="50" r="40" fill="none" stroke="#10b981" stroke-width="13"
              stroke-dasharray="<?= $att_dash ?> <?= $att_remain ?>" stroke-dashoffset="0" stroke-linecap="round"
              style="transform:rotate(-90deg);transform-origin:center" />
            <text x="50" y="46" text-anchor="middle" font-size="13" font-weight="800" fill="#1a1340"
              font-family="Plus Jakarta Sans,sans-serif"><?= $attendance_rate ?>%</text>
            <text x="50" y="58" text-anchor="middle" font-size="7" fill="#7c82aa"
              font-family="Plus Jakarta Sans,sans-serif">PRESENT</text>
          </svg>
          <div class="att-stat">
            <div class="att-row">
              <span class="ar-label"><i class="fas fa-circle me-1"
                  style="color:#10b981;font-size:.55rem"></i>Present</span>
              <span class="ar-val" style="color:#059669"><?= $present_today ?></span>
            </div>
            <div class="att-row">
              <span class="ar-label"><i class="fas fa-circle me-1"
                  style="color:#f87171;font-size:.55rem"></i>Absent</span>
              <span class="ar-val" style="color:#dc2626"><?= $absent_today ?></span>
            </div>
          </div>
        </div>

        <div class="workforce-box">
          <span class="wb-label"><i class="fas fa-map-marker-alt me-1"></i><?= htmlspecialchars($branch_name) ?>
            Workforce</span>
          <span class="wb-val"><?= $total_staff ?> <span
              style="font-size:.82rem;font-weight:500;color:var(--brand-400)">staff</span></span>
        </div>
      </div>

      <!-- Quick Links Panel -->
      <div class="dash-panel mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h5 class="mb-0 fw-bold"><i class="fas fa-link me-2 text-primary"></i>Manager Quick Actions</h5>
        </div>

        <div class="row g-3">
          <div class="col-md-6 col-lg-4">
            <a href="<?= base_url('Manager/viewEmployee') ?>" class="text-decoration-none">
              <div class="quick-link-box">
                <div class="ql-icon-wrapper bg-soft-purple">
                  <i class="fas fa-users"></i>
                </div>
                <div class="ql-content">
                  <span class="ql-title">My Branch Staff</span>
                  <span class="ql-subtitle">View & Manage <?= htmlspecialchars($branch_name) ?> Employees</span>
                </div>
                <i class="fas fa-chevron-right ql-arrow"></i>
              </div>
            </a>
          </div>

          <div class="col-md-6 col-lg-4">
            <a href="<?= base_url('Manager/viewProjects') ?>" class="text-decoration-none">
              <div class="quick-link-box">
                <div class="ql-icon-wrapper bg-soft-blue">
                  <i class="fas fa-project-diagram"></i>
                </div>
                <div class="ql-content">
                  <span class="ql-title">Manage Projects</span>
                  <span class="ql-subtitle"><?= $pending_projects ?> Tasks Pending</span>
                </div>
                <i class="fas fa-chevron-right ql-arrow"></i>
              </div>
            </a>
          </div>
        </div>
      </div><!-- /quick links panel -->

    </div><!-- /dash-grid -->
  </div><!-- /main-content -->

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>