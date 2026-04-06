<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<link rel="stylesheet" href="<?= base_url(); ?>css/About.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
    rel="stylesheet">



<div class="container-fluid p-0">
    <div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel" data-bs-interval="1500">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0"
                class="active"></button>
            <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1"></button>
            <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="2"></button>
        </div>
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="<?= base_url() ?>assets/banner1.jpeg" class="d-block w-100" alt="Solutions">
            </div>
            <div class="carousel-item">
                <img src="<?= base_url() ?>assets/banner2.jpeg" class="d-block w-100" alt="Apps">
            </div>
            <div class="carousel-item">
                <img src="<?= base_url() ?>assets/banner3.jpeg" class="d-block w-100" alt="Systems">
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators"
            data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators"
            data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
        </button>
    </div>
</div>

<div class="about">
    <h1>About Us</h1>

    <p>
        Suropriyo Enterprise is a modern software development company focused on building
        scalable, secure, and high-performance digital solutions for businesses of all sizes.
    </p>

    <p>
        We design and develop websites, mobile applications, and custom business systems
        that help organizations grow faster, operate efficiently, and adapt to changing market needs.
    </p>

    <p class="features-list">
        <i class="fas fa-code"></i> Custom Software &nbsp;|&nbsp;
        <i class="fas fa-mobile-alt"></i> Web & Mobile Apps &nbsp;|&nbsp;
        <i class="fas fa-chart-line"></i> Business Automation
    </p>
</div>

<div class="product-showcase-wrapper">
    <section class="container py-5" id="solutions">
        <h2 class="section-title text-center mb-5">Our Software Solutions</h2>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 justify-content-center">

            <?php if (!empty($products)): ?>
            <?php foreach ($products as $product): ?>
            <div class="col d-flex">
                <div class="card h-100 shadow-sm border-0 w-100 innovative-card">
                    <div class="position-relative overflow-hidden">
                        <img src="<?= base_url() . 'uploads/products/' . $product->seprod_img ?>" class="card-img-top"
                            alt="<?= htmlspecialchars($product->seprod_name) ?>">
                    </div>
                    <div class="card-body d-flex flex-column p-4">
                        <h5 class="card-title fw-bold text-dark mb-3">

                            <?= (strlen($product->seprod_name) > 20) ? substr($product->seprod_name, 0, 20) . '...' : $product->seprod_name ?>

                        </h5>
                        <p class="card-text text-muted flex-grow-1">
                            <?= (strlen($product->seprod_inf) > 120) ? substr($product->seprod_inf, 0, 120) . '...' : $product->seprod_inf ?>
                        </p>
                        <button class="btn btn-primary mt-4 py-2 fw-bold viewProductBtn" data-bs-toggle="modal"
                            data-bs-target="#productModal" data-name="<?= htmlspecialchars($product->seprod_name) ?>"
                            data-desc="<?= htmlspecialchars($product->seprod_inf) ?>"
                            data-img="<?= base_url() . 'uploads/products/' . $product->seprod_img ?>"
                            data-link="<?= $product->seprod_link ?>">
                            <i class="fas fa-external-link-alt me-2"></i>View Details
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            <?php else: ?>
            <div class="col-12 text-center py-5">
                <p class="text-muted fst-italic">
                    We are currently updating our latest solutions. Please check back soon.
                </p>
            </div>
            <?php endif; ?>
        </div>
    </section>
</div>

<div class="para">
    <p>
        <!-- strong will be bold and bigger -->
        <strong class="fs-3">Suropriyo Enterprise</strong> delivers reliable and scalable digital solutions
        designed to solve real business challenges. Our focus is on building systems that are
        not only functional, but also efficient, secure, and easy to manage.
    </p>

    <p class="mt-3">
        From business automation tools and management systems to modern websites and mobile
        applications, we help organizations streamline operations and improve productivity.
        Every solution we build is tailored to meet specific business goals.
    </p>

    <p class="mt-3">
        We handle the complete development lifecycle from planning and design to deployment
        and ongoing support so you can focus on growing your business without worrying about
        technical complexity.
    </p>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var myCarousel = document.querySelector('#carouselExampleIndicators');
    var carousel = new bootstrap.Carousel(myCarousel, {
        interval: 1500, // 1.5 seconds (FAST)
        ride: 'carousel' // Auto-start
    });
});
</script>

