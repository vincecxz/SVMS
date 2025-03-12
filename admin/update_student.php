<?php
require_once '../config/database.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit;
}

// Validate required fields
$required_fields = ['student_id', 'id_number', 'full_name', 'program', 'year_section'];
$missing_fields = [];

foreach ($required_fields as $field) {
    if (!isset($_POST[$field]) || empty(trim($_POST[$field]))) {
        $missing_fields[] = $field;
    }
}

if (!empty($missing_fields)) {
    echo json_encode(['success' => false, 'error' => 'Please fill in all required fields: ' . implode(', ', $missing_fields)]);
    exit;
}

$student_id = mysqli_real_escape_string($conn, $_POST['student_id']);
$id_number = mysqli_real_escape_string($conn, $_POST['id_number']);
$full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
$program_id = mysqli_real_escape_string($conn, $_POST['program']);
$section = mysqli_real_escape_string($conn, $_POST['year_section']);
$contact_number = isset($_POST['contact_number']) ? mysqli_real_escape_string($conn, $_POST['contact_number']) : null;
$email = isset($_POST['email']) && !empty($_POST['email']) ? mysqli_real_escape_string($conn, $_POST['email']) : null;

// Validate email format if provided
if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'error' => 'Invalid email format']);
    exit;
}

try {
    // Check if another student exists with the same ID number or email
    $check_query = "SELECT id_number, email FROM students WHERE (id_number = ? OR email = ?) AND id != ?";
    $stmt = mysqli_prepare($conn, $check_query);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, "ssi", $id_number, $email, $student_id);
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Execute failed: " . mysqli_stmt_error($stmt));
    }

    $check_result = mysqli_stmt_get_result($stmt);
    if ($check_result && mysqli_num_rows($check_result) > 0) {
        $existing = mysqli_fetch_assoc($check_result);
        if ($existing['id_number'] === $id_number) {
            echo json_encode(['success' => false, 'error' => "Another student with ID number $id_number already exists."]);
        } else {
            echo json_encode(['success' => false, 'error' => "Another student with email $email already exists."]);
        }
        exit;
    }

    // Update student
    $update_query = "UPDATE students SET 
                    id_number = ?,
                    full_name = ?,
                    program_id = ?,
                    section = ?,
                    contact_number = ?,
                    email = ?
                    WHERE id = ?";
    
    $stmt = mysqli_prepare($conn, $update_query);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, "ssisssi", $id_number, $full_name, $program_id, $section, $contact_number, $email, $student_id);
    
    if (mysqli_stmt_execute($stmt)) {
        if (mysqli_affected_rows($conn) > 0) {
            echo json_encode(['success' => true, 'message' => 'Student updated successfully']);
        } else {
            echo json_encode(['success' => true, 'message' => 'No changes were made']);
        }
    } else {
        throw new Exception("Execute failed: " . mysqli_stmt_error($stmt));
    }

} catch (Exception $e) {
    error_log("Error in update_student.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => "Error updating student. Please try again or contact support."]);
} 