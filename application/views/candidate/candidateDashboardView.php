<style>
/* ===== PREMIUM GLOBAL ===== */
body {
    background: #F1F5F9;
    font-family: 'Inter', sans-serif;
}

/* Glass effect card */
.premium-card {
    border-radius: 20px;
    background: #ffffff;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.06);
    transition: 0.3s ease;
}

.premium-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
}

/* Header section */
.welcome-box {
    padding: 25px;
    border-radius: 20px;
    background: linear-gradient(135deg, #2563eb, #06b6d4);
    color: white;
}

/* Badge upgrade */
.badge-premium {
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    border-radius: 50px;
}

/* Table upgrade */
.table thead {
    background: #f8fafc;
}

.table tbody tr {
    transition: 0.2s;
}

.table tbody tr:hover {
    background: #f1f5f9;
}

/* Status badges */
.badge {
    border-radius: 50px !important;
    padding: 6px 12px;
    font-size: 12px;
}

/* Responsive fix */
@media (max-width: 768px) {
    .welcome-box {
        text-align: center;
    }
}
</style>
<div class="row mb-4">
    <div class="col-12">
        <div class="welcome-box d-flex flex-column flex-md-row justify-content-between align-items-center">

            <div>
                <h2 class="fw-bold mb-1">Welcome back 👋</h2>
                <p class="mb-0 opacity-75">Manage your profile and track your job applications</p>
            </div>

            <div class="mt-3 mt-md-0">
                <span class="badge badge-premium px-3 py-2">
                    <i class="fas fa-envelope me-2"></i>
                    <?= htmlspecialchars($profile->email); ?>
                </span>
            </div>

        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class=" premium-card p-3">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="mb-0 fw-bold"><i class="fas fa-briefcase text-primary me-2"></i> My Applications</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class=" table align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="px-4 py-3 text-muted small text-uppercase">Position</th>
                                <th class="py-3 text-muted small text-uppercase">Date</th>
                                <th class="py-3 text-muted small text-uppercase">Status</th>
                                <th class="py-3 text-muted small text-uppercase">Interview</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($applications)): ?>
                            <?php foreach($applications as $app): ?>
                            <tr>
                                <td class="px-4 py-3 fw-bold text-dark">
                                    <?= htmlspecialchars($app->sejob_jobtitle ?? 'Unknown Position'); ?>
                                </td>
                                <td class="py-3 text-muted">
                                    <?= date('M j, Y', strtotime($app->sejoba_atime)); ?>
                                </td>
                                <td class="py-3">
                                    <?php 
                                                $status = strtolower($app->sejoba_state);
                                                $badge = 'bg-secondary';
                                                if($status == 'applied' || $status == 'pending') $badge = 'bg-warning text-dark';
                                                if($status == 'interviewing' || $status == 'interview_scheduled') $badge = 'bg-info text-dark';
                                                if($status == 'selected' || $status == 'hired') $badge = 'bg-success';
                                                if($status == 'rejected') $badge = 'bg-danger';
                                            ?>
                                    <span class="badge <?= $badge; ?> px-2 py-1" style="text-transform: capitalize;">
                                        <?= str_replace('_', ' ', $status); ?>
                                    </span>
                                </td>
                                <td class="py-3">
                                    <?php if(!empty($app->sejoba_interview_date)): ?>
                                    <small class="d-block text-primary fw-bold">
                                        <i class="far fa-calendar-alt"></i>
                                        <?= date('M j, Y', strtotime($app->sejoba_interview_date)); ?>
                                        at <?= date('g:i A', strtotime($app->sejoba_interview_time)); ?>
                                    </small>
                                    <small class="text-muted"><i class="fas fa-map-marker-alt"></i>
                                        <?= htmlspecialchars($app->sejoba_interview_location); ?></small>
                                    <?php else: ?>
                                    <span class="text-muted small">Not scheduled yet</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center py-5">
                                    <div class="text-muted mb-3"><i class="fas fa-folder-open fs-1"></i></div>
                                    <h5 class="fw-bold">No applications found</h5>
                                    <p class="text-muted mb-3">You haven't applied to any open positions yet.</p>
                                    <a href="<?= base_url('Careers/Jobs'); ?>" class="btn btn-primary btn-sm">Browse
                                        Open Jobs</a>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php if(isset($auto_apply_job)): ?>
<div class="modal fade" id="autoApplyModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg border-0 rounded-4">

            <div class="modal-header bg-primary text-white border-0 rounded-top-4 p-4">
                <div>
                    <h4 class="modal-title fw-bold mb-1">Apply for:
                        <?= htmlspecialchars($auto_apply_job->sejob_jobtitle); ?></h4>
                    <p class="mb-0 opacity-75 small">Complete your profile to submit this application.</p>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>

            <div class="modal-body p-4 p-md-5 bg-light">

                <?= form_open_multipart('Jobs/ApplyStatus/'.$auto_apply_job->sejob_id); ?>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="fw-bold text-muted small text-uppercase">Full Name *</label>
                        <input type="text" class="form-control" name="full_name"
                            value="<?= isset($profile->full_name) ? $profile->full_name : ''; ?>"
                            <?= !empty($profile->full_name) ?   'required': 'required'; ?>>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="fw-bold text-muted small text-uppercase">Phone Number *</label>
                        <input type="tel" class="form-control" name="phone" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="fw-bold text-muted small text-uppercase">Years of Experience *</label>
                        <input type="number" class="form-control" name="experience" min="0" step="0.5" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="fw-bold text-muted small text-uppercase">Expected Salary (Monthly) *</label>
                        <div class="input-group">
                            <span class="input-group-text">₹</span>
                            <input type="number" class="form-control" name="expected_salary" required>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="fw-bold text-muted small text-uppercase">Resume Upload (PDF ONLY) *</label>
                    <input type="file" class="form-control form-control-lg" name="resume" accept=".pdf" required>
                </div>

                <div class="mb-4">
                    <label class="fw-bold text-muted small text-uppercase">Cover Letter</label>
                    <textarea class="form-control" name="coverletter" rows="3" required></textarea>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-5 fw-bold">Submit Application <i
                            class="fas fa-paper-plane ms-2"></i></button>
                </div>

                <?= form_close(); ?>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    var myModal = new bootstrap.Modal(document.getElementById('autoApplyModal'));
    myModal.show();
});
</script>
<?php endif; ?>