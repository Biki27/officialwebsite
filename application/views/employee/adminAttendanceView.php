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

        <?= form_open('Employee/viewAttendance') ?>

        <div class="search-form-wrapper">
            <div class="search-group">
                <label for="searchempid" class="text-white"><b>Employee ID</b></label>
                <input type="text" name="searchempid" class="search-bar" placeholder="Enter ID">
            </div>

            <div class="search-group">
                <label for="startdate" class="text-white"><b>Start Date</b></label>
                <input type="date" name="startdate" class="search-bar">
            </div>

            <div class="search-group">
                <label for="enddate" class="text-white"><b>End Date</b></label>
                <input type="date" name="enddate" class="search-bar">
            </div>

            <div class="search-group">
                <button type="submit" class="search-btn">
                    <i class="fas fa-search"></i> Search
                </button>
            </div>
        </div>

        <?= form_close() ?>

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

    <script>
        document.querySelector('[name="searchempid"]').addEventListener('input', function (e) {
            const searchTerm = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('#attendanceTable tr');
            rows.forEach(row => {
                // Check if it's not the "No records found" row
                if (row.cells.length > 1) {
                    row.style.display = row.textContent.toLowerCase().includes(searchTerm) ? '' : 'none';
                }
            });
        });
    </script>

</body>

</html>