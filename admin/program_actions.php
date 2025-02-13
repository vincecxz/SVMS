<?php
require_once '../config/database.php';
header('Content-Type: application/json');

if (!isset($_REQUEST['action'])) {
    echo json_encode(['success' => false, 'message' => 'No action specified']);
    exit;
}

$action = $_REQUEST['action'];

switch ($action) {
    case 'add':
        if (!isset($_POST['program_code']) || !isset($_POST['program_name'])) {
            echo json_encode(['success' => false, 'message' => 'Missing required fields']);
            exit;
        }

        $program_code = mysqli_real_escape_string($conn, trim($_POST['program_code']));
        $program_name = mysqli_real_escape_string($conn, trim($_POST['program_name']));
        $sections = isset($_POST['sections']) ? array_map('trim', explode(',', $_POST['sections'])) : [];

        // Check if program code already exists
        $check_query = "SELECT id FROM programs WHERE code = ?";
        $stmt = mysqli_prepare($conn, $check_query);
        mysqli_stmt_bind_param($stmt, "s", $program_code);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            echo json_encode(['success' => false, 'message' => 'Program code already exists']);
            exit;
        }

        // Insert program
        $insert_query = "INSERT INTO programs (code, name) VALUES (?, ?)";
        $stmt = mysqli_prepare($conn, $insert_query);
        mysqli_stmt_bind_param($stmt, "ss", $program_code, $program_name);

        if (!mysqli_stmt_execute($stmt)) {
            echo json_encode(['success' => false, 'message' => 'Failed to add program']);
            exit;
        }

        $program_id = mysqli_insert_id($conn);

        // Add sections if provided
        if (!empty($sections)) {
            $section_values = [];
            $section_params = str_repeat('(?,?),', count($sections) - 1) . '(?,?)';
            $stmt = mysqli_prepare($conn, "INSERT INTO sections (program_id, section) VALUES " . $section_params);
            
            if ($stmt) {
                $types = str_repeat('is', count($sections));
                $params = [];
                foreach ($sections as $section) {
                    $params[] = $program_id;
                    $params[] = trim($section);
                }
                
                mysqli_stmt_bind_param($stmt, $types, ...$params);
                mysqli_stmt_execute($stmt);
            }
        }

        echo json_encode(['success' => true, 'message' => 'Program added successfully']);
        break;

    case 'edit':
        if (!isset($_POST['program_id']) || !isset($_POST['program_code']) || !isset($_POST['program_name'])) {
            echo json_encode(['success' => false, 'message' => 'Missing required fields']);
            exit;
        }

        $program_id = mysqli_real_escape_string($conn, $_POST['program_id']);
        $program_code = mysqli_real_escape_string($conn, trim($_POST['program_code']));
        $program_name = mysqli_real_escape_string($conn, trim($_POST['program_name']));

        // Check if program code already exists for other programs
        $check_query = "SELECT id FROM programs WHERE code = ? AND id != ?";
        $stmt = mysqli_prepare($conn, $check_query);
        mysqli_stmt_bind_param($stmt, "si", $program_code, $program_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            echo json_encode(['success' => false, 'message' => 'Program code already exists']);
            exit;
        }

        // Update program
        $update_query = "UPDATE programs SET code = ?, name = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $update_query);
        mysqli_stmt_bind_param($stmt, "ssi", $program_code, $program_name, $program_id);

        if (mysqli_stmt_execute($stmt)) {
            echo json_encode(['success' => true, 'message' => 'Program updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update program']);
        }
        break;

    case 'delete':
        if (!isset($_POST['program_id'])) {
            echo json_encode(['success' => false, 'message' => 'Program ID is required']);
            exit;
        }

        $program_id = mysqli_real_escape_string($conn, $_POST['program_id']);

        // Check if program has students
        $check_query = "SELECT COUNT(*) as count FROM students WHERE program_id = ?";
        $stmt = mysqli_prepare($conn, $check_query);
        mysqli_stmt_bind_param($stmt, "i", $program_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);

        if ($row['count'] > 0) {
            echo json_encode(['success' => false, 'message' => 'Cannot delete program: There are students enrolled in this program']);
            exit;
        }

        // Start transaction
        mysqli_begin_transaction($conn);

        try {
            // Delete sections first
            $delete_sections = "DELETE FROM sections WHERE program_id = ?";
            $stmt = mysqli_prepare($conn, $delete_sections);
            mysqli_stmt_bind_param($stmt, "i", $program_id);
            mysqli_stmt_execute($stmt);

            // Delete program
            $delete_program = "DELETE FROM programs WHERE id = ?";
            $stmt = mysqli_prepare($conn, $delete_program);
            mysqli_stmt_bind_param($stmt, "i", $program_id);
            mysqli_stmt_execute($stmt);

            mysqli_commit($conn);
            echo json_encode(['success' => true, 'message' => 'Program deleted successfully']);
        } catch (Exception $e) {
            mysqli_rollback($conn);
            echo json_encode(['success' => false, 'message' => 'Failed to delete program']);
        }
        break;

    case 'get_program':
        if (!isset($_GET['program_id'])) {
            echo json_encode(['success' => false, 'message' => 'Program ID is required']);
            exit;
        }

        $program_id = mysqli_real_escape_string($conn, $_GET['program_id']);
        $query = "SELECT * FROM programs WHERE id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $program_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            echo json_encode(['success' => true, 'data' => $row]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Program not found']);
        }
        break;

    case 'get_program_sections':
        if (!isset($_GET['program_id'])) {
            echo json_encode(['success' => false, 'message' => 'Program ID is required']);
            exit;
        }

        $program_id = mysqli_real_escape_string($conn, $_GET['program_id']);
        $query = "SELECT p.*, GROUP_CONCAT(s.section ORDER BY s.section) as sections 
                 FROM programs p 
                 LEFT JOIN sections s ON p.id = s.program_id 
                 WHERE p.id = ? 
                 GROUP BY p.id";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $program_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            echo json_encode(['success' => true, 'data' => $row]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Program not found']);
        }
        break;

    case 'update_sections':
        if (!isset($_POST['program_id']) || !isset($_POST['sections'])) {
            echo json_encode(['success' => false, 'message' => 'Missing required fields']);
            exit;
        }

        $program_id = mysqli_real_escape_string($conn, $_POST['program_id']);
        $sections = array_map('trim', explode(',', $_POST['sections']));

        // Start transaction
        mysqli_begin_transaction($conn);

        try {
            // Delete existing sections
            $delete_query = "DELETE FROM sections WHERE program_id = ?";
            $stmt = mysqli_prepare($conn, $delete_query);
            mysqli_stmt_bind_param($stmt, "i", $program_id);
            mysqli_stmt_execute($stmt);

            // Add new sections
            if (!empty($sections)) {
                $section_values = [];
                $section_params = str_repeat('(?,?),', count($sections) - 1) . '(?,?)';
                $stmt = mysqli_prepare($conn, "INSERT INTO sections (program_id, section) VALUES " . $section_params);
                
                if ($stmt) {
                    $types = str_repeat('is', count($sections));
                    $params = [];
                    foreach ($sections as $section) {
                        $params[] = $program_id;
                        $params[] = trim($section);
                    }
                    
                    mysqli_stmt_bind_param($stmt, $types, ...$params);
                    mysqli_stmt_execute($stmt);
                }
            }

            mysqli_commit($conn);
            echo json_encode(['success' => true, 'message' => 'Sections updated successfully']);
        } catch (Exception $e) {
            mysqli_rollback($conn);
            echo json_encode(['success' => false, 'message' => 'Failed to update sections']);
        }
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
} 