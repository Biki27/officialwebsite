<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Services - Suropriyo Enterprise</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <style>
    

    /* =========================================
            GLOBAL & NAVBAR SPACING
            ========================================= */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    /* MODIFICATION: Added safety check to prevent overflow on the root level */
    html,
    body {
        max-width: 100%;
        /* overflow-x: hidden; */
    }

    body {
        font-family: 'Inter', sans-serif;
        background-color: #F8FAFC;
        color: #475569;
        padding-top: 85px;
        /* Keeps your navbar spacing */
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }

    /* --- Section Titles --- */
    .section-title {
        font-weight: 800;
        color: #ffffff;
        position: relative;
        padding-bottom: 20px;
        margin-bottom: 0;
        font-size: clamp(2rem, 5vw, 2.5rem);
        letter-spacing: -0.5px;
        display: inline-block;
        text-align: center;
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
            PREMIUM SEARCH BAR
            ========================================= */
    .search-hero {
        /* background: #FFFFFF; */
        /* MODIFICATION: Changed 20px to 0 to prevent container overflow on mobile */
        padding: 40px 0;
        /* border-bottom: 1px solid #E2E8F0; */
    }

    .standalone-search {
        max-width: 700px;
        margin: 0 auto;
        background: #FFFFFF;
        border-radius: 16px;
        box-shadow: 0 15px 35px -5px rgba(15, 23, 42, 0.1);
        border: 1px solid #E2E8F0;
        overflow: hidden;
        display: flex;
        transition: all 0.3s ease;
    }

    .standalone-search:focus-within {
        box-shadow: 0 20px 40px -5px rgba(37, 99, 235, 0.15);
        border-color: #2563eb;
    }

    .search-input {
        flex: 1;
        padding: 16px 24px;
        border: none;
        background: transparent;
        font-size: 1.05rem;
        color: #0F172A;
        outline: none;
        font-weight: 500;
        width: 100%;
        /* MODIFICATION: Added for responsive input */
    }

    .search-input::placeholder {
        color: #94A3B8;
    }

    .search-btn {
        background: #2563eb;
        color: white;
        border: none;
        padding: 0 35px;
        font-weight: 700;
        font-size: 1.05rem;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .search-btn:hover {
        background: #1d4ed8;
    }

    /* =========================================
            PREMIUM SERVICES GRID & CARDS
            ========================================= */
    .services-section {
        padding: 80px 0;
        flex-grow: 1;
        /* Pushes footer to bottom */
    }

    .services-grid {
        display: grid;
        /* MODIFICATION: Changed 300px to 280px to fit small mobile screens (e.g. iPhone SE) */
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 30px;
        margin-top: 50px;
    }

    .service-card {
        background: #FFFFFF;
        border-radius: 20px;
        box-shadow: 0 10px 30px -10px rgba(15, 23, 42, 0.08);
        border: 1px solid #E2E8F0;
        border-top: 5px solid #2563eb;
        /* Signature Brand Line */
        overflow: hidden;
        transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .service-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 25px 50px -12px rgba(37, 99, 235, 0.25);
    }

    .service-content {
        padding: 35px 30px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }

    .content-row {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
        margin-bottom: 20px;
    }

    .content-row h4 {
        font-size: 1.4rem;
        font-weight: 800;
        color: #0F172A;
        margin: 0;
        line-height: 1.3;
    }

    .subheading {
        background: rgba(37, 99, 235, 0.1);
        color: #2563eb;
        padding: 6px 14px;
        border-radius: 50px;
        font-size: 0.8rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .service-description {
        color: #64748B;
        font-size: 0.95rem;
        line-height: 1.7;
        margin-bottom: 25px;
        flex-grow: 1;
    }

    .service-features {
        list-style: none;
        padding: 0;
        margin: 0 0 25px 0;
    }

    .service-features li {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 8px 0;
        color: #475569;
        font-weight: 500;
        font-size: 0.95rem;
        border-bottom: 1px solid #F1F5F9;
    }

    .service-features li:last-child {
        border-bottom: none;
    }

    .service-features i {
        color: #10B981;
        /* Emerald Green Check */
        font-size: 0.95rem;
    }

    /* Standardized Premium Button */
    .service-btn {
        background: #2563eb !important;
        color: #FFFFFF !important;
        border: none;
        border-radius: 12px;
        padding: 12px 24px;
        font-weight: 700;
        font-size: 1rem;
        width: 100%;
        display: inline-flex;
        justify-content: center;
        align-items: center;
        box-shadow: 0 6px 15px rgba(37, 99, 235, 0.2);
        transition: all 0.3s ease;
    }

    .service-btn:hover {
        background: #1d4ed8 !important;
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(37, 99, 235, 0.3);
    }


    /* =========================================
            MOBILE RESPONSIVENESS
            ========================================= */
    @media (max-width: 768px) {
        .search-hero {
            /* MODIFICATION: Set horizontal padding to 0 to keep within screen bounds */
            padding: 30px 0;
        }

        .standalone-search {
            flex-direction: column;
            border-radius: 16px;
            /* MODIFICATION: Added margin to keep the card away from edges without breaking width */
            margin: 0 15px;
        }

        .search-input {
            text-align: center;
            border-bottom: 1px solid #E2E8F0;
        }

        .search-btn {
            padding: 16px;
            width: 100%;
        }

        .services-section {
            padding: 50px 0;
        }

        /* MODIFICATION: Added padding to the grid to protect edges on mobile */
        .services-grid {
            padding: 0 15px;
            gap: 20px;
        }
    }
    </style>
    </style>
</head>

<body>

    <div class="search-hero">
        <div class="container">
            <?= form_open('Services/Searchservice', array('method' => 'get')) ?>
            <div class="standalone-search">
                <input type="text" name="ques" class="search-input" placeholder="Search our services..."
                    value="<?= isset($_GET['ques']) ? htmlspecialchars($_GET['ques']) : '' ?>">
                <button type="submit" class="search-btn">
                    <i class="fas fa-search me-2 d-inline d-md-none"></i>Search
                </button>
            </div>
            <?= form_close() ?>
        </div>
    </div>

    <div class="services-section">
        <div class="container">

            <div class="text-center">
                <h2 class="section-title">Available Services</h2>
            </div>

            <div class="services-grid">
                <?php if (!empty($allserv)): ?>
                <?php foreach ($allserv as $serv) { ?>
                <div class="service-card">
                    <div class="service-content">

                        <div class="content-row">
                            <h4><?= $serv->sesrv_name ?></h4>
                            <span class="subheading"><?= $serv->sesrv_type ?></span>
                        </div>

                        <p class="service-description"><?= $serv->sesrv_desc ?></p>

                        <ul class="service-features">
                            <?php foreach (json_decode($serv->sesrv_lines) as $item) { ?>
                            <li><i class="fas fa-check-circle"></i> <?= $item[0] ?></li>
                            <?php } ?>
                        </ul>

                        <div class="mt-auto pt-3">
                            <div class="mt-auto pt-3">
                                <a href="<?= base_url('Services/ServiceDescription/' . $serv->sesrv_id) ?>"
                                    class="service-btn text-decoration-none">
                                    Learn More <i class="fas fa-arrow-right ms-2"></i>
                                </a>
                            </div>
                        </div>

                    </div>
                </div>
                <?php } ?>
                <?php else: ?>
                <div class="col-12 text-center py-5">
                    <i class="fas fa-search-minus fa-3x text-muted mb-3 opacity-50"></i>
                    <h4 class="text-dark fw-bold">No services found</h4>
                    <p class="text-secondary">Try adjusting your search terms.</p>
                </div>
                <?php endif; ?>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>



</body>

</html>