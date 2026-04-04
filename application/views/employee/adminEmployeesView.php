<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <link href="<?= base_url('css/admin/adminEmployeesView.css') ?>" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
  <?php if ($this->session->flashdata('msg')):
    $msg = $this->session->flashdata('msg');
    $icon = (stripos($msg, 'Success') !== false) ? 'success' : 'info';
    ?>
    <script>
      document.addEventListener("DOMContentLoaded", function () {
        const Toast = Swal.mixin({
          toast: true,
          position: 'top-end',
          showConfirmButton: false,
          timer: 3000,
          timerProgressBar: true
        });
        Toast.fire({
          icon: '<?= $icon ?>',
          title: '<?= $msg ?>'
        });
      });
    </script>
  <?php endif; ?>

  <!-- Main Content -->
  <div class="main-content">
    <!-- Employees Section -->
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

            <?php foreach ($employees as $emp) { ?>

              <tr>
                <td>
                  <span class="emp-id"><?= $emp->seemp_id ?></span><br>
                  <a href="javascript:void(0)" class="text-primary small text-decoration-none"
                    onclick="viewEmployeeQuickDetails('<?= $emp->seemp_id ?>', '<?= addslashes($emp->seempd_name) ?>')">
                    <i class="fas fa-info-circle"></i> view details
                  </a>
                </td>
                <td>
                  <div>
                    <strong><?= $emp->seempd_name ?></strong><br>
                  </div>
                </td>
                <td><strong><?= $emp->seempd_designation ?></strong></td>
                <td><?= $emp->seemp_email ?></td>
                <td><span
                    class="status-badge <?= $emp->seemp_status == 'active' ? 'text-bg-primary' : 'text-bg-warning' ?>"><?= $emp->seemp_status ?></span>
                </td>
                <td style="display:flex">

                  <?= form_open('Employee/viewEmployeeDetails') ?>
                  <input type="hidden" name="empid" value="<?= $emp->seemp_id ?>" />

                  <button type='submit' class="btn btn-view btn-action btn-sm">
                    <i class="fas fa-eye"></i>
                  </button>
                  <?= form_close() ?>

                  <?= form_open('Employee/RegisterEmployee') ?>
                  <input type="hidden" name="empid" value="<?= $emp->seemp_id ?>">
                  <button type="submit" class="btn btn-edit btn-action btn-sm">
                    <i class="fas fa-edit"></i>
                  </button>
                  <?= form_close() ?>
                </td>
              </tr>

            <?php } ?>
            <tr id="noDataRow" style="display: none;">
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
  <div class="modal fade" id="empDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
      <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
        <div class="modal-header bg-light">
          <h5 class="modal-title fw-bold text-primary"><i class="fas fa-user-clock me-2"></i>Employee Leave Summary</h5>
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
              <tbody id="dt_leave_history">
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Employee Details Modal -->

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    // Function to explicitly close the modal
    function closeLeaveModal() {
      var modalElement = document.getElementById('empDetailsModal');
      var myModal = bootstrap.Modal.getInstance(modalElement);
      if (myModal) {
        myModal.hide();
      }
    }

    // Function to open and load the modal
    function viewEmployeeQuickDetails(empid, name) {
      document.getElementById('dt_name').innerText = name;
      document.getElementById('dt_id').innerText = empid;
      document.getElementById('dt_leave_history').innerHTML = '<tr><td colspan="7" class="text-center">Loading...</td></tr>';

      // Use getOrCreateInstance to prevent duplicates
      var modalElement = document.getElementById('empDetailsModal');
      var myModal = bootstrap.Modal.getOrCreateInstance(modalElement);
      myModal.show();

      $.ajax({
        url: '<?= base_url("Employee/getEmployeeLeaveSummary/") ?>' + empid,
        type: 'GET',
        dataType: 'json',
        success: function (res) {
          document.getElementById('dt_leave_count').innerText = res.approved_days;

          let html = '';
          if (res.history && res.history.length > 0) {
            res.history.forEach(row => {
              let statusClass = row.seemrq_status === 'approved' ? 'bg-success' :
                (row.seemrq_status === 'rejected' ? 'bg-danger' : 'bg-warning text-dark');
              html += `
                        <tr>
                            <td>REQ${row.seemrq_id}</td>
                            <td><span class="badge bg-light text-dark border">${row.seemrq_reason}</span></td>
                            <td><small>${row.seemrq_summary}</small></td>
                            <td>${row.seemrq_reqdate}</td>
                            <td>${row.seemrq_fromdate} to ${row.seemrq_todate}</td>
                            <td>${row.seemrq_days}</td>
                            <td><span class="badge ${statusClass}">${row.seemrq_status.toUpperCase()}</span></td>
                        </tr>`;
            });
          } else {
            html = '<tr><td colspan="7" class="text-center text-muted">No history found.</td></tr>';
          }
          document.getElementById('dt_leave_history').innerHTML = html;
        },
        error: function () {
          document.getElementById('dt_leave_history').innerHTML = '<tr><td colspan="7" class="text-center text-danger">Error loading data.</td></tr>';
        }
      });
    }
    document.addEventListener('DOMContentLoaded', function () {
      const searchInput = document.getElementById('searchInput');
      const statusFilter = document.getElementById('statusFilter');
      const noDataRow = document.getElementById('noDataRow');

      // Select only rows that are NOT the "No Data" row
      const tableRows = document.querySelectorAll('#employeesTableBody tr:not(#noDataRow)');

      function filterEmployees() {
        const searchTerm = searchInput.value.toLowerCase().trim();
        const selectedStatus = statusFilter.value.toLowerCase();
        let visibleCount = 0;

        tableRows.forEach(row => {
          const rowText = row.textContent.toLowerCase();
          const statusBadge = row.querySelector('.status-badge');
          const rowStatus = statusBadge ? statusBadge.textContent.toLowerCase().trim() : '';

          const matchesSearch = rowText.includes(searchTerm);
          const matchesStatus = (selectedStatus === '' || rowStatus === selectedStatus);

          if (matchesSearch && matchesStatus) {
            row.style.display = '';
            visibleCount++; // Increment if a match is found
          } else {
            row.style.display = 'none';
          }
        });

        // Toggle the "No Data" row based on the count
        if (visibleCount === 0) {
          noDataRow.style.display = '';
        } else {
          noDataRow.style.display = 'none';
        }
      }

      searchInput.addEventListener('input', filterEmployees);
      statusFilter.addEventListener('change', filterEmployees);
    });
  </script>
</body>

</html>