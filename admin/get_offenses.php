<?php
require_once '../config/database.php';
header('Content-Type: application/json');

$response = array('success' => false, 'message' => '', 'data' => array());

if (!isset($_POST['section'])) {
    $response['message'] = 'Section not specified';
    echo json_encode($response);
    exit;
}

$section = $_POST['section'];

if ($section === 'section1') {
    // Fetch Section 1 (Academic) offenses
    $query = "SELECT * FROM sec1 ORDER BY id";
    $result = $conn->query($query);
    
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $response['data'][] = array(
                'id' => $row['id'],
                'description' => $row['description']
            );
        }
        $response['success'] = true;
    } else {
        $response['message'] = 'Error fetching offenses: ' . $conn->error;
    }
} else if ($section === 'section2') {
    // Fetch Section 2 (Non-Academic) offenses based on level
    if (!isset($_POST['level'])) {
        $response['message'] = 'Offense level not specified';
        echo json_encode($response);
        exit;
    }

    $level = mysqli_real_escape_string($conn, $_POST['level']);
    $query = "SELECT * FROM sec2 WHERE category = '$level' ORDER BY id";
    $result = $conn->query($query);
    
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $response['data'][] = array(
                'id' => $row['id'],
                'description' => $row['description']
            );
        }
        $response['success'] = true;
    } else {
        $response['message'] = 'Error fetching offenses: ' . $conn->error;
    }
} else {
    $response['message'] = 'Invalid section';
}

echo json_encode($response); 