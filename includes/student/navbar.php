<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-dark">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="dashboard.php" class="nav-link">Home</a>
        </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        <!-- User Menu Dropdown -->
        <li class="nav-item dropdown user-menu">
            <a href="#" class="nav-link dropdown-toggle d-flex align-items-center" data-toggle="dropdown">
                <div class="user-image letter-avatar">
                    <?php 
                    $name = isset($_SESSION['full_name']) ? $_SESSION['full_name'] : 'Administrator';
                    echo strtoupper(substr($name, 0, 1));
                    ?>
                </div>
                <span class="d-none d-md-inline ml-2"><?php echo htmlspecialchars($name); ?></span>
            </a>
            <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <!-- User image -->
                <li class="user-header bg-gray-dark">
                    <div class="user-header-avatar letter-avatar">
                        <?php echo strtoupper(substr($name, 0, 1)); ?>
                    </div>
                    <p>
                        <?php echo htmlspecialchars($name); ?>
                        <small><?php echo isset($_SESSION['role']) ? ucfirst(htmlspecialchars($_SESSION['role'])) : 'Admin'; ?></small>
                    </p>
                </li>
                <!-- Menu Footer-->
                <li class="user-footer">
                    <a href="change_password.php" class="btn btn-default btn-flat">
                        <i class="fas fa-key mr-2"></i>Change Password
                    </a>
                    <a href="javascript:void(0);" onclick="confirmLogout()" class="btn btn-default btn-flat float-right">
                        <i class="fas fa-sign-out-alt mr-2"></i>Sign out
            </a>
                </li>
            </ul>
        </li>
    </ul>
</nav> 

<style>
.main-header.navbar {
    background-color: #343a40;
    border: none;
}

.letter-avatar {
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #007bff;
    color: white;
    font-weight: bold;
    border-radius: 50%;
    flex-shrink: 0;
}

.user-image.letter-avatar {
    width: 30px;
    height: 30px;
    font-size: 14px;
}

.nav-link.dropdown-toggle {
    padding-right: 1rem;
}

.user-header {
    height: auto !important;
    padding: 20px;
    text-align: center;
    background-color: #343a40 !important;
}

.user-header-avatar.letter-avatar {
    width: 90px;
    height: 90px;
    margin: 0 auto 15px;
    font-size: 36px;
}

.user-header p {
    color: #fff;
    margin: 0;
}

.user-header p small {
    display: block;
    margin-top: 5px;
}

.user-footer {
    padding: 10px;
    display: flex;
    justify-content: space-between;
    background-color: #f8f9fa;
    border-bottom-left-radius: 4px;
    border-bottom-right-radius: 4px;
}

.user-footer .btn {
    display: flex;
    align-items: center;
    padding: 0.375rem 0.75rem;
}

.dropdown-menu {
    border: none;
    box-shadow: 0 2px 5px rgba(0,0,0,.15);
    margin-top: 5px;
}

.nav-link {
    color: rgba(255,255,255,.7) !important;
}

.nav-link:hover {
    color: rgba(255,255,255,1) !important;
}

/* Fix alignment for user menu */
.user-menu .nav-link {
    display: flex;
    align-items: center;
    padding: 0.5rem 1rem;
}

.user-menu .dropdown-menu {
    right: 0;
    left: auto;
}

/* Ensure proper spacing between avatar and name */
.d-md-inline.ml-2 {
    margin-left: 0.5rem !important;
}
</style>

<script>
function confirmLogout() {
    Swal.fire({
        title: 'Are you sure?',
        text: "You will be logged out of the system.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, sign out!',
        cancelButtonText: 'Cancel',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = '../logout.php';
        }
    });
}
</script> 