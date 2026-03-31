<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Jobs | Suropriyo Enterprise</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('css/hr/hrManageJobsView.css') ?>">
</head>

<body>
    <!-- error and sucess message -->
    <?php if ($this->session->flashdata('msg') || $this->session->flashdata('error')): ?>
        <div class="toast-container position-fixed top-0 end-0 p-4" style="z-index: 1060; margin-top: 20px;">

            <?php if ($this->session->flashdata('msg')): ?>
                <div class="toast align-items-center text-white bg-success border-0 shadow-lg" role="alert"
                    aria-live="assertive" aria-atomic="true" id="liveToast">
                    <div class="d-flex p-1">
                        <div class="toast-body fw-medium" style="font-size: 0.95rem;">
                            <i class="fas fa-check-circle me-2 fs-5 align-middle"></i>
                            <?= $this->session->flashdata('msg'); ?>
                        </div>
                        <button type="button" class="btn-close btn-close-white me-3 m-auto" data-bs-dismiss="toast"
                            aria-label="Close"></button>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($this->session->flashdata('error')): ?>
                <div class="toast align-items-center text-white bg-danger border-0 shadow-lg" role="alert" aria-live="assertive"
                    aria-atomic="true" id="liveToast">
                    <div class="d-flex p-1">
                        <div class="toast-body fw-medium" style="font-size: 0.95rem;">
                            <i class="fas fa-exclamation-triangle me-2 fs-5 align-middle"></i>
                            <?= $this->session->flashdata('error'); ?>
                        </div>
                        <button type="button" class="btn-close btn-close-white me-3 m-auto" data-bs-dismiss="toast"
                            aria-label="Close"></button>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    <?php endif; ?>

    <div class="main-content">
        <div class="container-fluid">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="text-white fw-bold mb-0">Job Management</h2>
                    <p class="text-white-50 small mb-0 mt-1">Manage and monitor active career opportunities</p>
                </div>
                <button class="btn btn-light rounded-pill px-4 py-2 fw-bold shadow-sm action-btn-hover text-primary"
                    data-bs-toggle="modal" data-bs-target="#jobModal">
                    <i class="fas fa-plus-circle me-2"></i> Post New Job
                </button>
            </div>

            <div class="table-container">
                <div class="table-responsive">
                    <table class="table align-middle table-hover mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">Job Title</th>
                                <th>Experience</th>
                                <th>Location</th>
                                <th>Status</th>
                                <th>Urgency</th>
                                <th class="text-center pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($jobs)):
                                foreach ($jobs as $job): ?>
                                    <tr>
                                        <td class="ps-4">
                                            <div class="fw-bold text-dark">
                                                <?= $job->sejob_jobtitle ?>
                                            </div>
                                            <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.7rem;">

                                                <a href="javascript:void(0)" class="text-primary small text-decoration-none"
                                                    onclick='viewJobDetails(<?= htmlspecialchars(json_encode($job), ENT_QUOTES, 'UTF-8') ?>)'>
                                                    <i class="fas fa-info-circle"></i> view details
                                                </a>
                                            </small>
                                        </td>
                                        <td><span class="text-secondary fw-medium"><?= $job->sejob_experience ?> Years</span>
                                        </td>
                                        <td><i class="fas fa-map-marker-alt me-2 text-muted"></i><?= $job->sejob_address ?></td>
                                        <td>
                                            <span
                                                class="badge <?= $job->sejob_state == 'active' ? 'bg-success' : 'bg-secondary' ?> rounded-pill">
                                                <?= ucfirst($job->sejob_state) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="text-uppercase small text-<?= strtolower($job->sejob_urgency) ?>">
                                                <?= $job->sejob_urgency ?>
                                            </span>
                                        </td>
                                        <td class="text-center pe-4">
                                            <div class="d-flex justify-content-center gap-2">
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-primary rounded-circle action-btn"
                                                    onclick='editJob(<?= htmlspecialchars(json_encode($job), ENT_QUOTES, 'UTF-8') ?>)'>
                                                    <i class="fas fa-edit"></i>
                                                </button>

                                                <a href="<?= base_url('Employee/deleteJob/' . $job->sejob_id) ?>"
                                                    class="btn btn-sm btn-outline-danger rounded-circle action-btn"
                                                    onclick="return confirm('Are you sure you want to delete this job posting?')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; else: ?>
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="fas fa-briefcase fa-2x mb-3 opacity-50"></i>
                                        <p class="mb-0">No job postings available. Click "Post New Job" to get started.</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
    <!-- form mo -->
    <div class="modal fade" id="jobModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content shadow-lg">

                <?= form_open('Employee/saveJob') ?>

                <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>"
                    value="<?= $this->security->get_csrf_hash(); ?>" />

                <div class="modal-header border-0 pb-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold text-primary" id="modalTitle">Post New Job Requirement</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <input type="hidden" name="job_id" id="job_id">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="small fw-bold text-muted mb-1">Job Title <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="jobTitle" id="jobTitle" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="small fw-bold text-muted mb-1">Location <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="address" id="address" class="form-control"
                                placeholder="e.g. Mumbai, MH (Hybrid)" required>
                        </div>
                        <div class="col-md-4">
                            <label class="small fw-bold text-muted mb-1">Experience (Years) <span
                                    class="text-danger">*</span></label>
                            <input type="number" name="experience" id="experience" class="form-control" required
                                min="0">
                        </div>
                        <div class="col-md-4">
                            <label class="small fw-bold text-muted mb-1">Monthly Salary <span
                                    class="text-danger">*</span></label>
                            <input type="number" name="salary" id="salary" class="form-control" required min="0">
                        </div>
                        <div class="col-md-4">
                            <label class="small fw-bold text-muted mb-1">Work Hours</label>
                            <select name="workingHours" id="workingHours" class="form-select">
                                <option value="fulltime">Full Time</option>
                                <option value="parttime">Part Time</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="small fw-bold text-muted mb-1">Urgency</label>
                            <select name="urgency" id="urgency" class="form-select">
                                <option value="new">New</option>
                                <option value="hot">Hot</option>
                                <option value="urgent">Urgent</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="small fw-bold text-muted mb-1">Status</label>
                            <select name="status" id="status" class="form-select">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="small fw-bold text-muted mb-1">Required Skills</label>
                            <input type="text" name="skills" id="skills" class="form-control"
                                placeholder="e.g. React, PHP, SQL">
                        </div>
                        <div class="col-12">
                            <label class="small fw-bold text-muted mb-1">Job Description <span
                                    class="text-danger">*</span></label>
                            <textarea name="description" id="description" class="form-control" rows="4"
                                required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 pb-4 px-4">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-medium"
                        data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">Save Job
                        Details</button>
                </div>
                </form>
            </div>
        </div>
    </div>
    <!-- "Quick View" Modal -->
    <div class="modal fade" id="viewJobModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <div class="modal-header border-0 pb-0 pt-4 px-4">
                    <h4 class="modal-title fw-bold text-primary" id="viewJobTitle">Job Title</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="d-flex flex-wrap gap-2 mb-4 pb-3 border-bottom" id="viewJobBadges">
                    </div>

                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <div class="text-muted small fw-bold text-uppercase mb-1">Location</div>
                            <div id="viewJobLocation" class="fw-medium text-dark"></div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small fw-bold text-uppercase mb-1">Salary (Monthly)</div>
                            <div id="viewJobSalary" class="fw-medium text-dark"></div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small fw-bold text-uppercase mb-1">Experience Required</div>
                            <div id="viewJobExperience" class="fw-medium text-dark"></div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small fw-bold text-uppercase mb-1">Required Skills</div>
                            <div id="viewJobSkills" class="fw-medium"></div>
                        </div>
                    </div>

                    <div>
                        <div class="text-muted small fw-bold text-uppercase mb-2">Job Description</div>
                        <div id="viewJobDescription" class="p-3 bg-light text-dark rounded-3 border"
                            style="min-height: 120px; white-space: pre-wrap; line-height: 1.6;">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 pb-4 px-4">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-medium shadow-sm"
                        data-bs-dismiss="modal">Close Window</button>
                </div>
            </div>
        </div>
    </div>
     

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Function to handle the Edit Button
        function editJob(job) {
            document.getElementById('modalTitle').innerText = "Edit Job Posting";
            document.getElementById('job_id').value = job.sejob_id;
            document.getElementById('jobTitle').value = job.sejob_jobtitle;
            document.getElementById('experience').value = job.sejob_experience;
            document.getElementById('salary').value = job.sejob_salary;
            document.getElementById('address').value = job.sejob_address;
            document.getElementById('workingHours').value = job.sejob_workinghours;
            document.getElementById('skills').value = job.sejob_skills;
            document.getElementById('urgency').value = job.sejob_urgency;
            document.getElementById('status').value = job.sejob_state;
            document.getElementById('description').value = job.sejob_desc;

            var myModal = new bootstrap.Modal(document.getElementById('jobModal'));
            myModal.show();
        }

        // Reset the form back to "Add Mode" when the modal closes
        document.getElementById('jobModal').addEventListener('hidden.bs.modal', function () {
            document.getElementById('modalTitle').innerText = "Post New Job Requirement";
            document.querySelector('form').reset();
            document.getElementById('job_id').value = ""; // Clear the hidden ID input
        });

        // Initialize and show any Bootstrap Toasts on page load
        document.addEventListener('DOMContentLoaded', function () {
            var toastElList = [].slice.call(document.querySelectorAll('.toast'));
            var toastList = toastElList.map(function (toastEl) {
                // 'delay: 4000' means it stays on screen for 4 seconds before fading out
                return new bootstrap.Toast(toastEl, { delay: 4000 });
            });

            // Show all toasts
            toastList.forEach(toast => toast.show());
        });

        // Function to handle the Quick View Modal
        function viewJobDetails(job) {
            // 1. Set basic text fields
            document.getElementById('viewJobTitle').innerText = job.sejob_jobtitle;
            document.getElementById('viewJobLocation').innerHTML = `<i class="fas fa-map-marker-alt text-muted me-2"></i> ${job.sejob_address}`;
            document.getElementById('viewJobSalary').innerHTML = `<i class="fas fa-rupee-sign text-muted me-2"></i> ${job.sejob_salary}`;
            document.getElementById('viewJobExperience').innerHTML = `<i class="fas fa-briefcase text-muted me-2"></i> ${job.sejob_experience} Years`;
            document.getElementById('viewJobDescription').innerText = job.sejob_desc;

            // 2. Format Skills into neat little pill badges
            let skillsHtml = '';
            if (job.sejob_skills) {
                let skillsArray = job.sejob_skills.split(',');
                skillsArray.forEach(skill => {
                    if (skill.trim() !== "") {
                        skillsHtml += `<span class="badge bg-white text-dark border me-1 mb-1 px-2 py-1 shadow-sm">${skill.trim()}</span>`;
                    }
                });
            } else {
                skillsHtml = '<span class="text-muted fst-italic">Not specified</span>';
            }
            document.getElementById('viewJobSkills').innerHTML = skillsHtml;

            // 3. Setup Status & Urgency Badges
            let statusColor = job.sejob_state === 'active' ? 'bg-success' : 'bg-secondary';
            let urgencyColor = job.sejob_urgency === 'urgent' ? 'bg-danger' : (job.sejob_urgency === 'hot' ? 'bg-warning text-dark' : 'bg-primary');
            let hoursText = job.sejob_workinghours === 'fulltime' ? 'Full Time' : 'Part Time';

            document.getElementById('viewJobBadges').innerHTML = `
        <span class="badge ${statusColor} rounded-pill px-3 py-2 text-capitalize shadow-sm"><i class="fas fa-circle me-1" style="font-size: 0.5rem; vertical-align: middle;"></i> ${job.sejob_state}</span>
        <span class="badge bg-light text-dark border rounded-pill px-3 py-2 shadow-sm"><i class="fas fa-clock text-muted me-1"></i> ${hoursText}</span>
        <span class="badge ${urgencyColor} rounded-pill px-3 py-2 text-uppercase shadow-sm"><i class="fas fa-fire me-1"></i> ${job.sejob_urgency}</span>
    `;

            // 4. Trigger the modal
            var viewModal = new bootstrap.Modal(document.getElementById('viewJobModal'));
            viewModal.show();
        }
    </script>
</body>

</html>