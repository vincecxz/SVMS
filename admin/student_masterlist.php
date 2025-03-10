<?php
require_once '../config/database.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    // Validate required fields
    $required_fields = ['id_number', 'full_name', 'program', 'year_section'];
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
    $email = isset($_POST['email']) ? mysqli_real_escape_string($conn, $_POST['email']) : null;

    // Validate email format if provided
    if (!empty($email)) {
        $email_pattern = '/^[a-zA-ZñÑ0-9._%+-]+@[a-zA-ZñÑ0-9.-]+\.[a-zA-Z]{2,}$/';
        if (!preg_match($email_pattern, $email)) {
            echo json_encode(['error' => 'Invalid email format']);
            exit;
        }
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
<body class="hold-transition sidebar-mini layout-fixed">
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
            
            <!-- Floating Action Menu -->
            <div class="fab-container">
                <div class="fab fab-main">
                    <i class="fas fa-plus"></i>
                </div>
                <ul class="fab-options">
                    <li>
                        <span class="fab-label">Add Student</span>
                        <div class="fab-button fab-secondary" data-toggle="modal" data-target="#addStudentModal">
                            <i class="fas fa-user-plus"></i>
                        </div>
                    </li>
                    <li>
                        <span class="fab-label">Import Students</span>
                        <div class="fab-button fab-secondary" data-toggle="modal" data-target="#importStudentModal">
                            <i class="fas fa-file-import"></i>
                        </div>
                    </li>
                </ul>
            </div>

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h3 class="card-title">Student Records</h3>
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
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-success btn-sm" onclick="showGoodMoralModal('<?php echo $row['id']; ?>', '<?php echo $row['full_name']; ?>')" title="Generate Good Moral">
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
    <div class="modal fade" id="addStudentModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h4 class="modal-title text-white">Add New Student</h4>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="addStudentForm" method="POST">
                    <div class="modal-body">
                        <div class="row">
                            <!-- Left Column -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="id_number">ID Number <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="id_number" name="id_number" required>
                                </div>
                                <div class="form-group">
                                    <label for="full_name">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="full_name" name="full_name" required>
                                </div>
                                <div class="form-group">
                                    <label for="email">Email Address</label>
                                    <input type="email" class="form-control" id="email" name="email">
                                </div>
                            </div>
                            <!-- Right Column -->
                            <div class="col-md-6">
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
                                </div>
                                <div class="form-group">
                                    <label for="contact_number">Contact Number</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                        </div>
                                        <input type="text" class="form-control" id="contact_number" name="contact_number" 
                                               placeholder="09XXXXXXXXX" pattern="09[0-9]{9}">
                                    </div>
                                    <small class="form-text text-muted">Format: 09XXXXXXXXX</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button> -->
                        <button type="submit" class="btn btn-primary">Add Student</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- View Student Modal -->
    <div class="modal fade" id="viewStudentModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h4 class="modal-title text-white">Student Details</h4>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 font-weight-bold">ID Number</div>
                                <div class="col-md-8" id="view_id_number"></div>
                            </div>
                            <hr class="my-2">
                            <div class="row">
                                <div class="col-md-4 font-weight-bold">Full Name</div>
                                <div class="col-md-8" id="view_full_name"></div>
                            </div>
                            <hr class="my-2">
                            <div class="row">
                                <div class="col-md-4 font-weight-bold">Program</div>
                                <div class="col-md-8" id="view_program"></div>
                            </div>
                            <hr class="my-2">
                            <div class="row">
                                <div class="col-md-4 font-weight-bold">Section</div>
                                <div class="col-md-8" id="view_section"></div>
                            </div>
                            <hr class="my-2">
                            <div class="row">
                                <div class="col-md-4 font-weight-bold">Contact Number</div>
                                <div class="col-md-8" id="view_contact_number"></div>
                            </div>
                            <hr class="my-2">
                            <div class="row">
                                <div class="col-md-4 font-weight-bold">Email Address</div>
                                <div class="col-md-8" id="view_email"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Student Modal -->
    <div class="modal fade" id="editStudentModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-warning py-3">
                    <h4 class="modal-title">Edit Student</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <form id="editStudentForm" method="POST">
                        <input type="hidden" id="edit_student_id" name="student_id">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="edit_id_number" class="mb-2">ID Number <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="edit_id_number" name="id_number" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="edit_full_name" class="mb-2">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="edit_full_name" name="full_name" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="edit_program" class="mb-2">Program <span class="text-danger">*</span></label>
                                    <select class="form-control" id="edit_program" name="program" required>
                                        <option value="">Select Program</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="edit_year_section" class="mb-2">Year and Section <span class="text-danger">*</span></label>
                                    <select class="form-control" id="edit_year_section" name="year_section" required>
                                        <option value="">Select Year and Section</option>
                                    </select>
                                    <input type="hidden" id="edit_course_year_section" name="course_year_section">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="edit_contact_number" class="mb-2">Contact Number</label>
                                    <input type="text" class="form-control" id="edit_contact_number" name="contact_number" 
                                           placeholder="09XXXXXXXXX" pattern="09[0-9]{9}">
                                    <small class="form-text text-muted mt-1">Format: 09XXXXXXXXX</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="edit_email" class="mb-2">Email Address <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="edit_email" name="email" required>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer px-0 pb-0 pt-3">
                            <button type="submit" class="btn btn-warning">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Import Student Modal -->
    <div class="modal fade" id="importStudentModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h4 class="modal-title">Import Students</h4>
                    <button type="button" class="close text-black" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                </div>
                <form id="importStudentForm" method="POST" enctype="multipart/form-data" action="import_students.php">
                    <div class="modal-body">
                        <div class="alert">
                            <p class="mb-0 text-red"><strong>Please download the template file and fill in the student details before importing.</strong></p>
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
                        <!-- <button type="button" class="btn btn-default" data-dismiss="modal">Close</button> -->
                        <button type="submit" class="btn btn-primary">Import Students</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- View Sanctions Modal -->
    <div class="modal fade" id="viewSanctionsModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h5 class="modal-title">Active Sanctions</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="sanctionsContent">
                        <!-- Sanctions will be loaded here -->
                    </div>
                </div>
                <!-- <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div> -->
            </div>
        </div>
        
    </div>
    <?php include '../includes/admin/footer.php'; ?>
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
                var requiredFields = ['id_number', 'full_name', 'program', 'year_section'];
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
                var requiredFields = ['edit_id_number', 'edit_full_name', 'edit_program', 'edit_year_section'];
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

            // Handle purpose selection change
            $('#purpose').change(function() {
                if ($(this).val() === 'Others') {
                    $('#otherPurposeGroup').show();
                    $('#other_purpose').prop('required', true);
                } else {
                    $('#otherPurposeGroup').hide();
                    $('#other_purpose').prop('required', false);
                }
            });

            // Handle good moral form submission
            $('#goodMoralForm').on('submit', function(e) {
                e.preventDefault();
                
                // Show loading state
                Swal.fire({
                    title: 'Generating Certificate',
                    text: 'Please wait...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Get form data
                const formData = {
                    student_id: $('#student_id').val(),
                    purpose: $('#purpose').val() === 'Others' ? $('#other_purpose').val() : $('#purpose').val(),
                    school_year: $('#school_year').val()
                };

                // Submit form using AJAX
                $.ajax({
                    url: 'generate_goodmoral.php',
                    method: 'POST',
                    data: formData,
                    xhrFields: {
                        responseType: 'blob'
                    },
                    success: function(response) {
                        Swal.close();
                        
                        // Create blob URL for preview
                        const blob = new Blob([response], { type: 'application/pdf' });
                        const url = window.URL.createObjectURL(blob);
                        
                        // Show preview modal
                        $('#pdfPreviewFrame').attr('src', url);
                        $('#generateGoodMoralModal').modal('hide');
                        $('#pdfPreviewModal').modal('show');
                        
                        // Handle download button click
                        $('#downloadPdf').off('click').on('click', function() {
                            const a = document.createElement('a');
                            a.href = url;
                            a.download = 'good_moral_certificate.pdf';
                            document.body.appendChild(a);
                            a.click();
                            window.URL.revokeObjectURL(url);
                            document.body.removeChild(a);
                            $('#pdfPreviewModal').modal('hide');
                        });
                        
                        // Clean up blob URL when modal is closed
                        $('#pdfPreviewModal').on('hidden.bs.modal', function() {
                            window.URL.revokeObjectURL(url);
                            $('#goodMoralForm')[0].reset();
                        });
                    },
                    error: function(xhr, status, error) {
                        Swal.close();
                        console.error('Error:', error);
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to generate certificate. Please try again.'
                        });
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

        function showGoodMoralModal(studentId, studentName) {
            // Check for active sanctions first
            $.ajax({
                url: 'check_student_sanctions.php',
                method: 'POST',
                data: { student_id: studentId },
                dataType: 'json',
                success: function(response) {
                    if (response.has_pending_sanctions) {
                        // Show warning modal with sanction details
                        Swal.fire({
                            title: 'Cannot Generate Good Moral',
                            html: `
                                <div class="text-left">
                                    <p class="text-danger"><i class="fas fa-exclamation-triangle"></i> Student has active sanctions that need to be resolved first.</p>
                                    <button type="button" class="btn btn-danger btn-block mt-3" 
                                        onclick="viewSanctions('${studentId}')">
                                        <i class="fas fa-eye"></i> View Sanctions
                                    </button>
                                </div>
                            `,
                            icon: 'warning',
                            showConfirmButton: false,
                            showCloseButton: true
                        });
                    } else {
                        // Show success message and proceed with good moral generation
                        Swal.fire({
                            title: 'Good Moral Certificate',
                            html: `
                                <div class="text-left">
                                    <p class="text-success"><i class="fas fa-check-circle"></i> Student has no active sanctions.</p>
                                    
                                </div>
                            `,
                            icon: 'success',
                           
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Proceed with showing the good moral form
                                $('#student_id').val(studentId);
                                $('#student_name').val(studentName);
                                $('#generateGoodMoralModal').modal('show');
                            }
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        title: 'Error',
                        text: 'Failed to check student sanctions. Please try again.',
                        icon: 'error'
                    });
                }
            });
        }

        function viewSanctions(studentId) {
            // Close the previous SweetAlert
            Swal.close();
            
            // Show the sanctions modal
            $('#viewSanctionsModal').modal('show');
            
            // Load sanctions content
            $.ajax({
                url: 'get_student_sanctions.php',
                method: 'POST',
                data: { student_id: studentId },
                success: function(response) {
                    $('#sanctionsContent').html(response);
                },
                error: function() {
                    $('#sanctionsContent').html(
                        '<div class="alert alert-danger">' +
                        '<i class="fas fa-exclamation-circle"></i> Failed to load sanction details.' +
                        '</div>'
                    );
                }
            });
        }
    </script>
    <style>
        /* Floating Action Button Menu */
        .fab-container {
            position: fixed !important;
            bottom: 30px !important;
            right: 30px !important;
            z-index: 1050 !important;
        }

        .fab-main {
            width: 60px !important;
            height: 60px !important;
            background: #00b0ff !important;
            border-radius: 50% !important;
            box-shadow: 0 4px 10px rgba(0, 176, 255, 0.3) !important;
            color: white !important;
            text-align: center !important;
            line-height: 60px !important;
            cursor: pointer !important;
            transition: all 0.3s ease !important;
        }

        .fab-main:hover {
            transform: scale(1.1) !important;
            box-shadow: 0 6px 15px rgba(0, 176, 255, 0.4) !important;
        }

        .fab-main i {
            font-size: 24px !important;
            transition: transform 0.3s ease !important;
        }

        .fab-options {
            list-style-type: none !important;
            margin: 0 !important;
            padding: 0 !important;
            position: absolute !important;
            bottom: 70px !important;
            right: 0 !important;
            opacity: 0 !important;
            visibility: hidden !important;
            transition: all 0.3s ease !important;
            transform: scale(0.5) !important;
        }

        .fab-container:hover .fab-options,
        .fab-container:focus-within .fab-options {
            opacity: 1 !important;
            visibility: visible !important;
            transform: scale(1) !important;
        }

        .fab-options li {
            display: flex !important;
            align-items: center !important;
            margin-bottom: 10px !important;
        }

        .fab-button {
            width: 48px !important;
            height: 48px !important;
            border-radius: 50% !important;
            text-align: center !important;
            line-height: 48px !important;
            cursor: pointer !important;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2) !important;
            transition: all 0.3s ease !important;
        }

        .fab-secondary {
            background: #fff !important;
            color: #00b0ff !important;
        }

        .fab-secondary:hover {
            background: #00b0ff !important;
            color: #fff !important;
            transform: scale(1.1) !important;
        }

        .fab-label {
            padding: 6px 12px !important;
            background: rgba(0, 0, 0, 0.8) !important;
            color: white !important;
            border-radius: 4px !important;
            margin-right: 10px !important;
            font-size: 14px !important;
            font-weight: 500 !important;
            position: relative !important;
        }

        .fab-label:after {
            content: '' !important;
            position: absolute !important;
            right: -5px !important;
            top: 50% !important;
            transform: translateY(-50%) !important;
            border-left: 5px solid rgba(0, 0, 0, 0.8) !important;
            border-top: 5px solid transparent !important;
            border-bottom: 5px solid transparent !important;
        }

        @media (max-width: 768px) {
            .fab-container {
                bottom: 20px !important;
                right: 20px !important;
            }

            .fab-main {
                width: 50px !important;
                height: 50px !important;
                line-height: 50px !important;
            }

            .fab-main i {
                font-size: 20px !important;
            }

            .fab-button {
                width: 40px !important;
                height: 40px !important;
                line-height: 40px !important;
            }

            .fab-label {
                font-size: 12px !important;
            }
        }

        /* PDF Preview Modal Styles */
        #pdfPreviewModal .modal-dialog {
            max-width: 90%;
            margin: 1.75rem auto;
        }

        #pdfPreviewModal .modal-content {
            height: 90vh;
        }

        #pdfPreviewModal .modal-body {
            padding: 1rem;
            background-color: #f8f9fa;
        }

        #pdfPreviewContainer {
            background-color: #fff;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        #pdfPreviewFrame {
            background-color: #fff;
        }

        #pdfPreviewModal .modal-footer {
            border-top: 1px solid #dee2e6;
            padding: 1rem;
            background-color: #fff;
        }

        #downloadPdf {
            background-color: #007bff;
            border-color: #007bff;
            padding: 0.5rem 1.5rem;
        }

        #downloadPdf:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }

        /* Edit Student Modal Styles */
        #editStudentModal .modal-dialog {
            max-width: 700px;
        }

        #editStudentModal .form-control-sm {
            height: calc(1.8em + 0.5rem + 2px);
            padding: 0.375rem 0.75rem;
            font-size: 0.95rem;
        }

        #editStudentModal .modal-header {
            border-bottom: 1px solid #dee2e6;
            padding: 0.8rem 1rem;
        }

        #editStudentModal .modal-title {
            font-size: 1.25rem;
            font-weight: 600;
        }

        #editStudentModal .modal-body {
            padding: 1rem 1.5rem;
        }

        #editStudentModal .form-group {
            margin-bottom: 0.8rem;
        }

        #editStudentModal label {
            font-size: 0.95rem;
            font-weight: 500;
            margin-bottom: 0.3rem;
        }

        #editStudentModal .form-text {
            font-size: 0.85rem;
            margin-top: 0.2rem;
        }

        #editStudentModal .btn-sm {
            padding: 0.375rem 1.2rem;
            font-size: 0.95rem;
        }

        #editStudentModal .modal-footer {
            padding-top: 0.8rem;
        }

        /* Add these styles to your existing styles */
        #addStudentModal .modal-content {
            border: none;
            border-radius: 8px;
            box-shadow: 0 2px 20px rgba(0,0,0,0.1);
        }

        #addStudentModal .modal-header {
            padding: 1.2rem;
            border-bottom: 1px solid #e9ecef;
        }

        #addStudentModal .modal-body {
            padding: 1.5rem;
        }

        #addStudentModal .form-group {
            margin-bottom: 1.2rem;
        }

        #addStudentModal label {
            font-weight: 500;
            margin-bottom: 0.5rem;
            color: #2c3e50;
        }

        #addStudentModal .form-control {
            border-radius: 4px;
            border: 1px solid #ced4da;
            padding: 0.5rem 0.75rem;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }

        #addStudentModal .form-control:focus {
            border-color: #80bdff;
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
        }

        #addStudentModal .input-group-text {
            background-color: #f8f9fa;
            border: 1px solid #ced4da;
        }

        #addStudentModal .modal-footer {
            padding: 1rem 1.5rem;
            border-top: 1px solid #e9ecef;
        }

        #addStudentModal .btn {
            padding: 0.5rem 1.5rem;
            font-weight: 500;
        }

        #addStudentModal .text-danger {
            font-weight: bold;
        }

        #addStudentModal .form-text {
            color: #6c757d;
            font-size: 0.85rem;
            margin-top: 0.25rem;
        }

        /* Dark mode support */
        .dark-mode #addStudentModal .modal-content {
            background-color: #343a40;
            color: #fff;
        }

        .dark-mode #addStudentModal .modal-header {
            border-bottom-color: #454d55;
        }

        .dark-mode #addStudentModal .modal-footer {
            border-top-color: #454d55;
        }

        .dark-mode #addStudentModal .form-control {
            background-color: #454d55;
            border-color: #545b62;
            color: #fff;
        }

        .dark-mode #addStudentModal .form-control:focus {
            border-color: #80bdff;
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
        }

        .dark-mode #addStudentModal .input-group-text {
            background-color: #454d55;
            border-color: #545b62;
            color: #fff;
        }
    </style>
</body>
</html> 