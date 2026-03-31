<?php
defined('BASEPATH') OR exit('No direct script access allowed');
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
</head>

<body>

    <div class="container py-4" id="overview-section" style="display: block;">

        <div class="mb-4 p-3 rounded-4 shadow-sm text-white"
            style="background: linear-gradient(135deg, #4f46e5, #7c3aed);">
            <h4 class="fw-bold mb-1">
                <i class="fas fa-chart-line me-2"></i>Overview Dashboard
            </h4>
            <small class="opacity-75">Your performance & personal details</small>
        </div>

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
                    <p class="text-muted small">You must be within 100 meters of the office to mark attendance.</p>
                    <div class="d-flex justify-content-center gap-3">
                        <button id="clockInBtn" class="btn btn-primary btn-lg" onclick="markAttendance('login')">
                            <i class="fas fa-sign-in-alt me-2"></i> Clock In
                        </button>
                        <button id="clockOutBtn" class="btn btn-danger btn-lg" onclick="markAttendance('logout')">
                            <i class="fas fa-sign-out-alt me-2"></i> Clock Out
                        </button>
                    </div>
                    <div id="attendanceAlert" class="mt-3" style="display:none;"></div>
                </div>
            </div>
        </div>

    </div>

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

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
    let currentAction = 'login'; // Store which button was clicked

    function markAttendance(action) {
        currentAction = action;
        const $btn = action === 'login' ? $('#clockInBtn') : $('#clockOutBtn');
        const originalHtml = $btn.html();
        
        $btn.html('<i class="fas fa-spinner fa-spin me-2"></i> Locating...').prop('disabled', true);

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                function(position) { sendPosition(position, originalHtml); }, 
                function(error) { showError(error, originalHtml); }, 
                { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
            );
        } else {
            showModalMessage('error', "Geolocation is not supported by your browser.");
            resetButtons(originalHtml);
        }
    }

   function sendPosition(position, originalHtml) {
        let lat = position.coords.latitude;
        let lng = position.coords.longitude;
        
        // 1. Grab the CodeIgniter CSRF Security Token
        let csrfName = '<?= $this->security->get_csrf_token_name(); ?>';
        let csrfHash = '<?= $this->security->get_csrf_hash(); ?>';

        console.log("My exact coordinates are: ", lat, lng);

        $.ajax({
            url: '<?= base_url("Employee/SubmitAttendanceAjax") ?>',
            type: 'POST',
            dataType: 'json',
            data: { 
                lat: lat.toString(), 
                lng: lng.toString(),
                action: currentAction,
                [csrfName]: csrfHash // 2. Send the token with the request!
            },
            success: function(data) {
                if(data.status === 'success') {
                    showModalMessage('success', data.message + '<br><small class="text-muted">Detected: ' + data.device + '</small>');
                } else {
                    showModalMessage('error', data.message);
                }
                resetButtons(originalHtml);
            },
            error: function(xhr, status, error) {
                console.log('AJAX Error:', error);
                showModalMessage('error', 'Server error occurred. Please try again.');
                resetButtons(originalHtml);
            }
        });
    }
    function showError(error, originalHtml) {
        let msg = "";
        switch(error.code) {
            case error.PERMISSION_DENIED: msg = "You denied the request for Geolocation. Please allow location access."; break;
            case error.POSITION_UNAVAILABLE: msg = "Location information is unavailable. Ensure your GPS is turned on."; break;
            case error.TIMEOUT: msg = "The request to get user location timed out."; break;
            case error.UNKNOWN_ERROR: msg = "An unknown error occurred."; break;
        }
        showModalMessage('error', msg);
        resetButtons(originalHtml);
    }

    function showModalMessage(type, message) {
        let icon = type === 'success' ? '<i class="fas fa-check-circle text-success fa-4x mb-3"></i>' : '<i class="fas fa-times-circle text-danger fa-4x mb-3"></i>';
        $('#attendanceModalBody').html(`${icon}<br><h5 class="fw-bold text-dark">${message}</h5>`);
        var myModal = new bootstrap.Modal(document.getElementById('attendanceModal'));
        myModal.show();
    }

    function resetButtons(originalHtml) {
        if(currentAction === 'login') {
            $('#clockInBtn').html(originalHtml).prop('disabled', false);
        } else {
            $('#clockOutBtn').html(originalHtml).prop('disabled', false);
        }
    }
</script>

</body>

</html>