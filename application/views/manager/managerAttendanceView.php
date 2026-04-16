<?php
defined('BASEPATH') OR exit('No direct script access allowed');
// Branch is locked from session – never trust user input for managers
$manager_branch = $this->session->userdata('branch');
$branch_label   = ucfirst(strtolower($manager_branch));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance | <?= $branch_label ?> Branch</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="<?= base_url('css/admin/adminAttendanceView.css') ?>" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .branch-lock-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(255,255,255,0.15);
            border: 1px solid rgba(255,255,255,0.3);
            color: #fff;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.82rem;
            font-weight: 600;
            letter-spacing: 0.3px;
        }
        .branch-lock-badge i { font-size: 0.75rem; }
    </style>
</head>
<body>

<?php if ($this->session->flashdata('msg')): ?>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            Swal.fire({
                title: 'Notice',
                text: <?= json_encode($this->session->flashdata('msg')) ?>,
                icon: 'info',
                confirmButtonColor: '#461bb9'
            });
        });
    </script>
<?php endif; ?>

<div class="main-content">

    <!-- ── Top Status Card ── -->
    <div class="status-header mb-4">
        <div class="card bg-primary text-white shadow-sm border-0">
            <div class="card-body p-4">
                <div class="row align-items-center text-center text-md-start">
                    <div class="col-md-5 border-md-end border-white-50 mb-3 mb-md-0">
                        <h5 class="mb-1 opacity-75">Manager Portal</h5>
                        <h3 class="mb-0 fw-bold">
                            <?= $this->session->userdata('empname') ?? 'Manager' ?>
                        </h3>
                    </div>
                    <div class="col-md-7 text-md-end">
                        <span class="branch-lock-badge">
                            <i class="fas fa-lock"></i>
                            Viewing: <?= $branch_label ?> Branch Only
                        </span>
                        <div class="mt-2 opacity-75" style="font-size:0.82rem;">
                            You can only view attendance records for your assigned branch.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ── Search Form ── -->
    <div class="attendance-search-form">
        <div class="search-form-wrapper">

            <div class="search-group">
                <label class="text-white"><b>Employee ID / Name</b></label>
                <input type="text" id="searchempid" name="searchempid" class="search-bar"
                       placeholder="Enter ID or name"
                       maxlength="50"
                       autocomplete="off">
            </div>

            <!-- Branch is HIDDEN and auto-locked; not shown to manager -->
            <input type="hidden" id="branchFilter" value="<?= htmlspecialchars($manager_branch, ENT_QUOTES, 'UTF-8') ?>">

            <div class="search-group">
                <label class="text-white"><b>Start Date</b></label>
                <input type="date" id="startdate" name="startdate" class="search-bar">
            </div>

            <div class="search-group">
                <label class="text-white"><b>End Date</b></label>
                <input type="date" id="enddate" name="enddate" class="search-bar">
            </div>

            <div class="search-group">
                <button type="button" id="ajaxSearchBtn" class="search-btn">
                    <i class="fas fa-search"></i> Search
                </button>
            </div>
        </div>

        <!-- ── Attendance Table ── -->
        <div class="table-section mt-4">
            <h2 class="table-title">
                <?= $branch_label ?> Branch — Attendance Records
            </h2>
            <table class="table-custom">
                <thead>
                    <tr>
                        <th><i class="fas fa-calendar-day me-2"></i>Date</th>
                        <th><i class="fas fa-id-badge me-2"></i>Employee ID</th>
                        <th><i class="fas fa-user me-2"></i>Employee Name</th>
                        <th><i class="fas fa-clock me-2"></i>Login Time</th>
                        <th><i class="fas fa-clock me-2"></i>Logout Time</th>
                        <th><i class="fas fa-laptop me-2"></i>Device</th>
                        <th><i class="fas fa-network-wired me-2"></i>IP Address</th>
                    </tr>
                </thead>
                <tbody id="attendanceTable">
                    <?php if (!empty($atten)): ?>
                        <?php foreach ($atten as $att): ?>
                            <tr>
                                <td><?= date("d-M-Y", strtotime($att->seemp_logdate)) ?></td>
                                <td><span class="emp-id"><?= htmlspecialchars($att->seemp_logempid, ENT_QUOTES, 'UTF-8') ?></span></td>
                                <td><strong><?= htmlspecialchars($att->seempd_name ?? 'Unknown', ENT_QUOTES, 'UTF-8') ?></strong></td>
                                <td class="login-time">
                                    <i class="fas fa-sign-in-alt me-1"></i>
                                    <?= date("h:i A", strtotime($att->seemp_logintime)) ?>
                                </td>
                                <td class="logout-time">
                                    <i class="fas fa-sign-out-alt me-1"></i>
                                    <?= ($att->seemp_logouttime && $att->seemp_logouttime != '0000-00-00 00:00:00')
                                        ? date("h:i A", strtotime($att->seemp_logouttime))
                                        : '<span class="text-muted">Not Logged Out</span>' ?>
                                </td>
                                <td><small class="text-muted"><?= htmlspecialchars($att->seemp_device_info ?? 'N/A', ENT_QUOTES, 'UTF-8') ?></small></td>
                                <td><code><?= htmlspecialchars($att->seemp_ip_address ?? '0.0.0.0', ENT_QUOTES, 'UTF-8') ?></code></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center p-4">No attendance records found for <?= $branch_label ?> branch.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div><!-- /.attendance-search-form -->
