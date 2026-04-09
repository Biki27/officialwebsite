<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('css/employee/employeeHeaderView.css') ?>">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* ── Password strength ── */
        .strength-bar { height: 4px; border-radius: 2px; transition: width .3s, background .3s; }
        .strength-label { font-size: 0.75rem; font-weight: 600; margin-top: 4px; }
        .pw-toggle { position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
                     background: none; border: none; color: #94a3b8; cursor: pointer; }
        .pw-toggle:hover { color: #4f46e5; }
        .pw-wrap { position: relative; }
    </style>
</head>

<!-- ── Topbar Navbar ── -->
<nav class="navbar navbar-expand-lg navbar-dark shadow-sm"
     style="background: linear-gradient(90deg, #4f46e5, #7c3aed);">
    <div class="container">
        <a class="navbar-brand fw-bold" href="<?= base_url('Employee/EmployeeOverview') ?>">
            <i class="fas fa-building me-2"></i>Suropriyo Enterprise
        </a>
        <div class="ms-auto d-flex align-items-center gap-3">
            <span class="text-white small d-none d-md-flex align-items-center gap-2">
                <span class="rounded-circle d-inline-flex align-items-center justify-content-center"
                      style="width:30px;height:30px;background:rgba(255,255,255,0.2);font-size:0.75rem;font-weight:700;">
                    <?= strtoupper(substr($this->session->userdata('empname'), 0, 1)) ?>
                </span>
                <?= htmlspecialchars($this->session->userdata('empname')) ?>
            </span>
            <a href="<?= base_url() ?>Employee/Logout"
               class="btn btn-light btn-sm rounded-pill px-3 fw-semibold">
                <i class="fas fa-sign-out-alt me-1"></i>Logout
            </a>
        </div>
    </div>
</nav>

<div class="container py-4">

    <!-- ── Welcome Banner ── -->
    <div class="card border-0 shadow-lg rounded-4 mb-4 overflow-hidden">
        <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-3 p-4">
            <div>
                <h4 class="fw-bold text-primary mb-1">
                    <i class="fas fa-user-tie me-2"></i>Welcome, <?= htmlspecialchars($this->session->userdata('empname')) ?>
                </h4>
                <small class="text-muted">
                    <i class="fas fa-calendar-day me-1"></i>
                    <?= date('l, d F Y') ?>
                </small>
            </div>
            <button class="btn btn-warning rounded-pill px-4 fw-semibold shadow-sm"
                    data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                <i class="fas fa-key me-2"></i>Change Password
            </button>
        </div>
    </div>

    <!-- ── Navigation Tabs ── -->
    <div class="card border-0 shadow-sm rounded-4 p-3 mb-2">
        <div class="d-flex justify-content-center flex-wrap gap-2">
            <a href="<?= base_url('Employee/EmployeeOverview') ?>"
               class="btn rounded-pill px-4 nav-section-btn <?= (uri_string() == 'Employee/EmployeeOverview') ? 'btn-primary active' : 'btn-outline-primary' ?>">
                <i class="fas fa-home me-2"></i>Overview
            </a>
            <a href="<?= base_url('Employee/EmployeeAttendence') ?>"
               class="btn rounded-pill px-4 nav-section-btn <?= (uri_string() == 'Employee/EmployeeAttendence') ? 'btn-primary active' : 'btn-outline-primary' ?>">
                <i class="fas fa-clock me-2"></i>Attendance
            </a>
            <a href="<?= base_url('Employee/EmployeeRequest') ?>"
               class="btn rounded-pill px-4 nav-section-btn <?= (uri_string() == 'Employee/EmployeeRequest') ? 'btn-primary active' : 'btn-outline-primary' ?>">
                <i class="fas fa-paper-plane me-2"></i>Requests
            </a>
            <a href="<?= base_url('Employee/mySalarySlips') ?>"
               class="btn rounded-pill px-4 nav-section-btn <?= (uri_string() == 'Employee/mySalarySlips') ? 'btn-primary active' : 'btn-outline-primary' ?>">
                <i class="fas fa-file-invoice-dollar me-2"></i>Salary Slips
            </a>
        </div>
    </div>

</div><!-- /container -->


<!--  CHANGE PASSWORD MODAL -->
<div class="modal fade" id="changePasswordModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden">

            <div class="modal-header border-0 pb-2"
                 style="background: linear-gradient(135deg, #4f46e5, #7c3aed);">
                <h5 class="modal-title fw-bold text-white">
                    <i class="fas fa-key me-2"></i>Change Password
                </h5>
                <button type="button" class="btn-close btn-close-white"
                        data-bs-dismiss="modal"></button>
            </div>

            <?= form_open('Employee/ChangePassword', ['id'=>'changePassForm']) ?>

            <div class="modal-body px-4 pt-4 pb-2">

                <!-- Current Password -->
                <div class="mb-3">
                    <label class="form-label fw-semibold small">Current Password</label>
                    <div class="pw-wrap">
                        <input id="oldpass" name="oldpass" type="password"
                               class="form-control pe-5" required
                               placeholder="Enter current password">
                        <button type="button" class="pw-toggle" onclick="togglePw('oldpass','ei1')">
                            <i class="fas fa-eye" id="ei1"></i>
                        </button>
                    </div>
                </div>

                <!-- New Password -->
                <div class="mb-1">
                    <label class="form-label fw-semibold small">New Password</label>
                    <div class="pw-wrap">
                        <input id="newpass" name="newpass" type="password"
                               class="form-control pe-5" required
                               placeholder="Min. 8 characters"
                               oninput="evalStrength(this.value)">
                        <button type="button" class="pw-toggle" onclick="togglePw('newpass','ei2')">
                            <i class="fas fa-eye" id="ei2"></i>
                        </button>
                    </div>
                    <!-- Strength meter -->
                    <div class="mt-2" style="background:#e2e8f0;border-radius:2px;height:4px;">
                        <div id="strengthBar" class="strength-bar" style="width:0%;background:#ef4444;"></div>
                    </div>
                    <div id="strengthLabel" class="strength-label text-muted"></div>
                </div>

                <!-- Confirm Password -->
                <div class="mb-3 mt-3">
                    <label class="form-label fw-semibold small">Confirm New Password</label>
                    <div class="pw-wrap">
                        <input id="confirmpass" name="confirmpass" type="password"
                               class="form-control pe-5" required
                               placeholder="Re-enter new password"
                               oninput="checkPassMatch()">
                        <button type="button" class="pw-toggle" onclick="togglePw('confirmpass','ei3')">
                            <i class="fas fa-eye" id="ei3"></i>
                        </button>
                    </div>
                    <div id="passMatchMsg" class="small mt-1" style="display:none;"></div>
                </div>

            </div><!-- /modal-body -->

            <div class="modal-footer border-0 pt-0 pb-4 px-4 gap-2">
                <button type="button" class="btn btn-light rounded-pill px-4"
                        data-bs-dismiss="modal">Cancel</button>
                <button type="submit" id="changePassBtn"
                        class="btn btn-primary rounded-pill px-5 fw-bold">
                    <i class="fas fa-save me-1"></i>Update Password
                </button>
            </div>

            <?= form_close() ?>

        </div>
    </div>
</div>


<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script>
/* ── Password visibility toggle ── */
function togglePw(id, iconId) {
    const f = document.getElementById(id);
    const i = document.getElementById(iconId);
    if (f.type === 'password') {
        f.type = 'text';
        i.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        f.type = 'password';
        i.classList.replace('fa-eye-slash', 'fa-eye');
    }
}

/* ── Password strength meter ── */
function evalStrength(pw) {
    const bar   = document.getElementById('strengthBar');
    const label = document.getElementById('strengthLabel');
    let score = 0;
    if (pw.length >= 8)  score++;
    if (/[A-Z]/.test(pw)) score++;
    if (/[0-9]/.test(pw)) score++;
    if (/[^A-Za-z0-9]/.test(pw)) score++;

    const levels = [
        { pct:'25%', color:'#ef4444', text:'Weak' },
        { pct:'50%', color:'#f59e0b', text:'Fair' },
        { pct:'75%', color:'#3b82f6', text:'Good' },
        { pct:'100%',color:'#10b981', text:'Strong' },
    ];
    const lvl = levels[score > 0 ? score - 1 : 0];
    bar.style.width     = pw.length ? lvl.pct  : '0%';
    bar.style.background = lvl.color;
    label.textContent   = pw.length ? lvl.text : '';
    label.style.color   = lvl.color;
    checkPassMatch();
}

/* ── Confirm password match ── */
function checkPassMatch() {
    const np  = document.getElementById('newpass').value;
    const cp  = document.getElementById('confirmpass').value;
    const msg = document.getElementById('passMatchMsg');
    if (!cp) { msg.style.display = 'none'; return; }
    if (np === cp) {
        msg.innerHTML   = '<i class="fas fa-check-circle text-success me-1"></i>Passwords match';
        msg.style.color = '#10b981';
    } else {
        msg.innerHTML   = '<i class="fas fa-times-circle text-danger me-1"></i>Passwords do not match';
        msg.style.color = '#ef4444';
    }
    msg.style.display = 'block';
}

/* ── Change password form ── */
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('changePassForm');
    if (!form) return;
    form.addEventListener('submit', function (e) {
        const np = document.getElementById('newpass').value;
        const cp = document.getElementById('confirmpass').value;

        if (np.length < 8) {
            e.preventDefault();
            Swal.fire({ icon:'error', title:'Password Too Short',
                text:'New password must be at least 8 characters.', confirmButtonColor:'#4f46e5' });
            return;
        }
        if (np !== cp) {
            e.preventDefault();
            Swal.fire({ icon:'error', title:'Passwords Don\'t Match',
                text:'New password and confirm password must be identical.', confirmButtonColor:'#4f46e5' });
        }
    });

    // Flash messages for password change result
    <?php if ($this->session->flashdata('pass_success')): ?>
    Swal.fire({ icon:'success', title:'Password Updated!',
        text:<?= json_encode($this->session->flashdata('pass_success')) ?>,
        confirmButtonColor:'#4f46e5' });
    <?php endif; ?>

    <?php if ($this->session->flashdata('pass_error')): ?>
    Swal.fire({ icon:'error', title:'Update Failed',
        text:<?= json_encode($this->session->flashdata('pass_error')) ?>,
        confirmButtonColor:'#4f46e5' });
    <?php endif; ?>
});
</script>
