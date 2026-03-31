<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Information | Supropriyo Enterprise</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="<?= base_url('css/admin/adminEmployeeDetailsView.css') ?>" rel="stylesheet">
</head>
<body>

    <!-- Main Content -->
    <div class="main-content">
       
        <div class="employee-container">
            <!-- Header -->
            <div class="emp-header">
                <h1 class="emp-title">
                    <i class="fas fa-user me-3"></i>
                    Profile Details
                </h1>
                <p class="emp-subtitle">Complete employee information overview</p>
            </div>

            <!-- Employee Details Layout -->
            <div class="emp-details">
                <!-- Left: Information Grid -->
                <div class="info-section">
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-label">Employee ID</div>
                            <div class="info-value"><?= $info->seemp_id?></div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">Name</div>
                            <div class="info-value"><?= $info->seempd_name ?></div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">Email</div>
                            <div class="info-value"><?= $info->seemp_email?></div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">Phone</div>
                            <div class="info-value"><?= $info->seempd_phone ?></div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">Position</div>
                            <div class="info-value"><?= $info->seempd_designation ?></div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">Experience</div>
                            <div class="info-value"><?= $info->seempd_experience ?> Years</div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">Salary</div>
                            <div class="info-value">₹ <?= $info->seempd_salary ?></div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">Increment</div>
                            <div class="info-value"><?= $info->seempd_increment ?>%</div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">Date of Birth</div>
                            <div class="info-value"><?= $info->seempd_dob ?></div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">Joining Date</div>
                            <div class="info-value"><?= $info->seempd_joiningdate ?></div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">Branch</div>
                            <div class="info-value"><?= $info->seemp_branch ?></div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">Status</div>
                            <div class="info-value"><?= $info->seemp_status ?></div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">Last Login</div>
                            <div class="info-value"><?= $info->seemp_lastlogin	 ?></div>
                        </div>

                        <!--  COVER LETTER AS TEXT -->
                        <div class="info-item" style="grid-column: 1 / -1;">
                            <div class="info-label">Cover Letter</div>
                            <div class="info-value">
                                <div class="cover-letter-text">
                                    <?= $info->sejoba_coverletter	 ?>
                                </div>
                            </div>
                        </div>

                       

                        <div class="info-item" style="grid-column: 1 / -1;">
                            <div class="info-label">Permanent Address</div>
                            <div class="info-value"><?= $info->seempd_address_permanent	 ?></div>
                        </div>

                        <div class="info-item" style="grid-column: 1 / -1;">
                            <div class="info-label">Current Address</div>
                            <div class="info-value"><?= $info->seempd_address_current	 ?></div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">Aadhar No</div>
                            <div class="info-value"><?= $info->	seempd_aadhar ?></div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">PAN No</div>
                            <div class="info-value"><?= $info->seempd_pan	?></div>
                        </div>
                    </div>
                </div>

                <!-- Right: Photo Section -->
               <div class="photo-section">
                    <?php 
                        // Generate a beautiful initials avatar matching your theme
                        $initials_avatar = "https://ui-avatars.com/api/?name=" . urlencode($info->seempd_name) . "&background=461bb9&color=ffffff&size=180&bold=true";

                        $profile_pic = (!empty($info->seempd_img)) 
                                       ? base_url('uploads/' . $info->seempd_img) 
                                       : $initials_avatar;
                    ?>
                    
                    <img src="<?= $profile_pic ?>" 
                         alt="Employee Photo" 
                         class="emp-photo"
                         onerror="this.onerror=null; this.src='<?= $initials_avatar ?>';">

                    <?= form_open('Employee/RegisterEmployee') ?>
                        <input type="hidden" name="empid" value="<?= $info->seemp_id ?>" />
                        <button type="submit" class="edit-btn">
                            <i class="fas fa-user-edit me-2"></i>Edit Profile
                        </button>
                    <?= form_close() ?>
                </div>
            </div>
        </div>
    </div>

   
</body>
</html>
