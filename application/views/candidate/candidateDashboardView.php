<style>
    /* ===== PREMIUM GLOBAL ===== */
    body {
        background: #F1F5F9;
        font-family: 'Inter', sans-serif;
    }

    /* Glass effect card */
    .premium-card {
        border-radius: 20px;
        background: #ffffff;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.06);
        transition: 0.3s ease;
    }

    .premium-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    }

    /* Header section */
    .welcome-box {
        padding: 25px;
        border-radius: 20px;
        background: linear-gradient(135deg, #2563eb, #06b6d4);
        color: white;
    }

    /* Badge upgrade */
    .badge-premium {
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
        border-radius: 50px;
    }

    /* Table upgrade */
    .table thead {
        background: #f8fafc;
    }

    .table tbody tr {
        transition: 0.2s;
    }

    .table tbody tr:hover {
        background: #f1f5f9;
    }

    /* Status badges */
    .badge {
        border-radius: 50px !important;
        padding: 6px 12px;
        font-size: 12px;
    }

    /* Responsive fix */
    @media (max-width: 768px) {
        .welcome-box {
            text-align: center;
        }
    }
</style>
<div class="row mb-4">
    <div class="col-12">
        <div class="welcome-box d-flex flex-column flex-md-row justify-content-between align-items-center">

            <div>
                <h2 class="fw-bold mb-1">Welcome back 👋</h2>
                <p class="mb-0 opacity-75">Manage your profile and track your job applications</p>
            </div>

            <div class="mt-3 mt-md-0">
                <span class="badge badge-premium px-3 py-2">
                    <i class="fas fa-envelope me-2"></i>
                    <?= htmlspecialchars($profile->email); ?>
                </span>
            </div>

        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class=" premium-card p-3">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="mb-0 fw-bold"><i class="fas fa-briefcase text-primary me-2"></i> My Applications</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class=" table align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="px-4 py-3 text-muted small text-uppercase">Position</th>
                                <th class="py-3 text-muted small text-uppercase">Date</th>
                                <th class="py-3 text-muted small text-uppercase">Status</th>
                                <th class="py-3 text-muted small text-uppercase">Interview</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($applications)): ?>
                                <?php foreach ($applications as $app): ?>
                                    <tr>
                                        <td class="px-4 py-3 fw-bold text-dark">
                                            <?= htmlspecialchars($app->sejob_jobtitle ?? 'Unknown Position'); ?>
                                        </td>
                                        <td class="py-3 text-muted">
                                            <?= date('M j, Y', strtotime($app->sejoba_atime)); ?>
                                        </td>
                                        <td class="py-3">
                                            <?php
                                            $status = strtolower($app->sejoba_state);
                                            $badge = 'bg-secondary';
                                            if ($status == 'applied' || $status == 'pending')
                                                $badge = 'bg-warning text-dark';
                                            if ($status == 'interviewing' || $status == 'interview_scheduled')
                                                $badge = 'bg-info text-dark';
                                            if ($status == 'selected' || $status == 'hired')
                                                $badge = 'bg-success';
                                            if ($status == 'rejected')
                                                $badge = 'bg-danger';
                                            ?>
                                            <span class="badge <?= $badge; ?> px-2 py-1" style="text-transform: capitalize;">
                                                <?= str_replace('_', ' ', $status); ?>
                                            </span>
                                        </td>
                                        <td class="py-3">
                                            <?php if (!empty($app->sejoba_interview_date)): ?>
                                                <small class="d-block text-primary fw-bold">
                                                    <i class="far fa-calendar-alt"></i>
                                                    <?= date('M j, Y', strtotime($app->sejoba_interview_date)); ?>
                                                    at <?= date('g:i A', strtotime($app->sejoba_interview_time)); ?>
                                                </small>
                                                <small class="text-muted"><i class="fas fa-map-marker-alt"></i>
                                                    <?= htmlspecialchars($app->sejoba_interview_location); ?></small>
                                            <?php else: ?>
                                                <span class="text-muted small">Not scheduled yet</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center py-5">
                                        <div class="text-muted mb-3"><i class="fas fa-folder-open fs-1"></i></div>
                                        <h5 class="fw-bold">No applications found</h5>
                                        <p class="text-muted mb-3">You haven't applied to any open positions yet.</p>
                                        <a href="<?= base_url('Careers/Jobs'); ?>" class="btn btn-primary btn-sm">Browse
                                            Open Jobs</a>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php if (isset($auto_apply_job)): ?>

    <?php
    $apply_errors = array();
    $apply_old = array();
    $has_apply_err = FALSE;

    $_err_json = $this->session->flashdata('apply_errors');
    $_old_json = $this->session->flashdata('apply_old');

    if (!empty($_err_json)) {
        $apply_errors = json_decode($_err_json, TRUE) ?: array();
        $apply_old = json_decode($_old_json, TRUE) ?: array();
        $has_apply_err = !empty($apply_errors);
    }

    // Helper: return Bootstrap is-invalid class when a field has an error
    function ae($field, $errors)
    {
        return isset($errors[$field]) ? 'is-invalid' : '';
    }
    // Helper: return old (re-populated) value
    function ao($field, $old, $fallback = '')
    {
        return htmlspecialchars($old[$field] ?? $fallback);
    }
    ?>

    <style>
        /* ===== autoApplyModal validation styles ===== */
        #autoApplyModal .invalid-feedback {
            display: none;
            /* hidden by default                         */
            font-size: 0.78rem;
            margin-top: 4px;
            color: #dc3545;
        }

        #autoApplyModal .is-invalid~.invalid-feedback,
        #autoApplyModal .is-invalid+.input-group-text~.invalid-feedback,
        #autoApplyModal .input-group .is-invalid~.invalid-feedback {
            display: block;
            /* show when field gets is-invalid class     */
        }

        #autoApplyModal .is-valid {
            border-color: #198754 !important;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%23198754' d='M2.3 6.73.6 4.53c-.4-1.04.46-1.4 1.1-.8l1.1 1.4 3.4-3.8c.6-.63 1.6-.27 1.2.7l-4 4.6c-.43.5-.8.4-1.1.1z'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 10px center;
            background-size: 16px;
            padding-right: 2.5rem;
        }

        /* Server error summary box */
        #apply-server-errors {
            border-left: 4px solid #dc3545;
            background: #fff5f5;
            border-radius: 8px;
            padding: 12px 16px;
            margin-bottom: 1rem;
            font-size: 0.85rem;
        }

        #apply-server-errors ul {
            margin: 0;
            padding-left: 1.2rem;
        }
    </style>

    <div class="modal fade" id="autoApplyModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content shadow-lg border-0 rounded-4">

                <div class="modal-header bg-primary text-white border-0 rounded-top-4 p-4">
                    <div>
                        <h4 class="modal-title fw-bold mb-1">Apply for:
                            <?= htmlspecialchars($auto_apply_job->sejob_jobtitle); ?>
                        </h4>
                        <p class="mb-0 opacity-75 small">Complete your profile to submit this application.</p>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <div class="modal-body p-4 p-md-5 bg-light">

                    <?php if ($has_apply_err): ?>
                        <div id="apply-server-errors">
                            <strong class="text-danger"><i class="fas fa-exclamation-triangle me-1"></i> Please fix the
                                following:</strong>
                            <ul class="mt-1 mb-0">
                                <?php foreach ($apply_errors as $field => $msg): ?>
                                    <li><?= htmlspecialchars($msg); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <?= form_open_multipart('Jobs/ApplyStatus/' . $auto_apply_job->sejob_id, ['id' => 'applyForm', 'novalidate' => 'novalidate']); ?>

                    <!--  Full Name & Phone  -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold text-muted small text-uppercase">Full Name <span
                                    class="text-danger">*</span></label>
                            <input type="text" id="af_full_name" class="form-control <?= ae('full_name', $apply_errors); ?>"
                                name="full_name" value="<?= ao('full_name', $apply_old, $profile->full_name ?? ''); ?>"
                                placeholder="e.g. Riya Sharma" autocomplete="name">
                            <div class="invalid-feedback">
                                <?= $apply_errors['full_name'] ?? 'Full name is required (2–80 characters, letters only).'; ?>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="fw-bold text-muted small text-uppercase">Phone Number <span
                                    class="text-danger">*</span></label>
                            <input type="tel" id="af_phone" class="form-control <?= ae('phone', $apply_errors); ?>"
                                name="phone" value="<?= ao('phone', $apply_old); ?>" placeholder="10-digit mobile number"
                                maxlength="15" autocomplete="tel">
                            <div class="invalid-feedback">
                                <?= $apply_errors['phone'] ?? 'Enter a valid 10-digit Indian mobile number.'; ?>
                            </div>
                        </div>
                    </div>

                    <!--  Experience & Salary  -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold text-muted small text-uppercase">Years of Experience <span
                                    class="text-danger">*</span></label>
                            <input type="number" id="af_experience"
                                class="form-control <?= ae('experience', $apply_errors); ?>" name="experience"
                                value="<?= ao('experience', $apply_old); ?>" min="0" max="50" step="0.5" placeholder="0">
                            <div class="invalid-feedback">
                                <?= $apply_errors['experience'] ?? 'Enter a value between 0 and 50 years.'; ?>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="fw-bold text-muted small text-uppercase">Expected Salary (Monthly) <span
                                    class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="number" id="af_expected_salary"
                                    class="form-control <?= ae('expected_salary', $apply_errors); ?>" name="expected_salary"
                                    value="<?= ao('expected_salary', $apply_old); ?>" min="1000" max="10000000" step="500"
                                    placeholder="e.g. 10000">
                            </div>
                            <div class="invalid-feedback">
                                <?= $apply_errors['expected_salary'] ?? 'Enter a valid monthly salary (min ₹1,000).'; ?>
                            </div>
                        </div>
                    </div>

                    <!--  Resume  -->
                    <div class="mb-4">
                        <label class="fw-bold text-muted small text-uppercase">
                            Resume Upload <span class="text-danger">*</span>
                            <span class="text-muted fw-normal ms-1">(PDF only, max 5 MB)</span>
                        </label>
                        <input type="file" id="af_resume"
                            class="form-control form-control-lg <?= ae('resume', $apply_errors); ?>" name="resume"
                            accept=".pdf,application/pdf">
                        <div class="invalid-feedback">
                            <?= $apply_errors['resume'] ?? 'Upload a PDF file (max 2 MB).'; ?>
                        </div>
                        <div id="af_resume_hint" class="form-text text-muted mt-1" style="font-size:0.78rem;"></div>
                    </div>

                    <!-- ── Cover Letter ── -->
                    <div class="mb-4">
                        <label class="fw-bold text-muted small text-uppercase">
                            Cover Letter <span class="text-danger">*</span>
                            <span id="af_cl_count" class="text-muted fw-normal ms-1 small">(0 / 2000 chars)</span>
                        </label>
                        <textarea class="form-control <?= ae('coverletter', $apply_errors); ?>" id="af_coverletter"
                            name="coverletter" rows="4" maxlength="2000"
                            placeholder="Tell us why you're a great fit for this role…"><?= ao('coverletter', $apply_old); ?></textarea>
                        <div class="invalid-feedback">
                            <?= $apply_errors['coverletter'] ?? 'Cover letter is required (min 50 characters).'; ?>
                        </div>
                    </div>

                    <!-- ── Buttons ── -->
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" id="af_submit_btn" class="btn btn-primary px-5 fw-bold">
                            Submit Application <i class="fas fa-paper-plane ms-2"></i>
                        </button>
                    </div>

                    <?= form_close(); ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {

            //  Auto-open the modal
            var modalEl = document.getElementById('autoApplyModal');
            var bsModal = new bootstrap.Modal(modalEl);
            bsModal.show();

            //  Field references
            var form = document.getElementById('applyForm');
            var fName = document.getElementById('af_full_name');
            var fPhone = document.getElementById('af_phone');
            var fExp = document.getElementById('af_experience');
            var fSalary = document.getElementById('af_expected_salary');
            var fResume = document.getElementById('af_resume');
            var fCL = document.getElementById('af_coverletter');
            var clCount = document.getElementById('af_cl_count');
            var resumeHint = document.getElementById('af_resume_hint');
            var submitBtn = document.getElementById('af_submit_btn');

            function markValid(el) {
                el.classList.remove('is-invalid');
                el.classList.add('is-valid');
            }
            function markInvalid(el) {
                el.classList.remove('is-valid');
                el.classList.add('is-invalid');
            }
            function clearMark(el) {
                el.classList.remove('is-valid', 'is-invalid');
            }

            //    Individual field validators. Full name: 2–80 chars, only letters/spaces/dots/hyphens
            function validateName() {
                var v = fName.value.trim();
                if (v.length < 2 || v.length > 80 || !/^[A-Za-z\s.\-']+$/.test(v)) {
                    markInvalid(fName); return false;
                }
                markValid(fName); return true;
            }

            // Phone: 10-digit Indian mobile (optional +91 / 0 prefix → strip → 10 digits)
            function validatePhone() {
                var raw = fPhone.value.trim().replace(/[\s\-()]/g, '');
                // Strip +91 or leading 0
                var digits = raw.replace(/^(\+91|91|0)/, '');
                if (!/^[6-9]\d{9}$/.test(digits)) {
                    markInvalid(fPhone); return false;
                }
                markValid(fPhone); return true;
            }

            // Experience: 0–50
            function validateExp() {
                var v = parseFloat(fExp.value);
                if (isNaN(v) || v < 0 || v > 50) {
                    markInvalid(fExp); return false;
                }
                markValid(fExp); return true;
            }

            // Salary: ≥ 1000
            function validateSalary() {
                var v = parseInt(fSalary.value, 10);
                if (isNaN(v) || v < 1000) {
                    markInvalid(fSalary); return false;
                }
                markValid(fSalary); return true;
            }

            // Resume: required, must be PDF, ≤ 2 MB
            function validateResume() {
                var files = fResume.files;
                if (!files || files.length === 0) {
                    markInvalid(fResume);
                    resumeHint.textContent = '';
                    return false;
                }
                var file = files[0];
                var isPdf = file.type === 'application/pdf' || file.name.toLowerCase().endsWith('.pdf');
                var isSize = file.size <= 5 * 1024 * 1024; // 5 MB

                if (!isPdf) {
                    markInvalid(fResume);
                    resumeHint.textContent = '✗ Only PDF files are accepted.';
                    return false;
                }
                if (!isSize) {
                    markInvalid(fResume);
                    resumeHint.textContent = '✗ File too large (' + (file.size / 1048576).toFixed(1) + ' MB). Max 5 MB.';
                    return false;
                }
                markValid(fResume);
                resumeHint.textContent = '✓ ' + file.name + ' (' + (file.size / 1024).toFixed(0) + ' KB)';
                return true;
            }

            // Cover letter: 50–2000 chars
            function validateCL() {
                var len = fCL.value.trim().length;
                clCount.textContent = '(' + fCL.value.length + ' / 2000 chars)';
                if (len < 50) {
                    markInvalid(fCL); return false;
                }
                markValid(fCL); return true;
            }

            //  Real-time listeners (blur = on leaving field)
            fName.addEventListener('blur', validateName);
            fName.addEventListener('input', function () { if (fName.classList.contains('is-invalid')) validateName(); });

            fPhone.addEventListener('blur', validatePhone);
            fPhone.addEventListener('input', function () { if (fPhone.classList.contains('is-invalid')) validatePhone(); });

            fExp.addEventListener('blur', validateExp);
            fExp.addEventListener('input', function () { if (fExp.classList.contains('is-invalid')) validateExp(); });

            fSalary.addEventListener('blur', validateSalary);
            fSalary.addEventListener('input', function () { if (fSalary.classList.contains('is-invalid')) validateSalary(); });

            fResume.addEventListener('change', validateResume);

            fCL.addEventListener('input', function () {
                validateCL();
            });

            //  On submit: run all validators
            form.addEventListener('submit', function (e) {
                var ok = true;
                if (!validateName()) ok = false;
                if (!validatePhone()) ok = false;
                if (!validateExp()) ok = false;
                if (!validateSalary()) ok = false;
                if (!validateResume()) ok = false;
                if (!validateCL()) ok = false;

                if (!ok) {
                    e.preventDefault();
                    e.stopPropagation();
                    // Scroll to first error inside modal body
                    var firstErr = form.querySelector('.is-invalid');
                    if (firstErr) firstErr.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    return;
                }

                // Disable button to prevent double-submit
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Submitting…';
            });

            //  If server returned errors, keep modal open & mark fields ── */
            <?php if ($has_apply_err): ?>
                var serverErrors = <?= json_encode($apply_errors); ?>;
                var fieldMap = {
                    'full_name': fName,
                    'phone': fPhone,
                    'experience': fExp,
                    'expected_salary': fSalary,
                    'resume': fResume,
                    'coverletter': fCL
                };
                Object.keys(serverErrors).forEach(function (key) {
                    if (fieldMap[key]) markInvalid(fieldMap[key]);
                });
                // Modal was already opened by PHP having $has_apply_err set in $auto_apply_job
            <?php endif; ?>

        });
    </script>
<?php endif; ?>