<?php
defined('BASEPATH') or exit('No direct script access allowed');

// Calculate Overall Company Project Completion Rate
$total_all_projects = $projpending + $projrunning + $projcompleted;
$completion_rate = ($total_all_projects > 0) ? round(($projcompleted / $total_all_projects) * 100) : 0;
$attendance_rate = ($total_staff > 0) ? round(($present_today / $total_staff) * 100) : 0; 
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard | Supropriyo Enterprise</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= base_url() ?>css/admin/adminDashboardView.css">
</head>

<body>

  <div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-5">
      <div class="welcome-text">
        <h1 class="fw-bold text-white">Enterprise Command Center</h1>
        <p class="text-white-50">
            <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-50 me-2">
                <i class="fas fa-check-circle me-1"></i>System Operational
            </span> 
            <?= date('l, d M Y') ?>
        </p>
      </div>
      <div class="quick-actions">
        <a href="<?= base_url('Employee/addProjectPage') ?>" class="btn btn-light rounded-pill px-4 fw-bold text-primary shadow-sm">
          <i class="fas fa-plus me-2"></i>New Project
        </a>
      </div>
    </div>

    <div class="row g-4 mb-5">
      <div class="col-xl-3 col-md-6">
        <div class="stat-card glass-card h-100">
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <p class="text-uppercase small fw-bold text-white-50 mb-1">On-Site Staff</p>
              <h2 class="text-white mb-0 fw-bold"><?= $present_today ?><span class="fs-5 text-white-50">/<?= $total_staff ?></span></h2>
            </div>
            <div class="icon-box bg-success-soft"><i class="fas fa-users text-success"></i></div>
          </div>
          <p class="text-success small mb-0 mt-3 fw-medium"><i class="fas fa-chart-line me-1"></i> <?= $attendance_rate ?>% Workforce Active</p>
        </div>
      </div>

      <div class="col-xl-3 col-md-6">
        <div class="stat-card glass-card h-100">
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <p class="text-uppercase small fw-bold text-white-50 mb-1">Running Projects</p>
              <h2 class="text-white mb-0 fw-bold"><?= $projrunning ?></h2>
            </div>
            <div class="icon-box bg-primary-soft"><i class="fas fa-rocket text-primary"></i></div>
          </div>
          <div class="progress mt-3 bg-dark bg-opacity-25" style="height: 5px;">
            <div class="progress-bar bg-primary" style="width: <?= $completion_rate ?>%"></div>
          </div>
          <p class="text-primary small mb-0 mt-2 fw-medium"><?= $completion_rate ?>% Company Completion</p>
        </div>
      </div>

      <div class="col-xl-3 col-md-6">
        <div class="stat-card glass-card h-100">
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <p class="text-uppercase small fw-bold text-white-50 mb-1">Completed</p>
              <h2 class="text-white mb-0 fw-bold"><?= $projcompleted ?></h2>
            </div>
            <div class="icon-box bg-info bg-opacity-10"><i class="fas fa-check-double text-info"></i></div>
          </div>
          <p class="text-info small mb-0 mt-3 fw-medium"><i class="fas fa-history me-1"></i> Lifetime total</p>
        </div>
      </div>

      <div class="col-xl-3 col-md-6">
        <div class="stat-card glass-card h-100">
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <p class="text-uppercase small fw-bold text-white-50 mb-1">New Applicants</p>
              <h2 class="text-white mb-0 fw-bold"><?= $new_apps ?></h2>
            </div>
            <div class="icon-box bg-warning bg-opacity-10"><i class="fas fa-user-tie text-warning"></i></div>
          </div>
          <a href="<?= base_url('Employee/viewJobApplicants') ?>" class="text-warning small text-decoration-none mt-3 d-inline-block fw-medium">
            Review Candidates <i class="fas fa-arrow-right ms-1"></i>
          </a>
        </div>
      </div>
    </div>

    <div class="row g-4">
      
      <div class="col-lg-6">
        <div class="glass-card h-100">
          <h5 class="text-white mb-4"><i class="fas fa-bolt text-warning me-2"></i>Quick Action Center</h5>
          
          <div class="row g-3">
            <div class="col-md-6">
                <a href="<?= base_url('Employee/salaryManagement') ?>" class="text-decoration-none">
                    <div class="p-3 border border-white border-opacity-10 rounded-3 bg-white bg-opacity-10 hover-lift transition-all">
                        <i class="fas fa-file-invoice-dollar fs-3 text-success mb-2"></i>
                        <h6 class="text-white mb-1">Process Payroll</h6>
                        <small class="text-white-50">Manage employee salary & slips</small>
                    </div>
                </a>
            </div>
            
            <div class="col-md-6">
                <a href="<?= base_url('Employee/products') ?>" class="text-decoration-none">
                    <div class="p-3 border border-white border-opacity-10 rounded-3 bg-white bg-opacity-10 hover-lift transition-all">
                        <i class="fas fa-box-open fs-3 text-info mb-2"></i>
                        <h6 class="text-white mb-1">Product Inventory</h6>
                        <small class="text-white-50">Manage enterprise products</small>
                    </div>
                </a>
            </div>

            <div class="col-md-6">
                <a href="<?= base_url('Employee/viewAttendance') ?>" class="text-decoration-none">
                    <div class="p-3 border border-white border-opacity-10 rounded-3 bg-white bg-opacity-10 hover-lift transition-all">
                        <i class="fas fa-calendar-check fs-3 text-primary mb-2"></i>
                        <h6 class="text-white mb-1">Attendance Logs</h6>
                        <small class="text-white-50">View daily clock-ins/outs</small>
                    </div>
                </a>
            </div>

            <div class="col-md-6">
                <a href="<?= base_url('Employee/viewEmployee') ?>" class="text-decoration-none">
                    <div class="p-3 border border-white border-opacity-10 rounded-3 bg-white bg-opacity-10 hover-lift transition-all">
                        <i class="fas fa-id-badge fs-3 text-secondary mb-2"></i>
                        <h6 class="text-white mb-1">Staff Directory</h6>
                        <small class="text-white-50">Manage employee profiles</small>
                    </div>
                </a>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-6">
        <div class="glass-card h-100 border-start border-warning border-4">
          <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="text-white mb-0">Upcoming Deadlines</h5>
            <span class="badge bg-warning text-dark rounded-pill"><?= count($deadlines) ?> Active</span>
          </div>

          <div class="deadline-timeline pe-2" style="max-height: 300px; overflow-y: auto;">
            <?php if (!empty($deadlines)): ?>
              <?php foreach ($deadlines as $dl): 
                 $days_left = date_diff(date_create(), date_create($dl->seproj_deadline))->format('%r%a');
                 $is_urgent = $days_left <= 3 && $days_left >= 0;
                 $is_overdue = $days_left < 0;
                 
                 $border_color = $is_overdue ? 'border-danger' : ($is_urgent ? 'border-warning' : 'border-info');
                 $text_color = $is_overdue ? 'text-danger' : ($is_urgent ? 'text-warning' : 'text-info');
              ?>
                <div class="d-flex mb-3 align-items-stretch">
                    <div class="timeline-indicator d-flex flex-column align-items-center me-3">
                        <div class="rounded-circle border border-2 <?= $border_color ?> bg-dark" style="width: 16px; height: 16px;"></div>
                        <div class="h-100 border-start border-white border-opacity-25 my-1" style="width: 2px;"></div>
                    </div>
                    <div class="timeline-content bg-dark bg-opacity-25 p-3 rounded-3 w-100 border border-white border-opacity-10">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="text-white mb-1"><?= $dl->seproj_name ?></h6>
                                <p class="text-white-50 small mb-0">Client: <?= $dl->seproj_clientid ?></p>
                            </div>
                            <div class="text-end">
                                <strong class="<?= $text_color ?> d-block" style="font-size: 0.9rem;">
                                    <?= $is_overdue ? 'Overdue by ' . abs($days_left) . ' days' : $days_left . ' Days Left' ?>
                                </strong>
                                <small class="text-white-50"><?= date('M d, Y', strtotime($dl->seproj_deadline)) ?></small>
                            </div>
                        </div>
                    </div>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <div class="text-center py-5">
                  <i class="fas fa-calendar-check fa-3x text-white-50 mb-3 opacity-50"></i>
                  <p class="text-white-50">No upcoming project deadlines. You're all clear!</p>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>

    </div>
  </div>

  <style>
      .hover-lift { cursor: pointer; }
      .hover-lift:hover { 
          transform: translateY(-3px); 
          background: rgba(255,255,255,0.15) !important; 
          border-color: rgba(255,255,255,0.3) !important;
      }
      /* Custom scrollbar for timelines */
      .deadline-timeline::-webkit-scrollbar { width: 6px; }
      .deadline-timeline::-webkit-scrollbar-track { background: rgba(0,0,0,0.1); border-radius: 10px; }
      .deadline-timeline::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.2); border-radius: 10px; }
      .deadline-timeline::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.4); }
      .timeline-indicator div:last-child { display: <?= count($deadlines) > 1 ? 'block' : 'none' ?>; }
  </style>
</body>
</html>