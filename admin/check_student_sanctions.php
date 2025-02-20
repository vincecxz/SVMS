<?php
require_once '../config/database.php';

header('Content-Type: application/json');

if (!isset($_POST['student_id'])) {
    echo json_encode(['error' => 'Student ID is required']);
    exit;
}

$student_id = mysqli_real_escape_string($conn, $_POST['student_id']);

// Query to check for active sanctions
$query = "SELECT COUNT(*) as pending_count 
          FROM violation_reports 
          WHERE student_id = ? 
          AND status = 'Active'";

try {
    $stmt = mysqli_prepare($conn, $query);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, "s", $student_id);
    
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Execute failed: " . mysqli_stmt_error($stmt));
    }

    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);

    echo json_encode([
        'success' => true,
        'has_pending_sanctions' => $row['pending_count'] > 0
    ]);

} catch (Exception $e) {
    error_log("Error in check_student_sanctions.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'Failed to check sanctions'
    ]);
} 