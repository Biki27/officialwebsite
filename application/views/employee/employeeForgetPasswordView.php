<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ESS Portal - Forgot Password | Supropriyo Enterprise</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  
    <link href="<?= base_url('css/employee/employeeForgetPasswordView.css') ?>" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="card p-4">
                    <div class="text-center mb-4">
                        <div class="logo"><i class="fas fa-building"></i></div>
                        <h2 class="mt-3 text-dark">Supropriyo Enterprise</h2>
                        <p class="text-muted">Employee Self-Service Portal</p>
                    </div>
                    
                    <form id="forgotPasswordForm" novalidate>
                        <div class="mb-3">
                            <label for="employeeId" class="form-label fw-semibold">
                                <i class="fas fa-id-badge me-2"></i>Employee ID
                            </label>
                            <input type="text" class="form-control" id="employeeId" 
                                   placeholder="e.g., EMP001" required
                                   aria-describedby="employeeIdHelp">
                            <div id="employeeIdHelp" class="form-text">
                                Enter your Employee ID (format: EMPXXX).
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold">
                                <i class="fas fa-envelope me-2"></i>Email ID
                            </label>
                            <input type="email" class="form-control" id="email" 
                                   placeholder="e.g., john.doe@supropriyo.com" required>
                            <div class="form-text">
                                Enter your registered company email address.
                            </div>
                        </div>

                        <div class="mb-3 text-end">
                            <a href="login.html" class="text-decoration-none">
                                <i class="fas fa-arrow-left me-1"></i>Back to Login
                            </a>
                        </div>

                        <button type="submit" class="btn btn-primary w-100" id="resetBtn">
                            <i class="fas fa-paper-plane me-2"></i>Send Reset Link
                        </button>
                    </form>

                    <div id="errorMessage" class="alert alert-danger mt-3" style="display: none;" role="alert"></div>
                    <div id="successMessage" class="alert alert-success mt-3" style="display: none;" role="alert"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="footer">
        &copy; 2026 Suropriyo Enterprise. All rights reserved.
    </div>

    <!-- <script>
        document.getElementById('forgotPasswordForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const employeeId = document.getElementById('employeeId').value.trim();
            const email = document.getElementById('email').value.trim();
            const errorMessage = document.getElementById('errorMessage');
            const successMessage = document.getElementById('successMessage');
            const resetBtn = document.getElementById('resetBtn');
            
            // Hide previous messages
            errorMessage.style.display = 'none';
            successMessage.style.display = 'none';
            
            // Validation
            if (!employeeId || !email) {
                showError('Please fill in both Employee ID and Email ID.');
                return;
            }
            
            if (!/^(EMP|emp)\d{3,4}$/.test(employeeId)) {
                showError('Employee ID must be in format: EMP001, EMP0123, etc.');
                return;
            }
            
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                showError('Please enter a valid email address.');
                return;
            }
            
            // Simulate API call
            resetBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Sending...';
            resetBtn.disabled = true;
            
            setTimeout(() => {
                successMessage.innerHTML = `
                    <i class="fas fa-check-circle me-2"></i>
                    Reset link sent successfully to <strong>${email}</strong>!<br>
                    Please check your inbox (and spam folder).
                `;
                successMessage.style.display = 'block';
                resetBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Send Reset Link';
                resetBtn.disabled = false;
                document.getElementById('forgotPasswordForm').reset();
            }, 2000);
        });
        
        function showError(message) {
            const errorMessage = document.getElementById('errorMessage');
            errorMessage.textContent = message;
            errorMessage.style.display = 'block';
        }
    </script> -->
</body>
</html>