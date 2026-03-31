<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard | Suropriyo Enterprise</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= base_url() ?>css/hr/hrEmployeeDirectoryView.css">
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
              toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true
          });
          Toast.fire({ icon: '<?= $isError ? "error" : "success" ?>', title: <?= json_encode($msg) ?> });
      });
  </script>
  <?php endif; ?>

  <div class="main-content">
        <div id="employees" class="section active">
      <h2 class="text-white mb-4">Employee Management</h2>
      
      <?= form_open('Employee/viewEmployee') ?>
      <div class="search-box">
        <div class="row align-items-center">
          <div class="col-md-6">
            <input name="query" type="text" class="form-control" id="searchInput" placeholder="Search employees by name, ID, or designation...">
          </div>
          <div class="col-md-3">
            <select name="status" class="form-control" id="statusFilter">
              <option value="">All Status</option>
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
            </select>
          </div>
          <div class="col-md-3">
            <button class="btn btn-view w-100" type="submit">
              <i ></i>Search
            </button>
          </div>
        </div>
      </div>

      <?= form_close() ?>

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
                  <strong><?= $emp->seempd_name  ?></strong><br>
                </div>
              </td>
              <td><strong><?= $emp->seempd_designation  ?></strong></td>
              <td><?= $emp->seemp_email?></td>
              <td><span class="status-badge <?= $emp->seemp_status == 'active' ? 'text-bg-primary' : 'text-bg-warning' ?>"><?= $emp->seemp_status?></span></td>
              <td style="display:flex">

                <?= form_open('Employee/viewEmployeeDetails')?>
                <input type="hidden" name="empid" value="<?= $emp->seemp_id ?>"/>
                
                <button type='submit' class="btn btn-view btn-action btn-sm" >
                  <i class="fas fa-eye"></i>
                </button>
                <?= form_close()?>

                <?= form_open('Employee/RegisterEmployee')?>
                <input type="hidden" name="empid" value="<?= $emp->seemp_id ?>">
                <button type="submit" class="btn btn-edit btn-action btn-sm">
                  <i class="fas fa-edit"></i>
                </button>
                <?= form_close()?>
              </td>
            </tr>

          <?php  } ?>
          
          </tbody>
        </table>
      </div>
    </div>
  </body>
</html>