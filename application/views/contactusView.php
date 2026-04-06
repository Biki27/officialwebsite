<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<link rel="stylesheet" href="<?= base_url(); ?>css/Contact.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="banner-container" style="margin-top: 85px; position: relative; z-index: 1;">
    <img id="banner" src="<?= base_url(); ?>assets/banner_technologices.png" alt="Technologies Banner"
        class="img-fluid w-100 object-fit-cover"
        style="max-height: 400px; box-shadow: 0 10px 30px rgba(15, 23, 42, 0.1);">
</div>

<section class="team-wrapper ">
    <div class="container py-5">
        <h2 class="section-title text-white title-light">Our Experts</h2>
        <div class="row g-4 justify-content-center mt-4">

            <div class="col-12 col-md-6 col-lg-4 d-flex">
                <div class="premium-card w-100 h-100 d-flex flex-column">
                    <div class="card-img-wrapper">
                        <img src="<?= base_url(); ?>assets/t1.png" class="card-img-top" alt="Founder">
                    </div>
                    <div class="card-body p-4 p-xl-5 d-flex flex-column flex-grow-1">
                        <h4 class="card-title">Founder</h4>
                        <p class="card-text flex-grow-1">Bringing 10+ years of enterprise experience in PHP, Laravel,
                            and SQL to lead complex software development projects from conception to delivery.</p>
                        <a href="<?= base_url() ?>ContactUs/Ceo"
                            class="btn btn-outline-primary btn-premium-outline mt-4">View Profile</a>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-4 d-flex">
                <div class="premium-card w-100 h-100 d-flex flex-column">
                    <div class="card-img-wrapper">
                        <img src="<?= base_url(); ?>assets/t2.png" class="card-img-top" alt="Lead Developer">
                    </div>
                    <div class="card-body p-4 p-xl-5 d-flex flex-column flex-grow-1">
                        <h4 class="card-title">Lead Web Developer</h4>
                        <p class="card-text flex-grow-1">Expert in building highly responsive and scalable enterprise
                            web solutions with over a decade of hands-on experience in PHP and Laravel frameworks.</p>
                        <a href="<?= base_url() ?>ContactUs/LeadDev"
                            class="btn btn-outline-primary btn-premium-outline mt-4">View Profile</a>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-4 d-flex">
                <div class="premium-card w-100 h-100 d-flex flex-column">
                    <div class="card-img-wrapper">
                        <img src="<?= base_url(); ?>assets/t3.png" class="card-img-top" alt="DevOps Engineer">
                    </div>
                    <div class="card-body p-4 p-xl-5 d-flex flex-column flex-grow-1">
                        <h4 class="card-title">DevOps Engineer</h4>
                        <p class="card-text flex-grow-1">Specializes in CI/CD pipelines and infrastructure management,
                            ensuring zero-downtime updates and enterprise-grade security for PHP-based software.</p>
                        <a href="<?= base_url() ?>ContactUs/DevOps"
                            class="btn btn-outline-primary btn-premium-outline mt-4">View Profile</a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<section class="container py-5 mt-4" id="contactFormSection">
    <div class="row g-5 align-items-stretch">

        <div class="col-lg-6">
            <div class="premium-contact-box p-4 p-md-5 h-100">
                <h3 class="mb-4 form-title">Get in Touch</h3>
                
                <form id="contactForm">
                    <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                    
                    <div class="mb-4">
                        <label class="form-label premium-label">Name</label>
                        <input type="text" name="name" id="name" class="form-control premium-input" placeholder="Enter your name" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label premium-label">Email</label>
                        <input type="email" name="email" id="email" class="form-control premium-input" placeholder="name@example.com" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label premium-label">Subject</label>
                        <input type="text" name="subject" id="subject" class="form-control premium-input" placeholder="How can we help?" required>
                    </div>
                    <div class="mb-5">
                        <label class="form-label premium-label">Message</label>
                        <textarea name="message" id="message" class="form-control premium-input" rows="4" placeholder="Your message here..." required></textarea>
                    </div>
                    <button type="submit" id="submitBtn" class="btn btn-primary btn-premium w-100 py-3">
                        <i class="fas fa-paper-plane me-2"></i> Send Message
                    </button>
                </form>
            </div>
        </div>

        <div class="col-lg-6 d-flex flex-column gap-4">
            <div class="premium-map-wrapper flex-grow-1">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d117925.334399277!2d88.26495021200544!3d22.53542731853625!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x39f882db4908f667%3A0x43e330e68f6c2cbc!2sKolkata%2C%20West%20Bengal!5e0!3m2!1sen!2sin!4v1710000000000!5m2!1sen!2sin" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
            </div>

            <div class="premium-address-box p-4 p-md-5">
                <h4 class="address-title mb-4"><i class="fas fa-map-marker-alt me-2 text-primary"></i> Office Address</h4>

                <div class="contact-detail-row">
                    <div class="contact-icon"><i class="fas fa-city"></i></div>
                    <span class="contact-text">Kolkata, West Bengal, India</span>
                </div>

                <!-- <div class="contact-detail-row">
                    <div class="contact-icon"><i class="fas fa-phone"></i></div>
                    <a href="tel:+918777270124" class="contact-text contact-link">+91 87772 70124</a>
                </div> -->

                <div class="contact-detail-row">
                    <div class="contact-icon"><i class="fas fa-envelope"></i></div>
                    <a href="#" onclick="openMail(event)" class="contact-text contact-link">suropriyoenterprise@gmail.com</a>
                </div>
            </div>
        </div>

    </div>
