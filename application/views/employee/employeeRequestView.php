<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ESS Portal — Requests</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('css/employee/employeeRequestView.css') ?>">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>

    <div class="modal fade" id="timeOffModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden"
                 style="font-family:'Plus Jakarta Sans',sans-serif;">
                <div class="modal-header text-white border-0"
                     style="background:linear-gradient(135deg,#1e1b4b,#4338ca);padding:1.25rem 1.5rem;">
                    <div>
                        <h5 class="modal-title fw-bold mb-0">
                            <i class="fas fa-calendar-plus me-2"></i>New Leave Request
                        </h5>
                        <small class="opacity-65">Submit a new leave or absence request</small>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <?= form_open('Employee/EmployeeRequest', ['id' => 'leaveRequestForm']) ?>
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="req-label">Start Date</label>
                            <input name="startdate" type="date" class="req-input" required>
                        </div>
                        <div class="col-6">
                            <label class="req-label">End Date</label>
                            <input name="enddate" type="date" class="req-input" required>
                        </div>
                        <div class="col-12">
                            <label class="req-label">Leave Type</label>
                            <select name="reason" class="req-input" required>
                                <option value="">Select type</option>
                                <option value="Medical">Medical</option>
                                <option value="Leave">Annual Leave</option>
                                <option value="Personal">Personal</option>
                                <option value="Business">Business</option>
                            </select>
                        </div>
                        <input name="action" type="hidden" value="requestsubmit">
                        <div class="col-12">
                            <label class="req-label">Summary / Reason</label>
                            <textarea name="summary" class="req-input" rows="3"
                                      placeholder="Briefly describe your request..." required></textarea>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn-ent-primary w-100">
                                <i class="fas fa-paper-plane me-2"></i>Submit Request
                            </button>
                        </div>
                    </div>
                    <?= form_close() ?>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="summaryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4"
                 style="font-family:'Plus Jakarta Sans',sans-serif;">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold" style="color:#4338ca;">
                        <i class="fas fa-file-alt me-2"></i>Full Request Summary
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <p id="fullSummaryText" style="white-space:pre-wrap;line-height:1.7;color:#1e293b;font-size:0.88rem;"></p>
                </div>
                <div class="modal-footer border-0 pt-0 pb-3">
                    <button class="btn-ent-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>


    <div class="container-xl px-3 px-md-4 pb-5">

        <div class="ent-section-banner">
            <h4><i class="fas fa-paper-plane me-2"></i>Request Dashboard</h4>
            <small>Manage your leave and absence requests</small>
        </div>

        <div class="ent-card req-desktop">
            <div class="ent-card-header">
                <div class="card-title-group">
                    <span class="card-section-icon bank"><i class="fas fa-list-alt"></i></span>
                    <div>
                        <div class="card-section-title">Request History</div>
                        <div class="card-section-sub"><?= count($requests) ?> total requests</div>
                    </div>
                </div>
                <button class="btn-ent-primary" data-bs-toggle="modal" data-bs-target="#timeOffModal">
                    <i class="fas fa-plus me-1"></i> New Request
                </button>
            </div>

            <div class="req-table-wrap">
                <table class="req-table">
                    <thead>
                        <tr>
                            <th>Req. ID</th>
                            <th>Type</th>
                            <th>Summary</th>
                            <th>Applied On</th>
                            <th>Date Range</th>
                            <th>Days</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($requests as $req): ?>
                        <tr>
                            <td><span class="req-id">#REQ<?= $req->seemrq_id ?></span></td>
                            <td>
                                <?php
                                $typeColors = [
                                    'Medical'  => 'type-medical',
                                    'Leave'    => 'type-leave',
                                    'Personal' => 'type-personal',
                                    'Business' => 'type-business',
                                ];
                                $cls = $typeColors[$req->seemrq_reason] ?? 'type-leave';
                                ?>
                                <span class="type-badge <?= $cls ?>"><?= $req->seemrq_reason ?></span>
                            </td>
                            <td>
                                <div class="summary-clip"><?= htmlspecialchars($req->seemrq_summary) ?></div>
                                <a href="javascript:void(0)"
                                   class="view-detail-link"
                                   onclick='viewFullSummary(<?= htmlspecialchars(json_encode($req->seemrq_summary), ENT_QUOTES, "UTF-8") ?>)'>
                                    View details
                                </a>
                            </td>
                            <td style="color:#64748b;"><?= $req->seemrq_reqdate ?></td>
                            <td>
                                <span class="date-range">
                                    <?= $req->seemrq_fromdate ?>
                                    <i class="fas fa-arrow-right" style="font-size:0.55rem;opacity:0.5;margin:0 4px;"></i>
                                    <?= $req->seemrq_todate ?>
                                </span>
                            </td>
                            <td>
                                <span class="days-badge"><?= $req->seemrq_days ?>d</span>
                            </td>
                            <td>
                                <?php
                                $statusMap = [
                                    'approved' => ['cls'=>'status-approved','icon'=>'check-circle'],
                                    'rejected' => ['cls'=>'status-rejected','icon'=>'times-circle'],
                                ];
                                $sm = $statusMap[$req->seemrq_status] ?? ['cls'=>'status-pending','icon'=>'clock'];
                                ?>
                                <span class="status-pill <?= $sm['cls'] ?>">
                                    <i class="fas fa-<?= $sm['icon'] ?> me-1" style="font-size:0.65rem;"></i>
                                    <?= ucfirst($req->seemrq_status) ?>
                                </span>
                                <?php if ($req->seemrq_status == 'pending'): ?>
                                <div class="mt-1">
                                    <button onclick="sendHRReminder(<?= $req->seemrq_id ?>, this)"
                                            id="remindBtn_<?= $req->seemrq_id ?>"
                                            class="remind-btn">
                                        <i class="fas fa-bell me-1"></i>Remind HR
                                    </button>
                                </div>
                                <?php endif; ?>

                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($requests)): ?>
                        <tr>
                            <td colspan="7" style="text-align:center;color:#64748b;padding:2rem 0;">
                                <i class="fas fa-inbox fa-2x mb-2"></i><br>
                                No requests found.<br>
                                Click "New Request" to submit your first leave or absence request.
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="req-mobile">
            <div style="display:flex;justify-content:flex-end;margin-bottom:0.75rem;">
                <button class="btn-ent-primary" data-bs-toggle="modal" data-bs-target="#timeOffModal">
                    <i class="fas fa-plus me-1"></i> New Request
                </button>
            </div>

            <?php foreach ($requests as $req): ?>
            <?php
            $typeColors = ['Medical'=>'type-medical','Leave'=>'type-leave','Personal'=>'type-personal','Business'=>'type-business'];
            $cls = $typeColors[$req->seemrq_reason] ?? 'type-leave';
            $statusMap = ['approved'=>['cls'=>'status-approved','icon'=>'check-circle'],'rejected'=>['cls'=>'status-rejected','icon'=>'times-circle']];
            $sm = $statusMap[$req->seemrq_status] ?? ['cls'=>'status-pending','icon'=>'clock'];
            ?>
            <div class="req-mobile-card">
                <div class="req-card-top">
                    <div>
                        <span class="req-id" style="font-size:0.75rem;">#<?= $req->seemrq_id ?></span>
                        <span class="type-badge <?= $cls ?> ms-2"><?= $req->seemrq_reason ?></span>
                    </div>
                    <span class="status-pill <?= $sm['cls'] ?>">
                        <i class="fas fa-<?= $sm['icon'] ?> me-1" style="font-size:0.6rem;"></i>
                        <?= ucfirst($req->seemrq_status) ?>
                    </span>
                </div>
                <div class="req-card-row">
                    <span class="req-card-label">Period</span>
                    <span class="date-range"><?= $req->seemrq_fromdate ?> → <?= $req->seemrq_todate ?></span>
                </div>
                <div class="req-card-row">
                    <span class="req-card-label">Days</span>
                    <span class="days-badge"><?= $req->seemrq_days ?>d</span>
                </div>
                <div class="req-card-row">
                    <span class="req-card-label">Applied</span>
                    <span style="color:#64748b;font-size:0.8rem;"><?= $req->seemrq_reqdate ?></span>
                </div>
                <div class="req-card-summary"><?= htmlspecialchars(substr($req->seemrq_summary, 0, 80)) ?>...</div>
                <div style="display:flex;gap:8px;margin-top:0.5rem;flex-wrap:wrap;">
                    <a href="javascript:void(0)"
                       class="view-detail-link"
                       onclick='viewFullSummary(<?= htmlspecialchars(json_encode($req->seemrq_summary), ENT_QUOTES, "UTF-8") ?>)'>
                        <i class="fas fa-eye me-1"></i>View full summary
                    </a>
                    <?php if ($req->seemrq_status == 'pending'): ?>
                    <button onclick="sendHRReminder(<?= $req->seemrq_id ?>, this)"
                            id="remindBtn_m_<?= $req->seemrq_id ?>"
                            class="remind-btn">
                        <i class="fas fa-bell me-1"></i>Remind HR
                    </button>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
            <?php if (empty($requests)): ?>
            <div style="text-align:center;color:#64748b;padding:2rem 0;">
                <i class="fas fa-inbox fa-2x mb-2"></i><br>
                No requests found.<br>
                 Click "New Request" to submit your first leave or absence request.
            </div>
            <?php endif; ?>
        </div>

    </div>

    <script>
    document.addEventListener("DOMContentLoaded", function () {
        
        // --- CALENDAR BLOCKING LOGIC ---
        const startDateInput = document.querySelector('input[name="startdate"]');
        const endDateInput = document.querySelector('input[name="enddate"]');

        if (startDateInput && endDateInput) {
            // When Start Date changes, set the minimum allowed End Date
            startDateInput.addEventListener('change', function() {
                endDateInput.min = this.value;
                
                // If an end date was already selected and is now invalid, clear it
                if(endDateInput.value && endDateInput.value < this.value) {
                    endDateInput.value = '';
                }
            });
        }

        // --- FLASH MESSAGES & VALIDATION ---
        <?php if ($this->session->flashdata('success')): ?>
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: '<?= $this->session->flashdata('success'); ?>',
                confirmButtonColor: '#4338ca',
                timer: 3000
            });
        <?php endif; ?>

        <?php if (validation_errors() && trim(validation_errors()) != ''): ?>
            Swal.fire({
                icon: 'error',
                title: 'Submission Failed',
                html: '<?= trim(str_replace(["\r", "\n"], '', validation_errors())); ?>',
                confirmButtonColor: '#4338ca'
            }).then(() => {
                new bootstrap.Modal(document.getElementById('timeOffModal')).show();
            });
        <?php endif; ?>

        // --- FORM SUBMISSION DIALOG ---
        const requestForm = document.getElementById('leaveRequestForm');
        if (requestForm) {
            requestForm.addEventListener('submit', function(e) {
                e.preventDefault();

                // Extra safety check just in case browser doesn't support the 'min' attribute
                if (startDateInput.value > endDateInput.value) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid Dates',
                        text: 'The End Date cannot be earlier than the Start Date.',
                        confirmButtonColor: '#4338ca'
                    });
                    return; // Stop submission entirely
                }

                Swal.fire({
                    title: 'Submit Leave Request?',
                    text: "Please confirm that your dates and reasons are correct.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#4338ca',
                    cancelButtonColor: '#dc3545',
                    confirmButtonText: 'Yes, Submit'
                }).then((result) => {
                    if (result.isConfirmed) {
                        requestForm.submit();
                    }
                });
            });
        }
    });

    // --- AJAX REMINDER FUNCTION ---
    function sendHRReminder(reqId, btnElement) {
        const originalContent = btnElement.innerHTML;
        btnElement.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
        btnElement.disabled = true;
        
        $.ajax({
            url: '<?= base_url("Employee/sendLeaveReminder/") ?>' + reqId,
            type: 'POST', 
            dataType: 'json',
            data: { '<?= $this->security->get_csrf_token_name(); ?>':'<?= $this->security->get_csrf_hash(); ?>' },
            success: function (res) {
                if (res.status === 'success') {
                    btnElement.innerHTML = '<i class="fas fa-check"></i> Sent';
                    btnElement.style.color = '#10b981';
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Reminder Sent!',
                        text: 'HR has been notified about your request.',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });
                } else {
                    btnElement.innerHTML = originalContent;
                    btnElement.disabled = false;
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Failed',
                        text: res.message || 'Unable to send reminder right now.',
                        confirmButtonColor: '#4338ca'
                    });
                }
            },
            error: function () {
                btnElement.innerHTML = originalContent;
                btnElement.disabled = false;
                
                Swal.fire({
                    icon: 'error',
                    title: 'Server Error',
                    text: 'An unexpected error occurred. Please try again later.',
                    confirmButtonColor: '#4338ca'
                });
            }
        });
    }

    // --- MODAL SUMMARY FORMATTING ---
    function viewFullSummary(text) {
        let cleanText = text;
        try { if (typeof text === 'string' && text.startsWith('"') && text.endsWith('"')) cleanText = JSON.parse(text); } catch(e){}
        document.getElementById('fullSummaryText').innerText = cleanText;
        bootstrap.Modal.getOrCreateInstance(document.getElementById('summaryModal')).show();
    }
    </script>

</body>
</html>