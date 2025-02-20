<?php
require_once '../config/database.php';

if (!isset($_POST['student_id'])) {
    echo 'Student ID is required';
    exit;
}

$student_id = mysqli_real_escape_string($conn, $_POST['student_id']);

// Query to get active sanctions with details
$query = "SELECT vr.*, s.full_name,
          CASE 
              WHEN vr.section_type = 'section1' THEN sec1.description
              WHEN vr.section_type = 'section2' THEN sec2.description
          END as offense_description
          FROM violation_reports vr
          LEFT JOIN students s ON vr.student_id = s.id
          LEFT JOIN sec1 ON (vr.section_type = 'section1' AND vr.offense_id = sec1.id)
          LEFT JOIN sec2 ON (vr.section_type = 'section2' AND vr.offense_id = sec2.id)
          WHERE vr.student_id = ? AND vr.status = 'Active'
          ORDER BY vr.incident_datetime DESC";

try {
    $stmt = mysqli_prepare($conn, $query);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, "s", $student_id);
    
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Execute failed: " . mysqli_stmt_error($stmt));
    }

    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) > 0) {
        echo '<div class="card">
                <div class="card-header bg-danger">
                    <h3 class="card-title text-white">Active Sanctions</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Offense</th>
                                    <th>Level</th>
                                    <th>Sanction</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>';
        
        while ($row = mysqli_fetch_assoc($result)) {
            $date = date('M d, Y', strtotime($row['incident_datetime']));
            echo "<tr>
                    <td>{$date}</td>
                    <td>{$row['offense_description']}</td>
                    <td>{$row['offense_level']}</td>
                    <td>{$row['sanction']}</td>
                    <td><span class='badge badge-danger'>Active</span></td>
                </tr>";
        }
        
        echo '</tbody>
            </table>
            </div>
            <div class="alert alert-warning mt-3">
                <i class="fas fa-exclamation-triangle"></i>
                Student must resolve all active sanctions before requesting a Good Moral Certificate.
            </div>
            </div>
        </div>';
    } else {
        echo '<div class="alert alert-info">No active sanctions found.</div>';
    }

} catch (Exception $e) {
    error_log("Error in get_student_sanctions.php: " . $e->getMessage());
    echo '<div class="alert alert-danger">Failed to load sanction details.</div>';
} 