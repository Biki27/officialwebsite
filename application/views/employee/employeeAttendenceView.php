<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ESS Portal — Attendance</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('css/employee/employeeAttendance.css') ?>">
</head>

<body>

    <div class="container-xl px-3 px-md-4 mt-2 pb-5" id="attendance-section">

        <!-- Section Banner -->
        <div class="ent-section-banner">
            <h4><i class="fas fa-clock me-2"></i>Attendance Summary</h4>
            <small>Your login &amp; logout records</small>
        </div>

        <!-- Desktop Table -->
        <div class="ent-card attendance-desktop">
            <div class="ent-card-header">
                <div class="card-title-group">
                    <span class="card-section-icon clock"><i class="fas fa-history"></i></span>
                    <div>
                        <div class="card-section-title">Attendance History</div>
                        <div class="card-section-sub">Detailed daily check-in / check-out log</div>
                    </div>
                </div>
                <span class="record-count-badge">
                    <?= count($attendence) ?> Records
                </span>
            </div>
            <div class="atd-table-wrap">
                <table class="atd-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Day</th>
                            <th>Check In</th>
                            <th>Check Out</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $n = 1; foreach ($attendence as $atdc): ?>
                        <tr>
                            <td class="row-num"><?= $n++ ?></td>
                            <td class="fw-semibold"><?= date('d M Y', strtotime($atdc->seemp_logdate)) ?></td>
                            <td style="color:#94a3b8;"><?= date('D', strtotime($atdc->seemp_logdate)) ?></td>
                            <td>
                                <span class="time-badge check-in">
                                    <i class="fas fa-sign-in-alt me-1"></i>
                                    <?= date('h:i A', strtotime($atdc->seemp_logintime)) ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($atdc->seemp_logouttime && $atdc->seemp_logouttime != '0000-00-00 00:00:00'): ?>
                                    <span class="time-badge check-out">
                                        <i class="fas fa-sign-out-alt me-1"></i>
                                        <?= date('h:i A', strtotime($atdc->seemp_logouttime)) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="time-badge pending-out">
                                        <i class="fas fa-user-clock me-1"></i>Pending
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($atdc->seemp_logouttime && $atdc->seemp_logouttime != '0000-00-00 00:00:00'): ?>
                                    <span class="status-dot complete">
                                        <i class="fas fa-circle me-1" style="font-size:0.5rem;"></i>Complete
                                    </span>
                                <?php else: ?>
                                    <span class="status-dot active">
                                        <i class="fas fa-circle me-1" style="font-size:0.5rem;"></i>Active
                                    </span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Mobile Cards -->
        <div class="attendance-mobile">
            <?php $n = 1; foreach ($attendence as $atdc): ?>
            <div class="atd-mobile-card">
                <div class="atd-card-top">
                    <div>
                        <div class="atd-card-date"><?= date('d M Y', strtotime($atdc->seemp_logdate)) ?></div>
                        <div class="atd-card-day"><?= date('l', strtotime($atdc->seemp_logdate)) ?></div>
                    </div>
                    <?php if ($atdc->seemp_logouttime && $atdc->seemp_logouttime != '0000-00-00 00:00:00'): ?>
                        <span class="status-dot complete"><i class="fas fa-circle me-1" style="font-size:0.5rem;"></i>Complete</span>
                    <?php else: ?>
                        <span class="status-dot active"><i class="fas fa-circle me-1" style="font-size:0.5rem;"></i>Active</span>
                    <?php endif; ?>
                </div>
                <div class="atd-card-times">
                    <div class="atd-time-item">
                        <span class="atd-time-label">Check In</span>
                        <span class="time-badge check-in">
                            <i class="fas fa-sign-in-alt me-1"></i>
                            <?= date('h:i A', strtotime($atdc->seemp_logintime)) ?>
                        </span>
                    </div>
                    <div class="atd-time-divider"></div>
                    <div class="atd-time-item">
                        <span class="atd-time-label">Check Out</span>
                        <?php if ($atdc->seemp_logouttime && $atdc->seemp_logouttime != '0000-00-00 00:00:00'): ?>
                            <span class="time-badge check-out">
                                <i class="fas fa-sign-out-alt me-1"></i>
                                <?= date('h:i A', strtotime($atdc->seemp_logouttime)) ?>
                            </span>
                        <?php else: ?>
                            <span class="time-badge pending-out">
                                <i class="fas fa-user-clock me-1"></i>Pending
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

    </div>

</body>
</html>
