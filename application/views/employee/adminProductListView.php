<!DOCTYPE html>
<html lang="en">

<head>


    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

    <link href="<?= base_url('css/admin/adminProductListView.css') ?>" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <!-- Main Content Area -->
    <div class="main-content">
        <div class="welcome">
            <h1>Product Management</h1>
            <p>View and manage all products in the system</p>
        </div>

        <div class="container-fluid">

            <!-- Page Title -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div></div>
                <a href="<?= base_url('Employee/addProduct') ?>" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i> Add Product
                </a>
            </div>
            <div class="table-container">
                <div class="table-responsive">
                    <table class="table align-middle table-hover mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">Product ID</th>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Link</th>
                                <th class="text-center pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($products)) { ?>
                                <?php foreach ($products as $p) { ?>
                                    <tr>
                                        <td class="ps-4">
                                            <span class="badge bg-secondary rounded-pill">
                                                PR<?= str_pad($p->seprod_id, 2, '0', STR_PAD_LEFT) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <img src="<?= base_url('uploads/products/' . $p->seprod_img) ?>" class="product-img"
                                                alt="Product Image">
                                        </td>
                                        <td class="fw-bold text-dark">
                                            <?= $p->seprod_name ?>
                                            <div class="mt-1">
                                                <a href="javascript:void(0)" class="text-primary small text-decoration-none"
                                                    onclick='viewProductDetails(<?= htmlspecialchars(json_encode($p), ENT_QUOTES, 'UTF-8') ?>)'>
                                                    <i class="fas fa-info-circle"></i> view details
                                                </a>
                                            </div>
                                        </td>
                                        <td class="text-muted description-cell" style="max-width:260px;"
                                            title="<?= htmlspecialchars($p->seprod_inf) ?>">
                                            <?= $p->seprod_inf ?>
                                        </td>
                                        <td>
                                            <a href="<?= $p->seprod_link ?>" target="_blank"
                                                class="btn btn-sm btn-outline-primary rounded-pill">
                                                <i class="fas fa-link me-1"></i>
                                                Visit
                                            </a>
                                        </td>
                                        <td class="text-center pe-4">
                                            <div class="d-flex justify-content-center gap-2">
                                                <a href="<?= base_url('Employee/editProduct/' . $p->seprod_id) ?>"
                                                    class="btn btn-sm btn-outline-primary rounded-circle action-btn">
                                                    <i class="fas fa-edit"></i>
                                                </a>

                                                <a href="<?= base_url('Employee/deleteProduct/' . $p->seprod_id) ?>"
                                                    class="btn btn-sm btn-outline-danger rounded-circle action-btn"
                                                    onclick="confirmDelete(event, this.href)">
                                                    <i class="fas fa-trash"></i>
                                                </a>

                                            </div>
                                        </td>
                                    </tr>
                                <?php } ?>
                            <?php } else { ?>
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="fas fa-box-open fa-2x mb-3 opacity-50"></i>
                                        <p class="mb-0">
                                            No products found.
                                        </p>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- Product Details Modal -->
    <div class="modal fade" id="viewProductModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <div class="modal-header border-0 pb-0 pt-4 px-4">
                    <h4 class="modal-title fw-bold text-primary" id="viewProductTitle">Product Name</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="d-flex flex-wrap gap-2 mb-4 pb-3 border-bottom" id="viewProductBadges">
                    </div>

                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <div class="text-muted small fw-bold text-uppercase mb-1">Product ID</div>
                            <div id="viewProductId" class="fw-medium text-dark"></div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small fw-bold text-uppercase mb-1">Product Link</div>
                            <div id="viewProductLink" class="fw-medium text-dark">
                                <a href="#" target="_blank" class="text-primary">
                                    <i class="fas fa-external-link-alt me-1"></i> Visit Product
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="row g-4 mb-4">
                        <div class="col-md-12">
                            <div class="text-muted small fw-bold text-uppercase mb-2">Product Image</div>
                            <div class="text-center">
                                <img id="viewProductImage" class="img-fluid rounded-3 shadow-sm"
                                    style="max-height: 200px; object-fit: cover;" alt="Product Image">
                            </div>
                        </div>
                    </div>

                    <div>
                        <div class="text-muted small fw-bold text-uppercase mb-2">Product Description</div>
                        <div id="viewProductDescription" class="p-3 bg-light text-dark rounded-3 border"
                            style="min-height: 120px; white-space: pre-wrap; line-height: 1.6;">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 pb-4 px-4">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-medium shadow-sm"
                        data-bs-dismiss="modal">Close Window</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function confirmDelete(event, url) {
            event.preventDefault(); // Stop the link from opening immediately
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this! The product will be deleted.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444', // Red for delete
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Deleting...',
                        allowOutsideClick: false,
                        didOpen: () => { Swal.showLoading(); }
                    });
                    window.location.href = url; // Proceed to delete
                }
            });
        }
        // Function to handle the Product Details View Modal
        function viewProductDetails(product) {
            // Set basic text fields
            document.getElementById('viewProductTitle').innerText = product.seprod_name;
            document.getElementById('viewProductId').innerHTML = `<span class="badge bg-primary rounded-pill px-3 py-2">PR${String(product.seprod_id).padStart(2, '0')}</span>`;
            document.getElementById('viewProductDescription').innerText = product.seprod_inf;

            // Set product image
            document.getElementById('viewProductImage').src = '<?= base_url('uploads/products/') ?>' + product.seprod_img;

            // Set product link
            const productLinkElement = document.getElementById('viewProductLink');
            productLinkElement.innerHTML = `
                <a href="${product.seprod_link}" target="_blank" class="text-primary">
                    <i class="fas fa-external-link-alt me-1"></i> Visit Product
                </a>
            `;

            // Setup Product Badges
            let badgesHtml = '';
            // badgesHtml += `<span class="badge bg-success rounded-pill px-3 py-2 shadow-sm"><i class="fas fa-circle me-1" style="font-size: 0.5rem; vertical-align: middle;"></i> Active</span>`;
            badgesHtml += `<span class="badge bg-info text-white rounded-pill px-3 py-2 shadow-sm"><i class="fas fa-box me-1"></i> Product</span>`;
            // badgesHtml += `<span class="badge bg-light text-dark border rounded-pill px-3 py-2 shadow-sm"><i class="fas fa-link me-1"></i> Available</span>`;

            document.getElementById('viewProductBadges').innerHTML = badgesHtml;

            // Trigger the modal
            var viewModal = new bootstrap.Modal(document.getElementById('viewProductModal'));
            viewModal.show();
        }
    </script>
</body>

</html>