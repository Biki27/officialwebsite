<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance | Supropriyo Enterprise</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="<?= base_url('css/admin/adminAttendanceView.css') ?>" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <?php if (isset($alert)):
        $isError = (stripos($alert, 'error') !== false || stripos($alert, 'failed') !== false);
        ?>
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                Swal.fire({
                    title: '<?= $isError ? "Notice" : "Success" ?>',
                    text: <?= json_encode($alert) ?>,
                    icon: '<?= $isError ? "warning" : "success" ?>',
                    confirmButtonColor: '#461bb9',
                    customClass: {
                        popup: 'rounded-4 shadow-lg'
                    }
                });
            });
        </script>
    <?php endif; ?>

    <div class="main-content">
        <div class="status-header mb-4">
            <div class="card bg-primary text-white shadow-sm border-0">
                <div class="card-body p-4">
                    <div class="row align-items-center text-center text-md-start">
                        <div class="col-md-4 border-md-end border-white-50 mb-3 mb-md-0">
                            <h5 class="mb-1 opacity-75">Employee Details</h5>
                            <h3 class="mb-0 fw-bold">
                                <?= ($this->session->userdata("accesslevel") == 'HR') ? "HR's" : "Admin's" ?> :
                                <?= $this->session->userdata("empname") ?>
                            </h3>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div class="attendance-search-form">

            <div class="search-form-wrapper">
                <div class="search-group">
                    <label class="text-white"><b>Employee ID</b></label>
                    <input type="text" id="searchempid" name="searchempid" class="search-bar" placeholder="Enter ID">
                </div>
                <div class="search-group">
                    <label class="text-white"><b>Branch</b></label>
                    <select id="branchFilter" name="branch" class="search-bar" style="background: white; color: black;">
                        <option value="">All Branches</option>
                        <option value="KOLKATA">Kolkata</option>
                        <option value="HOWRAH">Howrah</option>
                    </select>
                </div>
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



            <div class="table-section mt-4">
                <h2 class="table-title">Attendance Records</h2>
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th><i class="fas fa-calendar-day me-2"></i>Date</th>
                            <th><i class="fas fa-id-badge me-2"></i>Employee ID</th>
                            <th><i class="fas fa-user me-2"></i>Employee Name</th>
                            <th><i class="fas fa-clock me-2"></i>Login Time</th>
                            <th><i class="fas fa-clock me-3"></i>Logout Time</th>
                            <th><i class="fas fa-laptop me-2"></i>Device</th>
                            <th><i class="fas fa-network-wired me-2"></i>IP Address</th>
                        </tr>
                    </thead>
                    <tbody id="attendanceTable">
                        <?php if (!empty($atten)): ?>
                            <?php foreach ($atten as $att): ?>
                                <tr>
                                    <td><?= date("d-M-Y", strtotime($att->seemp_logdate)) ?></td>
                                    <td><span class="emp-id"><?= $att->seemp_logempid ?></span></td>
                                    <td><strong><?= $att->seempd_name ?? 'Unknown Employee' ?></strong></td>
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
                                    <td><small class="text-muted"><?= $att->seemp_device_info ?? 'N/A' ?></small></td>
                                    <td><code><?= $att->seemp_ip_address ?? '0.0.0.0' ?></code></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center p-4">No attendance records found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script>
            $(document).ready(function () {

                let isSearching = false;
                let originalData = [];

                // ─── TODAY STRING (used for all comparisons — avoids timezone bugs) ──────────
                // new Date("YYYY-MM-DD") parses as UTC midnight, which can be "yesterday" or
                // "tomorrow" in local time. String comparison is always exact for ISO dates.
                function getTodayStr() {
                    const d = new Date();
                    const y = d.getFullYear();
                    const m = String(d.getMonth() + 1).padStart(2, '0');
                    const day = String(d.getDate()).padStart(2, '0');
                    return `${y}-${m}-${day}`; // local date, no timezone shift
                }
                const todayStr = getTodayStr();

                // Cap both date inputs so the calendar won't show future dates
                $('#startdate, #enddate').attr('max', todayStr);

                // ─── ERROR HELPERS ────────────────────────────────────────────────────────────

                function showError(fieldId, message) {
                    const $field = $('#' + fieldId);
                    $field.addClass('is-invalid').removeClass('is-valid');
                    const $err = $('#err-' + fieldId);
                    if (!$err.length) {
                        $field.after(`<div id="err-${fieldId}" class="invalid-feedback d-block" style="color:#f87171;font-size:12px;margin-top:4px;">${message}</div>`);
                    } else {
                        $err.text(message).show();
                    }
                }

                function clearError(fieldId) {
                    const $field = $('#' + fieldId);
                    $field.removeClass('is-invalid is-valid');

                    // Remove the error message div entirely instead of just hiding it
                    $('#err-' + fieldId).remove();
                }

                function clearAllErrors() {
                    ['searchempid', 'startdate', 'enddate'].forEach(clearError);
                }

                // ─── SPINNER HELPERS ──────────────────────────────────────────────────────────

                function startSpinner() {
                    $('#ajaxSearchBtn i').removeClass('fa-search').addClass('fa-spinner fa-spin');
                }

                function stopSpinner() {
                    $('#ajaxSearchBtn i').removeClass('fa-spinner fa-spin').addClass('fa-search');
                }

                // ─── VALIDATION ENGINE ────────────────────────────────────────────────────────
                // Returns: true  → valid, proceed with AJAX
                //          false → invalid or SweetAlert took over (do NOT fire AJAX)

                function validateInputs() {
                    clearAllErrors();

                    const empid = $('#searchempid').val().trim();
                    const startVal = $('#startdate').val(); // "YYYY-MM-DD" or ""
                    const endVal = $('#enddate').val();

                    let valid = true;

                    // ── CASE 1: Employee ID format ──
                    // Allow alphanumeric, hyphens, underscores — block spaces and special chars
                    if (empid !== '' && !/^[a-zA-Z0-9_\-]+$/.test(empid)) {
                        showError('searchempid', 'Employee ID may only contain letters, numbers, hyphens, or underscores.');
                        valid = false;
                    }

                    // ── CASE 2: Start date in the future ──
                    // String compare: "2026-04-07" > "2026-04-06" is true — no Date object needed
                    if (startVal !== '' && startVal > todayStr) {
                        showError('startdate', 'Start date cannot be in the future — no attendance records exist yet.');
                        valid = false;
                    }

                    // ── CASE 3: End date in the future ──
                    if (endVal !== '' && endVal > todayStr) {
                        showError('enddate', `End date cannot be in the future. Maximum allowed is today (${todayStr}).`);
                        valid = false;
                    }

                    // ── CASE 4: Start date is after end date ──
                    // Only check when both are present and each individually passed Cases 2 & 3
                    if (startVal !== '' && endVal !== '' && startVal > endVal) {
                        showError('startdate', 'Start date must be on or before the end date.');
                        showError('enddate', 'End date must be on or after the start date.');
                        valid = false;
                    }

                    // ── Stop here if any basic error exists — don't show the range warning on top ──
                    if (!valid) return false;

                    // ── CASE 5: Date range larger than 1 year — warn then let user decide ──
                    if (startVal !== '' && endVal !== '') {
                        // Safe to use Date math here since both strings already passed string checks
                        const diffDays = Math.ceil(
                            (new Date(endVal + 'T00:00:00') - new Date(startVal + 'T00:00:00')) /
                            (1000 * 60 * 60 * 24)
                        );

                        if (diffDays > 365) {
                            // SweetAlert takes control — stopSpinner here so it doesn't freeze
                            stopSpinner();

                            Swal.fire({
                                title: 'Large Date Range',
                                html: `You are searching across <strong>${diffDays} days</strong> of records.<br>This may take a moment. Continue?`,
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonText: 'Yes, Search',
                                cancelButtonText: 'Cancel',
                                confirmButtonColor: '#461bb9'
                            }).then(function (result) {
                                if (result.isConfirmed) {
                                    startSpinner();
                                    _fireAjaxSearch(); // User confirmed — bypass validation and go
                                }
                                // If cancelled, spinner is already stopped — nothing more to do
                            });

                            return false; // Block the normal AJAX flow — SweetAlert handles it
                        }
                    }

                    // ── CASE 6: All fields empty — valid, fetches all records (same as page load) ──
                    // ── CASE 7: Only empid filled — valid, searches all dates for that employee ──
                    // ── CASE 8: Only start date — valid, searches from that date to today ──
                    // ── CASE 9: Only end date — valid, searches all records up to that date ──
                    // ── CASE 10: Same start and end — valid, single-day search ──
                    // All above reach here with valid = true. No blocking needed.

                    return true;
                }

                // ─── LIVE FILTER (client-side) ────────────────────────────────────────────────

                function storeOriginalData() {
                    originalData = [];
                    $('#attendanceTable tr').each(function () {
                        if ($(this).find('td').length > 1) {
                            originalData.push({
                                date: $(this).find('td:eq(0)').text().trim(),
                                empid: $(this).find('td:eq(1)').text().trim(),
                                name: $(this).find('td:eq(2)').text().trim(),
                                login: $(this).find('td:eq(3)').text().trim(),
                                logout: $(this).find('td:eq(4)').text().trim(),
                                device: $(this).find('td:eq(5)').text().trim(),
                                ip: $(this).find('td:eq(6)').text().trim(),
                                html: $(this)[0].outerHTML
                            });
                        }
                    });
                }

                function performLiveFilter(searchTerm) {
                    const $tbody = $('#attendanceTable');
                    let count = 0;
                    $tbody.empty();

                    if (searchTerm.trim() === '') {
                        originalData.forEach(r => {
                            $tbody.append(r.html);
                            count++;
                        });
                    } else {
                        originalData.forEach(function (r) {
                            const text = (r.date + r.empid + r.name + r.login + r.logout + r.device + r.ip).toLowerCase();
                            if (text.includes(searchTerm.toLowerCase())) {
                                $tbody.append(r.html);
                                count++;
                            }
                        });
                    }

                    if (count === 0) {
                        $tbody.html('<tr><td colspan="7" class="text-center p-4 bg-light"><i class="fas fa-search me-2"></i>No matching records in current view. Click <strong>Search</strong> to query the database.</td></tr>');
                    } else {
                        $tbody.prepend(`<tr class="table-info"><td colspan="7" class="p-2"><small><i class="fas fa-filter me-1"></i>Showing ${count} of ${originalData.length} records (Live Filter)</small></td></tr>`);
                    }
                }

                storeOriginalData();

                // Live filter on Employee ID input
                let searchTimeout;
                $('#searchempid').on('input', function () {
                    clearError('searchempid');
                    clearTimeout(searchTimeout);
                    const term = $(this).val();
                    searchTimeout = setTimeout(() => performLiveFilter(term), 300);
                });

                // ─── REAL-TIME DATE CROSS-VALIDATION ─────────────────────────────────────────

                $('#startdate').on('change', function () {
                    clearError('startdate');
                    clearError('enddate'); // Clear stale end-date error when start changes
                    const startVal = $(this).val();
                    const endVal = $('#enddate').val();

                    // Update end date min so the calendar itself blocks impossible picks
                    if (startVal) $('#enddate').attr('min', startVal);

                    if (startVal && startVal > todayStr) {
                        showError('startdate', 'Start date cannot be in the future.');
                    } else if (startVal && endVal && startVal > endVal) {
                        showError('startdate', 'Start date cannot be after the end date.');
                    }
                });

                $('#enddate').on('change', function () {
                    clearError('enddate');
                    clearError('startdate'); // Clear stale start-date error when end changes
                    const startVal = $('#startdate').val();
                    const endVal = $(this).val();

                    if (endVal && endVal > todayStr) {
                        showError('enddate', 'End date cannot be in the future.');
                        $(this).val(todayStr); // Auto-correct to today silently
                        return;
                    }

                    if (startVal && endVal && startVal > endVal) {
                        showError('enddate', 'End date must be on or after the start date.');
                    }
                });

                // ─── AJAX SEARCH ──────────────────────────────────────────────────────────────

                function _fireAjaxSearch() {
                    if (isSearching) return;

                    const empid = $('#searchempid').val().trim();
                    const start = $('#startdate').val();
                    const end = $('#enddate').val();

                    const branch = $('#branchFilter').val(); // Get the selected branch

                    const $tbody = $('#attendanceTable');
                    $tbody.html(`
            <tr>
                <td colspan="7" class="text-center p-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Searching...</span>
                    </div>
                    <div class="mt-2">Searching database for attendance records...</div>
                </td>
            </tr>`);

                    isSearching = true;

                    $.ajax({
                        // Dynamic URL routing based on session
                        url: "<?= ($this->session->userdata('accesslevel') == 'MANAGER') ? base_url('Manager/fetchAttendanceAjax') : base_url('Employee/fetchAttendanceAjax') ?>",
                        type: "POST",
                        data: {
                            startdate: startdate,
                            enddate: enddate,
                            empid: empid,
                            branch: branch, // This is ignored by Manager controller for security
                            '<?= $this->security->get_csrf_token_name(); ?>': '<?= $this->security->get_csrf_hash(); ?>'
                        },
                        dataType: 'json',
                        timeout: 15000,
                        success: function (response) {
                            let html = '';
                            if (response && response.length > 0) {
                                response.forEach(function (att) {
                                    html += `
                            <tr>
                                <td>${att.formatted_date}</td>
                                <td><span class="emp-id">${att.seemp_logempid}</span></td>
                                <td><strong>${att.seempd_name || 'Unknown Employee'}</strong></td>
                                <td class="login-time"><i class="fas fa-sign-in-alt me-1"></i>${att.formatted_login}</td>
                                <td class="logout-time"><i class="fas fa-sign-out-alt me-1"></i>${att.formatted_logout}</td>
                                <td><small class="text-muted">${att.seemp_device_info || 'N/A'}</small></td>
                                <td><code>${att.seemp_ip_address || '0.0.0.0'}</code></td>
                            </tr>`;
                                });
                                html = `<tr class="table-success"><td colspan="7" class="p-2"><small><i class="fas fa-database me-1"></i>Found ${response.length} record(s) from database</small></td></tr>` + html;
                            } else {
                                html = `<tr><td colspan="7" class="text-center p-4"><i class="fas fa-inbox fa-2x text-muted mb-3 d-block"></i><span class="text-muted">No attendance records found for the selected criteria.</span></td></tr>`;
                            }
                            $tbody.html(html);
                            storeOriginalData();
                        },
                        error: function (xhr, status) {
                            let msg = 'Search failed. Please try again.';
                            if (status === 'timeout') msg = 'Search timed out. Try a narrower date range.';
                            else if (xhr.status === 403) msg = 'Session expired. Please refresh the page.';

                            $tbody.html(`
                    <tr>
                        <td colspan="7" class="text-center text-danger p-4">
                            <i class="fas fa-exclamation-triangle fa-2x mb-3 d-block"></i>
                            <div>${msg}</div>
                            <button class="btn btn-outline-danger btn-sm mt-2" onclick="location.reload()">
                                <i class="fas fa-refresh"></i> Retry
                            </button>
                        </td>
                    </tr>`);
                        },
                        complete: function () {
                            isSearching = false;
                            stopSpinner(); // Always restore icon when request finishes
                        }
                    });
                }

                // --- UPDATED SEARCH LOGIC ---

                // Update the button click handler
                $('#ajaxSearchBtn').on('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();

                    // Clear ALL existing error messages before starting a new search attempt
                    clearAllErrors();

                    startSpinner();
                    performAjaxSearch();
                });

                function performAjaxSearch() {
                    // Validate inputs returns false if there's an error
                    const isValid = validateInputs();

                    if (!isValid) {
                        stopSpinner(); // Stop the spinner if validation found an error
                        return; // The error messages are now visible, stop execution
                    }

                    // If we reach here, validation passed, so fire the AJAX
                    _fireAjaxSearch();
                }


                // Enter key on any search field
                $('.search-bar').on('keypress', function (e) {
                    if (e.which === 13) {
                        e.preventDefault();
                        startSpinner();
                        performAjaxSearch();
                    }
                });

                // ─── DEFAULT DATE RANGE: last 30 days ─────────────────────────────────────────
                const thirtyDaysAgo = new Date();
                thirtyDaysAgo.setDate(thirtyDaysAgo.getDate() - 30);
                const thirtyStr = thirtyDaysAgo.getFullYear() + '-' +
                    String(thirtyDaysAgo.getMonth() + 1).padStart(2, '0') + '-' +
                    String(thirtyDaysAgo.getDate()).padStart(2, '0'); // local date, no UTC shift

                $('#startdate').val(thirtyStr);
                $('#enddate').val(todayStr);
                $('#enddate').attr('min', thirtyStr);
            });
        </script>


</body>

</html>