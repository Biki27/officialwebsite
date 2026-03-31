<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>DevOps Engineer | Suropriyo Enterprise</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        /* =========================================
           GLOBAL RESETS & FIXES (Overrides Header.css)
           ========================================= */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        /* FIX 1: Locks horizontal scroll & removes top gap */
        body {
            font-family: 'Inter', sans-serif !important;
            background-color: #F8FAFC !important;
            color: #475569 !important;
            padding-top: 0px !important; /* Kills the white gap under navbar */
            overflow-x: hidden !important; /* Kills horizontal scrolling */
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* =========================================
           PREMIUM HERO SECTION
           ========================================= */
        /* FIX 2: Full bleed hero section */
        /* .premium-profile-hero {
            background: #0F172A !important;
            color: white !important;
            
           
            padding: 140px 0 120px !important; 
            
         
            width: 100vw !important;
            max-width: 100vw !important;
            margin-left: calc(-50vw + 50%) !important;
            margin-top: 0 !important;
            border-radius: 0 !important; 
            
            position: relative;
            overflow: hidden;
            border-bottom: 1px solid rgba(255,255,255,0.05);
        } */

            .premium-profile-hero {
    background: #0F172A !important;
    color: white !important;
    padding: 140px 0 120px !important;

    width: 100%;
    max-width: 100%;

    position: relative;
    overflow: hidden;
    border-bottom: 1px solid rgba(255,255,255,0.05);
}

        /* Subtle glowing orb in the background */
        .hero-glow {
            /* position: absolute;
            top: -20%;
            right: -10%;
            width: 800px;
            height: 800px;
            background: radial-gradient(circle, rgba(37,99,235,0.2) 0%, rgba(15,23,42,0) 70%);
            z-index: 1;
            pointer-events: none; */
            
    position: absolute;
    top: -20%;
    right: -30%;
    width: 400px;
    height: 400px;
    max-width: 100%;
}
        

        .hero-content-wrapper {
            position: relative;
            z-index: 2;
        }

        .profile-avatar-wrapper {
            position: relative;
            display: inline-block;
        }

        .profile-avatar-wrapper::after {
            /* content: '';
            position: absolute;
            top: 15px; left: -15px; right: 15px; bottom: -15px;
            border: 2px solid rgba(37, 99, 235, 0.4);
            border-radius: 50%;
            z-index: -1; */
              top: 10px;
    left: 0;
    right: 0;
    bottom: 0;
        }
        .row {
    margin-left: 0 !important;
    margin-right: 0 !important;
}

        .profile-photo-main {
            width: 100%;
            max-width: 320px;
            aspect-ratio: 1 / 1; /* FIX: Forces a perfect circle mathematically */
            object-fit: cover;
            border-radius: 50%;
            border: 6px solid #1e293b;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            transition: transform 0.4s ease;
            display: block;
            margin: 0 auto; /* Ensures it stays centered */
        }
        .profile-photo-main:hover {
            transform: translateY(-10px);
        }

        .hero-title {
            font-size: clamp(2.5rem, 5vw, 4rem);
            font-weight: 800;
            color: #FFFFFF !important;
            letter-spacing: -1.5px;
            margin-bottom: 10px;
            line-height: 1.2;
        }

        .hero-subtitle {
            font-size: 1.35rem;
            font-weight: 500;
            color: #94A3B8 !important; 
            margin-bottom: 30px;
            line-height: 1.6;
        }

        /* --- Premium Skill Tags --- */
        .skills-wrapper {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 40px;
        }

        .premium-skill-tag {
            background: rgba(37, 99, 235, 0.15);
            color: #60A5FA;
            border: 1px solid rgba(59, 130, 246, 0.3);
            padding: 8px 20px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.95rem;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }

        .premium-skill-tag:hover {
            background: #2563eb;
            color: #FFFFFF;
            border-color: #2563eb;
            transform: translateY(-2px);
        }

        /* --- Buttons --- */
        .btn-premium-primary {
            background: #FFFFFF !important;
            color: #2563eb !important;
            padding: 16px 40px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 1.05rem;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            display: inline-flex;
            align-items: center;
        }

        .btn-premium-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.2);
            color: #1d4ed8 !important;
        }

        /* =========================================
           EXPERIENCE SECTION
           ========================================= */
        .experience-section {
            background: #F8FAFC;
            padding: 80px 0;
            flex-grow: 1; /* Pushes footer to bottom */
        }

        .premium-exp-card {
            background: #FFFFFF;
            padding: 40px 35px;
            border-radius: 20px;
            box-shadow: 0 10px 30px -10px rgba(15, 23, 42, 0.08);
            border: 1px solid #E2E8F0;
            border-top: 5px solid #2563eb; /* Signature Brand Line */
            transition: transform 0.3s ease;
            height: 100%;
        }

        .premium-exp-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px -12px rgba(37, 99, 235, 0.2);
        }

        .exp-card-title {
            font-weight: 800;
            color: #0F172A;
            font-size: 1.35rem;
            margin-bottom: 10px;
        }

        .exp-card-subtitle {
            color: #64748B;
            font-weight: 500;
            font-size: 1rem;
            margin-bottom: 25px;
            display: inline-block;
            background: #F1F5F9;
            padding: 6px 14px;
            border-radius: 8px;
        }

        .exp-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .exp-list li {
            color: #475569;
            font-size: 1.05rem;
            margin-bottom: 12px;
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }

        .exp-list li i {
            color: #10B981; /* Premium Emerald */
            margin-top: 5px;
            font-size: 0.9rem;
        }

        .exp-list.achievements li i {
            color: #F59E0B; /* Premium Gold for Achievements */
        }

        /* =========================================
           MOBILE RESPONSIVENESS
           ========================================= */
        @media (max-width: 991px) {
            .premium-profile-hero {
                text-align: center;
                padding: 120px 0 80px !important;
            }
            .profile-avatar-wrapper {
                margin-bottom: 40px;
            }
            .profile-avatar-wrapper::after {
                display: none; /* Hide offset border on mobile to save space */
            }
            .skills-wrapper {
                justify-content: center;
            }
        }

        @media (max-width: 768px) {
            .premium-exp-card {
                padding: 30px 20px;
            }
            .btn-premium-primary {
                width: 100%;
                justify-content: center;
            }
            .experience-section {
                padding: 50px 0;
            }
        }
    </style>
