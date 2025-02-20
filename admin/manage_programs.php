<?php
require_once '../config/database.php';

// Fetch all programs with their sections
$query = "SELECT p.id, p.code, p.name, GROUP_CONCAT(s.section ORDER BY s.section) as sections 
          FROM programs p 
          LEFT JOIN sections s ON p.id = s.program_id 
          GROUP BY p.id 
          ORDER BY p.code";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Programs & Sections - SAO</title>
    
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
                            <h1>Programs & Sections Management</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                                <li class="breadcrumb-item active">Programs & Sections</li>
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
                                        <h3 class="card-title">Programs List</h3>
                                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addProgramModal">
                                            <i class="fas fa-plus"></i> Add Program
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <table id="programsTable" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th width="15%">Actions</th>
                                                <th>Program Code</th>
                                                <th>Program Name</th>
                                                <th>Sections</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                                <tr>
                                                    <td>
                                                        <div class="btn-group">
                                                            <button type="button" class="btn btn-warning btn-sm" onclick="editProgram('<?php echo $row['id']; ?>')" title="Edit Program">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-info btn-sm" onclick="manageSections('<?php echo $row['id']; ?>')" title="Manage Sections">
                                                                <i class="fas fa-list"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-danger btn-sm" onclick="deleteProgram('<?php echo $row['id']; ?>')" title="Delete Program">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($row['code']); ?></td>
                                                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                                                    <td><?php echo $row['sections'] ? htmlspecialchars($row['sections']) : 'No sections'; ?></td>
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

    <!-- Add Program Modal -->
    <div class="modal fade" id="addProgramModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Add Program</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="addProgramForm">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="program_code">Program Code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="program_code" name="program_code" required>
                        </div>
                        <div class="form-group">
                            <label for="program_name">Program Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="program_name" name="program_name" required>
                        </div>
                        <div class="form-group">
                            <label for="sections">Initial Sections (comma-separated)</label>
                            <input type="text" class="form-control" id="sections" name="sections" placeholder="e.g., 1A, 1B, 2A">
                            <small class="form-text text-muted">Leave empty if no sections to add yet</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Add Program</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Program Modal -->
    <div class="modal fade" id="editProgramModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Edit Program</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="editProgramForm">
                    <input type="hidden" id="edit_program_id" name="program_id">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="edit_program_code">Program Code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_program_code" name="program_code" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_program_name">Program Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_program_name" name="program_name" required>
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

    <!-- Manage Sections Modal -->
    <div class="modal fade" id="manageSectionsModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Manage Sections</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="manageSectionsForm">
                    <input type="hidden" id="sections_program_id" name="program_id">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Program:</label>
                            <p id="sections_program_name" class="form-control-static"></p>
                        </div>
                        <div class="form-group">
                            <label for="program_sections">Sections (comma-separated) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="program_sections" name="sections" required>
                            <small class="form-text text-muted">e.g., 1A, 1B, 2A, 2B</small>
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
            var table = $('#programsTable').DataTable({
                "responsive": true,
                "lengthChange": true,
                "autoWidth": false,
                "pageLength": 25,
                "order": [[1, "asc"]], // Sort by program code by default
                "columnDefs": [
                    {
                        "targets": 0, // Actions column
                        "orderable": false
                    }
                ]
            });

            // Add Program Form Submission
            $('#addProgramForm').on('submit', function(e) {
                e.preventDefault();
                
                $.ajax({
                    type: 'POST',
                    url: 'program_actions.php',
                    data: $(this).serialize() + '&action=add',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: response.message,
                                showConfirmButton: false,
                                timer: 1500
                            }).then(function() {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to add program. Please try again.'
                        });
                    }
                });
            });

            // Edit Program Form Submission
            $('#editProgramForm').on('submit', function(e) {
                e.preventDefault();
                
                $.ajax({
                    type: 'POST',
                    url: 'program_actions.php',
                    data: $(this).serialize() + '&action=edit',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: response.message,
                                showConfirmButton: false,
                                timer: 1500
                            }).then(function() {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to update program. Please try again.'
                        });
                    }
                });
            });

            // Manage Sections Form Submission
            $('#manageSectionsForm').on('submit', function(e) {
                e.preventDefault();
                
                $.ajax({
                    type: 'POST',
                    url: 'program_actions.php',
                    data: $(this).serialize() + '&action=update_sections',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: response.message,
                                showConfirmButton: false,
                                timer: 1500
                            }).then(function() {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to update sections. Please try again.'
                        });
                    }
                });
            });
        });

        function editProgram(programId) {
            $.get('program_actions.php', { 
                action: 'get_program',
                program_id: programId
            }, function(response) {
                if (response.success) {
                    $('#edit_program_id').val(response.data.id);
                    $('#edit_program_code').val(response.data.code);
                    $('#edit_program_name').val(response.data.name);
                    $('#editProgramModal').modal('show');
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to load program details'
                    });
                }
            });
        }

        function manageSections(programId) {
            $.get('program_actions.php', { 
                action: 'get_program_sections',
                program_id: programId
            }, function(response) {
                if (response.success) {
                    $('#sections_program_id').val(response.data.id);
                    $('#sections_program_name').text(response.data.code + ' - ' + response.data.name);
                    $('#program_sections').val(response.data.sections);
                    $('#manageSectionsModal').modal('show');
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to load program sections'
                    });
                }
            });
        }

        function deleteProgram(programId) {
            Swal.fire({
                title: 'Are you sure?',
                text: "This will delete the program and all associated sections. This action cannot be undone!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post('program_actions.php', {
                        action: 'delete',
                        program_id: programId
                    }, function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: response.message,
                                showConfirmButton: false,
                                timer: 1500
                            }).then(function() {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message
                            });
                        }
                    });
                }
            });
        }
    </script>
</body>
</html> 