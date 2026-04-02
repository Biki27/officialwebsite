<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<link rel="stylesheet" href="<?= base_url(); ?>css/Career.css">

<section class="hero-banner">
    <video class="video-background" autoplay muted loop playsinline preload="auto">
        <source src="<?= base_url(); ?>videos/carere.mp4" type="video/mp4">
        <img src="career-fallback-image.jpg" alt="Career Banner" style="width:100%;height:100%;object-fit:cover;">
    </video>

    <div class="video-overlay"></div>

    <div class="container position-relative z-3">
        <div class="row">
            <div class="col-lg-8 col-md-10 content-left">
                <h1 class="hero-title text-white">CAREERS</h1>
                <p class="hero-subtitle text-white-50">Seize the future.</p>

                <div class="cta-container d-flex flex-wrap gap-3 align-items-center">
                    <div class="d-flex align-items-center">
                        <a href="<?= base_url() ?>Careers/Jobs" class="cta-btn text-white">Explore Opportunities</a>
                        <div>
                            <a href="<?= base_url() ?>Careers/Jobs"
                                class="fas arrow-btn fa-arrow-right text-white text-decoration-none"></a>
                        </div>
                    </div>

                    <a href="<?= base_url() ?>Candidate/login" class="btn btn-outline-light px-4 py-2 fw-bold"
                        style="border-radius: 30px; backdrop-filter: blur(5px);">
                        <i class="fas fa-user-circle me-2"></i> Track Application
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="careers" class="py-5 bg-light">
    <div class="container py-4">
        <h2 class="section-title mb-5">Career Opportunities</h2>
        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 border-0 shadow-sm custom-hover-card rounded-4">
                    <div class="card-body text-center p-5">
                        <i class="fas fa-code display-4 text-primary mb-4"></i>
                        <h4 class="card-title fw-bold">Software Development</h4>
                        <p class="card-text text-secondary">Build next-generation applications using cutting-edge
                            technologies and modern development practices.</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 border-0 shadow-sm custom-hover-card rounded-4">
                    <div class="card-body text-center p-5">
                        <i class="fas fa-database display-4 text-primary mb-4"></i>
                        <h4 class="card-title fw-bold">Database Management </h4>
                        <p class="card-text text-secondary">Manage and optimize database performance for high-traffic
                            applications.</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 border-0 shadow-sm custom-hover-card rounded-4">
                    <div class="card-body text-center p-5">
                        <i class="fas fa-cloud display-4 text-primary mb-4"></i>
                        <h4 class="card-title fw-bold">Cloud & Server Management</h4>
                        <p class="card-text text-secondary">Optimize cloud and server infrastructure for optimal
                            performance.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="careers-opportunities" class="py-5 bg-light">
    <div class="container py-5">

        <div class="row mb-5 justify-content-center text-center">
            <div class="col-lg-8">
                <span class="premium-badge mb-3 d-inline-block">Join The Team</span>
                <h2 class="fw-bold text-dark display-6 mb-0" style="letter-spacing: -1px;">
                    Why SUROPRIYO ENTERPRISE?
                </h2>
            </div>
        </div>

        <div class="row g-5 align-items-start">

            <div class="col-lg-4 col-xl-3">
                <div class="premium-tab-menu sticky-lg-top" style="top: 100px;">
                    <div class="tab-item active" data-tab="software">
                        <i class="fas fa-laptop-code"></i> Software Development
                    </div>
                    <div class="tab-item" data-tab="data">
                        <i class="fas fa-mobile-alt"></i> App Development
                    </div>
                    <div class="tab-item" data-tab="cloud">
                        <i class="fas fa-shield-alt"></i> Secure Maintenance
                    </div>
                    <div class="tab-item" data-tab="cyber">
                        <i class="fas fa-server"></i> Hosting & Infra
                    </div>
                </div>
            </div>

            <div class="col-lg-8 col-xl-9">

                <div class="career-detail active" id="software">
                    <div class="row align-items-center g-5">
                        <div class="col-md-5">
                            <div class="premium-img-wrapper">
                                <img src="<?= base_url(); ?>assets/software.png" alt="Software Development">
                            </div>
                        </div>
                        <div class="col-md-7">
                            <h3 class="fw-bold mb-3 text-dark">Software Development</h3>
                            <p class="lead mb-4 text-secondary fs-6">Build next-generation applications using
                                cutting-edge technologies and modern development practices.</p>

                            <div class="premium-skill-list mb-5">
                                <span class="skill-badge"><i class="fas fa-check-circle"></i> HTML, CSS, JS</span>
                                <span class="skill-badge"><i class="fas fa-check-circle"></i> Laravel &
                                    CodeIgniter</span>
                                <span class="skill-badge"><i class="fas fa-check-circle"></i> Test-Driven Dev</span>
                                <span class="skill-badge"><i class="fas fa-check-circle"></i> Agile Methodology</span>
                            </div>

                            <a href='<?= base_url() ?>Careers/Jobs' class="btn btn-primary btn-premium">Apply Now
                                &rarr;</a>
                        </div>
                    </div>
                </div>

                <div class="career-detail" id="data">
                    <div class="row align-items-center g-5">
                        <div class="col-md-5">
                            <div class="premium-img-wrapper">
                                <img src="<?= base_url(); ?>assets/appdevlopent.png" alt="App Development">
                            </div>
                        </div>
                        <div class="col-md-7">
                            <h3 class="fw-bold mb-3 text-dark">App Development</h3>
                            <p class="lead mb-4 text-secondary fs-6">Design and develop user-centric mobile and web
                                applications with a strong focus on performance and usability.</p>
                            <a href='<?= base_url() ?>Careers/Jobs' class="btn btn-primary btn-premium">Apply Now
                                &rarr;</a>
                        </div>
                    </div>
                </div>

                <div class="career-detail" id="cloud">
                    <div class="row align-items-center g-5">
                        <div class="col-md-5">
                            <div class="premium-img-wrapper">
                                <img src="<?= base_url(); ?>assets/secure.png" alt="Secure Maintenance">
                            </div>
                        </div>
                        <div class="col-md-7">
                            <h3 class="fw-bold mb-3 text-dark">Secure Maintenance</h3>
                            <p class="lead mb-4 text-secondary fs-6">Gain hands-on experience in maintaining, securing,
                                and optimizing live production systems.</p>
                            <a href='<?= base_url() ?>Careers/Jobs' class="btn btn-primary btn-premium">Apply Now
                                &rarr;</a>
                        </div>
                    </div>
                </div>

                <div class="career-detail" id="cyber">
                    <div class="row align-items-center g-5">
                        <div class="col-md-5">
                            <div class="premium-img-wrapper">
                                <img src="<?= base_url(); ?>assets/hosting.png" alt="Hosting">
                            </div>
                        </div>
                        <div class="col-md-7">
                            <h3 class="fw-bold mb-3 text-dark">Hosting & Infrastructure</h3>
                            <p class="lead mb-4 text-secondary fs-6">Work with modern hosting and infrastructure
                                technologies to ensure high availability and reliability.</p>
                            <a href='<?= base_url() ?>Careers/Jobs' class="btn btn-primary btn-premium">Apply Now
                                &rarr;</a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

<section id="ceo-vision" class="py-5 bg-white">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">

                <div class="visionary-card">
                    <i class="fas fa-quote-right giant-bg-quote"></i>

                    <div class="visionary-content">
                        <div class="visionary-avatar-wrapper">
                            <div class="avatar-ring">
                                <img src="<?= base_url(); ?>assets/ceo.png" alt="Mr. Surajit Das">
                            </div>
                            <div class="avatar-glow"></div>
                        </div>

                        <div class="visionary-text">
                            <h3 class="quote-text">
                                "At Suropriyo Enterprise, we don't just build technology &mdash; we build futures. Join
                                us to create transformative solutions that redefine industries."
                            </h3>

                            <div class="visionary-author">
                                <div class="author-details">
                                    <h5 class="author-name">Mr. Surajit Das</h5>
                                    <span class="author-title">FOUNDER</span>
                                </div>
                                <!-- <div class="author-accent-line"></div> -->
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.querySelectorAll('.tab-item').forEach(tab => {
    tab.addEventListener('click', function() {
        document.querySelectorAll('.tab-item').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.career-detail').forEach(c => c.classList.remove('active'));
        this.classList.add('active');
        document.getElementById(this.dataset.tab).classList.add('active');
    });
});
</script>