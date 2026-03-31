<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invalid Access</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body class="d-flex align-items-center justify-content-center vh-100 bg-body-tertiary">

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-sm-10 col-md-8 col-lg-5">

                <div class="card shadow-lg border-0 rounded-4 p-4 p-sm-5 text-center bg-body">
                    <div class="card-body p-0">

                        <i class="bi bi-exclamation-circle text-warning display-1 mb-4 d-inline-block"></i>

                        <h1 class="h2 fw-bold mb-3">Invalid Access</h1>

                        <p class="text-secondary mb-5 fs-5">Session out.</p>

                        <a href="<?= site_url('Employee/Login') ?>"
                            class="btn btn-primary btn-lg px-5 rounded-pill fw-medium">
                            Return To Login
                        </a>

                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>