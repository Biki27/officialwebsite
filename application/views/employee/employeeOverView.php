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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="<?= base_url('css/employee/employeeOverView.css') ?>" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        /* ── Bank detail field states ── */
        .form-control.is-match  { border-color: #10b981; background: rgba(16,185,129,0.04); }
        .form-control.is-mismatch { border-color: #ef4444; background: rgba(239,68,68,0.04); }
        .match-feedback  { color: #10b981; font-size: 0.78rem; margin-top: 4px; display: none; }
        .mismatch-feedback { color: #ef4444; font-size: 0.78rem; margin-top: 4px; display: none; }

        /* ── Password toggle ── */
        .input-icon-wrap { position: relative; }
        .input-icon-wrap .toggle-vis {
            position: absolute; right: 12px; top: 50%;
            transform: translateY(-50%);
            background: none; border: none; color: #94a3b8;
            cursor: pointer; padding: 0; font-size: 0.9rem;
            transition: color .2s;
        }
        .input-icon-wrap .toggle-vis:hover { color: #4f46e5; }

        /* ── Masked badge ── */
        .bank-masked {
            font-family: monospace;
            background: rgba(79,70,229,0.08);
            color: #4f46e5;
            border-radius: 8px;
            padding: 2px 10px;
            font-size: 0.9rem;
            letter-spacing: 2px;
        }

        /* ── Strength meter ── */
        #ifscHelp.ok  { color: #10b981; }
        #ifscHelp.err { color: #ef4444; }

        /* ── Overview stat cards ── */
        .stat-card { border-left: 4px solid transparent; transition: border-color .3s, box-shadow .3s; }
        .stat-card:hover { border-left-color: #4f46e5; }

        /* ── Section pulse for missing bank ── */
        @keyframes pulse-border {
            0%,100% { box-shadow: 0 0 0 0 rgba(245,158,11,0.4); }
            50%      { box-shadow: 0 0 0 8px rgba(245,158,11,0); }
        }
        .pulse-alert { animation: pulse-border 2s infinite; border-radius: 16px; }

        /* ── Modal steps ── */
        .step-dot {
            width: 8px; height: 8px; border-radius: 50%;
            background: #e2e8f0; display: inline-block; margin: 0 3px;
            transition: background .3s;
        }
        .step-dot.active { background: #4f46e5; }
    </style>
</head>

<body>

    <?php if ($this->session->flashdata('msg')): ?>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false,
                         timer: 4000, timerProgressBar: true })
                .fire({ icon: 'success', title: <?= json_encode($this->session->flashdata('msg')) ?> });
        });
    </script>
    <?php endif; ?>

    <?php if ($this->session->flashdata('bank_success')): ?>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            Swal.fire({ icon: 'success', title: 'Bank Details Saved!',
                text: 'Your bank information has been securely updated.',
                confirmButtonColor: '#4f46e5', confirmButtonText: 'Great!' });
        });
    </script>
    <?php endif; ?>

    <div class="container py-4" id="overview-section">

        <!-- ── Page Header ── -->
        <div class="mb-4 p-3 rounded-4 shadow-sm text-white"
             style="background: linear-gradient(135deg, #4f46e5, #7c3aed);">
            <h4 class="fw-bold mb-1">
                <i class="fas fa-chart-line me-2"></i>Overview Dashboard
            </h4>
            <small class="opacity-75">Your performance &amp; personal details</small>
        </div>

        <!-- ── Bank Details Missing Alert ── -->
        <?php if (!isset($bank_details) || empty($bank_details->sebank_ac_no)): ?>
        <div class="alert d-flex align-items-center p-4 shadow-sm rounded-4 mb-4 pulse-alert"
             style="background: rgba(245,158,11,0.08); border: 1px solid rgba(245,158,11,0.3);" role="alert">
            <i class="fas fa-exclamation-circle fa-2x text-warning me-3 flex-shrink-0"></i>
            <div class="flex-grow-1">
                <h5 class="text-dark fw-bold mb-1">Action Required: Bank Details Missing</h5>
                <p class="mb-0 text-muted small">Your salary cannot be processed until you add your bank account details.</p>
            </div>
            <button class="btn btn-warning text-dark fw-bold rounded-pill px-4 shadow-sm flex-shrink-0"
                    data-bs-toggle="modal" data-bs-target="#bankDetailsModal">
                <i class="fas fa-plus me-1"></i> Add Now
            </button>
        </div>
        <?php endif; ?>

        <!-- ── Stats Row ── -->
        <div class="row g-4">

            <!-- Salary Card -->
            <div class="col-md-4">
                <div class="card border-0 shadow-lg rounded-4 text-center p-4 h-100 stat-card">
                    <div class="mb-3">
                        <span class="d-inline-flex align-items-center justify-content-center rounded-circle"
                              style="width:56px;height:56px;background:rgba(16,185,129,0.1);">
                            <i class="fas fa-wallet fa-lg text-success"></i>
                        </span>
                    </div>
                    <h6 class="text-muted mb-1">Monthly Salary</h6>
                    <h3 class="fw-bold text-success mb-0">₹<?= number_format($empdetails->seempd_salary) ?></h3>
                    <div class="progress mt-3" style="height:6px;">
                        <div class="progress-bar bg-success" style="width:100%"></div>
                    </div>
                </div>
            </div>

            <!-- Holidays Card -->
            <div class="col-md-4">
                <div class="card border-0 shadow-lg rounded-4 text-center p-4 h-100 stat-card">
                    <div class="mb-3">
                        <span class="d-inline-flex align-items-center justify-content-center rounded-circle"
                              style="width:56px;height:56px;background:rgba(59,130,246,0.1);">
                            <i class="fas fa-calendar-check fa-lg text-info"></i>
                        </span>
                    </div>
                    <h6 class="text-muted mb-1">Holidays Used</h6>
                    <h3 class="fw-bold text-info mb-0"><?= $holidays_taken ?><span class="text-muted fs-5">/20</span></h3>
                    <div class="progress mt-3" style="height:6px;">
                        <div class="progress-bar bg-info" style="width:<?= $holidays_percent ?>%"></div>
                    </div>
                    <small class="text-muted mt-2 d-block"><?= 20 - $holidays_taken ?> days remaining</small>
                </div>
            </div>

            <!-- Employee Info Card -->
            <div class="col-md-4">
                <div class="card border-0 shadow-lg rounded-4 p-4 h-100 stat-card">
                    <h5 class="fw-bold mb-3 text-primary">
                        <i class="fas fa-id-badge me-2"></i>Employee Info
                    </h5>
                    <div class="d-flex flex-column gap-2 small">
                        <?php
                        $fields = [
                            ['label'=>'Employee ID',  'value'=> $empdetails->seempd_empid],
                            ['label'=>'Name',          'value'=> $empdetails->seempd_name],
                            ['label'=>'Email',         'value'=> $this->session->userdata('email')],
                            ['label'=>'Phone',         'value'=> $empdetails->seempd_phone],
                            ['label'=>'Position',      'value'=> $empdetails->seempd_designation],
                            ['label'=>'Experience',    'value'=> $empdetails->seempd_experience . ' Years'],
                        ];
                        foreach ($fields as $f): ?>
                        <div class="d-flex justify-content-between align-items-center py-1"
                             style="border-bottom:1px solid #f1f5f9;">
                            <span class="text-muted"><?= $f['label'] ?></span>
                            <span class="fw-semibold text-end" style="max-width:60%;word-break:break-all;">
                                <?= htmlspecialchars($f['value']) ?>
                            </span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

        </div>

        <!-- ── Bank Details Card ── -->
        <div class="row g-4 mt-1">
            <div class="col-12">
                <div class="card border-0 shadow-lg rounded-4 p-4 stat-card">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                        <h5 class="fw-bold text-primary mb-0">
                            <i class="fas fa-university me-2"></i>Bank Details
                        </h5>
                        <button class="btn btn-outline-primary rounded-pill px-4 btn-sm"
                                data-bs-toggle="modal" data-bs-target="#bankDetailsModal">
                            <i class="fas fa-<?= (isset($bank_details) && !empty($bank_details->sebank_ac_no)) ? 'edit' : 'plus' ?> me-1"></i>
                            <?= (isset($bank_details) && !empty($bank_details->sebank_ac_no)) ? 'Edit Details' : 'Add Details' ?>
                        </button>
                    </div>

                    <?php if (isset($bank_details) && !empty($bank_details->sebank_ac_no)): ?>
                    <div class="row g-3 small">
                        <div class="col-sm-4">
                            <span class="text-muted d-block mb-1">Account Number</span>
                            <span class="bank-masked">
                                ••••&nbsp;••••&nbsp;<?= substr($bank_details->sebank_ac_no, -4) ?>
                            </span>
                        </div>
                        <div class="col-sm-4">
                            <span class="text-muted d-block mb-1">IFSC Code</span>
                            <span class="fw-semibold text-uppercase"><?= htmlspecialchars($bank_details->sebank_ifsc) ?></span>
                        </div>
                        <div class="col-sm-4">
                            <span class="text-muted d-block mb-1">ESI Number</span>
                            <span class="fw-semibold">
                                <?= !empty($bank_details->sebank_esi) ? htmlspecialchars($bank_details->sebank_esi) : '<span class="text-muted fst-italic">Not provided</span>' ?>
                            </span>
                        </div>
                    </div>
                    <div class="mt-3">
                        <span class="badge rounded-pill px-3 py-2"
                              style="background:rgba(16,185,129,0.1);color:#10b981;border:1px solid rgba(16,185,129,0.2);">
                            <i class="fas fa-shield-alt me-1"></i> Verified &amp; Secure
                        </span>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-4 text-muted">
                        <i class="fas fa-university fa-3x mb-3 opacity-25"></i>
                        <p class="mb-0">No bank details on file. Please add your account details for salary processing.</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- ── Daily Attendance Card ── -->
        <div class="row g-4 mt-1">
            <div class="col-12">
                <div class="card border-0 shadow-lg rounded-4 p-4 text-center stat-card">
                    <h5 class="fw-bold mb-2 text-primary">
                        <i class="fas fa-clock me-2"></i>Daily Attendance
                    </h5>
                    <p class="text-muted small mb-3">
                        Your IP address and device information will be recorded for attendance.
                    </p>
                    <div class="d-flex justify-content-center gap-3">
                        <?php if (!$todayAttendance): ?>
                        <button id="clockBtn" class="btn btn-primary btn-lg rounded-pill px-5"
                                onclick="markAttendance('login')">
                            <i class="fas fa-sign-in-alt me-2"></i>Clock In
                        </button>
                        <?php elseif (empty($todayAttendance->seemp_logouttime) || $todayAttendance->seemp_logouttime == '0000-00-00 00:00:00'): ?>
                        <button id="clockBtn" class="btn btn-danger btn-lg rounded-pill px-5"
                                onclick="confirmClockOut()">
                            <i class="fas fa-sign-out-alt me-2"></i>Clock Out
                        </button>
                        <?php else: ?>
                        <button class="btn btn-success btn-lg rounded-pill px-5" disabled>
                            <i class="fas fa-check-circle me-2"></i>Attendance Completed
                        </button>
                        <?php endif; ?>
                    </div>
                    <div id="attendanceAlert" class="mt-3" style="display:none;"></div>
                </div>
            </div>
        </div>

    </div><!-- /container -->


    <!-- BANK DETAILS MODAL (with Confirm A/C) -->
    <div class="modal fade" id="bankDetailsModal" tabindex="-1" aria-labelledby="bankModalLabel">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden">

                <!-- Header -->
                <div class="modal-header text-white border-0 pb-3"
                     style="background: linear-gradient(135deg, #4f46e5, #7c3aed);">
                    <div>
                        <h5 class="modal-title fw-bold mb-0" id="bankModalLabel">
                            <i class="fas fa-university me-2"></i>
                            <?= (isset($bank_details) && !empty($bank_details->sebank_ac_no)) ? 'Update Bank Details' : 'Add Bank Details' ?>
                        </h5>
                        <small class="opacity-75">All data is encrypted end-to-end</small>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <!-- Info strip -->
                <div class="px-4 pt-3">
                    <div class="alert alert-info border-0 rounded-3 small mb-0 d-flex align-items-center gap-2"
                         style="background:rgba(59,130,246,0.08);color:#1d4ed8;">
                        <i class="fas fa-lock"></i>
                        <span>This information is used <strong>only</strong> by HR for salary disbursement.</span>
                    </div>
                </div>

                <?= form_open('Employee/updateMyBankDetails', ['id'=>'bankDetailsForm', 'autocomplete'=>'off']) ?>

                <div class="modal-body px-4 pt-3 pb-0">

                    <!-- Account Number -->
                    <div class="mb-3">
                        <label class="fw-semibold small text-dark mb-1">
                            Account Number <span class="text-danger">*</span>
                        </label>
                        <div class="input-icon-wrap">
                            <input type="password" id="bank_ac" name="bank_ac"
                                   class="form-control pe-5" required
                                   autocomplete="new-password"
                                   placeholder="Enter account number"
                                   value="<?= isset($bank_details) ? $bank_details->sebank_ac_no : '' ?>">
                            <button type="button" class="toggle-vis" onclick="toggleVis('bank_ac','eye1')">
                                <i class="fas fa-eye" id="eye1"></i>
                            </button>
                        </div>
                        <div id="acLengthHint" class="text-muted small mt-1">9–18 digits required.</div>
                    </div>

                    <!-- Confirm Account Number -->
                    <div class="mb-3">
                        <label class="fw-semibold small text-dark mb-1">
                            Confirm Account Number <span class="text-danger">*</span>
                        </label>
                        <div class="input-icon-wrap">
                            <input type="password" id="bank_ac_confirm"
                                   class="form-control pe-5" required
                                   autocomplete="new-password"
                                   placeholder="Re-enter account number">
                            <button type="button" class="toggle-vis" onclick="toggleVis('bank_ac_confirm','eye2')">
                                <i class="fas fa-eye" id="eye2"></i>
                            </button>
                        </div>
                        <div class="match-feedback"    id="matchOk"><i class="fas fa-check-circle me-1"></i>Account numbers match</div>
                        <div class="mismatch-feedback" id="matchErr"><i class="fas fa-times-circle me-1"></i>Account numbers do not match</div>
                    </div>

                    <!-- IFSC Code -->
                    <div class="mb-3">
                        <label class="fw-semibold small text-dark mb-1">
                            IFSC Code <span class="text-danger">*</span>
                        </label>
                        <input type="text" id="bank_ifsc" name="bank_ifsc"
                               class="form-control text-uppercase" required
                               placeholder="e.g. SBIN0001234" maxlength="11"
                               value="<?= isset($bank_details) ? $bank_details->sebank_ifsc : '' ?>">
                        <div id="ifscHelp" class="small mt-1 text-muted">11-character code (e.g. SBIN0001234)</div>
                    </div>

                    <!-- ESI Number -->
                    <div class="mb-3">
                        <label class="fw-semibold small text-dark mb-1">ESI Number
                            <span class="text-muted fw-normal">(if applicable)</span>
                        </label>
                        <input type="text" name="bank_esi" class="form-control"
                               placeholder="Enter ESI number"
                               value="<?= isset($bank_details) ? $bank_details->sebank_esi : '' ?>">
                    </div>

                </div><!-- /modal-body -->

                <div class="modal-footer border-0 pt-2 pb-4 px-4 gap-2">
                    <button type="button" class="btn btn-light rounded-pill px-4"
                            data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" id="bankSaveBtn"
                            class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm" disabled>
                        <i class="fas fa-shield-alt me-2"></i>Save Securely
                    </button>
                </div>

                <?= form_close() ?>
            </div>
        </div>
    </div>


    <!-- ── Attendance Modal (for error messages) ── -->
    <div class="modal fade" id="attendanceModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content rounded-4 shadow border-0 text-center p-4">
                <div id="attendanceModalBody"></div>
                <button type="button" class="btn btn-light rounded-pill mt-3"
                        data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <script>
    /*  1. BANK DETAILS FORM LOGIC */

    // Toggle password/text visibility
    function toggleVis(fieldId, iconId) {
        const f = document.getElementById(fieldId);
        const i = document.getElementById(iconId);
        if (f.type === 'password') {
            f.type = 'text';
            i.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            f.type = 'password';
            i.classList.replace('fa-eye-slash', 'fa-eye');
        }
    }

    // IFSC regex validator
    function validateIFSC(val) {
        return /^[A-Z]{4}0[A-Z0-9]{6}$/.test(val.toUpperCase());
    }

    // Account number: digits only, 9-18 chars
    function validateAC(val) {
        return /^\d{9,18}$/.test(val);
    }

    function checkFormValidity() {
        const ac      = document.getElementById('bank_ac').value.trim();
        const confirm = document.getElementById('bank_ac_confirm').value.trim();
        const ifsc    = document.getElementById('bank_ifsc').value.trim();
        const btn     = document.getElementById('bankSaveBtn');

        const acOk      = validateAC(ac);
        const confirmOk = ac === confirm && confirm.length > 0;
        const ifscOk    = validateIFSC(ifsc);

        btn.disabled = !(acOk && confirmOk && ifscOk);
    }

    // Live IFSC feedback
    document.getElementById('bank_ifsc').addEventListener('input', function () {
        const val  = this.value.toUpperCase();
        this.value = val;
        const help = document.getElementById('ifscHelp');
        if (val.length === 0) {
            help.textContent = '11-character code (e.g. SBIN0001234)';
            help.className   = 'small mt-1 text-muted';
            this.classList.remove('is-valid','is-invalid');
        } else if (validateIFSC(val)) {
            help.textContent = '✓ Valid IFSC format';
            help.className   = 'small mt-1 ok';
            this.classList.add('is-valid'); this.classList.remove('is-invalid');
        } else {
            help.textContent = '✗ Invalid format. Should be like SBIN0001234';
            help.className   = 'small mt-1 err';
            this.classList.add('is-invalid'); this.classList.remove('is-valid');
        }
        checkFormValidity();
    });

    // Live A/C number feedback
    document.getElementById('bank_ac').addEventListener('input', function () {
        const hint = document.getElementById('acLengthHint');
        const len  = this.value.replace(/\D/g,'').length;
        if (len > 0 && (!/^\d+$/.test(this.value))) {
            hint.textContent = '⚠ Digits only, no spaces or letters.';
            hint.style.color = '#ef4444';
        } else if (len > 0 && len < 9) {
            hint.textContent = `${len}/9 minimum digits entered`;
            hint.style.color = '#f59e0b';
        } else if (len >= 9 && len <= 18) {
            hint.textContent = `✓ ${len} digits`;
            hint.style.color = '#10b981';
        } else if (len > 18) {
            hint.textContent = 'Maximum 18 digits allowed';
            hint.style.color = '#ef4444';
        } else {
            hint.textContent = '9–18 digits required.';
            hint.style.color = '#94a3b8';
        }
        // Re-trigger confirm check
        document.getElementById('bank_ac_confirm').dispatchEvent(new Event('input'));
        checkFormValidity();
    });

    // Live confirm A/C feedback
    document.getElementById('bank_ac_confirm').addEventListener('input', function () {
        const ac      = document.getElementById('bank_ac').value.trim();
        const confirm = this.value.trim();
        const ok      = document.getElementById('matchOk');
        const err     = document.getElementById('matchErr');

        if (confirm.length === 0) {
            this.classList.remove('is-match','is-mismatch');
            ok.style.display = err.style.display = 'none';
        } else if (ac === confirm) {
            this.classList.add('is-match'); this.classList.remove('is-mismatch');
            ok.style.display = 'block'; err.style.display = 'none';
        } else {
            this.classList.add('is-mismatch'); this.classList.remove('is-match');
            ok.style.display = 'none'; err.style.display = 'block';
        }
        checkFormValidity();
    });

    // Intercept submit → SweetAlert confirmation
    document.addEventListener("DOMContentLoaded", function () {
        const bankForm = document.getElementById('bankDetailsForm');
        if (!bankForm) return;

        bankForm.addEventListener('submit', function (e) {
            e.preventDefault();

            // Final client-side guard
            const ac      = document.getElementById('bank_ac').value.trim();
            const confirm = document.getElementById('bank_ac_confirm').value.trim();
            const ifsc    = document.getElementById('bank_ifsc').value.trim();

            if (!validateAC(ac)) {
                Swal.fire({ icon:'error', title:'Invalid Account Number',
                    text:'Please enter a valid 9–18 digit account number.', confirmButtonColor:'#4f46e5' });
                return;
            }
            if (ac !== confirm) {
                Swal.fire({ icon:'error', title:'Account Numbers Don\'t Match',
                    text:'Please make sure both account number fields are identical.', confirmButtonColor:'#4f46e5' });
                return;
            }
            if (!validateIFSC(ifsc)) {
                Swal.fire({ icon:'error', title:'Invalid IFSC Code',
                    text:'Please enter a valid 11-character IFSC code.', confirmButtonColor:'#4f46e5' });
                return;
            }

            Swal.fire({
                title: 'Confirm Bank Details?',
                html: `<div class="text-start small">
                    <div class="mb-2"><span class="text-muted">Account:</span> <strong>••••&nbsp;${ac.slice(-4)}</strong></div>
                    <div class="mb-2"><span class="text-muted">IFSC:</span> <strong>${ifsc}</strong></div>
                    <hr class="my-2">
                    <p class="text-danger mb-0"><i class="fas fa-exclamation-triangle me-1"></i>
                    Incorrect details may delay your salary. Please verify before submitting.</p>
                </div>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#4f46e5',
                cancelButtonColor: '#64748b',
                confirmButtonText: '<i class="fas fa-shield-alt me-1"></i> Yes, Save Securely',
                cancelButtonText: 'Let me re-check'
            }).then(function (result) {
                if (result.isConfirmed) {
                    Swal.fire({ title: 'Saving Securely...', allowOutsideClick: false,
                        didOpen: () => Swal.showLoading() });
                    HTMLFormElement.prototype.submit.call(bankForm);
                }
            });
        });

        // Reset confirm field when modal closes
        document.getElementById('bankDetailsModal').addEventListener('hidden.bs.modal', function () {
            const c = document.getElementById('bank_ac_confirm');
            c.value = '';
            c.classList.remove('is-match','is-mismatch');
            document.getElementById('matchOk').style.display  = 'none';
            document.getElementById('matchErr').style.display = 'none';
            document.getElementById('bankSaveBtn').disabled   = true;
        });

        // If bank_details already exists, pre-fill confirm and enable submit
        <?php if (isset($bank_details) && !empty($bank_details->sebank_ac_no)): ?>
        // Bank details exist: allow immediate save without re-confirming
        document.getElementById('bankSaveBtn').disabled = false;
        document.getElementById('bank_ac').addEventListener('focus', function () {
            // Clear masked value so user types fresh
            if (this.value) {
                this.value = '';
                document.getElementById('bank_ac_confirm').value = '';
                document.getElementById('bankSaveBtn').disabled = true;
                document.getElementById('acLengthHint').textContent = '9–18 digits required.';
                document.getElementById('acLengthHint').style.color = '#94a3b8';
            }
        }, { once: true });
        <?php endif; ?>
    });


    /*  2. ATTENDANCE LOGIC */

    let currentAction = 'login';

    function markAttendance(action) {
        currentAction = action;
        const $btn = $('#clockBtn');
        const originalHtml = $btn.html();
        $btn.html('<i class="fas fa-spinner fa-spin me-2"></i> Processing...')
            .prop('disabled', true);
        sendAttendanceRequest(action, originalHtml);
    }

    function confirmClockOut() {
        Swal.fire({
            title: 'Clock Out?',
            text: 'Do you want to clock out and log out from the portal?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, Clock Out & Logout'
        }).then(function (result) {
            if (result.isConfirmed) markAttendance('logout');
        });
    }

    function sendAttendanceRequest(action, originalHtml) {
        let csrfName = '<?= $this->security->get_csrf_token_name(); ?>';
        let csrfHash = '<?= $this->security->get_csrf_hash(); ?>';

        $.ajax({
            url: '<?= base_url("Employee/SubmitAttendanceAjax") ?>',
            type: 'POST',
            dataType: 'json',
            data: { action: action, [csrfName]: csrfHash },
            success: function (data) {
                if (data.status === 'success') {
                    if (action === 'logout') {
                        Swal.fire({ icon:'success', title:'Clocked Out',
                            text:'You successfully clocked out. Logging out securely...',
                            showConfirmButton: false, timer: 2000 })
                        .then(function () {
                            window.location.href = '<?= base_url("Employee/Logout") ?>';
                        });
                    } else {
                        Swal.fire({ icon:'success', title:'Clocked In',
                            text:'You have successfully clocked in for today.',
                            showConfirmButton: false, timer: 1500 })
                        .then(function () { location.reload(); });
                    }
                } else {
                    showModalMessage('error', data.message);
                    $('#clockBtn').html(originalHtml).prop('disabled', false);
                }
            },
            error: function () {
                showModalMessage('error', 'Server error occurred. Please try again.');
                $('#clockBtn').html(originalHtml).prop('disabled', false);
            }
        });
    }

    function showModalMessage(type, message) {
        const icon = type === 'success'
            ? '<i class="fas fa-check-circle text-success fa-4x mb-3"></i>'
            : '<i class="fas fa-times-circle text-danger fa-4x mb-3"></i>';
        $('#attendanceModalBody').html(icon + '<br><h5 class="fw-bold text-dark mt-2">' + message + '</h5>');
        new bootstrap.Modal(document.getElementById('attendanceModal')).show();
    }
    </script>

</body>
</html>
