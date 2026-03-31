<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('css/employee/employeeHeaderView.css') ?>">

</head>
<nav class="navbar navbar-expand-lg navbar-dark shadow-sm"
    style="background: linear-gradient(90deg, #4f46e5, #7c3aed);">
    <div class="container">

        <a class="navbar-brand fw-semibold" href="#">
            <i class="fas fa-building me-2"></i>
            Suropriyo Enterprise
        </a>

        <div class="ms-auto d-flex align-items-center gap-3">
            <span class="text-white small d-none d-md-block">
                Welcome, <?= $this->session->userdata('empname'); ?>
            </span>

            <a href="<?= base_url() ?>Employee/Logout" class="btn btn-light btn-sm rounded-pill px-3">
                <i class="fas fa-sign-out-alt me-1"></i> Logout
            </a>
        </div>

    </div>
</nav>

<div class="container py-4">

    <!-- HEADER -->
    <div class="card border-0 shadow-lg rounded-4 mb-4">
        <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-3">

            <div>
                <h4 class="fw-bold text-primary mb-1">
                    <i class="fas fa-user-tie me-2"></i>
                    Welcome <?= $this->session->userdata('empname'); ?>,
                </h4>
                <small class="text-muted">Manage your employee dashboard</small>
            </div>

            <a href="<?= base_url() ?>Employee/ChangePassword" class="btn btn-warning rounded-pill px-4"
                data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                <i class="fas fa-key me-1"></i> Change Password
            </a>

        </div>
    </div>

    <!-- NAVIGATION -->
    <div class="card border-0 shadow-sm rounded-4 p-3">
        <div class="d-flex justify-content-center flex-wrap gap-3">

            <a href="<?= base_url() ?>Employee/EmployeeOverview"
                class="btn btn-outline-primary rounded-pill px-4 nav-section-btn">
                <i class="fas fa-home me-2"></i>Overview
            </a>

            <a href="<?= base_url() ?>Employee/EmployeeAttendence"
                class="btn btn-outline-primary rounded-pill px-4 nav-section-btn">
                <i class="fas fa-clock me-2"></i>Attendance
            </a>

            <a href="<?= base_url() ?>Employee/EmployeeRequest"
                class="btn btn-outline-primary rounded-pill px-4 nav-section-btn">
                <i class="fas fa-paper-plane me-2"></i>Requests
            </a>

        </div>
    </div>
    <div class="modal fade" id="changePasswordModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content rounded-4 shadow">

                <div class="modal-header">
                    <h5 class="modal-title fw-semibold">
                        <i class="fas fa-key me-2"></i>Change Password
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <?= form_open('Employee/ChangePassword') ?>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Current Password</label>
                        <input name="oldpass" type="password" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">New Password</label>
                        <input name="newpass" type="password" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Confirm Password</label>
                        <input name="confirmpass" type="password" class="form-control" required>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 rounded-pill">
                        Update Password
                    </button>

                    <?= form_close() ?>

                </div>

            </div>
        </div>
    </div>

</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>