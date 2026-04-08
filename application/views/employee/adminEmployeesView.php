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

                  <!-- <button class="btn btn-sm btn-outline-success"
                    onclick='openIncrementModal("<?= $emp->seemp_id ?>", <?= htmlspecialchars(json_encode($emp->seempd_name), ENT_QUOTES, "UTF-8") ?>, <?= (float)$emp->seempd_salary ?>)'>
                    <i class="fas fa-chart-line"></i> Increments
                  </button> -->
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

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    // ── Cached DOM refs ───────────────────────────────────────────────────────
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const noDataRow = document.getElementById('noDataRow');
    const tableRows = document.querySelectorAll('#employeesTableBody tr:not(#noDataRow)');

    // const incMonthWarning = document.getElementById('inc_month_warning');
    // const incPastWarning = document.getElementById('inc_pastmonth_warning');
    // const applyIncrementBtn = document.getElementById('applyIncrementBtn');
    // const incDateInput = document.getElementById('inc_date');

    // First day of current month as a comparable string "YYYY-MM"
    // Passed from PHP so there is never a client/server clock mismatch.
    // const MIN_INC_YM = '<?= $current_ym_str ?>'; // e.g. "2026-04"

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

    
  </script>
</body>

</html>