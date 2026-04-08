<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// ── Cache all repeated checks & values once ──────────────────────────────────
$is_edit          = isset($emp);
$has_prefill      = isset($prefill_applicant);

// Employee field values (avoids re-evaluating isset($emp) on every input)
$v_empid          = $is_edit ? $emp->seemp_id          : '';
$v_branch         = $is_edit ? $emp->seemp_branch       : '';
$v_access         = $is_edit ? $emp->seemp_acesslevel   : '';
$v_status         = $is_edit ? $emp->seemp_status       : '';
$v_email          = $is_edit ? $emp->seemp_email
                             : ($has_prefill ? htmlspecialchars($prefill_applicant->sejoba_email,   ENT_QUOTES) : '');
$v_name           = $is_edit ? $emp->seempd_name
                             : ($has_prefill ? htmlspecialchars($prefill_applicant->sejoba_name,    ENT_QUOTES) : '');
$v_designation    = $is_edit ? $emp->seempd_designation
                             : ($has_prefill ? htmlspecialchars($prefill_applicant->sejoba_position,ENT_QUOTES) : '');
$v_phone          = $is_edit ? $emp->seempd_phone
                             : ($has_prefill ? htmlspecialchars($prefill_applicant->sejoba_phone,   ENT_QUOTES) : '');
$v_salary         = $is_edit ? $emp->seempd_salary
                             : ($has_prefill ? htmlspecialchars($prefill_applicant->sejoba_exp_salary, ENT_QUOTES) : '');
$v_experience     = $is_edit ? $emp->seempd_experience
                             : ($has_prefill ? htmlspecialchars($prefill_applicant->sejoba_experience, ENT_QUOTES) : '');
$v_dob            = $is_edit ? $emp->seempd_dob         : '1990-01-01';
$v_joining        = $is_edit ? $emp->seempd_joiningdate : date('Y-m-d');
$v_aadhar         = $is_edit ? $emp->seempd_aadhar      : '';
$v_pan            = $is_edit ? $emp->seempd_pan         : '';
$v_perm_addr      = $is_edit ? $emp->seempd_address_permanent : '';
$v_curr_addr      = $is_edit ? $emp->seempd_address_current   : '';
$v_project        = $is_edit ? $emp->seempd_project     : '';
$v_img            = $is_edit ? $emp->seempd_img         : '';
$v_cv             = $is_edit ? $emp->seempd_cv          : '';
$v_linked_app     = $has_prefill ? $prefill_applicant->sejoba_id : '';

// Avatar
$emp_name_for_avatar = $is_edit ? $emp->seempd_name : ($has_prefill ? $prefill_applicant->sejoba_name : 'New Employee');
$fallback_avatar     = "https://ui-avatars.com/api/?name=" . urlencode($emp_name_for_avatar) . "&background=461bb9&color=ffffff&size=180&bold=true";
$img_url             = ($is_edit && !empty($v_img)) ? base_url('uploads/' . $v_img) : $fallback_avatar;

// Flash message
$flash_msg  = $this->session->flashdata('msg');
$has_flash  = (bool) $flash_msg;
$is_error   = $has_flash && (stripos($flash_msg, 'Failed') !== false || stripos($flash_msg, 'Error') !== false);

// JS booleans (computed once, echoed into <script>)
$js_is_update      = $is_edit ? 'true' : 'false';
$js_has_photo      = ($is_edit && !empty($v_img)) ? 'true' : 'false';
$js_orig_empid     = $is_edit ? $v_empid  : '';
$js_orig_email     = $is_edit ? $v_email  : '';
$js_cv_status_text = (!empty($v_cv)) ? 'Click to replace current CV' : 'Click to upload CV (PDF, DOC)';

// Projects array
$existing_projects = (!empty($v_project)) ? explode(',', $v_project) : [''];

// CSRF (cached — same token throughout the page)
$csrf_name = $this->security->get_csrf_token_name();
$csrf_hash = $this->security->get_csrf_hash();
?>
<!-- Preconnect speeds up Google Fonts negotiation -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= base_url('css/admin/adminEmployeeRegistrationView.css') ?>">
<!-- defer: non-blocking; fires after HTML is parsed, before DOMContentLoaded -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" defer></script>

