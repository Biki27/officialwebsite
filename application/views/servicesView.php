<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>



<link rel="stylesheet" href="<?= base_url(); ?>css/services.css">



<section class="premium-hero">
    <div class="container">
        <div class="row align-items-center g-5">
            <!-- Remove margin bottom from hero content -->
            <div class="col-lg-6 hero-content text-center text-lg-start mb-0">
                <span class="premium-hero-badge mb-3">Our Expertise</span>
                <h1 class="hero-title">Amazing Services Await</h1>
                <p class="hero-subtitle">Discover premium services designed to transform your business. Our expert
                    team is ready to help you scale.</p>
                <!-- <a href="<?= base_url(); ?>Services#ourServices" class="btn-premium mt-3">Explore Services</a> -->
            </div>
            <div class="col-lg-6">
                <div class="hero-image-wrapper">
                    <img src="<?= base_url(); ?>assets/services.png" alt="Services Overview"
                        class="img-fluid rounded-4 shadow-lg">
                    <div class="hero-glow"></div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="container mt-n5 position-relative" style="z-index: 10;">
    <div class="premium-quote-card text-center p-4 p-md-5 mx-auto shadow-lg">
        <h2 class="text-primary fw-bold mb-3">What We Do</h2>
        <p class="lead text-secondary mb-0" style="font-size: 1.1rem; line-height: 1.8; font-weight: bold">
            We provide comprehensive digital solutions that empower organizations to achieve sustainable growth and
            operational excellence. Our services are designed to align with business objectives, leveraging
            technology, strategy, and innovation to deliver measurable results.
        </p>
    </div>
</section>

<section id="services-grid" class="services-section py-5 bg-light-soft">
    <div class="container py-5" id="ourServices">
        <div class="text-center mb-5">
            <h2 class="section-title">Our Services</h2>
        </div>

        <div class="services-grid">
            <?php foreach($allserv as $srv){ ?>
            <div class="premium-flip-card">
                <div class="flip-card-inner">

                    <div class="flip-card-front">
                        <div class="service-icon-wrapper">
                            <img src="<?= base_url(); ?>assets/web.png" alt="<?= $srv->sesrv_name ?>">
                        </div>
                        <h4 class="service-title"><?= $srv->sesrv_name ?></h4>
                        <span class="service-badge"><?= $srv->sesrv_type ?></span>
                        <p class="service-desc"><?= $srv->sesrv_desc ?></p>
                        <div class="flip-hint"><i class="fas fa-sync-alt me-2"></i> Hover for Details</div>
                    </div>

                    <div class="flip-card-back d-flex flex-column">
                        <h4 class="text-white fw-bold mb-4 border-bottom border-light pb-3"><?= $srv->sesrv_name ?>
                        </h4>
                        <ul class="service-features text-start flex-grow-1">
                            <?php foreach (json_decode($srv->sesrv_lines) as $item) { ?>
                            <li><i class="fas fa-check-circle text-success me-2 bg-white rounded-circle"></i>
                                <?= $item[0] ?></li>
                            <?php } ?>
                        </ul>

                        <a href="<?= base_url() ?>Services/Searchservice"
                            class="btn btn-light text-primary fw-bold rounded-pill w-100 mt-3">Learn More &rarr;</a>
                    </div>

                </div>
            </div>
            <?php }?>
        </div>

        <div class="text-center mt-5 pt-4">
            <a href="<?= base_url() ?>Services/Searchservice" class="btn-premium-outline">View All Services
                &rarr;</a>
        </div>
    </div>
</section>

