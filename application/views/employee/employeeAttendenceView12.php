<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ESS Portal - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('css/employee/employeeAttendance.css') ?>">
</head>

<body>

    <!-- ATTENDANCE SECTION -->
    <div class="container mt-4" id="attendance-section" class="section-content" style="display: block;">
        <!-- ATTENDANCE HEADING WITH RIGHT ALIGN -->
        <!-- <div class="section-header">
            <h3 class="mb-0 fw-semibold text-primary">
                <i class="fas fa-clock me-2"></i>Attendance Summary
            </h3>
            <span class="badge bg-primary fs-6 px-3 py-2">Attendance</span>
        </div> -->
        <div class="mb-4 p-3 rounded-4 shadow-sm text-white"
            style="background: linear-gradient(135deg, #4f46e5, #7c3aed);">
            <h4 class="fw-bold mb-1">
                <i class="fas fa-clock me-2"></i>Attendance Summary

            </h4>
            <small class="opacity-75">Manage your leave and requests</small>
        </div>

        <!-- ATTENDANCE TABLE -->
        <!-- <div class="card p-3">
            <h5 class="fw-semibold mb-3">Attendance History</h5>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Login Time</th>
                            <th>Log Out Time</th>
                        </tr>
                    </thead>
                    <tbody id="attendanceTable">

                        <?php foreach ($attendence as $atdc) { ?>
                            <tr>
                                <td><?= $atdc->seemp_logdate ?></td>
                                <td>
                                    <span class="badge rounded-pill px-3 py-2"
                                        style="background: rgba(127, 255, 112, 0.24); color:#10b981;">
                                        <i class="fas fa-sign-in-alt me-1"></i>
                                        <?= date('h:i A', strtotime($atdc->seemp_logintime)) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($atdc->seemp_logouttime && $atdc->seemp_logouttime != '0000-00-00 00:00:00'): ?>
                                        <span class="badge rounded-pill px-3 py-2"
                                            style="background: rgba(239,68,68,0.1); color:#dc2626;">
                                            <i class="fas fa-sign-out-alt me-1"></i>
                                            <?= date('h:i A', strtotime($atdc->seemp_logouttime)) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="badge rounded-pill px-3 py-2"
                                            style="background: rgba(239,68,68,0.1); color:rgba(6, 6, 6, 0.51);">
                                            <i class="fas fa-sign-out-alt me-1"></i>Not Logged Out
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php } ?>

                    </tbody>
                </table>
            </div>
        </div> -->
        <!-- Change <div class="card p-3"> to this -->
        <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="p-4 border-bottom bg-white">
                <h5 class="fw-bold mb-0">Attendance History</h5>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Date</th>
                            <th>Login Time</th>
                            <th class="pe-4">Log Out Time</th>
                        </tr>
                    </thead>
                    <tbody id="attendanceTable">
                        <?php foreach ($attendence as $atdc) { ?>
                            <tr>
                                <td class="ps-4 fw-medium text-dark"><?= $atdc->seemp_logdate ?></td>
                                <td>
                                    <span class="badge rounded-pill px-3 py-2"
                                        style="background: rgba(16, 185, 129, 0.1); color:#10b981; border: 1px solid rgba(16, 185, 129, 0.2);">
                                        <i class="fas fa-sign-in-alt me-1"></i>
                                        <?= date('h:i A', strtotime($atdc->seemp_logintime)) ?>
                                    </span>
                                </td>
                                <td class="pe-4">
                                    <?php if ($atdc->seemp_logouttime && $atdc->seemp_logouttime != '0000-00-00 00:00:00'): ?>
                                        <span class="badge rounded-pill px-3 py-2"
                                            style="background: rgba(239, 68, 68, 0.1); color:#dc2626; border: 1px solid rgba(239, 68, 68, 0.2);">
                                            <i class="fas fa-sign-out-alt me-1"></i>
                                            <?= date('h:i A', strtotime($atdc->seemp_logouttime)) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="badge rounded-pill px-3 py-2 text-muted"
                                            style="background: #f3f4f6; color:#6b7280; border: 1px solid #e5e7eb;">
                                            <i class="fas fa-user-clock me-1"></i>Not Logged Out
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    </div>

</body>

</html>