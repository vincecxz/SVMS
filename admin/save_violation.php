<?php
require_once '../config/database.php';
header('Content-Type: application/json');

$response = array('success' => false, 'message' => '');

// Validate required fields
if (!isset($_POST['student']) || !isset($_POST['incident_datetime']) || 
    !isset($_POST['section']) || !isset($_POST['offense'])) {
    $response['message'] = 'Missing required fields';
    echo json_encode($response);
    exit;
}

// Sanitize inputs
$student_id = mysqli_real_escape_string($conn, $_POST['student']);
$incident_datetime = mysqli_real_escape_string($conn, $_POST['incident_datetime']);
$section = mysqli_real_escape_string($conn, $_POST['section']);
$offense_id = mysqli_real_escape_string($conn, $_POST['offense']);
$offense_level = isset($_POST['offense_level']) ? mysqli_real_escape_string($conn, $_POST['offense_level']) : null;
$sanction = mysqli_real_escape_string($conn, $_POST['sanction']); // Get the selected sanction directly
$service_hours = isset($_POST['service_hours']) ? mysqli_real_escape_string($conn, $_POST['service_hours']) : null;

// Convert datetime to MySQL format
$incident_datetime = date('Y-m-d H:i:s', strtotime($incident_datetime));

// Get current timestamp for created_at/updated_at
$current_timestamp = date('Y-m-d H:i:s');

try {
    // Start transaction
    $conn->begin_transaction();

    // Get total violation count for this student and offense (regardless of status)
    $count_query = "SELECT COUNT(*) as total_count 
                   FROM violation_reports 
                   WHERE student_id = '$student_id' 
                   AND section_type = '$section'
                   AND offense_id = '$offense_id'";
    
    $count_result = $conn->query($count_query);
    $violation_count = ($count_result && $count_result->num_rows > 0) 
        ? $count_result->fetch_assoc()['total_count'] + 1 
        : 1;

    // Extract hours from sanction if it contains hours
    $total_hours = null;
    if (preg_match('/(\d+)\s*hours?/i', $sanction, $matches)) {
        $total_hours = intval($matches[1]);
    }

    // Insert new violation report
    $query = "INSERT INTO violation_reports (
        student_id,
        incident_datetime,
        section_type,
        offense_id,
        offense_level,
        violation_count,
        sanction,
        created_at,
        status,
        total_hours,
        completed_hours
    ) VALUES (
        '$student_id',
        '$incident_datetime',
        '$section',
        '$offense_id',
        " . ($offense_level ? "'$offense_level'" : "NULL") . ",
        $violation_count,
        '$sanction',
        '$current_timestamp',
        'Active',
        " . ($total_hours !== NULL ? $total_hours : "NULL") . ",
        0
    )";

    if ($conn->query($query)) {
        $conn->commit();
        $response['success'] = true;
        $response['message'] = 'Violation report saved successfully';
    } else {
        throw new Exception('Error saving violation report: ' . $conn->error);
    }
} catch (Exception $e) {
    $conn->rollback();
    $response['message'] = $e->getMessage();
}

// Helper function to extract hours from sanction text
function extractHoursFromSanction($sanction) {
    // First try to extract numeric hours with parentheses (e.g., "(40)")
    if (preg_match('/\((\d+)\)\s*hours?/i', $sanction, $matches)) {
        return intval($matches[1]);
    }
    // Then try just numeric hours (e.g., "40 hours")
    if (preg_match('/(\d+)\s*hours?/i', $sanction, $matches)) {
        return intval($matches[1]);
    }
    // Finally try text numbers (e.g., "forty hours")
    if (preg_match('/\b(one|two|three|four|five|six|seven|eight|nine|ten|twenty|thirty|forty|fifty|sixty)\b\s+hours?/i', $sanction, $matches)) {
        $number_map = [
            'one' => 1, 'two' => 2, 'three' => 3, 'four' => 4, 'five' => 5,
            'six' => 6, 'seven' => 7, 'eight' => 8, 'nine' => 9, 'ten' => 10,
            'twenty' => 20, 'thirty' => 30, 'forty' => 40, 'fifty' => 50, 'sixty' => 60
        ];
        return $number_map[strtolower($matches[1])];
    }
    // No longer defaulting to 5 hours just because "service" is mentioned
    return null;
}

echo json_encode($response);