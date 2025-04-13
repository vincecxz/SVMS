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
        .login-page {
            background: linear-gradient(-45deg,rgb(236, 218, 218),rgb(241, 230, 230),rgb(241, 241, 241),rgb(228, 221, 221));
            background-size: 400% 400%;
            animation: gradient 15s ease infinite;
            height: 100vh;
        }

        @keyframes gradient {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        .login-box {
            margin-top: 0;
            backdrop-filter: blur(10px);
            background-color: white;
            border-radius: 15px;
            box-shadow: 0 15px 25px rgba(0, 0, 0, 0.8);
        }

        .card {
            background: transparent;
            border: none;
        }

        .card-outline.card-primary {
            border-top: 3px solid rgb(33, 121, 236);
        }

        .company-logo {
            width: 120px;
            height: auto;
            margin-bottom: 20px;
            animation: float 6s ease-in-out infinite;
            /* filter: drop-shadow(0 0 8px rgba(255, 165, 0, 0.6)); */
        }

        @keyframes float {
            0% {
                transform: translatey(0px);
            }

            50% {
                transform: translatey(-10px);
            }

            100% {
                transform: translatey(0px);
            }
        }

        .form-control {
            background: rgb(255, 255, 255);
            border: 1px solid rgba(0, 0, 0, 0.2);
            border-radius: 25px;
            padding: 12px 15px;
            color: black;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            background: rgba(255, 255, 255, 0.4);
            border-color:black;
            box-shadow: 0 0 10px rgba(255, 255, 255, 0.3);
        }

        .input-group-text {
            background: transparent;
            border: 1px solid rgba(19, 13, 2, 0.2);
            color: black;
            transition: all 0.3s ease;
            width: 40px;
            justify-content: center;
        }

        .btn-primary {
            border-radius: 25px;
            padding: 10px;
            background: linear-gradient(45deg,rgb(50, 160, 224),rgb(82, 47, 207));
            border: none;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 165, 0, 0.4);
            background: linear-gradient(45deg,rgb(50, 160, 224),rgb(82, 47, 207));
        }

        .login-box-msg {
            color: black;
        }

        .area {
            position: absolute;
            width: 100%;
            height: 100vh;
            overflow: hidden;
        }

        .circles {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            margin: 0;
            padding: 0;
        }

        .circles li {
            position: absolute;
            display: block;
            list-style: none;
            width: 20px;
            height: 20px;
            background: linear-gradient(45deg, rgba(255, 165, 0, 0.1), rgba(255, 140, 0, 0.1));
            animation: animate 25s linear infinite;
            bottom: -150px;
            border-radius: 50%;
            backdrop-filter: blur(5px);
            border: 1px solid rgba(255, 165, 0, 0.2);
        }

        .circles li:nth-child(1) {
            left: 25%;
            width: 80px;
            height: 80px;
            animation-delay: 0s;
        }

        .circles li:nth-child(2) {
            left: 10%;
            width: 20px;
            height: 20px;
            animation-delay: 2s;
            animation-duration: 12s;
        }

        .circles li:nth-child(3) {
            left: 70%;
            width: 30px;
            height: 30px;
            animation-delay: 4s;
        }

        .circles li:nth-child(4) {
            left: 40%;
            width: 60px;
            height: 60px;
            animation-delay: 0s;
            animation-duration: 18s;
        }

        .circles li:nth-child(5) {
            left: 65%;
            width: 20px;
            height: 20px;
            animation-delay: 0s;
        }

        .circles li:nth-child(6) {
            left: 75%;
            width: 110px;
            height: 110px;
            animation-delay: 3s;
        }

        .circles li:nth-child(7) {
            left: 35%;
            width: 150px;
            height: 150px;
            animation-delay: 7s;
        }

        .circles li:nth-child(8) {
            left: 50%;
            width: 25px;
            height: 25px;
            animation-delay: 15s;
            animation-duration: 45s;
        }

        .circles li:nth-child(9) {
            left: 20%;
            width: 15px;
            height: 15px;
            animation-delay: 2s;
            animation-duration: 35s;
        }

        .circles li:nth-child(10) {
            left: 85%;
            width: 150px;
            height: 150px;
            animation-delay: 0s;
            animation-duration: 11s;
        }

        @keyframes animate {
            0% {
                transform: translateY(0) rotate(0deg);
                opacity: 0.5;
                border-radius: 50%;
            }

            100% {
                transform: translateY(-1000px) rotate(720deg);
                opacity: 0;
                border-radius: 50%;
            }
        }

        h1.h1 {
            color: black;
            /* text-shadow: 0 0 10px rgba(255, 165, 0, 0.3); */
        }

        h1.h1 b {
            color: black;
        }

        .text-danger {
            color: #ff6b6b !important;
        }

        .fas {
            color: black;
            text-shadow: 0 0 5px rgba(255, 165, 0, 0.3);
        }

        .is-invalid {
            border-color: #dc3545 !important;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
        }

        .error-icon {
            color: #dc3545 !important;
            animation: fadeIn 0.3s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .fa-exclamation-circle {
            margin-right: 5px;
        }

        .is-invalid+.input-group-append .input-group-text {
            border-color:rgb(14, 3, 4) !important;
            color: #dc3545 !important;
        }
    </style>
</head>
<body class="hold-transition login-page">
<div class="area">
    <ul class="circles">
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
    </ul>
</div>
<div class="login-box animate__animated animate__fadeIn">
    <div class="card card-outline card-warning">
        <div class="card-header text-center">
            <img src="assets/img/logo.png" alt="CTU Logo" class="company-logo">
            <h1 class="h1 animate__animated animate__fadeInDown"><b>Student</b> Violation Monitoring System</h1>
        </div>
        <div class="card-body animate__animated animate__fadeInUp">
            <p class="login-box-msg">Sign in to <b>Student Violation Monitoring System</b></p>
            <form action="login_process.php" method="post">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" name="username" placeholder="Username" required>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-user"></span>
                        </div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input type="password" class="form-control" name="password" placeholder="Password" required>
                    <div class="input-group-append">
                     
                        <div class="input-group-text" style="cursor: pointer;" id="togglePassword">
                            <span class="fas fa-eye"></span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary btn-block">Sign In</button>
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

    // Toggle password visibility
    $('#togglePassword').click(function() {
        const passwordInput = $('input[name="password"]');
        const icon = $(this).find('span');
        
        if (passwordInput.attr('type') === 'password') {
            passwordInput.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            passwordInput.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });
});
</script>
</body>
</html> 