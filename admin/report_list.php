<?php
include('../config/database.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Violation Reports - SASO</title>
    
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">

    <link rel="stylesheet" href="../plugins/daterangepicker/daterangepicker.css">

    <link rel="stylesheet" href="../../plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="../plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="../plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    
    <link rel="stylesheet" href="../plugins/select2/css/select2.min.css">
  <link rel="stylesheet" href="../plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="../dist/css/adminlte.min.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="../plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
<style>
    .btn-warning {
        background-color: rgb(255, 174, 0) !important;
        border-color: rgb(255, 191, 0) !important;
    }
    .bg-warning {
        background-color: rgb(255, 191, 0) !important;
    }

    /* Clean Select2 Dropdown Styling */
    .select2-container--bootstrap4 {
        width: 100% !important;
    }

    /* Input field styling */
    .select2-container--bootstrap4 .select2-selection {
        height: auto !important;
        min-height: 45px !important;
        background-color: #fff !important;
        border: 1px solid #ced4da !important;
        border-radius: 4px !important;
        transition: border-color 0.15s ease-in-out !important;
    }

    /* Dropdown styling */
    .select2-container--bootstrap4 .select2-dropdown {
        border: none !important;
        box-shadow: 0 4px 16px rgba(0,0,0,0.1) !important;
        border-radius: 8px !important;
        overflow: hidden !important;
        margin-top: 4px !important;
        background: #fff !important;
    }

    /* Search box styling */
    .select2-container--bootstrap4 .select2-search--dropdown {
        padding: 10px !important;
        background: #f8f9fa !important;
        border-bottom: 1px solid #e9ecef !important;
    }

    .select2-container--bootstrap4 .select2-search--dropdown .select2-search__field {
        border: 1px solid #ced4da !important;
        border-radius: 4px !important;
        padding: 8px 12px !important;
        height: 38px !important;
        font-size: 14px !important;
    }

    /* Options container */
    .select2-container--bootstrap4 .select2-results > .select2-results__options {
        max-height: 400px !important;
        overflow-y: auto !important;
        scrollbar-width: thin !important;
        scrollbar-color: #90A4AE #CFD8DC !important;
    }

    /* Option group headers */
    .select2-container--bootstrap4 .select2-results__group {
        padding: 12px 16px !important;
        font-size: 12px !important;
        font-weight: 600 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.5px !important;
        color: #1976D2 !important;
        background: #F5F9FF !important;
        border-bottom: 1px solid #E3F2FD !important;
        margin: 0 !important;
        position: sticky !important;
        top: 0 !important;
        z-index: 2 !important;
    }

    /* Option items */
    .select2-container--bootstrap4 .select2-results__option {
        padding: 10px 16px !important;
        margin: 0 !important;
        font-size: 14px !important;
        line-height: 1.5 !important;
        color: #37474F !important;
        border-bottom: 1px solid #f0f0f0 !important;
        transition: all 0.2s ease !important;
        position: relative !important;
        cursor: pointer !important;
        display: flex !important;
        align-items: center !important;
    }

    /* Option hover state */
    .select2-container--bootstrap4 .select2-results__option--highlighted[aria-selected] {
        background-color: #E3F2FD !important;
        color: #1976D2 !important;
    }

    /* Selected option */
    .select2-container--bootstrap4 .select2-results__option[aria-selected=true] {
        background-color: #F5F9FF !important;
        color: #1976D2 !important;
        font-weight: 500 !important;
    }

    /* Selected option icon */
    .select2-container--bootstrap4 .select2-results__option[aria-selected=true]::after {
        content: '✓' !important;
        position: absolute !important;
        right: 16px !important;
        color: #1976D2 !important;
        font-weight: bold !important;
    }

    /* Option text wrapping */
    .select2-results__option-text {
        display: block !important;
        white-space: normal !important;
        word-wrap: break-word !important;
        padding-right: 24px !important;
    }

    /* Scrollbar styling */
    .select2-results__options::-webkit-scrollbar {
        width: 6px !important;
    }

    .select2-results__options::-webkit-scrollbar-track {
        background: #f1f1f1 !important;
    }

    .select2-results__options::-webkit-scrollbar-thumb {
        background: #90A4AE !important;
        border-radius: 3px !important;
    }

    .select2-results__options::-webkit-scrollbar-thumb:hover {
        background: #78909C !important;
    }

    /* Dark mode support */
    .dark-mode .select2-container--bootstrap4 .select2-dropdown {
        background: #2d3748 !important;
        border-color: #4a5568 !important;
    }

    .dark-mode .select2-container--bootstrap4 .select2-results__option {
        color: #e2e8f0 !important;
        border-bottom-color: #4a5568 !important;
    }

    .dark-mode .select2-container--bootstrap4 .select2-results__group {
        background: #2d3748 !important;
        color: #90cdf4 !important;
        border-bottom-color: #4a5568 !important;
    }

    .dark-mode .select2-container--bootstrap4 .select2-results__option--highlighted[aria-selected] {
        background-color: #2c5282 !important;
        color: #fff !important;
    }

    .dark-mode .select2-container--bootstrap4 .select2-results__option[aria-selected=true] {
        background-color: #2a4365 !important;
        color: #90cdf4 !important;
    }

    .dark-mode .select2-container--bootstrap4 .select2-search--dropdown {
        background: #2d3748 !important;
        border-bottom-color: #4a5568 !important;
    }

    .dark-mode .select2-container--bootstrap4 .select2-search--dropdown .select2-search__field {
        background: #2d3748 !important;
        border-color: #4a5568 !important;
        color: #e2e8f0 !important;
    }

    /* Progress bar styles */
    .progress {
        height: 25px;
        border-radius: 12px;
        background-color: #e9ecef;
    }
    
    /* Select2 Dropdown Styling */
    .select2-container--default .select2-selection--single {
        height: 40px !important;
        padding: 6px 12px !important;
        border: 1px solid #ced4da !important;
        border-radius: 4px !important;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 38px !important;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 28px !important;
    }
    
    .select2-container--default .select2-results__option {
        padding: 8px 12px !important;
    }
    
    .select2-container--default .select2-search--dropdown .select2-search__field {
        border: 1px solid #ced4da !important;
        border-radius: 4px !important;
        padding: 6px 12px !important;
    }
    
    .select2-dropdown {
        border: 1px solid #ced4da !important;
        border-radius: 4px !important;
    }
    
    /* Option items */
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #E3F2FD !important;
        color: #1976D2 !important;
    }
    
    /* Add orange badge style */
    .badge-orange {
        background-color: #fd7e14 !important;
        color: white !important;
    }