<section class="how-it-works py-5 bg-white">
    <div class="container py-5">
        <div class="text-center mb-5">
            <h2 class="section-title">How It Works</h2>
        </div>

        <div class="row align-items-center g-5">
            <div class="col-lg-5">
                <div class="position-relative">
                    <img src="<?= base_url(); ?>assets/h.png" alt="Process"
                        class="img-fluid rounded-4 shadow-lg position-relative z-2">
                    <div
                        class="bg-primary rounded-4 position-absolute w-100 h-100 top-0 start-0 translate-middle-x ms-4 mt-4 opacity-10 z-1 d-none d-lg-block">
                    </div>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="process-steps">
                    <div class="process-step-card mb-4">
                        <div class="step-icon"><img src="<?= base_url(); ?>assets/consult.png" alt="Consult"></div>
                        <div class="step-info">
                            <h4>1. Consultation & Planning</h4>
                            <p>We understand your goals and create a customized strategy for success.</p>
                        </div>
                    </div>
                    <div class="process-step-card mb-4">
                        <div class="step-icon"><img src="<?= base_url(); ?>assets/design.jpg" alt="Design"></div>
                        <div class="step-info">
                            <h4>2. Design & Development</h4>
                            <p>Our experts craft pixel-perfect designs and robust functionality.</p>
                        </div>
                    </div>
                    <div class="process-step-card mb-4">
                        <div class="step-icon"><img src="<?= base_url(); ?>assets/h3.png" alt="Testing"></div>
                        <div class="step-info">
                            <h4>3. Testing & Launch</h4>
                            <p>Rigorous testing ensures flawless performance before going live.</p>
                        </div>
                    </div>
                    <div class="process-step-card">
                        <div class="step-icon"><img src="<?= base_url(); ?>assets/support.jpg" alt="Support"></div>
                        <div class="step-info">
                            <h4>4. Ongoing Support</h4>
                            <p>Continuous maintenance and optimization for long-term success.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="gallery-wrapper py-5 bg-light-soft">
    <div class="container py-4">
        <div class="text-center mb-5">
            <h2 class="section-title">Our Gallery</h2>
        </div>
        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <img src="<?= base_url(); ?>assets/g1.png" class="gallery-img mb-4" alt="Gallery 1" />
                <img src="<?= base_url(); ?>assets/g2.png" class="gallery-img" alt="Gallery 2" />
            </div>
            <div class="col-lg-4 col-md-6">
                <img src="<?= base_url(); ?>assets/g3.png" class="gallery-img mb-4" alt="Gallery 3" />
                <img src="<?= base_url(); ?>assets/g4.png" class="gallery-img" alt="Gallery 4" />
            </div>
            <div class="col-lg-4 col-md-12 d-flex flex-column gap-4">
                <img src="<?= base_url(); ?>assets/g5.png" class="gallery-img" alt="Gallery 5" />
                <img src="<?= base_url(); ?>assets/g6.png" class="gallery-img" alt="Gallery 6" />
            </div>
        </div>
    </div>
</section>

<section class="py-5 bg-white mb-5">
    <div class="container py-5">
        <div class="premium-mission-card">
            <div class="row g-0">
                <div class="col-xl-6 p-4 p-md-5 bg-dark-slate text-white d-flex flex-column justify-content-center">
                    <h2 class="fw-bold mb-4 text-white">Our Mission</h2>
                    <p class="mb-4 opacity-75" style="line-height: 1.8;">
                        At SUROPRIYO ENTERPRISES, our mission is to set benchmarks in quality, reliability, and
                        innovation by delivering solutions that create tangible and lasting value for our clients.
                        We strive to be more than a service provider—we aim to be a trusted strategic partner in our
                        clients’ growth journeys.
                    </p>
                    <p class="mb-0 opacity-75" style="line-height: 1.8;">
                        Through continuous innovation, process excellence, and a client-first approach, we empower
                        organizations to adapt, evolve, and thrive in a rapidly changing digital landscape.
                    </p>
                </div>

                <div class="col-xl-6 bg-primary-soft p-4 p-md-5">
                    <div class="row g-4">
                        <div class="col-sm-6">
                            <div class="core-value-card">
                                <img src="<?= base_url(); ?>assets/innovation.png" alt="Innovation">
                                <h5>Innovation</h5>
                                <p>Driving innovation to create cutting-edge solutions.</p>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="core-value-card">
                                <img src="<?= base_url(); ?>assets/quality.png" alt="Quality">
                                <h5>Quality</h5>
                                <p>Commitment to the highest standards.</p>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="core-value-card">
                                <img src="<?= base_url(); ?>assets/sustanibility.png" alt="Sustainability">
                                <h5>Sustainability</h5>
                                <p>Eco-friendly practices for a better future.</p>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="core-value-card">
                                <img src="<?= base_url(); ?>assets/excellence.png" alt="Excellence">
                                <h5>Excellence</h5>
                                <p>Exceptional results through dedication.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>