</div><!-- /.main-content -->

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function () {

    let isSearching = false;
    let originalData = [];

    /* ── Today string (local, no UTC drift) ─────────────────────────── */
    function getTodayStr() {
        const d = new Date();
        return d.getFullYear() + '-' +
               String(d.getMonth() + 1).padStart(2, '0') + '-' +
               String(d.getDate()).padStart(2, '0');
    }
    const todayStr = getTodayStr();

    /* Cap future dates on the calendar pickers */
    $('#startdate, #enddate').attr('max', todayStr);

    /* ── Error helpers ────────────────────────────────────────────────── */
    function showError(fieldId, message) {
        const $f = $('#' + fieldId);
        $f.addClass('is-invalid');
        if (!$('#err-' + fieldId).length) {
            $f.after(`<div id="err-${fieldId}" class="invalid-feedback d-block"
                       style="color:#f87171;font-size:12px;margin-top:4px;">${message}</div>`);
        } else {
            $('#err-' + fieldId).text(message).show();
        }
    }

    function clearError(fieldId) {
        $('#' + fieldId).removeClass('is-invalid is-valid');
        $('#err-' + fieldId).remove();
    }

    function clearAllErrors() {
        ['searchempid', 'startdate', 'enddate'].forEach(clearError);
    }

    /* ── Spinner helpers ──────────────────────────────────────────────── */
    function startSpinner() {
        $('#ajaxSearchBtn i').removeClass('fa-search').addClass('fa-spinner fa-spin');
    }
    function stopSpinner() {
        $('#ajaxSearchBtn i').removeClass('fa-spinner fa-spin').addClass('fa-search');
    }

    /* ── Input validation ─────────────────────────────────────────────── */
    function validateInputs() {
        clearAllErrors();
        const empid    = $('#searchempid').val().trim();
        const startVal = $('#startdate').val();
        const endVal   = $('#enddate').val();
        let valid = true;

        /* Employee ID: alphanumeric, hyphens, underscores only */
        if (empid !== '' && !/^[a-zA-Z0-9_\-\s]+$/.test(empid)) {
            showError('searchempid', 'Invalid characters in Employee ID / Name field.');
            valid = false;
        }

        if (startVal !== '' && startVal > todayStr) {
            showError('startdate', 'Start date cannot be in the future.');
            valid = false;
        }

        if (endVal !== '' && endVal > todayStr) {
            showError('enddate', 'End date cannot be in the future.');
            valid = false;
        }

        if (startVal !== '' && endVal !== '' && startVal > endVal) {
            showError('startdate', 'Start date must be before or equal to end date.');
            showError('enddate', 'End date must be after or equal to start date.');
            valid = false;
        }

        if (!valid) return false;

        /* Warn for ranges > 1 year */
        if (startVal !== '' && endVal !== '') {
            const diffDays = Math.ceil(
                (new Date(endVal + 'T00:00:00') - new Date(startVal + 'T00:00:00')) /
                (1000 * 60 * 60 * 24)
            );
            if (diffDays > 365) {
                stopSpinner();
                Swal.fire({
                    title: 'Large Date Range',
                    html: `Searching across <strong>${diffDays} days</strong> may take a moment. Continue?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Search',
                    cancelButtonText: 'Cancel',
                    confirmButtonColor: '#461bb9'
                }).then(function (r) {
                    if (r.isConfirmed) { startSpinner(); _fireAjaxSearch(); }
                });
                return false;
            }
        }
        return true;
    }

    /* ── Live client-side filter ──────────────────────────────────────── */
    function storeOriginalData() {
        originalData = [];
        $('#attendanceTable tr').each(function () {
            if ($(this).find('td').length > 1) {
                originalData.push({ html: $(this)[0].outerHTML });
            }
        });
    }

    function performLiveFilter(term) {
        const $tbody = $('#attendanceTable');
        $tbody.empty();
        let count = 0;
        originalData.forEach(function (r) {
            const $row = $(r.html);
            if (!term || $row.text().toLowerCase().includes(term.toLowerCase())) {
                $tbody.append(r.html);
                count++;
            }
        });
        if (count === 0) {
            $tbody.html('<tr><td colspan="7" class="text-center p-4"><i class="fas fa-search me-2"></i>No matching records in current view. Click <strong>Search</strong> to query the database.</td></tr>');
        }
    }

    storeOriginalData();

    let liveTimeout;
    $('#searchempid').on('input', function () {
        clearError('searchempid');
        clearTimeout(liveTimeout);
        const term = $(this).val();
        liveTimeout = setTimeout(() => performLiveFilter(term), 300);
    });

    /* ── Real-time date cross-validation ─────────────────────────────── */
    $('#startdate').on('change', function () {
        clearError('startdate'); clearError('enddate');
        const s = $(this).val(), e = $('#enddate').val();
        if (s) $('#enddate').attr('min', s);
        if (s && s > todayStr) showError('startdate', 'Start date cannot be in the future.');
        else if (s && e && s > e) showError('startdate', 'Start date cannot be after end date.');
    });

    $('#enddate').on('change', function () {
        clearError('enddate'); clearError('startdate');
        const s = $('#startdate').val(), e = $(this).val();
        if (e && e > todayStr) {
            showError('enddate', 'End date cannot be in the future.');
            $(this).val(todayStr); return;
        }
        if (s && e && s > e) showError('enddate', 'End date must be on or after start date.');
    });

    /* ── AJAX search ──────────────────────────────────────────────────── */
    function _fireAjaxSearch() {
        if (isSearching) return;

        const empid  = $('#searchempid').val().trim();
        const start  = $('#startdate').val();
        const end    = $('#enddate').val();
        /* Branch is ALWAYS taken from the hidden field — the server also enforces it */
        const branch = $('#branchFilter').val();

        const $tbody = $('#attendanceTable');
        $tbody.html(`<tr><td colspan="7" class="text-center p-4">
            <div class="spinner-border text-primary" role="status"></div>
            <div class="mt-2">Searching <?= $branch_label ?> branch records...</div>
        </td></tr>`);

        isSearching = true;

        $.ajax({
            url: '<?= base_url('Manager/fetchAttendanceAjax') ?>',
            type: 'POST',
            data: {
                empid     : empid,
                startdate : start,
                enddate   : end,
                branch    : branch,   /* server ignores this and uses session branch */
                '<?= $this->security->get_csrf_token_name() ?>': '<?= $this->security->get_csrf_hash() ?>'
            },
            dataType: 'json',
            timeout: 15000,
            success: function (response) {
                let html = '';
                if (response && response.data && response.data.length > 0) {
                    response.data.forEach(function (att) {
                        const name    = att.seempd_name     ? escHtml(att.seempd_name)         : 'Unknown Employee';
                        const device  = att.seemp_device_info ? escHtml(att.seemp_device_info) : 'N/A';
                        const ip      = att.seemp_ip_address  ? escHtml(att.seemp_ip_address)  : '0.0.0.0';
                        const empid_d = escHtml(att.seemp_logempid);
                        html += `<tr>
                            <td>${escHtml(att.formatted_date)}</td>
                            <td><span class="emp-id">${empid_d}</span></td>
                            <td><strong>${name}</strong></td>
                            <td class="login-time"><i class="fas fa-sign-in-alt me-1"></i>${escHtml(att.formatted_login)}</td>
                            <td class="logout-time"><i class="fas fa-sign-out-alt me-1"></i>${att.formatted_logout}</td>
                            <td><small class="text-muted">${device}</small></td>
                            <td><code>${ip}</code></td>
                        </tr>`;
                    });
                    html = `<tr class="table-success"><td colspan="7" class="p-2">
                                <small><i class="fas fa-database me-1"></i>Found ${response.data.length} record(s) — <?= $branch_label ?> branch</small>
                            </td></tr>` + html;
                } else {
                    html = `<tr><td colspan="7" class="text-center p-4 text-muted">
                        <i class="fas fa-inbox fa-2x d-block mb-2"></i>
                        No attendance records found for the selected criteria in <?= $branch_label ?> branch.
                    </td></tr>`;
                }
                $tbody.html(html);
                storeOriginalData();
            },
            error: function (xhr, status) {
                let msg = 'Search failed. Please try again.';
                if (status === 'timeout') msg = 'Search timed out. Try a narrower date range.';
                else if (xhr.status === 403) msg = 'Session expired. Please refresh the page.';
                $tbody.html(`<tr><td colspan="7" class="text-center text-danger p-4">
                    <i class="fas fa-exclamation-triangle fa-2x mb-2 d-block"></i>${msg}
                    <button class="btn btn-outline-danger btn-sm mt-2" onclick="location.reload()">
                        <i class="fas fa-refresh"></i> Retry
                    </button></td></tr>`);
            },
            complete: function () {
                isSearching = false;
                stopSpinner();
            }
        });
    }

    /* Escape HTML to prevent XSS in dynamic content */
    function escHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    /* ── Button click ─────────────────────────────────────────────────── */
    $('#ajaxSearchBtn').on('click', function (e) {
        e.preventDefault();
        clearAllErrors();
        startSpinner();
        if (!validateInputs()) { stopSpinner(); return; }
        _fireAjaxSearch();
    });

    /* Enter key triggers search */
    $('.search-bar').on('keypress', function (e) {
        if (e.which === 13) {
            e.preventDefault();
            $('#ajaxSearchBtn').trigger('click');
        }
    });

    /* ── Default date range: last 30 days ─────────────────────────────── */
    const thirtyDaysAgo = new Date();
    thirtyDaysAgo.setDate(thirtyDaysAgo.getDate() - 30);
    const thirtyStr = thirtyDaysAgo.getFullYear() + '-' +
                      String(thirtyDaysAgo.getMonth() + 1).padStart(2, '0') + '-' +
                      String(thirtyDaysAgo.getDate()).padStart(2, '0');
    $('#startdate').val(thirtyStr);
    $('#enddate').val(todayStr);
    $('#enddate').attr('min', thirtyStr);

});
</script>
</body>
</html>