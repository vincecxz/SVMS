<?php
require_once '../config/database.php';
header('Content-Type: application/json');

$response = array('success' => false, 'message' => '', 'sanction' => '');

// Validate required parameters
if (!isset($_POST['student_id']) || !isset($_POST['offense_id']) || !isset($_POST['section'])) {
    $response['message'] = 'Missing required parameters';
    echo json_encode($response);
    exit;
}

// Sanitize inputs
$student_id = mysqli_real_escape_string($conn, $_POST['student_id']);
$offense_id = mysqli_real_escape_string($conn, $_POST['offense_id']);
$section = mysqli_real_escape_string($conn, $_POST['section']);
$level = isset($_POST['level']) ? mysqli_real_escape_string($conn, $_POST['level']) : null;

try {
    // Get previous violations count for this student and offense (regardless of status)
    $violations_count_query = "SELECT COUNT(*) as count 
        FROM violation_reports 
        WHERE student_id = '$student_id' 
        AND section_type = '$section'
        AND offense_id = '$offense_id'";
    
    $count_result = $conn->query($violations_count_query);
    $violations_count = ($count_result && $count_result->num_rows > 0) 
        ? $count_result->fetch_assoc()['count'] 
        : 0;

    // Get offense details
    $offense_table = ($section === 'section1') ? 'sec1' : 'sec2';
    $offense_query = "SELECT * FROM $offense_table WHERE id = '$offense_id'";
    if ($section === 'section2' && $level) {
        $offense_query .= " AND category = '$level'";
    }

    $offense_result = $conn->query($offense_query);

    if ($offense_result && $offense_result->num_rows > 0) {
        $offense_data = $offense_result->fetch_assoc();

        // Get the appropriate sanction based on violation count
        $sanction = '';
        if ($violations_count == 0) {
            $sanction = $offense_data['first_sanction'];
        } elseif ($violations_count == 1) {
            $sanction = $offense_data['second_sanction'];
        } else {
            $sanction = $offense_data['third_sanction'];
        }
        
        // Add all available sanctions to the response
        $response['success'] = true;
        $response['sanction'] = $sanction;
        $response['violations_count'] = $violations_count;
        $response['available_sanctions'] = [
            'first_sanction' => $offense_data['first_sanction'],
            'second_sanction' => $offense_data['second_sanction'],
            'third_sanction' => $offense_data['third_sanction']
        ];
    } else {
        $response['message'] = 'Offense not found';
    }
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response); 