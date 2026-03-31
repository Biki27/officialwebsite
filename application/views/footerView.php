<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<link rel="stylesheet" href="<?= base_url(); ?>css/Footer.css">
<footer class="text-center text-lg-start text-muted">
    <!-- <section class="footer5 d-flex justify-content-center justify-content-lg-between p-4 border-bottom">
        <div class="me-5 d-none d-lg-block">
            <span class="c-name">Get connected with us on social networks:</span>
        </div>
        <div>
            <a href="#" class="me-4 text-reset text-decoration-none"><i class="fab fa-facebook-f"></i></a>
            <a href="#" class="me-4 text-reset text-decoration-none"><i class="fab fa-linkedin-in"></i></a>
            <a href="#" class="me-4 text-reset text-decoration-none"><i class="fab fa-instagram"></i></a>
        </div>
    </section> -->
    <section class="footer2">
        <div class="text-center text-md-start mt-5">
            <div class="row mt-3">
                <div class="col-md-3 col-lg-4 col-xl-3 mx-auto mb-4">
                    <h6 class="text-uppercase fw-bold mb-4">
                        <i class="fas fa-robot me-3" style="color: #0ea5e9;"></i>Suropriyo Enterprise
                    </h6>
                    <p class="c-name">software solutions that scale with your business</p>
                </div>
                <div class="col-md-2 col-lg-2 col-xl-2 mx-auto mb-4">
                    <h6 class="text-uppercase fw-bold mb-4">Explore</h6>
                    <p><a href="<?= base_url('AboutUs') ?>" class="text-reset text-decoration-none">About Us</a>
                    </p>
                    <p><a href="<?= base_url('Services') ?>" class="text-reset text-decoration-none">Services</a></p>
                    <p><a href="<?= base_url('Careers') ?>" class="text-reset text-decoration-none">Careers</a>
                    </p>
                </div>
                <div class="col-md-3 col-lg-2 col-xl-2 mx-auto mb-4">
                    <h6 class="text-uppercase fw-bold mb-4">Solutions</h6>
                    <!-- <p><a href="#" class="text-reset text-decoration-none">Healthcare Systems</a></p> -->
                    <p><a href="<?= base_url() ?>AboutUs#solutions" class="text-reset text-decoration-none">Tea
                            Heaven</a></p>
                    <p><a href="<?= base_url() ?>AboutUs#solutions" class="text-reset text-decoration-none">E-commerce website</a></p>
                </div>
                <div class="col-md-4 col-lg-3 col-xl-3 mx-auto mb-md-0 mb-4">
                    <h6 class="text-uppercase fw-bold mb-4">Contact</h6>

                    <p>
                        <i class="fas fa-envelope me-3"></i>
                        <a href="#" onclick="openMail(event)" class="text-reset text-decoration-none">
                            suropriyoenterprise@gmail.com
                        </a>
                    </p>

                    <p>
                        <i class="fas fa-globe me-3"></i>
                        <a href="https://suropriyo.in" target="_blank" class="text-reset text-decoration-none">
                            suropriyo.in
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </section>
    <div class="text-center p-4" style="background-color: rgba(0, 0, 0, 0.05);">
        © 2021  <a class="text-reset fw-bold text-decoration-none" href="#">Suropriyo Enterprise</a> | All
        Rights Reserved |
        <span class="ai-badge" style="font-size: 0.8rem; padding: 6px 18px;">🛠️ Built by Engineers</span>
    </div>
    <script>
        function openMail(e) {
            e.preventDefault();

            var isMobile = /iPhone|Android|iPad|iPod/i.test(navigator.userAgent);

            if (isMobile) {
                // Open Gmail app / default mail app
                window.location.href = "mailto:suropriyoenterprise@gmail.com";
            } else {
                // Open Gmail web in browser
                window.open("https://mail.google.com/mail/?view=cm&fs=1&to=suropriyoenterprise@gmail.com", "_blank");
            }
        }
    </script>
</footer>