</head>

<body>

    <section class="premium-profile-hero">
        <div class="hero-glow"></div>
        <div class="container hero-content-wrapper">
            <div class="row align-items-center flex-column-reverse flex-lg-row g-5">
                
                <div class="col-lg-7">
                    <h1 class="hero-title">DevOps Engineer</h1>
                    <p class="hero-subtitle">10+ Years Building Enterprise DevOps Solutions</p>
                    
                    <div class="skills-wrapper">
                        <span class="premium-skill-tag">Manage CI & CD</span>
                        <span class="premium-skill-tag">Deployment</span>
                        <span class="premium-skill-tag">CI/CD Pipelines</span>
                        <span class="premium-skill-tag">Monitoring & Logging</span>
                        <span class="premium-skill-tag">Automation</span>
                        <span class="premium-skill-tag">Infrastructure as Code (IaC)</span>
                        <span class="premium-skill-tag">Collaboration & Security</span>
                    </div>

                    <a href="<?= base_url() ?>ContactUs#contactForm" class="btn-premium-primary">
                        <i class="fas fa-code me-2"></i> Hire Expert
                    </a>
                </div>

                <div class="col-lg-5 text-center">
                    <div class="profile-avatar-wrapper">
                        <img src="<?= base_url() ?>assets/devopslogo.jpeg" alt="DevOps Engineer" class="profile-photo-main">
                    </div>
                </div>

            </div>
        </div>
    </section>

    <section class="experience-section">
        <div class="container">
            <div class="row g-4">
                
                <div class="col-lg-6">
                    <div class="premium-exp-card">
                        <h4 class="exp-card-title">
                            <i class="fas fa-briefcase text-primary me-2"></i> Senior DevOps Developer
                        </h4>
                        <span class="exp-card-subtitle">
                            <strong>Suropriyo Enterprise</strong> &nbsp;•&nbsp; 2022 - Present
                        </span>
                        <ul class="exp-list">
                            <li><i class="fas fa-check-circle"></i> Lead 50+ enterprise DevOps projects</li>
                            <li><i class="fas fa-check-circle"></i> Performance optimization expert</li>
                        </ul>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="premium-exp-card">
                        <h4 class="exp-card-title">
                            <i class="fas fa-trophy text-warning me-2"></i> Key Achievements
                        </h4>
                        <span class="exp-card-subtitle" style="visibility: hidden;">Spacer</span> <ul class="exp-list achievements mt-n4">
                            <li><i class="fas fa-star"></i> 500K+ users served globally</li>
                            <li><i class="fas fa-star"></i> 99.9% uptime across platforms</li>
                            <li><i class="fas fa-star"></i> Reduced load times by 70%</li>
                        </ul>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>