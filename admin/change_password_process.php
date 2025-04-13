<?php
require_once '../includes/auth_check.php';
require_once '../config/database.php';

header('Content-Type: application/json');

// Check if user is logged in and is admin
check_auth('admin');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get the posted data
$currentPassword = isset($_POST['currentPassword']) ? $_POST['currentPassword'] : '';
$newPassword = isset($_POST['newPassword']) ? $_POST['newPassword'] : '';

// Validate input
if (empty($currentPassword) || empty($newPassword)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit;
}

try {
    // Get user's current password from database
    $userId = $_SESSION['user_id'];
    $query = "SELECT password FROM users WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    
    if (!$stmt) {
        throw new Exception("Database error: " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    // Verify current password
    if (!password_verify($currentPassword, $user['password'])) {
        echo json_encode(['success' => false, 'message' => 'Current password is incorrect']);
        exit;
    }

    // Hash new password
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    // Update password in database
    $updateQuery = "UPDATE users SET password = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $updateQuery);
    
    if (!$stmt) {
        throw new Exception("Database error: " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, "si", $hashedPassword, $userId);
    
    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['success' => true, 'message' => 'Password updated successfully']);
    } else {
        throw new Exception("Failed to update password: " . mysqli_stmt_error($stmt));
    }

} catch (Exception $e) {
    error_log("Error in change_password_process.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred while changing password']);
} 