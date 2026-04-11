<?php defined('BASEPATH') or exit('No direct script access allowed');
$csrf_name = $this->security->get_csrf_token_name();
$csrf_hash = $this->security->get_csrf_hash();
$today = date('Y-m-d');
?>

<link rel="stylesheet" href="<?= base_url('css/hr/hrBonusReportView.css') ?>">

<div class="main-content">
    <div class="container-fluid p-0">

        <div class="d-flex justify-content-between align-items-center mb-4 pb-2">
            <div class="page-title">
                <h3 class="mb-1 d-flex align-items-center gap-2">
                    <i class="fas fa-gift"></i> Annual Bonus Management
                </h3>
                <p>Track annual eligibility and manage employee performance bonuses.</p>
            </div>

            <form action="<?= base_url('Employee/hrBonusReportView') ?>" method="GET" class="d-flex gap-2">
                <select name="year" class="form-select filter-select" onchange="this.form.submit()">
                    <?php
                    $current_year = date('Y');
                    // Show a range of years in the dropdown
                    for ($y = $current_year + 1; $y >= 2024; $y--): ?>
                        <option value="<?= $y ?>" <?= ($selected_year == $y) ? 'selected' : '' ?>>
                            <?= $y ?> Cycle
                        </option>
                    <?php endfor; ?>
                </select>
            </form>
        </div>

        <div class="row g-4 mb-4 pb-2">
            <div class="col-md-6">
                <div class="stat-card">
                    <div class="stat-icon success">
                        <i class="fas fa-hand-holding-usd"></i>
                    </div>
                    <div class="stat-details">
                        <h6>Active Employees</h6>
                        <h2><?= $total_emps ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="stat-card">
                    <div class="stat-icon primary">
                        <i class="fas fa-history"></i>
                    </div>
                    <div class="stat-details">
                        <h6>Bonus Policy</h6>
                        <h2>365 Days</h2>
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
                            <th>Last Bonus Date</th>
                            <th>Next Eligibility</th>
                            <th class="text-end pe-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($report as $emp):
                            $today = date('Y-m-d');
                            $is_eligible = true;
                            $lock_reason = "";

                            // 1. Calculate Initial Eligibility (365 days from Permanent Date)
                            //
                            $initial_eligibility = !empty($emp->seempd_permanent_date)
                                ? date('Y-m-d', strtotime($emp->seempd_permanent_date . ' + 365 days'))
                                : null;

                            // 2. Determine Eligibility Status and Lock Reason
                            if (empty($emp->seempd_permanent_date)) {
                                //
                                $is_eligible = false;
                                $lock_reason = "Not Permanent";
                            } elseif ($today < $initial_eligibility) {
                                //
                                $is_eligible = false;
                                $lock_reason = "Locked until " . date('d M Y', strtotime($initial_eligibility));
                            } elseif (!empty($emp->next_eligible_date) && $today < $emp->next_eligible_date) {
                                //
                                $is_eligible = false;
                                $lock_reason = "Locked until " . date('d M Y', strtotime($emp->next_eligible_date));
                            }
                            ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-3">
                                        <div>
                                            <div class="fw-bold text-dark mb-1"><?= $emp->seempd_name ?></div>
                                            <div class="text-muted small">
                                                <i class="fas fa-id-badge me-1"></i><?= $emp->seemp_id ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-bold">₹<?= number_format($emp->seempd_salary, 2) ?></div>
                                </td>
                                <td>
                                    <?= !empty($emp->bonus_date) ? date('d M Y', strtotime($emp->bonus_date)) : '<span class="text-muted">No History</span>' ?>
                                    <?php if (!empty($emp->bonus_amount)): ?>
                                        <div class="small text-success fw-bold">₹<?= number_format($emp->bonus_amount, 2) ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($is_eligible): ?>
                                        <span
                                            class="badge-soft bg-success bg-opacity-10 text-success border border-success px-2 py-1">
                                            <i class="fas fa-check-circle me-1"></i> Eligible Now
                                        </span>
                                    <?php else: ?>
                                        <span
                                            class="badge-soft bg-danger bg-opacity-10 text-danger border border-danger px-2 py-1">
                                            <i class="fas fa-lock me-1"></i> <?= $lock_reason ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <!-- Action if user_access level is HR then show the manage button otherwise lock -->
                                <td class="text-end pe-4">
                                    <?php if ($this->session->accesslevel == 'HR'): ?>
                                        <button class="btn btn-sm btn-primary btn-manage"
                                            onclick="openBonusModal('<?= $emp->seemp_id ?>', '<?= addslashes($emp->seempd_name) ?>', <?= (float) $emp->seempd_salary ?>)">
                                            <i class="fas fa-plus-circle me-1"></i> Manage Bonus
                                        </button>
                                    <?php else: ?>
                                        <button class="btn btn-sm btn-secondary btn-lock" disabled>
                                            <i class="fas fa-lock me-1"></i> Locked
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="bonusModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">
                    <i class="fas fa-gift text-primary me-2"></i> Bonus Adjustment: <span id="bonus_emp_name"
                        class="text-primary"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">

                <div id="eligibility_warning" class="alert alert-danger d-none border-0 shadow-sm mb-4">
                    <i class="fas fa-lock me-2"></i>
                    <strong>Policy Restriction:</strong> This employee is not eligible for a new bonus until <span
                        id="next_date_label"></span>.
                </div>

                <div class="card bg-light border-0 rounded-3 mb-4" id="bonus_form_container">
                    <div class="card-body p-4">
                        <form action="<?= base_url('Employee/applyBonus') ?>" method="POST" id="bonusForm">
                            <input type="hidden" name="<?= $csrf_name ?>" value="<?= $csrf_hash ?>">
                            <input type="hidden" name="bonus_empid" id="bonus_empid">

                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label text-muted fw-bold small text-uppercase">Bonus Amount
                                        (₹)</label>
                                    <input type="number" step="0.01" class="form-control" name="bonus_amount" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label text-muted fw-bold small text-uppercase">Issue Date</label>
                                    <input type="date" class="form-control" name="bonus_date" value="<?= $today ?>"
                                        required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label text-muted fw-bold small text-uppercase">Eligibility
                                        Lock</label>
                                    <input type="text" class="form-control bg-white" value="+365 Days" readonly>
                                </div>
                                <div class="col-12">
                                    <label class="form-label text-muted fw-bold small text-uppercase">Reason</label>
                                    <input type="text" class="form-control" name="bonus_reason"
                                        placeholder="e.g. Annual Festival Bonus, Performance 2026" required>
                                </div>
                                <div class="col-12 text-end mt-3">
                                    <button type="submit" class="btn btn-primary btn-manage px-4" id="submitBonusBtn">
                                        Confirm Bonus
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <h6 class="fw-bold mb-3"><i class="fas fa-history text-secondary me-2"></i> Bonus History</h6>
                <div class="table-responsive rounded-3 border">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted small text-uppercase">
                            <tr>
                                <th class="ps-3 py-3">Date</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th class="pe-3">Reason</th>
                            </tr>
                        </thead>
                        <tbody id="bonusHistoryBody"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- <script>
    function openBonusModal(empid, name, salary) {
        document.getElementById('bonus_emp_name').textContent = name;
        document.getElementById('bonus_empid').value = empid;

        // Reset View states
        const warn = document.getElementById('eligibility_warning');
        const form = document.getElementById('bonus_form_container');
        const dateInput = document.querySelector('input[name="bonus_date"]');

        warn.classList.add('d-none');
        form.classList.remove('d-none');

        fetch('<?= base_url("Employee/getBonusHistoryAjax/") ?>' + empid)
            .then(r => r.json())
            .then(res => {
                // 1. Get the joining date from the response
                const joiningDate = res.eligibility.joining_date;

                // 2. Set the minimum allowed date in the calendar picker
                dateInput.setAttribute('min', joiningDate);

                // 3. Auto-adjust value if current "today" is invalid for this employee
                const todayStr = new Date().toISOString().split('T')[0];
                dateInput.value = (todayStr < joiningDate) ? joiningDate : todayStr;

                // 4. Check 365-day Policy Eligibility
                if (!res.eligibility.eligible) {
                    warn.classList.remove('d-none');
                    document.getElementById('next_date_label').textContent = res.eligibility.next_date;
                    form.classList.add('d-none');
                    warn.innerHTML = `<i class="fas fa-lock me-2"></i> 
                    <strong>Policy Restriction:</strong> ${res.eligibility.message}. 
                    Eligibility returns on ${res.eligibility.next_date}`;
                }

                // Render History logic (remains the same)
                const tbody = document.getElementById('bonusHistoryBody');
                tbody.innerHTML = '';
                if (res.history.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="4" class="text-center py-4 text-muted">No records.</td></tr>';
                } else {
                    res.history.forEach(b => {
                        tbody.innerHTML += `
                        <tr>
                            <td class="ps-3 fw-bold">${b.bonus_date}</td>
                            <td class="text-success fw-bold">₹${parseFloat(b.bonus_amount).toFixed(2)}</td>
                            <td><span class="badge-soft">Completed</span></td>
                            <td class="pe-3 small text-muted">${b.bonus_reason}</td>
                        </tr>`;
                    });
                }
            });

        bootstrap.Modal.getOrCreateInstance(document.getElementById('bonusModal')).show();
    }

    /** * NEW: Validation for mistake in selecting past date
     * Shows a message if the selected date is before the joining date.
     */
    document.getElementById('bonusForm').addEventListener('submit', function (e) {
        const dateInput = this.querySelector('input[name="bonus_date"]');
        const minDate = dateInput.getAttribute('min'); // This is the joining_date we set earlier

        if (dateInput.value < minDate) {
            e.preventDefault(); // Stop the form from submitting

            // Show the custom alert message
            alert("You can't select this date because the employee joined on " + minDate);

            // Correct the field automatically to the joining date
            dateInput.value = minDate;
            dateInput.focus();
        }
    });
