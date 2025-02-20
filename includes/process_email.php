<?php
header('Content-Type: application/json');
require_once 'send_email.php';

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
    exit;
}

// Check for required parameters
if (!isset($_POST['action']) || $_POST['action'] !== 'send_violation_email' || 
    !isset($_POST['student_id']) || !isset($_POST['violation_details'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Missing required parameters'
    ]);
    exit;
}

try {
    // Get the parameters
    $studentId = intval($_POST['student_id']);
    $violationDetails = $_POST['violation_details'];

    // Validate student ID
    if ($studentId <= 0) {
        throw new Exception('Invalid student ID');
    }

    // Validate violation details
    $requiredFields = ['incident_datetime', 'section', 'offense', 'sanction'];
    foreach ($requiredFields as $field) {
        if (!isset($violationDetails[$field]) || empty($violationDetails[$field])) {
            throw new Exception("Missing required field: $field");
        }
    }

    // Send the email
    $result = sendViolationEmail($studentId, $violationDetails);
    
    echo json_encode($result);

} catch (Exception $e) {
    error_log("Error in process_email.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 