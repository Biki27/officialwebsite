<?php
defined('BASEPATH') or exit('No direct script access allowed');

// ── PHP constants echoed into the page once ───────────────────────────────
// First day of the current month → minimum allowed effective date
$min_inc_date   = date('Y-m-01');           // e.g. "2026-04-01"
$current_ym_str = date('Y-m');              // e.g. "2026-04"

// Flash message
$flash_msg  = $this->session->flashdata('msg');
$has_flash  = (bool) $flash_msg;
$flash_icon = $has_flash
  ? (stripos($flash_msg, 'Success') !== false ? 'success'
    : (stripos($flash_msg, 'Failed')  !== false ? 'error' : 'info'))
  : '';

// CSRF (cached once — reused for modal form)
$csrf_name = $this->security->get_csrf_token_name();
$csrf_hash = $this->security->get_csrf_hash();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <link href="<?= base_url('css/admin/adminEmployeesView.css') ?>" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" defer></script>
</head>

<body>

  <?php if ($has_flash): ?>
    <script>
      document.addEventListener("DOMContentLoaded", function() {
        Swal.mixin({
          toast: true,
          position: 'top-end',
          showConfirmButton: false,
          timer: 3000,
          timerProgressBar: true
        }).fire({
          icon: '<?= $flash_icon ?>',
          title: <?= json_encode($flash_msg) ?>
        });
      });
    </script>
  <?php endif; ?>

  <!-- Main Content -->
  <div class="main-content">
    <div id="employees" class="section active">
      <h2 class="text-white mb-4">Employee Management</h2>

      <!-- Search Box -->
      <?= form_open('Employee/viewEmployee') ?>
      <div class="search-box">
        <div class="row align-items-center">
          <div class="col-12 col-md-6 mb-3 mb-md-0">
            <input type="text" class="form-control" id="searchInput"
              placeholder="Search employees by name, ID, or designation...">
          </div>
          <div class="col-12 col-md-3 mb-3 mb-md-0">
            <select class="form-control" id="statusFilter">
              <option value="">All Status</option>
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
            </select>
          </div>
          <div class="col-12 col-md-3">
            <button class="btn btn-view w-100" type="button" id="searchBtn">
              <i class="fas fa-search me-2"></i>Filter Results
            </button>
          </div>
        </div>
      </div>
      <?= form_close() ?>

      <!-- Employees Table -->
      <div class="table-responsive">
        <table class="table employees-table">
          <thead>
            <tr>
              <th>Emp ID</th>
              <th>Employee Name</th>
              <th>Designation</th>
              <th>Email</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="employeesTableBody">
            <?php foreach ($employees as $emp): ?>
              <tr>
                <td>
                  <span class="emp-id"><?= $emp->seemp_id ?></span><br>
                  <a href="javascript:void(0)" class="text-primary small text-decoration-none"
                    onclick='viewEmployeeQuickDetails("<?= $emp->seemp_id ?>", <?= htmlspecialchars(json_encode($emp->seempd_name), ENT_QUOTES, "UTF-8") ?>)'>
                    <i class="fas fa-info-circle"></i> view details
                  </a>
                </td>
                <td><strong><?= $emp->seempd_name ?></strong></td>
                <td><strong><?= $emp->seempd_designation ?></strong></td>
                <td><?= $emp->seemp_email ?></td>
                <td>
                  <span class="status-badge <?= $emp->seemp_status == 'active' ? 'text-bg-primary' : 'text-bg-warning' ?>">
                    <?= $emp->seemp_status ?>
                  </span>
                </td>
                <td style="display:flex; gap:4px;">
                  <?= form_open('Employee/viewEmployeeDetails') ?>
                  <input type="hidden" name="empid" value="<?= $emp->seemp_id ?>">
                  <button type="submit" class="btn btn-view btn-action btn-sm"><i class="fas fa-eye"></i></button>
                  <?= form_close() ?>

                  <?= form_open('Employee/RegisterEmployee') ?>
                  <input type="hidden" name="empid" value="<?= $emp->seemp_id ?>">
                  <button type="submit" class="btn btn-edit btn-action btn-sm"><i class="fas fa-edit"></i></button>
                  <?= form_close() ?>

                  <button class="btn btn-sm btn-outline-success"
                    onclick='openIncrementModal("<?= $emp->seemp_id ?>", <?= htmlspecialchars(json_encode($emp->seempd_name), ENT_QUOTES, "UTF-8") ?>, <?= (float)$emp->seempd_salary ?>)'>
                    <i class="fas fa-chart-line"></i> Increments
                  </button>
                </td>
              </tr>
            <?php endforeach; ?>

            <tr id="noDataRow" style="display:none;">
              <td colspan="6" class="text-center py-4">
                <div class="text-muted">
                  <i class="fas fa-search mb-2 fa-2x"></i>
                  <p class="mb-0">No employees found matching your search.</p>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- ═══════════════════════════════════════════════════════════════════════
       LEAVE SUMMARY MODAL
  ════════════════════════════════════════════════════════════════════════ -->
  <div class="modal fade" id="empDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
      <div class="modal-content border-0 shadow-lg" style="border-radius:15px;">
        <div class="modal-header bg-light">
          <h5 class="modal-title fw-bold text-primary">
            <i class="fas fa-user-clock me-2"></i>Employee Leave Summary
          </h5>
          <button type="button" class="btn-close" onclick="closeLeaveModal()" aria-label="Close"></button>
        </div>
        <div class="modal-body p-4">
          <div class="row g-3 mb-4">
            <div class="col-md-6">
              <div class="p-3 bg-primary bg-opacity-10 rounded-3 border-start border-primary border-4">
                <small class="text-muted fw-bold text-uppercase">Employee</small>
                <h5 id="dt_name" class="mb-0 fw-bold"></h5>
                <span id="dt_id" class="badge bg-primary mt-1"></span>
              </div>
            </div>
            <div class="col-md-6">
              <div class="p-3 bg-info bg-opacity-10 rounded-3 border-start border-info border-4 text-center">
                <small class="text-muted fw-bold text-uppercase">Leaves Consumed</small>
                <h4 class="mb-0 fw-bold text-info"><span id="dt_leave_count">0</span> / 20</h4>
              </div>
            </div>
          </div>
          <div class="table-responsive">
            <h6 class="fw-bold mb-3"><i class="fas fa-history me-2"></i>Request History</h6>
            <table class="table table-sm table-hover align-middle border">
              <thead class="table-light">
                <tr>
                  <th>Req ID</th>
                  <th>Reason</th>
                  <th>Summary</th>
                  <th>Applied On</th>
                  <th>Date Range</th>
                  <th>Days</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody id="dt_leave_history"></tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- ═══════════════════════════════════════════════════════════════════════
       INCREMENT MODAL
  ════════════════════════════════════════════════════════════════════════ -->
  <div class="modal fade" id="incrementModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">
            Increment History: <span id="inc_emp_name" class="fw-bold"></span>
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">

          <!-- Apply new increment card -->
          <div class="card bg-light mb-4">
            <div class="card-body">
              <h6 class="card-title mb-3">
                <i class="fas fa-plus-circle text-primary"></i> Apply New Increment
              </h6>

              <!-- Warning: increment already exists this month -->
              <div id="inc_month_warning" class="alert alert-warning d-none mb-3" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Warning:</strong> An increment already exists for this month.
                The server will reject a second one.
              </div>

              <!-- Warning: date selected is in a past month (shown by JS) -->
              <div id="inc_pastmonth_warning" class="alert alert-danger d-none mb-3" role="alert">
                <i class="fas fa-ban me-2"></i>
                <strong>Not Allowed:</strong> The effective date is in a past month.
                Please select a date in <strong><?= date('F Y') ?></strong> or later.
              </div>

              <form action="<?= base_url('Employee/applyIncrement') ?>" method="POST"
                id="incrementForm">
                <input type="hidden" name="<?= $csrf_name ?>" value="<?= $csrf_hash ?>">
                <input type="hidden" name="inc_empid" id="inc_empid">
                <input type="hidden" name="old_salary" id="old_salary">

                <div class="row g-3">
                  <div class="col-md-3">
                    <label class="form-label">Current Salary</label>
                    <input type="text" class="form-control" id="display_old_salary" readonly>
                  </div>
                  <div class="col-md-3">
                    <label class="form-label">Increment (%)</label>
                    <input type="number" step="0.01" min="1" max="100"
                      class="form-control" name="inc_percentage" id="inc_percentage"
                      oninput="calculateIncrement()" required>
                  </div>
                  <div class="col-md-3">
                    <label class="form-label">Increment Amount (₹)</label>
                    <input type="number" step="0.01"
                      class="form-control" name="inc_amount" id="inc_amount"
                      oninput="calculateIncrementFromAmount()">
                  </div>
                  <div class="col-md-3">
                    <label class="form-label">New Salary (₹)</label>
                    <input type="number" step="0.01"
                      class="form-control" name="new_salary" id="new_salary" readonly>
                  </div>

                  <div class="col-md-4">
                    <label class="form-label">Effective Date</label>
                    <!-- min = first day of current month — past months are unselectable -->
                    <input type="date" class="form-control" name="inc_date" id="inc_date"
                      min="<?= $min_inc_date ?>" required>
                    <small id="inc_date_hint" class="text-muted">
                      Earliest allowed: <?= date('d M Y', strtotime($min_inc_date)) ?>
                    </small>
                  </div>
                  <div class="col-md-8">
                    <label class="form-label">Reason / Note</label>
                    <input type="text" class="form-control" name="inc_reason"
                      placeholder="e.g. Annual Appraisal 2026">
                  </div>

                  <div class="col-12 text-end">
                    <button type="submit" class="btn btn-success" id="applyIncrementBtn">
                      Apply Increment
                    </button>
                  </div>
                </div>
              </form>
            </div>
          </div><!-- /card -->

          <!-- Past increments table — now includes Status column -->
          <h6 class="mb-3"><i class="fas fa-history text-secondary"></i> Past Increments</h6>
          <div class="table-responsive">
            <table class="table table-bordered table-striped text-sm">
              <thead class="table-dark">
                <tr>
                  <th>Effective Date</th>
                  <th>Old Salary</th>
                  <th>Increment</th>
                  <th>New Salary</th>
                  <th>Reason</th>
                  <th>Status</th> <!-- NEW column -->
                </tr>
              </thead>
              <tbody id="incrementHistoryBody"></tbody>
            </table>
          </div>

        </div><!-- /modal-body -->
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    // ── Cached DOM refs ───────────────────────────────────────────────────────
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const noDataRow = document.getElementById('noDataRow');
    const tableRows = document.querySelectorAll('#employeesTableBody tr:not(#noDataRow)');
    const incMonthWarning = document.getElementById('inc_month_warning');
    const incPastWarning = document.getElementById('inc_pastmonth_warning');
    const applyIncrementBtn = document.getElementById('applyIncrementBtn');
    const incDateInput = document.getElementById('inc_date');

    // First day of current month as a comparable string "YYYY-MM"
    // Passed from PHP so there is never a client/server clock mismatch.
    const MIN_INC_YM = '<?= $current_ym_str ?>'; // e.g. "2026-04"

    // ── Client-side employee table filter ────────────────────────────────────
    document.addEventListener('DOMContentLoaded', function() {
      function filterEmployees() {
        const term = searchInput.value.toLowerCase().trim();
        const status = statusFilter.value.toLowerCase();
        let visible = 0;

        tableRows.forEach(row => {
          const text = row.textContent.toLowerCase();
          const badge = row.querySelector('.status-badge');
          const rowStatus = badge ? badge.textContent.toLowerCase().trim() : '';
          const match = text.includes(term) && (status === '' || rowStatus === status);

          row.style.display = match ? '' : 'none';
          if (match) visible++;
        });

        noDataRow.style.display = visible === 0 ? '' : 'none';
      }

      searchInput.addEventListener('input', filterEmployees);
      statusFilter.addEventListener('change', filterEmployees);
    });

    // ── Leave Summary Modal ───────────────────────────────────────────────────
    function closeLeaveModal() {
      const m = bootstrap.Modal.getInstance(document.getElementById('empDetailsModal'));
      if (m) m.hide();
    }

    function viewEmployeeQuickDetails(empid, name) {
      document.getElementById('dt_name').innerText = name;
      document.getElementById('dt_id').innerText = empid;
      document.getElementById('dt_leave_history').innerHTML =
        '<tr><td colspan="7" class="text-center">Loading...</td></tr>';

      bootstrap.Modal.getOrCreateInstance(document.getElementById('empDetailsModal')).show();

      // ── BUG FIX: was $.ajax (jQuery) — this page has no jQuery loaded ──
      fetch('<?= base_url("Employee/getEmployeeLeaveSummary/") ?>' + empid)
        .then(r => r.json())
        .then(res => {
          document.getElementById('dt_leave_count').innerText = res.approved_days ?? 0;

          let html = '';
          if (res.history && res.history.length > 0) {
            res.history.forEach(row => {
              const cls = row.seemrq_status === 'approved' ? 'bg-success' :
                row.seemrq_status === 'rejected' ? 'bg-danger' :
                'bg-warning text-dark';
              html += `<tr>
                <td>REQ${row.seemrq_id}</td>
                <td><span class="badge bg-light text-dark border">${row.seemrq_reason}</span></td>
                <td><small>${row.seemrq_summary}</small></td>
                <td>${row.seemrq_reqdate}</td>
                <td>${row.seemrq_fromdate} to ${row.seemrq_todate}</td>
                <td>${row.seemrq_days}</td>
                <td><span class="badge ${cls}">${row.seemrq_status.toUpperCase()}</span></td>
              </tr>`;
            });
          } else {
            html = '<tr><td colspan="7" class="text-center text-muted">No history found.</td></tr>';
          }
          document.getElementById('dt_leave_history').innerHTML = html;
        })
        .catch(() => {
          document.getElementById('dt_leave_history').innerHTML =
            '<tr><td colspan="7" class="text-center text-danger">Error loading data.</td></tr>';
        });
    }

    // ── Increment Modal ───────────────────────────────────────────────────────
    function openIncrementModal(empid, name, currentSalary) {
      // Populate basic fields
      document.getElementById('inc_emp_name').textContent = name;
      document.getElementById('inc_empid').value = empid;
      document.getElementById('old_salary').value = currentSalary;
      document.getElementById('display_old_salary').value = '₹ ' + currentSalary;
      document.getElementById('inc_percentage').value = '';
      document.getElementById('inc_amount').value = '';
      document.getElementById('new_salary').value = currentSalary;
      document.getElementById('inc_date').value = '';

      // Reset warning banners and submit button
      incMonthWarning.classList.add('d-none');
      incPastWarning.classList.add('d-none');
      applyIncrementBtn.disabled = false;

      // Fetch history (controller also auto-applies pending increments inside)
      fetch('<?= base_url("Employee/getIncrementHistoryAjax/") ?>' + empid)
        .then(r => r.json())
        .then(data => {
          const tbody = document.getElementById('incrementHistoryBody');
          tbody.innerHTML = '';

          const now = new Date();
          let hasThisMonth = false;

          if (!Array.isArray(data) || data.length === 0) {
            tbody.innerHTML =
              '<tr><td colspan="6" class="text-center text-muted">No past increments found.</td></tr>';
          } else {
            data.forEach(inc => {
              // inc_effective_date is "YYYY-MM-DD" — parse the YYYY-MM part only
              // to avoid timezone-shift issues that Date() has with ISO strings
              const [incYear, incMonth] = inc.inc_effective_date.split('-').map(Number);
              const isThisMonth = (incYear === now.getFullYear() && incMonth === now.getMonth() + 1);

              if (isThisMonth) hasThisMonth = true;

              // Status badge
              const isPending = (inc.inc_status === 'pending');
              const statusBadge = isPending ?
                '<span class="badge bg-warning text-dark">Pending</span>' :
                '<span class="badge bg-success">Applied</span>';

              // Highlight current-month rows
              const rowClass = isThisMonth ? 'class="table-warning"' : '';

              tbody.innerHTML += `
                <tr ${rowClass}>
                  <td>${inc.inc_effective_date}${isPending ? ' <i class="fas fa-clock text-warning" title="Salary updates on this date"></i>' : ''}</td>
                  <td>₹${parseFloat(inc.old_salary).toFixed(2)}</td>
                  <td class="text-success">+₹${parseFloat(inc.inc_amount).toFixed(2)} (${parseFloat(inc.inc_percentage).toFixed(2)}%)</td>
                  <td><strong>₹${parseFloat(inc.new_salary).toFixed(2)}</strong></td>
                  <td>${inc.inc_reason || '-'}</td>
                  <td>${statusBadge}</td>
                </tr>`;
            });
          }

          // Show banner if this month already has an increment (applied or pending)
          if (hasThisMonth) {
            incMonthWarning.classList.remove('d-none');
            applyIncrementBtn.disabled = true; // prevent accidental double-submit
          }
        })
        .catch(() => {
          document.getElementById('incrementHistoryBody').innerHTML =
            '<tr><td colspan="6" class="text-center text-danger">Error loading history.</td></tr>';
        });

      bootstrap.Modal.getOrCreateInstance(document.getElementById('incrementModal')).show();
    }

    // ── Date validation: block past months on date change ─────────────────────
    incDateInput.addEventListener('change', function() {
      const val = this.value; // "YYYY-MM-DD"
      if (!val) return;

      const selectedYM = val.substring(0, 7); // "YYYY-MM"

      if (selectedYM < MIN_INC_YM) {
        // Past month selected — show error, disable submit
        incPastWarning.classList.remove('d-none');
        applyIncrementBtn.disabled = true;
        this.value = ''; // clear the invalid date
      } else {
        incPastWarning.classList.add('d-none');
        // Re-enable only if there's no "already this month" block
        if (incMonthWarning.classList.contains('d-none')) {
          applyIncrementBtn.disabled = false;
        }
      }
    });

    // ── Salary calculators ────────────────────────────────────────────────────
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
</body>

</html>