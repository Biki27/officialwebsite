<style>
    .register-wrapper {
        min-height: 90vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .register-card {
        border: none;
        border-radius: 20px;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.05);
    }

    .form-control-custom {
        padding: 12px 15px;
        background-color: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 10px;
        font-size: 15px;
    }

    .form-control-custom:focus {
        background-color: #fff;
        border-color: #0d6efd;
        box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.1);
    }

    @media (max-width: 576px) {
        .register-wrapper {
            padding: 1rem;
            align-items: flex-start;
        }
        .register-card {
            border-radius: 15px;
        }
    }
</style>

<div class="container register-wrapper">
    <div class="row justify-content-center w-100 m-0">
        <div class="col-12 col-sm-11 col-md-8 col-lg-6 col-xl-5">
            
            <div class="card register-card">
                <div class="card-body p-4 p-md-5">
                    
                    <div class="text-center mb-4">
                        <h2 class="fw-800 text-dark mb-1">Create Account</h2>
                        <p class="text-muted">Start your application at Suropriyo Enterprise</p>
                    </div>

                    <?php if(validation_errors()): ?>
                        <div class="alert alert-danger border-0 small rounded-3 mb-4">
                            <i class="fas fa-exclamation-circle me-2"></i> <?= validation_errors(); ?>
                        </div>
                    <?php endif; ?>

                    <?= form_open('Candidate/register'); ?>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label fw-bold small text-muted">EMAIL ADDRESS <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control form-control-custom" 
                               id="email" placeholder="you@example.com" value="<?= set_value('email'); ?>" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label fw-bold small text-muted">PASSWORD <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control form-control-custom" 
                                   id="password" placeholder="Min 6 characters" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="passconf" class="form-label fw-bold small text-muted">CONFIRM <span class="text-danger">*</span></label>
                            <input type="password" name="passconf" class="form-control form-control-custom" 
                                   id="passconf" placeholder="Re-type password" required>
                        </div>
                    </div>

                    <div class="form-check mb-4 small">
                        <input class="form-check-input" type="checkbox" value="" id="terms" required>
                        <label class="form-check-label text-muted" for="terms">
                            I agree to the <a href="#" class="text-primary text-decoration-none">Terms of Service</a> and <a href="#" class="text-primary text-decoration-none">Privacy Policy</a>.
                        </label>
                    </div>

                    <div class="d-grid mb-4">
                        <button type="submit" class="btn btn-primary btn-lg fw-bold py-3" style="border-radius: 12px;">
                            Create My Account
                        </button>
                    </div>

                    <?= form_close(); ?>

                    <div class="position-relative mb-4">
                        <hr class="text-muted opacity-25">
                        <span class="position-absolute top-50 start-50 translate-middle bg-white px-3 text-muted small fw-bold">OR</span>
                    </div>

                    <div class="d-grid">
                        <a href="<?= base_url('Candidate/google_login'); ?>" 
                           class="btn btn-outline-dark d-flex align-items-center justify-content-center py-3" 
                           style="border-radius: 12px; border-color: #dee2e6; font-weight: 600;">
                            <img src="https://fonts.gstatic.com/s/i/productlogos/googleg/v6/24px.svg" alt="Google" style="width: 20px; margin-right: 12px;">
                            Sign up with Google
                        </a>
                    </div>

                </div>

                <div class="card-footer bg-light border-0 py-3 text-center rounded-bottom-4">
                    <span class="text-muted small">Already have an account?</span> 
                    <a href="<?= base_url('Candidate/login'); ?>" class="fw-bold text-primary text-decoration-none ms-1 small">Log in here</a>
                </div>
            </div>

        </div>
    </div>
</div>