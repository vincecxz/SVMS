<?php
require_once '../config/database.php';
header('Content-Type: application/json');

$response = array('success' => false, 'message' => '', 'html' => '');

if (!isset($_POST['id'])) {
    $response['message'] = 'Missing report ID';
    echo json_encode($response);
    exit;
}

$id = mysqli_real_escape_string($conn, $_POST['id']);

// Get violation report details with related information
$query = "SELECT vr.*, 
          s.full_name as student_name,
          s.id_number,
          s.email,
          s.contact_number,
          p.name as program_name,
          s.section as student_section,
          CASE 
              WHEN vr.section_type = 'section1' THEN sec1.description
              WHEN vr.section_type = 'section2' THEN sec2.description
          END as offense_description
          FROM violation_reports vr
          LEFT JOIN students s ON vr.student_id = s.id
          LEFT JOIN programs p ON s.program_id = p.id
          LEFT JOIN sec1 ON (vr.section_type = 'section1' AND vr.offense_id = sec1.id)
          LEFT JOIN sec2 ON (vr.section_type = 'section2' AND vr.offense_id = sec2.id)
          WHERE vr.id = '$id'";

$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    $report = $result->fetch_assoc();
    
    // Format dates
    $incident_date = date('F d, Y h:i A', strtotime($report['incident_datetime']));
    $created_date = date('F d, Y h:i A', strtotime($report['created_at']));
    $updated_date = date('F d, Y h:i A', strtotime($report['updated_at']));
    $resolution_date = $report['resolution_datetime'] ? date('F d, Y h:i A', strtotime($report['resolution_datetime'])) : 'Not resolved yet';
    
    // Get violation history if count > 1
    $violation_history = '';
    if ($report['violation_count'] > 1) {
        $history_query = "SELECT vr.*,
                         CASE 
                             WHEN vr.section_type = 'section1' THEN sec1.description
                             WHEN vr.section_type = 'section2' THEN sec2.description
                         END as offense_description
                         FROM violation_reports vr
                         LEFT JOIN sec1 ON (vr.section_type = 'section1' AND vr.offense_id = sec1.id)
                         LEFT JOIN sec2 ON (vr.section_type = 'section2' AND vr.offense_id = sec2.id)
                         WHERE vr.student_id = '{$report['student_id']}'
                         AND vr.section_type = '{$report['section_type']}'
                         AND vr.offense_id = '{$report['offense_id']}'
                         ORDER BY vr.incident_datetime ASC";
        
        $history_result = $conn->query($history_query);
        
        if ($history_result && $history_result->num_rows > 0) {
            $violation_history = '
            <div class="col-12">
                <h4 class="mb-0">Violation History</h4>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="bg-dark text-white">
                            <tr>
                                <th style="width: 50px;">#</th>
                                <th style="width: 150px;">Date</th>
                                <th>Offense Description</th>
                                <th>Sanction</th>
                                <th style="width: 100px;">Status</th>
                            </tr>
                        </thead>
                        <tbody>';
            
            $count = 1;
            while ($history = $history_result->fetch_assoc()) {
                $history_date = date('Y-m-d', strtotime($history['incident_datetime']));
                $violation_history .= '
                    <tr>
                        <td class="text-center">' . $count . '</td>
                        <td>' . $history_date . '</td>
                        <td>' . htmlspecialchars($history['offense_description']) . '</td>
                        <td>' . htmlspecialchars($history['sanction']) . '</td>
                        <td class="text-center">
                            <span class="badge badge-' . ($history['status'] == 'Active' ? 'danger' : ($history['status'] == 'Resolved' ? 'success' : 'orange')) . ' badge-pill">
                                ' . ($history['status'] == 'Active' ? '● Active' : ($history['status'] == 'Resolved' ? '● Resolved' : '● In progress')) . '
                            </span>
                        </td>
                    </tr>';
                $count++;
            }
            
            $violation_history .= '
                        </tbody>
                    </table>
                </div>
            </div>';
        }
    }
    
    // Build HTML for report details
    $html = '
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6">
                <h4 class="mb-0">Student Information</h4>
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 font-weight-bold">Student ID</div>
                            <div class="col-md-8">' . htmlspecialchars($report['id_number']) . '</div>
                        </div>
                        <hr class="my-2">
                        <div class="row">
                            <div class="col-md-4 font-weight-bold">Full Name</div>
                            <div class="col-md-8">' . htmlspecialchars($report['student_name']) . '</div>
                        </div>
                        <hr class="my-2">
                        <div class="row">
                            <div class="col-md-4 font-weight-bold">Program</div>
                            <div class="col-md-8">' . htmlspecialchars($report['program_name']) . '</div>
                        </div>
                        <hr class="my-2">
                        <div class="row">
                            <div class="col-md-4 font-weight-bold">Section</div>
                            <div class="col-md-8">' . htmlspecialchars($report['student_section']) . '</div>
                        </div>
                        <hr class="my-2">
                        <div class="row">
                            <div class="col-md-4 font-weight-bold">Email</div>
                            <div class="col-md-8">' . htmlspecialchars($report['email']) . '</div>
                        </div>
                        <hr class="my-2">
                        <div class="row">
                            <div class="col-md-4 font-weight-bold">Contact Number</div>
                            <div class="col-md-8">' . htmlspecialchars($report['contact_number']) . '</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <h4 class="mb-0">Current Violation Details</h4>
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 font-weight-bold">Incident Date & Time</div>
                            <div class="col-md-8">' . $incident_date . '</div>
                        </div>
                        <hr class="my-2">
                        <div class="row">
                            <div class="col-md-4 font-weight-bold">Section Type</div>
                            <div class="col-md-8">' . ($report['section_type'] == 'section1' ? 'Section 1 - Academic' : 'Section 2 - Non-Academic') . '</div>
                        </div>
                        ' . ($report['offense_level'] ? '
                        <hr class="my-2">
                        <div class="row">
                            <div class="col-md-4 font-weight-bold">Offense Level</div>
                            <div class="col-md-8">' . htmlspecialchars($report['offense_level']) . '</div>
                        </div>' : '') . '
                        <hr class="my-2">
                        <div class="row">
                            <div class="col-md-4 font-weight-bold">Offense Description</div>
                            <div class="col-md-8">' . htmlspecialchars($report['offense_description']) . '</div>
                        </div>
                        <hr class="my-2">
                        <div class="row">
                            <div class="col-md-4 font-weight-bold">Violation Count</div>
                            <div class="col-md-8">' . htmlspecialchars($report['violation_count']) . '</div>
                        </div>
                        <hr class="my-2">
                        <div class="row">
                            <div class="col-md-4 font-weight-bold">Sanction</div>
                            <div class="col-md-8">' . htmlspecialchars($report['sanction']) . '</div>
                        </div>
                        <hr class="my-2">
                        <div class="row">
                            <div class="col-md-4 font-weight-bold">Status</div>
                            <div class="col-md-8">
                                <span class="badge badge-' . ($report['status'] == 'Active' ? 'danger' : 'success') . ' badge-pill">' 
                                . ($report['status'] == 'Active' ? '● Active' : '● Resolved') . '</span>
                            </div>
                        </div>
                        ' . ($report['status'] == 'Resolved' ? '
                        <hr class="my-2">
                        <div class="row">
                            <div class="col-md-4 font-weight-bold">Resolution Date & Time</div>
                            <div class="col-md-8">' . $resolution_date . '</div>
                        </div>' : '') . '
                    </div>
                </div>

                <h4 class="mb-0 mt-3">Report Timeline</h4>
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 font-weight-bold">Created On</div>
                            <div class="col-md-8">' . $created_date . '</div>
                        </div>
                        <hr class="my-2">
                        <div class="row">
                            <div class="col-md-4 font-weight-bold">Last Updated</div>
                            <div class="col-md-8">' . $updated_date . '</div>
                        </div>
                    </div>
                </div>
            </div>

            ' . ($violation_history ? '
            <div class="col-12 mt-3">
                ' . $violation_history . '
            </div>' : '') . '
        </div>
    </div>';

    $response['success'] = true;
    $response['html'] = $html;
} else {
    $response['message'] = 'Report not found';
}

echo json_encode($response); 