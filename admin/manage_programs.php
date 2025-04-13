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

              <!-- Floating Add Button -->
              <button type="button" class="btn-float"  data-toggle="modal" data-target="#addProgramModal">
                <i class="fas fa-plus fa-lg"></i>
                <span class="btn-float-label">Add Program</span>
            </button>

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h3 class="card-title">Programs List</h3>
                                        <!-- <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addProgramModal">
                                            <i class="fas fa-plus"></i> Add Program
                                        </button> -->
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
        <?php include '../includes/admin/footer.php'; ?>
    </div>

    <!-- Add Program Modal -->
    <div class="modal fade" id="addProgramModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary">
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
                        <!-- <button type="button" class="btn btn-default" data-dismiss="modal">Close</button> -->
                        <button type="submit" class="btn btn-primary">Add Program</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Program Modal -->
    <div class="modal fade" id="editProgramModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning">
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
                        <div class="form-group">
                            <label for="edit_program_sections">Sections <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_program_sections" name="sections" 
                                   placeholder="e.g., 1A, 1B, 2A" required>
                            <small class="form-text text-muted">Enter sections separated by commas (e.g., 1A, 1B, 2A)</small>
                            <div id="current_sections" class="mt-2">
                                <small class="text-red">Current sections: <span id="current_sections_list"></span></small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <!-- <button type="button" class="btn btn-default" data-dismiss="modal">Close</button> -->
                        <button type="submit" class="btn btn-warning">Save Changes</button>
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
                
                // Basic validation for sections
                const sections = $('#edit_program_sections').val().trim();
                if (!sections) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Please enter at least one section'
                    });
                    return;
                }

                // Submit the form if validation passes
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
                    
                    // Handle sections
                    if (response.data.sections) {
                        const sections = response.data.sections;
                        $('#edit_program_sections').val(sections);
                        $('#current_sections_list').text(sections);
                        $('#current_sections').show();
                    } else {
                        $('#edit_program_sections').val('');
                        $('#current_sections').hide();
                    }
                    
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

<style>
         
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