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

// Convert datetime to MySQL format
$incident_datetime = date('Y-m-d H:i:s', strtotime($incident_datetime));

// Get current timestamp for created_at/updated_at
$current_timestamp = date('Y-m-d H:i:s');

try {
    // Start transaction
    $conn->begin_transaction();

    // Check if student already has this offense
    $check_query = "SELECT * FROM violation_reports 
                   WHERE student_id = '$student_id' 
                   AND section_type = '$section'
                   AND offense_id = '$offense_id'
                   AND status = 'Active'";
    
    $check_result = $conn->query($check_query);

    if ($check_result && $check_result->num_rows > 0) {
        // Existing violation found - update it
        $existing_violation = $check_result->fetch_assoc();
        $violation_count = $existing_violation['violation_count'] + 1;

        // Get offense details to determine sanction
        $offense_table = ($section === 'section1') ? 'sec1' : 'sec2';
        $offense_query = "SELECT * FROM $offense_table WHERE id = '$offense_id'";
        $offense_result = $conn->query($offense_query);

        if ($offense_result && $offense_result->num_rows > 0) {
            $offense_data = $offense_result->fetch_assoc();

            // Determine which sanction to apply based on violation count
            if ($violation_count <= 1) {
                $sanction = $offense_data['first_sanction'];
            } else if ($violation_count == 2) {
                $sanction = $offense_data['second_sanction'];
            } else {
                $sanction = $offense_data['third_sanction'];
            }

            // Update existing violation
            $update_query = "UPDATE violation_reports 
                           SET incident_datetime = '$incident_datetime',
                               violation_count = '$violation_count',
                               sanction = '$sanction',
                               " . ($offense_level ? "offense_level = '$offense_level'," : "") . "
                               updated_at = '$current_timestamp'
                           WHERE id = '" . $existing_violation['id'] . "'";

            if ($conn->query($update_query)) {
                $conn->commit();
                $response['success'] = true;
                $response['message'] = 'Violation report updated successfully';
            } else {
                throw new Exception('Error updating violation: ' . $conn->error);
            }
        } else {
            throw new Exception('Error retrieving offense details: ' . $conn->error);
        }
    } else {
        // New violation - insert it
        $query = "INSERT INTO violation_reports (
            student_id,
            incident_datetime,
            section_type,
            offense_id,
            offense_level,
            violation_count,
            created_at,
            status
        ) VALUES (
            '$student_id',
            '$incident_datetime',
            '$section',
            '$offense_id',
            " . ($offense_level ? "'$offense_level'" : "NULL") . ",
            1,
            '$current_timestamp',
            'Active'
        )";

        if ($conn->query($query)) {
            $report_id = $conn->insert_id;

            // Get offense details to determine sanction
            $offense_table = ($section === 'section1') ? 'sec1' : 'sec2';
            $offense_query = "SELECT * FROM $offense_table WHERE id = '$offense_id'";
            $offense_result = $conn->query($offense_query);

            if ($offense_result && $offense_result->num_rows > 0) {
                $offense_data = $offense_result->fetch_assoc();
                $sanction = $offense_data['first_sanction'];

                // Update report with sanction
                $update_query = "UPDATE violation_reports 
                               SET sanction = '$sanction'
                               WHERE id = '$report_id'";
                
                if ($conn->query($update_query)) {
                    $conn->commit();
                    $response['success'] = true;
                    $response['message'] = 'Violation report saved successfully';
                } else {
                    throw new Exception('Error updating sanction: ' . $conn->error);
                }
            } else {
                throw new Exception('Error retrieving offense details: ' . $conn->error);
            }
        } else {
            throw new Exception('Error saving violation report: ' . $conn->error);
        }
    }
} catch (Exception $e) {
    $conn->rollback();
    $response['message'] = $e->getMessage();
}

echo json_encode($response);