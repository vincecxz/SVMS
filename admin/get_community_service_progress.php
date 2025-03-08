<?php
include('../config/database.php');
header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    if (!isset($_POST['violation_id'])) {
        throw new Exception('Violation ID is required');
    }

    $violation_id = intval($_POST['violation_id']);
    
    if ($violation_id <= 0) {
        throw new Exception('Invalid violation ID');
    }

    // Get the current violation details including student name
    $violation_query = "SELECT vr.*, s.full_name as student_name 
                       FROM violation_reports vr
                       LEFT JOIN students s ON vr.student_id = s.id
                       WHERE vr.id = ?";
    
    $stmt = $conn->prepare($violation_query);
    if (!$stmt) {
        throw new Exception('Failed to prepare violation query: ' . $conn->error);
    }
    
    $stmt->bind_param("i", $violation_id);
    if (!$stmt->execute()) {
        throw new Exception('Failed to execute violation query: ' . $stmt->error);
    }
    
    $result = $stmt->get_result();
    $current_violation = $result->fetch_assoc();
    
    if (!$current_violation) {
        throw new Exception('Violation not found');
    }

    // Get all related violations (including current one) and their progress
    $progress_query = "SELECT 
                        vr.id,
                        vr.sanction,
                        vr.total_hours,
                        vr.status,
                        vr.incident_datetime,
                        COALESCE(SUM(csp.hours_completed), 0) as completed_hours
                      FROM violation_reports vr
                      LEFT JOIN community_service_progress csp ON vr.id = csp.violation_report_id
                      WHERE vr.student_id = ?
                      AND vr.section_type = ?
                      AND vr.offense_id = ?
                      AND vr.total_hours IS NOT NULL
                      AND vr.status IN ('Active', 'In Progress')
                      GROUP BY vr.id
                      ORDER BY vr.incident_datetime ASC";
    
    $stmt = $conn->prepare($progress_query);
    if (!$stmt) {
        throw new Exception('Failed to prepare progress query: ' . $conn->error);
    }
    
    $stmt->bind_param("isi", 
        $current_violation['student_id'], 
        $current_violation['section_type'], 
        $current_violation['offense_id']
    );
    
    if (!$stmt->execute()) {
        throw new Exception('Failed to execute progress query: ' . $stmt->error);
    }
    
    $result = $stmt->get_result();
    
    // Calculate totals only for active/in-progress violations with hours
    $total_required_hours = 0;
    $total_completed_hours = 0;
    $violations = array();
    
    while ($row = $result->fetch_assoc()) {
        // Only include hours from violations that have total_hours set
        if ($row['total_hours'] !== null) {
            $total_required_hours += floatval($row['total_hours']);
            $total_completed_hours += floatval($row['completed_hours']);
        }
        $violations[] = $row;
    }

    // Get progress history for all related violations
    $history_query = "SELECT 
                        csp.*,
                        DATE_FORMAT(csp.service_date, '%b %d, %Y %h:%i %p') as formatted_date,
                        u.username as updated_by_name,
                        vr.sanction,
                        vr.total_hours
                     FROM community_service_progress csp
                     LEFT JOIN users u ON csp.updated_by = u.id
                     LEFT JOIN violation_reports vr ON csp.violation_report_id = vr.id
                     WHERE vr.student_id = ? 
                     AND vr.section_type = ? 
                     AND vr.offense_id = ?
                     AND vr.total_hours IS NOT NULL
                     AND vr.status IN ('Active', 'In Progress')
                     ORDER BY csp.service_date DESC";
    
    $stmt = $conn->prepare($history_query);
    if (!$stmt) {
        throw new Exception('Failed to prepare history query: ' . $conn->error);
    }
    
    $stmt->bind_param("isi", 
        $current_violation['student_id'],
        $current_violation['section_type'],
        $current_violation['offense_id']
    );
    if (!$stmt->execute()) {
        throw new Exception('Failed to execute history query: ' . $stmt->error);
    }
    
    $result = $stmt->get_result();
    $history = array();
    
    while ($row = $result->fetch_assoc()) {
        $history[] = array(
            'service_date' => $row['formatted_date'],
            'hours_completed' => floatval($row['hours_completed']),
            'remarks' => $row['remarks'],
            'updated_by' => $row['updated_by_name'],
            'sanction' => $row['sanction'],
            'total_hours' => $row['total_hours']
        );
    }

    echo json_encode([
        'success' => true,
        'current_violation' => array(
            'id' => $current_violation['id'],
            'student_name' => $current_violation['student_name'],
            'sanction' => $current_violation['sanction'],
            'total_hours' => $total_required_hours, // Send total hours across all violations
            'status' => $current_violation['status']
        ),
        'total_required_hours' => $total_required_hours,
        'total_completed_hours' => $total_completed_hours,
        'remaining_hours' => $total_required_hours - $total_completed_hours,
        'violations' => $violations,
        'history' => $history
    ]);

} catch (Exception $e) {
    error_log('Error in get_community_service_progress.php: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 