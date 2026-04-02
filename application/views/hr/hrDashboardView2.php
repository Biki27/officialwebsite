<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HR Dashboard | Suropriyo Enterprise</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url() ?>css/hr/hrDashboardView.css">
</head>

<body>
    <div class="main-content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="text-white fw-bold">Operation Center</h2>
                <div class="text-white bg-dark bg-opacity-25 px-4 py-2 rounded-pill shadow-sm">
                    <i class="fas fa-calendar-alt me-2"></i> <?= date('l, d M Y') ?>
                </div>
            </div>
            <?php
            $reminder_count = 0;
            foreach ($recent_leaves as $l) {
                if ($l['seemrq_reminder'] == 1)
                    $reminder_count++;
            }

            if ($reminder_count > 0): ?>
                <div class="alert alert-warning border-0 shadow-sm rounded-4 mb-4 d-flex align-items-center">
                    <div class="bg-warning text-white rounded-circle p-2 me-3">
                        <i class="fas fa-bell"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold text-dark">Action Required</h6>
                        <small class="text-dark">You have <strong><?= $reminder_count ?></strong> leave requests with
                            pending reminders.</small>
                    </div>
                </div>
            <?php endif; ?>
            <div class="row g-4 mb-4">
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="text-primary mb-2"><i class="fas fa-users fa-2x"></i></div>
                        <h6 class="text-muted small">Total Employees</h6>
                        <h3 class="fw-bold"><?= isset($total_staff) ? $total_staff : 0 ?></h3>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="text-warning mb-2"><i class="fas fa-clock fa-2x"></i></div>
                        <h6 class="text-muted small">Pending Leaves</h6>
                        <h3 class="fw-bold"><?= isset($pending_count) ? $pending_count : 0 ?></h3>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="text-success mb-2"><i class="fas fa-user-tie fa-2x"></i></div>
                        <h6 class="text-muted small">New Applicants</h6>
                        <h3 class="fw-bold"><?= isset($new_applicants) ? $new_applicants : 0 ?></h3>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="text-info mb-2"><i class="fas fa-check-circle fa-2x"></i></div>
                        <h6 class="text-muted small">Present Today</h6>
                        <h3 class="fw-bold"><?= isset($present_today) ? $present_today : 0 ?></h3>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-7">
                    <div class="table-container">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="fw-bold mb-0">Leave Request Actions</h5>
                            <a href="<?= base_url('Employee/viewEmployeeLeaveRequests') ?>"
                                class="btn btn-sm btn-outline-primary rounded-pill px-3">Manage All</a>
                        </div>
                        <div class="table-responsive">
                            <table class="table align-middle table-hover">
                                <thead class="table-light text-muted small text-uppercase">
                                    <tr>
                                        <th>Employee</th>
                                        <th>Reason</th>
                                        <th>Applied</th>
                                        <th>Yearly Stats (Used/Max)</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <!-- <tbody>
                                    <?php if (!empty($recent_leaves)): ?>
                                        <?php foreach ($recent_leaves as $leave): ?>
                                            <?php
                                            $taken = $leave['total_taken'] ? $leave['total_taken'] : 0;
                                            $remaining = 20 - $taken;
                                            $status_color = ($taken >= 18) ? 'text-danger' : 'text-success';
                                            ?>
                                            <tr class="<?= ($leave['seemrq_reminder'] == 1) ? 'table-warning' : '' ?>">
                                                <td>
                                                    <div class="fw-bold text-dark">
                                                        <?= $leave['seempd_name'] ?>
                                                        <?php if ($leave['seemrq_reminder'] == 1): ?>
                                                            <span class="badge bg-danger ms-1"
                                                                style="font-size: 0.6rem;">REMINDER</span>
                                                        <?php endif; ?>
                                                    </div>
                                                    <small class="text-muted"><?= $leave['seemrq_empid'] ?></small>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="fw-bold text-dark"><?= $leave['seempd_name'] ?></div>
                                                    <small class="text-muted"><?= $leave['seemrq_empid'] ?></small>
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge bg-light text-dark border fw-normal"><?= $leave['seemrq_reason'] ?></span>
                                                    <br>
                                                    <a href="javascript:void(0)" class="text-primary small text-decoration-none"
                                                        onclick="showLeaveDetails('<?= htmlspecialchars($leave['seempd_name'], ENT_QUOTES) ?>', '<?= htmlspecialchars($leave['seemrq_reason'], ENT_QUOTES) ?>', '<?= htmlspecialchars(json_encode($leave['seemrq_summary']), ENT_QUOTES) ?>', '<?= $leave['seemrq_fromdate'] ?>', '<?= $leave['seemrq_todate'] ?>')">
                                                        <i class="fas fa-info-circle"></i> View details
                                                    </a>
                                                </td>
                                                <td class="fw-bold text-primary">
                                                    <?= $leave['seemrq_days'] ?> Days
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <span class="me-2 fw-bold <?= $status_color ?>"><?= $taken ?>/20</span>
                                                        <div class="progress w-100"
                                                            style="height: 6px; border-radius: 10px; background-color: rgba(0,0,0,0.05);">
                                                            <div class="progress-bar <?= ($taken >= 18) ? 'bg-danger' : 'bg-primary' ?>"
                                                                role="progressbar"
                                                                style="width: <?= min(($taken / 20) * 100, 100) ?>%"></div>
                                                        </div>
                                                    </div>
                                                    <small
                                                        class="text-muted"><?= ($remaining > 0) ? $remaining . ' days left' : 'Limit reached' ?></small>
                                                </td>
                                                <td class="text-center">
                                                    <div class="d-flex justify-content-center gap-2">
                                                        <a href="<?= base_url('Employee/updateLeaveStatus/' . $leave['seemrq_id'] . '/approved') ?>"
                                                            class="btn btn-success rounded-circle d-flex align-items-center justify-content-center"
                                                            style="width: 40px; height: 40px; transition: all 0.3s ease;"
                                                            title="Approve">
                                                            <i class="fas fa-check"></i>
                                                        </a>
                                                        <a href="<?= base_url('Employee/updateLeaveStatus/' . $leave['seemrq_id'] . '/rejected') ?>"
                                                            class="btn btn-danger rounded-circle d-flex align-items-center justify-content-center"
                                                            style="width: 40px; height: 40px; transition: all 0.3s ease;"
                                                            title="Reject">
                                                            <i class="fas fa-times"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center py-5 text-muted">
                                                <i class="fas fa-inbox fa-2x mb-2 d-block opacity-25"></i>
                                                No pending leave requests.
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>  -->
                                <tbody>
                                    <?php if (!empty($recent_leaves)): ?>
                                        <?php foreach ($recent_leaves as $leave): ?>
                                            <?php
                                            // Calculate stats
                                            $taken = isset($leave['total_taken']) ? $leave['total_taken'] : 0;
                                            $remaining = 20 - $taken;
                                            $status_color = ($taken >= 18) ? 'text-danger' : 'text-success';

                                            // Check for reminder flag
                                            $is_reminded = (isset($leave['seemrq_reminder']) && $leave['seemrq_reminder'] == 1);
                                            ?>

                                            <tr class="<?= $is_reminded ? 'table-warning' : '' ?>">
                                                <td>
                                                    <div class="fw-bold text-dark">
                                                        <?= $leave['seempd_name'] ?>
                                                        <?php if ($is_reminded): ?>
                                                            <span class="badge bg-danger ms-1"
                                                                style="font-size: 0.6rem; vertical-align: middle;">REMINDER</span>
                                                        <?php endif; ?>
                                                    </div>
                                                    <small class="text-muted"><?= $leave['seemrq_empid'] ?></small>
                                                </td>

                                                <td>
                                                    <span
                                                        class="badge bg-light text-dark border fw-normal"><?= $leave['seemrq_reason'] ?></span>
                                                    <br>
                                                    <a href="javascript:void(0)" class="text-primary small text-decoration-none"
                                                        onclick="showLeaveDetails('<?= htmlspecialchars($leave['seempd_name'], ENT_QUOTES) ?>', '<?= htmlspecialchars($leave['seemrq_reason'], ENT_QUOTES) ?>', '<?= htmlspecialchars(json_encode($leave['seemrq_summary']), ENT_QUOTES) ?>', '<?= $leave['seemrq_fromdate'] ?>', '<?= $leave['seemrq_todate'] ?>')">
                                                        <i class="fas fa-info-circle"></i> View details
                                                    </a>
                                                </td>

                                                <td class="fw-bold text-primary">
                                                    <?= $leave['seemrq_days'] ?> Days
                                                </td>

                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <span class="me-2 fw-bold <?= $status_color ?>"><?= $taken ?>/20</span>
                                                        <div class="progress w-100"
                                                            style="height: 6px; border-radius: 10px; background-color: rgba(0,0,0,0.05);">
                                                            <div class="progress-bar <?= ($taken >= 18) ? 'bg-danger' : 'bg-primary' ?>"
                                                                role="progressbar"
                                                                style="width: <?= min(($taken / 20) * 100, 100) ?>%"></div>
                                                        </div>
                                                    </div>
                                                    <small
                                                        class="text-muted"><?= ($remaining > 0) ? $remaining . ' days left' : 'Limit reached' ?></small>
                                                </td>

                                                <td class="text-center">
                                                    <div class="d-flex justify-content-center gap-2">
                                                        <a href="<?= base_url('Employee/updateLeaveStatus/' . $leave['seemrq_id'] . '/approved') ?>"
                                                            class="btn btn-success rounded-circle d-flex align-items-center justify-content-center"
                                                            style="width: 40px; height: 40px;" title="Approve">
                                                            <i class="fas fa-check"></i>
                                                        </a>
                                                        <a href="<?= base_url('Employee/updateLeaveStatus/' . $leave['seemrq_id'] . '/rejected') ?>"
                                                            class="btn btn-danger rounded-circle d-flex align-items-center justify-content-center"
                                                            style="width: 40px; height: 40px;" title="Reject">
                                                            <i class="fas fa-times"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center py-5 text-muted">
                                                <i class="fas fa-inbox fa-2x mb-2 d-block opacity-25"></i>
                                                No pending leave requests.
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="table-container">
                        <h5 class="fw-bold mb-4">Recent Hiring</h5>
                        <div class="list-group list-group-flush">
                            <?php if (!empty($recent_applicants)): ?>
                                <?php foreach ($recent_applicants as $app): ?>
                                    <div
                                        class="list-group-item d-flex justify-content-between align-items-center bg-transparent px-0 py-3 border-bottom">
                                        <div>
                                            <h6 class="mb-0 fw-bold"><?= $app['sejoba_name'] ?></h6>
                                            <small class="text-muted"><?= $app['sejoba_position'] ?></small>
                                        </div>
                                        <?php
                                        $badge_class = 'bg-secondary';
                                        if ($app['sejoba_state'] == 'selected')
                                            $badge_class = 'bg-success';
                                        elseif ($app['sejoba_state'] == 'rejected')
                                            $badge_class = 'bg-danger';
                                        elseif ($app['sejoba_state'] == 'applied')
                                            $badge_class = 'bg-info text-dark';
                                        ?>
                                        <span
                                            class="badge <?= $badge_class ?> rounded-pill text-capitalize"><?= $app['sejoba_state'] ?></span>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="py-3 text-center text-muted">No recent applications</div>
                            <?php endif; ?>
                        </div>
                        <a href="<?= base_url('Employee/viewJobApplicants') ?>"
                            class="btn btn-primary w-100 mt-4 rounded-pill shadow-sm">Manage Applications</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="leaveDetailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold text-primary" id="modalEmpName">Employee Name</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-3">
                    <div class="mb-3">
                        <label class="text-muted small text-uppercase fw-bold">Reason Type</label>
                        <p id="modalReason" class="mb-0 fw-semibold text-dark"></p>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small text-uppercase fw-bold">Duration</label>
                        <p class="mb-0 text-dark"><i class="far fa-calendar-alt me-2 text-primary"></i>
                            <span id="modalDates"></span>
                        </p>
                    </div>
                    <hr class="opacity-10">
                    <div class="mb-2">
                        <label class="text-muted small text-uppercase fw-bold">Request Summary</label>
                        <div id="modalSummary" class="p-3 bg-light rounded-3 text-dark mt-2"
                            style="min-height: 100px; line-height: 1.6;">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2" crossorigin="anonymous">
        </script>

    <script>

        function showLeaveDetails(name, reason, summary, from, to) {

            let cleanSummary = summary;
            try {
                if (summary.startsWith('"') && summary.endsWith('"')) {
                    cleanSummary = JSON.parse(summary);
                }
            } catch (e) {
                cleanSummary = summary;
            }

            document.getElementById('modalEmpName').innerText = name;
            document.getElementById('modalReason').innerText = reason;
            document.getElementById('modalDates').innerText = from + ' to ' + to;
            document.getElementById('modalSummary').innerText = cleanSummary ? cleanSummary : 'No additional details provided.';

            var myModal = new bootstrap.Modal(document.getElementById('leaveDetailModal'));
            myModal.show();
        }
    </script>

</body>
</body>

</html>