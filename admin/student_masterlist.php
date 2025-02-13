<?php
require_once '../config/database.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    // Validate required fields
    $required_fields = ['id_number', 'full_name', 'program', 'year_section', 'email'];
    $missing_fields = [];
    
    foreach ($required_fields as $field) {
        if (!isset($_POST[$field]) || empty(trim($_POST[$field]))) {
            $missing_fields[] = $field;
        }
    }
    
    if (!empty($missing_fields)) {
        echo json_encode(['error' => 'Please fill in all required fields: ' . implode(', ', $missing_fields)]);
        exit;
    }
    
    $id_number = mysqli_real_escape_string($conn, $_POST['id_number']);
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $program_id = mysqli_real_escape_string($conn, $_POST['program']);
    $section = mysqli_real_escape_string($conn, $_POST['year_section']);
    $contact_number = isset($_POST['contact_number']) ? mysqli_real_escape_string($conn, $_POST['contact_number']) : null;
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['error' => 'Invalid email format']);
        exit;
    }

    try {
        // Check if student already exists
        $check_query = "SELECT id_number, email FROM students WHERE id_number = ? OR email = ?";
        $stmt = mysqli_prepare($conn, $check_query);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . mysqli_error($conn));
        }

        mysqli_stmt_bind_param($stmt, "ss", $id_number, $email);
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Execute failed: " . mysqli_stmt_error($stmt));
        }

        $check_result = mysqli_stmt_get_result($stmt);
        if ($check_result && mysqli_num_rows($check_result) > 0) {
            $existing = mysqli_fetch_assoc($check_result);
            if ($existing['id_number'] === $id_number) {
                echo json_encode(['error' => "A student with ID number $id_number already exists."]);
            } else {
                echo json_encode(['error' => "A student with email $email already exists."]);
            }
            exit;
        }

        // Insert new student
        $insert_query = "INSERT INTO students (id_number, full_name, program_id, section, contact_number, email) 
                        VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $insert_query);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . mysqli_error($conn));
        }

        mysqli_stmt_bind_param($stmt, "ssisss", $id_number, $full_name, $program_id, $section, $contact_number, $email);
        
        if (mysqli_stmt_execute($stmt)) {
            if (mysqli_affected_rows($conn) > 0) {
                echo json_encode(['success' => true, 'message' => 'Student added successfully']);
            } else {
                throw new Exception("No rows were inserted");
            }
    } else {
            throw new Exception("Execute failed: " . mysqli_stmt_error($stmt));
        }

    } catch (Exception $e) {
        error_log("Error in student_masterlist.php: " . $e->getMessage());
        echo json_encode(['error' => "Error adding student. Please try again or contact support."]);
    }
    exit;
}

