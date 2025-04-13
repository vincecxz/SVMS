<?php
require_once '../config/database.php';
header('Content-Type: application/json');

$response = array('success' => false, 'message' => '');

if (!isset($_POST['id']) || !isset($_POST['status'])) {
    $response['message'] = 'Missing required parameters';
    echo json_encode($response);
    exit;
}

$id = mysqli_real_escape_string($conn, $_POST['id']);
$status = mysqli_real_escape_string($conn, $_POST['status']);
$updated_at = date('Y-m-d H:i:s');

// Get the student ID from the violation report
$get_student = "SELECT student_id FROM violation_reports WHERE id = ?";
$stmt = $conn->prepare($get_student);
if (!$stmt) {
    $response['message'] = 'Failed to prepare query: ' . $conn->error;
    echo json_encode($response);
    exit;
}

$stmt->bind_param("i", $id);
if (!$stmt->execute()) {
    $response['message'] = 'Failed to execute query: ' . $stmt->error;
    echo json_encode($response);
    exit;
}

$result = $stmt->get_result();
$violation = $result->fetch_assoc();

if (!$violation) {
    $response['message'] = 'Violation not found';
    echo json_encode($response);
    exit;
}

// Handle resolution datetime if provided
$resolution_datetime = null;
if (isset($_POST['resolution_datetime']) && !empty($_POST['resolution_datetime'])) {
    $resolution_datetime = mysqli_real_escape_string($conn, $_POST['resolution_datetime']);
    // Convert from MM/DD/YYYY hh:mm A to MySQL datetime format
    $datetime = DateTime::createFromFormat('m/d/Y h:i A', $resolution_datetime);
    if ($datetime) {
        $resolution_datetime = $datetime->format('Y-m-d H:i:s');
    } else {
        $response['message'] = 'Invalid resolution datetime format';
        echo json_encode($response);
        exit;
    }
}

// Start transaction
$conn->begin_transaction();

try {
    // Update all active violations for this student
    $update_all = "UPDATE violation_reports 
                   SET status = ?, 
                       updated_at = ?,
                       resolution_datetime = ?
                   WHERE student_id = ? 
                   AND status IN ('Active', 'In Progress')";
    
    $update_stmt = $conn->prepare($update_all);
    if (!$update_stmt) {
        throw new Exception('Failed to prepare update query: ' . $conn->error);
    }
    
    $update_stmt->bind_param("sssi", 
        $status,
        $updated_at,
        $resolution_datetime,
        $violation['student_id']
    );
    
    if (!$update_stmt->execute()) {
        throw new Exception('Failed to update violations: ' . $update_stmt->error);
    }
    
    $conn->commit();
    
    $response['success'] = true;
    $response['message'] = 'All violations have been resolved successfully';
} catch (Exception $e) {
    $conn->rollback();
    $response['message'] = $e->getMessage();
}

echo json_encode($response); 