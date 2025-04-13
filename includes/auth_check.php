<?php
session_start();

// Function to check if user is logged in and has correct role
function check_auth($required_role = null) {
    // Check if user is logged in
    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
        header('Location: /SVMS-main/admin/dashboard.php?error=3'); // Not logged in
        exit;
    }

    // If a specific role is required, check for it
    if ($required_role !== null && (!isset($_SESSION['role']) || $_SESSION['role'] !== $required_role)) {
        header('Location: /SVMS-main/admin/dashboard.php?error=4'); // Unauthorized role
        exit;
    }

    // Check if session has expired (optional)
    $session_lifetime = 3600; // 1 hour
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $session_lifetime)) {
        session_unset();
        session_destroy();
        header('Location: /SVMS-main/admin/dashboard.php?error=5'); // Session expired
        exit;
    }

    // Update last activity time
    $_SESSION['last_activity'] = time();
} 