<?php
include('../config/database.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Offenses & Sanctions - SAO</title>
    
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
    <link rel="stylesheet" href="../plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
</head>
<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <?php include('../includes/admin/navbar.php'); ?>
        <?php include('../includes/admin/sidebar.php'); ?>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1>Manage Offenses & Sanctions</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                                <li class="breadcrumb-item active">Offenses & Sanctions</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </section>

             <!-- Floating Add Button -->
             <button type="button" class="btn-float"  data-toggle="modal" data-target="#addOffenseModal">
                <i class="fas fa-plus fa-lg"></i>
                <span class="btn-float-label">Add Offense</span>
            </button>

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h3 class="card-title"> <strong> Section 1. Academic Violations Committed Either Offline or Online</strong></h3> 
                                </div>
                                <div class="card-body">
                                    <table id="offensesTable" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                
                                                <th>Offense Description</th>
                                       
                                                <th>1st Sanction</th>
                                                <th>2nd Sanction</th>
                                                <th>3rd Sanction</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $query = "SELECT * FROM sec1";
                                            $result = $conn->query($query);
                                            
                                            while($row = $result->fetch_assoc()) {
                                                echo "<tr>";
                                               
                                                echo "<td>".$row['description']."</td>";
                                            
                                                echo "<td>".$row['first_sanction']."</td>";
                                                echo "<td>".$row['second_sanction']."</td>";
                                                echo "<td>".$row['third_sanction']."</td>";
                                                echo "<td>
                                                    <button class='btn btn-sm btn-primary edit-offense' data-id='".$row['id']."'><i class='fas fa-edit'></i></button>
                                                    <button class='btn btn-sm btn-danger delete-offense' data-id='".$row['id']."'><i class='fas fa-trash'></i></button>
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

        <!-- Add Offense Modal -->
        <div class="modal fade" id="addOffenseModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New Offense</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="addOffenseForm">
                            <div class="form-group">
                                <label>Offense Description</label>
                                <textarea class="form-control" name="description" required rows="3"></textarea>
                            </div>
                            
                            <div class="form-group">
                                <label>1st Sanction</label>
                                <input type="text" class="form-control" name="first_sanction" required>
                            </div>
                            <div class="form-group">
                                <label>2nd Sanction</label>
                                <input type="text" class="form-control" name="second_sanction" required>
                            </div>
                            <div class="form-group">
                                <label>3rd Sanction</label>
                                <input type="text" class="form-control" name="third_sanction" required>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="saveOffense">Save Offense</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Offense Modal -->
        <div class="modal fade" id="editOffenseModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Offense</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="editOffenseForm">
                            <input type="hidden" name="offense_id" id="edit_offense_id">
                            <div class="form-group">
                                <label>Offense Description</label>
                                <textarea class="form-control" name="description" id="edit_description" required rows="3"></textarea>
                            </div>
                           
                            <div class="form-group">
                                <label>1st Sanction</label>
                                <input type="text" class="form-control" name="first_sanction" id="edit_first_sanction" required>
                            </div>
                            <div class="form-group">
                                <label>2nd Sanction</label>
                                <input type="text" class="form-control" name="second_sanction" id="edit_second_sanction" required>
                            </div>
                            <div class="form-group">
                                <label>3rd Sanction</label>
                                <input type="text" class="form-control" name="third_sanction" id="edit_third_sanction" required>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="updateOffense">Update Offense</button>
                    </div>
                </div>
            </div>
        </div>

        <?php include('../includes/admin/footer.php'); ?>
    </div>

    <!-- jQuery -->
    <script src="../plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables -->
    <script src="../plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="../plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="../plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <!-- AdminLTE App -->
    <script src="../dist/js/adminlte.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="../plugins/sweetalert2/sweetalert2.min.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize DataTable
            $('#offensesTable').DataTable({
                "responsive": true,
                "autoWidth": false
            });

            // Add new offense
            $('#saveOffense').click(function() {
                $.ajax({
                    url: 'offense_actions.php',
                    method: 'POST',
                    data: $('#addOffenseForm').serialize() + '&action=add',
                    success: function(response) {
                        if(response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: 'Offense added successfully'
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message || 'Failed to add offense'
                            });
                        }
                    }
                });
            });

            // Load offense data for editing
            $('.edit-offense').click(function() {
                const id = $(this).data('id');
                $.ajax({
                    url: 'offense_actions.php',
                    method: 'POST',
                    data: { action: 'get', id: id },
                    success: function(response) {
                        if(response.success) {
                            const offense = response.data;
                            $('#edit_offense_id').val(offense.id);
                            $('#edit_description').val(offense.description);
                    
                            $('#edit_first_sanction').val(offense.first_sanction);
                            $('#edit_second_sanction').val(offense.second_sanction);
                            $('#edit_third_sanction').val(offense.third_sanction);
                            $('#editOffenseModal').modal('show');
                        }
                    }
                });
            });

            // Update offense
            $('#updateOffense').click(function() {
                $.ajax({
                    url: 'offense_actions.php',
                    method: 'POST',
                    data: $('#editOffenseForm').serialize() + '&action=update',
                    success: function(response) {
                        if(response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: 'Offense updated successfully'
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message || 'Failed to update offense'
                            });
                        }
                    }
                });
            });

            // Delete offense
            $('.delete-offense').click(function() {
                const id = $(this).data('id');
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: 'offense_actions.php',
                            method: 'POST',
                            data: { action: 'delete', id: id },
                            success: function(response) {
                                if(response.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Deleted!',
                                        text: 'Offense has been deleted.'
                                    }).then(() => {
                                        location.reload();
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: response.message || 'Failed to delete offense'
                                    });
                                }
                            }
                        });
                    }
                });
            });
        });
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