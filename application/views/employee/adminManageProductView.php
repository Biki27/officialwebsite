<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product | Suropriyo Enterprise</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="<?= base_url('css/admin/adminManageProductView.css') ?>" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>

   <?php if ($this->session->flashdata('msg')) { 
        $msg = $this->session->flashdata('msg');
        $icon = (stripos($msg, 'Success') !== false || stripos($msg, 'Updated') !== false) ? 'success' : 'info';
    ?>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const Toast = Swal.mixin({
                    toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true
                });
                Toast.fire({ icon: '<?= $icon ?>', title: '<?= $msg ?>' });
            });
        </script>
    <?php } ?>

    <div class="main-content">

        <!-- <div class="welcome">
            <h1><?= isset($product) ? 'Edit Product' : 'Add New Product' ?></h1>
            <p>Product Management System</p>
        </div> -->

        <div class="product-container">

            <div class="form-header">
                <h4 class="form-title">
                    <i class="fas fa-box me-2"></i><?= isset($product) ? 'Edit Product' : 'Add New Product' ?>
                </h4>
                <p class="form-subtitle">Fill in the product details below</p>
            </div>

            <div class="form-body">

                <form method="post" id="productForm"
                    action="<?= isset($product) ? base_url('Employee/updateProduct/' . $product->seprod_id) : base_url('Employee/addProduct') ?>"
                    enctype="multipart/form-data">

                    <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>"
                        value="<?= $this->security->get_csrf_hash(); ?>">

                    <div class="form-grid">

                        <div class="mb-3">
                            <label class="form-label">Product Name <span class="required">*</span></label>
                            <input type="text" name="productName"
                                value="<?= isset($product) ? $product->seprod_name : '' ?>" class="form-control"
                                required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                Product Image 
                                <?php if (!isset($product)): ?>
                                    <span class="required">*</span>
                                <?php endif; ?>
                            </label>
                            
                            <div class="position-relative text-center p-4 rounded-3 bg-light d-flex flex-column justify-content-center align-items-center" 
                                 style="border: 2px dashed #cbd5e1; cursor: pointer; transition: all 0.3s ease; min-height: 220px;"
                                 onmouseover="this.style.borderColor='#461bb9'; this.style.backgroundColor='#f8fafc';" 
                                 onmouseout="this.style.borderColor='#cbd5e1'; this.style.backgroundColor='#f8f9fa';">
                                 
                                <input type="file" name="productImg" id="productImg" accept="image/*" 
                                       class="position-absolute top-0 start-0 w-100 h-100 opacity-0" 
                                       style="cursor: pointer; z-index: 10;" 
                                       <?= isset($product) ? '' : 'required' ?>>
                                
                                <div id="uploadPrompt" style="<?= isset($product) && !empty($product->seprod_img) ? 'display: none;' : 'display: block;' ?>">
                                    <i class="fas fa-cloud-upload-alt fa-3x mb-3" style="color: #461bb9; opacity: 0.8;"></i>
                                    <h6 class="fw-bold text-dark mb-1">Click to upload or drag and drop</h6>
                                    <p class="text-muted small mb-0">SVG, PNG, JPG or GIF (max. 5MB)</p>
                                </div>

                                <div id="previewContainer" style="<?= isset($product) && !empty($product->seprod_img) ? 'display: block;' : 'display: none;' ?>">
                                    <img id="imagePreview" 
                                         src="<?= isset($product) && !empty($product->seprod_img) ? base_url('uploads/products/' . $product->seprod_img) : '#' ?>" 
                                         class="img-fluid rounded-3 shadow-sm border" 
                                         style="max-height: 160px; object-fit: contain;">
                                    <div class="mt-2 text-primary small fw-bold">
                                        <i class="fas fa-edit me-1"></i>Click to change image
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Product Information <span class="required">*</span></label>
                            <textarea name="productInfo"
                                class="form-control form-control-textarea" required><?= isset($product) ? $product->seprod_inf : '' ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Product Link</label>
                            <input type="url" name="productLink" class="form-control"
                                value="<?= isset($product) ? $product->seprod_link : '' ?>"
                                placeholder="https://example.com/product">
                        </div>

                    </div>

                    <div class="action-buttons">

                        <?php if (isset($product)) { ?>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-edit me-2"></i> Update Product
                            </button>
                        <?php } else { ?>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i> Add Product
                            </button>
                        <?php } ?>

                        <button type="reset" class="btn btn-secondary" onclick="resetImagePreview()">
                            <i class="fas fa-undo me-2"></i> Reset
                        </button>

                    </div>

                </form>
            </div>
        </div>
    </div>

    <script>
        // 1. Unified Image Preview Logic
        document.getElementById('productImg').addEventListener('change', function(e) {
            const file = this.files[0];
            const uploadPrompt = document.getElementById('uploadPrompt');
            const previewContainer = document.getElementById('previewContainer');
            const previewImage = document.getElementById('imagePreview');

            if (file) {
                // File size validation (Max 5MB)
                if (file.size > 5 * 1024 * 1024) {
                    Swal.fire({
                        icon: 'error',
                        title: 'File Too Large',
                        text: 'Please select an image smaller than 5MB.',
                        confirmButtonColor: '#ef4444'
                    });
                    this.value = ''; // Clear the input
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    // Hide the cloud text, show the image
                    uploadPrompt.style.display = 'none';
                    previewContainer.style.display = 'block';
                }
                reader.readAsDataURL(file);
            }
        });

        // 2. Reset Preview when Reset button is clicked
        function resetImagePreview() {
            const uploadPrompt = document.getElementById('uploadPrompt');
            const previewContainer = document.getElementById('previewContainer');
            const previewImage = document.getElementById('imagePreview');

            <?php if (isset($product)): ?>
                // Revert to original database image
                previewImage.src = '<?= base_url('uploads/products/' . $product->seprod_img) ?>';
                uploadPrompt.style.display = 'none';
                previewContainer.style.display = 'block';
            <?php else: ?>
                // Show cloud prompt, hide image for new products
                previewContainer.style.display = 'none';
                uploadPrompt.style.display = 'block';
                previewImage.src = '#';
            <?php endif; ?>
        }

        // 3. Form Submission & SweetAlert
        document.getElementById('productForm').addEventListener('submit', function(e) {
            // Let the browser's native HTML5 validation run first
            if (!this.checkValidity()) {
                return; 
            }

            e.preventDefault(); // Stop standard submission to show SweetAlert
            
            let isUpdate = <?= isset($product) ? 'true' : 'false' ?>;
            
            Swal.fire({
                title: isUpdate ? 'Update Product?' : 'Add Product?',
                text: isUpdate ? "Apply changes to this product?" : "Add this new product to the database?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#461bb9',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Yes, Save it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Saving...',
                        allowOutsideClick: false,
                        didOpen: () => { Swal.showLoading(); }
                    });
                    HTMLFormElement.prototype.submit.call(this);
                }
            });
        });
    </script>

</body>
</html>