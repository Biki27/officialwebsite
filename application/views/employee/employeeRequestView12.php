<?php
defined('BASEPATH') OR exit('No direct script access allowed');
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
    <link rel="stylesheet" href="<?= base_url('css/employee/employeeRequestView.css') ?>">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

</head>

<body>

    <div class="modal fade" id="timeOffModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content rounded-4 shadow">

                <div class="modal-header">
                    <h5 class="modal-title fw-semibold">
                        <i class="fas fa-calendar-plus me-2"></i>New Request
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <?= form_open('Employee/EmployeeRequest') ?>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Start Date</label>
                        <input name="startdate" type="date" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">End Date</label>
                        <input name="enddate" type="date" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Reason</label>
                        <select name="reason" class="form-select" required>
                            <option value="">Select reason</option>
                            <option value="Medical">Medical</option>
                            <option value="Leave">Leave</option>
                            <option value="Personal">Personal</option>
                            <option value="Business">Business</option>
                        </select>
                    </div>

                    <input name="action" type="hidden" value="requestsubmit">

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Summary</label>
                        <textarea name="summary" class="form-control" rows="3" required></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 rounded-pill">
                        Submit Request
                    </button>

                    <?= form_close() ?>

                </div>

            </div>
        </div>
    </div>

    <div class="container py-4">

        <!-- HEADER -->
        <div class="mb-4 p-3 rounded-4 shadow-sm text-white"
            style="background: linear-gradient(135deg, #4f46e5, #7c3aed);">
            <h4 class="fw-bold mb-1">
                <i class="fas fa-paper-plane me-2"></i>Request Dashboard
            </h4>
            <small class="opacity-75">Manage your leave and requests</small>
        </div>

        <!-- ALERTS -->
        <?php if ($this->session->flashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show shadow-sm rounded-3 mb-4">
                <i class="fas fa-check-circle me-2"></i>
                <?= $this->session->flashdata('success'); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (validation_errors() && trim(validation_errors()) != ''): ?>
            <div class="alert alert-danger alert-dismissible fade show shadow-sm rounded-3 mb-4">
                <i class="fas fa-exclamation-circle me-2"></i>
                <?= validation_errors(); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- CARD -->
        <div class="card border-0 shadow-lg rounded-4">
            <div class="card-body">

                <!-- TOP BAR -->
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                    <h5 class="fw-semibold mb-0">
                        <i class="fas fa-list me-2 text-primary"></i>Request History
                    </h5>

                    <button class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal"
                        data-bs-target="#timeOffModal">
                        <i class="fas fa-plus me-1"></i> New Request
                    </button>
                </div>

                <!-- TABLE -->
                <div class="table-responsive">
                    <table class="table align-middle table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>REQ.ID</th>
                                <th>Reason</th>
                                <th>Summary</th>
                                <th>Applied On</th>
                                <th>Date Range</th>
                                <th>Days</th>
                                <th>Status</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($requests as $req) { ?>
                                <!-- One Row per Request -->
                                <tr>
                                    <td class="fw-semibold">
                                        <?= $req->seemrq_id ?>
                                    </td>

                                    <td>
                                        <span class="badge bg-info-subtle text-info px-3 py-2">
                                            <?= $req->seemrq_reason ?>
                                        </span>
                                    </td>

                                    <td class="text-muted">
                                        <!-- 1. The truncated container -->
                                        <div class="summary-text"
                                            style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; line-clamp: 2; overflow: hidden; text-overflow: ellipsis; max-width: 200px; pointer-events: none;">
                                            <?= $req->seemrq_summary ?>
                                        </div>

                                        <!-- 2. The link (Moved outside the div and secured with json_encode) -->
                                        <a href="javascript:void(0)" class="text-primary small text-decoration-none"
                                            style="position: relative; z-index: 10;"
                                            onclick='viewFullSummary(<?= htmlspecialchars(json_encode($req->seemrq_summary), ENT_QUOTES, 'UTF-8') ?>)'>
                                            view details
                                        </a>
                                    </td>

                                    <td><?= $req->seemrq_reqdate ?></td>

                                    <td>
                                        <span class="text-primary fw-medium">
                                            <?= $req->seemrq_fromdate ?>
                                        </span>
                                        <br>
                                        <small class="text-muted">to <?= $req->seemrq_todate ?></small>
                                    </td>

                                    <td>
                                        <span class="badge bg-secondary-subtle text-dark px-3 py-2">
                                            <?= $req->seemrq_days ?> days
                                        </span>
                                    </td>

                                    <td>
                                        <!-- Status Badge -->
                                        <span class="badge px-3 py-2
                <?php
                switch ($req->seemrq_status) {
                    case 'approved':
                        echo 'bg-success-subtle text-success';
                        break;
                    case 'rejected':
                        echo 'bg-danger-subtle text-danger';
                        break;
                    default:
                        echo 'bg-warning-subtle text-warning';
                        break;
                }
                ?>">
                                            <?= ucfirst($req->seemrq_status) ?>
                                        </span>

                                        <!-- Remind HR Button (Only for Pending) -->
                                        <?php if ($req->seemrq_status == 'pending'): ?>
                                            <div class="mt-2">
                                                <button onclick="sendHRReminder(<?= $req->seemrq_id ?>)"
                                                    id="remindBtn_<?= $req->seemrq_id ?>"
                                                    class="btn btn-sm btn-link text-decoration-none p-0"
                                                    style="font-size: 0.75rem; color: #4f46e5;">
                                                    <i class="fas fa-bell me-1"></i> Remind HR
                                                </button>
                                            </div>
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
    <div class="modal fade" id="summaryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-primary">
                        <i class="fas fa-file-alt me-2"></i>Full Request Summary
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <p id="fullSummaryText" class="text-dark" style="white-space: pre-wrap; line-height: 1.6;"></p>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <?php if (validation_errors() && trim(validation_errors()) != ''): ?>
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                var timeOffModal = new bootstrap.Modal(document.getElementById('timeOffModal'));
                timeOffModal.show();
            });
        </script>
    <?php endif; ?>
    <script>
        function sendHRReminder(reqId) {
            const btn = document.getElementById('remindBtn_' + reqId);
            const originalContent = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
            btn.disabled = true;

            $.ajax({
                url: '<?= base_url("Employee/sendLeaveReminder/") ?>' + reqId,
                type: 'POST',
                dataType: 'json',
                data: { '<?= $this->security->get_csrf_token_name(); ?>': '<?= $this->security->get_csrf_hash(); ?>' },
                success: function (res) {
                    if (res.status === 'success') {
                        btn.innerHTML = '<i class="fas fa-check"></i> Reminder Sent';
                        btn.style.color = '#10b981';
                    } else {
                        btn.innerHTML = originalContent;
                        btn.disabled = false;
                    }
                },
                error: function () {
                    btn.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Error';
                    btn.disabled = false;
                }
            });
        }
        function viewFullSummary(text) {
            // 1. If text is a JSON string (with quotes), parse it clean
            let cleanText = text;
            try {
                if (typeof text === 'string' && text.startsWith('"') && text.endsWith('"')) {
                    cleanText = JSON.parse(text);
                }
            } catch (e) {
                cleanText = text;
            }

            // 2. Inject text
            document.getElementById('fullSummaryText').innerText = cleanText;

            // 3. Trigger Modal (Bootstrap 5 way)
            var modalElement = document.getElementById('summaryModal');
            var myModal = bootstrap.Modal.getOrCreateInstance(modalElement);
            myModal.show();
        }
    </script>

</body>

</html>