</style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <?php include('../includes/admin/navbar.php'); ?>
        <?php include('../includes/admin/sidebar.php'); ?>

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <!-- Content Header -->
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1>Violation Reports</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                                <li class="breadcrumb-item active">Violation Reports</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">List of Violation Reports</h3>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-success btn-sm" id="exportExcelBtn">
                                            <i class="fas fa-file-excel"></i> Export to Excel
                                        </button>
                                        <div class="input-group input-group-sm" style="width: 150px;">
                                            
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-md-3">
                                            <select id="sectionFilter" class="form-control">
                                                <option value="">All Sections</option>
                                                <option value="section1">Section 1 - Academic</option>
                                                <option value="section2">Section 2 - Non-Academic</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <select id="levelFilter" class="form-control">
                                                <option value="">All Levels</option>
                                                <option value="Light">Light</option>
                                                <option value="Serious">Serious</option>
                                                <option value="Very Serious">Very Serious</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <select id="statusFilter" class="form-control">
                                                <option value="">All Status</option>
                                                <option value="Active">Active</option>
                                                <option value="In Progress">In Progress</option>
                                                <option value="Resolved">Resolved</option>
                                            </select>
                                        </div>
                                    </div>
                                    <table id="reportsTable" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Date & Time</th>
                                                <th>Student Name</th>
                                                <th>Section</th>
                                                <th>Level</th>
                                                <th>Offense</th>
                                                <th>Violation Count</th>
                                                <th>Sanction</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $query = "SELECT vr.*, s.full_name as student_name,
                                                    CASE 
                                                        WHEN vr.section_type = 'section1' THEN sec1.description
                                                        WHEN vr.section_type = 'section2' THEN sec2.description
                                                    END as offense_description
                                                    FROM violation_reports vr
                                                    LEFT JOIN students s ON vr.student_id = s.id
                                                    LEFT JOIN sec1 ON (vr.section_type = 'section1' AND vr.offense_id = sec1.id)
                                                    LEFT JOIN sec2 ON (vr.section_type = 'section2' AND vr.offense_id = sec2.id)
                                                    WHERE vr.id IN (
                                                        SELECT MAX(id)
                                                        FROM violation_reports
                                                        GROUP BY student_id, section_type, offense_id
                                                    )
                                                    ORDER BY vr.incident_datetime DESC";
                                            
                                            $result = $conn->query($query);
                                            
                                            while($row = $result->fetch_assoc()) {
                                                $section_display = ($row['section_type'] == 'section1') ? 'Academic' : 'Non-Academic';
                                                $level_display = $row['offense_level'] ? $row['offense_level'] : 'N/A';
                                                
                                                // Format date and time separately
                                                $date_display = date('M d, Y', strtotime($row['incident_datetime']));
                                                $time_display = date('h:i A', strtotime($row['incident_datetime']));
                                                
                                                echo "<tr>";
                                                echo "<td><div class='date-time'><div class='date'>".$date_display."</div><div class='time'>".$time_display."</div></div></td>";
                                                echo "<td>".$row['student_name']."</td>";
                                                echo "<td>".$section_display."</td>";
                                                echo "<td>".$level_display."</td>";
                                                echo "<td><span class='truncate-text' data-toggle='tooltip' data-placement='top' title='".htmlspecialchars($row['offense_description'])."'>".$row['offense_description']."</span></td>";
                                                echo "<td>".$row['violation_count']."</td>";
                                                echo "<td><span class='truncate-text' data-toggle='tooltip' data-placement='top' title='".htmlspecialchars($row['sanction'])."'>".
                                                    ($row['violation_count'] == 1 ? "1st offense" . '-' . $row['sanction'] : 
                                                    ($row['violation_count'] == 2 ? "2nd offense" . '-' . $row['sanction'] : 
                                                    ($row['violation_count'] == 3 ? "3rd offense" . '-' . $row['sanction'] : 
                                                    $row['violation_count']."th offense"))).
                                                    "</span></td>";
                                                echo "<td><span class='badge badge-".
                                                    ($row['status'] == 'Active' ? 'danger' : 
                                                    ($row['status'] == 'In Progress' ? 'orange' : 'success'))."'>".$row['status']."</span></td>";
                                                echo "<td>\n    <div class='action-buttons-wrapper'>\n        <button class='btn btn-sm btn-primary view-report' data-id='".$row['id']."'><i class='fas fa-eye'></i></button>\n        <button class='btn btn-sm btn-warning edit-report' data-id='".$row['id']."'><i class='fas fa-edit'></i></button>\n        <button class='btn btn-sm btn-success update-status' data-id='".$row['id']."' ".(($row['status'] != 'Active' && $row['status'] != 'In Progress') ? 'disabled' : '')."><i class='fas fa-check'></i></button>\n        <button class='btn btn-sm btn-danger delete-report' data-id='".$row['id']."'><i class='fas fa-trash'></i></button>";
                                                
                                                // Add progress tracking button for community service sanctions
                                                // Check if this is a first offense with community service or if there's an active first offense
                                                $show_progress_button = false;
                                                
                                                // Check if this is the first offense with community service
                                                if ($row['violation_count'] == 1 && preg_match('/(community service|university hours|university service|(\d+)\s*hours?)/i', $row['sanction'])) {
                                                    $show_progress_button = true;
                                                }
                                                
                                                // Check if there's an active first offense that requires hours
                                                $check_first_offense_sql = "SELECT vr.id, vr.sanction, vr.status 
                                                                          FROM violation_reports vr 
                                                                          WHERE vr.student_id = ? 
                                                                          AND vr.violation_count = 1 
                                                                          AND vr.status IN ('Active', 'In Progress')
                                                                          AND vr.sanction REGEXP '(community service|university hours|university service|[0-9]+\\s*hours?)'";
                                                $check_stmt = $conn->prepare($check_first_offense_sql);
                                                $check_stmt->bind_param("i", $row['student_id']);
                                                $check_stmt->execute();
                                                $active_first_result = $check_stmt->get_result();
                                                
                                                if ($active_first_result->num_rows > 0) {
                                                    $show_progress_button = true;
                                                }

                                                // Show button for Active and In Progress, hide for Resolved
                                                if (preg_match('/(community service|university hours|university service|(\d+)\s*hours?)/i', $row['sanction'])) {
                                                    if ($row['status'] === 'Active' || $row['status'] === 'In Progress') {
                                                        $show_progress_button = true;
                                                    } elseif ($row['status'] === 'Resolved') {
                                                        $show_progress_button = false;
                                                    }
                                                }

                                                if ($show_progress_button) {
                                                    // Extract hours from sanction text
                                                    $total_hours = 40; // Default value
                                                    
                                                    // First try to extract numeric hours with parentheses (e.g., "(40)")
                                                    if (preg_match('/\((\d+)\)\s*hours?/i', $row['sanction'], $matches)) {
                                                        $total_hours = intval($matches[1]);
                                                    }
                                                    // Then try just numeric hours (e.g., "40 hours")
                                                    else if (preg_match('/(\d+)\s*hours?/i', $row['sanction'], $matches)) {
                                                        $total_hours = intval($matches[1]);
                                                    }
                                                    // Finally try text numbers (e.g., "forty hours")
                                                    else if (preg_match('/\b(one|two|three|four|five|six|seven|eight|nine|ten|twenty|thirty|forty|fifty|sixty)\b/i', $row['sanction'], $matches)) {
                                                        $number_map = [
                                                            'one' => 1, 'two' => 2, 'three' => 3, 'four' => 4, 'five' => 5,
                                                            'six' => 6, 'seven' => 7, 'eight' => 8, 'nine' => 9, 'ten' => 10,
                                                            'twenty' => 20, 'thirty' => 30, 'forty' => 40, 'fifty' => 50, 'sixty' => 60
                                                        ];
                                                        $total_hours = $number_map[strtolower($matches[1])];
                                                    }
                                                    
                                                    echo "<button class='btn btn-sm btn-info track-progress' 
                                                          data-id='".$row['id']."' 
                                                          data-student='".$row['student_name']."' 
                                                          data-sanction='".$row['sanction']."'
                                                          data-total-hours='".$total_hours."'
                                                          data-status='".$row['status']."'>
                                                          <i class='fas fa-clock'></i>
                                                    </button>";
                                                } else {
                                                    // Add placeholder for consistent spacing when no track-progress button
                                                    echo "<button class='btn btn-sm invisible' style='width: 24px;'></button>";
                                                }
                                                
                                                echo "</div></td>";
                                                echo "</tr>";
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <!-- View Report Modal -->
        <div class="modal fade" id="viewReportModal"  data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header bg-primary">
                        <h5 class="modal-title text-white">Violation Report Details</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body p-0">
                        <div id="reportDetails">
                            <!-- Report details will be loaded here -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Report Modal -->
        <div class="modal fade" id="addReportModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary">
                        <h5 class="modal-title text-black">Add Violation Report</h5>
                        <button type="button" class="close text-black" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="violationForm">
                            <div class="row">
                                <!-- Left Column -->
                                <div class="col-md-6">
                                    <!-- Student Selection -->
                                    <div class="form-group">
                                        <label for="student">Select Student</label>
                                        <select class="form-control select2" id="student" name="student" required>
                                            <?php
                                            $query = "SELECT * FROM students ORDER BY full_name ASC";
                                            $result = $conn->query($query);
                                            while($row = $result->fetch_assoc()) {
                                                echo "<option value='".$row['id']."'>".$row['full_name']."</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>

                                    <!-- Section Selection -->
                                    <div class="form-group">
                                        <label for="section">Section</label>
                                        <select class="form-control" id="section" name="section" required>
                                            <option value="">Select Section</option>
                                            <option value="section1">Section 1 - Academic</option>
                                            <option value="section2">Section 2 - Non-Academic</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <!-- Right Column -->
                                <div class="col-md-6">
                                    <!-- Date and time -->
                                    <div class="form-group">
                                        <label for="incident_date">Incident Date</label>
                                        <div class="input-group" id="incident_datetime_picker" data-target-input="nearest">
                                            <input type="text" class="form-control datetimepicker-input" data-target="#incident_datetime_picker" name="incident_datetime" required placeholder="MM/DD/YYYY HH:MM AM/PM"/>
                                            <div class="input-group-append" data-target="#incident_datetime_picker" data-toggle="datetimepicker">
                                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Offense Level -->
                                    <div class="form-group">
                                        <label for="offense_level">Offense Level</label>
                                        <select class="form-control" id="offense_level" name="offense_level" disabled required>
                                            <option value="">SELECT OFFENSE LEVEL</option>
                                            <option value="Light">Light</option>
                                            <option value="Serious">Serious</option>
                                            <option value="Very Serious">Very Serious</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Full Width Fields -->
                            <div class="row">
                                <div class="col-md-12">
                                    <!-- Offense Selection -->
                                    <div class="form-group">
                                        <label for="offense">Offense</label>
                                        <select class="form-control select2" id="offense" name="offense" required style="width: 100%;">
                                            <option value="">Select an offense</option>
                                        </select>
                                    </div>

                                    <!-- Sanction Display -->
                                    <div class="form-group">
                                        <label>Corresponding Sanction</label>
                                        <input type="text" class="form-control" id="sanction_display" readonly>
                                    </div>

                                    <!-- Custom Sanction Option -->
                                    <div class="form-group mb-3">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="use_custom_sanction">
                                            <label class="custom-control-label" for="use_custom_sanction">Use Custom Sanction</label>
                                        </div>
                                    </div>

                                    <!-- Custom Sanction Input (Hidden by default) -->
                                    <div class="form-group mb-3" id="custom_sanction_container" style="display: none;">
                                        <label class="font-weight-bold">Custom Sanction</label>
                                        <textarea class="form-control" id="custom_sanction" rows="3" placeholder="Example: Twenty (20) hours Community Service within four (4) weeks"></textarea>
                                        <small class="form-text text-muted">
                                            <strong>Format Guidelines:</strong><br>
                                            1. Describe the sanction clearly<br>
                                            2. Do not include the offense number (it will be added automatically)<br>
                                            3. Examples:<br>
                                            - "Twenty (20) hours Community Service within four (4) weeks"<br>
                                            - "Five (5) hours University Service and written warning"<br>
                                            - "Community Service with counseling"
                                        </small>
                                    </div>

                                    <!-- University Service Hours -->
                                    <div class="form-group mb-3" id="service_hours_container" style="display: none;">
                                        <label class="font-weight-bold">University Service Hours</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" id="service_hours" name="service_hours" min="1">
                                            <div class="input-group-append">
                                                <span class="input-group-text">hours</span>
                                            </div>
                                        </div>
                                        <small class="form-text text-muted">Enter the number of required service hours (this number will be used for tracking progress)</small>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                            <!-- Submit Button -->
                                     <button type="submit" class="btn btn-primary">Add Violation</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Report Modal -->
        <div class="modal fade" id="editReportModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-warning">
                        <h5 class="modal-title text-dark font-weight-bold">Edit Violation Report</h5>
                        <button type="button" class="close text-dark" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="editViolationForm">
                            <input type="hidden" id="edit_violation_id" name="violation_id">
                            <input type="hidden" id="edit_student_id" name="student">

                            <div class="row mb-3">
                                <!-- Left Column -->
                                <div class="col-md-6">
                                    <!-- Student Information -->
                                    <div class="form-group">
                                        <label class="font-weight-bold">Student</label>
                                        <input type="text" class="form-control" id="edit_student" readonly>
                                    </div>

                                    <!-- Incident Date -->
                                    <div class="form-group">
                                        <label class="font-weight-bold">Incident Date</label>
                                        <div class="input-group" id="edit_incident_datetime_picker" data-target-input="nearest">
                                            <input type="text" class="form-control datetimepicker-input" 
                                                   data-target="#edit_incident_datetime_picker" required
                                                   placeholder="MM/DD/YYYY HH:MM AM/PM">
                                            <div class="input-group-append" data-target="#edit_incident_datetime_picker" 
                                                 data-toggle="datetimepicker">
                                                <div class="input-group-text"><i class="fas fa-calendar"></i></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Right Column -->
                                <div class="col-md-6">
                                    <!-- Section -->
                                    <div class="form-group">
                                        <label class="font-weight-bold">Section</label>
                                        <select class="form-control" id="edit_section" name="section" required>
                                            <option value="">Select Section</option>
                                            <option value="section1">Section 1 - Academic</option>
                                            <option value="section2">Section 2 - Non-Academic</option>
                                        </select>
                                    </div>

                                    <!-- Offense Level -->
                                    <div class="form-group">
                                        <label class="font-weight-bold">Offense Level</label>
                                        <select class="form-control" id="edit_offense_level" name="offense_level" disabled required>
                                            <option value="">Select Offense Level</option>
                                            <option value="Light">Light</option>
                                            <option value="Serious">Serious</option>
                                            <option value="Very Serious">Very Serious</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Full Width Fields -->
                            <div class="row">
                                <div class="col-12">
                                    <!-- Offense -->
                                    <div class="form-group">
                                        <label class="font-weight-bold">Offense</label>
                                        <select class="form-control select2" id="edit_offense" name="offense" required style="width: 100%;">
                                            <option value="">Select Offense</option>
                                        </select>
                                    </div>

                                    <!-- Corresponding Sanction -->
                                    <div class="form-group mb-3">
                                        <label class="font-weight-bold">Corresponding Sanction</label>
                                        <input type="text" class="form-control" id="edit_sanction_display" readonly>
                                    </div>

                                    <!-- Custom Sanction Option -->
                                    <div class="form-group mb-3">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="edit_use_custom_sanction">
                                            <label class="custom-control-label" for="edit_use_custom_sanction">Use Custom Sanction</label>
                                        </div>
                                    </div>

                                    <!-- Custom Sanction Input (Hidden by default) -->
                                    <div class="form-group mb-3" id="edit_custom_sanction_container" style="display: none;">
                                        <label class="font-weight-bold">Custom Sanction</label>
                                        <textarea class="form-control" id="edit_custom_sanction" rows="3" placeholder="Example: Twenty (20) hours Community Service within four (4) weeks"></textarea>
                                        <small class="form-text text-muted">
                                            <strong>Format Guidelines:</strong><br>
                                            1. Describe the sanction clearly<br>
                                            2. Do not include the offense number (it will be added automatically)<br>
                                            3. Examples:<br>
                                            - "Twenty (20) hours Community Service within four (4) weeks"<br>
                                            - "Five (5) hours University Service and written warning"<br>
                                            - "Community Service with counseling"
                                        </small>
                                    </div>

                                    <!-- University Service Hours -->
                                    <div class="form-group mb-3" id="edit_service_hours_container" style="display: none;">
                                        <label class="font-weight-bold">University Service Hours</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" id="edit_service_hours" name="service_hours" min="1">
                                            <div class="input-group-append">
                                                <span class="input-group-text">hours</span>
                                            </div>
                                        </div>
                                        <small class="form-text text-muted">Enter the number of required service hours (this number will be used for tracking progress)</small>
                                    </div>
                                </div>
                            </div>

                            <div class="text-right">
                                <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button> -->
                                <button type="submit" class="btn btn-warning ml-2">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Progress Tracking Modal -->
        <div class="modal fade" id="progressModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-info">
                        <h5 class="modal-title text-white">Update University Service Progress</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="progressForm">
                            <input type="hidden" id="violation_id" name="violation_id">
                            <div class="form-group">
                                <label class="d-inline">Student Name:</label>
                                <span id="student_name" class="font-weight ml-2"></span>
                            </div>
                            <div class="form-group">
                                <label>Sanction:</label>
                                <p id="sanction_text" class="font-weight"></p>
                            </div>  
                            <div class="form-group">
                                <label>Current Progress:</label>
                                <div class="progress mb-2" style="height: 25px; border-radius: 5px;">
                                    <div class="progress-bar bg-info" role="progressbar" style="width: 0%; border-radius: 5px;"></div>
                                </div>
                                <p id="progress_text" class="text-center">0 / 0 hours completed</p>
                            </div>
                            <div class="form-group">
                                <label for="hours_completed">Add Hours:</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="hours_completed" name="hours_completed" step="0.5" min="0.5" required>
                                    <div class="input-group-append">
                                        <span class="input-group-text">hours</span>
                                    </div>
                                </div>
                                <div id="hours-validation-message" class="mt-2" style="display: none;">
                                    <small class="text-danger">
                                        <i class="fas fa-exclamation-circle"></i>
                                        <span id="hours-validation-text"></span>
                                    </small>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="service_date">Date and Time:</label>
                                <div class="input-group" id="service_datetime_picker" data-target-input="nearest">
                                    <input type="text" class="form-control datetimepicker-input" id="service_date" name="service_date" data-target="#service_datetime_picker" required placeholder="MM/DD/YYYY HH:MM AM/PM"/>
                                    <div class="input-group-append" data-target="#service_datetime_picker" data-toggle="datetimepicker">
                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="remarks">Remarks:</label>
                                <textarea class="form-control" id="remarks" name="remarks" rows="3"></textarea>
                            </div>
                            
                            <div class="d-flex justify-content-end mb-3">
                                <button type="submit" class="btn btn-info">Update Progress</button>
                            </div>
                        </form>
                        
                        <hr>
                        <h5>Progress History</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-sm">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Hours</th>
                                        <th>Remarks</th>
                                    </tr>
                                </thead>
                                <tbody id="progress_history">
                                    <!-- Progress history will be loaded here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- View Report Modal -->
        <div class="modal fade" id="resolutionDateModal"  data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-success">
                        <h5 class="modal-title text-white">Set Resolution Date & Time</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="resolutionForm">
                            <input type="hidden" id="violation_id_for_resolution">
                            <div class="form-group">
                                <label for="resolution_datetime">Resolution Date & Time:</label>
                                <div class="input-group" id="resolution_datetime_picker" data-target-input="nearest">
                                    <input type="text" class="form-control datetimepicker-input" id="resolution_datetime" name="resolution_datetime" data-target="#resolution_datetime_picker" required placeholder="MM/DD/YYYY HH:MM AM/PM"/>
                                    <div class="input-group-append" data-target="#resolution_datetime_picker" data-toggle="datetimepicker">
                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success float-right">Mark as Resolved</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Report Button -->
        <button class="btn-float" onclick="$('#addReportModal').modal('show')">
            <i class="fas fa-plus"></i>
            <span class="btn-float-label">Add Report</span>
        </button>

        <!-- Export Options Modal -->
        <div class="modal fade" id="exportOptionsModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Export Options</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form method="post" action="export_violation_reports.php">
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Sort By</label>
                                <select class="form-control" name="sort_by" required>
                                    <option value="name">Student Name</option>
                                    <option value="date">Date and Time</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Sort Order</label>
                                <select class="form-control" name="sort_order" required>
                                    <option value="asc">Ascending</option>
                                    <option value="desc">Descending</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success">Export</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <?php include('../includes/admin/footer.php'); ?>
    </div>

    <!-- jQuery -->
    <script src="../plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Select2 -->
    <script src="../plugins/select2/js/select2.full.min.js"></script>

    <!-- InputMask -->
    <script src="../plugins/moment/moment.min.js"></script>
    <script src="../plugins/inputmask/jquery.inputmask.min.js"></script>
    <!-- date-range-picker -->
    <script src="../plugins/daterangepicker/daterangepicker.js"></script>

    <!-- Tempusdominus Bootstrap 4 -->
    <script src="../plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
    <!-- Select2 -->
    <script src="../plugins/select2/js/select2.full.min.js"></script>
    <!-- DataTables -->
    <script src="../plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="../plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="../plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    
    <!-- AdminLTE App -->
    <script src="../dist/js/adminlte.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="../plugins/sweetalert2/sweetalert2.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(function () {
            //Date and time picker
            $('#reservationdatetime').datetimepicker({ icons: { time: 'far fa-clock' } });
        });
    

    $(document).ready(function() {
        // Initialize DataTable
        var table = $('#reportsTable').DataTable({
            "responsive": true,
            "autoWidth": false,
            "order": [[0, "desc"]],
            "columnDefs": [
                {
                    "targets": -1,
                    "orderable": false
                }
            ]
        });

        // Custom filtering function for section type
        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
            var selectedSection = $('#sectionFilter').val();
            var selectedLevel = $('#levelFilter').val();
            var selectedStatus = $('#statusFilter').val();
            
            var rowSection = data[2];  // Section column
            var rowLevel = data[3];    // Level column
            var rowStatus = data[7];   // Status column
            
            // Check each filter
            var sectionMatch = !selectedSection || 
                             (selectedSection === 'section1' && rowSection === 'Academic') || 
                             (selectedSection === 'section2' && rowSection === 'Non-Academic');
            var levelMatch = !selectedLevel || rowLevel === selectedLevel;
            var statusMatch = !selectedStatus || rowStatus.includes(selectedStatus);
            
            return sectionMatch && levelMatch && statusMatch;
        });

        // Apply filters
        $('#sectionFilter, #levelFilter, #statusFilter').on('change', function() {
            table.draw();
        });

        // View Report Details - Use event delegation
        $('#reportsTable').on('click', '.view-report', function() {
            const id = $(this).data('id');
            $.ajax({
                url: 'get_report_details.php',
                method: 'POST',
                data: { id: id },
                dataType: 'json',
                success: function(response) {
                    if(response.success) {
                        $('#reportDetails').html(response.html);
                        $('#viewReportModal').modal('show');
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'Failed to load report details'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while loading the report details'
                    });
                    console.error(error);
                }
            });
        });

        // Update Status - Use event delegation
        $('#reportsTable').on('click', '.update-status', function() {
            const id = $(this).data('id');
            $('#violation_id_for_resolution').val(id);
            $('#resolutionDateModal').modal('show');
        });

        // Handle resolution form submission
        $('#resolutionForm').on('submit', function(e) {
            e.preventDefault();
            const id = $('#violation_id_for_resolution').val(); 
            const resolution_datetime = $('#resolution_datetime').val();

            if (!resolution_datetime) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Please select a resolution date and time'
                });
                return;
            }

            $.ajax({
                url: 'update_violation_status.php',
                method: 'POST',
                data: { 
                    id: id,
                    status: 'Resolved',
                    resolution_datetime: resolution_datetime
                },
                dataType: 'json',
                success: function(response) {
                    if(response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Violation status updated successfully'
                        }).then(() => {
                            $('#resolutionDateModal').modal('hide');
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'Failed to update status'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while updating the status'
                    });
                    console.error(error);
                }
            });
        });

        // Initialize resolution datetime picker
        $('#resolution_datetime_picker').datetimepicker({
            format: 'MM/DD/YYYY hh:mm A',
            icons: {
                time: 'far fa-clock',
                date: 'far fa-calendar',
                up: 'fas fa-chevron-up',
                down: 'fas fa-chevron-down',
                previous: 'fas fa-chevron-left',
                next: 'fas fa-chevron-right',
                today: 'far fa-calendar-check',
                clear: 'fas fa-trash',
                close: 'fas fa-times'
            },
            buttons: {
                showToday: true,
                showClear: true,
                showClose: true
            },
            useCurrent: true,
            keepOpen: false,
            allowInputToggle: true,
            focusOnShow: true,
            widgetPositioning: {
                horizontal: 'auto',
                vertical: 'bottom'
            }
        });

        // Reset form when modal is closed
        $('#resolutionDateModal').on('hidden.bs.modal', function() {
            $('#resolutionForm')[0].reset();
            $('#violation_id_for_resolution').val('');
        });

        // Add these variables at the top of your script
        let currentViolationHours = 0;
        let currentCompletedHours = 0;
        let totalRequiredHours = 0;
        let totalCompletedHours = 0;
        let allViolationsHours = [];

        // Modify your existing track-progress click handler
        $(document).on('click', '.track-progress', function() {
            const id = $(this).data('id');
            const student = $(this).data('student');
            const sanction = $(this).data('sanction');
            
            // Reset form and clear previous data
            $('#progressForm')[0].reset();
            $('#violation_id').val(id);
            $('#student_name').text('Loading...');
            $('#sanction_text').text('Loading...');
            $('.progress-bar').css('width', '0%').text('0%');
            $('#progress_text').text('Loading...');
            $('#progress_history').html('<tr><td colspan="3" class="text-center">Loading...</td></tr>');
            
            // Show modal
            $('#progressModal').modal('show');
            
            // Fetch current progress
            $.ajax({
                url: 'get_community_service_progress.php',
                method: 'POST',
                data: { violation_id: id },
                dataType: 'json',
                success: function(response) {
                    console.log('Progress data:', response);
                    
                    if (response.success) {
                        // Store the hours information globally
                        totalRequiredHours = parseFloat(response.total_required_hours) || 0;
                        totalCompletedHours = parseFloat(response.total_completed_hours) || 0;
                        allViolationsHours = [];
                        currentViolationHours = 0;
                        currentCompletedHours = 0;
                        
                        // Store all violations hours information
                        if (response.violations && response.violations.length > 0) {
                            response.violations.forEach(v => {
                                if (v.total_hours !== null) {
                                    allViolationsHours.push({
                                        id: v.id,
                                        total_hours: parseFloat(v.total_hours) || 0,
                                        completed_hours: parseFloat(v.completed_hours) || 0
                                    });
                                }
                            });
                            
                            // Calculate total hours for all active sanctions
                            allViolationsHours.forEach(v => {
                                currentViolationHours += parseFloat(v.total_hours) || 0;
                                currentCompletedHours += parseFloat(v.completed_hours) || 0;
                            });
                        }
                        
                        // Update student and violation info
                        $('#student_name').text(response.current_violation.student_name || student);
                        
                        // Show all related sanctions
                        let sanctionsText = '';
                        if (response.violations && response.violations.length > 0) {
                            response.violations.forEach((v, index) => {
                                // Only show sanctions that have hours requirement
                                if (v.total_hours !== null) {
                                    sanctionsText += `${index + 1}. ${v.sanction} (${v.completed_hours}/${v.total_hours} hours completed)`;
                                    if (v.status === 'In Progress') {
                                        sanctionsText += ' - In Progress';
                                    }
                                    sanctionsText += '<br>';
                                }
                            });
                        }
                        if (!sanctionsText) {
                            sanctionsText = 'No active service hours required.';
                        }
                        $('#sanction_text').html(sanctionsText);
                        
                        // Calculate total hours and completed hours
                        const totalHours = parseFloat(response.total_required_hours) || 0;
                        const completedHours = parseFloat(response.total_completed_hours) || 0;
                        const remainingHours = Math.max(0, totalHours - completedHours);
                        
                        // Update progress bar and text
                        if (totalHours > 0) {
                            const percentComplete = Math.min(100, (completedHours / totalHours) * 100);
                            $('.progress-bar')
                                .css('width', percentComplete + '%')
                                .text(percentComplete.toFixed(1) + '%')
                                .removeClass('bg-info bg-success bg-warning')
                                .addClass(percentComplete >= 100 ? 'bg-success' : 
                                        percentComplete >= 50 ? 'bg-info' : 'bg-warning');
                            
                            $('#progress_text').text(
                                `${completedHours.toFixed(1)} / ${totalHours.toFixed(1)} total hours completed`
                            );
                        } else {
                            // Hide progress bar when no hours are required
                            $('.progress-bar')
                                .css('width', '0%')
                                .text('');
                            $('#progress_text').text('No active service hours required');
                        }
                        
                        // Update progress history with sanction info
                        if (response.history && response.history.length > 0) {
                            let historyHtml = '';
                            let totalHoursCompleted = 0;
                            
                            response.history.forEach(function(entry) {
                                totalHoursCompleted += parseFloat(entry.hours_completed);
                                const remainingHours = Math.max(0, entry.total_hours - totalHoursCompleted);
                                
                                historyHtml += `<tr>
                                    <td>${entry.service_date}</td>
                                    <td>${parseFloat(entry.hours_completed).toFixed(1)}</td>
                                    <td>
                                        ${entry.remarks || '-'}<br>
                                        <small class="text-muted">
                                            Updated by: ${entry.updated_by || 'System'}
                                        </small>
                                    </td>
                                </tr>`;
                            });
                            $('#progress_history').html(historyHtml);
                        } else {
                            $('#progress_history').html('<tr><td colspan="3" class="text-center">No progress history available</td></tr>');
                        }
                        
                        // Show completion message only if ALL active sanctions are completed
                        if (totalHours > 0 && remainingHours <= 0) {
                            if (!$('#completion_message').length) {
                                $('#progressForm').prepend(
                                    '<div id="completion_message" class="alert alert-success mb-3">' +
                                    '<i class="fas fa-check-circle mr-2"></i>' +
                                    'All required service hours have been completed!' +
                                    '</div>'
                                ); 
                            }
                            $('#progressForm button[type="submit"]').prop('disabled', true);
                            $('#hours_completed').prop('disabled', true);
                            $('#service_datetime').prop('disabled', true);
                            $('#remarks').prop('disabled', true);
                        } else {
                            $('#completion_message').remove();
                            $('#progressForm button[type="submit"]').prop('disabled', false);
                            $('#hours_completed').prop('disabled', false);
                            $('#service_datetime').prop('disabled', false);
                            $('#remarks').prop('disabled', false);
                        }
                    } else {
                        // Show error in the modal
                        $('#student_name').text('Error loading data');
                        $('#sanction_text').text('Error loading data');
                        $('#progress_text').text('Error loading progress data');
                        $('#progress_history').html('<tr><td colspan="3" class="text-center text-danger">Failed to load progress data</td></tr>');
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'Failed to load progress data'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    // Show error in the modal
                    $('#student_name').text('Error loading data');
                    $('#sanction_text').text('Error loading data');
                    $('#progress_text').text('Error loading progress data');
                    $('#progress_history').html('<tr><td colspan="3" class="text-center text-danger">Failed to load progress data</td></tr>');
                    
                    console.error('Load progress error:', error);
                    console.error('Server response:', xhr.responseText);
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to load progress data. Please try again.'
                    });
                }
            });
        });

        // Update the hours validation logic
        $('#hours_completed').on('input', function() {
            const inputHours = parseFloat($(this).val()) || 0;
            const validationMessage = $('#hours-validation-message');
            const validationText = $('#hours-validation-text');
            const submitButton = $('#progressForm button[type="submit"]');

            // Calculate total remaining hours across all sanctions
            let totalRemainingHours = currentViolationHours - currentCompletedHours;

            if (inputHours <= 0) {
                validationMessage.show();
                validationText.html('Hours must be greater than 0');
                submitButton.prop('disabled', true);
            } else if (inputHours > totalRemainingHours) {
                validationMessage.show();
                validationText.html(`Cannot exceed remaining hours (${totalRemainingHours.toFixed(1)} hours remaining)`);
                submitButton.prop('disabled', true);
            } else {
                validationMessage.hide();
                submitButton.prop('disabled', false);
            }
        });

        // Reset validation when modal is closed
        $('#progressModal').on('hidden.bs.modal', function() {
            $('#hours-validation-message').hide();
            $('#progressForm button[type="submit"]').prop('disabled', false);
            currentViolationHours = 0;
            currentCompletedHours = 0;
            totalRequiredHours = 0;
            totalCompletedHours = 0;
            allViolationsHours = [];
        });

        // Handle progress form submission
        $('#progressForm').on('submit', function(e) {
            e.preventDefault();
            
            const formData = {
                violation_id: $('#violation_id').val(),
                hours_completed: $('#hours_completed').val(),
                service_date: $('#service_date').val(),
                remarks: $('#remarks').val()
            };
            
            if (!formData.hours_completed || !formData.service_date) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Please fill in all required fields'
                });
                return;
            }
            
            // Show loading state
            const submitBtn = $(this).find('button[type="submit"]');
            const originalText = submitBtn.text();
            submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Updating...');
            
            $.ajax({
                url: 'update_community_service_progress.php',
                method: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    submitBtn.prop('disabled', false).text(originalText);
                    
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Progress updated successfully'
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'Failed to update progress'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    submitBtn.prop('disabled', false).text(originalText);
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while updating progress. Please try again.'
                    });
                    
                    console.error('Update progress error:', error);
                    console.error('Server response:', xhr.responseText);
                }
            });
        });

        // Initialize Select2 with custom options
        $('#student').select2({
            // theme: 'bootstrap4',
            placeholder: 'Select Student',
            allowClear: true,
            width: '100%',
            minimumInputLength: 1,
            dropdownParent: $('#addReportModal')
        });

        // Initialize offense dropdown with Select2
        $('#offense').select2({
            placeholder: 'Select an offense',
            allowClear: true,
            width: '100%',
            dropdownParent: $('#addReportModal')
        });

        // Initialize DateTime Picker with custom options
        $('#incident_datetime_picker').datetimepicker({
            format: 'MM/DD/YYYY hh:mm A',
            icons: {
                time: 'far fa-clock',
                date: 'far fa-calendar',
                up: 'fas fa-chevron-up',
                down: 'fas fa-chevron-down',
                previous: 'fas fa-chevron-left',
                next: 'fas fa-chevron-right',
                today: 'far fa-calendar-check',
                clear: 'fas fa-trash',
                close: 'fas fa-times'
            },
            buttons: {
                showToday: true,
                showClear: true,
                showClose: true
            },
            useCurrent: false,
            keepOpen: false,
            allowInputToggle: true,
            focusOnShow: true,
            widgetPositioning: {
                horizontal: 'auto',
                vertical: 'bottom'
            }
        });

        // Add active class to selected date
        $(document).on('click', '.day', function() {
            $('.day').removeClass('active selected');
            $(this).addClass('active selected');
        });

        // Handle section change
        $('#section').change(function() {
            const selectedSection = $(this).val();
            const offenseLevelSelect = $('#offense_level');
            const offenseSelect = $('#offense');

            // Clear offense selection
            offenseSelect.empty().append('<option value="">Select Offense</option>');

            if (selectedSection === 'section1') {
                // Section 1 - Academic
                offenseLevelSelect.prop('disabled', true);
                offenseLevelSelect.val('');
                
                // Load Section 1 offenses
                $.ajax({
                    url: 'get_offenses.php',
                    method: 'POST',
                    data: { section: 'section1' },
                    success: function(response) {
                        if (response.success) {
                            response.data.forEach(function(offense) {
                                offenseSelect.append(
                                    `<option value="${offense.id}">${offense.description}</option>`
                                );
                            });
                            // Reinitialize Select2 for offense dropdown
                            offenseSelect.trigger('change');
                        }
                    }
                });
            } else if (selectedSection === 'section2') {
                // Section 2 - Non-Academic
                offenseLevelSelect.prop('disabled', false);
                
                // Clear offense selection until offense level is selected
                offenseLevelSelect.val('');
            } else {
                // No section selected
                offenseLevelSelect.prop('disabled', true);
                offenseLevelSelect.val('');
            }
        });

        // Handle offense level change for Section 2
        $('#offense_level').change(function() {
            const selectedLevel = $(this).val();
            const offenseSelect = $('#offense');
            const selectedSection = $('#section').val();

            if (selectedSection === 'section2' && selectedLevel) {
                // Load Section 2 offenses based on level
                $.ajax({
                    url: 'get_offenses.php',
                    method: 'POST',
                    data: { 
                        section: 'section2',
                        level: selectedLevel
                    },
                    success: function(response) {
                        offenseSelect.empty().append('<option value="">Select Offense</option>');
                        if (response.success) {
                            response.data.forEach(function(offense) {
                                offenseSelect.append(
                                    `<option value="${offense.id}">${offense.description}</option>`
                                );
                            });
                            // Reinitialize Select2 for offense dropdown
                            offenseSelect.trigger('change');
                        }
                    }
                });
            }
        });

        // Handle offense change
        $('#offense').change(function() {
            const selectedOffense = $(this).val();
            const selectedStudent = $('#student').val();
            const selectedSection = $('#section').val();
            const selectedLevel = $('#offense_level').val();
            
            if (selectedOffense && selectedStudent) {
                $.ajax({
                    url: 'get_sanction.php',
                    method: 'POST',
                    data: {
                        student_id: selectedStudent,
                        offense_id: selectedOffense,
                        section: selectedSection,
                        level: selectedLevel
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#sanction_display').val(response.sanction);
                            
                            // Show service hours input if it's 4th offense or beyond
                            if (response.sanction.match(/(\d+)(?:st|nd|rd|th)\s+offense/i)) {
                                const offenseNumber = parseInt(response.sanction.match(/(\d+)(?:st|nd|rd|th)\s+offense/i)[1]);
                                if (offenseNumber >= 4) {
                                    $('#service_hours_container').show();
                                    
                                    // Extract hours from sanction text if available
                                    const hoursMatch = response.sanction.match(/(\d+)\s*hours?/i);
                                    if (hoursMatch) {
                                        $('#service_hours').val(hoursMatch[1]);
                                    }
                                } else {
                                    $('#service_hours_container').hide();
                                    $('#service_hours').val('');
                                }
                            } else {
                                $('#service_hours_container').hide();
                                $('#service_hours').val('');
                            }
                        } else {
                            $('#sanction_display').val('');
                            $('#service_hours_container').hide();
                            $('#service_hours').val('');
                        }
                    },
                    error: function() {
                        $('#sanction_display').val('Error fetching sanction');
                        $('#service_hours_container').hide();
                        $('#service_hours').val('');
                    }
                });
            } else {
                $('#sanction_display').val('');
                $('#service_hours_container').hide();
                $('#service_hours').val('');
            }
        });

        // Handle form submission
        $('#violationForm').on('submit', function(e) {
            e.preventDefault();
            
            const formData = {
                student: $('#student').val(),
                incident_datetime: $('#incident_datetime_picker input').val(),
                section: $('#section').val(),
                offense_level: $('#offense_level').val(),
                offense: $('#offense').val(),
                sanction: $('#use_custom_sanction').is(':checked') ? $('#custom_sanction').val() : $('#sanction_display').val(),
                service_hours: $('#service_hours').val() || null,
                is_custom_sanction: $('#use_custom_sanction').is(':checked')
            };

            // Validate form data
            if (!formData.student || !formData.incident_datetime || !formData.section || !formData.offense) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Please fill in all required fields'
                });
                return;
            }

            // Validate custom sanction if enabled
            if (formData.is_custom_sanction) {
                if (!formData.sanction.trim()) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Please enter a custom sanction description'
                    });
                    return;
                }
                
                // Validate service hours if mentioned in the sanction
                if ((formData.sanction.match(/(\d+)\s*hours?/i) || 
                     formData.sanction.toLowerCase().includes('service')) && 
                    (!formData.service_hours || formData.service_hours <= 0)) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Please enter the number of service hours in the Service Hours field'
                    });
                    return;
                }
            }

            // Show loading state
            Swal.fire({
                title: 'Processing...',
                text: 'Please wait while we save the report and send the notification.',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Submit form using AJAX
            $.ajax({
                url: 'save_violation.php',
                method: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {

                        let message = 'Violation report has been saved successfully.';
                        let icon = 'success';
                        // Simple success message without email
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Violation report has been saved successfully.'
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.close();  // Close the loading dialog
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'Failed to save violation report'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    Swal.close();  // Close the loading dialog
                    console.error('Save AJAX error:', error);
                    console.log('XHR:', xhr.responseText);  // Log the full response
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while processing your request. Please check the system logs for details.'
                    });
                }
            });
        });

        // Reset form when modal is closed
        $('#addReportModal').on('hidden.bs.modal', function() {
            $('#violationForm')[0].reset();
            $('#student').val('').trigger('change');
            $('#offense').empty().append('<option value="">Select Offense</option>');
            $('#sanction_display').val('');
        });

        // Reinitialize Select2 when the modal is shown
        $('#addReportModal').on('shown.bs.modal', function() {
            $('#student').select2({
                placeholder: 'Select Student',
                allowClear: true,
                width: '100%',
                minimumInputLength: 1,
                dropdownParent: $('#addReportModal')
            });
            
            $('#offense').select2({
                placeholder: 'Select an offense',
                allowClear: true,
                width: '100%',
                dropdownParent: $('#addReportModal')
            });
        });

        // Initialize service date picker
        $('#service_datetime_picker').datetimepicker({
            format: 'MM/DD/YYYY hh:mm A',
            icons: {
                time: 'far fa-clock',
                date: 'far fa-calendar',
                up: 'fas fa-chevron-up',
                down: 'fas fa-chevron-down',
                previous: 'fas fa-chevron-left',
                next: 'fas fa-chevron-right',
                today: 'far fa-calendar-check',
                clear: 'fas fa-trash',
                close: 'fas fa-times'
            },
            buttons: {
                showToday: true,
                showClear: true,
                showClose: true
            },
            useCurrent: false,
            keepOpen: false,
            allowInputToggle: true,
            focusOnShow: true,
            widgetPositioning: {
                horizontal: 'auto',
                vertical: 'bottom'
            }
        });

        // Initialize tooltips
        $('[data-toggle="tooltip"]').tooltip({
            container: 'body',
            html: true
        });

        // Reinitialize tooltips after DataTable operations
        $('#reportsTable').on('draw.dt', function() {
            $('[data-bs-toggle="tooltip"]').tooltip({
                container: 'body',
                html: true
            });
        });

        // Initialize edit datetime picker
        $('#edit_incident_datetime_picker').datetimepicker({
            format: 'MM/DD/YYYY hh:mm A',
            icons: {
                time: 'far fa-clock',
                date: 'far fa-calendar',
                up: 'fas fa-chevron-up',
                down: 'fas fa-chevron-down',
                previous: 'fas fa-chevron-left',
                next: 'fas fa-chevron-right',
                today: 'far fa-calendar-check',
                clear: 'fas fa-trash',
                close: 'fas fa-times'
            },
            buttons: {
                showToday: true,
                showClear: true,
                showClose: true
            }
        });

        // Handle edit button click
        $('#reportsTable').on('click', '.edit-report', function() {
            const id = $(this).data('id');
            
            // Reset custom sanction fields
            $('#edit_use_custom_sanction').prop('checked', false);
            $('#edit_custom_sanction_container').hide();
            $('#edit_service_hours_container').hide();
            $('#edit_custom_sanction').val('');
            $('#edit_service_hours').val('');
            
            // Fetch violation details
            $.ajax({
                url: 'get_violation_details.php',
                method: 'POST',
                data: { id: id },
                dataType: 'json',
                success: function(response) {
                    if(response.success) {
                        const data = response.data;
                        
                        // Populate the edit form
                        $('#edit_violation_id').val(data.id);
                        $('#edit_student').val(data.student_name);
                        $('#edit_student_id').val(data.student_id);
                        $('#edit_incident_datetime_picker input').val(moment(data.incident_datetime).format('MM/DD/YYYY hh:mm A'));
                        $('#edit_section').val(data.section_type).trigger('change');
                        
                        if(data.section_type === 'section2') {
                            $('#edit_offense_level').prop('disabled', false);
                            $('#edit_offense_level').val(data.offense_level);
                        } else {
                            $('#edit_offense_level').prop('disabled', true);
                            $('#edit_offense_level').val('');
                        }
                        
                        // Load offenses based on section and level for edit form
                        loadOffensesForEdit(data.section_type, data.offense_level, data.offense_id);
                        
                        $('#edit_sanction_display').val(data.sanction);

                        // Check if this is a custom sanction
                        if (data.is_custom_sanction) {
                            $('#edit_use_custom_sanction').prop('checked', true);
                            $('#edit_custom_sanction_container').show();
                            $('#edit_custom_sanction').val(data.sanction);
                            
                            // Show service hours if present
                            if (data.service_hours !== null) {
                                $('#edit_service_hours_container').show();
                                $('#edit_service_hours').val(data.service_hours);
                            }
                        }
                        
                        // Initialize select2 for edit_offense when modal is shown
                        $('#edit_offense').select2({
                            placeholder: 'Select an offense',
                            allowClear: true,
                            width: '100%',
                            dropdownParent: $('#editReportModal')
                        });
                        
                        $('#editReportModal').modal('show');
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'Failed to load violation details'
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while loading violation details'
                    });
                }
            });
        });

        // Handle custom sanction checkbox in edit form
        $('#edit_use_custom_sanction').change(function() {
            if ($(this).is(':checked')) {
                $('#edit_custom_sanction_container').show();
                $('#edit_sanction_display').prop('readonly', true);
                $('#edit_service_hours_container').show();
                
                // Clear any existing values
                $('#edit_custom_sanction').val('');
                $('#edit_service_hours').val('');
            } else {
                $('#edit_custom_sanction_container').hide();
                $('#edit_sanction_display').prop('readonly', true);
                // Hide service hours input if no custom sanction and no default service hours
                if (!$('#edit_sanction_display').val().match(/(\d+)\s*hours?/i) && 
                    !$('#edit_sanction_display').val().match(/service/i)) {
                    $('#edit_service_hours_container').hide();
                }
            }
        });

        // Handle custom sanction input in edit form
        $('#edit_custom_sanction').on('input', function() {
            let customText = $(this).val();
            
            // Try to extract hours from the custom sanction text
            let hours = null;
            let matches = customText.match(/(\d+)\s*hours?/i);
            if (matches) {
                hours = parseInt(matches[1]);
                $('#edit_service_hours').val(hours);
            }
            
            // Also check for text numbers
            let textNumbers = {
                'one': 1, 'two': 2, 'three': 3, 'four': 4, 'five': 5,
                'six': 6, 'seven': 7, 'eight': 8, 'nine': 9, 'ten': 10,
                'twenty': 20, 'thirty': 30, 'forty': 40, 'fifty': 50, 'sixty': 60
            };
            
            Object.keys(textNumbers).forEach(function(word) {
                let regex = new RegExp('\\b' + word + '\\s*hours?\\b', 'i');
                if (regex.test(customText)) {
                    hours = textNumbers[word.toLowerCase()];
                    $('#edit_service_hours').val(hours);
                }
            });
        });

        // Update edit form submission to include custom sanction
        $('#editViolationForm').on('submit', function(e) {
            e.preventDefault();
            
            const formData = {
                violation_id: $('#edit_violation_id').val(),
                student: $('#edit_student_id').val(),
                incident_datetime: $('#edit_incident_datetime_picker input').val(),
                section: $('#edit_section').val(),
                offense_level: $('#edit_offense_level').val(),
                offense: $('#edit_offense').val(),
                sanction: $('#edit_use_custom_sanction').is(':checked') ? $('#edit_custom_sanction').val() : $('#edit_sanction_display').val(),
                service_hours: $('#edit_service_hours').val() || null,
                is_custom_sanction: $('#edit_use_custom_sanction').is(':checked')
            };

            // Validate form data
            if (!formData.student || !formData.incident_datetime || !formData.section || !formData.offense) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Please fill in all required fields'
                });
                return;
            }

            // Validate custom sanction if enabled
            if (formData.is_custom_sanction) {
                if (!formData.sanction.trim()) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Please enter a custom sanction description'
                    });
                    return;
                }
                
                // Validate service hours if mentioned in the sanction
                if ((formData.sanction.match(/(\d+)\s*hours?/i) || 
                     formData.sanction.toLowerCase().includes('service')) && 
                    (!formData.service_hours || formData.service_hours <= 0)) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Please enter the number of service hours in the Service Hours field'
                    });
                    return;
                }
            }

            // Show loading state
            Swal.fire({
                title: 'Processing...',
                text: 'Please wait while we update the report.',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Submit form using AJAX
            $.ajax({
                url: 'update_violation.php',
                method: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Violation report has been updated successfully'
                        }).then(() => {
                            $('#editReportModal').modal('hide');
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'Failed to update violation report'
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while updating the violation report'
                    });
                }
            });
        });

        // Load offenses based on section and level for edit form
        function loadOffensesForEdit(section, level, selectedOffenseId) {
            const offenseSelect = $('#edit_offense');
            offenseSelect.empty().append('<option value="">Select Offense</option>');

            if (section === 'section1') {
                // Load Section 1 offenses
                $.ajax({
                    url: 'get_offenses.php',
                    method: 'POST',
                    data: { section: 'section1' },
                    success: function(response) {
                        if (response.success) {
                            // Clear existing options first
                            offenseSelect.empty().append('<option value="">Select Offense</option>');
                            // Add new options
                            response.data.forEach(function(offense) {
                                offenseSelect.append(
                                    `<option value="${offense.id}" ${offense.id == selectedOffenseId ? 'selected' : ''}>${offense.description}</option>`
                                );
                            });
                            // Reinitialize Select2 for offense dropdown
                            offenseSelect.select2({
                                placeholder: 'Select an offense',
                                allowClear: true,
                                width: '100%',
                                dropdownParent: $('#editReportModal')
                            });
                        }
                    }
                });
            } else if (section === 'section2' && level) {
                // Load Section 2 offenses based on level
                $.ajax({
                    url: 'get_offenses.php',
                    method: 'POST',
                    data: { 
                        section: 'section2',
                        level: level
                    },
                    success: function(response) {
                        offenseSelect.empty().append('<option value="">Select Offense</option>');
                        if (response.success) {
                            response.data.forEach(function(offense) {
                                offenseSelect.append(
                                    `<option value="${offense.id}">${offense.description}</option>`
                                );
                            });
                            // Reinitialize Select2 for offense dropdown
                            offenseSelect.select2({
                                placeholder: 'Select an offense',
                                allowClear: true,
                                width: '100%',
                                dropdownParent: $('#editReportModal')
                            });
                        }
                    }
                });
            }
        }

        // Handle section change in edit form
        $('#edit_section').change(function() {
            const selectedSection = $(this).val();
            const offenseLevelSelect = $('#edit_offense_level');
            const offenseSelect = $('#edit_offense');

            // Clear offense selection
            offenseSelect.empty().append('<option value="">Select Offense</option>');
            
            // Reinitialize select2
            offenseSelect.select2({
                placeholder: 'Select an offense',
                allowClear: true,
                width: '100%',
                dropdownParent: $('#editReportModal')
            });

            if (selectedSection === 'section1') {
                // Section 1 - Academic
                offenseLevelSelect.prop('disabled', true);
                offenseLevelSelect.val('');
                loadOffensesForEdit('section1', null, null);
            } else if (selectedSection === 'section2') {
                // Section 2 - Non-Academic
                offenseLevelSelect.prop('disabled', false);
                offenseLevelSelect.val('');
            } else {
                // No section selected
                offenseLevelSelect.prop('disabled', true);
                offenseLevelSelect.val('');
            }
        });

        // Handle offense level change in edit form
        $('#edit_offense_level').change(function() {
            const selectedLevel = $(this).val();
            const selectedSection = $('#edit_section').val();
            const offenseSelect = $('#edit_offense');
            
            // Clear offense selection
            offenseSelect.empty().append('<option value="">Select Offense</option>');
            
            // Reinitialize select2
            offenseSelect.select2({
                placeholder: 'Select an offense',
                allowClear: true,
                width: '100%',
                dropdownParent: $('#editReportModal')
            });

            if (selectedSection === 'section2' && selectedLevel) {
                loadOffensesForEdit('section2', selectedLevel, null);
            }
        });

        // Handle offense change in edit form
        $('#edit_offense').change(function() {
            const selectedOffense = $(this).val();
            const selectedStudent = $('#edit_student_id').val();
            const selectedSection = $('#edit_section').val();
            const selectedLevel = $('#edit_offense_level').val();
            
            if (selectedOffense && selectedStudent) {
                $.ajax({
                    url: 'get_sanction.php',
                    method: 'POST',
                    data: {
                        student_id: selectedStudent,
                        offense_id: selectedOffense,
                        section: selectedSection,
                        level: selectedLevel
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#edit_sanction_display').val(response.sanction);
                        } else {
                            $('#edit_sanction_display').val('');
                        }
                    },
                    error: function() {
                        $('#edit_sanction_display').val('Error fetching sanction');
                    }
                });
            } else {
                $('#edit_sanction_display').val('');
            }
        });

        // Reset edit form when modal is closed
        $('#editReportModal').on('hidden.bs.modal', function() {
            $('#editViolationForm')[0].reset();
            $('#edit_offense').empty().append('<option value="">Select Offense</option>');
            $('#edit_sanction_display').val('');
        });

        // Initialize Select2 with custom options
        $('.select2bs4').select2({
            theme: 'bootstrap4',
            width: '100%',
            placeholder: 'Select an offense',
            allowClear: true,
            minimumResultsForSearch: 5,
            escapeMarkup: function(markup) {
                return markup;
            },
            templateResult: function(data) {
                if (!data.id) return data.text;
                // Add custom formatting for options
                return $('<div class="select2-results__option-text">' + 
                        '<span class="option-title">' + data.text + '</span>' +
                        '</div>');
            },
            templateSelection: function(data) {
                if (!data.id) return data.text;
                // Add custom formatting for selected option
                return $('<div class="select2-selection-text">' + data.text + '</div>');
            },
            dropdownParent: $('#addViolationModal')
        });

        // Reinitialize Select2 when the modal is shown
        $('#addViolationModal').on('shown.bs.modal', function () {
            $('.select2bs4').select2('destroy').select2({
                theme: 'bootstrap4',
                dropdownParent: $('#addViolationModal'),
                width: '100%'
            });
        });

        // Reinitialize Select2 when the edit modal is shown
        $('#editReportModal').on('shown.bs.modal', function() {
            $('#edit_offense').select2({
                placeholder: 'Select an offense',
                allowClear: true,
                width: '100%',
                dropdownParent: $('#editReportModal')
            });
        });

        // Initialize datepicker
        $('#incident_datetime_picker').datetimepicker({
            format: 'MM/DD/YYYY hh:mm A'
        });

        // Handle custom sanction checkbox
        $('#use_custom_sanction').change(function() {
            if ($(this).is(':checked')) {
                $('#custom_sanction_container').show();
                $('#sanction_display').prop('readonly', true);
                $('#service_hours_container').show();
                
                // Clear any existing values
                $('#custom_sanction').val('');
                $('#service_hours').val('');
            } else {
                $('#custom_sanction_container').hide();
                $('#sanction_display').prop('readonly', true);
                // Hide service hours input if no custom sanction and no default service hours
                if (!$('#sanction_display').val().match(/(\d+)\s*hours?/i) && 
                    !$('#sanction_display').val().match(/service/i)) {
                    $('#service_hours_container').hide();
                }
            }
        });

        // Handle custom sanction input
        $('#custom_sanction').on('input', function() {
            let customText = $(this).val();
            
            // Try to extract hours from the custom sanction text
            let hours = null;
            let matches = customText.match(/(\d+)\s*hours?/i);
            if (matches) {
                hours = parseInt(matches[1]);
                $('#service_hours').val(hours);
            }
            
            // Also check for text numbers
            let textNumbers = {
                'one': 1, 'two': 2, 'three': 3, 'four': 4, 'five': 5,
                'six': 6, 'seven': 7, 'eight': 8, 'nine': 9, 'ten': 10,
                'twenty': 20, 'thirty': 30, 'forty': 40, 'fifty': 50, 'sixty': 60
            };
            
            Object.keys(textNumbers).forEach(function(word) {
                let regex = new RegExp('\\b' + word + '\\s*hours?\\b', 'i');
                if (regex.test(customText)) {
                    hours = textNumbers[word.toLowerCase()];
                    $('#service_hours').val(hours);
                }
            });
        });

        // Handle delete report button click
        $('#reportsTable').on('click', '.delete-report', function() {
            const id = $(this).data('id');
            Swal.fire({
                title: 'Are you sure?',
                text: 'This will permanently delete the violation report.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: 'delete_violation.php',
                        method: 'POST',
                        data: { id: id },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: 'Violation report has been deleted.'
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: response.message || 'Failed to delete violation report.'
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'An error occurred while deleting the violation report.'
                            });
                        }
                    });
                }
            });
        });

        // Handle Export to Excel button click
        $('#exportExcelBtn').click(function() {
            $('#exportOptionsModal').modal('show');
        });
    });
    </script>

    <style>
    /* Progress bar styles */
    .progress {
        height: 25px;
        border-radius: 12px;
        background-color: #e9ecef;
    }
    
    /* Select2 Dropdown Styling */
    .select2-container--default .select2-selection--single {
        height: 40px !important;
        padding: 6px 12px !important;
        border: 1px solid #ced4da !important;
        border-radius: 4px !important;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 38px !important;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 28px !important;
    }
    
    .select2-container--default .select2-results__option {
        padding: 8px 12px !important;
    }
    
    .select2-container--default .select2-search--dropdown .select2-search__field {
        border: 1px solid #ced4da !important;
        border-radius: 4px !important;
        padding: 6px 12px !important;
    }
    
    .select2-dropdown {
        border: 1px solid #ced4da !important;
        border-radius: 4px !important;
    }
    
    /* Option items */
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #E3F2FD !important;
        color: #1976D2 !important;
    }
    
    /* Add orange badge style */
    .badge-orange {
        background-color: #fd7e14 !important;
        color: white !important;
    }
    
    .progress-bar {
        transition: width 0.6s ease, background-color 0.6s ease;
        border-radius: 12px;
        font-weight: bold;
        font-size: 14px;
    }
    
    .progress-bar.bg-success {
        background-color: #28a745 !important;
        box-shadow: 0 0 10px rgba(40, 167, 69, 0.5);
    }
    
    .text-success {
        color: #28a745 !important;
    }
    
    /* Existing styles... */
    .badge {
        padding: 0.5em 1em;
    }
    .badge-danger {
        background-color: #dc3545;
    }
    .badge-success {
        background-color: #28a745;
    }
    .btn-group-sm > .btn, .btn-sm {
        padding: 0.25rem 0.5rem;
        margin: 0 2px;
    }
    .table td {
        vertical-align: middle;
    }
    #viewReportModal .modal-xl {
        max-width: 90%;
    }
    #viewReportModal .modal-body {
        max-height: calc(100vh - 120px);
        overflow-y: auto;
    }
    #viewReportModal .table {
        margin-bottom: 0;
    }
    #viewReportModal .card {
        margin-bottom: 0;
        box-shadow: none;
        border: 1px solid rgba(0,0,0,.125);
    }
    #viewReportModal .card-body {
        padding: 1rem;
    }
    #viewReportModal .row {
        margin-right: 0;
        margin-left: 0;
    }
    #viewReportModal .col-12 {
        padding: 10px 15px;
    }
    #viewReportModal h4 {
        margin: 0;
        padding: 10px 15px;
        background-color: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
    }
    #viewReportModal .table-responsive {
        margin: 0;
    }
    #viewReportModal .badge-pill {
        font-size: 0.875rem;
        padding: 0.4em 0.8em;
    }
    /* Modal specific styles */
    #addReportModal .modal-content {
        border-radius: 4px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }
    #addReportModal .modal-header {
        border-bottom: 2px solid #dee2e6;
    }
    #addReportModal .modal-body {
        padding: 2rem;
    }
    #addReportModal .form-group {
        margin-bottom: 1.5rem;
    }
    #addReportModal .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        color: #212529;
        font-weight: normal;
    }
    #addReportModal .form-control,
    #addReportModal .select2-container--bootstrap4 .select2-selection--single,
    #addReportModal .input-group .form-control {
        height: 40px !important;
        border-radius: 4px;
        border: 1px solid #ced4da;
        padding: 0.375rem 0.75rem;
        font-size: 1rem;
        font-weight: 400;
        line-height: 1.5;
        color: #212529;
        background-color: #fff;
        box-shadow: none !important;
    }
    #addReportModal .form-control:focus,
    #addReportModal .select2-container--bootstrap4.select2-container--focus .select2-selection--single,
    #addReportModal .input-group .form-control:focus {
        border-color: #80bdff;
        box-shadow: none !important;
    }
    #addReportModal .select2-container--bootstrap4 .select2-selection--single {
        padding-top: 8px;
        padding-bottom: 8px;
    }
    #addReportModal .select2-container--bootstrap4 .select2-selection--single .select2-selection__arrow {
        height: 38px;
        right: 5px;
    }
    #addReportModal .select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered {
        color: #212529;
        line-height: 1.5;
        padding-left: 0;
    }
    #addReportModal .select2-container--bootstrap4 .select2-selection--single .select2-selection__placeholder {
        color: #6c757d;
    }
    #addReportModal .select2-container--bootstrap4 .select2-container--bootstrap4 .select2-selection--single .select2-selection__placeholder {
        color: #6c757d;
    }
    #addReportModal .select2-container--bootstrap4 .select2-dropdown {
        border-radius: 0;
        border: 1px solid #ced4da;
    }
    #addReportModal .select2-container--bootstrap4 .select2-results__option {
        padding: 8px 12px;
        color: #212529;
    }
    #addReportModal .select2-container--bootstrap4 .select2-results__option--highlighted[aria-selected] {
        background-color: #f8f9fa;
        color: #212529;
    }
    #addReportModal .select2-container--bootstrap4 .select2-search--dropdown .select2-search__field {
        border: 1px solid #ced4da;
        border-radius: 0;
        padding: 8px 12px;
    }
    #addReportModal .input-group-text {
        border-radius: 0;
        border: 1px solid #ced4da;
        background-color: #fff;
    }
    #addReportModal .btn-primary {
        border-radius: 4px;
        padding: 8px 16px;
        font-size: 1rem;
        line-height: 1.5;
    }
    #addReportModal .datetimepicker-input {
        background-color: #fff !important;
    }
    #addReportModal .input-group .input-group-text {
        border-left: none;
    }
    #addReportModal .input-group .form-control {
        border-right: none;
    }
    #addReportModal .form-control:disabled,
    #addReportModal .form-control[readonly] {
        background-color: #e9ecef;
        opacity: 1;
    }
    /* Datetime picker specific styles */
    .datetimepicker-input {
        background-color: #fff !important;
    }
    .input-group .input-group-text {
        border-left: none;
    }
    .input-group .form-control {
        border-right: none;
    }
    .form-control,
    .select2-container--bootstrap4 .select2-selection--single,
    .input-group .form-control {
        height: 40px !important;
        border-radius: 4px;
        border: 1px solid #ced4da;
        padding: 0.375rem 0.75rem;
        font-size: 1rem;
        font-weight: 400;
        line-height: 1.5;
        color: #212529;
        background-color: #fff;
        box-shadow: none !important;
    }
    .form-control:focus,
    .select2-container--bootstrap4.select2-container--focus .select2-selection--single,
    .input-group .form-control:focus {
        border-color: #80bdff;
        box-shadow: none !important;
    }
    .input-group-text {
        border-radius: 0;
        border: 1px solid #ced4da;
        background-color: #fff;
    }
    /* Disabled state */
    .form-control:disabled,
    .form-control[readonly] {
        background-color: #e9ecef;
        opacity: 1;
    }
    
    /* DateTime Picker Custom Styles */
    .bootstrap-datetimepicker-widget {
        background-color: #fff !important;
        border: 1px solid #e4e7ea !important;
        border-radius: 8px !important;
        box-shadow: 0 3px 10px rgba(0,0,0,0.08) !important;
        padding: 8px !important;
        width: 280px !important;
        font-family: 'Source Sans Pro', sans-serif !important;
    }

    .bootstrap-datetimepicker-widget .datepicker {
        padding: 0 !important;
    }

    .bootstrap-datetimepicker-widget table {
        width: 100% !important;
        margin: 0 !important;
    }

    .bootstrap-datetimepicker-widget table thead tr:first-child th {
        height: 40px !important;
        line-height: 40px !important;
        background: transparent !important;
        color: #495057 !important;
        font-weight: 600 !important;
        border: none !important;
    }

    .bootstrap-datetimepicker-widget table thead tr:first-child th.picker-switch {
        font-size: 16px !important;
        font-weight: 600 !important;
    }

    .bootstrap-datetimepicker-widget table thead tr:first-child th.prev,
    .bootstrap-datetimepicker-widget table thead tr:first-child th.next {
        color: #6c757d !important;
    }

    .bootstrap-datetimepicker-widget table td {
        height: 35px !important;
        line-height: 35px !important;
        width: 35px !important;
        font-size: 14px !important;
        color: #495057 !important;
        text-align: center !important;
        border-radius: 50% !important;
    }

    .bootstrap-datetimepicker-widget table td.day {
        position: relative !important;
        cursor: pointer !important;
        transition: all 0.2s ease !important;
    }

    .bootstrap-datetimepicker-widget table td.day:hover {
        background-color: #e9ecef !important;
    }

    .bootstrap-datetimepicker-widget table td.active,
    .bootstrap-datetimepicker-widget table td.active:hover {
        background-color: #00b0ff !important;
        color: #fff !important;
        font-weight: 600 !important;
        text-shadow: none !important;
    }

    .bootstrap-datetimepicker-widget table td.today {
        position: relative !important;
        color: #00b0ff !important;
        background-color: #e8f7ff !important;
    }

    .bootstrap-datetimepicker-widget table td.today:before {
        content: '' !important;
        display: inline-block !important;
        border: solid transparent !important;
        border-width: 0 0 7px 7px !important;
        border-bottom-color: #00b0ff !important;
        position: absolute !important;
        bottom: 4px !important;
        right: 4px !important;
    }

    .bootstrap-datetimepicker-widget .timepicker {
        margin: 0 !important;
        padding: 10px !important;
        border-top: 1px solid #e9ecef !important;
    }

    .bootstrap-datetimepicker-widget .timepicker-picker table {
        margin: 0 auto !important;
        width: auto !important;
    }

    .bootstrap-datetimepicker-widget .timepicker-picker table td {
        height: 32px !important;
        line-height: 32px !important;
        width: auto !important;
        padding: 0 !important;
        text-align: center !important;
    }

    .bootstrap-datetimepicker-widget .timepicker-picker table td a {
        padding: 0 !important;
        border-radius: 50% !important;
        width: 32px !important;
        height: 32px !important;
        line-height: 32px !important;
        display: inline-block !important;
        color: #495057 !important;
    }

    .bootstrap-datetimepicker-widget .timepicker-picker table td a:hover {
        background-color: #e9ecef !important;
    }

    .bootstrap-datetimepicker-widget .timepicker-picker table td span {
        width: 54px !important;
        height: 54px !important;
        line-height: 54px !important;
        font-size: 1.2em !important;
        color: #495057 !important;
    }

    .bootstrap-datetimepicker-widget .timepicker-hour,
    .bootstrap-datetimepicker-widget .timepicker-minute {
        font-size: 20px !important;
        font-weight: 600 !important;
        color: #495057 !important;
        width: auto !important;
        border: none !important;
        background: transparent !important;
    }

    .bootstrap-datetimepicker-widget .btn[data-action="togglePeriod"] {
        background: #e9ecef !important;
        border: none !important;
        color: #495057 !important;
        font-size: 12px !important;
        font-weight: 600 !important;
        padding: 5px 10px !important;
        border-radius: 4px !important;
        height: auto !important;
        min-width: 50px !important;
        text-transform: uppercase !important;
    }

    .bootstrap-datetimepicker-widget .btn[data-action="togglePeriod"]:hover {
        background: #dee2e6 !important;
    }

    .bootstrap-datetimepicker-widget .picker-switch {
        text-align: center !important;
        border-top: 1px solid #e9ecef !important;
        padding-top: 10px !important;
    }

    .bootstrap-datetimepicker-widget .picker-switch td {
        padding: 0 !important;
        margin: 0 !important;
    }

    .bootstrap-datetimepicker-widget .picker-switch td span {
        height: 2.5em !important;
        line-height: 2.5em !important;
        width: 100% !important;
        font-size: 14px !important;
        color: #495057 !important;
        border-radius: 4px !important;
    }

    .bootstrap-datetimepicker-widget .picker-switch td span:hover {
        background-color: #e9ecef !important;
    }

    /* Bottom toolbar */
    .bootstrap-datetimepicker-widget .toolbar {
        display: flex !important;
        justify-content: space-between !important;
        padding: 10px !important;
        border-top: 1px solid #e9ecef !important;
        margin-top: 10px !important;
    }

    .bootstrap-datetimepicker-widget .toolbar button {
        color: #00b0ff !important;
        background: none !important;
        border: none !important;
        padding: 5px 10px !important;
        font-size: 13px !important;
        font-weight: 600 !important;
        cursor: pointer !important;
        border-radius: 4px !important;
        transition: all 0.2s ease !important;
    }

    .bootstrap-datetimepicker-widget .toolbar button:hover {
        background-color: #e8f7ff !important;
    }

    /* Input field styles */
    .datetimepicker-input {
        height: 38px !important;
        border-radius: 4px 0 0 4px !important;
        border: 1px solid #ced4da !important;
        padding: 0.375rem 0.75rem !important;
        font-size: 14px !important;
        color: #495057 !important;
        background-color: #fff !important;
        transition: border-color 0.15s ease-in-out !important;
    }

    .datetimepicker-input:focus {
        border-color: #80bdff !important;
        outline: 0 !important;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25) !important;
    }

    .input-group .input-group-text {
        background-color: #fff !important;
        border: 1px solid #ced4da !important;
        border-left: none !important;
        border-radius: 0 4px 4px 0 !important;
        padding: 0.375rem 0.75rem !important;
    }

    .input-group .input-group-text i {
        color: #6c757d !important;
    }

    /* Floating Action Button */
    .btn-float {
        position: fixed !important;
        bottom: 30px !important;
        right: 30px !important;
        width: 60px !important;
        height: 60px !important;
        background-color: #00b0ff !important;
        border: none !important;
        border-radius: 50% !important;
        color: white !important;
        font-size: 24px !important;
        cursor: pointer !important;
        box-shadow: 0 4px 10px rgba(0, 176, 255, 0.3) !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        transition: all 0.3s ease !important;
        z-index: 1050 !important;
        outline: none !important;
    }

    .btn-float:hover {
        transform: scale(1.1) !important;
        box-shadow: 0 6px 15px rgba(0, 176, 255, 0.4) !important;
    }

    .btn-float:active {
        transform: scale(0.95) !important;
    }

    .btn-float i {
        transition: all 0.3s ease !important;
    }

    .btn-float-label {
        position: absolute !important;
        right: 70px !important;
        background-color: rgba(0, 0, 0, 0.8) !important;
        color: white !important;
        padding: 8px 12px !important;
        border-radius: 4px !important;
        font-size: 14px !important;
        font-weight: 500 !important;
        opacity: 0 !important;
        visibility: hidden !important;
        transition: all 0.3s ease !important;
        white-space: nowrap !important;
    }

    .btn-float:hover .btn-float-label {
        opacity: 1 !important;
        visibility: visible !important;
        right: 75px !important;
    }

    .btn-float-label:after {
        content: '' !important;
        position: absolute !important;
        right: -5px !important;
        top: 50% !important;
        transform: translateY(-50%) !important;
        width: 0 !important;
        height: 0 !important;
        border-top: 5px solid transparent !important;
        border-bottom: 5px solid transparent !important;
        border-left: 5px solid rgba(0, 0, 0, 0.8) !important;
    }

    @media (max-width: 768px) {
        .btn-float {
            width: 50px !important;
            height: 50px !important;
            bottom: 20px !important;
            right: 20px !important;
            font-size: 20px !important;
        }
    }

    /* Add these new styles at the end */
    .remarks-cell {
        max-width: 200px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        position: relative;
        cursor: pointer;
    }

    .tooltip {
        pointer-events: none;
        z-index: 9999;
    }

    .tooltip-inner {
        max-width: 300px;
        padding: 8px 12px;
        color: #ffffff;
        background-color: #000000;
        border-radius: 4px;
        font-size: 14px;
        line-height: 1.4;
        text-align: left;
        word-wrap: break-word;
        opacity: 1;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }

    /* Ensure tooltips are always on top */
    .modal {
        z-index: 1050;
    }

    .tooltip {
        z-index: 1060;
    }

    /* Table Layout Optimization */
    #reportsTable {
        table-layout: fixed !important;
        width: 100% !important;
    }

    /* Date and Time Column Styles */
    .date-time {
        display: flex !important;
        flex-direction: column !important;
        line-height: 1.2 !important;
    }

    .date-time .date {
        font-weight: 600 !important;
    }

    .date-time .time {
        color: #666 !important;
        font-size: 12px !important;
    }

    #reportsTable th,
    #reportsTable td {
        padding: 6px 4px !important;
        vertical-align: middle !important;
        font-size: 13px !important;
        line-height: 1.3 !important;
    }

    /* Column Width Distribution */
    #reportsTable th:nth-child(1), /* Date & Time */
    #reportsTable td:nth-child(1) {
        width: 11% !important;
    }

    #reportsTable th:nth-child(2), /* Student Name */
    #reportsTable td:nth-child(2) {
        width: 13% !important;
    }

    #reportsTable th:nth-child(3), /* Section */
    #reportsTable td:nth-child(3) {
        width: 8% !important;
    }

    #reportsTable th:nth-child(4), /* Level */
    #reportsTable td:nth-child(4) {
        width: 7% !important;
    }

    #reportsTable th:nth-child(5), /* Offense */
    #reportsTable td:nth-child(5) {
        width: 18% !important;
        max-width: 0 !important; /* Forces truncation */
    }

    #reportsTable th:nth-child(6), /* Violation Count */
    #reportsTable td:nth-child(6) {
        width: 7% !important;
        text-align: center !important;
    }

    #reportsTable th:nth-child(7), /* Sanction */
    #reportsTable td:nth-child(7) {
        width: 15% !important;
        max-width: 0 !important; /* Forces truncation */
    }

    #reportsTable th:nth-child(8), /* Status */
    #reportsTable td:nth-child(8) {
        width: 7% !important;
        text-align: center !important;
    }

    #reportsTable th:nth-child(9), /* Actions */
    #reportsTable td:nth-child(9) {
        width: 14% !important;
        text-align: center !important;
        white-space: nowrap !important;
        padding: 4px !important;
    }

    /* Action Buttons Wrapper */
    .action-buttons-wrapper {
        display: flex !important;
        justify-content: center !important;
        align-items: center !important;
        gap: 4px !important;
        min-width: 80px !important;
    }

    /* Action Buttons Optimization */
    #reportsTable .btn-sm {
        padding: 0.2rem 0.4rem !important;
        font-size: 0.75rem !important;
        margin: 0 !important;
        height: 24px !important;
        width: 24px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        flex-shrink: 0 !important;
    }

    #reportsTable .btn-sm i {
        font-size: 0.75rem !important;
        line-height: 1 !important;
    }

    /* Ensure action column doesn't wrap */
    #reportsTable td:nth-child(9) {
        white-space: nowrap !important;
        overflow: visible !important;
        padding: 4px !important;
        text-align: center !important;
    }

    /* Responsive adjustments */
    @media (max-width: 1200px) {
        #reportsTable .btn-sm {
            padding: 0.15rem 0.3rem !important;
            font-size: 0.7rem !important;
        }
        
        .action-buttons-wrapper {
            min-width: 76px !important;
        }
    }

    /* Add text truncation styles */
    .truncate-text {
        display: block !important;
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        max-width: 100% !important;
    }

    #hours-validation-message {
        padding: 5px 10px;
        border-radius: 4px;
        background-color: rgba(220, 53, 69, 0.1);
        border: 1px solid rgba(220, 53, 69, 0.2);
    }

    #hours-validation-message small {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    #hours-validation-message i {
        color: #dc3545;
    }

    .input-group .form-control:focus {
        border-color: #80bdff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    .input-group .form-control.is-invalid {
        border-color: #dc3545;
    }

    .input-group .form-control.is-invalid:focus {
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
    }

    /* Edit Report Modal Styles */
    #editReportModal .modal-dialog {
        max-width: 800px;
    }

    #editReportModal .modal-content {
        border: none;
        border-radius: 8px;
    }

    #editReportModal .modal-header {
        padding: 0.75rem 1.25rem;
    }

    #editReportModal .modal-body {
        padding: 1.25rem;
    }

    #editReportModal .form-group {
        margin-bottom: 1rem;
    }

    #editReportModal label {
        margin-bottom: 0.5rem;
    }

    #editReportModal .form-control {
        height: calc(2.25rem + 2px);
    }

    #editReportModal .btn {
        padding: 0.375rem 1.5rem;
    }

    /* Dark mode support */
    .dark-mode #editReportModal .modal-content {
        background-color: #343a40;
        color: #fff;
    }

    .dark-mode #editReportModal .form-control {
        background-color: #454d55;
        border-color: #545b62;
        color: #fff;
    }

    .dark-mode #editReportModal .input-group-text {
        background-color: #454d55;
        border-color: #545b62;
        color: #fff;
    }
    </style>
</body>
</html> 