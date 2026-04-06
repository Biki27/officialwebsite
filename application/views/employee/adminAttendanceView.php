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
            document.addEventListener("DOMContentLoaded", function() {
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

        <!-- <script>
            document.querySelector('[name="searchempid"]').addEventListener('input', function(e) {
                const searchTerm = e.target.value.toLowerCase();
                const rows = document.querySelectorAll('#attendanceTable tr');
                rows.forEach(row => {
                    // Check if it's not the "No records found" row
                    if (row.cells.length > 1) {
                        row.style.display = row.textContent.toLowerCase().includes(searchTerm) ? '' : 'none';
                    }
                });
            });
        </script> -->

        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script>
            $(document).ready(function() {
                // 1. LETTER-BASED LIVE FILTERING (Client-side)
                // This filters the rows currently loaded in the table as you type
                $('#searchempid').on('input', function() {
                    const searchTerm = $(this).val().toLowerCase();
                    const $rows = $('#attendanceTable tr');
                    let visibleCount = 0;

                    $rows.each(function() {
                        const $row = $(this);
                        // Skip the "No records found" or "Loading" rows
                        if ($row.find('td').length > 1) {
                            const text = $row.text().toLowerCase();
                            if (text.includes(searchTerm)) {
                                $row.show();
                                visibleCount++;
                            } else {
                                $row.hide();
                            }
                        }
                    });

                    // Toggle "No results" message if everything is filtered out
                    if (visibleCount === 0 && searchTerm !== "") {
                        if ($('#noResultsLive').length === 0) {
                            $('#attendanceTable').append('<tr id="noResultsLive"><td colspan="7" class="text-center p-4">No matching records visible. Press Search for deep search.</td></tr>');
                        }
                    } else {
                        $('#noResultsLive').remove();
                    }
                });

                // 2. SERVER-SIDE SEARCH (AJAX)
                // This fetches new data from the database based on ID and Date Range
                function performAjaxSearch() {
                    const empid = $('#searchempid').val();
                    const start = $('#startdate').val();
                    const end = $('#enddate').val();

                    $('#attendanceTable').html('<tr><td colspan="7" class="text-center p-4"><i class="fas fa-spinner fa-spin me-2"></i>Searching records...</td></tr>');

                    $.ajax({
                        url: '<?= base_url("Employee/viewAttendanceAjax") ?>',
                        type: 'POST',
                        data: {
                            searchempid: empid,
                            startdate: start,
                            enddate: end,
                            '<?= $this->security->get_csrf_token_name(); ?>': '<?= $this->security->get_csrf_hash(); ?>'
                        },
                        dataType: 'json',
                        success: function(response) {
                            let html = '';
                            if (response.length > 0) {
                                response.forEach(function(att) {
                                    html += `
                        <tr>
                            <td>${att.formatted_date}</td>
                            <td><span class="emp-id">${att.seemp_logempid}</span></td>
                            <td><strong>${att.seempd_name || 'Unknown'}</strong></td>
                            <td class="login-time"><i class="fas fa-sign-in-alt me-1"></i>${att.formatted_login}</td>
                            <td class="logout-time"><i class="fas fa-sign-out-alt me-1"></i>${att.formatted_logout}</td>
                            <td><small class="text-muted">${att.seemp_device_info || 'N/A'}</small></td>
                            <td><code>${att.seemp_ip_address || '0.0.0.0'}</code></td>
                        </tr>`;
                                });
                            } else {
                                html = '<tr><td colspan="7" class="text-center p-4">No attendance records found.</td></tr>';
                            }
                            $('#attendanceTable').html(html);
                        },
                        error: function() {
                            $('#attendanceTable').html('<tr><td colspan="7" class="text-center text-danger p-4">Error connecting to server.</td></tr>');
                        }
                    });
                }

                // Trigger AJAX on Button Click
                $('#ajaxSearchBtn').on('click', function(e) {
                    e.preventDefault();
                    performAjaxSearch();
                });

                // Trigger AJAX on Enter Key
                $('.search-bar').on('keypress', function(e) {
                    if (e.which == 13) {
                        e.preventDefault();
                        performAjaxSearch();
                    }
                });
            });
        </script>

</body>

</html>