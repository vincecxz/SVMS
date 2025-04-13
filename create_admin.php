<?php
require_once 'config/database.php';

// Default admin credentials
$username = 'admin';
$password = 'admin123'; // This will be hashed
$email = 'admin@ctu.edu.ph';
$full_name = 'System Administrator';
$role = 'admin';

try {
    // Check if admin already exists
    $check_query = "SELECT id FROM users WHERE username = ?";
    $stmt = mysqli_prepare($conn, $check_query);
    
    if (!$stmt) {
        throw new Exception("Prepare failed: " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        echo "Admin account already exists!";
        exit;
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert admin user
    $insert_query = "INSERT INTO users (username, password, email, full_name, role) VALUES (?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $insert_query);
    
    if (!$stmt) {
        throw new Exception("Prepare failed: " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, "sssss", $username, $hashed_password, $email, $full_name, $role);
    
    if (mysqli_stmt_execute($stmt)) {
        echo "Admin account created successfully!<br>";
        echo "Username: " . $username . "<br>";
        echo "Password: " . $password . "<br>";
        echo "<strong>Please make sure to change these credentials after first login!</strong>";
    } else {
        throw new Exception("Execute failed: " . mysqli_stmt_error($stmt));
    }

} catch (Exception $e) {
    echo "Error creating admin account: " . $e->getMessage();
} 