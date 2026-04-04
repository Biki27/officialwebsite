<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ESS Portal - Login | Suropriyo Enterprise</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

  <link href="<?= base_url('css/employee/employeeLoginView.css') ?>" rel="stylesheet">

</head>

<body>

  <div class="login-container">
    <div class="login-card text-center">
      <div class="logo mb-3">
        <i class="fas fa-building"></i>
      </div>

      <h2>Suropriyo Enterprise</h2>
      <p class="mb-4">Employee / HR / Admin Login</p>

      <?= form_open('Employee/Login') ?>

      <div class="mb-3 text-start">
        <label for="username" class="form-label"><i class="fas fa-user"></i> Username</label>
        <input type="email" class="form-control" id="username" name="username" placeholder="Enter your email"
         value="<?= isset($old_username) ? $old_username : ''; ?>" required> 
      </div>

      <div class="mb-3 text-start">
        <label for="password" class="form-label"><i class="fas fa-lock"></i> Password</label>
        <div class="input-group">
          <input type="password" class="form-control" id="password" name="password"
            placeholder="Enter password" required>
          <button class="btn btn-light" type="button" id="togglePassword">
            <i class="fas fa-eye" id="eyeIcon"></i>
          </button>
        </div>

        <!-- CSRF -->
        <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>"
          value="<?= $this->security->get_csrf_hash(); ?>" />
      </div>

      <div class="mb-4 text-end">
        <a href="#" class="text-decoration-none small"
          onclick="alert('Forgot Password? Contact IT support at support@suropriyo.com')">Forgot Password?</a>
      </div>

      <button type="submit" class="btn btn-login w-100">
        <i class="fas fa-sign-in-alt"></i> Login
      </button>
      <!-- color white -->
      <div class="my-3 text-white">OR</div>

      <a href="<?= base_url('Employee/google_login') ?>" class="custom-google-btn">
        <img src="https://www.gstatic.com/images/branding/product/1x/gsa_512dp.png" width="20" class="me-2">
        Login with Google
      </a>
    
      <?= form_close() ?>
    </div>

    <div class="footer">
      &copy; 2021 Suropriyo Enterprise
    </div>
  </div>

 <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
     document.addEventListener('DOMContentLoaded', function() {
        <?php if ($this->session->flashdata('login_error')): ?>
            Swal.fire({
                icon: 'error',
                title: 'Login Failed',
                text: '<?= $this->session->flashdata('login_error'); ?>',
                timer: 5000,
                timerProgressBar: true,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        <?php endif; ?>

        <?php if (isset($error)): ?>
            Swal.fire({
                icon: 'error',
                title: 'Invalid Credentials',
                text: '<?= $error ?>',
                timer: 5000,
                timerProgressBar: true,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        <?php endif; ?>
    });

     
    const togglePassword = document.querySelector('#togglePassword');
    const password = document.querySelector('#password');
    const eyeIcon = document.querySelector('#eyeIcon');

    togglePassword.addEventListener('click', function() {
      const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
      password.setAttribute('type', type);
      eyeIcon.classList.toggle('fa-eye');
      eyeIcon.classList.toggle('fa-eye-slash');
    });
</script>

</body>

</html>