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
    <link href="<?= base_url('css/employee/employeeOverView.css') ?>" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>

    <?php if ($this->session->flashdata('msg')): ?>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 4000,
                    timerProgressBar: true
                });
                Toast.fire({
                    icon: 'success',
                    title: <?= json_encode($this->session->flashdata('msg')) ?>
                });
            });
        </script>
    <?php endif; ?>

    <div class="container py-4" id="overview-section" style="display: block;">

        <div class="mb-4 p-3 rounded-4 shadow-sm text-white"
            style="background: linear-gradient(135deg, #4f46e5, #7c3aed);">
            <h4 class="fw-bold mb-1">
                <i class="fas fa-chart-line me-2"></i>Overview Dashboard
            </h4>
            <small class="opacity-75">Your performance & personal details</small>
        </div>

        <?php if (!isset($bank_details) || empty($bank_details->sebank_ac_no)): ?>
            <div class="alert border-warning bg-warning bg-opacity-10 d-flex align-items-center p-4 shadow-sm rounded-4 mb-4" role="alert">
                <i class="fas fa-exclamation-circle fa-2x text-warning me-3"></i>
                <div class="flex-grow-1">
                    <h5 class="alert-heading text-dark fw-bold mb-1">Action Required: Update Bank Details</h5>
                    <p class="mb-0 text-muted small">Your salary processing is currently on hold. Please provide your bank account information securely.</p>
                </div>
                <button class="btn btn-warning text-dark fw-bold rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#bankDetailsModal">
                    Update Now
                </button>
            </div>
        <?php endif; ?>

        <div class="row g-4">

            <div class="col-md-4">
                <div class="card border-0 shadow-lg rounded-4 text-center p-4 h-100">
                    <div class="mb-3">
                        <i class="fas fa-wallet fa-2x text-success"></i>
                    </div>
                    <h6 class="text-muted">Monthly Salary</h6>
                    <h3 class="fw-bold text-success">
                        ₹ <?= $empdetails->seempd_salary ?>
                    </h3>

                    <div class="progress mt-3" style="height: 8px;">
                        <div class="progress-bar bg-success" style="width: 100%"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-lg rounded-4 text-center p-4 h-100">
                    <div class="mb-3">
                        <i class="fas fa-calendar-check fa-2x text-info"></i>
                    </div>
                    <h6 class="text-muted">Holidays Used</h6>
                    <h3 class="fw-bold text-info">
                        <?= $holidays_taken ?>/20
                    </h3>

                    <div class="progress mt-3" style="height: 8px;">
                        <div class="progress-bar bg-info" style="width: <?= $holidays_percent ?>%">
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-lg rounded-4 p-4 h-100">

                    <h5 class="fw-bold mb-3 text-primary">
                        <i class="fas fa-id-badge me-2"></i>Employee Info
                    </h5>

                    <div class="d-flex flex-column gap-2 small">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Employee ID</span>
                            <span class="fw-semibold"><?= $empdetails->seempd_empid ?></span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Name</span>
                            <span class="fw-semibold"><?= $empdetails->seempd_name ?></span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Email</span>
                            <span class="fw-semibold"><?= $this->session->userdata('email') ?></span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Phone</span>
                            <span class="fw-semibold"><?= $empdetails->seempd_phone ?></span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Position</span>
                            <span class="fw-semibold"><?= $empdetails->seempd_designation ?></span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Experience</span>
                            <span class="fw-semibold"><?= $empdetails->seempd_experience ?> Years</span>
                        </div>


                    </div>
                </div>
            </div>

        </div>

        <div class="row g-4 mt-1">
            <div class="col-12">
                <div class="card border-0 shadow-lg rounded-4 p-4 text-center">
                    <h5 class="fw-bold mb-3 text-primary">
                        <i class="fas fa-clock me-2"></i>Daily Attendance
                    </h5>
                    <p class="text-muted small">Your IP address and device information will be recorded for attendance.</p>
                    <div class="d-flex justify-content-center gap-3">
                        <?php if (!$todayAttendance): ?>
                            <button id="clockBtn" class="btn btn-primary btn-lg" onclick="markAttendance('login')">
                                <i class="fas fa-sign-in-alt me-2"></i> Clock In
                            </button>
                        <?php elseif ($todayAttendance && (empty($todayAttendance->seemp_logouttime) || $todayAttendance->seemp_logouttime == '0000-00-00 00:00:00')): ?>
                            <button id="clockBtn" class="btn btn-danger btn-lg" onclick="confirmClockOut()">
                                <i class="fas fa-sign-out-alt me-2"></i> Clock Out
                            </button>
                        <?php else: ?>
                            <button class="btn btn-success btn-lg" disabled>
                                <i class="fas fa-check-circle me-2"></i> Attendance Completed
                            </button>
                        <?php endif; ?>
                    </div>
                    <div id="attendanceAlert" class="mt-3" style="display:none;"></div>
                </div>
            </div>
        </div>

    </div>
    <!-- Attendance Modal -->

    <div class="modal fade" id="attendanceModal" tabindex="-1" aria-labelledby="attendanceModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="attendanceModalLabel">Attendance Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center py-4" id="attendanceModalBody">
                </div>
                <div class="modal-footer border-0 justify-content-center">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Bank Details Modal -->
    <div class="modal fade" id="bankDetailsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow-lg" style="border-radius: 15px;">

                <?= form_open('Employee/updateMyBankDetails', ['id' => 'bankDetailsForm']) ?>

                <div class="modal-header border-0 pb-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold text-primary"><i class="fas fa-shield-alt me-2 text-success"></i> Secure Financial Info</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">
                    <div class="alert alert-info border-0 rounded-3 small mb-4">
                        <i class="fas fa-info-circle me-2"></i> This information is encrypted and only used by HR for salary disbursement.
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold small text-muted mb-1">Bank Account Number <span class="text-danger">*</span></label>
                        <input type="text" name="bank_ac" class="form-control fw-medium" required
                            value="<?= isset($bank_details) ? $bank_details->sebank_ac_no : '' ?>">
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold small text-muted mb-1">IFSC Code <span class="text-danger">*</span></label>
                        <input type="text" name="bank_ifsc" class="form-control fw-medium text-uppercase" required
                            value="<?= isset($bank_details) ? $bank_details->sebank_ifsc : '' ?>"
                            placeholder="e.g. SBIN0001234">
                    </div>

                    <div class="mb-2">
                        <label class="fw-bold small text-muted mb-1">ESI Number (If applicable)</label>
                        <input type="text" name="bank_esi" class="form-control fw-medium"
                            value="<?= isset($bank_details) ? $bank_details->sebank_esi : '' ?>">
                    </div>
                </div>

                <div class="modal-footer border-0 pt-0 pb-4 px-4">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-medium" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">Securely Save</button>
                </div>

                <?= form_close() ?>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        // --- Bank Details Submission Alert ---
        document.addEventListener("DOMContentLoaded", function() {
            const bankForm = document.getElementById('bankDetailsForm');
            if (bankForm) {
                bankForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Save Bank Details?',
                        text: 'Please ensure your Account Number and IFSC code are completely accurate before submitting.',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#461bb9',
                        cancelButtonColor: '#64748b',
                        confirmButtonText: 'Yes, Submit Securely'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire({
                                title: 'Saving Securely...',
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });
                            HTMLFormElement.prototype.submit.call(bankForm);
                        }
                    });
                });
            }
        });

        // --- Attendance Logic ---
    let currentAction = 'login'; 

    function markAttendance(action) {
        currentAction = action;
        const $btn = $('#clockBtn');
        const originalHtml = $btn.html();
        
        $btn.html('<i class="fas fa-spinner fa-spin me-2"></i> Processing...').prop('disabled', true);
        sendAttendanceRequest(action, originalHtml);
    }

    // New Function for Clock Out Confirmation
    function confirmClockOut() {
        Swal.fire({
            title: 'Are you sure?',
            text: "Do you want to clock out and logout from the portal?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, Clock Out & Logout'
        }).then((result) => {
            if (result.isConfirmed) {
                markAttendance('logout');
            }
        });
    }

    function sendAttendanceRequest(action, originalHtml) {
        let csrfName = '<?= $this->security->get_csrf_token_name(); ?>';
        let csrfHash = '<?= $this->security->get_csrf_hash(); ?>';

        $.ajax({
            url: '<?= base_url("Employee/SubmitAttendanceAjax") ?>',
            type: 'POST',
            dataType: 'json',
            data: { 
                action: action,
                [csrfName]: csrfHash 
            },
            success: function(data) {
                if(data.status === 'success') {
                    if (action === 'logout') {
                        // Clocked out successfully, log them out of the session
                        Swal.fire({
                            icon: 'success',
                            title: 'Clocked Out',
                            text: 'You successfully clocked out. Logging out securely...',
                            showConfirmButton: false,
                            timer: 2000
                        }).then(() => {
                            window.location.href = '<?= base_url("Employee/Logout") ?>';
                        });
                    } else {
                        // Clocked in successfully, reload page to toggle to "Clock Out" button
                        Swal.fire({
                            icon: 'success',
                            title: 'Clocked In',
                            text: 'You have successfully clocked in for today.',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            location.reload(); 
                        });
                    }
                } else {
                    showModalMessage('error', data.message);
                    $('#clockBtn').html(originalHtml).prop('disabled', false);
                }
            },
            error: function(xhr, status, error) {
                console.log('AJAX Error:', error);
                showModalMessage('error', 'Server error occurred. Please try again.');
                $('#clockBtn').html(originalHtml).prop('disabled', false);
            }
        });
    }

     function showModalMessage(type, message) {
        let icon = type === 'success' ? '<i class="fas fa-check-circle text-success fa-4x mb-3"></i>' : '<i class="fas fa-times-circle text-danger fa-4x mb-3"></i>';
        $('#attendanceModalBody').html(`${icon}<br><h5 class="fw-bold text-dark">${message}</h5>`);
        var myModal = new bootstrap.Modal(document.getElementById('attendanceModal'));
        myModal.show();
    }
 
        function resetButtons(originalHtml) {
            if (currentAction === 'login') {
                $('#clockInBtn').html(originalHtml).prop('disabled', false);
            } else {
                $('#clockOutBtn').html(originalHtml).prop('disabled', false);
            }
        }
    </script>
</body>

</html>