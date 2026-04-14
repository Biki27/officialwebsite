<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="<?= base_url('css/admin/adminHeaderView.css') ?>" rel="stylesheet">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>

<body>
    <!-- MOBILE TOGGLE BUTTON -->
    <button class="mobile-toggle" id="mobileToggle">
        <i class="fas fa-bars"></i>
    </button>

    <!-- MOBILE OVERLAY -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo">
            <div class="logo-icon rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 60px; height: 60px;">
                <!-- <i class="fas fa-building"></i> -->
                 <img src="<?= base_url() ?>imgs/logo-without-bg.png" alt="Suropriyo Logo" class="img-fluid" style="max-width: 50px;">
            </div>
            <h5>Suropriyo Enterprise</h5>
        </div>
        <nav class="nav flex-column">
            <a class="nav-link <?= ($this->uri->segment(2) == 'Dashboard') ? 'active' : '' ?>" href="<?= base_url() ?>Employee/Dashboard">
                <i class="fas fa-chart-line"></i> Overview
            </a>
            <a class="nav-link <?= ($this->uri->segment(2) == 'viewEmployee') ? 'active' : '' ?>" 
                href="<?= base_url() ?>Employee/viewEmployee">
                <i class="fas fa-users"></i> Employees
            </a>
            <a class="nav-link <?= ($this->uri->segment(2) == 'viewAttendance') ? 'active' : '' ?>" href="<?= base_url() ?>Employee/viewAttendance">
                <i class="fas fa-calendar-alt"></i> Attendance
            </a>
            <!-- add add employee -->
            <a class="nav-link <?= ($this->uri->segment(2) == 'addEmployee') ? 'active' : '' ?>" href="<?= base_url() ?>Employee/registerEmployee">
                <i class="fas fa-user-plus"></i> Add Employee
            </a>
            <!-- increment report -->
              <a href="<?= base_url('Employee/incrementReport') ?>"
                class="nav-link <?= ($this->uri->segment(2) == 'incrementReport') ? 'active' : '' ?>">
                <i class="fas fa-chart-pie me-2"></i>Increment Report
            </a>
            <!-- bonus management -->
            <a href="<?= base_url('Employee/BonusReportView') ?>"
                class="nav-link <?= ($this->uri->segment(2) == 'BonusReportView') ? 'active' : '' ?>">
                <i class="fas fa-gift me-2"></i> Bonus Report
            </a>

            <a class="nav-link <?= ($this->uri->segment(2) == 'viewProjects') ? 'active' : '' ?>" href="<?= base_url() ?>Employee/viewProjects">
                <i class="fas fa-project-diagram"></i> Projects
            </a>
            <a class="nav-link <?= ($this->uri->segment(2) == 'products') ? 'active' : '' ?>" href="<?= base_url() ?>Employee/products">
                <i class="fas fa-box"></i> Product
            </a>
            <a class="nav-link" onclick="logout()">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </nav>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const btn = document.getElementById('mobileToggle');
            const sidebar = document.querySelector('.sidebar');
            const overlay = document.getElementById('sidebarOverlay');

            if (btn && sidebar && overlay) {
                btn.addEventListener('click', function () {
                    sidebar.classList.toggle('active');
                    overlay.classList.toggle('active');
                    btn.style.display = 'none';
                });

                overlay.addEventListener('click', function () {
                    sidebar.classList.remove('active');
                    overlay.classList.remove('active');
                    btn.style.display = 'flex';
                });
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