<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projects Management | Supropriyo Enterprise</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="<?= base_url('css/admin/adminProjectsView.css') ?>" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>

<body>
    <!-- ════════════ FLASH MESSAGES ════════════ -->
    <?php if ($this->session->flashdata('msg')): ?>
        <div class="flash-toast">
            <div class="alert alert-success alert-dismissible fade show shadow-lg" role="alert">
                <i class="fas fa-check-circle me-2"></i><?= $this->session->flashdata('msg') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($this->session->flashdata('error')): ?>
        <div class="flash-toast">
            <div class="alert alert-danger alert-dismissible fade show shadow-lg" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i><?= $this->session->flashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    <?php endif; ?>

    <!-- ════════════ MAIN CONTENT ════════════ -->
    <div class="main-content">
        <div id="projects" class="section active">

            <!-- Page header row -->
            <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
                <h2 class="text-white mb-0">
                    <i class="fas fa-project-diagram me-2"></i>Projects Management
                </h2>
                <button class="btn btn-primary px-4" onclick="openAddModal()">
                    <i class="fas fa-plus me-1"></i>New Project
                </button>
            </div>

            <!-- ─── Stats Cards ─── -->
            <div class="stats-row">
                <div class="stat-card s-total">
                    <div class="stat-number"><?= $total ?></div>
                    <div class="stat-label"><i class="fas fa-layer-group me-1"></i>Total Projects</div>
                </div>
                <div class="stat-card s-running">
                    <div class="stat-number"><?= $running ?></div>
                    <div class="stat-label"><i class="fas fa-spinner me-1"></i>Running</div>
                </div>
                <div class="stat-card s-pending">
                    <div class="stat-number"><?= $pending ?></div>
                    <div class="stat-label"><i class="fas fa-clock me-1"></i>Pending</div>
                </div>
                <div class="stat-card s-done">
                    <div class="stat-number"><?= $completed ?></div>
                    <div class="stat-label"><i class="fas fa-check-circle me-1"></i>Completed</div>
                </div>
            </div>

            <!-- ─── Search & Filter Bar ─── -->
            <div class="search-bar-wrap">
                <div class="row align-items-center g-2">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text bg-transparent border-end-0 text-white-50">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" id="projectSearch" onkeyup="filterProjects()" class="form-control" placeholder="Search...">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <select class="form-select" id="statusFilter" onchange="filterProjects()">
                            <option value="">All Projects</option>
                            <option value="running">Running</option>
                            <option value="pending">Pending</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>
                    <div class="col-md-2 text-end">
                        <span id="visibleBadge"><?= count($projects) ?> shown</span>
                    </div>
                </div>
            </div>

            <!-- ─── Projects Table ─── -->
            <div class="card project-table">
                <div class="table-responsive">
                    <table id="projectsTable" class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th><i class="fas fa-hashtag me-1"></i>ID</th>
                                <th>Project Name</th>
                                <th>Description</th>
                                <th>Start</th>
                                <th>Deadline</th>
                                <th>Client</th>
                                <th>Head</th>
                                <th>Price</th>
                                <th>Status</th>
                                <th style="min-width:100px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($projects)): ?>
                                <?php foreach ($projects as $proj): ?>
                                    <tr data-id="<?= $proj->seproj_id ?>">
                                        <td>
                                            <span class="project-id">
                                                PJ<?= str_pad($proj->seproj_id, 2, '0', STR_PAD_LEFT) ?>
                                            </span>
                                        </td>

                                        <td class="fw-bold text-dark">
                                            <?= htmlspecialchars($proj->seproj_name, ENT_QUOTES, 'UTF-8') ?>
                                            <div class="mt-1">
                                                <a href="javascript:void(0)" class="text-primary small text-decoration-none"
                                                    onclick='viewProjectDetails(<?= htmlspecialchars(json_encode($proj), ENT_QUOTES, "UTF-8") ?>)'>
                                                    <i class="fas fa-info-circle"></i> details
                                                </a>
                                            </div>
                                        </td>

                                        <td class="desc-cell" title="<?= htmlspecialchars($proj->seproj_desc, ENT_QUOTES, 'UTF-8') ?>">
                                            <?= htmlspecialchars($proj->seproj_desc, ENT_QUOTES, 'UTF-8') ?>
                                        </td>

                                        <td><?= $proj->seproj_date ?></td>

                                        <td class="deadline"><?= $proj->seproj_deadline ?></td>

                                        <td><?= htmlspecialchars($proj->seproj_clientid, ENT_QUOTES, 'UTF-8') ?></td>

                                        <td><code><?= htmlspecialchars($proj->seproj_headid, ENT_QUOTES, 'UTF-8') ?></code></td>

                                        <td class="price">₹<?= htmlspecialchars($proj->seproj_price, ENT_QUOTES, 'UTF-8') ?></td>

                                        <td>
                                            <?php if ($proj->seproj_status == 'running'): ?>
                                                <span class="status-badge status-running">Running</span>
                                            <?php elseif ($proj->seproj_status == 'completed'): ?>
                                                <span class="status-badge status-completed">Completed</span>
                                            <?php else: ?>
                                                <span class="status-badge status-existing">Pending</span>
                                            <?php endif; ?>
                                        </td>

                                        <!-- ── Action Buttons ── -->
                                        <td>
                                            <div class="action-btns">
                                                <button class="btn btn-sm btn-outline-primary"
                                                    title="Edit Project"
                                                    onclick='openEditModal(<?= htmlspecialchars(json_encode($proj), ENT_QUOTES, "UTF-8") ?>)'>
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger"
                                                    title="Delete Project"
                                                    onclick="confirmDelete(<?= $proj->seproj_id ?>, '<?= htmlspecialchars($proj->seproj_name, ENT_QUOTES, 'UTF-8') ?>')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="10" class="text-center py-5 text-muted">
                                        <i class="fas fa-folder-open fa-2x mb-2 d-block opacity-50"></i>
                                        No projects found.
                                        <a href="javascript:void(0)" onclick="openAddModal()" class="text-primary">
                                            Add your first project!
                                        </a>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div><!-- /section -->
    </div><!-- /main-content -->


    <!-- ════════════════════════════════════════
         ADD / EDIT PROJECT MODAL
    ════════════════════════════════════════ -->
    <div class="modal fade" id="projectModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
            <div class="modal-content">

                <!-- Header -->
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="modalTitle">
                        <i class="fas fa-plus-circle me-2" id="modalTitleIcon"></i>
                        <span id="modalTitleText">Add New Project</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <!-- Form -->
                <form id="projectModalForm" method="post" action="">
                    <!-- CSRF -->
                    <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>"
                        value="<?= $this->security->get_csrf_hash() ?>" id="csrfInput">

                    <!-- Hidden fields for edit mode -->
                    <input type="hidden" name="projectId" id="modalHiddenId" value="">
                    <input type="hidden" id="modalMode" value="add">

                    <div class="modal-body">

                        <!-- Edit mode info bar -->
                        <div id="editInfoBar" class="alert alert-info py-2 small d-none mb-3">
                            <i class="fas fa-pencil-alt me-1"></i>
                            Editing: <strong id="editInfoLabel"></strong>
                        </div>

                        <div class="form-modal-grid">

                            <!-- Project Name -->
                            <div>
                                <label class="form-label fw-semibold small">
                                    Project Name <span class="required-star">*</span>
                                </label>
                                <input type="text" class="form-control" id="mProjectName"
                                    name="projectName" placeholder="Enter project name" required>
                            </div>

                            <!-- Client Name -->
                            <div>
                                <label class="form-label fw-semibold small">
                                    Client Name <span class="required-star">*</span>
                                </label>
                                <input type="text" class="form-control" id="mClientName"
                                    name="clientName" placeholder="Enter client name" required>
                            </div>

                            <!-- Description (full width) -->
                            <div class="col-full">
                                <label class="form-label fw-semibold small">
                                    Description <span class="required-star">*</span>
                                </label>
                                <textarea class="form-control" id="mDescription" name="description"
                                    rows="3" placeholder="Project description…" required></textarea>
                            </div>

                            <!-- Start Date -->
                            <div>
                                <label class="form-label fw-semibold small">
                                    Start Date <span class="required-star">*</span>
                                </label>
                                <input type="date" class="form-control" id="mStartDate"
                                    name="startDate" required>
                            </div>

                            <!-- Deadline -->
                            <div>
                                <label class="form-label fw-semibold small">
                                    Deadline Date <span class="required-star">*</span>
                                </label>
                                <input type="date" class="form-control" id="mDeadlineDate"
                                    name="deadlineDate" required>
                            </div>

                            <!-- Project Head -->
                            <div>
                                <label class="form-label fw-semibold small">
                                    Head of Project <span class="required-star">*</span>
                                </label>
                                <input type="text" class="form-control" id="mProjectHead"
                                    name="projectHead" placeholder="Project head name" required>
                            </div>

                            <!-- Price -->
                            <div>
                                <label class="form-label fw-semibold small">
                                    Price (₹) <span class="required-star">*</span>
                                </label>
                                <input type="text" class="form-control" id="mPrice"
                                    name="price" placeholder="e.g. 12.5L" required>
                            </div>

                            <!-- Status (full width) -->
                            <div class="col-full">
                                <label class="form-label fw-semibold small">
                                    Status <span class="required-star">*</span>
                                </label>
                                <select class="form-select" id="mStatus" name="status" required>
                                    <option value="">Select Status</option>
                                    <option value="running">Running</option>
                                    <option value="pending">Pending</option>
                                    <option value="completed">Completed</option>
                                </select>
                            </div>

                        </div><!-- /form-modal-grid -->
                    </div><!-- /modal-body -->

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>Cancel
                        </button>
                        <button type="submit" class="btn btn-primary" id="modalSubmitBtn">
                            <i class="fas fa-plus me-1" id="modalSubmitIcon"></i>
                            <span id="modalSubmitText">Add Project</span>
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div><!-- /#projectModal -->


    <!-- ════════════════════════════════════════
         VIEW DETAILS MODAL (original kept)
    ════════════════════════════════════════ -->
    <div class="modal fade" id="viewProjectModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px; overflow: hidden;">
            <div class="modal-header border-0 bg-primary p-4" style="background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);">
                <h4 class="modal-title fw-bold text-white" id="projectTitle" style="letter-spacing: -0.5px;"></h4>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            
            <div class="modal-body p-4 bg-light">
                <div class="row g-3 mb-4">
                    <div class="col-6 col-md-3">
                        <label class="text-muted small fw-bold text-uppercase mb-1 d-block">Project ID</label>
                        <div id="projectId" class="fw-bold text-dark"></div>
                    </div>
                    <div class="col-6 col-md-3">
                        <label class="text-muted small fw-bold text-uppercase mb-1 d-block">Status</label>
                        <div id="projectStatus"></div>
                    </div>
                    <div class="col-6 col-md-3">
                        <label class="text-muted small fw-bold text-uppercase mb-1 d-block">Start Date</label>
                        <div id="projectStart" class="text-dark"></div>
                    </div>
                    <div class="col-6 col-md-3">
                        <label class="text-muted small fw-bold text-uppercase mb-1 d-block">Deadline</label>
                        <div id="projectDeadline" class="text-danger fw-bold"></div>
                    </div>
                </div>

                <hr class="opacity-10">

                <div class="row g-4 mt-1">
                    <div class="col-md-4">
                        <div class="p-3 bg-white rounded-3 shadow-sm border">
                            <label class="text-muted small fw-bold d-block mb-1">Client</label>
                            <span id="projectClient" class="fw-semibold"></span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-white rounded-3 shadow-sm border">
                            <label class="text-muted small fw-bold d-block mb-1">Project Head</label>
                            <span id="projectHead" class="fw-semibold"></span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-white rounded-3 shadow-sm border">
                            <label class="text-muted small fw-bold d-block mb-1">Budget</label>
                            <span id="projectPrice" class="text-primary fw-bold fs-5"></span>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <label class="text-muted small fw-bold text-uppercase mb-2 d-block">Description</label>
                    <div id="projectDesc" class="p-3 bg-white rounded-3 border text-secondary" style="line-height: 1.6; min-height: 100px;"></div>
                </div>
            </div>
            
            <div class="modal-footer border-0 p-3 bg-white">
                <button class="btn btn-light px-4 fw-bold" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div><!-- /#viewProjectModal -->
    <script>
        /* ── Config ── */
        const BASE_URL = "<?= base_url() ?>";
        const CSRF_NAME = "<?= $this->security->get_csrf_token_name() ?>";
        let CSRF_HASH = "<?= $this->security->get_csrf_hash() ?>";

        /* ── Helper: Build fresh POST data with current CSRF hash ── */
        function csrfPost(extra = {}) {
            const data = {};
            data[CSRF_NAME] = CSRF_HASH;
            return Object.assign(data, extra);
        }

        /* ── Date helpers ── */
        function fmtDate(d) {
            if (!d) return '';
            const dt = new Date(d);
            if (isNaN(dt)) return d;
            const m = ('0' + (dt.getMonth() + 1)).slice(-2);
            const dy = ('0' + dt.getDate()).slice(-2);
            return dt.getFullYear() + '-' + m + '-' + dy;
        }

        function todayISO() {
            return fmtDate(new Date());
        }

        function nextMonthISO() {
            const d = new Date();
            d.setMonth(d.getMonth() + 1);
            return fmtDate(d);
        }

        /* ═══════════════════════════════════════
           SEARCH
        ═══════════════════════════════════════ */
        document.getElementById('projectSearch').addEventListener('keyup', function() {
            const q = this.value.toLowerCase().trim();
            const rows = document.querySelectorAll('#projectsTable tbody tr');
            let visible = 0;

            rows.forEach(row => {
                if (row.cells.length < 7) {
                    row.style.display = '';
                    return;
                }
                const id = row.cells[0].innerText.toLowerCase();
                const name = row.cells[1].innerText.toLowerCase();
                const client = row.cells[5].innerText.toLowerCase();
                const head = row.cells[6].innerText.toLowerCase();

                const match = !q || id.includes(q) || name.includes(q) || client.includes(q) || head.includes(q);
                row.style.display = match ? '' : 'none';
                if (match) visible++;
            });

            const total = rows.length;
            document.getElementById('visibleBadge').textContent = (q ? visible : total) + ' shown';
        });


        /* ═══════════════════════════════════════
           OPEN ADD MODAL
        ═══════════════════════════════════════ */
        function openAddModal() {
            setModalMode('add');
            document.getElementById('projectModalForm').reset();
            document.getElementById('modalHiddenId').value = '';
            document.getElementById('mStartDate').value = todayISO();
            document.getElementById('mDeadlineDate').value = nextMonthISO();
            document.getElementById('editInfoBar').classList.add('d-none');

            getOrCreateModal('projectModal').show();
        }

        /* ═══════════════════════════════════════
           OPEN EDIT MODAL
        ═══════════════════════════════════════ */
        function openEditModal(proj) {
            setModalMode('edit');

            document.getElementById('modalHiddenId').value = proj.seproj_id;
            document.getElementById('mProjectName').value = proj.seproj_name;
            document.getElementById('mDescription').value = proj.seproj_desc;
            document.getElementById('mStartDate').value = proj.seproj_date;
            document.getElementById('mDeadlineDate').value = proj.seproj_deadline;
            document.getElementById('mClientName').value = proj.seproj_clientid;
            document.getElementById('mProjectHead').value = proj.seproj_headid;
            document.getElementById('mPrice').value = proj.seproj_price;
            document.getElementById('mStatus').value = proj.seproj_status;

            /* Show edit badge */
            document.getElementById('editInfoBar').classList.remove('d-none');
            document.getElementById('editInfoLabel').textContent =
                'PJ' + String(proj.seproj_id).padStart(2, '0') + ' — ' + proj.seproj_name;

            getOrCreateModal('projectModal').show();
        }

        /* ── Set modal UI to add/edit mode ── */
        function setModalMode(mode) {
            document.getElementById('modalMode').value = mode;
            const isEdit = mode === 'edit';

            document.getElementById('modalTitleIcon').className = isEdit ?
                'fas fa-edit me-2' : 'fas fa-plus-circle me-2';
            document.getElementById('modalTitleText').textContent = isEdit ?
                'Edit Project' : 'Add New Project';
            document.getElementById('modalSubmitIcon').className = isEdit ?
                'fas fa-save me-1' : 'fas fa-plus me-1';
            document.getElementById('modalSubmitText').textContent = isEdit ?
                'Update Project' : 'Add Project';
            document.getElementById('modalSubmitBtn').className = isEdit ?
                'btn btn-success' : 'btn btn-primary';
        }

        /* ── Lazy-create Bootstrap modal instance ── */
        function getOrCreateModal(id) {
            const el = document.getElementById(id);
            return bootstrap.Modal.getOrCreateInstance(el);
        }


        /* ═══════════════════════════════════════
           FORM SUBMIT — with duplicate guard for ADD
        ═══════════════════════════════════════ */
        document.getElementById('projectModalForm').addEventListener('submit', function(e) {
            e.preventDefault();

            /* Required-field check */
            let valid = true;
            this.querySelectorAll('[required]').forEach(el => {
                if (!el.value.trim()) {
                    el.classList.add('is-invalid');
                    valid = false;
                } else {
                    el.classList.remove('is-invalid');
                }
            });

            if (!valid) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Incomplete Form',
                    text: 'Please fill out all fields marked with *.',
                    confirmButtonColor: '#461bb9'
                });
                return;
            }

            const mode = document.getElementById('modalMode').value;

            if (mode === 'edit') {
                /* ─ Edit: submit straight away ─ */
                this.action = BASE_URL + 'Employee/updateProject';
                this.submit();
            } else {
                /* ─ Add: run duplicate check first ─ */
                checkDuplicateThenSubmit();
            }
        });


        /* ═══════════════════════════════════════
           DUPLICATE CHECK
        ═══════════════════════════════════════ */
        function checkDuplicateThenSubmit() {
            const name = document.getElementById('mProjectName').value.trim();

            $.ajax({
                url: BASE_URL + 'Employee/checkDuplicateProject',
                type: 'POST',
                dataType: 'json',
                data: csrfPost({
                    name
                }),
                success(existing) {
                    /* Refresh CSRF from response header if present */
                    if (existing && existing.seproj_id) {
                        /* ── DUPLICATE FOUND ── */
                        const padId = String(existing.seproj_id).padStart(2, '0');

                        Swal.fire({
                            icon: 'warning',
                            title: 'Duplicate Project Found!',
                            html: `
                                A project named <b>"${existing.seproj_name}"</b> already exists.<br>
                                <span class="badge bg-primary mt-1">PJ${padId}</span>
                                &nbsp;|&nbsp;
                                <span class="badge bg-secondary">${existing.seproj_status}</span>
                                <hr class="my-3">
                                What would you like to do?
                            `,
                            showDenyButton: true,
                            showCancelButton: true,
                            confirmButtonText: '✏️ Update Existing Project',
                            denyButtonText: '➕ Add as New Anyway',
                            cancelButtonText: '✏ Change the Name',
                            confirmButtonColor: '#22c55e',
                            denyButtonColor: '#6366f1',
                            cancelButtonColor: '#6b7280',
                            focusConfirm: false,
                        }).then(result => {
                            if (result.isConfirmed) {
                                /* Switch to edit mode for the existing project */
                                getOrCreateModal('projectModal').hide();
                                setTimeout(() => openEditModal(existing), 400);

                            } else if (result.isDenied) {
                                /* Force-add as a new separate project */
                                submitAddForm();
                            }
                            /* Cancel → user stays in modal to fix name */
                        });

                    } else {
                        /* No duplicate — go ahead */
                        submitAddForm();
                    }
                },
                error() {
                    /* Server error: submit anyway (fail-open) */
                    submitAddForm();
                }
            });
        }

        /* ── Actually submit the add form ── */
        function submitAddForm() {
            const form = document.getElementById('projectModalForm');
            form.action = BASE_URL + 'Employee/addProject';
            form.submit();
        }


        /* ═══════════════════════════════════════
           DELETE
        ═══════════════════════════════════════ */
        function confirmDelete(id, name) {
            Swal.fire({
                icon: 'warning',
                title: 'Delete Project?',
                html: `You are about to delete <b>"${name}"</b>.<br>
                       <span class="text-danger small">This action cannot be undone.</span>`,
                showCancelButton: true,
                confirmButtonText: '<i class="fas fa-trash me-1"></i> Yes, Delete',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
            }).then(result => {
                if (!result.isConfirmed) return;

                $.ajax({
                    url: BASE_URL + 'Employee/deleteProject',
                    type: 'POST',
                    dataType: 'json',
                    data: csrfPost({
                        id
                    }),
                    success(res) {
                        if (res.success) {
                            /* Update CSRF hash for next request */
                            if (res.csrf_hash) CSRF_HASH = res.csrf_hash;

                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: res.message || 'Project removed successfully.',
                                timer: 2000,
                                showConfirmButton: false,
                                timerProgressBar: true,
                            }).then(() => {
                                /* Remove row from DOM instead of full reload */
                                const row = document.querySelector(`#projectsTable tr[data-id="${id}"]`);
                                if (row) row.remove();

                                /* Update visible-count badge */
                                const total = document.querySelectorAll('#projectsTable tbody tr').length;
                                document.getElementById('visibleBadge').textContent = total + ' shown';
                            });
                        } else {
                            Swal.fire('Error', res.message || 'Could not delete project.', 'error');
                        }
                    },
                    error() {
                        Swal.fire('Server Error', 'Could not connect. Please try again.', 'error');
                    }
                });
            });
        }


        /* ═══════════════════════════════════════
           VIEW DETAILS (original logic kept)
        ═══════════════════════════════════════ */
        function viewProjectDetails(project) {
            document.getElementById('projectTitle').innerText = project.seproj_name;
            document.getElementById('projectId').innerHTML =
                `<span class="badge bg-primary">PJ${String(project.seproj_id).padStart(2, '0')}</span>`;
            document.getElementById('projectStart').innerText = project.seproj_date;
            document.getElementById('projectDeadline').innerText = project.seproj_deadline;
            document.getElementById('projectClient').innerText = project.seproj_clientid;
            document.getElementById('projectHead').innerText = project.seproj_headid;
            document.getElementById('projectPrice').innerText = '₹' + project.seproj_price;
            document.getElementById('projectDesc').innerText = project.seproj_desc;

            let statusHTML = '';
            if (project.seproj_status === 'running') {
                statusHTML = '<span class="badge bg-success">Running</span>';
            } else if (project.seproj_status === 'completed') {
                statusHTML = '<span class="badge bg-warning text-dark">Completed</span>';
            } else {
                statusHTML = '<span class="badge bg-primary">Pending</span>';
            }
            document.getElementById('projectStatus').innerHTML = statusHTML;

            getOrCreateModal('viewProjectModal').show();
        }


        /* ═══════════════════════════════════════
           AUTO-DISMISS FLASH MESSAGES
        ═══════════════════════════════════════ */
        setTimeout(() => {
            document.querySelectorAll('.flash-toast .alert').forEach(el => {
                bootstrap.Alert.getOrCreateInstance(el).close();
            });
        }, 4500);

        /* ═══════════════════════════════════════
           CLEAR is-invalid on input
        ═══════════════════════════════════════ */
        document.querySelectorAll('#projectModalForm [required]').forEach(el => {
            el.addEventListener('input', () => el.classList.remove('is-invalid'));
        });

        /* ═══════════════════════════════════════
   UNIFIED FILTER (Search + Status)
   This runs entirely in the browser for instant results
═══════════════════════════════════════ */
        function filterProjects() {
            const searchQuery = document.getElementById('projectSearch').value.toLowerCase().trim();
            const statusQuery = document.getElementById('statusFilter').value.toLowerCase();
            const rows = document.querySelectorAll('#projectsTable tbody tr');

            let visibleCount = 0;

            rows.forEach(row => {
                // Skip the "No projects found" row if it exists
                if (row.cells.length < 9) return;

                // Get text content from relevant cells
                const id = row.cells[0].innerText.toLowerCase();
                const name = row.cells[1].innerText.toLowerCase();
                const client = row.cells[5].innerText.toLowerCase();
                const head = row.cells[6].innerText.toLowerCase();

                // Get status from the badge text (Case-insensitive)
                const status = row.querySelector('.status-badge').innerText.toLowerCase();

                // Check if row matches search text
                const matchesSearch = !searchQuery ||
                    id.includes(searchQuery) ||
                    name.includes(searchQuery) ||
                    client.includes(searchQuery) ||
                    head.includes(searchQuery);

                // Check if row matches status dropdown
                const matchesStatus = !statusQuery || status === statusQuery;

                // Show row only if it matches BOTH filters
                if (matchesSearch && matchesStatus) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            // Update the "X shown" badge
            document.getElementById('visibleBadge').textContent = visibleCount + ' shown';
        }
    </script>

</body>

</html>