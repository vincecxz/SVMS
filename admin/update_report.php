<?php
require_once('../config/db_connect.php');
require_once('../includes/auth_check.php');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Get POST data
$report_id = $_POST['report_id'] ?? null;
$student_id = $_POST['student_id'] ?? null;
$incident_datetime = $_POST['incident_datetime'] ?? null;
$section = $_POST['section'] ?? null;
$offense_id = $_POST['offense_id'] ?? null;
$offense_level = $_POST['offense_level'] ?? '';

// Validate required fields
if (!$report_id || !$student_id || !$incident_datetime || !$section || !$offense_id) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

try {
    // Start transaction
    $conn->begin_transaction();

    // Update the violation report
    $update_query = "UPDATE violation_reports 
                    SET student_id = ?, 
                        incident_datetime = ?, 
                        section_type = ?, 
                        offense_id = ?, 
                        offense_level = ?,
                        updated_at = CURRENT_TIMESTAMP
                    WHERE id = ?";
    
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("issssi", 
        $student_id,
        $incident_datetime,
        $section,
        $offense_id,
        $offense_level,
        $report_id
    );
    
    if (!$stmt->execute()) {
        throw new Exception("Error updating violation report: " . $stmt->error);
    }

    // Commit transaction
    $conn->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Violation report updated successfully'
    ]);

} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error updating violation report: ' . $e->getMessage()
    ]);
}

$conn->close();
?> 