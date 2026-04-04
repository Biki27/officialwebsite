<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Supropriyo Enterprise</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="<?= base_url('css/admin/adminProjectsView.css') ?>" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>
    
    <!-- Main Content -->
    <div class="main-content">
        

        <!-- Projects Section - ACTIVE BY DEFAULT (no JS needed) -->
        <div id="projects" class="section active">
            <h2 class="text-white mb-4">Projects Management</h2>

            <!-- Static Project Search & Filter (Visual Only) -->
            <div class="project-search">
                <form class="row align-items-center g-3">
                    <div class="col-md-5">
                        <div class="input-group">
                            <span class="input-group-text bg-transparent border-0">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                            <input type="text" id="projectSearch" class="form-control"
                                placeholder="Search by project name, client or ID...">
                        </div>
                    </div>
                    <!-- This section contains the status filter -->
                    <div class="col-md-3">
                        <select name="status" class="form-select border-0 shadow-sm"
                            onchange="window.location.href='<?= base_url('Employee/viewProjects') ?>?status='+this.value">
                            <option value="">All Projects (<?= $total ?> Total)</option>
                            <option value="running" <?= ($this->input->get('status') == 'running') ? 'selected' : '' ?>>
                                Running Projects (<?= $running ?> Active)</option>
                            <option value="pending" <?= ($this->input->get('status') == 'pending') ? 'selected' : '' ?>>
                                Pending Projects (<?= $pending ?> Active)</option>
                            <option value="completed" <?= ($this->input->get('status') == 'completed') ? 'selected' : '' ?>>Completed Projects (<?= $completed ?> Done)</option>
                        </select>
                    </div>
                  
                    <div class="col-md-2">
                        <a href="<?= base_url('Employee/addProjectPage') ?>" class="btn btn-primary w-100">
                            <i class="fas fa-plus me-1"></i>New Project
                        </a>
                    </div>
                </form>
            </div>

            <!-- Projects Table (Perfect Styling) -->
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
                            </tr>
                        </thead>

                        <!-- Added code for the fetching data from MYSQL Database -->
                        <tbody>

                            <?php if (!empty($projects)) {
                                foreach ($projects as $proj) { ?>
                                    <tr>
                                        <td>
                                            <span class="project-id">
                                                PJ<?= str_pad($proj->seproj_id, 2, '0', STR_PAD_LEFT) ?>
                                            </span>
                                        </td>

                                        <td class="fw-bold text-dark">
                                            <?= $proj->seproj_name ?>

                                            <div class="mt-1">
                                                <a href="javascript:void(0)" class="text-primary small text-decoration-none"
                                                    onclick='viewProjectDetails(<?= htmlspecialchars(json_encode($proj), ENT_QUOTES, "UTF-8") ?>)'>

                                                    <i class="fas fa-info-circle"></i> view details
                                                </a>
                                            </div>
                                        </td>

                                        <td><?= $proj->seproj_desc ?></td>

                                        <td><?= $proj->seproj_date ?></td>

                                        <td class="deadline"><?= $proj->seproj_deadline ?></td>

                                        <td><?= $proj->seproj_clientid ?></td>

                                        <td><code><?= $proj->seproj_headid ?></code></td>

                                        <td class="price">₹<?= $proj->seproj_price ?></td>

                                        <td>
                                            <?php if ($proj->seproj_status == 'running') { ?>

                                                <span class="status-badge status-running">Running</span>

                                            <?php } elseif ($proj->seproj_status == 'completed') { ?>

                                                <span class="status-badge status-completed">Completed</span>

                                            <?php } else { ?>

                                                <span class="status-badge status-existing">Pending</span>

                                            <?php } ?>

                                        </td>
                                    </tr>

                                <?php }
                            } ?>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <script>

            document.getElementById("projectSearch").addEventListener("keyup", function () {

                let searchValue = this.value.toLowerCase();

                let rows = document.querySelectorAll("#projectsTable tbody tr");

                rows.forEach(function (row) {

                    let projectID = row.cells[0].innerText.toLowerCase();
                    let projectName = row.cells[1].innerText.toLowerCase();
                    let clientName = row.cells[5].innerText.toLowerCase();

                    if (
                        projectID.includes(searchValue) ||
                        projectName.includes(searchValue) ||
                        clientName.includes(searchValue)
                    ) {
                        row.style.display = "";
                    } else {
                        row.style.display = "none";
                    }

                });

            });
        </script>
    </div>
    <!-- Project Details Modal -->
    <div class="modal fade" id="viewProjectModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content" style="border-radius:20px;">

                <div class="modal-header">
                    <h4 class="modal-title fw-bold text-primary" id="projectTitle"></h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Project ID:</strong>
                            <div id="projectId"></div>
                        </div>

                        <div class="col-md-6">
                            <strong>Status:</strong>
                            <div id="projectStatus"></div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Start Date:</strong>
                            <div id="projectStart"></div>
                        </div>

                        <div class="col-md-6">
                            <strong>Deadline:</strong>
                            <div id="projectDeadline"></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <strong>Client:</strong>
                        <div id="projectClient"></div>
                    </div>

                    <div class="mb-3">
                        <strong>Project Head:</strong>
                        <div id="projectHead"></div>
                    </div>

                    <div class="mb-3">
                        <strong>Price:</strong>
                        <div id="projectPrice"></div>
                    </div>

                    <div>
                        <strong>Description:</strong>
                        <div id="projectDesc" class="p-3 bg-light rounded mt-2"></div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">
                        Close
                    </button>
                </div>

            </div>
        </div>
    </div>
    <script>
        function viewProjectDetails(project) {

            document.getElementById('projectTitle').innerText = project.seproj_name;

            document.getElementById('projectId').innerHTML =
                `<span class="badge bg-primary">PJ${String(project.seproj_id).padStart(2, '0')}</span>`;

            document.getElementById('projectStart').innerText = project.seproj_date;
            document.getElementById('projectDeadline').innerText = project.seproj_deadline;
            document.getElementById('projectClient').innerText = project.seproj_clientid;
            document.getElementById('projectHead').innerText = project.seproj_headid;
            document.getElementById('projectPrice').innerText = "₹" + project.seproj_price;
            document.getElementById('projectDesc').innerText = project.seproj_desc;

            // Status badge
            let statusHTML = '';

            if (project.seproj_status === 'running') {
                statusHTML = `<span class="badge bg-success">Running</span>`;
            } else if (project.seproj_status === 'completed') {
                statusHTML = `<span class="badge bg-warning text-dark">Completed</span>`;
            } else {
                statusHTML = `<span class="badge bg-primary">Pending</span>`;
            }

            document.getElementById('projectStatus').innerHTML = statusHTML;

            // Bootstrap 5 Modal initialization
            var modal = new bootstrap.Modal(document.getElementById('viewProjectModal'));
            modal.show();
        }
    </script>

</body>

</html>