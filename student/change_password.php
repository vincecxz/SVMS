<?php
require_once '../includes/auth_check.php';
check_auth('student');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Change Password - SAO</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Include CDN links instead of local plugins -->
    <?php include '../includes/cdn_links.php'; ?>
    <!-- Font Awesome -->
    
    <!-- Theme style -->
    <link rel="stylesheet" href="../dist/css/adminlte.min.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <style>
    .content-wrapper {
        background: #f4f6f9;
    }
    
    .card {
        border: none;
        box-shadow: 0 0 20px rgba(0,0,0,.08);
        border-radius: 15px;
        overflow: hidden;
    }
    
    .card-header {
        background: #fff;
        border-bottom: 1px solid #f0f0f0;
        padding: 1.5rem;
    }
    
    .card-header .card-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #343a40;
        margin: 0;
    }
    
    .card-body {
        padding: 2rem;
    }
    
    .form-group label {
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 0.5rem;
    }
    
    .input-group {
        margin-bottom: 0.5rem;
        box-shadow: 0 2px 4px rgba(0,0,0,.04);
        border-radius: 10px;
        overflow: hidden;
    }
    
    .form-control {
        border: 2px solid #e2e8f0;
        padding: 0.75rem 1rem;
        height: auto;
        font-size: 1rem;
        transition: all 0.2s ease;
    }
    
    .form-control:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0,123,255,.1);
    }
    
    .input-group-append .btn {
        padding: 0.75rem 1rem;
        border: none;
        background: #f8f9fa;
        color: #6c757d;
        transition: all 0.2s ease;
    }
    
    .input-group-append .btn:hover {
        background: #e9ecef;
        color: #343a40;
    }
    
    .card-footer {
        background: #fff;
        border-top: 1px solid #f0f0f0;
        padding: 1.5rem;
    }
    
    .btn-primary {
        padding: 0.75rem 2rem;
        font-weight: 600;
        border-radius: 10px;
        transition: all 0.2s ease;
    }
    
    .btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0,123,255,.15);
    }
    
    .text-danger {
        font-weight: 600;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .card-body {
            padding: 1.5rem;
        }
        
        .form-control {
            font-size: 16px; /* Prevent zoom on mobile */
        }
    }
    </style>
</head>
<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <?php include '../includes/student/navbar.php'; ?>
        <?php include '../includes/student/sidebar.php'; ?>

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <!-- Content Header -->
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0 text-dark">Change Password</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                                <li class="breadcrumb-item active">Change Password</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="row justify-content-center">
                        <div class="col-md-8 col-lg-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-key mr-2"></i>Change Your Password
                                    </h3>
                                </div>
                                <form id="changePasswordForm" method="POST" action="javascript:void(0);" autocomplete="off">
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="currentPassword">Current Password <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="password" class="form-control" id="currentPassword" name="currentPassword" required>
                                                <div class="input-group-append">
                                                    <button class="btn toggle-password" type="button">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="newPassword">New Password <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="password" class="form-control" id="newPassword" name="newPassword" required>
                                                <div class="input-group-append">
                                                    <button class="btn toggle-password" type="button">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group mb-0">
                                            <label for="confirmPassword">Confirm New Password <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required>
                                                <div class="input-group-append">
                                                    <button class="btn toggle-password" type="button">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer text-center">
                                        <button type="submit" class="btn btn-primary px-5">
                                            <i class="fas fa-save mr-2"></i>Save Changes
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <!-- jQuery -->
    <script src="../plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE App -->
    <!-- No need to include jQuery, Bootstrap, and other plugins again as they are in cdn_links.php -->
<script src="../dist/js/adminlte.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
    $(document).ready(function() {
        // Toggle password visibility
        $('.toggle-password').click(function(e) {
            e.preventDefault();
            const input = $(this).closest('.input-group').find('input');
            const icon = $(this).find('i');
            
            if (input.attr('type') === 'password') {
                input.attr('type', 'text');
                icon.removeClass('fa-eye').addClass('fa-eye-slash');
            } else {
                input.attr('type', 'password');
                icon.removeClass('fa-eye-slash').addClass('fa-eye');
            }
        });

        // Handle form submission
        $('#changePasswordForm').on('submit', function(e) {
            e.preventDefault();
            
            const currentPassword = $('#currentPassword').val();
            const newPassword = $('#newPassword').val();
            const confirmPassword = $('#confirmPassword').val();

            // Validate passwords match
            if (newPassword !== confirmPassword) {
                Swal.fire({
                    icon: 'error',
                    title: 'Password Mismatch',
                    text: 'New password and confirm password do not match!'
                });
                return;
            }

            // Show loading state
            const submitBtn = $(this).find('button[type="submit"]');
            const originalText = submitBtn.html();
            submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>Saving...');

            // Send AJAX request
            $.ajax({
                url: 'change_password_process.php',
                type: 'POST',
                data: {
                    currentPassword: currentPassword,
                    newPassword: newPassword
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Password changed successfully!',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(function() {
                            $('#changePasswordForm')[0].reset();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'Failed to change password'
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred. Please try again.'
                    });
                },
                complete: function() {
                    submitBtn.prop('disabled', false).html(originalText);
                }
            });
        });
    });
    </script>
</body>
</html> 