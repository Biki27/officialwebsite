<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HR Portal | Suropriyo Enterprise</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url() ?>css/hr/hrHeaderView.css">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>

    <button class="mobile-toggle" id="mobileToggle">
        <i class="fas fa-bars"></i>
    </button>

    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <div class="sidebar" id="sidebar">
        <div class="logo text-center border-bottom mb-4 pb-3">
            <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 60px; height: 60px;">
                <!-- <i class="fas fa-user-tie"></i> -->
                <img src="<?= base_url() ?>imgs/logo-without-bg.png" alt="Suropriyo Logo" class="img-fluid" style="max-width: 50px;">
        </div>
            <h5 class="fw-bold text-primary">HR Portal</h5>
        </div>
        <nav class="nav flex-column">
            <a class="nav-link <?= ($this->uri->segment(2) == 'Dashboard') ? 'active' : '' ?>"
                href="<?= base_url('Employee/Dashboard') ?>">
                <i class="fas fa-th-large me-2"></i> Overview
            </a>
            <a class="nav-link <?= ($this->uri->segment(2) == 'viewEmployee') ? 'active' : '' ?>"
                href="<?= base_url('Employee/viewEmployee') ?>">
                <i class="fas fa-users-cog me-2"></i> Employees
            </a>
            <a class="nav-link <?= ($this->uri->segment(2) == 'viewAttendance') ? 'active' : '' ?>"
                href="<?= base_url('Employee/viewAttendance') ?>">
                <i class="fas fa-calendar-check me-2"></i> Attendance
            </a>
            <a class="nav-link <?= ($this->uri->segment(2) == 'viewJobApplicants') ? 'active' : '' ?>"
                href="<?= base_url('Employee/viewJobApplicants') ?>">
                <i class="fas fa-briefcase me-2"></i> Recruitment
            </a>
            <a class="nav-link <?= ($this->uri->segment(2) == 'RegisterEmployee') ? 'active' : '' ?>"
                href="<?= base_url('Employee/RegisterEmployee') ?>">
                <i class="fas fa-user-plus me-2"></i> Add Employee
            </a>
            <!-- leave management -->
             <a class="nav-link <?= ($this->uri->segment(2) == 'viewEmployeeLeaveRequests') ? 'active' : '' ?>"
                href="<?= base_url('Employee/viewEmployeeLeaveRequests') ?>">
                <i class="fas fa-plane-departure me-2"></i> Leave Management
            </a>
            <a class="nav-link <?= ($this->uri->segment(2) == 'viewJobs') ? 'active' : '' ?>"
                href="<?= base_url('Employee/viewJobs') ?>">
                <i class="fas fa-list-alt me-2"></i> Manage Jobs
            </a>
            <a href="<?= base_url('Employee/incrementReport') ?>"
                class="nav-link <?= ($this->uri->segment(2) == 'incrementReport') ? 'active' : '' ?>">
                <i class="fas fa-chart-pie me-2"></i>Increment Report
            </a>
            <a href="<?= base_url('Employee/BonusReportView') ?>"
                class="nav-link <?= ($this->uri->segment(2) == 'BonusReportView') ? 'active' : '' ?>">
                <i class="fas fa-gift me-2"></i> Bonus Management
            </a>
            <a class="nav-link <?= ($this->uri->segment(2) == 'salaryManagement') ? 'active' : '' ?>"
                href="<?= base_url('Employee/salaryManagement') ?>">
                <i class="fas fa-file-invoice-dollar me-2"></i> Salary Setup
            </a>
            <a class="nav-link " onclick="logout()">
                <i class="fas fa-sign-out-alt me-2"></i> Logout
            </a>
        </nav>

    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const btn = document.getElementById('mobileToggle');
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');

            if (btn && sidebar && overlay) {
                btn.onclick = function () {
                    sidebar.classList.add('active');
                    overlay.classList.add('active');
                    btn.style.display = 'none'; // Hide button when sidebar opens
                };

                overlay.onclick = function () {
                    sidebar.classList.remove('active');
                    overlay.classList.remove('active');
                    btn.style.display = 'flex'; // Show button when sidebar closes
                };
            }
        });
        window.logout = function () {
            Swal.fire({
                title: 'Ready to leave?',
                text: "You will be logged out of the Admin Dashboard.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#461bb9',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Yes, Logout'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '<?= base_url() ?>Employee/logout';
                }
            });
        };
    </script>