<?php
include('../config/database.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Violation Reports - SAO</title>
    
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
                                                <option value="Resolved">Resolved</option>
                                            </select>
                                        </div>
                                    </div>
                                    <table id="reportsTable" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
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
                                                $date_display = date('M d, Y h:i A', strtotime($row['incident_datetime']));
                                                
                                                echo "<tr>";
                                                echo "<td>".$date_display."</td>";
                                                echo "<td>".$row['student_name']."</td>";
                                                echo "<td>".$section_display."</td>";
                                                echo "<td>".$level_display."</td>";
                                                echo "<td>".$row['offense_description']."</td>";
                                                echo "<td>".$row['violation_count']."</td>";
                                                echo "<td>".$row['sanction']."</td>";
                                                echo "<td><span class='badge badge-".($row['status'] == 'Active' ? 'danger' : 'success')."'>".$row['status']."</span></td>";
                                                echo "<td>
                                                    <button class='btn btn-sm btn-info view-report' data-id='".$row['id']."'><i class='fas fa-eye'></i></button>
                                                    <button class='btn btn-sm btn-success update-status' data-id='".$row['id']."' ".($row['status'] != 'Active' ? 'disabled' : '')."><i class='fas fa-check'></i></button>
                                                </td>";
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
        <div class="modal fade" id="viewReportModal" tabindex="-1">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header bg-info">
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
                        <h5 class="modal-title text-white">Add Violation Report</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="violationForm">
                            <!-- Student Selection -->
                            <div class="form-group">
                                <label for="student">Select Student</label>
                                <select class="form-control select2" id="student" name="student" required>
                                    <!-- <option value="">Type student name...</option> -->
                                    <?php
                                    $query = "SELECT * FROM students ORDER BY full_name ASC";
                                    $result = $conn->query($query);
                                    while($row = $result->fetch_assoc()) {
                                        echo "<option value='".$row['id']."'>".$row['full_name']."</option>";
                                    }
                                    ?>
                                </select>
                            </div>

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

                            <!-- Section Selection -->
                            <div class="form-group">
                                <label for="section">Section</label>
                                <select class="form-control" id="section" name="section" required>
                                    <option value="">Select Section</option>
                                    <option value="section1">Section 1 - Academic</option>
                                    <option value="section2">Section 2 - Non-Academic</option>
                                </select>
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

                            <!-- Offense Selection -->
                            <div class="form-group">
                                <label for="offense">Offense</label>
                                <select class="form-control" id="offense" name="offense" required>
                                    <option value="">Select Offense</option>
                                </select>
                            </div>

                            <!-- Sanction Display -->
                            <div class="form-group">
                                <label>Corresponding Sanction</label>
                                <input type="text" class="form-control" id="sanction_display" readonly>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-primary">Submit</button>
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
            Swal.fire({
                title: 'Update Status',
                text: "Mark this violation as resolved?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, resolve it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: 'update_violation_status.php',
                        method: 'POST',
                        data: { 
                            id: id,
                            status: 'Resolved'
                        },
                        dataType: 'json',
                        success: function(response) {
                            if(response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success',
                                    text: 'Violation status updated successfully'
                                }).then(() => {
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
                        }
                    }
                });
            }
        });

        // Handle offense selection change
        $('#offense').change(function() {
            const selectedOffense = $(this).val();
            const selectedStudent = $('#student').val();
            const selectedSection = $('#section').val();
            const selectedLevel = $('#offense_level').val();

            if (selectedOffense && selectedStudent) {
                // Fetch sanction based on offense and student
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
                        } else {
                            $('#sanction_display').val('');
                        }
                    },
                    error: function() {
                        $('#sanction_display').val('Error fetching sanction');
                    }
                });
            } else {
                $('#sanction_display').val('');
            }
        });

        // Handle form submission
        $('#violationForm').on('submit', function(e) {
            e.preventDefault();
            
            // Get form data
            const formData = {
                student: $('#student').val(),
                incident_datetime: $('#incident_datetime_picker input').val(),
                section: $('#section').val(),
                offense_level: $('#offense_level').val(),
                offense: $('#offense').val(),
                sanction: $('#sanction_display').val()
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
                        // Send email notification
                        $.ajax({
                            url: '../includes/process_email.php',
                            method: 'POST',
                            data: {
                                action: 'send_violation_email',
                                student_id: formData.student,
                                violation_details: {
                                    incident_datetime: formData.incident_datetime,
                                    section: formData.section,
                                    offense_level: formData.offense_level || 'N/A',
                                    offense: $('#offense option:selected').text(),
                                    sanction: formData.sanction
                                }
                            },
                            dataType: 'json',
                            success: function(emailResponse) {
                                Swal.close();  // Close the loading dialog
                                
                                let message = 'Violation report has been saved successfully.';
                                let icon = 'success';
                                
                                if (emailResponse.success) {
                                    message += ' Email notification has been sent.';
                                } else {
                                    message += ' However, email notification could not be sent: ' + emailResponse.message;
                                    icon = 'warning';
                                    console.error('Email error:', emailResponse.message);
                                }
                                
                                Swal.fire({
                                    icon: icon,
                                    title: 'Success',
                                    text: message
                                }).then(() => {
                                    // Reset form and close modal
                                    $('#violationForm')[0].reset();
                                    $('#student').val('').trigger('change');
                                    $('#offense').empty().append('<option value="">Select Offense</option>');
                                    $('#sanction_display').val('');
                                    $('#addReportModal').modal('hide');
                                    // Reload the table
                                    location.reload();
                                });
                            },
                            error: function(xhr, status, error) {
                                Swal.close();  // Close the loading dialog
                                console.error('Email AJAX error:', error);
                                console.log('XHR:', xhr.responseText);  // Log the full response
                                
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Partial Success',
                                    text: 'Violation report saved successfully, but email notification failed to send. Please check the system logs for details.'
                                }).then(() => {
                                    // Reset form and close modal
                                    $('#violationForm')[0].reset();
                                    $('#student').val('').trigger('change');
                                    $('#offense').empty().append('<option value="">Select Offense</option>');
                                    $('#sanction_display').val('');
                                    $('#addReportModal').modal('hide');
                                    // Reload the table
                                    location.reload();
                                });
                            }
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
    });
    </script>

    <style>
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

    .input-group-append .input-group-text {
        background-color: #fff !important;
        border: 1px solid #ced4da !important;
        border-left: none !important;
        border-radius: 0 4px 4px 0 !important;
        padding: 0.375rem 0.75rem !important;
    }

    .input-group-append .input-group-text i {
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
    </style>
</body>
</html> 