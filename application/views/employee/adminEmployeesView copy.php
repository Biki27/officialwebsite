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
                <td class="status-cell">
                  <div class="enterprise-switch">
                    <input type="checkbox"
                      id="status_<?= $emp->seemp_id ?>"
                      class="status-toggle-input"
                      data-joining-date="<?= $emp->seempd_joiningdate ?>"
                      data-term-date="<?= $emp->seempd_termination_date ?>"
                      <?= $emp->seemp_status == 'active' ? 'checked' : '' ?>
                      onchange="handleStatusChange('<?= $emp->seemp_id ?>', this)">
                    <label for="status_<?= $emp->seemp_id ?>" class="status-slider">
                      <span class="status-text active-text">Active</span>
                      <span class="status-text inactive-text">Inactive</span>
                    </label>
                  </div>
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
          <ul class="nav nav-pills mb-4 pb-2 border-bottom" id="pills-tab" role="tablist">
            <li class="nav-item" role="presentation">
              <button class="nav-link active fw-bold" id="pills-leave-tab" data-bs-toggle="pill" data-bs-target="#pills-leave" type="button" role="tab">
                <i class="fas fa-plane-departure me-2"></i>Leaves & Time Off
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link fw-bold" id="pills-lifecycle-tab" data-bs-toggle="pill" data-bs-target="#pills-lifecycle" type="button" role="tab">
                <i class="fas fa-stream me-2"></i>Lifecycle Timeline
              </button>
            </li>
          </ul>

          <div class="tab-content" id="pills-tabContent">

            <div class="tab-pane fade show active" id="pills-leave" role="tabpanel">
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

            <div class="tab-pane fade" id="pills-lifecycle" role="tabpanel">
              <div id="dt_lifecycle_container" class="py-3 px-2">
                <div class="text-center text-muted"><i class="fas fa-spinner fa-spin"></i> Loading timeline...</div>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Termination Modal -->
  <div class="modal fade" id="terminationModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content custom-enterprise-modal">
        <div class="modal-header border-0 pb-0">
          <h5 class="modal-title fw-bold text-dark">
            <i class="fas fa-user-slash me-2 text-danger"></i>Deactivate Profile
          </h5>
          <button type="button" class="btn-close shadow-none" onclick="cancelTermination()"></button>
        </div>
        <div class="modal-body pt-3">
          <p class="text-muted small mb-4">
            You are deactivating <span class="fw-bold text-dark" id="term_emp_name"></span>.
            This will move the employee to the inactive list.
          </p>
          <input type="hidden" id="term_empid">
          <input type="hidden" id="term_joining_date">

          <div class="mb-3">
            <label class="form-label small fw-bold text-uppercase text-muted">Termination Date</label>
            <input type="date" id="modal_term_date" class="form-control enterprise-input" value="<?= date('Y-m-d') ?>">
          </div>

          <div class="mb-3">
            <label class="form-label small fw-bold text-uppercase text-muted">Reason for Termination</label>
            <textarea id="modal_term_reason" class="form-control enterprise-input" rows="4" placeholder="Briefly explain the reason for deactivation..."></textarea>
          </div>
        </div>
        <div class="modal-footer border-0 pt-0 pb-4 justify-content-center">
          <button type="button" class="btn btn-light px-4 fw-bold" onclick="cancelTermination()">Keep Active</button>
          <button type="button" class="btn btn-danger px-4 fw-bold shadow-sm" onclick="confirmTermination()">Confirm Deactivation</button>
        </div>
      </div>
    </div>
  </div>
  <!-- Rejoin Modal -->
  <div class="modal fade" id="rejoinModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content custom-enterprise-modal">
        <div class="modal-header border-0 pb-0">
          <h5 class="modal-title fw-bold text-dark">
            <i class="fas fa-user-check me-2 text-success"></i>Reactivate Profile
          </h5>
          <button type="button" class="btn-close shadow-none" onclick="cancelRejoin()"></button>
        </div>
        <div class="modal-body pt-3">
          <p class="text-muted small mb-4">
            You are reactivating <span class="fw-bold text-dark" id="rejoin_emp_name"></span>.
            Please specify their official rejoining date for payroll purposes.
          </p>
          <input type="hidden" id="rejoin_empid">

          <div class="mb-3">
            <label class="form-label small fw-bold text-uppercase text-muted">Rejoining Date</label>
            <input type="date" id="modal_rejoin_date" class="form-control enterprise-input" value="<?= date('Y-m-d') ?>">
          </div>

          <div class="alert alert-info border-0 bg-info bg-opacity-10 small">
            <i class="fas fa-info-circle me-1"></i> Termination history for this employee will be cleared to allow active status.
          </div>
        </div>
        <div class="modal-footer border-0 pt-0 pb-4 justify-content-center">
          <button type="button" class="btn btn-light px-4 fw-bold" onclick="cancelRejoin()">Cancel</button>
          <button type="button" class="btn btn-success px-4 fw-bold shadow-sm" onclick="confirmRejoin()">Confirm Reactivation</button>
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

    // ── Client-side employee table filter ────────────────────────────────────
    document.addEventListener('DOMContentLoaded', function() {
      function filterEmployees() {
        const term = searchInput.value.toLowerCase().trim();
        const filterStatus = statusFilter.value.toLowerCase(); // 'active', 'inactive', or ''
        let visible = 0;

        tableRows.forEach(row => {
          const text = row.textContent.toLowerCase();

          // Find the toggle inside this specific row
          const toggle = row.querySelector('.status-toggle-input');
          const isToggleActive = toggle.checked; // true if checked, false if not

          // Map toggle state to status strings
          const currentStatus = isToggleActive ? 'active' : 'inactive';

          // Check if row matches search text AND matches the dropdown filter
          const matchesSearch = text.includes(term);
          const matchesStatus = (filterStatus === '' || currentStatus === filterStatus);

          if (matchesSearch && matchesStatus) {
            row.style.display = '';
            visible++;
          } else {
            row.style.display = 'none';
          }
        });

        noDataRow.style.display = (visible === 0) ? '' : 'none';
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
      document.getElementById('dt_leave_history').innerHTML = '<tr><td colspan="7" class="text-center">Loading...</td></tr>';
      document.getElementById('dt_lifecycle_container').innerHTML = '<div class="text-center text-muted"><i class="fas fa-spinner fa-spin"></i> Loading...</div>';

      // Ensure the first tab is active when opening the modal
      const firstTab = new bootstrap.Tab(document.getElementById('pills-leave-tab'));
      firstTab.show();

      bootstrap.Modal.getOrCreateInstance(document.getElementById('empDetailsModal')).show();

      fetch('<?= base_url("Employee/getEmployeeLeaveSummary/") ?>' + empid)
        .then(r => r.json())
        .then(res => {
          document.getElementById('dt_leave_count').innerText = res.approved_days ?? 0;

          // --- 1. RENDER LEAVE HISTORY ---
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
            html = '<tr><td colspan="7" class="text-center text-muted">No leave history found.</td></tr>';
          }
          document.getElementById('dt_leave_history').innerHTML = html;

          // --- 2. RENDER LIFECYCLE TIMELINE ---
          let timelineHtml = '<div class="emp-timeline">';
          if (res.lifecycle && res.lifecycle.length > 0) {
            res.lifecycle.forEach(event => {
              let badgeColor = event.status_action === 'terminated' ? 'danger' : 'success';
              let icon = event.status_action === 'terminated' ? 'fa-user-slash' : 'fa-user-check';
              let actionTitle = event.status_action.charAt(0).toUpperCase() + event.status_action.slice(1);

              let reasonHtml = event.reason ? `<div class="timeline-reason"><i class="fas fa-info-circle me-1"></i> ${event.reason}</div>` : '';

              // Format Date nicely (e.g., 10 Apr 2026)
              let dateObj = new Date(event.effective_date);
              let formattedDate = dateObj.toLocaleDateString('en-GB', {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
              });

              timelineHtml += `
                    <div class="timeline-item ${event.status_action}">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="badge bg-${badgeColor} bg-opacity-10 text-${badgeColor} border border-${badgeColor}">
                                <i class="fas ${icon} me-1"></i> ${actionTitle}
                            </span>
                            <span class="timeline-date">${formattedDate}</span>
                        </div>
                        ${reasonHtml}
                    </div>`;
            });
          } else {
            timelineHtml += '<p class="text-muted small">No lifecycle history recorded for this employee.</p>';
          }
          timelineHtml += '</div>';
          document.getElementById('dt_lifecycle_container').innerHTML = timelineHtml;

        })
        .catch(() => {
          document.getElementById('dt_leave_history').innerHTML = '<tr><td colspan="7" class="text-center text-danger">Error loading data.</td></tr>';
          document.getElementById('dt_lifecycle_container').innerHTML = '<div class="text-center text-danger">Error loading timeline.</div>';
        });
    }
    // status toggle logic with confirmation modal for deactivation
    let currentToggle = null;

    function handleStatusChange(empid, toggle) {
      const isChecked = toggle.checked;
      currentToggle = toggle;

      if (!isChecked) {
        // 1. ACTIVE TO INACTIVE: Open Termination Modal
        document.getElementById('term_empid').value = empid;
        document.getElementById('term_emp_name').innerText = empid;
        document.getElementById('term_joining_date').value = toggle.dataset.joiningDate || '';

        const termDateInput = document.getElementById('modal_term_date');
        if (toggle.dataset.joiningDate) {
          termDateInput.min = toggle.dataset.joiningDate;
        } else {
          termDateInput.removeAttribute('min');
        }
        new bootstrap.Modal(document.getElementById('terminationModal')).show();
      } else {
        // 2. INACTIVE TO ACTIVE: Open Rejoin Modal
        document.getElementById('rejoin_empid').value = empid;
        document.getElementById('rejoin_emp_name').innerText = empid;

        const dateInput = document.getElementById('modal_rejoin_date');
        const termDate = toggle.dataset.termDate;

        // Dynamically lock the calendar so HR cannot pick an illegal date
        if (termDate && termDate !== '0000-00-00') {
          // If they were terminated on the 5th, the earliest they can rejoin is the 6th
          let minRejoin = new Date(termDate);
          minRejoin.setDate(minRejoin.getDate() + 1);
          dateInput.min = minRejoin.toISOString().split('T')[0];
          dateInput.value = dateInput.min;
        } else {
          dateInput.value = new Date().toISOString().split('T')[0];
          dateInput.removeAttribute('min');
        }

        new bootstrap.Modal(document.getElementById('rejoinModal')).show();
      }
    }

    // --- TERMINATION LOGIC ---
    function cancelTermination() {
      if (currentToggle) currentToggle.checked = true; // Revert toggle
      bootstrap.Modal.getInstance(document.getElementById('terminationModal')).hide();
    }

    function confirmTermination() {
      const empid = document.getElementById('term_empid').value;
      const date = document.getElementById('modal_term_date').value;
      const reason = document.getElementById('modal_term_reason').value;
      const joiningDate = document.getElementById('term_joining_date').value;

      if (!date || !reason) {
        Swal.fire({
          icon: 'warning',
          title: 'Required Fields',
          text: 'Please provide both a termination date and a reason.',
          confirmButtonColor: '#461bb9'
        });
        return;
      }
      if (joiningDate && new Date(date) < new Date(joiningDate)) {
        Swal.fire({
          icon: 'error',
          title: 'Invalid Date',
          text: 'Termination date cannot be earlier than the joining date.',
          confirmButtonColor: '#461bb9'
        });
        return;
      }

      updateStatusInDB(empid, 'inactive', date, reason, null);
      bootstrap.Modal.getInstance(document.getElementById('terminationModal')).hide();
    }

    // --- REJOIN LOGIC ---
    function cancelRejoin() {
      if (currentToggle) currentToggle.checked = false; // Revert toggle
      bootstrap.Modal.getInstance(document.getElementById('rejoinModal')).hide();
    }

    function confirmRejoin() {
      const empid = document.getElementById('rejoin_empid').value;
      const date = document.getElementById('modal_rejoin_date').value;

      if (!date) {
        Swal.fire({
          icon: 'warning',
          title: 'Required',
          text: 'Please provide a rejoining date.',
          confirmButtonColor: '#461bb9'
        });
        return;
      }

      updateStatusInDB(empid, 'active', null, null, date);
      bootstrap.Modal.getInstance(document.getElementById('rejoinModal')).hide();
    }

    // --- MASTER AJAX CALL ---
    function updateStatusInDB(empid, status, termDate, termReason, rejoinDate) {
      let formData = new FormData();
      formData.append('empid', empid);
      formData.append('status', status);

      if (termDate) formData.append('term_date', termDate);
      if (termReason) formData.append('term_reason', termReason);
      if (rejoinDate) formData.append('rejoin_date', rejoinDate);

      formData.append('<?= $this->security->get_csrf_token_name(); ?>', '<?= $this->security->get_csrf_hash(); ?>');

      fetch('<?= base_url("Employee/updateEmployeeStatusAjax") ?>', {
          method: 'POST',
          body: formData
        })
        .then(r => r.json())
        .then(res => {
          if (res.success) {
            Swal.fire('Updated', 'Employee status changed to ' + status, 'success');
          } else {
            Swal.fire('Error', res.message, 'error');
            if (currentToggle) currentToggle.checked = !currentToggle.checked;
          }
        });
    }
  </script>
</body>

</html>