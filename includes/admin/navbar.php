<?php
// Remove session_start since it's already handled in auth_check.php
// which is included before this file
?>
<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-dark ">
   
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <!-- <a href="dashboard.php" class="nav-link">Home</a> -->
        </li>
        <!-- Dark Mode Toggle Button -->
        <li class="nav-item">
            <a class="nav-link" href="#" id="darkModeToggle" role="button"><i class="fas fa-adjust"></i></a>
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
                    <!-- <div class="user-header-avatar letter-avatar">
                        <?php echo strtoupper(substr($name, 0, 1)); ?>
                    </div>
                    <p>
                        <?php echo htmlspecialchars($name); ?>
                        <small><?php echo isset($_SESSION['role']) ? ucfirst(htmlspecialchars($_SESSION['role'])) : 'Admin'; ?></small>
                    </p> -->
                </li>
               
                <li class="dropdown-item">
                    <a href="change_password.php" class="d-flex align-items-center">
                        <i class="fas fa-key mr-2"></i> Change Password
                    </a>
                </li>
                <li class="dropdown-item">
                    <a href="javascript:void(0);" onclick="confirmLogout()" class="d-flex align-items-center">
                        <i class="fas fa-sign-out-alt mr-2"></i> Sign out
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

.dropdown-item a {
    color: #343a40;
    padding: 10px 20px;
    transition: background-color 0.3s ease;
}

.dropdown-item a:hover {
    background-color: #f1f1f1;
    color: #007bff;
}

.dropdown-item i {
    color: #007bff;
}

/* Dark Mode Styles */
body.dark-mode {
    background-color: #121212;
    color: #ffffff;
}

.navbar.dark-mode {
    background-color: #2b2b2b; /* Different from body background */
}

.dropdown-menu.dark-mode {
    background-color: #2c2c2c;
    color: #ffffff;
}

.dropdown-item a.dark-mode {
    color: #ffffff;
}

.dropdown-item a.dark-mode:hover {
    background-color: #3a3a3a;
    color: #ffffff;
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

// Toggle Dark Mode
function toggleDarkMode() {
    document.body.classList.toggle('dark-mode');
    // document.querySelector('.navbar').classList.toggle('dark-mode');
    document.querySelectorAll('.dropdown-menu').forEach(menu => menu.classList.toggle('dark-mode'));
    document.querySelectorAll('.dropdown-item a').forEach(item => item.classList.toggle('dark-mode'));
    
    // Save preference to localStorage
    if (document.body.classList.contains('dark-mode')) {
        localStorage.setItem('theme', 'dark');
    } else {
        localStorage.setItem('theme', 'light');
    }
}

// Load theme from localStorage
function loadTheme() {
    const theme = localStorage.getItem('theme');
    if (theme === 'dark') {
        document.body.classList.add('dark-mode');
        // document.querySelector('.navbar').classList.add('dark-mode');
        document.querySelectorAll('.dropdown-menu').forEach(menu => menu.classList.add('dark-mode'));
        document.querySelectorAll('.dropdown-item a').forEach(item => item.classList.add('dark-mode'));
    }
}

// Event listener for toggle button
const toggleButton = document.getElementById('darkModeToggle');
toggleButton.addEventListener('click', toggleDarkMode);

// Load theme on page load
document.addEventListener('DOMContentLoaded', loadTheme);
</script> 