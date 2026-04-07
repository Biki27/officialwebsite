<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Search | Suropriyo Enterprise</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url(); ?>css/jobsView.css">
</head>

<body>
    <section class="job-search-section py-5 bg-light">
        <div class="container">
            <div class="row mb-4">
                <div class="col-12">
                    <a href="<?= base_url('Careers') ?>"
                        class="text-decoration-none text-secondary fw-semibold back-btn d-inline-flex align-items-center">
                        <i class="fas fa-arrow-left me-2"></i> Back to Careers
                    </a>
                </div>
            </div>

            <?= form_open("Jobs/SearchJob") ?>
            <!-- <div class="row justify-content-center mb-5">
                <div class="col-lg-10 col-xl-8">
                    <div class="premium-search-bar">
                        <div class="input-group input-group-lg">
                            <span class="input-group-text bg-transparent border-0 ps-4 pe-2">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                            <input type="text" class="form-control border-0 shadow-none search-main"
                                placeholder="Search by Job Title, Keywords..." name="search" id="search"
                                value="<?= isset($search_val) ? htmlspecialchars($search_val) : '' ?>">
                            <button class="btn btn-primary search-btn-main" type="submit">Search Jobs</button>
                        </div>
                    </div>
                </div>
            </div> -->
            <?= form_close() ?>

            <!-- <?= form_open("Jobs/FilterJob") ?> -->
             <?= form_open("Jobs/FilterJob", ['method' => 'get']) ?>
            <div class="row g-3 justify-content-center">
                <div class="col-lg col-md-4 col-sm-6">
                    <div class="premium-filter-group">
                        <label class="form-label">Job Title</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text"><i class="fas fa-briefcase"></i></span>
                            <input id="jtitle" name="jtitle" type="text" class="form-control filter-input"
                                placeholder="Any Job Title"
                                value="<?= isset($filter_vals['jtitle']) ? htmlspecialchars($filter_vals['jtitle']) : '' ?>">
                        </div>
                    </div>
                </div>

                <div class="col-lg col-md-4 col-sm-6">
                    <div class="premium-filter-group">
                        <label class="form-label">Location</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                            <select name="jlocation" id="jlocation" class="form-select filter-input">
                                <option value="">All Locations</option>
                                <option value="Kolkata" <?= (isset($filter_vals['jlocation']) && $filter_vals['jlocation'] == 'Kolkata') ? 'selected' : '' ?>>Kolkata</option>
                                <option value="Howrah" <?= (isset($filter_vals['jlocation']) && $filter_vals['jlocation'] == 'Howrah') ? 'selected' : '' ?>>Howrah</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-lg col-md-4 col-sm-6">
                    <div class="premium-filter-group">
                        <label class="form-label">Skills</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text"><i class="fas fa-layer-group"></i></span>
                            <select id="jskills" name="jskills" class="form-select filter-input">
                                <option value="">All Skills</option>
                                <?php $skills = ['python', 'java', 'html', 'php', 'react'];
                                foreach ($skills as $s): ?>
                                    <option value="<?= $s ?>" <?= (isset($filter_vals['jskills']) && $filter_vals['jskills'] == $s) ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-lg col-md-4 col-sm-6">
                    <div class="premium-filter-group">
                        <label class="form-label">Experience</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text"><i class="fas fa-clock"></i></span>
                            <select id="jexp" name="jexp" class="form-select filter-input">
                                <option value="">All Experience</option>
                                <option value="1" <?= (isset($filter_vals['jexp']) && $filter_vals['jexp'] == '1') ? 'selected' : '' ?>>Fresher (0-1 Year)</option>
                                <option value="3" <?= (isset($filter_vals['jexp']) && $filter_vals['jexp'] == '3') ? 'selected' : '' ?>>Less than 3 Years</option>
                                <option value="7" <?= (isset($filter_vals['jexp']) && $filter_vals['jexp'] == '7') ? 'selected' : '' ?>>Less than 7 Years</option>
                                <option value="7plus" <?= (isset($filter_vals['jexp']) && $filter_vals['jexp'] == '7plus') ? 'selected' : '' ?>>7+ Years</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-lg col-md-4 col-sm-6">
                    <div
                        class="premium-filter-group h-100 d-flex align-items-end p-0 bg-transparent border-0 shadow-none">
                        <button class="btn btn-outline-primary w-100 filter-btn h-100 py-2" type="submit">
                            <i class="fas fa-sliders-h me-2"></i> Filter
                        </button>
                    </div>
                </div>
            </div>
            <?= form_close() ?>
        </div>
    </section>

    <section class="jobs-list-section py-5 bg-white" id="results-section">
        <div class="container">
            <div class="row mb-5">
                <div class="col-lg-8">
                    <h3 class="mb-0 fw-bold text-dark d-flex align-items-center gap-3">
                        <i class="fas fa-briefcase text-primary"></i>
                        <?= count($res) ?> Jobs Found
                    </h3>
                </div>
            </div>

            <div class="row g-4" id="jobsContainer">
                <?php if (!empty($res)): ?>
                    <?php foreach ($res as $job): ?>
                        <div class="col-lg-6 col-xl-4 mb-4">
                            <div class="premium-job-card h-100 d-flex flex-column">

                                <div class="job-header p-4 border-bottom">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div class="pe-2">
                                            <h4 class="job-title mb-1"><?= $job->sejob_jobtitle ?></h4>
                                            <div class="job-company text-primary fw-bold">Suropriyo Enterprise</div>
                                            <div class="job-location text-muted">
                                                <i class="fas fa-map-marker-alt me-1"></i>
                                                <?= $job->sejob_address ?>
                                            </div>
                                        </div>
                                        <span class="badge rounded-pill <?php
                                        if ($job->sejob_urgency == 'new')
                                            echo "bg-success";
                                        elseif ($job->sejob_urgency == 'hot')
                                            echo "bg-warning text-dark";
                                        elseif ($job->sejob_urgency == 'urgent')
                                            echo "bg-danger";
                                        ?>"><?= ucfirst($job->sejob_urgency) ?></span>
                                    </div>
                                </div>

                                <div class="job-body p-4 d-flex flex-column flex-grow-1">
                                    <div class="job-badges-container mb-4">
                                        <span class="premium-badge-soft">
                                            <i class="fas fa-code text-primary"></i> <?= $job->sejob_skills ?>
                                        </span>
                                        <span class="premium-badge-soft">
                                            <span class="text-success fw-bold">upto</span>
                                            <?= number_format($job->sejob_salary) ?>/mo
                                        </span>
                                        <span class="premium-badge-soft">
                                            <span class="text-success fw-bold">upto</span>
                                            <?= number_format($job->sejob_experience) ?> yrs experience
                                        </span>

                                        <span class="premium-badge-soft">
                                            <i class="fas fa-clock text-info"></i> <?= ucfirst($job->sejob_workinghours) ?>
                                        </span>
                                    </div>

                                    <div class="job-description mb-4 flex-grow-1">
                                        <?php
                                        $desc = trim($job->sejob_desc);
                                        $uid = $job->sejob_id;
                                        if (strlen($desc) > 120):
                                            $short_desc = substr($desc, 0, 115);
                                            ?>
                                            <div class="collapse show multi-collapse-<?= $uid ?>" id="shortDesc<?= $uid ?>">
                                                <span><?= $short_desc ?>...</span>
                                                <a data-bs-toggle="collapse" data-bs-target=".multi-collapse-<?= $uid ?>"
                                                    href="javascript:void(0);" class="read-more-link ms-1">Read More</a>
                                            </div>

                                            <div class="collapse multi-collapse-<?= $uid ?>" id="fullDesc<?= $uid ?>">
                                                <div class="full-desc-box">
                                                    <?= $desc ?>
                                                </div>
                                                <a data-bs-toggle="collapse" data-bs-target=".multi-collapse-<?= $uid ?>"
                                                    href="javascript:void(0);" class="read-more-link mt-2 d-inline-block">Show
                                                    Less</a>
                                            </div>
                                        <?php else: ?>
                                            <div><?= $desc ?></div>
                                        <?php endif; ?>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center mt-auto pt-3 border-top">
                                        <div class="job-meta-bottom text-muted">
                                            <i class="far fa-calendar-alt me-1"></i>
                                            <small><?= date('M d, Y', strtotime($job->sejob_dateofpost)) ?></small>
                                        </div>
                                        <?php if (strtolower($job->sejob_state) == 'active'): ?>
                                            <a href="<?= base_url() ?>Jobs/Apply/<?= $job->sejob_id ?>"
                                                class="btn btn-primary btn-apply px-4">
                                                Apply Now
                                            </a>
                                        <?php else: ?>
                                            <button class="btn btn-secondary px-4" disabled
                                                title="This position is currently closed">
                                                Closed
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center py-5">
                        <div class="empty-state-card">
                            <i class="fas fa-search-minus empty-icon mb-4"></i>
                            <h3 class="fw-bold text-dark mb-2">No jobs found</h3>
                            <p class="text-secondary mb-4">Try adjusting your filters or keywords.</p>
                            <a href="<?= base_url('Jobs') ?>" class="btn btn-outline-primary btn-apply">
                                <i class="fas fa-sync-alt me-2"></i>Reset Filters
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <?php if (isset($scrollToResults) && $scrollToResults): ?>
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                const resultsSection = document.getElementById('results-section');
                if (resultsSection) {
                    resultsSection.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        </script>
    <?php endif; ?>
</body>

</html>
<!-- search value , all filter  shouldnot be changed after submission   -->