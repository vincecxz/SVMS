<?php
session_start();
require_once 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$username = isset($_POST['username']) ? trim($_POST['username']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';
$remember = isset($_POST['remember']) ? true : false;

try {
    // Validate input
    if (empty($username) || empty($password)) {
        header('Location: index.php?error=1');
        exit;
    }

    // First check in users table (admin/staff)
    $query = "SELECT id, username, password, full_name, role FROM users WHERE username = ?";
    $stmt = mysqli_prepare($conn, $query);
    
    if (!$stmt) {
        throw new Exception("Database error: " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($user = mysqli_fetch_assoc($result)) {
        // Verify password for admin/staff
        if (password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['logged_in'] = true;

            // Handle remember me
            if ($remember) {
                $token = bin2hex(random_bytes(32));
                setcookie('remember_token', $token, time() + (30 * 24 * 60 * 60), '/');
                
                $update_query = "UPDATE users SET remember_token = ? WHERE id = ?";
                $stmt = mysqli_prepare($conn, $update_query);
                mysqli_stmt_bind_param($stmt, "si", $token, $user['id']);
                mysqli_stmt_execute($stmt);
            }

            // Redirect based on role
            if ($user['role'] === 'admin') {
                header('Location: admin/dashboard.php');
            } else {
                header('Location: staff/dashboard.php');
            }
            exit;
        }
    } else {
        // Check in students table
        $query = "SELECT id, email, password, full_name FROM students WHERE email = ?";
        $stmt = mysqli_prepare($conn, $query);
        
        if (!$stmt) {
            throw new Exception("Database error: " . mysqli_error($conn));
        }

        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($student = mysqli_fetch_assoc($result)) {
            // Verify password for student
            if (password_verify($password, $student['password'])) {
                // Set session variables for student
                $_SESSION['user_id'] = $student['id'];
                $_SESSION['email'] = $student['email'];
                $_SESSION['full_name'] = $student['full_name'];
                $_SESSION['role'] = 'student';
                $_SESSION['logged_in'] = true;

                // Handle remember me for student
                if ($remember) {
                    $token = bin2hex(random_bytes(32));
                    setcookie('remember_token', $token, time() + (30 * 24 * 60 * 60), '/');
                    
                    $update_query = "UPDATE students SET remember_token = ? WHERE id = ?";
                    $stmt = mysqli_prepare($conn, $update_query);
                    mysqli_stmt_bind_param($stmt, "si", $token, $student['id']);
                    mysqli_stmt_execute($stmt);
                }

                // Redirect to student dashboard
                header('Location: student/dashboard.php');
                exit;
            }
        }
    }

    // Invalid credentials
    header('Location: index.php?error=1');
    exit;

} catch (Exception $e) {
    error_log("Login error: " . $e->getMessage());
    header('Location: index.php?error=2');
    exit;
}
?> 