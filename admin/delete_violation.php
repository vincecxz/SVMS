<?php
require_once '../config/database.php';
header('Content-Type: application/json');

$response = array('success' => false, 'message' => '');

if (!isset($_POST['id'])) {
    $response['message'] = 'Missing report ID';
    echo json_encode($response);
    exit;
}

$id = intval($_POST['id']);

if ($id <= 0) {
    $response['message'] = 'Invalid report ID';
    echo json_encode($response);
    exit;
}

$query = "DELETE FROM violation_reports WHERE id = ?";
$stmt = $conn->prepare($query);
if (!$stmt) {
    $response['message'] = 'Failed to prepare statement: ' . $conn->error;
    echo json_encode($response);
    exit;
}
$stmt->bind_param('i', $id);
if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        $response['success'] = true;
        $response['message'] = 'Violation report deleted successfully.';
    } else {
        $response['message'] = 'Violation report not found or already deleted.';
    }
} else {
    $response['message'] = 'Failed to delete violation report: ' . $stmt->error;
}
$stmt->close();
$conn->close();
echo json_encode($response); 