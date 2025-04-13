<?php
include('../config/database.php');
session_start();
header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('User not logged in');
    }

    // Validate required fields
    if (!isset($_POST['violation_id']) || !isset($_POST['hours_completed']) || !isset($_POST['service_date'])) {
        throw new Exception('Violation ID, hours, and date are required');
    }

    $violation_id = intval($_POST['violation_id']);
    $hours_completed = floatval($_POST['hours_completed']);
    $remarks = isset($_POST['remarks']) ? trim($_POST['remarks']) : '';
    $updated_by = intval($_SESSION['user_id']);
    
    // Validate and format the service date
    $service_date_input = trim($_POST['service_date']);
    if (empty($service_date_input)) {
        throw new Exception('Service date is required');
    }
    
    // Convert the date from the format MM/DD/YYYY HH:MM AM/PM to MySQL datetime format
    $service_datetime = DateTime::createFromFormat('m/d/Y h:i A', $service_date_input);
    if (!$service_datetime) {
        throw new Exception('Invalid date format. Please use MM/DD/YYYY HH:MM AM/PM');
    }
    $service_date = $service_datetime->format('Y-m-d H:i:s');

    // Start transaction
    $conn->begin_transaction();

    try {
        // Get current violation details and total hours
        $query = "SELECT vr.*, 
                        COALESCE(SUM(csp.hours_completed), 0) as current_completed_hours
                 FROM violation_reports vr
                 LEFT JOIN community_service_progress csp ON vr.id = csp.violation_report_id
                 WHERE vr.id = ?
                 GROUP BY vr.id";
        
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            throw new Exception('Failed to prepare query: ' . $conn->error);
        }
        
        $stmt->bind_param("i", $violation_id);
        if (!$stmt->execute()) {
            throw new Exception('Failed to execute query: ' . $stmt->error);
        }
        
        $result = $stmt->get_result();
        $violation = $result->fetch_assoc();
        
        if (!$violation) {
            throw new Exception('Violation not found');
        }
        
        // Validate hours
        if ($hours_completed <= 0) {
            throw new Exception('Hours must be greater than 0');
        }
        
        // Get all unresolved violations for the same offense type and validate total hours
        $violations_query = "SELECT vr.*, 
                           COALESCE(SUM(csp.hours_completed), 0) as current_completed_hours
                           FROM violation_reports vr
                           LEFT JOIN community_service_progress csp ON vr.id = csp.violation_report_id
                           WHERE vr.student_id = ? 
                           AND vr.section_type = ? 
                           AND vr.offense_id = ?
                           AND vr.status IN ('Active', 'In Progress', 'Unresolved')
                           GROUP BY vr.id
                           ORDER BY vr.incident_datetime ASC";

        $violations_stmt = $conn->prepare($violations_query);
        if (!$violations_stmt) {
            throw new Exception('Failed to prepare violations query: ' . $conn->error);
        }

        $violations_stmt->bind_param("isi", $violation['student_id'], $violation['section_type'], $violation['offense_id']);
        if (!$violations_stmt->execute()) {
            throw new Exception('Failed to execute violations query: ' . $violations_stmt->error);
        }

        $violations_result = $violations_stmt->get_result();
        $violations = [];
        $total_required = 0;
        $total_completed = 0;
        
        while ($row = $violations_result->fetch_assoc()) {
            $violations[] = $row;
            $total_required += $row['total_hours'];
            $total_completed += $row['current_completed_hours'];
        }

        // Check if adding these hours would exceed the total required across all violations
        if ($total_completed + $hours_completed > $total_required) {
            throw new Exception('Cannot add ' . $hours_completed . ' hours. This would exceed the total required ' . 
                             $total_required . ' hours across all violations. Only ' . 
                             ($total_required - $total_completed) . ' hours remaining in total.');
        }

        // Find the first unresolved violation that needs hours
        $remaining_hours = $hours_completed;
        $status_updates = [];

        foreach ($violations as $v) {
            if ($v['status'] != 'Resolved' && $remaining_hours > 0) {
                $violation_remaining = $v['total_hours'] - $v['current_completed_hours'];
                
                if ($violation_remaining > 0) {
                    // Calculate how many hours to add to this violation
                    $hours_to_add = min($remaining_hours, $violation_remaining);
                    $remaining_hours -= $hours_to_add;
                    
                    // Insert progress record for this violation
                    $insert_stmt = $conn->prepare("INSERT INTO community_service_progress 
                        (violation_report_id, hours_completed, service_date, remarks, updated_by, date_updated) 
                        VALUES (?, ?, ?, ?, ?, NOW())");
                    
                    if (!$insert_stmt->bind_param("idssi", $v['id'], $hours_to_add, $service_date, $remarks, $updated_by) || 
                        !$insert_stmt->execute()) {
                        throw new Exception('Failed to insert progress record');
                    }
                    
                    // Check if this violation is now complete
                    if ($v['current_completed_hours'] + $hours_to_add >= $v['total_hours']) {
                        $status_updates[] = [
                            'id' => $v['id'],
                            'status' => 'Resolved',
                            'resolution_datetime' => date('Y-m-d H:i:s')
                        ];
                    } else {
                        // Update violation status to In Progress only if this is the first progress entry
                        $progress_check_query = "SELECT COUNT(*) as entry_count 
                                              FROM community_service_progress 
                                              WHERE violation_report_id = ?";
                        $progress_check_stmt = $conn->prepare($progress_check_query);
                        if (!$progress_check_stmt) {
                            throw new Exception('Failed to prepare progress check query: ' . $conn->error);
                        }
                        
                        $progress_check_stmt->bind_param("i", $v['id']);
                        if (!$progress_check_stmt->execute()) {
                            throw new Exception('Failed to execute progress check query: ' . $progress_check_stmt->error);
                        }
                        
                        $progress_check_result = $progress_check_stmt->get_result();
                        $entry_count = $progress_check_result->fetch_assoc()['entry_count'];
                        
                        // Only update to In Progress if this is the first entry
                        if ($entry_count <= 1) {
                            $status_updates[] = [
                                'id' => $v['id'],
                                'status' => 'In Progress',
                                'resolution_datetime' => null
                            ];
                        }
                    }
                }
            }
        }

        // Apply status updates
        foreach ($status_updates as $update) {
            // Calculate total completed hours for this violation
            $completed_hours_query = "SELECT COALESCE(SUM(hours_completed), 0) as total_completed 
                                    FROM community_service_progress 
                                    WHERE violation_report_id = ?";
            
            $completed_stmt = $conn->prepare($completed_hours_query);
            if (!$completed_stmt) {
                throw new Exception('Failed to prepare completed hours query: ' . $conn->error);
            }
            
            $completed_stmt->bind_param("i", $update['id']);
            if (!$completed_stmt->execute()) {
                throw new Exception('Failed to get completed hours: ' . $completed_stmt->error);
            }
            
            $completed_result = $completed_stmt->get_result();
            $completed_row = $completed_result->fetch_assoc();
            $total_completed = floatval($completed_row['total_completed']);

            // Update violation status and completed hours
        $update_query = "UPDATE violation_reports 
                        SET status = ?, 
                               resolution_datetime = ?,
                            completed_hours = ?,
                               updated_at = NOW() 
                           WHERE id = ?";
            
            $update_stmt = $conn->prepare($update_query);
            if (!$update_stmt) {
                throw new Exception('Failed to prepare update statement: ' . $conn->error);
            }
            
            $update_stmt->bind_param("ssdi", $update['status'], $update['resolution_datetime'], $total_completed, $update['id']);
            if (!$update_stmt->execute()) {
                throw new Exception('Failed to update violation status: ' . $update_stmt->error);
            }
        }

        // Update completed hours for the current violation if not in status updates
        if (!array_filter($status_updates, function($update) use ($violation_id) { 
            return $update['id'] == $violation_id; 
        })) {
            // Calculate total completed hours
            $completed_hours_query = "SELECT COALESCE(SUM(hours_completed), 0) as total_completed 
                                    FROM community_service_progress 
                                    WHERE violation_report_id = ?";
            
            $completed_stmt = $conn->prepare($completed_hours_query);
            if (!$completed_stmt) {
                throw new Exception('Failed to prepare completed hours query: ' . $conn->error);
            }
            
            $completed_stmt->bind_param("i", $violation_id);
            if (!$completed_stmt->execute()) {
                throw new Exception('Failed to get completed hours: ' . $completed_stmt->error);
            }
            
            $completed_result = $completed_stmt->get_result();
            $completed_row = $completed_result->fetch_assoc();
            $total_completed = floatval($completed_row['total_completed']);

            // Update completed hours
            $update_query = "UPDATE violation_reports 
                           SET completed_hours = ?,
                               updated_at = NOW() 
                        WHERE id = ?";
        
        $update_stmt = $conn->prepare($update_query);
        if (!$update_stmt) {
            throw new Exception('Failed to prepare update statement: ' . $conn->error);
        }
        
            $update_stmt->bind_param("di", $total_completed, $violation_id);
        if (!$update_stmt->execute()) {
                throw new Exception('Failed to update completed hours: ' . $update_stmt->error);
            }
        }

        // Commit transaction
        $conn->commit();
        
        echo json_encode([
            'success' => true,
            'message' => 'Progress updated successfully'
        ]);

    } catch (Exception $e) {
        $conn->rollback();
        throw $e;
    }

} catch (Exception $e) {
    error_log('Error in update_community_service_progress.php: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 