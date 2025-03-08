<?php
include('../config/database.php');

if(isset($_POST['id'])) {
    $id = $_POST['id'];
    
    $query = "SELECT vr.*, s.full_name as student_name,
            CASE 
                WHEN vr.section_type = 'section1' THEN sec1.description
                WHEN vr.section_type = 'section2' THEN sec2.description
            END as offense_description
            FROM violation_reports vr
            LEFT JOIN students s ON vr.student_id = s.id
            LEFT JOIN sec1 ON (vr.section_type = 'section1' AND vr.offense_id = sec1.id)
            LEFT JOIN sec2 ON (vr.section_type = 'section2' AND vr.offense_id = sec2.id)
            WHERE vr.id = ?";
            
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    
    if($stmt->execute()) {
        $result = $stmt->get_result();
        if($row = $result->fetch_assoc()) {
            echo json_encode([
                'success' => true,
                'data' => [
                    'id' => $row['id'],
                    'student_id' => $row['student_id'],
                    'student_name' => $row['student_name'],
                    'incident_datetime' => $row['incident_datetime'],
                    'section_type' => $row['section_type'],
                    'offense_level' => $row['offense_level'],
                    'offense_id' => $row['offense_id'],
                    'offense_description' => $row['offense_description'],
                    'sanction' => $row['sanction'],
                    'status' => $row['status']
                ]
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Violation report not found'
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to fetch violation details'
        ]);
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