// Fetch all students from database with full program names
$query = "SELECT s.id, s.id_number, s.full_name, s.program_id, s.section, s.contact_number, s.email,
          p.code as program_code, p.name as program_name
          FROM students s 
          JOIN programs p ON s.program_id = p.id
          ORDER BY s.full_name";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Masterlist - SAO</title>
    
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="../plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="../plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="../dist/css/adminlte.min.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
</head>
<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <?php include '../includes/admin/navbar.php'; ?>
        <?php include '../includes/admin/sidebar.php'; ?>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1>Student Masterlist</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                                <li class="breadcrumb-item active">Student Masterlist</li>
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
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h3 class="card-title">Student Records</h3>
                                        <div>
                                            <button type="button" class="btn btn-secondary btn-sm mr-2" data-toggle="modal" data-target="#importStudentModal">
                                                <i class="fas fa-file-import"></i> Import Students
                                            </button>
                                            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addStudentModal">
                                                <i class="fas fa-user-plus"></i> Add Student
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <table id="studentTable" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th width="15%">Actions</th>
                                                <th>Student ID</th>
                                                <th>Name</th>
                                                <th>Program</th>
                                                <th>Section</th>
                                                <th>Email</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                                <tr>
                                                    <td>
                                                        <div class="btn-group">
                                                            <button type="button" class="btn btn-primary btn-sm" onclick="viewStudent('<?php echo $row['id_number']; ?>')" title="View">
                                                                <i class="fas fa-eye"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-warning btn-sm" onclick="editStudent('<?php echo $row['id_number']; ?>')" title="Edit">
                                                                <i class="fas fa-pencil-alt"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-success btn-sm" onclick="generateGoodmoral('<?php echo $row['id_number']; ?>')" title="Generate Good Moral">
                                                                <i class="fas fa-file-alt"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($row['id_number']); ?></td>
                                                    <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                                                    <td><?php echo htmlspecialchars($row['program_name']); ?></td>
                                                    <td><?php echo htmlspecialchars($row['section']); ?></td>
                                                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                                                </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <!-- Add Student Modal -->
    <div class="modal fade" id="addStudentModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Add Student</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="addStudentForm" method="POST">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="id_number">ID Number <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="id_number" name="id_number" required>
                        </div>
                        <div class="form-group">
                            <label for="full_name">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="full_name" name="full_name" required>
                        </div>
                        <div class="form-group">
                            <label for="program">Program <span class="text-danger">*</span></label>
                            <select class="form-control" id="program" name="program" required>
                                <option value="">Select Program</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="year_section">Year and Section <span class="text-danger">*</span></label>
                            <select class="form-control" id="year_section" name="year_section" required disabled>
                                <option value="">Select Year and Section</option>
                            </select>
                            <input type="hidden" id="course_year_section" name="course_year_section">
                        </div>
                        <div class="form-group">
                            <label for="contact_number">Contact Number</label>
                            <input type="text" class="form-control" id="contact_number" name="contact_number" 
                                   placeholder="09XXXXXXXXX" pattern="09[0-9]{9}">
                            <small class="form-text text-muted">Format: 09XXXXXXXXX</small>
                        </div>
                        <div class="form-group">
                            <label for="email">Email Address <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Add Student</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- View Student Modal -->
    <div class="modal fade" id="viewStudentModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Student Details</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>ID Number</label>
                        <p id="view_id_number" class="form-control-static"></p>
                    </div>
                    <div class="form-group">
                        <label>Full Name</label>
                        <p id="view_full_name" class="form-control-static"></p>
                    </div>
                    <div class="form-group">
                        <label>Program</label>
                        <p id="view_program" class="form-control-static"></p>
                    </div>
                    <div class="form-group">
                        <label>Section</label>
                        <p id="view_section" class="form-control-static"></p>
                    </div>
                    <div class="form-group">
                        <label>Contact Number</label>
                        <p id="view_contact_number" class="form-control-static"></p>
                    </div>
                    <div class="form-group">
                        <label>Email Address</label>
                        <p id="view_email" class="form-control-static"></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Student Modal -->
    <div class="modal fade" id="editStudentModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Edit Student</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="editStudentForm" method="POST">
                    <div class="modal-body">
                        <input type="hidden" id="edit_student_id" name="student_id">
                        <div class="form-group">
                            <label for="edit_id_number">ID Number <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_id_number" name="id_number" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_full_name">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_full_name" name="full_name" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_program">Program <span class="text-danger">*</span></label>
                            <select class="form-control" id="edit_program" name="program" required>
                                <option value="">Select Program</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="edit_year_section">Year and Section <span class="text-danger">*</span></label>
                            <select class="form-control" id="edit_year_section" name="year_section" required>
                                <option value="">Select Year and Section</option>
                            </select>
                            <input type="hidden" id="edit_course_year_section" name="course_year_section">
                        </div>
                        <div class="form-group">
                            <label for="edit_contact_number">Contact Number</label>
                            <input type="text" class="form-control" id="edit_contact_number" name="contact_number" 
                                   placeholder="09XXXXXXXXX" pattern="09[0-9]{9}">
                            <small class="form-text text-muted">Format: 09XXXXXXXXX</small>
                        </div>
                        <div class="form-group">
                            <label for="edit_email">Email Address <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="edit_email" name="email" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Import Student Modal -->
    <div class="modal fade" id="importStudentModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Import Students</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="importStudentForm" method="POST" enctype="multipart/form-data" action="import_students.php">
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <p class="mb-0">Please download the template file and fill in the student details before importing.</p>
                        </div>
                        <div class="form-group">
                            <a href="download_template.php" class="btn btn-outline-primary btn-block">
                                <i class="fas fa-download"></i> Download Template
                            </a>
                        </div>
                        <div class="form-group">
                            <label for="import_file">Select File <span class="text-danger">*</span></label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="import_file" name="import_file" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" required>
                                <label class="custom-file-label" for="import_file">Choose file</label>
                            </div>
                            <small class="form-text text-muted">Accepted formats: .csv, .xls, .xlsx</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Import Students</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="../plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables & Plugins -->
    <script src="../plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="../plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="../plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <!-- AdminLTE App -->
    <script src="../dist/js/adminlte.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        $(document).ready(function() {
            var table = $('#studentTable').DataTable({
                "processing": true,
                "serverSide": false,
                "responsive": true,
                "lengthChange": true,
                "autoWidth": false,
                "pageLength": 25,
                "order": [[2, "asc"]], // Sort by name by default
                "columnDefs": [
                    {
                        "targets": 0, // Actions column
                        "orderable": false
                    },
                    {
                        "targets": [3, 4], // Program and Section columns
                        "className": "text-wrap"
                    }
                ],
                "language": {
                    "search": "Search: ",
                    "lengthMenu": "Show _MENU_ entries per page",
                    "info": "Showing _START_ to _END_ of _TOTAL_ students",
                    "infoEmpty": "Showing 0 to 0 of 0 students",
                    "infoFiltered": "(filtered from _MAX_ total students)"
                }
            });

            <?php if (isset($success)): ?>
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: '<?php echo $success; ?>',
                showConfirmButton: false,
                timer: 3000
            });
            <?php endif; ?>

            <?php if (isset($error)): ?>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '<?php echo $error; ?>',
                confirmButtonText: 'OK'
            });
            <?php endif; ?>

            // Load programs on page load
            $.get('get_programs_sections.php', { action: 'get_programs' }, function(response) {
                if (response.success) {
                    var programs = response.data;
                    var programSelect = $('#program');
                    programs.forEach(function(program) {
                        programSelect.append($('<option>', {
                            value: program.id,
                            text: program.code + ' - ' + program.name,
                            'data-code': program.code
                        }));
                    });
                }
            });

            // Load sections when program changes
            $('#program').change(function() {
                var programId = $(this).val();
                var yearSectionSelect = $('#year_section');
                yearSectionSelect.prop('disabled', true);
                yearSectionSelect.html('<option value="">Select Year and Section</option>');
                
                if (programId) {
                    $.get('get_programs_sections.php', { 
                        action: 'get_sections',
                        program_id: programId
                    }, function(response) {
                        if (response.success) {
                            var sections = response.data;
                            sections.forEach(function(section) {
                                yearSectionSelect.append($('<option>', {
                                    value: section.section,
                                    text: section.section
                                }));
                            });
                            yearSectionSelect.prop('disabled', false);
                        }
                    });
                }
            });

            // Form validation and submission
            $('#addStudentForm').on('submit', function(e) {
                e.preventDefault();
                
                // Validate required fields
                var requiredFields = ['id_number', 'full_name', 'program', 'year_section', 'email'];
                var missingFields = [];
                
                requiredFields.forEach(function(field) {
                    if (!$('#' + field).val()) {
                        missingFields.push(field.replace('_', ' '));
                    }
                });
                
                if (missingFields.length > 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Required Fields Missing',
                        text: 'Please fill in all required fields: ' + missingFields.join(', ')
                    });
                    return;
                }

                // Validate contact number if provided
                var contactNumber = $('#contact_number').val();
                if (contactNumber && !/^09\d{9}$/.test(contactNumber)) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid Contact Number',
                        text: 'Contact number should start with 09 and be 11 digits long'
                    });
                    return;
                }

                // Get form data
                var formData = $(this).serialize();

                // Submit form using AJAX
                $.ajax({
                    type: 'POST',
                    url: window.location.href,
                    data: formData,
                    dataType: 'json',
                    beforeSend: function() {
                        // Disable submit button to prevent double submission
                        $('#addStudentForm button[type="submit"]').prop('disabled', true);
                    },
                    success: function(response) {
                        if (response.error) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.error
                            });
                        } else if (response.success) {
                            // Show success message
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: response.message || 'Student added successfully',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(function() {
                                // Clear form and close modal
                            $('#addStudentForm')[0].reset();
                            $('#addStudentModal').modal('hide');
                            // Reload the page
                            window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'An unexpected error occurred. Please try again.'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', {
                            status: status,
                            error: error,
                            response: xhr.responseText
                        });
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to add student. Please try again.'
                        });
                    },
                    complete: function() {
                        // Re-enable submit button
                        $('#addStudentForm button[type="submit"]').prop('disabled', false);
                    }
                });
            });

            // Reset form and dropdowns when modal is closed
            $('#addStudentModal').on('hidden.bs.modal', function () {
                $('#addStudentForm')[0].reset();
                $('#year_section').prop('disabled', true).html('<option value="">Select Year and Section</option>');
            });

            // Load programs for edit modal
            function loadPrograms(selectedProgram = null) {
                $.get('get_programs_sections.php', { action: 'get_programs' }, function(response) {
                    if (response.success) {
                        var programs = response.data;
                        var programSelect = $('#edit_program');
                        programSelect.html('<option value="">Select Program</option>');
                        programs.forEach(function(program) {
                            programSelect.append($('<option>', {
                                value: program.id,
                                text: program.code + ' - ' + program.name,
                                'data-code': program.code
                            }));
                        });
                        if (selectedProgram) {
                            programSelect.val(selectedProgram);
                            programSelect.trigger('change');
                        }
                    }
                });
            }

            // Load sections for edit modal
            function loadSections(programId, selectedSection = null) {
                var yearSectionSelect = $('#edit_year_section');
                yearSectionSelect.prop('disabled', true);
                yearSectionSelect.html('<option value="">Select Year and Section</option>');
                
                if (programId) {
                    $.get('get_programs_sections.php', { 
                        action: 'get_sections',
                        program_id: programId
                    }, function(response) {
                        if (response.success) {
                            var sections = response.data;
                            sections.forEach(function(section) {
                                yearSectionSelect.append($('<option>', {
                                    value: section.section,
                                    text: section.section
                                }));
                            });
                            yearSectionSelect.prop('disabled', false);
                            if (selectedSection) {
                                yearSectionSelect.val(selectedSection);
                            }
                        }
                    });
                }
            }

            // Handle program change in edit modal
            $('#edit_program').change(function() {
                loadSections($(this).val());
            });

            // Update form submission handling
            $('#editStudentForm').on('submit', function(e) {
                e.preventDefault();
                
                // Validate required fields
                var requiredFields = ['edit_id_number', 'edit_full_name', 'edit_program', 'edit_year_section', 'edit_email'];
                var missingFields = [];
                
                requiredFields.forEach(function(field) {
                    if (!$('#' + field).val()) {
                        missingFields.push(field.replace('edit_', '').replace('_', ' '));
                    }
                });
                
                if (missingFields.length > 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Required Fields Missing',
                        text: 'Please fill in all required fields: ' + missingFields.join(', ')
                    });
                    return;
                }

                // Validate contact number if provided
                var contactNumber = $('#edit_contact_number').val();
                if (contactNumber && !/^09\d{9}$/.test(contactNumber)) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid Contact Number',
                        text: 'Contact number should start with 09 and be 11 digits long'
                    });
                    return;
                }

                // Submit form using AJAX
                $.ajax({
                    type: 'POST',
                    url: 'update_student.php',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                Swal.fire({
                    icon: 'success',
                                title: 'Success',
                                text: 'Student updated successfully',
                    showConfirmButton: false,
                                timer: 1500
                            }).then(function() {
                                $('#editStudentModal').modal('hide');
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.error || 'Failed to update student'
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to update student. Please try again.'
                        });
                    }
                });
            });

            // Update file input label when file is selected
            $('.custom-file-input').on('change', function() {
                var fileName = $(this).val().split('\\').pop();
                $(this).next('.custom-file-label').html(fileName);
            });

            // Handle import form submission
            $('#importStudentForm').on('submit', function(e) {
                e.preventDefault();
                
                var formData = new FormData(this);
                
                $.ajax({
                    type: 'POST',
                    url: $(this).attr('action'),
                    data: formData,
                    processData: false,
                    contentType: false,
                    beforeSend: function() {
                        // Disable submit button and show loading state
                        $('#importStudentForm button[type="submit"]').prop('disabled', true).html(
                            '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Importing...'
                        );
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: response.message,
                                showConfirmButton: false,
                                timer: 1500
                            }).then(function() {
                                $('#importStudentModal').modal('hide');
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.error
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to import students. Please try again.'
                        });
                    },
                    complete: function() {
                        // Reset button state
                        $('#importStudentForm button[type="submit"]').prop('disabled', false).html('Import Students');
                    }
                });
            });
        });

        function viewStudent(studentId) {
            $.get('get_student.php', { id: studentId }, function(response) {
                if (response.success) {
                    var student = response.data;
                    $('#view_id_number').text(student.id_number);
                    $('#view_full_name').text(student.full_name);
                    $('#view_program').text(student.program_name);
                    $('#view_section').text(student.section);
                    $('#view_contact_number').text(student.contact_number || 'N/A');
                    $('#view_email').text(student.email);
                    $('#viewStudentModal').modal('show');
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to load student details'
                    });
                }
            });
        }

        function editStudent(studentId) {
            $.get('get_student.php', { id: studentId }, function(response) {
                if (response.success) {
                    var student = response.data;
                    $('#edit_student_id').val(student.id);
                    $('#edit_id_number').val(student.id_number);
                    $('#edit_full_name').val(student.full_name);
                    $('#edit_contact_number').val(student.contact_number);
                    $('#edit_email').val(student.email);
                    
                    // Load programs and set selected program
                    $.get('get_programs_sections.php', { action: 'get_programs' }, function(response) {
                        if (response.success) {
                            var programs = response.data;
                            var programSelect = $('#edit_program');
                            programSelect.html('<option value="">Select Program</option>');
                            
                            // Find the matching program
                            var selectedProgramId = null;
                            programs.forEach(function(program) {
                                programSelect.append($('<option>', {
                                    value: program.id,
                                    text: program.code + ' - ' + program.name,
                                    'data-code': program.code
                                }));
                                
                                if (program.code === student.program_code) {
                                    selectedProgramId = program.id;
                                }
                            });
                            
                            if (selectedProgramId) {
                                programSelect.val(selectedProgramId);
                                
                                // Load sections for the selected program
                                $.get('get_programs_sections.php', { 
                                    action: 'get_sections',
                                    program_id: selectedProgramId
                                }, function(response) {
                                    if (response.success) {
                                        var sections = response.data;
                                        var yearSectionSelect = $('#edit_year_section');
                                        yearSectionSelect.html('<option value="">Select Year and Section</option>');
                                        
                                        sections.forEach(function(section) {
                                            yearSectionSelect.append($('<option>', {
                                                value: section.section,
                                                text: section.section
                                            }));
                                        });
                                        
                                        yearSectionSelect.val(student.section);
                                        yearSectionSelect.prop('disabled', false);
                                    }
                                });
                            }
                        }
                    });
                    
                    $('#editStudentModal').modal('show');
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to load student details'
                    });
                }
            });
        }

        function generateGoodmoral(studentId) {
            window.location.href = `generate_goodmoral.php?id=${studentId}`;
        }
    </script>
</body>
</html> 