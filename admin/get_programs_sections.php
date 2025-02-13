<?php
require_once '../config/database.php';
header('Content-Type: application/json');

if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'get_programs':
            $query = "SELECT id, code, name FROM programs ORDER BY code";
            $result = mysqli_query($conn, $query);
            $programs = [];
            
            while ($row = mysqli_fetch_assoc($result)) {
                $programs[] = $row;
            }
            
            echo json_encode(['success' => true, 'data' => $programs]);
            break;
            
        case 'get_sections':
            if (!isset($_GET['program_id'])) {
                echo json_encode(['success' => false, 'message' => 'Program ID is required']);
                exit;
            }
            
            $program_id = mysqli_real_escape_string($conn, $_GET['program_id']);
            $query = "SELECT section 
                     FROM sections 
                     WHERE program_id = ? 
                     ORDER BY section";
            
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "i", $program_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $sections = [];
            
            while ($row = mysqli_fetch_assoc($result)) {
                $sections[] = [
                    'section' => $row['section']
                ];
            }
            
            echo json_encode(['success' => true, 'data' => $sections]);
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Action is required']);
} 