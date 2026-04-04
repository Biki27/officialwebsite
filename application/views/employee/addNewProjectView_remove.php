<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Project | Supropriyo Enterprise</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url() ?>css/admin/addNewProjectView.css">
     
</head>
<body>
    <!-- Main Content -->
    <div class="main-content">
        <div class="project-container">
            <!-- Header -->
            <div class="form-header">
                <h1 class="form-title">
                    <i class="fas fa-project-diagram me-3"></i>
                    New Project Details
                </h1>
                <!-- <p class="form-subtitle">Complete project information for enterprise tracking</p> -->
            </div>

            <!-- FORM TAG STARTS HERE - WRAPS EVERYTHING -->
            <form id="projectForm" method="post" action="<?= base_url('Employee/addProject') ?>">

                <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>"
                    value="<?= $this->security->get_csrf_hash(); ?>">

                <!-- Form Body -->
                <div class="form-body">
                    <!-- Project ID Fetch Section - NOW INSIDE FORM -->
                    <div class="project-id-section">
                        <div class="project-id-group">
                            <label
                                style="font-weight: 600; color: #374151; font-size: 0.9rem; white-space: nowrap;">Project
                                ID:</label>
                            <input type="text" class="project-id-input" id="projectId" name="projectId"
                                placeholder="Enter Project ID">
                        </div>
                        <button type="button" class="fetch-btn" onclick="fetchProject()">
                            <i class="fas fa-search me-1"></i>Fetch
                        </button>
                    </div>

                    <!-- Form Grid -->
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Project Name <span class="required">*</span></label>
                            <input type="text" class="form-control" id="projectName" name="projectName"
                                placeholder="Project Name" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Description <span class="required">*</span></label>
                            <textarea class="form-control form-control-textarea" id="description" name="description"
                                required></textarea>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Start Date <span class="required">*</span></label>
                            <input type="date" class="form-control" id="startDate" name="startDate" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Deadline Date <span class="required">*</span></label>
                            <input type="date" class="form-control" id="deadlineDate" name="deadlineDate" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Client Name <span class="required">*</span></label>
                            <input type="text" class="form-control" id="clientName" name="clientName"
                                placeholder="Enter Client Name" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Head of Project <span class="required">*</span></label>
                            <input type="text" class="form-control" id="projectHead" name="projectHead"
                                placeholder="Enter Project Head Name" required>
                        </div>
                      
                        <div class="form-group">
                            <label class="form-label">Price (₹) <span class="required">*</span></label>
                            <input type="text" class="form-control" id="price" name="price" placeholder="12.5L"
                                required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Status <span class="required">*</span></label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="">Select Status</option>
                                <option value="running">Running</option>
                                <option value="completed">Completed</option>
                                <option value="pending">Pending</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons - NOW INSIDE FORM -->
                <div class="action-buttons">
                    <button type="submit" id="addBtn" formaction="<?= base_url('Employee/addProject') ?>"
                        class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Add Project
                    </button>

                    <button type="submit" id="updateBtn"
                        formaction="<?= base_url('Employee/updateProject') ?>" class="btn btn-success d-none">
                        <i class="fas fa-edit me-2"></i>Update Project
                    </button>

                    <button type="button" class="btn btn-secondary" onclick="resetForm()">
                        <i class="fas fa-undo me-2"></i>Reset
                    </button>
                </div>
            </form> <!--FORM TAG ENDS HERE -->
        </div>
    </div>

    <script>
        $(document).ready(function() {
            
            // --- Helper: Format Date to YYYY-MM-DD for input fields ---
            function formatDate(date) {
                let d = new Date(date),
                    month = '' + (d.getMonth() + 1),
                    day = '' + d.getDate(),
                    year = d.getFullYear();

                if (month.length < 2) month = '0' + month;
                if (day.length < 2) day = '0' + day;

                return [year, month, day].join('-');
            }

            // --- 1. Set Default Dates on Load ---
            let today = new Date();
            let nextMonth = new Date();
            nextMonth.setMonth(nextMonth.getMonth() + 1);

            $('#startDate').val(formatDate(today));
            $('#deadlineDate').val(formatDate(nextMonth));

            // --- 2. Form Reset Function ---
            window.resetForm = function() {
                $('#projectForm')[0].reset();
                $('#projectId').val('');
                
                // Reset dates to default
                $('#startDate').val(formatDate(new Date()));
                let futureDate = new Date();
                futureDate.setMonth(futureDate.getMonth() + 1);
                $('#deadlineDate').val(formatDate(futureDate));

                // Reset buttons
                $('#addBtn').removeClass('d-none');
                $('#updateBtn').addClass('d-none');
            };

            // --- 3. AJAX Fetch Function ---
            window.fetchProject = function() {
                let projectId = $('#projectId').val();

                if (!projectId) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Missing ID',
                        text: 'Please enter a Project ID to fetch data.',
                        confirmButtonColor: '#461bb9'
                    });
                    return;
                }

                // Clean the ID and prepare CSRF Token
                projectId = projectId.replace(/[^0-9]/g, "");
                let csrfName = "<?= $this->security->get_csrf_token_name(); ?>";
                let csrfHash = "<?= $this->security->get_csrf_hash(); ?>";

                // Build POST Data
                let postData = { id: projectId };
                postData[csrfName] = csrfHash;

                // jQuery AJAX Call
                $.ajax({
                    url: "<?= base_url('Employee/fetchProject') ?>",
                    type: "POST",
                    dataType: "json",
                    data: postData,
                    success: function(data) {
                        if (!data) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Not Found',
                                text: 'No project exists with this ID.',
                                confirmButtonColor: '#ef4444'
                            });
                            return;
                        }

                        // Success Toast
                        Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        }).fire({
                            icon: 'success',
                            title: 'Project Data Loaded!'
                        });

                        // Auto-fill all inputs using jQuery .val()
                        $('#projectName').val(data.seproj_name);
                        $('#description').val(data.seproj_desc);
                        $('#startDate').val(data.seproj_date);
                        $('#deadlineDate').val(data.seproj_deadline);
                        $('#clientName').val(data.seproj_clientid);
                        $('#projectHead').val(data.seproj_headid);
                        $('#price').val(data.seproj_price);
                        $('#status').val(data.seproj_status);

                        // Swap Buttons
                        $('#addBtn').addClass('d-none');
                        $('#updateBtn').removeClass('d-none');
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Server Error',
                            text: 'Could not connect to the server. Please try again.',
                            confirmButtonColor: '#ef4444'
                        });
                    }
                });
            };

            // --- 4. Pre-Submit Validation ---
            $('#projectForm').on('submit', function(e) {
                let isValid = true;
                
                // Check if all required fields have a value
                $(this).find('[required]').each(function() {
                    if (!$(this).val()) {
                        isValid = false;
                    }
                });

                if (!isValid) {
                    e.preventDefault(); // Stop form submission
                    Swal.fire({
                        icon: 'warning',
                        title: 'Incomplete Form',
                        text: 'Please fill out all required fields marked with *.',
                        confirmButtonColor: '#461bb9'
                    });
                }
                // If valid, the form submits normally to CodeIgniter
            });
            
            // --- 5. Premium Logout Alert ---
             

        });
    </script>
</body>

</html>