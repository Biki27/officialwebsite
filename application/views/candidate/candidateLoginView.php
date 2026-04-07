<style>
    .login-wrapper {
        min-height: 90vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .login-card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        transition: transform 0.3s ease;
    }

    @media (max-width: 576px) {
        .login-wrapper {
            align-items: flex-start;
            padding-top: 2rem;
        }

        .login-card {
            border-radius: 0;
            box-shadow: none;
            background: transparent;
        }

        .btn-lg-mobile {
            padding: 14px;
            font-size: 16px;
        }
    }
</style>

<div class="container login-wrapper">
    <div class="row justify-content-center w-100 m-0">
        <div class="col-12 col-sm-10 col-md-7 col-lg-5 col-xl-4 p-0">

            <div class="card login-card bg-white">
                <div class="card-body p-4 p-md-5">

                    <div class="text-center mb-4">
                        <a href="<?= base_url(); ?>">
                            <img src="<?= base_url(); ?>assets/logo.png" alt="Logo" class="mb-3 shadow-sm"
                                style="height:65px; width:65px; border-radius: 50%; object-fit: cover;">
                        </a>
                        <h3 class="fw-bold text-dark">Candidate Login</h3>
                        <p class="text-muted small">Access your Suropriyo Enterprise dashboard</p>
                    </div>

                    <?php echo form_open('Candidate/login'); ?>

                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold small text-uppercase text-muted">Email
                            Address</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i
                                    class="fas fa-envelope text-muted small"></i></span>
                            
                            <?php 
                                // BUG FIX: Retrieve flashdata email so user doesn't re-type it
                                $email_val = set_value('email');
                                if (empty($email_val) && $this->session->flashdata('old_email')) {
                                    $email_val = $this->session->flashdata('old_email');
                                }
                            ?>
                            <input type="email" name="email" class="form-control bg-light border-start-0 shadow-none"
                                id="email" placeholder="email@example.com" value="<?= htmlspecialchars($email_val); ?>" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="password"
                            class="form-label fw-semibold small text-uppercase text-muted">Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i
                                    class="fas fa-lock text-muted small"></i></span>
                            <input type="password" name="password"
                                class="form-control bg-light border-start-0 shadow-none" id="password"
                                placeholder="••••••••" required>
                        </div>
                    </div>

                    <div class="d-grid mb-3">
                        <button type="submit" name="login_submit" value="Login"
                            class="btn btn-primary btn-lg-mobile fw-bold py-3 py-md-2" style="border-radius: 12px;">
                            Sign In
                        </button>
                    </div>

                    <?php echo form_close(); ?>

                    <div class="position-relative my-4">
                        <hr class="text-muted opacity-25">
                        <span
                            class="position-absolute top-50 start-50 translate-middle bg-white px-3 text-muted small fw-bold">OR</span>
                    </div>

                    <div class="d-grid">
                        <a href="<?php echo base_url('Candidate/google_login'); ?>"
                            class="btn btn-outline-dark btn-lg-mobile d-flex align-items-center justify-content-center py-3 py-md-2"
                            style="border-radius: 12px; border-color: #dee2e6;">
                            <img src="https://fonts.gstatic.com/s/i/productlogos/googleg/v6/24px.svg" alt="Google"
                                style="width: 18px; margin-right: 10px;">
                            Continue with Google
                        </a>
                    </div>

                </div>

                <div class="card-footer bg-light border-0 py-4 text-center">
                    <p class="mb-0 small text-muted">
                        Don't have an account?
                        <a href="<?php echo base_url('Candidate/register'); ?>"
                            class="fw-bold text-primary text-decoration-none ms-1">Register Now</a>
                    </p>
                </div>
            </div>

            <div class="text-center mt-4 d-none d-md-block">
                <a href="<?= base_url('Careers/Jobs') ?>" class="text-muted small text-decoration-none">
                    <i class="fas fa-arrow-left me-1"></i> Back to Open Jobs
                </a>
            </div>

        </div>
    </div>
</div>