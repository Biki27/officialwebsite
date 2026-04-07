<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= base_url('css/admin/adminEmployeeRegistrationView.css') ?>">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


<body>
    <?php if ($this->session->flashdata('msg')):
        $msg = $this->session->flashdata('msg');
        $isError = (stripos($msg, 'Failed') !== false || stripos($msg, 'Error') !== false);
        ?>
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                Swal.fire({
                    title: '<?= $isError ? "Oops!" : "Success!" ?>',
                    text: <?= json_encode($msg) ?>,
                    icon: '<?= $isError ? "error" : "success" ?>',
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
            action="<?= isset($emp) ? site_url('Employee/updateEmployee/' . $emp->seemp_id) : site_url('Employee/addEmployee') ?>"
            enctype="multipart/form-data">

            <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>"
                value="<?= $this->security->get_csrf_hash(); ?>">

            <div class="form-body" style="position: relative;">
                <input type="hidden" name="linked_applicant_id"
                    value="<?= isset($prefill_applicant) ? $prefill_applicant->sejoba_id : '' ?>">

                <div class="photo-section">
                    <?php
                    // Get the name for the avatar (Existing Employee, Prefilled Applicant, or "New Employee")
                    $emp_name = isset($emp) ? $emp->seempd_name : (isset($prefill_applicant) ? $prefill_applicant->sejoba_name : 'New Employee');

                    // Generate the dynamic initials avatar
                    $fallback_avatar = "https://ui-avatars.com/api/?name=" . urlencode($emp_name) . "&background=461bb9&color=ffffff&size=180&bold=true";

                    $img_url = (isset($emp) && !empty($emp->seempd_img))
                        ? base_url('uploads/' . $emp->seempd_img)
                        : $fallback_avatar;
                    ?>

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
                            value="<?= isset($emp) ? $emp->seempd_name : (isset($prefill_applicant) ? htmlspecialchars($prefill_applicant->sejoba_name, ENT_QUOTES) : '') ?>"
                            required oninput="updateAvatarLive(this.value)">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Employee ID <span class="required">*</span></label>
                        <input type="text" class="form-control" id="empid" name="empid" placeholder="SE26KOL01"
                            value="<?= isset($emp) ? $emp->seemp_id : '' ?>" <?= isset($emp) ? ' style="background-color: #f3f4f6;"' : '' ?> required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Branch <span class="required">*</span></label>
                        <select class="form-select" id="branch" name="branch" required>
                            <option value="KOLKATA" <?= (isset($emp) && $emp->seemp_branch == 'KOLKATA') ? 'selected' : '' ?>>Kolkata</option>
                            <option value="HOWRAH" <?= (isset($emp) && $emp->seemp_branch == 'HOWRAH') ? 'selected' : '' ?>>Howrah</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Designation <span class="required">*</span></label>
                        <input type="text" class="form-control" id="designation" name="designation"
                            value="<?= isset($emp) ? $emp->seempd_designation : (isset($prefill_applicant) ? htmlspecialchars($prefill_applicant->sejoba_position, ENT_QUOTES) : '') ?>"
                            required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email <span class="required">*</span></label>
                        <input type="email" class="form-control" id="email" name="email"
                            value="<?= isset($emp) ? $emp->seemp_email : (isset($prefill_applicant) ? htmlspecialchars($prefill_applicant->sejoba_email, ENT_QUOTES) : '') ?>"
                            required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Phone <span class="required">*</span></label>
                        <input type="tel" class="form-control" id="phone" name="phone"
                            value="<?= isset($emp) ? $emp->seempd_phone : (isset($prefill_applicant) ? htmlspecialchars($prefill_applicant->sejoba_phone, ENT_QUOTES) : '') ?>"
                            required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Salary (₹) <span class="required">*</span></label>
                        <input type="number" class="form-control" id="salary" name="salary"
                            value="<?= isset($emp) ? $emp->seempd_salary : (isset($prefill_applicant) ? htmlspecialchars($prefill_applicant->sejoba_exp_salary, ENT_QUOTES) : '') ?>"
                            step="0.01" min="0"   required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Experience (Years) <span class="required">*</span></label>
                        <input type="number" class="form-control" id="experience" name="experience"
                            value="<?= isset($emp) ? $emp->seempd_experience : (isset($prefill_applicant) ? htmlspecialchars($prefill_applicant->sejoba_experience, ENT_QUOTES) : '') ?>"
                            required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Date of Birth <span class="required">*</span></label>
                        <input type="date" class="form-control" id="dob" name="dob"
                            value="<?= isset($emp) ? $emp->seempd_dob : '1990-01-01' ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Joining Date <span class="required">*</span></label>
                        <input type="date" class="form-control" id="joiningDate" name="joiningDate"
                            value="<?= isset($emp) ? $emp->seempd_joiningdate : date('Y-m-d') ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Access Level <span class="required">*</span></label>
                        <select name="accessLevel" id="accessLevel" class="form-select">
                            <option value="EMPL" <?= (isset($emp) && $emp->seemp_acesslevel == 'EMPL') ? 'selected' : '' ?>>Employee</option>
                            <option value="HR" <?= (isset($emp) && $emp->seemp_acesslevel == 'HR') ? 'selected' : '' ?>>HR
                            </option>
                            <option value="ADMIN" <?= (isset($emp) && $emp->seemp_acesslevel == 'ADMIN') ? 'selected' : '' ?>>Admin</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Status <span class="required">*</span></label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="active" <?= (isset($emp) && $emp->seemp_status == 'active') ? 'selected' : '' ?>>Active</option>
                            <option value="inactive" <?= (isset($emp) && $emp->seemp_status == 'inactive') ? 'selected' : '' ?>>Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Permanent Address <span class="required">*</span></label>
                        <textarea class="form-control" name="permAddress" id="permAddress" rows="3"
                            required><?= isset($emp) ? $emp->seempd_address_permanent : '' ?></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Current Address <span class="required">*</span></label>
                        <textarea class="form-control" name="currentAddress" id="currentAddress" rows="3"
                            required><?= isset($emp) ? $emp->seempd_address_current : '' ?></textarea>
                    </div>
                </div>
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Aadhar Number <span class="required">*</span></label>
                        <input type="text" class="form-control" id="aadhar" name="aadhar"
                            value="<?= isset($emp) ? $emp->seempd_aadhar : '' ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label optional">PAN Number</label>
                        <input type="text" class="form-control" id="pan" name="pan"
                            value="<?= isset($emp) ? $emp->seempd_pan : '' ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Increment (%)</label>
                        <input type="number" step="0.01" class="form-control" id="increment" name="increment"
                            value="<?= isset($emp) ? $emp->seempd_increment : '' ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Projects</label>
                        
                        <div id="projectContainer">
                            <?php
                                // If editing an employee, split their existing projects into an array
                                $existing_projects = [];
                                if (isset($emp) && !empty($emp->seempd_project)) {
                                    $existing_projects = explode(',', $emp->seempd_project);
                                } else {
                                    $existing_projects = ['']; // Start with at least one empty box
                                }
                            ?>
                            
                            <?php foreach($existing_projects as $index => $proj): ?>
                                <div class="input-group mb-2 project-input-group d-flex align-items-stretch">
                                    
                                    <span class="input-group-text bg-light border-end-0 text-muted" style="border-top-right-radius: 0; border-bottom-right-radius: 0;">
                                        <i class="fas fa-list-ul"></i>
                                    </span>
                                    
                                    <input type="text" class="form-control project-input border-start-0" value="<?= trim($proj) ?>" placeholder="e.g. Website Redesign" style="box-shadow: none;">
                                    
                                    <?php if($index == 0): ?>
                                        <button class="btn btn-outline-primary px-3" type="button" onclick="addProjectField()" title="Add another project" style="border-top-left-radius: 0; border-bottom-left-radius: 0;">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    <?php else: ?>
                                        <button class="btn btn-outline-danger px-3" type="button" onclick="removeProjectField(this)" title="Remove project" style="border-top-left-radius: 0; border-bottom-left-radius: 0;">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <input type="hidden" id="finalProjectString" name="project" value="<?= isset($emp) ? htmlspecialchars($emp->seempd_project, ENT_QUOTES) : '' ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            Login Password
                            <?= isset($emp) ? '(Leave blank to keep same)' : '<span class="required">*</span>' ?>
                        </label>

                        <div class="input-group">
                            <input type="password" class="form-control" id="passwordField" name="password"
                                <?= isset($emp) ? '' : 'required' ?>>

                            <span class="input-group-text" onclick="togglePassword()" style="cursor: pointer;">
                                <i class="fa-solid fa-eye" id="toggleIcon"></i>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="form-group mt-4">
                    <label class="form-label">CV Upload
                        <?= isset($emp) && !empty($emp->seempd_cv) ? '<span class="badge bg-success">CV Exists</span>' : '<span class="required">*</span>' ?></label>
                    <div class="cv-upload" onclick="document.getElementById('cvInput').click()">
                        <i class="fas fa-file-pdf fa-2x mb-2" style="color: #ef4444;"></i>
                        <div id="cvStatusText">
                            <?= (isset($emp) && !empty($emp->seempd_cv)) ? 'Click to replace current CV' : 'Click to upload CV (PDF, DOC)' ?>
                        </div>
                        <small class="text-muted">Max 5MB</small>
                        <input type="file" id="cvInput" name="cv" accept=".pdf,.doc,.docx" style="display: none;">
                    </div>
                </div>
                <div class="action-buttons">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i> <?= isset($emp) ? 'Update Employee' : 'Add Employee' ?>
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="resetForm()">
                        <i class="fas fa-undo me-2"></i> Reset
                    </button>
                </div>
            </div>
        </form>
    </div>


    <script>

        let hasCustomPhoto = <?= (isset($emp) && !empty($emp->seempd_img)) ? 'true' : 'false' ?>;

        // Dynamic Avatar live update when typing name
        function updateAvatarLive(name) {
            if (!hasCustomPhoto) {
                const defaultName = name.trim() === '' ? 'New Employee' : name;
                const newAvatar = "https://ui-avatars.com/api/?name=" + encodeURIComponent(defaultName) + "&background=461bb9&color=ffffff&size=180&bold=true";
                document.getElementById('photoPreview').src = newAvatar;
            }
        }

        // Photo preview logic with size validation
        document.getElementById('photoInput').addEventListener('change', function (e) {
            const file = e.target.files[0];

            if (file) {
                if (file.size > 5 * 1024 * 1024) {
                    Swal.fire({
                        icon: 'error',
                        title: 'File Too Large',
                        text: 'Please upload an image smaller than 5MB.',
                        confirmButtonColor: '#ef4444'
                    });
                    this.value = "";
                    return;
                }
                const reader = new FileReader();
                reader.onload = function (e) {
                    document.getElementById('photoPreview').src = e.target.result;
                    hasCustomPhoto = true; // Stop the live text from overwriting the uploaded image
                };
                reader.readAsDataURL(file);
            }
        });

        // CV Status logic
        document.getElementById('cvInput').addEventListener('change', function (e) {
            const fileName = e.target.files[0].name;
            document.getElementById('cvStatusText').innerText = "Selected: " + fileName;
        });

        function resetForm() {
            document.getElementById('employeeForm').reset();

            // Revert back to the proper initials avatar or original uploaded image
            hasCustomPhoto = <?= (isset($emp) && !empty($emp->seempd_img)) ? 'true' : 'false' ?>;
            document.getElementById('photoPreview').src = '<?= $img_url ?>';

            document.getElementById('cvStatusText').innerText = "<?= (isset($emp) && !empty($emp->seempd_cv)) ? 'Click to replace current CV' : 'Click to upload CV (PDF, DOC)' ?>";
        }

        // Form Submission Logic
        document.getElementById('employeeForm').addEventListener('submit', function (e) {
            e.preventDefault(); // ALWAYS stop the form first for SweetAlert
            
            // --- NEW: Combine all project inputs into the hidden field ---
            let allProjects = [];
            document.querySelectorAll('.project-input').forEach(function(input) {
                if (input.value.trim() !== '') {
                    allProjects.push(input.value.trim());
                }
            });
            document.getElementById('finalProjectString').value = allProjects.join(', ');


            const empid = document.getElementById('empid').value.trim();
            const empName = document.getElementById('empName').value.trim();
            const email = document.getElementById('email').value.trim();
            const phone = document.getElementById('phone').value.trim();
            const aadhar = document.getElementById('aadhar').value.trim();
            const salary = parseFloat(document.getElementById('salary').value);
            const password = document.querySelector('input[name="password"]').value;
            const cvInput = document.getElementById('cvInput').files.length;
            const isUpdate = <?= isset($emp) ? 'true' : 'false' ?>;

            let errors = [];
            // Basic Validations 
            // Employee ID: Required, min 5 chars and max 10 chars, alphanumeric
            if (empid.length < 5) errors.push("Employee ID is required and must be at least 5 characters.");
            if (empid.length > 10) errors.push("Employee ID must be no more than 10 characters.");
            if (!/^[a-zA-Z0-9\-]+$/.test(empid)) errors.push("Employee ID must be alphanumeric (hyphens allowed).");

            if (empName.length < 2) errors.push("Employee name is required and must be at least 2 characters.");

            // Salary validation
            if (isNaN(salary) || salary <= 0) errors.push("Salary must be a positive number.");
            if (salary > 9999999.99) errors.push("Salary cannot exceed ₹9,999,999.99. Please enter a valid amount.");


            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) errors.push("Please enter a valid email format.");

            const phoneRegex = /^\d{10}$/;
            if (!phoneRegex.test(phone.replace(/\D/g, ''))) errors.push("Phone number must be exactly 10 digits.");

            const aadharRegex = /^\d{12}$/;
            if (!aadharRegex.test(aadhar.replace(/\s/g, ''))) errors.push("Aadhar number must be exactly 12 digits.");

            if (!isUpdate && password.length < 6) errors.push("Password is required and must be at least 6 characters.");
            if (!isUpdate && cvInput === 0) errors.push("You must upload a CV document for new employees.");

            // Show Errors OR Show Confirmation
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
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Saving...',
                            text: 'Please wait',
                            allowOutsideClick: false,
                            didOpen: () => { Swal.showLoading(); }
                        });
                        // Submit natively to CodeIgniter
                        HTMLFormElement.prototype.submit.call(document.getElementById('employeeForm'));
                    }
                });
            }
        });

        function togglePassword() {
            const passwordField = document.getElementById("passwordField");
            const icon = document.getElementById("toggleIcon");

            if (passwordField.type === "password") {
                passwordField.type = "text";
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");
            } else {
                passwordField.type = "password";
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");
            }
        }

        // Function to dynamically add a new project input field
        function addProjectField() {
            const container = document.getElementById('projectContainer');
            const div = document.createElement('div');
            // Matching the new flexbox alignment
            div.className = 'input-group mb-2 project-input-group d-flex align-items-stretch';
            div.innerHTML = `
                <span class="input-group-text bg-light border-end-0 text-muted" style="border-top-right-radius: 0; border-bottom-right-radius: 0;">
                    <i class="fas fa-list-ul"></i>
                </span>
                <input type="text" class="form-control project-input border-start-0" placeholder="e.g. New App Development" style="box-shadow: none;">
                <button class="btn btn-outline-danger px-3" type="button" onclick="removeProjectField(this)" title="Remove project" style="border-top-left-radius: 0; border-bottom-left-radius: 0;">
                    <i class="fas fa-minus"></i>
                </button>
            `;
            container.appendChild(div);
        }

        // Function to remove a specific project input field
        function removeProjectField(button) {
            button.closest('.project-input-group').remove();
        }

    </script>
</body>

</html>