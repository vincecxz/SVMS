    <?php
    include('../config/database.php');
    ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Add Violation Report - SAO</title>
        
        <!-- Google Font: Source Sans Pro -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
        <!-- Font Awesome -->
        <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
        <!-- Theme style -->
        <link rel="stylesheet" href="../dist/css/adminlte.min.css">
        <!-- Select2 -->
        <link rel="stylesheet" href="../plugins/select2/css/select2.min.css">
        <!-- SweetAlert2 -->
        <link rel="stylesheet" href="../plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
        <!-- Tempusdominus Bootstrap 4 -->
        <link rel="stylesheet" href="../plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
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
                            <h1 style="margin-left: 10px;">Add Violation</h1>
                            </div>
                            <div class="col-sm-6 d-flex justify-content-end">
                            <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                                    <li class="breadcrumb-item active">Add Violation</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-12">
                            <div class="card" style="border-radius: 4px; box-shadow: 4px 4px 10px rgba(0, 0, 0, 0.1);">
                                    <div class="card-body">
                                        <form id="violationForm">
                                            <!-- Student Selection -->
                                            <div class="form-group">
                                                <label for="student">Select Student</label>
                                            

                                                    <select class="form-control select2" id="student" name="student" required>

                                                    <option value="">Type student name...</option>

                                                    <?php
                                                    $query = "SELECT * FROM students ORDER BY full_name ASC";
                                                    $result = $conn->query($query);
                                                    while($row = $result->fetch_assoc()) {
                                                        echo "<option value='".$row['id']."'>".$row['full_name']."</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>

                                            <!-- Incident Date -->
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
                    </div>
                </section>
            </div>

            <?php include('../includes/admin/footer.php'); ?>
        </div>

        <!-- jQuery -->
        <script src="../plugins/jquery/jquery.min.js"></script>
        <!-- Bootstrap 4 -->
        <script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
        <!-- Select2 -->
        <script src="../plugins/select2/js/select2.full.min.js"></script>
        <!-- AdminLTE App -->
        <script src="../dist/js/adminlte.min.js"></script>
        <!-- SweetAlert2 -->
        <script src="../plugins/sweetalert2/sweetalert2.min.js"></script>
        <!-- Moment.js -->
        <script src="../plugins/moment/moment.min.js"></script>
        <!-- Tempusdominus Bootstrap 4 -->
        <script src="../plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>

        <style>
            .content-wrapper {
                background-color: #f8f9fa;
            }
            .card {
                border: none;
                border-radius: 0;
                box-shadow: none;
            }
            .card-body {
                padding: 2rem;
            }
            .form-group {
                margin-bottom: 1.5rem;
            }
            .form-group label {
                display: block;
                margin-bottom: 0.5rem;
                color: #212529;
                font-weight: normal;
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
            .select2-container--bootstrap4 .select2-selection--single {
                padding-top: 8px;
                padding-bottom: 8px;
            }
            .select2-container--bootstrap4 .select2-selection--single .select2-selection__arrow {
                height: 38px;
                right: 5px;
            }
            .select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered {
                color: #212529;
                line-height: 1.5;
                padding-left: 0;
            }
            .select2-container--bootstrap4 .select2-selection--single .select2-selection__placeholder {
                color: #6c757d;
            }
            .select2-container--bootstrap4 .select2-dropdown {
                border-radius: 0;
                border: 1px solid #ced4da;
            }
            .select2-container--bootstrap4 .select2-results__option {
                padding: 8px 12px;
                color: #212529;
            }
            .select2-container--bootstrap4 .select2-results__option--highlighted[aria-selected] {
                background-color: #f8f9fa;
                color: #212529;
            }
            .select2-container--bootstrap4 .select2-search--dropdown .select2-search__field {
                border: 1px solid #ced4da;
                border-radius: 0;
                padding: 8px 12px;
            }
            .input-group-text {
                border-radius: 0;
                border: 1px solid #ced4da;
                background-color: #fff;
            }
            .btn-primary {
                border-radius: 0;
                padding: 8px 16px;
                font-size: 1rem;
                line-height: 1.5;
                background-color: #007bff;
                border-color: #007bff;
            }
            .btn-primary:hover {
                background-color: #0069d9;
                border-color: #0062cc;
            }
            /* Breadcrumb styling */
            .breadcrumb {
                background: none;
                padding: 0;
                margin: 0;
            }
            .breadcrumb-item a {
                color: #007bff;
                text-decoration: none;
            }
            .breadcrumb-item.active {
                color: #6c757d;
            }
            /* Content header styling */
            .content-header {
                padding: 1rem 0;
            }
            .content-header h1 {
                font-size: 1.8rem;
                margin: 0;
                font-weight: normal;
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
            /* Disabled state */
            .form-control:disabled,
            .form-control[readonly] {
                background-color: #e9ecef;
                opacity: 1;
            }
        </style>

        <script>
        $(document).ready(function() {
            // Initialize Select2 with custom options
            $('#student').select2({
                theme: 'bootstrap4',
                placeholder: 'Type student name...',
                allowClear: true,
                width: '100%',
                minimumInputLength: 1,
                dropdownParent: $('#student').parent()
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
                }
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

            // Form submission
            $('#violationForm').submit(function(e) {
                e.preventDefault();
                
                // Validate form
                if (!$(this)[0].checkValidity()) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Please fill in all required fields'
                    });
                    return;
                }

                // Submit form data
                $.ajax({
                    url: 'save_violation.php',
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: 'Violation report has been saved successfully'
                            }).then(() => {
                                window.location.href = 'report_list.php';
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message || 'Failed to save violation report'
                            });
                        }
                    }
                });
            });
        });
        </script>
    </body>
    </html> 