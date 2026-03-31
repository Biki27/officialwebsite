<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Description - Suropriyo Enterprise</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        /* =========================================
           GLOBAL SCROLL LOCK & RESETS
           ========================================= */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            width: 100%;
            max-width: 100%;
            /* overflow-x: hidden;   */
            position: relative;
            
        }
        .row{
            margin-left: 0 !important;
            margin-right: 0 !important;
        }

        body {
            font-family: 'Inter', sans-serif !important;
            background: #F8FAFC !important; 
            color: #475569 !important; 
            padding-top: 85px !important; /* Matches Navbar height */
            display: flex !important;
            flex-direction: column !important;
            /* Removed min-height: 100vh to fix Y-axis double scroll */
        }

        /* Container for all content to prevent row overflow */
        .page-wrapper {
            width: 100%;
            overflow-x: hidden;
            display: flex;
            flex-direction: column;
            flex: 1 0 auto;
        }

        .section-title {
            font-weight: 800;
            color: #0F172A !important;
            position: relative;
            padding-bottom: 20px;
            margin-bottom: 50px;
            font-size: clamp(1.8rem, 5vw, 2.5rem);
            letter-spacing: -0.5px;
            display: inline-block;
            text-align: center;
            max-width: 100%;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 4px;
            background: #2563eb;
            border-radius: 2px;
        }

        /* =========================================
           HERO SECTION
           ========================================= */
        .premium-hero-section {
            background: linear-gradient(90deg, rgba(2, 0, 36, 1) 0%, rgba(6, 5, 82, 1) 7%, rgba(9, 9, 121, 1) 21%, rgba(4, 130, 201, 1) 56%, rgba(0, 212, 255, 1) 89%) !important;
            color: white !important;
            padding: 100px 0 80px;
            text-align: center;
            position: relative;
            overflow: hidden;
            width: 100%;
        }

        .hero-glow {
            position: absolute;
            top: -50%;
            left: 50%;
            transform: translateX(-50%);
            width: 800px;
            height: 800px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, rgba(15, 23, 42, 0) 70%);
            z-index: 1;
            pointer-events: none;
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        .premium-service-badge {
            display: inline-flex;
            background: rgba(255, 255, 255, 0.15);
            color: #ffffff !important;
            border: 1px solid rgba(255, 255, 255, 0.3);
            padding: 8px 24px;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 2rem;
            align-items: center;
            gap: 8px;
            backdrop-filter: blur(5px);
        }

        .hero-title {
            font-size: clamp(2rem, 6vw, 3.5rem); 
            font-weight: 800;
            margin-bottom: 1.5rem;
            color: #FFFFFF !important;
            line-height: 1.2;
            text-shadow: 0 4px 15px rgba(0,0,0,0.3);
        }

        /* =========================================
           CONTENT & GRIDS
           ========================================= */
        .content-section {
            padding: 60px 0;
            background: #F8FAFC !important;
            width: 100%;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            margin-bottom: 60px;
        }

        .premium-feature-card {
            background: #FFFFFF !important;
            border-radius: 20px;
            padding: 35px 25px;
            box-shadow: 0 10px 30px -10px rgba(15, 23, 42, 0.05);
            border: 1px solid #E2E8F0;
            border-top: 5px solid #00D4FF; 
            transition: all 0.4s ease;
            height: 100%;
            word-wrap: break-word;
        }

        .premium-feature-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px -12px rgba(37, 99, 235, 0.2);
        }

        .feature-icon-wrapper {
            width: 60px;
            height: 60px;
            background: rgba(37, 99, 235, 0.08);
            color: #2563eb !important;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            margin-bottom: 20px;
        }

        .benefits-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
        }

        .premium-benefit-item {
            display: flex;
            align-items: flex-start;
            gap: 15px;
            padding: 25px;
            background: #FFFFFF !important;
            border-radius: 18px;
            border: 1px solid #E2E8F0;
            transition: 0.3s;
        }

        .benefit-icon {
            width: 45px;
            height: 45px;
            background: rgba(16, 185, 129, 0.1);
            color: #10B981 !important;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        /* =========================================
           CTA SECTION
           ========================================= */
        .premium-cta-section {
            background: linear-gradient(90deg, rgba(2, 0, 36, 1) 0%, rgba(6, 5, 82, 1) 7%, rgba(9, 9, 121, 1) 21%, rgba(4, 130, 201, 1) 56%, rgba(0, 212, 255, 1) 89%) !important;
            color: white !important;
            text-align: center;
            padding: 60px 30px;
            border-radius: 25px;
            margin: 40px 0;
            position: relative;
            overflow: hidden;
        }

        .btn-cta-premium {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: #FFFFFF !important;
            color: #2563eb !important;
            padding: 14px 35px;
            border-radius: 50px;
            font-weight: 700;
            text-decoration: none;
            transition: 0.3s ease;
        }

        .btn-cta-premium:hover {
            transform: scale(1.05);
            color: #1d4ed8 !important;
        }

        /* =========================================
           MOBILE FIXES
           ========================================= */
        @media (max-width: 768px) {
            body { padding-top: 70px !important; }
            .premium-hero-section { padding: 60px 15px; }
            .hero-title { font-size: 2.2rem; }
            .premium-cta-section { margin: 20px 10px; border-radius: 15px; }
            .features-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>

<body>
    <div class="page-wrapper">
        <div class="premium-hero-section">
            <div class="hero-glow"></div>
            <div class="container">
                <div class="hero-content">
                    <div class="premium-service-badge">
                        <i class="fas fa-layer-group"></i>
                        <?= $serv[0]->sesrv_name ?>
                    </div>
                    <h1 class="hero-title">Transform Your Vision<br>Into Digital Reality</h1>
                    <p class="hero-description text-white opacity-75">We craft modern, responsive applications using cutting-edge technologies and best practices.</p>
                </div>
            </div>
        </div>

        <div class="content-section">
            <div class="container">
                <div class="text-center">
                    <h2 class="section-title">Comprehensive <?= $serv[0]->sesrv_name ?> Solutions</h2>
                </div>

                <p class="text-center mx-auto mb-5" style="max-width: 800px; font-size: 1.1rem; line-height: 1.8;">
                    <?= $serv[0]->sesrv_desc ?>
                </p>

                <div class="features-grid">
                    <?php foreach (json_decode($serv[0]->sesrv_majdesc) as $item) { ?>
                        <div class="premium-feature-card">
                            <div class="feature-icon-wrapper">
                                <i class="fas fa-rocket"></i>
                            </div>
                            <h3><?= $item[1] ?></h3>
                            <p class="text-muted"><?= $item[2] ?></p>
                        </div>
                    <?php } ?>
                </div>

                <div class="text-center mt-5">
                    <h2 class="section-title">Why Choose Us</h2>
                </div>

                <div class="benefits-grid">
                    <div class="premium-benefit-item">
                        <div class="benefit-icon"><i class="fas fa-bolt"></i></div>
                        <div class="benefit-content">
                            <h5>Fast Delivery</h5>
                            <p class="small mb-0 text-muted">Agile cycles for rapid development.</p>
                        </div>
                    </div>
                    <div class="premium-benefit-item">
                        <div class="benefit-icon" style="background:rgba(37,99,235,0.1); color:#2563eb !important;"><i class="fas fa-shield-alt"></i></div>
                        <div class="benefit-content">
                            <h5>Secure</h5>
                            <p class="small mb-0 text-muted">Enterprise-grade security audits.</p>
                        </div>
                    </div>
                    <div class="premium-benefit-item">
                        <div class="benefit-icon"><i class="fas fa-infinity"></i></div>
                        <div class="benefit-content">
                            <h5>Scalable</h5>
                            <p class="small mb-0 text-muted">Cloud-native infrastructure.</p>
                        </div>
                    </div>
                </div>

                <div class="premium-cta-section">
                    <div class="cta-content">
                        <h2 class="text-white fw-bold mb-3">Ready to Build Something Amazing?</h2>
                        <p class="mb-4 opacity-75">Let's discuss your project and create a custom solution.</p>
                        <a href="<?= base_url('ContactUs#contactForm') ?>" class="btn-cta-premium">
                            Start Your Project <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>