<!-- Compact Product View Modal -->
<div class="modal fade" id="productModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content product-detail-card">

            <div class="modal-header border-0 pb-0">
                <h4 id="modalProductName" class="modal-title fw-bold text-primary"></h4>
                <button type="button" class="btn-close-circle" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="modal-body pt-3">
                <div class="text-center mb-4">
                    <div class="image-preview-wrapper shadow-sm">
                        <img id="modalProductImage" src="" alt="Product">
                    </div>
                </div>

                <div class="mb-2">
                    <label class="detail-label">Product Description</label>
                    <div class="description-box custom-scrollbar">
                        <p id="modalProductDescription" class="mb-0 text-muted"></p>
                    </div>
                </div>
            </div>

            <div class="modal-footer border-0 pt-2 pb-4 justify-content-center gap-3">
                <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">Cancel</button>
                <!-- <a id="modalProductLink" href="#" target="_blank" class="btn btn-save-action">
                        Visit Product
                    </a> -->
            </div>
        </div>
    </div>
</div>
<script>
// Compact Product Modal Functionality
document.querySelectorAll(".viewProductBtn").forEach(button => {

    button.addEventListener("click", function() {

        let name = this.dataset.name;
        let desc = this.dataset.desc;
        let img = this.dataset.img;
        let link = this.dataset.link;

        // Set modal content
        document.getElementById("modalProductName").innerText = name;
        document.getElementById("modalProductDescription").innerText = desc;
        document.getElementById("modalProductImage").src = img;
        document.getElementById("modalProductLink").href = link;

    });

});

// Share Product Function
function shareProduct() {
    const productName = document.getElementById("modalProductName").innerText;
    const productLink = document.getElementById("modalProductLink").href;

    if (navigator.share) {
        navigator.share({
            title: productName,
            text: `Check out this amazing product: ${productName}`,
            url: productLink
        }).then(() => {
            showToast('Thanks for sharing!');
        }).catch(err => {
            console.log('Share cancelled');
        });
    } else {
        // Fallback - Copy to clipboard
        navigator.clipboard.writeText(productLink).then(() => {
            showToast('Product link copied to clipboard!');
        });
    }
}

// Toast Notification Function
function showToast(message) {
    const toast = document.createElement('div');
    toast.className = 'toast-notification';
    toast.innerHTML = `
                <div class="toast-content">
                    <i class="fas fa-check-circle text-success me-2"></i>
                    <span>${message}</span>
                </div>
            `;

    // Add toast styles
    toast.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                background: white;
                padding: 1rem 1.5rem;
                border-radius: 12px;
                box-shadow: 0 10px 40px rgba(0,0,0,0.2);
                z-index: 9999;
                animation: slideInRight 0.3s ease-out;
                min-width: 300px;
            `;

    document.body.appendChild(toast);

    setTimeout(() => {
        toast.style.animation = 'slideOutRight 0.3s ease-out';
        setTimeout(() => {
            document.body.removeChild(toast);
        }, 300);
    }, 3000);
}

// Add toast animations
const style = document.createElement('style');
style.textContent = `
            @keyframes slideInRight {
                from { transform: translateX(100%); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
            @keyframes slideOutRight {
                from { transform: translateX(0); opacity: 1; }
                to { transform: translateX(100%); opacity: 0; }
            }
            .toast-content {
                display: flex;
                align-items: center;
                font-weight: 500;
            }
        `;
document.head.appendChild(style);
</script>