</script> -->
<script>
function openBonusModal(empid, name, salary) {
    document.getElementById('bonus_emp_name').textContent = name;
    document.getElementById('bonus_empid').value = empid;

    const warn = document.getElementById('eligibility_warning');
    const form = document.getElementById('bonus_form_container');
    const dateInput = document.querySelector('input[name="bonus_date"]');

    warn.classList.add('d-none');
    form.classList.remove('d-none');

    fetch('<?= base_url("Employee/getBonusHistoryAjax/") ?>' + empid)
        .then(r => r.json())
        .then(res => {
            // The EARLIEST date HR can select is Permanent Date + 365 days
            const minAllowedDate = res.eligibility.eligibility_threshold;

            if (minAllowedDate) {
                // Lock the calendar so no date before (Perm Date + 365) can be picked
                dateInput.setAttribute('min', minAllowedDate);
                
                const today = new Date().toISOString().split('T')[0];
                // Set default value to today, or the minAllowedDate if today is too early
                dateInput.value = (today > minAllowedDate) ? today : minAllowedDate;
            }

            if (!res.eligibility.eligible) {
                warn.classList.remove('d-none');
                form.classList.add('d-none');
                warn.innerHTML = `<i class="fas fa-lock me-2"></i> 
                <strong>Policy Restriction:</strong> ${res.eligibility.message}`;
            }

            // Render History...
            const tbody = document.getElementById('bonusHistoryBody');
            tbody.innerHTML = '';
            if (res.history.length === 0) {
                tbody.innerHTML = '<tr><td colspan="4" class="text-center py-4 text-muted">No records.</td></tr>';
            } else {
                res.history.forEach(b => {
                    tbody.innerHTML += `
                    <tr>
                        <td class="ps-3 fw-bold">${b.bonus_date}</td>
                        <td class="text-success fw-bold">₹${parseFloat(b.bonus_amount).toFixed(2)}</td>
                        <td><span class="badge-soft">Completed</span></td>
                        <td class="pe-3 small text-muted">${b.bonus_reason}</td>
                    </tr>`;
                });
            }
        });

    bootstrap.Modal.getOrCreateInstance(document.getElementById('bonusModal')).show();
}

// Final submission guard
document.getElementById('bonusForm').addEventListener('submit', function (e) {
    const dateInput = this.querySelector('input[name="bonus_date fantasy"]');
    const minDate = dateInput.getAttribute('min');

    if (minDate && dateInput.value < minDate) {
        e.preventDefault();
        alert("Selection Error: You can only select a date on or after " + minDate + " (Permanent Date + 365 days).");
        dateInput.value = minDate;
    }
});

</script>