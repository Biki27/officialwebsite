<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surajit Das - Founder | Suropriyo Enterprise</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <style>
       
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', sans-serif !important;
            background-color: #F8FAFC !important;
            color: #475569 !important;
            padding-top: 0px !important;
            /* FIX: Removed the 85px to kill the top gap */
            overflow-x: hidden !important;
        }

         

.row {
    margin-left: 0 !important;
    margin-right: 0 !important;
}
        
        .premium-ceo-hero {
            background: #0F172A !important;
            color: white !important;

            /* FIX: Add top padding here so the dark background goes under the navbar */
            padding: 120px 0 160px !important;

            /* FIX: Force absolute edge-to-edge width to defeat any rogue global margins */
            /* width: 100vw !important;
            max-width: 100vw !important;
            margin-left: calc(-50vw + 50%) !important; */
            background: #0F172A !important;
    color: white !important;
    padding: 120px 0 160px !important;
    width: 100%;
    overflow: hidden;
    position: relative;
    border-bottom: 1px solid rgba(255,255,255,0.05);
            margin-top: 0 !important;
            border-radius: 0 !important;
            /* Kills the rogue rounded corners */

            position: relative;
            overflow: hidden;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        /* Subtle glowing orb in the background */
        .hero-glow {
            position: absolute;
            top: -20%;
            /* right: -10%;
            width: 800px; */
             width: 500px;
    max-width: 100%;
    right: -20%;
            height: 800px;
            background: radial-gradient(circle, rgba(37, 99, 235, 0.2) 0%, rgba(15, 23, 42, 0) 70%);
            z-index: 1;
            pointer-events: none;
        }

        .hero-content-wrapper {
            position: relative;
            z-index: 2;
        }

        .ceo-avatar-wrapper {
            position: relative;
            display: inline-block;
        }

        /* .ceo-avatar-wrapper::after {
            content: '';
            position: absolute;
            top: 20px;
            left: -20px;
            right: 20px;
            bottom: -20px;
            border: 2px solid rgba(37, 99, 235, 0.4);
            border-radius: 30px;
            z-index: -1;
        } */

            .ceo-avatar-wrapper::after {
    top: 10px;
    left: 0;
    right: 0;
    bottom: 0;
}

        .ceo-photo-main {
            width: 100%;
            max-width: 380px;
            height: 380px;
            object-fit: cover;
            border-radius: 30px;
            border: 6px solid #1e293b;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            transition: transform 0.4s ease;
        }

        .ceo-photo-main:hover {
            transform: translateY(-10px);
        }

        .hero-title {
            font-size: clamp(3rem, 6vw, 4.5rem);
            font-weight: 800;
            color: #FFFFFF;
            letter-spacing: -1.5px;
            margin-bottom: 5px;
        }

        .hero-subtitle {
            font-size: 1.5rem;
            font-weight: 600;
            color: #60A5FA;
            /* Bright Brand Blue */
            margin-bottom: 25px;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .hero-description {
            font-size: 1.2rem;
            color: #94A3B8;
            line-height: 1.8;
            margin-bottom: 40px;
            max-width: 600px;
        }

        /* --- Buttons --- */
        .btn-premium-primary {
            background: #FFFFFF;
            color: #2563eb !important;
            padding: 16px 35px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 1.05rem;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            display: inline-flex;
            align-items: center;
        }

        .btn-premium-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
            color: #1d4ed8 !important;
        }

        .btn-premium-outline {
            background: transparent;
            color: #FFFFFF !important;
            border: 2px solid rgba(255, 255, 255, 0.3);
            padding: 14px 35px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 1.05rem;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
        }

        .btn-premium-outline:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: #FFFFFF;
            transform: translateY(-3px);
        }

        /* =========================================
           FLOATING STATS SECTION
           ========================================= */
        .stats-wrapper {
            margin-top: -80px;
            /* Pulls cards up over the hero background */
            position: relative;
            z-index: 10;
            margin-bottom: 80px;
        }

        .stat-card {
            background: #FFFFFF;
            padding: 35px 20px;
            border-radius: 20px;
            text-align: center;
            box-shadow: 0 15px 35px -5px rgba(15, 23, 42, 0.08);
            border: 1px solid #E2E8F0;
            border-bottom: 4px solid #2563eb;
            /* Signature Brand Line */
            transition: transform 0.3s ease;
            height: 100%;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-number {
            font-size: clamp(2rem, 4vw, 3rem);
            font-weight: 800;
            color: #0F172A;
            margin-bottom: 5px;
            letter-spacing: -1px;
        }

        .stat-label {
            color: #64748B;
            font-weight: 600;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* =========================================
           ABOUT SECTION
           ========================================= */
        .about-section {
            background: #F8FAFC;
            padding: 60px 0 100px;
        }

        .section-title {
            font-weight: 800;
            color: #0F172A;
            font-size: clamp(2rem, 4vw, 2.8rem);
            margin-bottom: 30px;
            letter-spacing: -0.5px;
            line-height: 1.3;
        }

        .about-text {
            color: #475569;
            font-size: 1.15rem;
            line-height: 1.8;
            margin-bottom: 40px;
        }

        .about-feature-card {
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            padding: 25px;
            border-radius: 16px;
            box-shadow: 0 10px 20px -5px rgba(15, 23, 42, 0.05);
            transition: all 0.3s ease;
            height: 100%;
            display: flex;
            align-items: flex-start;
            gap: 20px;
        }

        .about-feature-card:hover {
            border-color: rgba(37, 99, 235, 0.3);
            box-shadow: 0 15px 30px -10px rgba(37, 99, 235, 0.15);
        }

        .feature-icon-box {
            width: 50px;
            height: 50px;
            background: rgba(37, 99, 235, 0.1);
            color: #2563eb;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            flex-shrink: 0;
        }

        .feature-text h5 {
            color: #0F172A;
            font-weight: 700;
            font-size: 1.1rem;
            margin-bottom: 8px;
        }

        .feature-text p {
            color: #64748B;
            font-size: 0.95rem;
            margin: 0;
            line-height: 1.6;
        }

        .about-image-secondary {
            width: 100%;
            height: 500px;
            object-fit: cover;
            border-radius: 24px;
            box-shadow: 0 20px 40px -10px rgba(15, 23, 42, 0.15);
            border: 1px solid #E2E8F0;
        }
        /* img {
    max-width: 100%;
    height: auto;
    display: block;
} */

        /* =========================================
           MOBILE RESPONSIVENESS
           ========================================= */
        @media (max-width: 991px) {
            .premium-ceo-hero {
                text-align: center;
                padding: 80px 0 140px;
            }

            .hero-description {
                margin: 0 auto 40px;
            }

            .ceo-avatar-wrapper {
                margin-bottom: 50px;
            }

            .ceo-avatar-wrapper::after {
                display: none;
                /* Hide decorative offset border on mobile */
            }

            .btn-wrapper {
                justify-content: center;
            }

            .stats-wrapper {
                margin-top: -60px;
            }

            .about-image-secondary {
                margin-top: 40px;
                height: 400px;
            }
        }

        @media (max-width: 768px) {
            .stat-card {
                margin-bottom: 20px;
            }

            .stats-wrapper {
                margin-bottom: 40px;
            }

            .about-feature-card {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }

            .btn-premium-primary,
            .btn-premium-outline {
                width: 100%;
                justify-content: center;
                margin-bottom: 10px;
            }
        }
    </style>
</head>

<body>

    <section class="premium-ceo-hero">
        <div class="hero-glow"></div>
        <div class="container hero-content-wrapper">
            <div class="row align-items-center flex-column-reverse flex-lg-row g-5">

                <div class="col-lg-7">
                    <h1 class="hero-title">Surajit Das</h1>
                    <h2 class="hero-subtitle">Founder </h2>
                    <p class="hero-description">
                        Principal Architect of Scalable Enterprise Solutions with over a decade of experience building
                        robust, high-availability systems for global businesses.
                    </p>

                    <div class="d-flex flex-column flex-sm-row gap-3 btn-wrapper">
                        <a href="#" onclick="openMail(event)" target="_blank" class="btn-premium-primary">
                            <i class="fas fa-envelope me-2"></i> Let's Connect
                        </a>
                        <a href="<?= base_url() ?>ContactUs#contactForm" class="btn-premium-outline">
                            <i class="fas fa-comments me-2"></i> Contact Us
                        </a>
                    </div>
                </div>

                <div class="col-lg-5 text-center">
                    <div class="ceo-avatar-wrapper">
                        <img src="<?= base_url() ?>assets/ceo.png" alt="Surajit Das" class="ceo-photo-main">
                    </div>
                </div>

            </div>
        </div>
    </section>

    <div class="container stats-wrapper">
        <div class="row g-4">
            <div class="col-6 col-lg-3">
                <div class="stat-card">
                    <div class="stat-number text-primary">10+</div>
                    <div class="stat-label">Years Experience</div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="stat-card">
                    <div class="stat-number">500+</div>
                    <div class="stat-label">Enterprise Projects</div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="stat-card">
                    <div class="stat-number">50+</div>
                    <div class="stat-label">AI Solutions</div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="stat-card">
                    <div class="stat-number text-success">99.9%</div>
                    <div class="stat-label">Client Satisfaction</div>
                </div>
            </div>
        </div>
    </div>

    <section class="about-section" id="about">
        <div class="container">
            <div class="row g-5 align-items-center">

                <div class="col-lg-6">
                    <h2 class="section-title">Visionary Leader in Software Engineering</h2>
                    <p class="about-text">
                        Surajit Das is the driving force behind Suropriyo Enterprise’s mission to deliver
                        high-performance software that transforms business operations. With profound expertise in
                        enterprise systems, he specializes in high-availability architecture, custom full-stack
                        development, and rapid deployment frameworks that deliver measurable results within weeks.
                    </p>

                    <div class="row g-4">
                        <div class="col-md-12">
                            <div class="about-feature-card">
                                <div class="feature-icon-box">
                                    <i class="fas fa-award"></i>
                                </div>
                                <div class="feature-text">
                                    <h5>Award-Winning Innovator</h5>
                                    <p>Recognized by TechAsia for Excellence in Software Leadership and Technical
                                        Strategy.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="about-feature-card">
                                <div class="feature-icon-box">
                                    <i class="fas fa-rocket"></i>
                                </div>
                                <div class="feature-text">
                                    <h5>Rapid Deployment Expert</h5>
                                    <p>Specialist in 2-week agile implementations for complex enterprise
                                        infrastructures.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <img src="<?= base_url() ?>assets/ceo.png" alt="Surajit Das Workspace"
                        class="about-image-secondary">
                </div>

            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
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
</body>

</html>