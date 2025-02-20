<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SAO - Student Violation Monitoring System</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
    <!-- icheck bootstrap -->
    <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="dist/css/adminlte.min.css">
    <!-- Custom styles -->
    <style>
        :root {
            --primary-color: #007bff;
            --secondary-color: #6c757d;
            --success-color: #28a745;
            --error-color: #dc3545;
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-box {
            width: 500px;
            margin: 0;
        }

        .card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        }

        .login-card-body {
            padding: 40px;
            border-radius: 20px;
        }

        .login-logo {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid rgba(0,0,0,0.1);
        }

        .login-logo img {
            width: 150px;
            height: 150px;
            object-fit: contain;
            transition: transform 0.3s ease;
        }

        .login-logo img:hover {
            transform: scale(1.05);
        }

        .login-box-msg {
            color: var(--secondary-color);
            font-size: 1.4rem;
            margin-bottom: 30px;
            text-align: center;
            font-weight: 600;
            line-height: 1.3;
        }

        .input-group {
            margin-bottom: 1.8rem !important;
            flex-direction: row-reverse;
         
            
        }

        .form-control {
            height: 50px;
            border-radius: 10px;
            border: 2px solid #e2e8f0;
            padding: 10px 20px 10px 50px;
            font-size: 1.1rem;
            transition: all 0.3s ease;
     
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.15);
        }

        .input-group-text {
            border: none;
            background: none;
            color: #a0aec0;
            font-size: 1.3rem;
            padding: 0;
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            z-index: 10;
        }

        .input-group-append {
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            z-index: 10;
        }

        .btn-primary {
            height: 50px;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 10px;
            background-color: var(--primary-color);
            border: none;
            transition: all 0.3s ease;
            width: 100%;
            margin-top: 10px;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(0,123,255,0.3);
        }

        .alert {
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 25px;
            border: none;
            background-color: rgba(220,53,69,0.1);
            color: var(--error-color);
            font-weight: 500;
        }

        .icheck-primary {
            margin-top: 10px;
        }

        .icheck-primary label {
            color: var(--secondary-color);
            font-size: 1rem;
        }

        /* Custom checkbox styling */
        .icheck-primary input[type="checkbox"] {
            border: 2px solid #e2e8f0;
            border-radius: 6px;
            width: 20px;
            height: 20px;
        }

        .icheck-primary input[type="checkbox"]:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        /* Animation for error message */
        @keyframes slideIn {
            from {
                transform: translateY(-10px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .alert {
            animation: slideIn 0.3s ease-out;
        }

        /* Responsive adjustments */
        @media (max-width: 576px) {
            .login-box {
                width: 95%;
                margin: 20px;
            }

            .login-card-body {
                padding: 30px;
            }

            .login-logo img {
                width: 120px;
                height: 120px;
            }
        }
    </style>
</head>
<body class="hold-transition login-page">
<div class="login-box">
    <div class="card">
        <div class="card-body login-card-body">
            <div class="login-logo">
                <img src="assets/img/ctu_logo.png" alt="CTU Logo">
            </div>
            <p class="login-box-msg">Student Violation Monitoring System</p>

            <?php if(isset($_GET['error'])): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <?php
                    switch($_GET['error']) {
                        case '1':
                            echo 'Invalid username or password.';
                            break;
                        case '2':
                            echo 'System error. Please try again later.';
                            break;
                        case '3':
                            echo 'Please log in to access the system.';
                            break;
                        case '4':
                            echo 'You are not authorized to access that page.';
                            break;
                        case '5':
                            echo 'Your session has expired. Please log in again.';
                            break;
                        default:
                            echo 'An error occurred. Please try again.';
                    }
                ?>
            </div>
            <?php endif; ?>

            <form action="login_process.php" method="post">
                <div class="input-group">
                    <input type="text" class="form-control" name="username" placeholder="Username" required>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-user"></span>
                        </div>
                    </div>
                </div>
                <div class="input-group">
                    <input type="password" class="form-control" name="password" placeholder="Password" required>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock"></span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-8">
                        <div class="icheck-primary">
                            <input type="checkbox" id="remember">
                            <label for="remember">
                                Remember Me
                            </label>
                        </div>
                    </div>
                    <div class="col-12 mt-3">
                        <button type="submit" class="btn btn-primary">
                            Sign In
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- jQuery -->
<script src="plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>

<script>
$(document).ready(function() {
    // Add focus effect to input groups
    $('.form-control').focus(function() {
        $(this).closest('.input-group').find('.input-group-text').css('color', '#007bff');
    }).blur(function() {
        $(this).closest('.input-group').find('.input-group-text').css('color', '#a0aec0');
    });

    // Add loading state to button on form submit
    $('form').on('submit', function() {
        const btn = $(this).find('button[type="submit"]');
        btn.html('<i class="fas fa-spinner fa-spin"></i> Signing In...').prop('disabled', true);
    });
});
</script>
</body>
</html> 