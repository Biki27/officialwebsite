<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ESS Portal — Overview</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="<?= base_url('css/employee/employeeOverView.css') ?>" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .form-control.is-match   { border-color: #10b981; background: rgba(16,185,129,0.04); }
        .form-control.is-mismatch{ border-color: #ef4444; background: rgba(239,68,68,0.04); }
        .match-feedback   { color:#10b981; font-size:0.78rem; margin-top:4px; display:none; }
        .mismatch-feedback{ color:#ef4444; font-size:0.78rem; margin-top:4px; display:none; }
        .input-icon-wrap { position:relative; }
        .input-icon-wrap .toggle-vis {
            position:absolute; right:12px; top:50%; transform:translateY(-50%);
            background:none; border:none; color:#94a3b8; cursor:pointer; font-size:0.9rem; transition:color .2s;
        }
        .input-icon-wrap .toggle-vis:hover { color:#4338ca; }
        #ifscHelp.ok  { color:#10b981; }
        #ifscHelp.err { color:#ef4444; }
        @keyframes pulse-border {
            0%,100% { box-shadow:0 0 0 0 rgba(245,158,11,0.4); }
            50%      { box-shadow:0 0 0 8px rgba(245,158,11,0); }
        }
        .pulse-alert { animation:pulse-border 2s infinite; }
        .step-dot { width:8px; height:8px; border-radius:50%; background:#e2e8f0;
                    display:inline-block; margin:0 3px; transition:background .3s; }
        .step-dot.active { background:#4338ca; }
    </style>
</head>

<body>

    <?php if ($this->session->flashdata('msg')): ?>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            Swal.mixin({ toast:true, position:'top-end', showConfirmButton:false, timer:4000, timerProgressBar:true })
                .fire({ icon:'success', title:<?= json_encode($this->session->flashdata('msg')) ?> });
        });
    </script>
    <?php endif; ?>

    <?php if ($this->session->flashdata('bank_success')): ?>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            Swal.fire({ icon:'success', title:'Bank Details Saved!',
                text:'Your bank information has been securely updated.', confirmButtonColor:'#4338ca' });
        });
    </script>
    <?php endif; ?>

    <div class="container-xl px-3 px-md-4 pb-5" id="overview-section">

        <!-- ── Section Banner ── -->
        <div class="ent-section-banner">
            <h4><i class="fas fa-th-large me-2"></i>Overview Dashboard</h4>
            <small>Your performance &amp; personal details</small>
        </div>

        <!-- ── Bank Missing Alert ── -->
        <?php if (!isset($bank_details) || empty($bank_details->sebank_ac_no)): ?>
        <div class="ent-alert-warning pulse-alert mb-4">
            <div class="alert-icon"><i class="fas fa-exclamation-triangle"></i></div>
            <div class="alert-body">
                <div class="alert-title">Action Required: Bank Details Missing</div>
                <div class="alert-sub">Your salary cannot be processed until you add your bank account details.</div>
            </div>
            <button class="alert-cta" data-bs-toggle="modal" data-bs-target="#bankDetailsModal">
                <i class="fas fa-plus me-1"></i> Add Now
            </button>
        </div>
        <?php endif; ?>

        <!-- ── Stats Row ── -->
        <div class="row g-3 mb-3">

            <!-- Salary Card with Mask -->
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="ent-stat-card salary-card">
                    <div class="stat-icon-wrap green">
                        <i class="fas fa-wallet"></i>
                    </div>
                    <div class="stat-label">Monthly Salary</div>
                    <div class="stat-value-row">
                        <span class="stat-value green" id="salaryDisplay">₹••••••</span>
                        <button class="salary-eye-btn" id="salaryEyeBtn"
                                onclick="toggleSalary()"
                                title="Toggle salary visibility"
                                aria-label="Toggle salary visibility">
                            <i class="fas fa-eye" id="salaryEyeIcon"></i>
                        </button>
                    </div>
                    <div class="stat-sub">Gross monthly salary</div>
                    <div class="stat-bar">
                        <div class="stat-bar-fill green" style="width:100%"></div>
                    </div>
                    <!-- Hidden real value -->
                    <span id="salaryRealValue" style="display:none">₹<?= number_format($empdetails->seempd_salary) ?></span>
                </div>
            </div>

            <!-- Holidays Card -->
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="ent-stat-card">
                    <div class="stat-icon-wrap blue">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="stat-label">Holidays Used</div>
                    <div class="stat-value-row">
                        <span class="stat-value blue">
                            <?= $holidays_taken ?><span class="stat-denom">/20</span>
                        </span>
                    </div>
                    <div class="stat-sub"><?= 20 - $holidays_taken ?> days remaining</div>
                    <div class="stat-bar">
                        <div class="stat-bar-fill blue" style="width:<?= $holidays_percent ?>%"></div>
                    </div>
                </div>
            </div>

            <!-- Employee Info Card -->
            <div class="col-12 col-lg-4">
                <div class="ent-stat-card info-card">
                    <div class="info-header">
                        <i class="fas fa-id-badge me-2 text-primary"></i>Employee Profile
                    </div>
                    <div class="info-rows">
                        <?php
                        $fields = [
                            ['label'=>'Employee ID', 'value'=> $empdetails->seempd_empid,   'icon'=>'hashtag'],
                            ['label'=>'Full Name',   'value'=> $empdetails->seempd_name,     'icon'=>'user'],
                            ['label'=>'Email',       'value'=> $this->session->userdata('email'), 'icon'=>'envelope'],
                            ['label'=>'Phone',       'value'=> $empdetails->seempd_phone,    'icon'=>'phone'],
                            ['label'=>'Position',    'value'=> $empdetails->seempd_designation, 'icon'=>'briefcase'],
                            ['label'=>'Experience',  'value'=> $empdetails->seempd_experience.' Years', 'icon'=>'star'],
                        ];
                        foreach ($fields as $f): ?>
                        <div class="info-row">
                            <span class="info-row-label">
                                <i class="fas fa-<?= $f['icon'] ?> me-1 opacity-50"></i>
                                <?= $f['label'] ?>
                            </span>
                            <span class="info-row-val"><?= htmlspecialchars($f['value']) ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

        </div>

        <!-- ── Bank Details Card ── -->
        <div class="ent-card mb-3">
            <div class="ent-card-header">
                <div class="card-title-group">
                    <span class="card-section-icon bank"><i class="fas fa-university"></i></span>
                    <div>
                        <div class="card-section-title">Bank Details</div>
                        <div class="card-section-sub">Salary disbursement account</div>
                    </div>
                </div>
                <button class="btn-ent-outline" data-bs-toggle="modal" data-bs-target="#bankDetailsModal">
                    <i class="fas fa-<?= (isset($bank_details) && !empty($bank_details->sebank_ac_no)) ? 'edit' : 'plus' ?> me-1"></i>
                    <?= (isset($bank_details) && !empty($bank_details->sebank_ac_no)) ? 'Edit Details' : 'Add Details' ?>
                </button>
            </div>
            <div class="ent-card-body">
                <?php if (isset($bank_details) && !empty($bank_details->sebank_ac_no)): ?>
                <div class="row g-3">
                    <div class="col-12 col-sm-4">
                        <div class="bank-field">
                            <div class="bank-field-label">Account Number</div>
                            <div class="bank-masked-badge">
                                <i class="fas fa-lock me-1 opacity-50"></i>
                                ••••&nbsp;••••&nbsp;<?= substr($bank_details->sebank_ac_no, -4) ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-4">
                        <div class="bank-field">
                            <div class="bank-field-label">IFSC Code</div>
                            <div class="bank-field-val"><?= htmlspecialchars($bank_details->sebank_ifsc) ?></div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-4">
                        <div class="bank-field">
                            <div class="bank-field-label">ESI Number</div>
                            <div class="bank-field-val">
                                <?= !empty($bank_details->sebank_esi) ? htmlspecialchars($bank_details->sebank_esi) : '<span class="text-muted fst-italic small">Not provided</span>' ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="verified-badge">
                        <i class="fas fa-shield-alt me-1"></i> Verified &amp; Encrypted
                    </span>
                </div>
                <?php else: ?>
                <div class="empty-state">
                    <div class="empty-icon"><i class="fas fa-university"></i></div>
                    <div class="empty-title">No Bank Details Found</div>
                    <div class="empty-sub">Add your account details to enable salary disbursement.</div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- ── Daily Attendance Card ── -->
        <div class="ent-card">
            <div class="ent-card-header">
                <div class="card-title-group">
                    <span class="card-section-icon clock"><i class="fas fa-clock"></i></span>
                    <div>
                        <div class="card-section-title">Daily Attendance</div>
                        <div class="card-section-sub">Your IP &amp; device are logged for security</div>
                    </div>
                </div>
            </div>
            <div class="ent-card-body text-center py-4">
                <div class="d-flex justify-content-center">
                    <?php if (!$todayAttendance): ?>
                    <button id="clockBtn" class="btn-clock clock-in" onclick="markAttendance('login')">
                        <i class="fas fa-sign-in-alt me-2"></i>Clock In
                    </button>
                    <?php elseif (empty($todayAttendance->seemp_logouttime) || $todayAttendance->seemp_logouttime == '0000-00-00 00:00:00'): ?>
                    <button id="clockBtn" class="btn-clock clock-out" onclick="confirmClockOut()">
                        <i class="fas fa-sign-out-alt me-2"></i>Clock Out
                    </button>
                    <?php else: ?>
                    <button class="btn-clock clock-done" disabled>
                        <i class="fas fa-check-circle me-2"></i>Attendance Completed
                    </button>
                    <?php endif; ?>
                </div>
                <div id="attendanceAlert" class="mt-3" style="display:none;"></div>
            </div>
        </div>

    </div><!-- /container-xl -->


    <!-- BANK DETAILS MODAL -->
    <div class="modal fade" id="bankDetailsModal" tabindex="-1" aria-labelledby="bankModalLabel">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden" style="font-family:'Plus Jakarta Sans',sans-serif;">
                <div class="modal-header text-white border-0 pb-3"
                     style="background:linear-gradient(135deg,#1e1b4b,#4338ca);">
                    <div>
                        <h5 class="modal-title fw-bold mb-0" id="bankModalLabel">
                            <i class="fas fa-university me-2"></i>
                            <?= (isset($bank_details) && !empty($bank_details->sebank_ac_no)) ? 'Update Bank Details' : 'Add Bank Details' ?>
                        </h5>
                        <small class="opacity-65">All data is encrypted end-to-end</small>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="px-4 pt-3">
                    <div class="alert alert-info border-0 rounded-3 small mb-0 d-flex align-items-center gap-2"
                         style="background:rgba(67,56,202,0.06);color:#3730a3;">
                        <i class="fas fa-lock"></i>
                        <span>Used <strong>only</strong> by HR for salary disbursement.</span>
                    </div>
                </div>
                <?= form_open('Employee/updateMyBankDetails', ['id'=>'bankDetailsForm','autocomplete'=>'off']) ?>
                <div class="modal-body px-4 pt-3 pb-0">
                    <div class="mb-3">
                        <label class="ent-label">Account Number <span class="text-danger">*</span></label>
                        <div class="input-icon-wrap">
                            <input type="password" id="bank_ac" name="bank_ac"
                                   class="ent-input pe-5" required autocomplete="new-password"
                                   placeholder="Enter account number"
                                   value="<?= isset($bank_details) ? $bank_details->sebank_ac_no : '' ?>">
                            <button type="button" class="toggle-vis" onclick="toggleVis('bank_ac','eye1')">
                                <i class="fas fa-eye" id="eye1"></i>
                            </button>
                        </div>
                        <small id="acLengthHint" class="text-muted" style="font-size:0.75rem;">9–18 digits required.</small>
                    </div>
                    <div class="mb-3">
                        <label class="ent-label">Confirm Account Number <span class="text-danger">*</span></label>
                        <div class="input-icon-wrap">
                            <input type="password" id="bank_ac_confirm" name="bank_ac_confirm"
                                   class="ent-input pe-5" required autocomplete="new-password"
                                   placeholder="Re-enter account number">
                            <button type="button" class="toggle-vis" onclick="toggleVis('bank_ac_confirm','eye2')">
                                <i class="fas fa-eye" id="eye2"></i>
                            </button>
                        </div>
                        <div class="match-feedback"    id="matchOk"><i class="fas fa-check-circle me-1"></i>Account numbers match</div>
                        <div class="mismatch-feedback" id="matchErr"><i class="fas fa-times-circle me-1"></i>Account numbers do not match</div>
                    </div>
                    <div class="mb-3">
                        <label class="ent-label">IFSC Code <span class="text-danger">*</span></label>
                        <input type="text" id="bank_ifsc" name="bank_ifsc"
                               class="ent-input text-uppercase" required
                               placeholder="e.g. SBIN0001234" maxlength="11"
                               value="<?= isset($bank_details) ? $bank_details->sebank_ifsc : '' ?>">
                        <div id="ifscHelp" class="small mt-1 text-muted">11-character code (e.g. SBIN0001234)</div>
                    </div>
                    <div class="mb-3">
                        <label class="ent-label">ESI Number <span class="text-muted fw-normal">(optional)</span></label>
                        <input type="text" name="bank_esi" class="ent-input"
                               placeholder="Enter ESI number"
                               value="<?= isset($bank_details) ? $bank_details->sebank_esi : '' ?>">
                    </div>
                </div>
                <div class="modal-footer border-0 pt-2 pb-4 px-4 gap-2">
                    <button type="button" class="btn-ent-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" id="bankSaveBtn" class="btn-ent-primary" disabled>
                        <i class="fas fa-shield-alt me-1"></i>Save Securely
                    </button>
                </div>
                <?= form_close() ?>
            </div>
        </div>
    </div>

    <!-- Attendance Message Modal -->
    <div class="modal fade" id="attendanceModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content rounded-4 shadow border-0 text-center p-4"
                 style="font-family:'Plus Jakarta Sans',sans-serif;">
                <div id="attendanceModalBody"></div>
                <button type="button" class="btn-ent-secondary mt-3" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script>

    /* ── Salary Toggle ── */
    let salaryVisible = false;
    const realSalary = document.getElementById('salaryRealValue').innerText;

    function toggleSalary() {
        salaryVisible = !salaryVisible;
        const display = document.getElementById('salaryDisplay');
        const icon    = document.getElementById('salaryEyeIcon');
        if (salaryVisible) {
            display.textContent = realSalary;
            display.classList.add('revealed');
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            display.textContent = '₹••••••';
            display.classList.remove('revealed');
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    }

    /* ── Bank Details Form ── */
    function toggleVis(fieldId, iconId) {
        const f = document.getElementById(fieldId);
        const i = document.getElementById(iconId);
        f.type = f.type === 'password' ? 'text' : 'password';
        i.classList.toggle('fa-eye');
        i.classList.toggle('fa-eye-slash');
    }

    function validateIFSC(val) { return /^[A-Z]{4}0[A-Z0-9]{6}$/.test(val.toUpperCase()); }
    function validateAC(val)   { return /^\d{9,18}$/.test(val); }

    function checkFormValidity() {
        const ac      = document.getElementById('bank_ac').value.trim();
        const confirm = document.getElementById('bank_ac_confirm').value.trim();
        const ifsc    = document.getElementById('bank_ifsc').value.trim();
        document.getElementById('bankSaveBtn').disabled = !(validateAC(ac) && ac === confirm && confirm.length > 0 && validateIFSC(ifsc));
    }

    document.getElementById('bank_ifsc').addEventListener('input', function () {
        const val  = this.value.toUpperCase();
        this.value = val;
        const help = document.getElementById('ifscHelp');
        if (!val.length) {
            help.textContent = '11-character code (e.g. SBIN0001234)'; help.className = 'small mt-1 text-muted';
            this.classList.remove('is-valid','is-invalid');
        } else if (validateIFSC(val)) {
            help.textContent = '✓ Valid IFSC format'; help.className = 'small mt-1 ok';
            this.classList.add('is-valid'); this.classList.remove('is-invalid');
        } else {
            help.textContent = '✗ Invalid format. Should be like SBIN0001234'; help.className = 'small mt-1 err';
            this.classList.add('is-invalid'); this.classList.remove('is-valid');
        }
        checkFormValidity();
    });

    document.getElementById('bank_ac').addEventListener('input', function () {
        const hint = document.getElementById('acLengthHint');
        const len  = this.value.replace(/\D/g,'').length;
        if (len > 0 && (!/^\d+$/.test(this.value))) {
            hint.textContent = '⚠ Digits only.'; hint.style.color = '#ef4444';
        } else if (len > 0 && len < 9) {
            hint.textContent = `${len}/9 minimum digits`; hint.style.color = '#f59e0b';
        } else if (len >= 9 && len <= 18) {
            hint.textContent = `✓ ${len} digits`; hint.style.color = '#10b981';
        } else if (len > 18) {
            hint.textContent = 'Max 18 digits'; hint.style.color = '#ef4444';
        } else {
            hint.textContent = '9–18 digits required.'; hint.style.color = '#94a3b8';
        }
        document.getElementById('bank_ac_confirm').dispatchEvent(new Event('input'));
        checkFormValidity();
    });

    document.getElementById('bank_ac_confirm').addEventListener('input', function () {
        const ac      = document.getElementById('bank_ac').value.trim();
        const confirm = this.value.trim();
        const ok  = document.getElementById('matchOk');
        const err = document.getElementById('matchErr');
        if (!confirm.length) { this.classList.remove('is-match','is-mismatch'); ok.style.display = err.style.display = 'none'; }
        else if (ac === confirm) { this.classList.add('is-match'); this.classList.remove('is-mismatch'); ok.style.display = 'block'; err.style.display = 'none'; }
        else { this.classList.add('is-mismatch'); this.classList.remove('is-match'); ok.style.display = 'none'; err.style.display = 'block'; }
        checkFormValidity();
    });

    document.addEventListener("DOMContentLoaded", function () {
        const bankForm = document.getElementById('bankDetailsForm');
        if (!bankForm) return;
        bankForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const ac      = document.getElementById('bank_ac').value.trim();
            const confirm = document.getElementById('bank_ac_confirm').value.trim();
            const ifsc    = document.getElementById('bank_ifsc').value.trim();
            if (!validateAC(ac)) {
                Swal.fire({ icon:'error', title:'Invalid Account Number', text:'Please enter a valid 9–18 digit account number.', confirmButtonColor:'#4338ca' }); return;
            }
            if (ac !== confirm) {
                Swal.fire({ icon:'error', title:'Account Numbers Don\'t Match', text:'Both account number fields must be identical.', confirmButtonColor:'#4338ca' }); return;
            }
            if (!validateIFSC(ifsc)) {
                Swal.fire({ icon:'error', title:'Invalid IFSC Code', text:'Please enter a valid 11-character IFSC code.', confirmButtonColor:'#4338ca' }); return;
            }
            Swal.fire({
                title:'Confirm Bank Details?',
                html:`<div class="text-start small">
                    <div class="mb-2"><span class="text-muted">Account:</span> <strong>•••• ${ac.slice(-4)}</strong></div>
                    <div class="mb-2"><span class="text-muted">IFSC:</span> <strong>${ifsc}</strong></div>
                    <hr class="my-2">
                    <p class="text-danger mb-0"><i class="fas fa-exclamation-triangle me-1"></i>Incorrect details may delay salary.</p>
                </div>`,
                icon:'question', showCancelButton:true,
                confirmButtonColor:'#4338ca', cancelButtonColor:'#64748b',
                confirmButtonText:'<i class="fas fa-shield-alt me-1"></i> Yes, Save Securely',
                cancelButtonText:'Let me re-check'
            }).then(function (result) {
                if (result.isConfirmed) {
                    Swal.fire({ title:'Saving Securely...', allowOutsideClick:false, didOpen:() => Swal.showLoading() });
                    HTMLFormElement.prototype.submit.call(bankForm);
                }
            });
        });

        document.getElementById('bankDetailsModal').addEventListener('hidden.bs.modal', function () {
            const c = document.getElementById('bank_ac_confirm');
            c.value = '';
            c.classList.remove('is-match','is-mismatch');
            document.getElementById('matchOk').style.display  = 'none';
            document.getElementById('matchErr').style.display = 'none';
            document.getElementById('bankSaveBtn').disabled   = true;
        });

        <?php if (isset($bank_details) && !empty($bank_details->sebank_ac_no)): ?>
        document.getElementById('bankSaveBtn').disabled = false;
        document.getElementById('bank_ac').addEventListener('focus', function () {
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

    /* ── Attendance ── */
    let currentAction = 'login';

    function markAttendance(action) {
        currentAction = action;
        const $btn = $('#clockBtn');
        const originalHtml = $btn.html();
        $btn.html('<i class="fas fa-spinner fa-spin me-2"></i> Processing...').prop('disabled', true);
        sendAttendanceRequest(action, originalHtml);
    }

    function confirmClockOut() {
        Swal.fire({
            title:'Clock Out?', text:'Do you want to clock out and log out from the portal?',
            icon:'warning', showCancelButton:true, confirmButtonColor:'#ef4444',
            cancelButtonColor:'#64748b', confirmButtonText:'Yes, Clock Out & Logout'
        }).then(function (result) { if (result.isConfirmed) markAttendance('logout'); });
    }

    function sendAttendanceRequest(action, originalHtml) {
        $.ajax({
            url:'<?= base_url("Employee/SubmitAttendanceAjax") ?>', type:'POST', dataType:'json',
            data:{ action:action, '<?= $this->security->get_csrf_token_name(); ?>':'<?= $this->security->get_csrf_hash(); ?>' },
            success: function (data) {
                if (data.status === 'success') {
                    if (action === 'logout') {
                        Swal.fire({ icon:'success', title:'Clocked Out', text:'Logging out securely...', showConfirmButton:false, timer:2000 })
                        .then(function () { window.location.href = '<?= base_url("Employee/Logout") ?>'; });
                    } else {
                        Swal.fire({ icon:'success', title:'Clocked In', text:'Successfully clocked in for today.', showConfirmButton:false, timer:1500 })
                        .then(function () { location.reload(); });
                    }
                } else {
                    showModalMessage('error', data.message);
                    $('#clockBtn').html(originalHtml).prop('disabled', false);
                }
            },
            error: function () {
                showModalMessage('error', 'Server error. Please try again.');
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
