<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <link href="<?= base_url('css/admin/adminEmployeesView.css') ?>" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" defer></script>
</head>
<body>

  <div class="main-content">
    <div id="employees" class="section active">
      <h2 class="text-black mb-1">My Branch Employees</h2>
      <p class="text-black-50 mb-4">Viewing staff for the <?= ucfirst(strtolower($this->session->userdata('branch'))) ?> branch.</p>

      <div class="search-box">
        <div class="row align-items-center">
          <div class="col-12 col-md-8 mb-3 mb-md-0">
            <input type="text" class="form-control" id="searchInput" placeholder="Search branch employees by name, ID, or designation...">
          </div>
          <div class="col-12 col-md-4">
            <button class="btn btn-view w-100" type="button" id="searchBtn">
              <i class="fas fa-search me-2"></i>Filter Results
            </button>
          </div>
        </div>
      </div>

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
            <?php if (!empty($employees)): ?>
              <?php foreach ($employees as $emp): ?>
                <tr>
                  <td>
                    <span class="emp-id"><?= $emp->seemp_id ?></span>
                  </td>
                  <td><strong><?= $emp->seempd_name ?></strong></td>
                  <td><strong><?= $emp->seempd_designation ?></strong></td>
                  <td><?= $emp->seemp_email ?></td>
                  <td class="status-cell">
                      <?php if($emp->seemp_status == 'active'): ?>
                          <span class="badge bg-success bg-opacity-10 text-success border border-success px-3 py-2 rounded-pill">Active</span>
                      <?php else: ?>
                          <span class="badge bg-danger bg-opacity-10 text-danger border border-danger px-3 py-2 rounded-pill">Inactive</span>
                      <?php endif; ?>
                  </td>
                 <td>
                    <div class="d-flex gap-2">
                        <?= form_open('Manager/viewEmployeeDetails', ['class' => 'm-0']) ?>
                            <input type="hidden" name="empid" value="<?= $emp->seemp_id ?>">
                            <button type="submit" class="btn btn-view btn-action btn-sm"><i class="fas fa-eye"></i></button>
                        <?= form_close() ?>

                        <a href="<?= base_url('Manager/editEmployee/' . $emp->seemp_id) ?>" class="btn btn-outline-primary btn-sm" style="border-radius: 8px; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-edit"></i>
                        </a>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr id="noDataRow">
                <td colspan="6" class="text-center py-4">
                  <div class="text-muted">
                    <i class="fas fa-users mb-2 fa-2x"></i>
                    <p class="mb-0">No employees found in your branch.</p>
                  </div>
                </td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Simple client-side filter
    document.addEventListener('DOMContentLoaded', function() {
      const searchInput = document.getElementById('searchInput');
      const tableRows = document.querySelectorAll('#employeesTableBody tr:not(#noDataRow)');

      searchInput.addEventListener('input', function() {
        const term = searchInput.value.toLowerCase().trim();
        tableRows.forEach(row => {
          const text = row.textContent.toLowerCase();
          if (text.includes(term)) {
            row.style.display = '';
          } else {
            row.style.display = 'none';
          }
        });
      });
    });
  </script>
</body>
</html>