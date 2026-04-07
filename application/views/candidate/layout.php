<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? $title : 'Candidate Portal | Suropriyo Enterprise' ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">

    <style>
        body {
            background-color: #f8fafc;
            font-family: 'Inter', sans-serif;
            color: #1e293b;
        }

        .navbar {
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
        }

        .nav-link {
            font-weight: 600;
            color: #64748b !important;
            transition: color 0.2s;
        }

        .nav-link:hover {
            color: #2563eb !important;
        }

        /* Responsive Container Fix */
        .main-content {
            padding: 1.5rem 0.5rem;
            /* Tight padding for mobile */
        }

        @media (min-width: 768px) {
            .main-content {
                padding: 3rem 0;
                /* Spacious for desktop */
            }
        }

        /* Global Alert Styling */
        .alert {
            border: none;
            border-radius: 12px;
            font-weight: 500;
        }

        body.modal-open {
            overflow: hidden;
        }

        /* Ensure the navbar doesn't overlap modal headers on small screens */
        @media (max-width: 576px) {
            .modal-dialog {
                margin: 0.5rem;
            }
        }
    </style>
</head>

<body>
    <!-- in login page i dont want the navbar and footer -->
    <?php if (!isset($hide_navbar)): ?>
        <nav class="navbar navbar-expand-lg py-3 sticky-top">
            <div class="container">
                <a class="navbar-brand fw-800 text-primary d-flex align-items-center" href="<?= base_url('Careers') ?>">
                    <i class="fas fa-building me-2"></i>
                    <span class="d-none d-sm-inline">Suropriyo Enterprise</span>
                    <span class="d-inline d-sm-none">Suropriyo Enterprise</span>
                </a>

                <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#candidateNav">
                    <i class="fas fa-bars"></i>
                </button>

                <div class="collapse navbar-collapse" id="candidateNav">
                    <ul class="navbar-nav ms-auto align-items-lg-center mt-3 mt-lg-0">
                        <li class="nav-item">
                            <a href="<?= base_url('Careers/Jobs') ?>" class="nav-link px-3">Browse Jobs</a>
                        </li>
                        <?php if ($this->session->userdata('candidate_logged_in')): ?>
                            <li class="nav-item">
                                <a href="<?= base_url('Candidate/dashboard') ?>" class="nav-link px-3">My Dashboard</a>
                            </li>
                            <li class="nav-item mt-2 mt-lg-0 ms-lg-2">
                                <a href="<?= base_url('Candidate/logout') ?>" class="btn btn-outline-danger btn-sm w-100 w-lg-auto px-4 rounded-pill">Logout</a>
                            </li>
                        <?php else: ?>
                            <li class="nav-item mt-2 mt-lg-0 ms-lg-2">
                                <a href="<?= base_url('Candidate/login') ?>" class="btn btn-primary btn-sm w-100 w-lg-auto px-4 rounded-pill">Login</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>
    <?php endif; ?>

    <main class="container main-content min-vh-100">
        <?php if ($this->session->flashdata('error')): ?>
            <div class="alert alert-danger shadow-sm mb-4"><?= $this->session->flashdata('error'); ?></div>
        <?php endif; ?>
        <?php if ($this->session->flashdata('success')): ?>
            <div class="alert alert-success shadow-sm mb-4"><?= $this->session->flashdata('success'); ?></div>
        <?php endif; ?>

        <?= $content ?>
    </main>
    <?php if (!isset($hide_navbar)): ?>
        <footer class="bg-white border-top py-4 mt-5">
            <div class="container text-center">
                <p class="text-muted small mb-0">&copy; 2021 Suropriyo Enterprise. Candidate Portal.</p>
            </div>
        </footer>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>