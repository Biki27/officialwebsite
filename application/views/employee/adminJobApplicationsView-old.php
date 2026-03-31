<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Applicant Management | Suropriyo Enterprise</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('css/admin/adminJobApplicationsView.css') ?>">
</head>

<body>

    <?php if ($this->session->flashdata('msg')): ?>
        <div class="toast-container position-fixed top-0 end-0 p-4" style="z-index: 1060; margin-top: 20px;">
            <div class="toast align-items-center text-white bg-success border-0 shadow-lg" role="alert"
                aria-live="assertive" aria-atomic="true" id="liveToast">
                <div class="d-flex p-2">
                    <div class="toast-body fw-medium" style="font-size: 0.95rem;">
                        <i class="fas fa-check-circle me-2 fs-5 align-middle"></i>
                        <?= $this->session->flashdata('msg') ?>
                    </div>
                    <button type="button" class="btn-close btn-close-white me-3 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="main-content">
        <div class="container-fluid p-0">

            <div class="page-header d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
                <div>
                    <h2 class="text-white fw-bold mb-1">Applicant Management</h2>
                    <p class="text-white-50 small mb-0">Review and manage job applications efficiently</p>
                </div>

                <div class="d-flex flex-wrap gap-2 align-items-center">
                    <select class="form-select rounded-pill shadow-sm border-0 px-4 fw-medium text-secondary"
                        id="statusFilter" style="width: auto; height: 48px; cursor: pointer;">
                        <option value="all">All Statuses</option>
                        <option value="pending">Pending</option>
                        <option value="selected">Selected</option>
                        <option value="rejected">Rejected</option>
                    </select>

                    <form class="search-container mb-0 m-0 d-flex align-items-center" id="searchForm"
                        style="height: 48px;">
                        <input type="text" class="search-bar border-0 py-0 h-100" placeholder="Search ID, Name..."
                            id="searchInput" style="background: transparent; outline: none; box-shadow: none;">
                        <button type="submit" class="search-btn h-100 rounded-pill"><i
                                class="fas fa-search"></i></button>
                    </form>
                </div>
            </div>

            <div class="table-container">
                <div class="table-responsive">
                    <table class="table align-middle mb-0" id="applicantsTable">
                        <thead>
                            <tr>
                                <th class="ps-4">App ID</th>
                                <th>Date Applied</th>
                                <th>Candidate Info</th>
                                <th>Position</th>
                                <th>Status</th>
                                <th class="text-center pe-4">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($applicants as $app): ?>
                                <?php
                                // Determine Badge Color
                                $statusClass = 'bg-pending';
                                if (strtolower($app->sejoba_state) == 'selected')
                                    $statusClass = 'bg-selected';
                                if (strtolower($app->sejoba_state) == 'rejected')
                                    $statusClass = 'bg-rejected';
                                ?>
                                <tr>
                                    <td class="ps-4 fw-bold text-muted">
                                        APP<?= str_pad($app->sejoba_id, 3, '0', STR_PAD_LEFT) ?></td>
                                    <td><i
                                            class="far fa-calendar-alt text-muted me-2"></i><?= date('d M, Y', strtotime($app->sejoba_atime)) ?>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark"><?= $app->sejoba_name ?></div>
                                        <div class="text-muted small"><?= $app->sejoba_email ?></div>
                                    </td>
                                    <td class="fw-medium text-primary"><?= $app->sejoba_position ?></td>
                                    <td><span class="badge-status <?= $statusClass ?>"><?= $app->sejoba_state ?></span></td>
                                    <td class="text-center pe-4">
                                        <button class="view-btn" data-id="<?= $app->sejoba_id ?>"
                                            data-name="<?= htmlspecialchars($app->sejoba_name, ENT_QUOTES) ?>"
                                            data-email="<?= htmlspecialchars($app->sejoba_email, ENT_QUOTES) ?>"
                                            data-phone="<?= htmlspecialchars($app->sejoba_phone, ENT_QUOTES) ?>"
                                            data-position="<?= htmlspecialchars($app->sejoba_position, ENT_QUOTES) ?>"
                                            data-salary="<?= htmlspecialchars($app->sejoba_exp_salary, ENT_QUOTES) ?>"
                                            data-status="<?= htmlspecialchars($app->sejoba_state, ENT_QUOTES) ?>"
                                            data-exp="<?= htmlspecialchars($app->sejoba_experience, ENT_QUOTES) ?>"
                                            data-resume="<?= htmlspecialchars($app->sejoba_resume, ENT_QUOTES) ?>">
                                            Review Candidate
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <div class="modal fade" id="reviewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content shadow-lg">
                <div class="modal-header border-0 pb-0 pt-4 px-4">
                    <div>
                        <h4 class="modal-title fw-bold text-primary mb-1" id="modal_name">Candidate Name</h4>
                        <span class="badge bg-light text-dark border shadow-sm" id="modal_position">Position</span>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-md-6 border-end pe-4">
                            <h6 class="fw-bold text-dark mb-3 border-bottom pb-2">Candidate Details</h6>

                            <div class="info-label">Applicant ID</div>
                            <div class="info-value text-primary fw-bold" id="modal_id"></div>

                            <div class="info-label">Contact Information</div>
                            <div class="info-value">
                                <i class="fas fa-envelope text-muted me-2"></i><span id="modal_email"></span><br>
                                <i class="fas fa-phone-alt text-muted me-2 mt-2"></i><span id="modal_phone"></span>
                            </div>

                            <div class="row">
                                <div class="col-6">
                                    <div class="info-label">Experience</div>
                                    <div class="info-value" id="modal_exp"></div>
                                </div>
                                <div class="col-6">
                                    <div class="info-label">Expected Salary</div>
                                    <div class="info-value" id="modal_salary"></div>
                                </div>
                            </div>

                            <div class="info-label">Resume / CV</div>
                            <div class="info-value mt-1" id="modal_resume">
                            </div>
                        </div>

                        <div class="col-md-6 ps-4">
                            <h6 class="fw-bold text-dark mb-3 border-bottom pb-2">Review & Decision</h6>

                            <?= form_open('Employee/viewJobApplicants') ?>
                            <input type="hidden" name="applicant_id" id="form_applicant_id">

                            <div class="mb-3">
                                <label class="info-label">Update Status</label>
                                <select name="status" id="form_status" class="form-select bg-light" required>
                                    <option value="" disabled>Select Decision...</option>
                                    <option value="pending">Pending</option>
                                    <option value="selected">Selected</option>
                                    <option value="rejected">Rejected</option>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label class="info-label">HR Comments (Optional)</label>
                                <textarea name="comment" class="form-control bg-light" rows="4"
                                    placeholder="Add notes about the interview or decision..."></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-2 rounded-pill fw-bold shadow-sm">
                                Save Review Decision
                            </button>
                            <?= form_close() ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            // 1. Initialize Toasts
            var toastElList = [].slice.call(document.querySelectorAll('.toast'));
            var toastList = toastElList.map(function (toastEl) {
                return new bootstrap.Toast(toastEl, { delay: 4000 });
            });
            toastList.forEach(toast => toast.show());

            // 2. Search Functionality
            // 2. Combined Search & Filter Functionality
            const searchInput = document.getElementById('searchInput');
            const searchForm = document.getElementById('searchForm');
            const statusFilter = document.getElementById('statusFilter');

            function filterApplicants() {
                const searchTerm = searchInput.value.toLowerCase().trim();
                const selectedStatus = statusFilter.value.toLowerCase();
                const rows = document.querySelectorAll('#applicantsTable tbody tr');

                rows.forEach(row => {
                    // Check Text Match (Name, ID, Position, etc.)
                    const textMatch = row.textContent.toLowerCase().includes(searchTerm);

                    // Check Status Match
                    const rowStatusBadge = row.querySelector('.badge-status');
                    const rowStatus = rowStatusBadge ? rowStatusBadge.textContent.toLowerCase().trim() : '';
                    const statusMatch = (selectedStatus === 'all' || rowStatus === selectedStatus);

                    // Show row ONLY if it matches BOTH the search text AND the selected status
                    if (textMatch && statusMatch) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            }

            // Listen for typing in the search bar
            searchInput.addEventListener('input', filterApplicants);

            // Listen for changing the dropdown
            statusFilter.addEventListener('change', filterApplicants);

            // Prevent form submission from refreshing the page
            searchForm.addEventListener('submit', e => {
                e.preventDefault();
                filterApplicants();
            });

            // 3. Populate and Open Modal
            const reviewModal = new bootstrap.Modal(document.getElementById('reviewModal'));

            document.querySelectorAll('.view-btn').forEach(button => {
                button.addEventListener('click', function () {
                    const data = this.dataset;

                    // Populate Texts
                    document.getElementById('modal_name').innerText = data.name;
                    document.getElementById('modal_position').innerText = data.position;
                    document.getElementById('modal_id').innerText = "APP" + data.id.padStart(3, '0');
                    document.getElementById('modal_email').innerText = data.email;
                    document.getElementById('modal_phone').innerText = data.phone;
                    document.getElementById('modal_exp').innerText = data.exp + " Years";
                    document.getElementById('modal_salary').innerText = "₹" + data.salary;

                    // Populate Form
                    document.getElementById('form_applicant_id').value = data.id;

                    // Pre-select the dropdown to current status if it exists
                    const statusDropdown = document.getElementById('form_status');
                    statusDropdown.value = data.status.toLowerCase();

                    // Parse Resume URL safely
                    const resumeBox = document.getElementById('modal_resume');
                    // Parse Resume URL Safely
                    const resumeBox = document.getElementById('modal_resume');
                    if (data.resume && data.resume.trim() !== "") {
                        const baseUrl = '<?= base_url(); ?>';
                        // Remove './' if it exists
                        let cleanPath = data.resume.replace(/^\.\//, '');

                        // Build the base path
                        let fullResumePath = cleanPath.startsWith('resume/') ? baseUrl + cleanPath : baseUrl + 'resume/' + cleanPath;

                        // Only append .pdf if there is NO file extension present (checks for a dot followed by 3-4 letters)
                        if (!fullResumePath.match(/\.[0-9a-z]+$/i)) {
                            fullResumePath += '.pdf';
                        }

                        resumeBox.innerHTML = `<a href="${fullResumePath}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-3 mt-1 fw-bold"><i class="fas fa-file-download me-2"></i>Download File</a>`;
                    } else {
                        resumeBox.innerHTML = '<span class="text-danger small fst-italic">No Resume Uploaded</span>';
                    }
                    document.getElementById('pills-review-tab').click();
                    // Show Modal
                    reviewModal.show();
                });
            });
        });
    </script>
</body>

</html>