<body>
    <?php if ($has_flash): ?>
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                Swal.fire({
                    title: '<?= $is_error ? "Oops!" : "Success!" ?>',
                    text: <?= json_encode($flash_msg) ?>,
                    icon: '<?= $is_error ? "error" : "success" ?>',
                    confirmButtonColor: '#461bb9'
                });
            });
        </script>
    <?php endif; ?>

    <div class="employee-container">
        <div class="form-header">
            <h1 class="form-title">
                <i class="fas fa-user-plus me-3"></i>
                Employee Profile
            </h1>
            <p class="form-subtitle">Complete professional details for new employee onboarding</p>
        </div>

        <form id="employeeForm" method="POST"
            action="<?= $is_edit ? site_url('Employee/updateEmployee/' . $v_empid) : site_url('Employee/addEmployee') ?>"
            enctype="multipart/form-data">

            <input type="hidden" name="<?= $csrf_name ?>" value="<?= $csrf_hash ?>">

            <div class="form-body" style="position: relative;">
                <input type="hidden" name="linked_applicant_id" value="<?= $v_linked_app ?>">

                <div class="photo-section">
                    <img src="<?= $img_url ?>" alt="Photo" class="photo-preview" id="photoPreview"
                        onclick="document.getElementById('photoInput').click()"
                        onerror="this.onerror=null; this.src='<?= $fallback_avatar ?>';">

                    <button type="button" class="photo-btn" onclick="document.getElementById('photoInput').click()">
                        <i class="fas fa-camera"></i> Photo
                    </button>
                    <input type="file" id="photoInput" name="photo" accept="image/*" style="display: none;">
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Employee Name <span class="required">*</span></label>
                        <input type="text" class="form-control" id="empName" name="empName"
                            value="<?= $v_name ?>" required oninput="updateAvatarLive(this.value)">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Employee ID <span class="required">*</span></label>
                        <input type="text" class="form-control" id="empid" name="empid" placeholder="SE26KOL01"
                            value="<?= $v_empid ?>"<?= $is_edit ? ' style="background-color: #f3f4f6;"' : '' ?>
                            required autocomplete="off">
                        <small id="empidFeedback" class="mt-1 d-block" style="min-height: 20px;"></small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Branch <span class="required">*</span></label>
                        <select class="form-select" id="branch" name="branch" required>
                            <option value="KOLKATA" <?= ($v_branch == 'KOLKATA') ? 'selected' : '' ?>>Kolkata</option>
                            <option value="HOWRAH"  <?= ($v_branch == 'HOWRAH')  ? 'selected' : '' ?>>Howrah</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Designation <span class="required">*</span></label>
                        <select class="form-select" id="designation" name="designation" required>
                            <option value="">Select Designation</option>
                            <?php
                            $designations = [
                                'Jr. Php developer', 'Sr. Php developer', 'Html designer',
                                'Bidder', 'Sr. Video editor', 'Jr. Video editor', 'HR', 'Branch Manager'
                            ];
                            foreach ($designations as $d):
                            ?>
                            <option value="<?= $d ?>" <?= ($v_designation == $d) ? 'selected' : '' ?>><?= $d ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Email <span class="required">*</span></label>
                        <input type="email" class="form-control" id="email" name="email"
                            value="<?= $v_email ?>" required autocomplete="off">
                        <small id="emailFeedback" class="mt-1 d-block" style="min-height: 20px;"></small>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Phone <span class="required">*</span></label>
                        <input type="tel" class="form-control" id="phone" name="phone"
                            value="<?= $v_phone ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Salary (₹) <span class="required">*</span></label>
                        <input type="number" class="form-control" id="salary" name="salary"
                            value="<?= $v_salary ?>" step="0.01" min="0"
                            <?= $is_edit ? 'readonly' : 'required' ?>>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Experience (Years) <span class="required">*</span></label>
                        <input type="number" class="form-control" id="experience" name="experience"
                            value="<?= $v_experience ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Date of Birth <span class="required">*</span></label>
                        <input type="date" class="form-control" id="dob" name="dob"
                            value="<?= $v_dob ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Joining Date <span class="required">*</span></label>
                        <input type="date" class="form-control" id="joiningDate" name="joiningDate"
                            value="<?= $v_joining ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Access Level <span class="required">*</span></label>
                        <select name="accessLevel" id="accessLevel" class="form-select">
                            <option value="EMPL"  <?= ($v_access == 'EMPL')  ? 'selected' : '' ?>>Employee</option>
                            <option value="HR"    <?= ($v_access == 'HR')    ? 'selected' : '' ?>>HR</option>
                            <option value="ADMIN" <?= ($v_access == 'ADMIN') ? 'selected' : '' ?>>Admin</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Status <span class="required">*</span></label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="active"   <?= ($v_status == 'active')   ? 'selected' : '' ?>>Active</option>
                            <option value="inactive" <?= ($v_status == 'inactive') ? 'selected' : '' ?>>Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Permanent Address <span class="required">*</span></label>
                        <textarea class="form-control" name="permAddress" id="permAddress" rows="3"
                            required><?= $v_perm_addr ?></textarea>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label mb-0">Current Address <span class="required">*</span></label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="sameAddressCheck"
                                    onclick="copyAddress()">
                                <label class="form-check-label small text-muted" for="sameAddressCheck"
                                    style="cursor: pointer;">Same as Permanent</label>
                            </div>
                        </div>
                        <textarea class="form-control" name="currentAddress" id="currentAddress" rows="3"
                            required><?= $v_curr_addr ?></textarea>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Aadhar Number <span class="required">*</span></label>
                        <input type="text" class="form-control" id="aadhar" name="aadhar"
                            value="<?= $v_aadhar ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label optional">PAN Number</label>
                        <input type="text" class="form-control" id="pan" name="pan"
                            value="<?= $v_pan ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Projects</label>
                        <div id="projectContainer">
                            <?php foreach ($existing_projects as $index => $proj): ?>
                                <div class="input-group mb-2 project-input-group d-flex align-items-stretch">
                                    <span class="input-group-text bg-light border-end-0 text-muted"
                                        style="border-top-right-radius: 0; border-bottom-right-radius: 0;">
                                        <i class="fas fa-list-ul"></i>
                                    </span>
                                    <input type="text" class="form-control project-input border-start-0"
                                        value="<?= trim($proj) ?>" placeholder="e.g. Website Redesign"
                                        style="box-shadow: none;">
                                    <?php if ($index == 0): ?>
                                        <button class="btn btn-outline-primary px-3" type="button"
                                            onclick="addProjectField()" title="Add another project"
                                            style="border-top-left-radius: 0; border-bottom-left-radius: 0;">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    <?php else: ?>
                                        <button class="btn btn-outline-danger px-3" type="button"
                                            onclick="removeProjectField(this)" title="Remove project"
                                            style="border-top-left-radius: 0; border-bottom-left-radius: 0;">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <input type="hidden" id="finalProjectString" name="project"
                            value="<?= htmlspecialchars($v_project, ENT_QUOTES) ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            Login Password
                            <?= $is_edit ? '(Leave blank to keep same)' : '<span class="required">*</span>' ?>
                        </label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="passwordField" name="password"
                                <?= $is_edit ? '' : 'required' ?>>
                            <span class="input-group-text" onclick="togglePassword()" style="cursor: pointer;">
                                <i class="fa-solid fa-eye" id="toggleIcon"></i>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="form-group mt-4">
                    <label class="form-label">CV Upload
                        <?= !empty($v_cv) ? '<span class="badge bg-success">CV Exists</span>' : '<span class="required">*</span>' ?>
                    </label>
                    <div class="cv-upload" onclick="document.getElementById('cvInput').click()">
                        <i class="fas fa-file-pdf fa-2x mb-2" style="color: #ef4444;"></i>
                        <div id="cvStatusText"><?= $js_cv_status_text ?></div>
                        <small class="text-muted">Max 5MB</small>
                        <input type="file" id="cvInput" name="cv" accept=".pdf,.doc,.docx" style="display: none;">
                    </div>
                </div>

                <div class="action-buttons">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i> <?= $is_edit ? 'Update Employee' : 'Add Employee' ?>
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="resetForm()">
                        <i class="fas fa-undo me-2"></i> Reset
                    </button>
                </div>
            </div>
        </form>
    </div>

    <script>
        // ── Cached DOM references (looked up once, reused everywhere) ──────────
        const empidInput    = document.getElementById('empid');
        const empidFeedback = document.getElementById('empidFeedback');
        const emailInput    = document.getElementById('email');
        const emailFeedback = document.getElementById('emailFeedback');
        const submitBtn     = document.querySelector('button[type="submit"]');
        const photoPreview  = document.getElementById('photoPreview');
        const permAddress   = document.getElementById('permAddress');
        const currAddress   = document.getElementById('currentAddress');
        const sameAddrCheck = document.getElementById('sameAddressCheck');
        const cvStatusText  = document.getElementById('cvStatusText');
        const csrfInput     = document.querySelector('input[name="<?= $csrf_name ?>"]');

        // ── PHP values passed to JS once ──────────────────────────────────────
        const isUpdateMode  = <?= $js_is_update ?>;
        const originalEmpId = "<?= $js_orig_empid ?>";
        const originalEmail = "<?= $js_orig_email ?>";
        let   hasCustomPhoto = <?= $js_has_photo ?>;
        const initialImgUrl  = '<?= $img_url ?>';
        const initialCvText  = '<?= $js_cv_status_text ?>';
        const csrfName       = '<?= $csrf_name ?>';

        // ── Shared CSRF helper ────────────────────────────────────────────────
        function buildFormData(fields) {
            const fd = new FormData();
            for (const [k, v] of Object.entries(fields)) fd.append(k, v);
            fd.append(csrfName, csrfInput.value);
            return fd;
        }
        function updateCsrf(data) {
            if (data.csrf_hash) csrfInput.value = data.csrf_hash;
        }

        // ── Live Employee ID validation ───────────────────────────────────────
        let typingTimer;
        const doneTypingInterval = 500;

        empidInput.addEventListener('input', function () {
            clearTimeout(typingTimer);
            const currentVal = this.value.trim();

            if (currentVal.length === 0) {
                empidFeedback.innerHTML = '';
                this.style.borderColor  = '';
                submitBtn.disabled      = false;
                return;
            }
            if (isUpdateMode && currentVal === originalEmpId) {
                empidFeedback.innerHTML = '<span class="text-success small fw-bold"><i class="fas fa-check-circle"></i> Original ID (Valid)</span>';
                this.style.borderColor  = '#10b981';
                submitBtn.disabled      = false;
                return;
            }

            empidFeedback.innerHTML = '<span class="text-muted small"><i class="fas fa-spinner fa-spin"></i> Checking availability...</span>';
            typingTimer = setTimeout(() => checkEmployeeId(currentVal), doneTypingInterval);
        });

        function checkEmployeeId(empid) {
            fetch('<?= base_url("Employee/checkEmployeeIdAjax") ?>', {
                method: 'POST',
                body: buildFormData({ empid })
            })
            .then(r => r.json())
            .then(data => {
                updateCsrf(data);
                if (data.exists) {
                    empidFeedback.innerHTML    = '<span class="text-danger small fw-bold"><i class="fas fa-times-circle"></i> This Employee ID is already taken!</span>';
                    empidInput.style.borderColor = '#ef4444';
                    submitBtn.disabled           = true;
                } else {
                    empidFeedback.innerHTML    = '<span class="text-success small fw-bold"><i class="fas fa-check-circle"></i> Employee ID is available!</span>';
                    empidInput.style.borderColor = '#10b981';
                    submitBtn.disabled           = false;
                }
            })
            .catch(err => {
                console.error('Error:', err);
                empidFeedback.innerHTML = '<span class="text-warning small"><i class="fas fa-exclamation-triangle"></i> Error checking ID.</span>';
            });
        }

        // ── Live Email validation ─────────────────────────────────────────────
        let emailTypingTimer;
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/; // compiled once

        emailInput.addEventListener('input', function () {
            clearTimeout(emailTypingTimer);
            const currentVal = this.value.trim();

            if (currentVal.length === 0) {
                emailFeedback.innerHTML = '';
                this.style.borderColor  = '';
                return;
            }
            if (!emailRegex.test(currentVal)) {
                emailFeedback.innerHTML = '<span class="text-muted small">Keep typing a valid email...</span>';
                this.style.borderColor  = '';
                return;
            }
            if (isUpdateMode && currentVal === originalEmail) {
                emailFeedback.innerHTML = '<span class="text-success small fw-bold"><i class="fas fa-check-circle"></i> Original Email (Valid)</span>';
                this.style.borderColor  = '#10b981';
                submitBtn.disabled      = false;
                return;
            }

            emailFeedback.innerHTML = '<span class="text-muted small"><i class="fas fa-spinner fa-spin"></i> Checking availability...</span>';
            emailTypingTimer = setTimeout(() => checkEmployeeEmail(currentVal), doneTypingInterval);
        });

        function checkEmployeeEmail(email) {
            fetch('<?= base_url("Employee/checkEmployeeEmailAjax") ?>', {
                method: 'POST',
                body: buildFormData({ email })
            })
            .then(r => r.json())
            .then(data => {
                updateCsrf(data);
                if (data.exists) {
                    emailFeedback.innerHTML    = '<span class="text-danger small fw-bold"><i class="fas fa-times-circle"></i> This Email is already registered!</span>';
                    emailInput.style.borderColor = '#ef4444';
                    submitBtn.disabled           = true;
                } else {
                    emailFeedback.innerHTML    = '<span class="text-success small fw-bold"><i class="fas fa-check-circle"></i> Email is available!</span>';
                    emailInput.style.borderColor = '#10b981';
                    submitBtn.disabled           = false;
                }
            })
            .catch(err => {
                console.error('Error:', err);
                emailFeedback.innerHTML = '<span class="text-warning small"><i class="fas fa-exclamation-triangle"></i> Error checking email.</span>';
            });
        }

        // ── Avatar live update ────────────────────────────────────────────────
        function updateAvatarLive(name) {
            if (!hasCustomPhoto) {
                const defaultName = name.trim() === '' ? 'New Employee' : name;
                photoPreview.src  = "https://ui-avatars.com/api/?name=" + encodeURIComponent(defaultName) + "&background=461bb9&color=ffffff&size=180&bold=true";
            }
        }

        // ── Photo upload ──────────────────────────────────────────────────────
        document.getElementById('photoInput').addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (!file) return;
            if (file.size > 5 * 1024 * 1024) {
                Swal.fire({ icon: 'error', title: 'File Too Large', text: 'Please upload an image smaller than 5MB.', confirmButtonColor: '#ef4444' });
                this.value = "";
                return;
            }
            const reader = new FileReader();
            reader.onload = e => { photoPreview.src = e.target.result; hasCustomPhoto = true; };
            reader.readAsDataURL(file);
        });

        // ── CV status ─────────────────────────────────────────────────────────
        document.getElementById('cvInput').addEventListener('change', function (e) {
            cvStatusText.innerText = "Selected: " + e.target.files[0].name;
        });

        // ── Password toggle ───────────────────────────────────────────────────
        function togglePassword() {
            const passwordField = document.getElementById("passwordField");
            const icon          = document.getElementById("toggleIcon");
            const isPassword    = passwordField.type === "password";
            passwordField.type  = isPassword ? "text" : "password";
            icon.classList.toggle("fa-eye",       !isPassword);
            icon.classList.toggle("fa-eye-slash",  isPassword);
        }

        // ── Project fields ────────────────────────────────────────────────────
        function addProjectField() {
            const container = document.getElementById('projectContainer');
            const div = document.createElement('div');
            div.className = 'input-group mb-2 project-input-group d-flex align-items-stretch';
            div.innerHTML = `
                <span class="input-group-text bg-light border-end-0 text-muted" style="border-top-right-radius:0;border-bottom-right-radius:0;">
                    <i class="fas fa-list-ul"></i>
                </span>
                <input type="text" class="form-control project-input border-start-0" placeholder="e.g. New App Development" style="box-shadow:none;">
                <button class="btn btn-outline-danger px-3" type="button" onclick="removeProjectField(this)" title="Remove project" style="border-top-left-radius:0;border-bottom-left-radius:0;">
                    <i class="fas fa-minus"></i>
                </button>`;
            container.appendChild(div);
        }

        function removeProjectField(button) {
            button.closest('.project-input-group').remove();
        }

        // ── Copy address ──────────────────────────────────────────────────────
        function copyAddress() {
            if (sameAddrCheck.checked) {
                currAddress.value = permAddress.value;
                currAddress.setAttribute('readonly', true);
                currAddress.style.backgroundColor = "#f3f4f6";
            } else {
                currAddress.removeAttribute('readonly');
                currAddress.style.backgroundColor = "";
                currAddress.value = "";
            }
        }

        permAddress.addEventListener('input', function () {
            if (sameAddrCheck.checked) currAddress.value = this.value;
        });

        // ── Reset ─────────────────────────────────────────────────────────────
        function resetForm() {
            document.getElementById('employeeForm').reset();
            hasCustomPhoto    = <?= $js_has_photo ?>;
            photoPreview.src  = initialImgUrl;
            cvStatusText.innerText = initialCvText;
        }

        // ── Form submission ───────────────────────────────────────────────────
        document.getElementById('employeeForm').addEventListener('submit', function (e) {
            e.preventDefault();

            // Collect project inputs into hidden field
            const allProjects = [];
            document.querySelectorAll('.project-input').forEach(input => {
                if (input.value.trim() !== '') allProjects.push(input.value.trim());
            });
            document.getElementById('finalProjectString').value = allProjects.join(', ');

            const empid    = empidInput.value.trim();
            const empName  = document.getElementById('empName').value.trim();
            const email    = emailInput.value.trim();
            const phone    = document.getElementById('phone').value.trim();
            const aadhar   = document.getElementById('aadhar').value.trim();
            const salary   = parseFloat(document.getElementById('salary').value);
            const password = document.querySelector('input[name="password"]').value;
            const cvInput  = document.getElementById('cvInput').files.length;
            const isUpdate = <?= $js_is_update ?>;

            const errors = [];
            if (empid.length < 5)  errors.push("Employee ID is required and must be at least 5 characters.");
            if (empid.length > 10) errors.push("Employee ID must be no more than 10 characters.");
            if (!/^[a-zA-Z0-9\-]+$/.test(empid)) errors.push("Employee ID must be alphanumeric (hyphens allowed).");
            if (empName.length < 2) errors.push("Employee name is required and must be at least 2 characters.");
            if (isNaN(salary) || salary <= 0)   errors.push("Salary must be a positive number.");
            if (salary > 9999999.99)            errors.push("Salary cannot exceed ₹9,999,999.99. Please enter a valid amount.");
            if (!emailRegex.test(email))        errors.push("Please enter a valid email format.");
            if (!/^\d{10}$/.test(phone.replace(/\D/g, '')))   errors.push("Phone number must be exactly 10 digits.");
            if (!/^\d{12}$/.test(aadhar.replace(/\s/g, ''))) errors.push("Aadhar number must be exactly 12 digits.");
            if (!isUpdate && password.length < 6) errors.push("Password is required and must be at least 6 characters.");
            if (!isUpdate && cvInput === 0)       errors.push("You must upload a CV document for new employees.");

            if (errors.length > 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Validation Error',
                    html: '<div style="text-align: left;">Please fix the following errors:<br><br>• ' + errors.join("<br>• ") + '</div>',
                    confirmButtonColor: '#461bb9'
                });
            } else {
                Swal.fire({
                    title: isUpdate ? 'Update Profile?' : 'Save Employee?',
                    text: "Proceed with saving this employee profile?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#461bb9',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Yes, Save it!'
                }).then(result => {
                    if (result.isConfirmed) {
                        Swal.fire({ title: 'Saving...', text: 'Please wait', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
                        HTMLFormElement.prototype.submit.call(document.getElementById('employeeForm'));
                    }
                });
            }
        });
    </script>
</body>

</html>