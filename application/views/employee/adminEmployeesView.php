<?php
defined('BASEPATH') OR exit('No direct script access allowed');
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
      document.addEventListener("DOMContentLoaded", function() {
          const Toast = Swal.mixin({
              toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true
          });
          Toast.fire({ icon: '<?= $icon ?>', title: '<?= $msg ?>' });
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
            <input name="query" type="text" class="form-control" id="searchInput"
              placeholder="Search employees by name, ID, or designation...">
          </div>

          <div class="col-12 col-md-3 mb-3 mb-md-0">
            <select name="status" class="form-control" id="statusFilter" onchange="this.form.submit()">

              <option value="">All Status</option>

              <option value="active" <?= ($this->input->post('status') == 'active') ? 'selected' : '' ?>>
                Active
              </option>

              <option value="inactive" <?= ($this->input->post('status') == 'inactive') ? 'selected' : '' ?>>
                Inactive
              </option>

            </select>
          </div>

          <div class="col-12 col-md-3">
            <button class="btn btn-view w-100" type="submit">
              <i class="fas fa-search me-2"></i>Search
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
                <td><span class="emp-id"><?= $emp->seemp_id ?></span></td>
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

          </tbody>
        </table>
      </div>
    </div>

    
</body>

</html>