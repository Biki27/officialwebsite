<?php defined('BASEPATH') or exit('No direct script access allowed');
$csrf_name = $this->security->get_csrf_token_name();
$csrf_hash = $this->security->get_csrf_hash();
$min_inc_date = date('Y-m-01');
$current_ym_str = date('Y-m');
?>

<link rel="stylesheet" href="<?= base_url('css/hr/hrIncrementReportView.css') ?>">

<div class="main-content">
    <div class="container-fluid p-0">

        <div class="d-flex justify-content-between align-items-center mb-4 pb-2">
            <div class="page-title">
                <h3 class="mb-1 d-flex align-items-center gap-2">
                    <i class="fas fa-chart-line"></i> Compensation & Increments
                </h3>
                <p>Analyze annual frequency and manage employee salary adjustments.</p>
            </div>

            <form action="<?= base_url('Employee/incrementReport') ?>" method="GET" class="d-flex gap-2">
                <select name="year" class="form-select filter-select" onchange="this.form.submit()">
                    <?php
                    $current_year = date('Y');
                    for ($y = $current_year + 5; $y >= 2024; $y--): ?>
                        <option value="<?= $y ?>" <?= ($selected_year == $y) ? 'selected' : '' ?>><?= $y ?> Cycle</option>
                    <?php endfor; ?>
                </select>
            </form>
        </div>

        <div class="row g-4 mb-4 pb-2">
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-icon primary">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-details">
                        <h6>Total Staff</h6>
                        <h2><?= $total_emps ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-icon success">
                        <i class="fas fa-chart-pie"></i>
                    </div>
                    <div class="stat-details">
                        <h6>Incremented (<?= $selected_year ?>)</h6>
                        <h2><?= $incremented_count ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-icon warning">
                        <i class="fas fa-hourglass-half"></i>
                    </div>
                    <div class="stat-details">
                        <h6>Pending</h6>
                        <h2><?= $pending_count ?></h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-card">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th class="ps-4">Employee Details</th>
                            <th>Current Salary</th>
                            <th><?= $selected_year ?> Frequency</th>
                            <th><?= $selected_year ?> Growth</th>
                            <th class="text-end pe-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($report as $emp): ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-3">
                                        <!-- <div class="avatar-sm bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 40px; height: 40px;">
                                            <?= strtoupper(substr($emp->seempd_name, 0, 1)) ?>
                                        </div> -->
                                        <div>
                                            <div class="fw-bold text-dark mb-1" style="font-size: 0.95rem;"><?= $emp->seempd_name ?></div>
                                            <div class="text-muted" style="font-size: 0.8rem;">
                                                <i class="fas fa-id-badge me-1"></i><?= $emp->seemp_id ?>
                                                <span class="mx-1">•</span>
                                                <?= $emp->seempd_designation ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <div class="fw-bold text-dark">₹<?= number_format($emp->seempd_salary, 2) ?></div>
                                </td>

                                <td>
                                    <?php if (empty($emp->inc_count) || $emp->inc_count == 0): ?>
                                        <span class="badge-soft bg-danger bg-opacity-10 text-danger border border-danger">
                                            0 Increments
                                        </span>
                                    <?php elseif ($emp->inc_count == 1): ?>
                                        <span class="badge-soft bg-success bg-opacity-10 text-success border border-success">
                                            1 Increment
                                        </span>
                                    <?php else: ?>
                                        <span class="badge-soft bg-warning bg-opacity-10 text-warning border border-warning" title="Multiple adjustments this year!">
                                            <i class="fas fa-exclamation-triangle"></i> <?= $emp->inc_count ?> Increments
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <?php if (!empty($emp->total_inc_amount)): ?>
                                        <div class="text-success fw-bold mb-1">+ ₹<?= number_format($emp->total_inc_amount, 2) ?></div>
                                        <div class="text-muted" style="font-size: 0.8rem;">
                                            Latest: <strong><?= $emp->latest_percentage ?>%</strong> on <?= date('d M Y', strtotime($emp->last_inc_date)) ?>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <!-- Action if user_access level is HR then show the manage button otherwise lock -->
                                <td class="text-end pe-4">
                                    <?php if ($this->session->accesslevel == 'HR'): ?>
                                        <button class="btn btn-sm btn-primary btn-manage"
                                            onclick='openIncrementModal("<?= $emp->seemp_id ?>", <?= htmlspecialchars(json_encode($emp->seempd_name), ENT_QUOTES, "UTF-8") ?>, <?= (float)$emp->seempd_salary ?>)'>
                                            <i class="fas fa-sliders-h me-1"></i> Manage
                                        </button>
                                    <?php else: ?>
                                        <button class="btn btn-sm btn-secondary btn-lock" disabled>
                                            <i class="fas fa-lock me-1"></i> Locked
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                        <?php if (empty($report)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="fas fa-folder-open fa-3x mb-3 opacity-25"></i>
                                    <h5 class="fw-bold">No Records Found</h5>
                                    <p class="mb-0">There are no active employees available to display for this cycle.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="incrementModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-dark">
                    <i class="fas fa-chart-line text-primary me-2"></i> Increment Timeline: <span id="inc_emp_name" class="text-primary"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">

                <div class="card bg-light border-0 rounded-3 mb-4">
                    <div class="card-body p-4">
                        <h6 class="card-title fw-bold mb-3">
                            <i class="fas fa-plus-circle text-primary me-1"></i> Apply New Adjustment
                        </h6>

                        <div id="inc_month_warning" class="alert alert-warning d-none mb-3 border-0 shadow-sm" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Warning:</strong> An increment already exists for the selected month.
                        </div>

                        <div id="inc_pastmonth_warning" class="alert alert-danger d-none mb-3 border-0 shadow-sm" role="alert">
                            <i class="fas fa-ban me-2"></i>
                            <strong>Not Allowed:</strong> The effective date is in a past month. Please select a date in <strong><?= date('F Y') ?></strong> or later.
                        </div>

                        <form action="<?= base_url('Employee/applyIncrement') ?>" method="POST" id="incrementForm">
                            <input type="hidden" name="<?= $csrf_name ?>" value="<?= $csrf_hash ?>">
                            <input type="hidden" name="inc_empid" id="inc_empid">
                            <input type="hidden" name="old_salary" id="old_salary">

                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label text-muted fw-bold small text-uppercase">Current Salary</label>
                                    <input type="text" class="form-control bg-white" id="display_old_salary" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label text-muted fw-bold small text-uppercase">Increment (%)</label>
                                    <input type="number" step="0.01" min="1" max="100" class="form-control" name="inc_percentage" id="inc_percentage" oninput="calculateIncrement()" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label text-muted fw-bold small text-uppercase">Amount (₹)</label>
                                    <input type="number" step="0.01" class="form-control" name="inc_amount" id="inc_amount" oninput="calculateIncrementFromAmount()">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label text-muted fw-bold small text-uppercase">New Salary (₹)</label>
                                    <input type="number" step="0.01" class="form-control bg-white text-success fw-bold" name="new_salary" id="new_salary" readonly>
                                </div>

                                <div class="col-md-4 mt-3">
                                    <label class="form-label text-muted fw-bold small text-uppercase">Effective Date</label>
                                    <input type="date" class="form-control" name="inc_date" id="inc_date" min="<?= $min_inc_date ?>" required>
                                    <small id="inc_date_hint" class="text-muted d-block mt-1">
                                        Earliest: <?= date('d M Y', strtotime($min_inc_date)) ?>
                                    </small>
                                </div>
                                <div class="col-md-8 mt-3">
                                    <label class="form-label text-muted fw-bold small text-uppercase">Reason / Audit Note</label>
                                    <input type="text" class="form-control" name="inc_reason" placeholder="e.g. Annual Appraisal 2026, Market Correction">
                                </div>

                                <div class="col-12 text-end mt-4">
                                    <button type="submit" class="btn btn-primary btn-manage px-4" id="applyIncrementBtn">
                                        Save Increment
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <h6 class="fw-bold mb-3"><i class="fas fa-history text-secondary me-2"></i> Audit History</h6>
                <div class="table-responsive rounded-3 border">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted small text-uppercase">
                            <tr>
                                <th class="ps-3 py-3">Effective Date</th>
                                <th>Old Salary</th>
                                <th>Increment</th>
                                <th>New Salary</th>
                                <th>Reason</th>
                                <th class="pe-3">Status</th>
                            </tr>
                        </thead>
                        <tbody id="incrementHistoryBody"></tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // ── Cached DOM refs ───────────────────────────────────────────────────────
    const incMonthWarning = document.getElementById('inc_month_warning');
    const incPastWarning = document.getElementById('inc_pastmonth_warning');
    const applyIncrementBtn = document.getElementById('applyIncrementBtn');
    const incDateInput = document.getElementById('inc_date');
    const MIN_INC_YM = '<?= $current_ym_str ?>';

    // ── Global Array to track months ──
    let existingIncrementMonths = [];

    // ── Increment Modal Logic ──────────────────────────────────────────────────
    function openIncrementModal(empid, name, currentSalary) {
        document.getElementById('inc_emp_name').textContent = name;
        document.getElementById('inc_empid').value = empid;
        document.getElementById('old_salary').value = currentSalary;
        document.getElementById('display_old_salary').value = '₹ ' + currentSalary;
        document.getElementById('inc_percentage').value = '';
        document.getElementById('inc_amount').value = '';
        document.getElementById('new_salary').value = currentSalary;
        document.getElementById('inc_date').value = '';

        // Reset warnings and history array
        existingIncrementMonths = [];
        incMonthWarning.classList.add('d-none');
        incPastWarning.classList.add('d-none');
        applyIncrementBtn.disabled = false;

        fetch('<?= base_url("Employee/getIncrementHistoryAjax/") ?>' + empid)
            .then(r => r.json())
            .then(data => {
                const tbody = document.getElementById('incrementHistoryBody');
                tbody.innerHTML = '';

                if (!Array.isArray(data) || data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4">No past increments found.</td></tr>';
                } else {
                    data.forEach(inc => {
                        // Store the "YYYY-MM" of this increment so we can check against it later
                        const incYM = inc.inc_effective_date.substring(0, 7);
                        existingIncrementMonths.push(incYM);

                        const isPending = (inc.inc_status === 'pending');
                        const statusBadge = isPending ?
                            '<span class="badge-soft bg-warning bg-opacity-10 text-warning border border-warning px-2 py-1">Pending</span>' :
                            '<span class="badge-soft bg-success bg-opacity-10 text-success border border-success px-2 py-1">Applied</span>';

                        tbody.innerHTML += `
                <tr>
                  <td class="ps-3 fw-medium">${inc.inc_effective_date}</td>
                  <td>₹${parseFloat(inc.old_salary).toFixed(2)}</td>
                  <td class="text-success fw-bold">+₹${parseFloat(inc.inc_amount).toFixed(2)} (${parseFloat(inc.inc_percentage).toFixed(2)}%)</td>
                  <td class="fw-bold text-dark">₹${parseFloat(inc.new_salary).toFixed(2)}</td>
                  <td class="text-muted small">${inc.inc_reason || '-'}</td>
                  <td class="pe-3">${statusBadge}</td>
                </tr>`;
                    });
                }
            })
            .catch(() => {
                document.getElementById('incrementHistoryBody').innerHTML = '<tr><td colspan="6" class="text-center text-danger">Error loading history.</td></tr>';
            });

        bootstrap.Modal.getOrCreateInstance(document.getElementById('incrementModal')).show();
    }

    // ── Check date validity dynamically when the user picks a date ──
    incDateInput.addEventListener('change', function() {
        const val = this.value;

        // If cleared, reset everything
        if (!val) {
            incPastWarning.classList.add('d-none');
            incMonthWarning.classList.add('d-none');
            applyIncrementBtn.disabled = false;
            return;
        }

        const selectedYM = val.substring(0, 7); // Extracts "YYYY-MM"

        // Check 1: Is the date in the past?
        if (selectedYM < MIN_INC_YM) {
            incPastWarning.classList.remove('d-none');
            incMonthWarning.classList.add('d-none');
            applyIncrementBtn.disabled = true;
            this.value = ''; // clear the invalid input
        }
        // Check 2: Does an increment ALREADY exist for this specific month?
        else if (existingIncrementMonths.includes(selectedYM)) {
            incMonthWarning.classList.remove('d-none');
            incPastWarning.classList.add('d-none');
            applyIncrementBtn.disabled = true;
        }
        // All clear! Allow saving.
        else {
            incPastWarning.classList.add('d-none');
            incMonthWarning.classList.add('d-none');
            applyIncrementBtn.disabled = false;
        }
    });

    function calculateIncrement() {
        const old = parseFloat(document.getElementById('old_salary').value) || 0;
        const pct = parseFloat(document.getElementById('inc_percentage').value) || 0;
        const amount = old * (pct / 100);
        document.getElementById('inc_amount').value = amount.toFixed(2);
        document.getElementById('new_salary').value = (old + amount).toFixed(2);
    }

    function calculateIncrementFromAmount() {
        const old = parseFloat(document.getElementById('old_salary').value) || 0;
        const amount = parseFloat(document.getElementById('inc_amount').value) || 0;
        const pct = old > 0 ? (amount / old) * 100 : 0;
        document.getElementById('inc_percentage').value = pct.toFixed(2);
        document.getElementById('new_salary').value = (old + amount).toFixed(2);
    }
</script>