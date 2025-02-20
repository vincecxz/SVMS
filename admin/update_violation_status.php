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

$query = "UPDATE violation_reports 
          SET status = '$status', 
              updated_at = '$updated_at'
          WHERE id = '$id'";

if ($conn->query($query)) {
    $response['success'] = true;
    $response['message'] = 'Status updated successfully';
} else {
    $response['message'] = 'Error updating status: ' . $conn->error;
}

echo json_encode($response); 