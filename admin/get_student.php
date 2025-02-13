<?php
require_once '../config/database.php';
header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'error' => 'Student ID is required']);
    exit;
}

$id = mysqli_real_escape_string($conn, $_GET['id']);

$query = "SELECT s.*, p.code as program_code, p.name as program_name
          FROM students s 
          JOIN programs p ON s.program_id = p.id
          WHERE s.id_number = ?";

$stmt = mysqli_prepare($conn, $query);
if (!$stmt) {
    echo json_encode(['success' => false, 'error' => 'Database error']);
    exit;
}

mysqli_stmt_bind_param($stmt, "s", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($row = mysqli_fetch_assoc($result)) {
    echo json_encode(['success' => true, 'data' => $row]);
} else {
    echo json_encode(['success' => false, 'error' => 'Student not found']);
} 