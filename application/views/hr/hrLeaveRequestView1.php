<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Request Management</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= base_url('css/hr/hrLeaveRequestView.css') ?>">
</head>

<body>

    <div class="main-content">
        <div class="container-fluid">

            <!-- HEADER -->
            <div class="page-header d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="text-white fw-bold mb-1">Employee Request Management</h2>
                    <h4><p class="text-white-50 small">History of Manage leave and requests</p></h4>
                </div>

                <div class="d-flex gap-2">
                    <select id="statusFilter" class="form-select rounded-pill px-3" style="width:150px;">
                        <option value="all">All</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                    </select>

                    <input type="text" id="searchInput" class="form-control rounded-pill px-3" placeholder="Search...">
                </div>
            </div>

            <!-- TABLE -->
            <div class="table-container">
                <table class="table align-middle">
                    <thead>
                        <tr> 
                            <th>Request ID</th>
                            <th>Employee ID</th>
                            <th>Name</th>
                            <th>Request Date</th>
                            <th>Duration</th>
                            <th>Days</th>
                            <th>Reason</th>
                            <th>Status</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (!empty($requests)): ?>
                            <?php foreach ($requests as $req): ?>

                                <?php
                                // Status color logic
                                $statusClass = 'bg-warning text-dark';
                                if ($req->seemrq_status == 'approved')
                                    $statusClass = 'bg-success';
                                if ($req->seemrq_status == 'rejected')
                                    $statusClass = 'bg-danger';
                                ?>

                                <tr data-status="<?= strtolower($req->seemrq_status) ?>">
                                    <td>
                                        REQ<?= str_pad($req->seemrq_id, 2, '0', STR_PAD_LEFT) ?>
                                    </td>

                                    <td><?= $req->seemrq_empid ?></td>
                                    <td><?= $req->seempd_name ?? 'N/A' ?></td>

                                    <td>
                                        <?= date('dM Y', strtotime($req->seemrq_reqdate)) ?>
                                    </td>

                                    <td>
                                        <?= date('dM Y', strtotime($req->seemrq_fromdate)) ?>
                                        →
                                        <?= date('dM Y', strtotime($req->seemrq_todate)) ?>
                                    </td>

                                    <td><?= $req->seemrq_days ?></td>

                                    <td><?= $req->seemrq_reason ?></td>

                                    <td>
                                        <form method="post" action="<?= base_url('index.php/Employee/viewEmployeeLeaveRequests') ?>"
                                            class="status-update-form">
                                            <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>"
                                                value="<?= $this->security->get_csrf_hash(); ?>">
                                            <input type="hidden" name="request_id" value="<?= $req->seemrq_id ?>">

                                            <div class="status-update-controls">
                                                <select name="status" class="form-select form-select-sm status-select" required>
                                                    <option value="pending" <?= ($req->seemrq_status == 'pending') ? 'selected' : '' ?>>Pending</option>
                                                    <option value="approved" <?= ($req->seemrq_status == 'approved') ? 'selected' : '' ?>>Approved</option>
                                                    <option value="rejected" <?= ($req->seemrq_status == 'rejected') ? 'selected' : '' ?>>Rejected</option>
                                                </select>
                                                <button type="submit" class="btn btn-sm btn-outline-primary status-update-btn">
                                                    Update
                                                </button>
                                            </div>
                                        </form>
                                    </td>

                                    <td class="text-center">
                                        <button class="btn btn-sm btn-primary view-btn" data-id="<?= $req->seemrq_id ?>"
                                            data-emp="<?= $req->seemrq_empid ?>"
                                            data-name="<?= htmlspecialchars($req->seempd_name ?? '') ?>"
                                            data-reason="<?= $req->seemrq_reason ?>" data-days="<?= $req->seemrq_days ?>"
                                            data-summary="<?= htmlspecialchars($req->seemrq_summary) ?>"
                                            data-status="<?= $req->seemrq_status ?>">
                                            View
                                        </button>
                                    </td>
                                </tr>

                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    No requests found
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <!-- REQUEST VIEW MODAL -->
            <div class="modal fade" id="requestModal" tabindex="-1">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">

                        <div class="modal-header">
                            <h5 class="modal-title fw-bold text-primary">Request Details</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">

                            <div class="mb-2"><b>Request ID:</b> <span id="modal_id"></span></div>
                            <div class="mb-2"><b>Employee ID:</b> <span id="modal_emp"></span></div>
                            <div class="mb-2"><b>Name:</b> <span id="modal_name"></span></div>
                            <div class="mb-2"><b>Reason:</b> <span id="modal_reason"></span></div>
                            <div class="mb-2"><b>Days:</b> <span id="modal_days"></span></div>
                            <div class="mb-2"><b>Summary:</b> <span id="modal_summary"></span></div>
                            <div class="mb-2"><b>Status:</b> <span id="modal_status"></span></div>

                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const searchInput = document.getElementById('searchInput');
            const statusFilter = document.getElementById('statusFilter');
            const rows = document.querySelectorAll('tbody tr');

            function filterTable() {
                const searchText = searchInput.value.toLowerCase();
                const selectedStatus = statusFilter.value.toLowerCase();

                rows.forEach(row => {

                    const rowText = row.innerText.toLowerCase();
                    const rowStatus = row.getAttribute('data-status');

                    const matchSearch = rowText.includes(searchText);
                    const matchStatus = (selectedStatus === 'all' || rowStatus === selectedStatus);

                    if (matchSearch && matchStatus) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }

                });
            }

            searchInput.addEventListener('input', filterTable);
            statusFilter.addEventListener('change', filterTable);

        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const modal = new bootstrap.Modal(document.getElementById('requestModal'));

            document.querySelectorAll('.view-btn').forEach(button => {
                button.addEventListener('click', function () {

                    document.getElementById('modal_id').innerText = "REQ" + this.dataset.id;
                    document.getElementById('modal_emp').innerText = this.dataset.emp;
                    document.getElementById('modal_name').innerText = this.dataset.name;
                    document.getElementById('modal_reason').innerText = this.dataset.reason;
                    document.getElementById('modal_days').innerText = this.dataset.days + " Days";
                    document.getElementById('modal_summary').innerText = this.dataset.summary;
                    document.getElementById('modal_status').innerText = this.dataset.status;

                    modal.show();
                });
            });

        });
    </script>
</body>

</html>