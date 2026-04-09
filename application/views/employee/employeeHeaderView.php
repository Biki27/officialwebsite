<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('css/employee/employeeHeaderView.css') ?>">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .strength-bar { height: 4px; border-radius: 2px; transition: width .3s, background .3s; }
        .strength-label { font-size: 0.73rem; font-weight: 600; margin-top: 4px; }
        .pw-toggle { position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
                     background: none; border: none; color: #94a3b8; cursor: pointer; transition: color .2s; }
        .pw-toggle:hover { color: #4338ca; }
        .pw-wrap { position: relative; }
    </style>
</head>

<!-- ── Enterprise Topbar ── -->
<nav class="ent-navbar" id="mainNavbar">
    <div class="container-xl nav-inner">
        <!-- Brand -->
        <a class="nav-brand" href="<?= base_url('Employee/EmployeeOverview') ?>">
            <span class="brand-icon"><i class="fas fa-building"></i></span>
            <div class="brand-text">
                <span class="brand-name">Suropriyo</span>
                <span class="brand-tag">Enterprise</span>
            </div>
        </a>

        <!-- Right section -->
        <div class="nav-right">
            <div class="nav-user d-none d-md-flex">
                <div class="user-avatar">
                    <?= strtoupper(substr($this->session->userdata('empname'), 0, 1)) ?>
                </div>
                <div class="user-meta">
                    <span class="user-name"><?= htmlspecialchars($this->session->userdata('empname')) ?></span>
                    <span class="user-role">Employee</span>
                </div>
            </div>
            <button class="btn-change-pass d-none d-md-flex" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                <i class="fas fa-key me-1"></i> Change Password
            </button>
            <a href="<?= base_url() ?>Employee/Logout" class="btn-logout">
                <i class="fas fa-sign-out-alt me-1"></i><span class="d-none d-sm-inline">Logout</span>
            </a>
            <!-- Mobile menu toggle -->
            <button class="mobile-menu-toggle d-md-none" id="mobileMenuToggle">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </div>

    <!-- Mobile dropdown -->
    <div class="mobile-dropdown" id="mobileDropdown">
        <div class="mobile-user">
            <div class="user-avatar-sm">
                <?= strtoupper(substr($this->session->userdata('empname'), 0, 1)) ?>
            </div>
            <div>
                <div class="fw-semibold"><?= htmlspecialchars($this->session->userdata('empname')) ?></div>
                <div style="font-size:0.78rem;opacity:0.65">Employee Portal</div>
            </div>
        </div>
        <button class="mobile-pass-btn" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
            <i class="fas fa-key me-2"></i>Change Password
        </button>
    </div>
</nav>

<div class="container-xl px-3 px-md-4 pt-4">

    <!-- ── Welcome Banner ── -->
    <div class="welcome-banner mb-4">
        <div class="welcome-left">
            <div class="welcome-avatar">
                <?= strtoupper(substr($this->session->userdata('empname'), 0, 2)) ?>
            </div>
            <div>
                <div class="welcome-greeting">Good <?= (date('H') < 12) ? 'Morning' : ((date('H') < 17) ? 'Afternoon' : 'Evening') ?></div>
                <h4 class="welcome-name"><?= htmlspecialchars($this->session->userdata('empname')) ?></h4>
                <div class="welcome-date"><i class="fas fa-calendar-alt me-1"></i><?= date('l, d F Y') ?></div>
            </div>
        </div>
        <!-- <div class="welcome-badge d-none d-sm-flex">
            <i class="fas fa-shield-check me-1"></i> Verified Employee
        </div> -->
    </div>

    <!-- ── Navigation Tabs ── -->
    <div class="ent-nav-tabs mb-2">
        <a href="<?= base_url('Employee/EmployeeOverview') ?>"
           class="ent-tab <?= (uri_string() == 'Employee/EmployeeOverview') ? 'active' : '' ?>">
            <i class="fas fa-th-large me-2"></i><span>Overview</span>
        </a>
        <a href="<?= base_url('Employee/EmployeeAttendence') ?>"
           class="ent-tab <?= (uri_string() == 'Employee/EmployeeAttendence') ? 'active' : '' ?>">
            <i class="fas fa-clock me-2"></i><span>Attendance</span>
        </a>
        <a href="<?= base_url('Employee/EmployeeRequest') ?>"
           class="ent-tab <?= (uri_string() == 'Employee/EmployeeRequest') ? 'active' : '' ?>">
            <i class="fas fa-paper-plane me-2"></i><span>Requests</span>
        </a>
        <a href="<?= base_url('Employee/mySalarySlips') ?>"
           class="ent-tab <?= (uri_string() == 'Employee/mySalarySlips') ? 'active' : '' ?>">
            <i class="fas fa-file-invoice-dollar me-2"></i><span>Salary Slips</span>
        </a>
    </div>

</div><!-- /container-xl -->


<!-- CHANGE PASSWORD MODAL -->
<div class="modal fade" id="changePasswordModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content ent-modal rounded-4 border-0 shadow-lg overflow-hidden">

            <div class="modal-header ent-modal-header border-0 pb-2">
                <div>
                    <h5 class="modal-title fw-bold text-white mb-0">
                        <i class="fas fa-key me-2"></i>Change Password
                    </h5>
                    <small class="text-white opacity-65">Update your account credentials</small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <?= form_open('Employee/ChangePassword', ['id'=>'changePassForm']) ?>

            <div class="modal-body px-4 pt-4 pb-2">
                <!-- Current Password -->
                <div class="mb-3">
                    <label class="ent-label">Current Password</label>
                    <div class="pw-wrap">
                        <input id="oldpass" name="oldpass" type="password"
                               class="ent-input pe-5" required placeholder="Enter current password">
                        <button type="button" class="pw-toggle" onclick="togglePw('oldpass','ei1')">
                            <i class="fas fa-eye" id="ei1"></i>
                        </button>
                    </div>
                </div>
                <!-- New Password -->
                <div class="mb-1">
                    <label class="ent-label">New Password</label>
                    <div class="pw-wrap">
                        <input id="newpass" name="newpass" type="password"
                               class="ent-input pe-5" required placeholder="Min. 8 characters"
                               oninput="evalStrength(this.value)">
                        <button type="button" class="pw-toggle" onclick="togglePw('newpass','ei2')">
                            <i class="fas fa-eye" id="ei2"></i>
                        </button>
                    </div>
                    <div class="mt-2" style="background:#e2e8f0;border-radius:4px;height:4px;">
                        <div id="strengthBar" class="strength-bar" style="width:0%;background:#ef4444;"></div>
                    </div>
                    <div id="strengthLabel" class="strength-label text-muted"></div>
                </div>
                <!-- Confirm Password -->
                <div class="mb-3 mt-3">
                    <label class="ent-label">Confirm New Password</label>
                    <div class="pw-wrap">
                        <input id="confirmpass" name="confirmpass" type="password"
                               class="ent-input pe-5" required placeholder="Re-enter new password"
                               oninput="checkPassMatch()">
                        <button type="button" class="pw-toggle" onclick="togglePw('confirmpass','ei3')">
                            <i class="fas fa-eye" id="ei3"></i>
                        </button>
                    </div>
                    <div id="passMatchMsg" class="small mt-1" style="display:none;"></div>
                </div>
            </div>

            <div class="modal-footer border-0 pt-0 pb-4 px-4 gap-2">
                <button type="button" class="btn-ent-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" id="changePassBtn" class="btn-ent-primary">
                    <i class="fas fa-save me-1"></i>Update Password
                </button>
            </div>
            <?= form_close() ?>
        </div>
    </div>
</div>


<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script>
/* Mobile menu */
document.getElementById('mobileMenuToggle').addEventListener('click', function () {
    const d = document.getElementById('mobileDropdown');
    d.classList.toggle('open');
    this.querySelector('i').classList.toggle('fa-bars');
    this.querySelector('i').classList.toggle('fa-times');
});

/* Password visibility */
function togglePw(id, iconId) {
    const f = document.getElementById(id);
    const i = document.getElementById(iconId);
    f.type = f.type === 'password' ? 'text' : 'password';
    i.classList.toggle('fa-eye');
    i.classList.toggle('fa-eye-slash');
}

/* Strength meter */
function evalStrength(pw) {
    const bar   = document.getElementById('strengthBar');
    const label = document.getElementById('strengthLabel');
    let score = 0;
    if (pw.length >= 8)          score++;
    if (/[A-Z]/.test(pw))        score++;
    if (/[0-9]/.test(pw))        score++;
    if (/[^A-Za-z0-9]/.test(pw)) score++;
    const levels = [
        { pct:'25%', color:'#ef4444', text:'Weak'   },
        { pct:'50%', color:'#f59e0b', text:'Fair'   },
        { pct:'75%', color:'#3b82f6', text:'Good'   },
        { pct:'100%',color:'#10b981', text:'Strong' },
    ];
    const lvl = levels[score > 0 ? score - 1 : 0];
    bar.style.width      = pw.length ? lvl.pct   : '0%';
    bar.style.background = lvl.color;
    label.textContent    = pw.length ? lvl.text  : '';
    label.style.color    = lvl.color;
    checkPassMatch();
}

function checkPassMatch() {
    const np  = document.getElementById('newpass').value;
    const cp  = document.getElementById('confirmpass').value;
    const msg = document.getElementById('passMatchMsg');
    if (!cp) { msg.style.display = 'none'; return; }
    msg.innerHTML   = np === cp
        ? '<i class="fas fa-check-circle text-success me-1"></i>Passwords match'
        : '<i class="fas fa-times-circle text-danger me-1"></i>Passwords do not match';
    msg.style.color   = np === cp ? '#10b981' : '#ef4444';
    msg.style.display = 'block';
}

document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('changePassForm');
    if (!form) return;
    form.addEventListener('submit', function (e) {
        const np = document.getElementById('newpass').value;
        const cp = document.getElementById('confirmpass').value;
        if (np.length < 8) {
            e.preventDefault();
            Swal.fire({ icon:'error', title:'Password Too Short',
                text:'New password must be at least 8 characters.', confirmButtonColor:'#4338ca' });
            return;
        }
        if (np !== cp) {
            e.preventDefault();
            Swal.fire({ icon:'error', title:'Passwords Don\'t Match',
                text:'New password and confirm password must be identical.', confirmButtonColor:'#4338ca' });
        }
    });

    <?php if ($this->session->flashdata('pass_success')): ?>
    Swal.fire({ icon:'success', title:'Password Updated!',
        text:<?= json_encode($this->session->flashdata('pass_success')) ?>, confirmButtonColor:'#4338ca' });
    <?php endif; ?>
    <?php if ($this->session->flashdata('pass_error')): ?>
    Swal.fire({ icon:'error', title:'Update Failed',
        text:<?= json_encode($this->session->flashdata('pass_error')) ?>, confirmButtonColor:'#4338ca' });
    <?php endif; ?>
});
</script>
