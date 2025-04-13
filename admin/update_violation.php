<?php
include('../config/database.php');

if(isset($_POST['violation_id'])) {
    // Get form data
    $violation_id = $_POST['violation_id'];
    $student_id = $_POST['student'];
    $incident_datetime = $_POST['incident_datetime'];
    $section = $_POST['section'];
    $offense_level = $_POST['offense_level'];
    $offense_id = $_POST['offense'];
    $sanction = $_POST['sanction'];
    $is_custom_sanction = isset($_POST['is_custom_sanction']) ? filter_var($_POST['is_custom_sanction'], FILTER_VALIDATE_BOOLEAN) : false;
    $service_hours = isset($_POST['service_hours']) ? floatval($_POST['service_hours']) : null;
    
    // Convert datetime to MySQL format
    $incident_datetime = date('Y-m-d H:i:s', strtotime($incident_datetime));
    
    // Start transaction
    $conn->begin_transaction();

    // Get the current violation details
    $get_current = "SELECT student_id, section_type, offense_id FROM violation_reports WHERE id = ?";
    $stmt = $conn->prepare($get_current);
    $stmt->bind_param("i", $violation_id);
    $stmt->execute();
    $current_result = $stmt->get_result();
    $current_violation = $current_result->fetch_assoc();

    // Check if this is a repeated offense
    $count_query = "SELECT COUNT(*) as count 
                   FROM violation_reports 
                   WHERE student_id = ? 
                   AND section_type = ? 
                   AND offense_id = ?";
    $stmt = $conn->prepare($count_query);
    $stmt->bind_param("iss", $student_id, $section, $offense_id);
    $stmt->execute();
    $count_result = $stmt->get_result();
    $violation_count = $count_result->fetch_assoc()['count'];

    // Remove automatic status update of previous violations
    // Status changes should only occur when hours are added or completed

    // Update the current violation
    $update_query = "UPDATE violation_reports SET 
                    student_id = ?,
                    incident_datetime = ?,
                    section_type = ?,
                    offense_id = ?,
                    offense_level = ?,
                    sanction = ?,
                    total_hours = ?,
                    updated_at = CURRENT_TIMESTAMP
                    WHERE id = ?";

    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("isssssdi", 
        $student_id, 
        $incident_datetime, 
        $section, 
        $offense_id, 
        $offense_level, 
        $sanction,
        $service_hours,
        $violation_id
    );

    if ($stmt->execute()) {
        $conn->commit();
        echo json_encode(['success' => true, 'message' => 'Violation report updated successfully']);
    } else {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => 'Failed to update violation report: ' . $conn->error]);
    }
    
    $stmt->close();
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request'
    ]);
}

$conn->close();
?> 