</section>

<section class="container py-5 mb-5">
    <div class="row justify-content-center mb-5">
        <div class="col-lg-8 text-center">
            <h2 class="section-title text-white title-light">Client Testimonials</h2>
            <p class="lead text-white opacity-75 mt-4 px-md-5">
                At SUROPRIYO ENTERPRISE, we pride ourselves on delivering high-performance PHP and Laravel solutions.
            </p>
        </div>
    </div>

    <div class="row g-4 justify-content-center mt-3">
        <?php foreach ($tsm_d as $tsm) { ?>
        <div class="col-12 col-md-6 col-lg-4 d-flex">
            <div class="premium-testimonial-card p-4 p-xl-5 w-100 d-flex flex-column">
                <i class="fas fa-quote-right giant-bg-quote-tsm"></i>
                <div class="testimonial-avatar-wrapper mb-4">
                    <img src="<?= base_url(); ?>assets/<?= $tsm->setsm_img ?>" alt="<?= $tsm->setsm_name ?>" />
                </div>
                <h4 class="testimonial-name"><?= $tsm->setsm_name ?></h4>
                <span class="testimonial-role"><?= $tsm->setsm_designation ?></span>
                <p class="testimonial-text flex-grow-1 mt-4">"<?= $tsm->setsm_quote ?>"</p>
                <div class="testimonial-rating mt-4 pt-4 border-top">
                    <?php for ($i = 0; $i < $tsm->setsm_rating; $i++) { ?>
                    <i class="fas fa-star"></i>
                    <?php } ?>
                </div>
            </div>
        </div>
        <?php } ?>
    </div>
</section>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function() {
    // Premium AJAX Form Submission
    $('#contactForm').on('submit', function(e) {
        e.preventDefault();
        
        const $btn = $('#submitBtn');
        const originalBtnText = $btn.html();
        
        // Validation Check (SweetAlert UI)
        if ($('#name').val() === "" || $('#email').val() === "" || $('#message').val() === "") {
            Swal.fire({
                icon: 'warning',
                title: 'Missing Fields',
                text: 'Please fill out all required fields.',
                confirmButtonColor: '#2563eb'
            });
            return;
        }

        // Show Loading State
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> Sending...');

        $.ajax({
            url: "<?= base_url('ContactUs/submit') ?>", // Needs to point to your new submit method
            type: "POST",
            data: $(this).serialize(),
            dataType: "JSON",
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Sent!',
                        text: response.message,
                        confirmButtonColor: '#2563eb',
                        timer: 3000
                    });
                    $('#contactForm')[0].reset(); 
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Submission Failed',
                        html: response.message,
                        confirmButtonColor: '#2563eb'
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Connection Error',
                    text: 'Could not connect to the server. Please try again later.',
                    confirmButtonColor: '#2563eb'
                });
            },
            complete: function() {
                $btn.prop('disabled', false).html(originalBtnText);
            }
        });
    });

    // Hash handling for smooth scroll
   if (window.location.hash === "#contactForm") {
        setTimeout(function() {
            let target = $("#contactFormSection"); // Target the section container
            if (target.length) {
                // Calculate position minus header offset (approx 90px-100px)
                let headerOffset = 100; 
                let elementPosition = target.offset().top;
                let offsetPosition = elementPosition - headerOffset;

                window.scrollTo({
                    top: offsetPosition,
                    behavior: "smooth"
                });
            }
        }, 100); // Short delay to ensure images/layouts are loaded
        
        // Clean the URL without refreshing
        history.replaceState(null, null, window.location.pathname);
    }
});

function openMail(e) {
    e.preventDefault();
    var isMobile = /iPhone|Android|iPad|iPod/i.test(navigator.userAgent);
    if (isMobile) {
        window.location.href = "mailto:suropriyoenterprise@gmail.com";
    } else {
        window.open("https://mail.google.com/mail/?view=cm&fs=1&to=suropriyoenterprise@gmail.com", "_blank");
    }
}
</script>