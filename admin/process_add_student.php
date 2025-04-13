<?php
require_once '../config/database.php';

// Initialize response array
$response = array();

try {
    // Get form data
    $idNumber = filter_input(INPUT_POST, 'idNumber', FILTER_SANITIZE_STRING);
    $fullName = filter_input(INPUT_POST, 'fullName', FILTER_SANITIZE_STRING);
    $courseYearSection = filter_input(INPUT_POST, 'courseYearSection', FILTER_SANITIZE_STRING);
    $contactNumber = filter_input(INPUT_POST, 'contactNumber', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);

    // Validate required fields
    if (empty($fullName) || empty($courseYearSection) || empty($email)) {
        throw new Exception('Full Name, Course Year Section, and Email are required.');
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email format.');
    }

    // Validate contact number format if provided
    if (!empty($contactNumber) && !preg_match('/^[0-9]{11}$/', $contactNumber)) {
        throw new Exception('Contact number must be 11 digits.');
    }

    // Check if student ID already exists (only if ID is provided)
    if (!empty($idNumber)) {
        $checkStmt = $conn->prepare("SELECT id FROM students WHERE id_number = ?");
        $checkStmt->bind_param("s", $idNumber);
        $checkStmt->execute();
        $result = $checkStmt->get_result();
        
        if ($result->num_rows > 0) {
            throw new Exception('Student ID already exists.');
        }
        $checkStmt->close();
    }

    // Prepare and execute the INSERT statement
    $stmt = $conn->prepare("INSERT INTO students (id_number, full_name, course_year_section, contact_number, email) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $idNumber, $fullName, $courseYearSection, $contactNumber, $email);
    
    if ($stmt->execute()) {
        $response['status'] = 'success';
        $response['message'] = 'Student added successfully.';
    } else {
        throw new Exception('Failed to add student.');
    }
    
    $stmt->close();

} catch (Exception $e) {
    $response['status'] = 'error';
    $response['message'] = $e->getMessage();
}

// Close database connection
$conn->close();

// Send JSON response
header('Content-Type: application/json');
echo json_encode($response); 