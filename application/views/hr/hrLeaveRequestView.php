<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leave Request Management</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('css/hr/hrLeaveRequestView.css') ?>">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>

    <?php if ($this->session->flashdata('msg')): 
        $msg = $this->session->flashdata('msg');
        $isError = (stripos($msg, 'Failed') !== false || stripos($msg, 'Error') !== false);
    ?>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const Toast = Swal.mixin({
                toast: true, position: 'top-end', showConfirmButton: false, timer: 3500, timerProgressBar: true
            });
            Toast.fire({ icon: '<?= $isError ? "error" : "success" ?>', title: <?= json_encode($msg) ?> });
        });
    </script>
    <?php endif; ?>

    <div class="main-content">
        <div class="container-fluid">

            <!-- ══ PAGE HEADER ══ -->
            <div class="page-header mb-4">
                <div>
                    <h2 class="page-title">Leave Request Management</h2>
                    <p class="page-subtitle"><i class="fas fa-calendar-alt me-1"></i> Review, approve, or reject employee leave applications</p>
                </div>
            </div>

            <!-- ══ STATS CARDS ══ -->
            <?php
                $total    = count($requests);
                $pending  = count(array_filter((array)$requests, fn($r) => $r->seemrq_status === 'pending'));
                $approved = count(array_filter((array)$requests, fn($r) => $r->seemrq_status === 'approved'));
                $rejected = count(array_filter((array)$requests, fn($r) => $r->seemrq_status === 'rejected'));

                // Find old pending requests (> 3 days old)
                $reminders = array_filter((array)$requests, function($r) {
                    return $r->seemrq_status === 'pending' &&
                           (strtotime('now') - strtotime($r->seemrq_reqdate)) > (3 * 86400);
                });
            ?>

            <div class="stats-row">
                <div class="stat-card stat-total" onclick="filterByStatus('all')">
                    <div class="stat-icon"><i class="fas fa-layer-group"></i></div>
                    <div class="stat-info">
                        <div class="stat-number"><?= $total ?></div>
                        <div class="stat-label">Total Requests</div>
                    </div>
                </div>
                <div class="stat-card stat-pending" onclick="filterByStatus('pending')">
                    <div class="stat-icon"><i class="fas fa-hourglass-half"></i></div>
                    <div class="stat-info">
                        <div class="stat-number"><?= $pending ?></div>
                        <div class="stat-label">Awaiting Action</div>
                    </div>
                    <?php if ($pending > 0): ?>
                        <span class="stat-badge"><?= $pending ?> pending</span>
                    <?php endif; ?>
                </div>
                <div class="stat-card stat-approved" onclick="filterByStatus('approved')">
                    <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                    <div class="stat-info">
                        <div class="stat-number"><?= $approved ?></div>
                        <div class="stat-label">Approved</div>
                    </div>
                </div>
                <div class="stat-card stat-rejected" onclick="filterByStatus('rejected')">
                    <div class="stat-icon"><i class="fas fa-times-circle"></i></div>
                    <div class="stat-info">
                        <div class="stat-number"><?= $rejected ?></div>
                        <div class="stat-label">Rejected</div>
                    </div>
                </div>
                <?php if (count($reminders) > 0): ?>
                <div class="stat-card stat-reminder pulse-card" onclick="filterByStatus('reminder')">
                    <div class="stat-icon"><i class="fas fa-bell"></i></div>
                    <div class="stat-info">
                        <div class="stat-number"><?= count($reminders) ?></div>
                        <div class="stat-label">Overdue Reviews</div>
                    </div>
                    <span class="stat-badge reminder-badge">3+ days old</span>
                </div>
                <?php endif; ?>
            </div>

            <!-- ══ REMINDER BANNER ══ -->
            <?php if (count($reminders) > 0): ?>
            <div class="reminder-banner" id="reminderBanner">
                <div class="reminder-icon"><i class="fas fa-bell"></i></div>
                <div class="reminder-text">
                    <strong><?= count($reminders) ?> pending request<?= count($reminders) > 1 ? 's' : '' ?></strong>
                    <?= count($reminders) > 1 ? 'have' : 'has' ?> been waiting for more than 3 days and require your immediate attention.
                </div>
                <button class="reminder-action-btn" onclick="filterByStatus('reminder')">
                    <i class="fas fa-eye me-1"></i> Review Now
                </button>
                <button class="reminder-close-btn" onclick="this.closest('.reminder-banner').style.display='none'">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <?php endif; ?>

            <!-- ══ FILTER BAR ══ -->
            <div class="filter-bar">
                <div class="filter-tabs" id="statusTabs">
                    <button class="filter-tab active" data-status="all">
                        <i class="fas fa-list me-1"></i> All
                        <span class="tab-count"><?= $total ?></span>
                    </button>
                    <button class="filter-tab tab-pending" data-status="pending">
                        <i class="fas fa-hourglass-half me-1"></i> Pending
                        <span class="tab-count"><?= $pending ?></span>
                    </button>
                    <button class="filter-tab tab-approved" data-status="approved">
                        <i class="fas fa-check me-1"></i> Approved
                        <span class="tab-count"><?= $approved ?></span>
                    </button>
                    <button class="filter-tab tab-rejected" data-status="rejected">
                        <i class="fas fa-times me-1"></i> Rejected
                        <span class="tab-count"><?= $rejected ?></span>
                    </button>
                    <?php if (count($reminders) > 0): ?>
                    <button class="filter-tab tab-reminder" data-status="reminder">
                        <i class="fas fa-bell me-1"></i> Overdue
                        <span class="tab-count reminder-count"><?= count($reminders) ?></span>
                    </button>
                    <?php endif; ?>
                </div>

                <div class="search-wrap">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" id="searchInput" placeholder="Search by name, ID, reason…" autocomplete="off">
                    <button class="search-clear" id="clearSearch" style="display:none;" onclick="clearSearch()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

            <!-- ══ TABLE ══ -->
            <div class="table-container">
                <table class="leave-table" id="leaveTable">
                    <thead>
                        <tr>
                            <th>Request</th>
                            <th>Employee</th>
                            <th>Leave Period</th>
                            <th>Days</th>
                            <th>Reason</th>
                            <th>Requested On</th>
                            <th>Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <?php if (!empty($requests)): ?>
                            <?php foreach ($requests as $req): ?>
                                <?php
                                    $isOverdue = ($req->seemrq_status === 'pending' &&
                                                  (strtotime('now') - strtotime($req->seemrq_reqdate)) > (3 * 86400));
                                    $rowClass = $isOverdue ? 'row-overdue' : '';
                                    $daysWaiting = round((strtotime('now') - strtotime($req->seemrq_reqdate)) / 86400);
                                ?>
                                <tr class="<?= $rowClass ?>"
                                    data-status="<?= strtolower($req->seemrq_status) ?>"
                                    data-overdue="<?= $isOverdue ? '1' : '0' ?>"
                                    data-search="<?= strtolower($req->seemrq_empid . ' ' . ($req->seempd_name ?? '') . ' ' . $req->seemrq_reason) ?>">

                                    <!-- Request ID -->
                                    <td>
                                        <div class="req-id">
                                            <span class="req-id-badge">REQ<?= str_pad($req->seemrq_id, 4, '0', STR_PAD_LEFT) ?></span>
                                            <?php if ($isOverdue): ?>
                                                <span class="overdue-flag" title="Waiting <?= $daysWaiting ?> days">
                                                    <i class="fas fa-exclamation-triangle"></i> <?= $daysWaiting ?>d
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </td>

                                    <!-- Employee -->
                                    <td>
                                        <div class="emp-cell">
                                            <div class="emp-avatar"><?= strtoupper(substr($req->seempd_name ?? 'N', 0, 1)) ?></div>
                                            <div>
                                                <div class="emp-name"><?= htmlspecialchars($req->seempd_name ?? 'N/A') ?></div>
                                                <div class="emp-id"><?= $req->seemrq_empid ?></div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Period -->
                                    <td>
                                        <div class="period-cell">
                                            <div class="period-from"><i class="fas fa-play-circle me-1"></i><?= date('d M Y', strtotime($req->seemrq_fromdate)) ?></div>
                                            <div class="period-arrow"><i class="fas fa-arrow-down"></i></div>
                                            <div class="period-to"><i class="fas fa-stop-circle me-1"></i><?= date('d M Y', strtotime($req->seemrq_todate)) ?></div>
                                        </div>
                                    </td>

                                    <!-- Days -->
                                    <td>
                                        <span class="days-badge"><?= $req->seemrq_days ?> day<?= $req->seemrq_days > 1 ? 's' : '' ?></span>
                                    </td>

                                    <!-- Reason -->
                                    <td>
                                        <div class="reason-cell" title="<?= htmlspecialchars($req->seemrq_reason) ?>">
                                            <?= htmlspecialchars(strlen($req->seemrq_reason) > 40 ? substr($req->seemrq_reason, 0, 40) . '…' : $req->seemrq_reason) ?>
                                        </div>
                                    </td>

                                    <!-- Requested On -->
                                    <td>
                                        <div class="date-cell">
                                            <span class="date-main"><?= date('d M Y', strtotime($req->seemrq_reqdate)) ?></span>
                                            <?php if ($isOverdue): ?>
                                                <span class="date-sub overdue-text"><?= $daysWaiting ?> days ago</span>
                                            <?php else: ?>
                                                <span class="date-sub"><?= $daysWaiting <= 0 ? 'Today' : ($daysWaiting === 1 ? 'Yesterday' : $daysWaiting . ' days ago') ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </td>

                                    <!-- Status Badge -->
                                    <td>
                                        <?php if ($req->seemrq_status === 'approved'): ?>
                                            <span class="status-chip chip-approved"><i class="fas fa-check-circle me-1"></i>Approved</span>
                                        <?php elseif ($req->seemrq_status === 'rejected'): ?>
                                            <span class="status-chip chip-rejected"><i class="fas fa-times-circle me-1"></i>Rejected</span>
                                        <?php else: ?>
                                            <span class="status-chip chip-pending"><i class="fas fa-clock me-1"></i>Pending</span>
                                        <?php endif; ?>
                                    </td>

                                    <!-- Actions -->
                                    <td>
                                        <div class="action-group">
                                            <!-- View Details -->
                                            <button class="action-btn btn-view view-btn"
                                                    title="View Details"
                                                    data-id="<?= $req->seemrq_id ?>"
                                                    data-emp="<?= $req->seemrq_empid ?>"
                                                    data-name="<?= htmlspecialchars($req->seempd_name ?? '') ?>"
                                                    data-reason="<?= htmlspecialchars($req->seemrq_reason) ?>"
                                                    data-days="<?= $req->seemrq_days ?>"
                                                    data-from="<?= date('d M Y', strtotime($req->seemrq_fromdate)) ?>"
                                                    data-to="<?= date('d M Y', strtotime($req->seemrq_todate)) ?>"
                                                    data-reqdate="<?= date('d M Y', strtotime($req->seemrq_reqdate)) ?>"
                                                    data-summary="<?= htmlspecialchars($req->seemrq_summary ?? '') ?>"
                                                    data-status="<?= $req->seemrq_status ?>">
                                                <i class="fas fa-eye"></i>
                                            </button>

                                            <?php if ($req->seemrq_status === 'pending'): ?>
                                            <!-- Quick Approve -->
                                            <form method="post" action="<?= base_url('index.php/Employee/viewEmployeeLeaveRequests') ?>" class="quick-form">
                                                <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                                                <input type="hidden" name="request_id" value="<?= $req->seemrq_id ?>">
                                                <input type="hidden" name="status" value="approved">
                                                <button type="submit" class="action-btn btn-approve" title="Approve Leave">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                            <!-- Quick Reject -->
                                            <form method="post" action="<?= base_url('index.php/Employee/viewEmployeeLeaveRequests') ?>" class="quick-form">
                                                <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                                                <input type="hidden" name="request_id" value="<?= $req->seemrq_id ?>">
                                                <input type="hidden" name="status" value="rejected">
                                                <button type="submit" class="action-btn btn-reject" title="Reject Leave">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </form>
                                            <?php else: ?>
                                            <!-- Change Status -->
                                            <form method="post" action="<?= base_url('index.php/Employee/viewEmployeeLeaveRequests') ?>" class="status-update-form quick-form">
                                                <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                                                <input type="hidden" name="request_id" value="<?= $req->seemrq_id ?>">
                                                <select name="status" class="mini-select status-select">
                                                    <option value="pending"  <?= $req->seemrq_status == 'pending'  ? 'selected' : '' ?>>Pending</option>
                                                    <option value="approved" <?= $req->seemrq_status == 'approved' ? 'selected' : '' ?>>Approved</option>
                                                    <option value="rejected" <?= $req->seemrq_status == 'rejected' ? 'selected' : '' ?>>Rejected</option>
                                                </select>
                                                <button type="submit" class="action-btn btn-update" title="Update Status">
                                                    <i class="fas fa-sync-alt"></i>
                                                </button>
                                            </form>
                                            <?php endif; ?>
                                        </div>
                                    </td>

                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="empty-state">
                                    <div class="empty-icon"><i class="fas fa-inbox"></i></div>
                                    <div class="empty-text">No leave requests found</div>
                                    <div class="empty-sub">Employees haven't submitted any leave applications yet</div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>

                <!-- No results after filter -->
                <div class="no-results" id="noResults" style="display:none;">
                    <div class="empty-icon"><i class="fas fa-search"></i></div>
                    <div class="empty-text">No matching requests</div>
                    <div class="empty-sub">Try adjusting your search or filter criteria</div>
                </div>
            </div>

            <!-- Results info -->
            <div class="results-info" id="resultsInfo"></div>

        </div><!-- /container -->
    </div><!-- /main-content -->

    <!-- ══ DETAIL MODAL ══ -->
    <div class="modal fade" id="requestModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content detail-modal">

                <div class="modal-header detail-modal-header">
                    <div class="modal-title-group">
                        <span class="modal-req-id" id="modal_req_id"></span>
                        <span id="modal_status_chip"></span>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body detail-modal-body">
                    <div class="detail-grid">
                        <div class="detail-section">
                            <div class="detail-section-title"><i class="fas fa-user me-2"></i>Employee Info</div>
                            <div class="detail-field">
                                <span class="detail-label">Employee ID</span>
                                <span class="detail-value" id="modal_emp"></span>
                            </div>
                            <div class="detail-field">
                                <span class="detail-label">Full Name</span>
                                <span class="detail-value fw-semibold" id="modal_name"></span>
                            </div>
                        </div>

                        <div class="detail-section">
                            <div class="detail-section-title"><i class="fas fa-calendar-alt me-2"></i>Leave Details</div>
                            <div class="detail-field">
                                <span class="detail-label">From Date</span>
                                <span class="detail-value" id="modal_from"></span>
                            </div>
                            <div class="detail-field">
                                <span class="detail-label">To Date</span>
                                <span class="detail-value" id="modal_to"></span>
                            </div>
                            <div class="detail-field">
                                <span class="detail-label">Total Days</span>
                                <span class="detail-value" id="modal_days"></span>
                            </div>
                            <div class="detail-field">
                                <span class="detail-label">Requested On</span>
                                <span class="detail-value" id="modal_reqdate"></span>
                            </div>
                        </div>
                    </div>

                    <div class="detail-section mt-3">
                        <div class="detail-section-title"><i class="fas fa-comment-alt me-2"></i>Reason & Summary</div>
                        <div class="detail-field">
                            <span class="detail-label">Reason</span>
                            <span class="detail-value" id="modal_reason"></span>
                        </div>
                        <div class="detail-field">
                            <span class="detail-label">Summary</span>
                            <span class="detail-value text-secondary" id="modal_summary"></span>
                        </div>
                    </div>
                </div>

                <div class="modal-footer detail-modal-footer" id="modal_actions">
                    <!-- Quick action buttons injected here for pending requests -->
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function () {

        // ── STATE ──
        let currentFilter = 'all';
        const rows   = Array.from(document.querySelectorAll('#tableBody tr[data-status]'));
        const search = document.getElementById('searchInput');
        const noRes  = document.getElementById('noResults');
        const resInfo = document.getElementById('resultsInfo');

        // ── FILTER FUNCTION ──
        function applyFilters() {
            const q = search.value.trim().toLowerCase();
            let visible = 0;

            rows.forEach(row => {
                const status   = row.dataset.status;
                const overdue  = row.dataset.overdue === '1';
                const haystack = row.dataset.search || row.innerText.toLowerCase();

                const matchStatus =
                    currentFilter === 'all'      ? true :
                    currentFilter === 'reminder'  ? (status === 'pending' && overdue) :
                    status === currentFilter;

                const matchSearch = q === '' || haystack.includes(q);

                if (matchStatus && matchSearch) {
                    row.style.display = '';
                    visible++;
                } else {
                    row.style.display = 'none';
                }
            });

            noRes.style.display = visible === 0 ? 'block' : 'none';

            const label = currentFilter === 'all' ? 'requests' :
                          currentFilter === 'reminder' ? 'overdue requests' :
                          currentFilter + ' requests';
            resInfo.textContent = visible > 0
                ? `Showing ${visible} ${label}${q ? ' matching "' + q + '"' : ''}`
                : '';

            document.getElementById('clearSearch').style.display = q ? 'inline-flex' : 'none';
        }

        // ── TAB CLICKS ──
        document.querySelectorAll('.filter-tab').forEach(btn => {
            btn.addEventListener('click', function () {
                document.querySelectorAll('.filter-tab').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                currentFilter = this.dataset.status;
                applyFilters();
            });
        });

        // ── SEARCH ──
        search.addEventListener('input', applyFilters);

        // ── EXPOSE for stat cards ──
        window.filterByStatus = function(status) {
            const tab = document.querySelector(`.filter-tab[data-status="${status}"]`);
            if (tab) {
                document.querySelectorAll('.filter-tab').forEach(b => b.classList.remove('active'));
                tab.classList.add('active');
                currentFilter = status;
                applyFilters();
                document.querySelector('.filter-bar').scrollIntoView({ behavior: 'smooth' });
            }
        };

        window.clearSearch = function() {
            search.value = '';
            applyFilters();
        };

        // ── MODAL LOGIC ──
        const bsModal = new bootstrap.Modal(document.getElementById('requestModal'));

        document.querySelectorAll('.view-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                const d = this.dataset;
                const status = d.status;

                document.getElementById('modal_req_id').textContent  = 'REQ' + String(d.id).padStart(4, '0');
                document.getElementById('modal_emp').textContent      = d.emp;
                document.getElementById('modal_name').textContent     = d.name;
                document.getElementById('modal_from').textContent     = d.from;
                document.getElementById('modal_to').textContent       = d.to;
                document.getElementById('modal_days').textContent     = d.days + ' Days';
                document.getElementById('modal_reqdate').textContent  = d.reqdate;
                document.getElementById('modal_reason').textContent   = d.reason;
                document.getElementById('modal_summary').textContent  = d.summary || '—';

                // Status chip
                const chipHtml = {
                    pending:  '<span class="status-chip chip-pending"><i class="fas fa-clock me-1"></i>Pending</span>',
                    approved: '<span class="status-chip chip-approved"><i class="fas fa-check-circle me-1"></i>Approved</span>',
                    rejected: '<span class="status-chip chip-rejected"><i class="fas fa-times-circle me-1"></i>Rejected</span>',
                };
                document.getElementById('modal_status_chip').innerHTML = chipHtml[status] || '';

                bsModal.show();
            });
        });

        // ── SWEETALERT FOR QUICK APPROVE / REJECT ──
        document.querySelectorAll('.quick-form').forEach(form => {
            // Only intercept forms with a fixed hidden status (not the mini-select forms)
            const hiddenStatus = form.querySelector('input[name="status"]');
            if (!hiddenStatus) return;

            form.addEventListener('submit', function (e) {
                e.preventDefault();
                const val   = hiddenStatus.value;
                const color = val === 'approved' ? '#059669' : (val === 'rejected' ? '#dc2626' : '#d97706');
                const icon  = val === 'approved' ? 'success' : (val === 'rejected' ? 'error' : 'question');

                Swal.fire({
                    title: val === 'approved' ? 'Approve this leave?' : 'Reject this leave?',
                    text: `This will mark the request as ${val.toUpperCase()}.`,
                    icon: icon,
                    showCancelButton: true,
                    confirmButtonColor: color,
                    cancelButtonColor: '#64748b',
                    confirmButtonText: val === 'approved' ? '<i class="fas fa-check me-1"></i> Yes, Approve' : '<i class="fas fa-times me-1"></i> Yes, Reject',
                    cancelButtonText: 'Cancel',
                    reverseButtons: true
                }).then(result => {
                    if (result.isConfirmed) {
                        Swal.fire({ title: 'Updating…', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
                        HTMLFormElement.prototype.submit.call(this);
                    }
                });
            });
        });

        // ── SWEETALERT FOR STATUS-CHANGE FORMS (non-pending rows) ──
        document.querySelectorAll('.status-update-form').forEach(form => {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                const sel   = this.querySelector('.status-select').value;
                const color = sel === 'approved' ? '#059669' : (sel === 'rejected' ? '#dc2626' : '#d97706');

                Swal.fire({
                    title: 'Update Status?',
                    text: `Change this request to ${sel.toUpperCase()}?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: color,
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Yes, Update',
                    reverseButtons: true
                }).then(result => {
                    if (result.isConfirmed) {
                        Swal.fire({ title: 'Saving…', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
                        HTMLFormElement.prototype.submit.call(this);
                    }
                });
            });
        });

        // Initial render
        applyFilters();
    });
    </script>
</body>
</html>
