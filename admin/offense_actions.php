<?php
require_once '../config/database.php';
header('Content-Type: application/json');

$response = array('success' => false, 'message' => '', 'data' => null);

if (!isset($_POST['action'])) {
    $response['message'] = 'No action specified';
    echo json_encode($response);
    exit;
}

$action = $_POST['action'];

switch ($action) {
    case 'add':
        if (!isset($_POST['description']) || !isset($_POST['first_sanction']) || 
            !isset($_POST['second_sanction']) || !isset($_POST['third_sanction']) ) {
            $response['message'] = 'Missing required fields';
            break;
        }

        $description = mysqli_real_escape_string($conn, $_POST['description']);
      
        $first_sanction = mysqli_real_escape_string($conn, $_POST['first_sanction']);
        $second_sanction = mysqli_real_escape_string($conn, $_POST['second_sanction']);
        $third_sanction = mysqli_real_escape_string($conn, $_POST['third_sanction']);
      
        $query = "INSERT INTO sec1 (description, first_sanction, second_sanction, third_sanction) 
                 VALUES ('$description', '$first_sanction', '$second_sanction', '$third_sanction')";

        if ($conn->query($query)) {
            $response['success'] = true;
            $response['message'] = 'Offense added successfully';
        } else {
            $response['message'] = 'Error adding offense: ' . $conn->error;
        }
        break;

    case 'update':
        if (!isset($_POST['offense_id']) || !isset($_POST['description']) || 
            !isset($_POST['first_sanction']) || !isset($_POST['second_sanction']) || 
            !isset($_POST['third_sanction'])) {
            $response['message'] = 'Missing required fields';
            break;
        }

        $id = mysqli_real_escape_string($conn, $_POST['offense_id']);
        $description = mysqli_real_escape_string($conn, $_POST['description']);

        $first_sanction = mysqli_real_escape_string($conn, $_POST['first_sanction']);
        $second_sanction = mysqli_real_escape_string($conn, $_POST['second_sanction']);
        $third_sanction = mysqli_real_escape_string($conn, $_POST['third_sanction']);


        $query = "UPDATE sec1 SET 
                 description = '$description',
            
                 first_sanction = '$first_sanction',
                 second_sanction = '$second_sanction',
                 third_sanction = '$third_sanction',
          
                 WHERE id = '$id'";

        if ($conn->query($query)) {
            $response['success'] = true;
            $response['message'] = 'Offense updated successfully';
        } else {
            $response['message'] = 'Error updating offense: ' . $conn->error;
        }
        break;

    case 'delete':
        if (!isset($_POST['id'])) {
            $response['message'] = 'Missing offense ID';
            break;
        }

        $id = mysqli_real_escape_string($conn, $_POST['id']);
        $query = "DELETE FROM sec1 WHERE id = '$id'";

        if ($conn->query($query)) {
            $response['success'] = true;
            $response['message'] = 'Offense deleted successfully';
        } else {
            $response['message'] = 'Error deleting offense: ' . $conn->error;
        }
        break;

    case 'get':
        if (!isset($_POST['id'])) {
            $response['message'] = 'Missing offense ID';
            break;
        }

        $id = mysqli_real_escape_string($conn, $_POST['id']);
        $query = "SELECT * FROM sec1 WHERE id = '$id'";
        $result = $conn->query($query);

        if ($result && $result->num_rows > 0) {
            $response['success'] = true;
            $response['data'] = $result->fetch_assoc();
        } else {
            $response['message'] = 'Offense not found';
        }
        break;

    default:
        $response['message'] = 'Invalid action';
        break;
}

echo json_encode($response); 