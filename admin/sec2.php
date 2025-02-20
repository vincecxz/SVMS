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
                                    <h3 class="card-title"> <strong> Section 2. Academic Violations Committed Either Offline or Online</strong></h3> 
                                </div>
                                
                                <!-- Global Search Bar -->
                                <div class="card-body pb-0">
                                    <div class="row">
                                        <div class="col-md-6 offset-md-3">
                                            <div class="input-group mb-3">
                                                <input type="text" id="globalSearch" class="form-control" placeholder="Search for any offense...">
                                                <div class="input-group-append">
                                                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                                                </div>
                                            </div>
                                            <div id="searchResults" class="dropdown-menu w-100" style="max-height: 300px; overflow-y: auto;">
                                                <!-- Search results will be populated here -->
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-header">
                                    <!-- <h3 class="card-title">Light Offense</h3> -->
                                </div>
                                <div class="card-body">
                                <h4 class="card-title">Light Offense</h4>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <div class="d-flex align-items-center">
                                                <label class="mr-2 mb-0">Show</label>
                                                <select id="entriesSelect1" class="form-control form-control-sm" style="width: 60px;">
                                                    <option value="10">10</option>
                                                    <option value="25">25</option>
                                                    <option value="50">50</option>
                                                    <option value="100">100</option>
                                                </select>
                                                <label class="ml-2 mb-0">entries</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="d-flex justify-content-end">
                                                <label class="mr-2 mb-0">Search:</label>
                                                <input type="search" class="form-control form-control-sm" id="searchInput1" style="width: 200px;">
                                            </div>
                                        </div>
                                    </div>
                                    <table id="lightOffensesTable" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Offense Description</th>
                                                <th>1st Sanction</th>
                                                <th>2nd Sanction</th>
                                                <th>3rd Sanction</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $query = "SELECT * FROM sec2 WHERE category = 'Light' ORDER BY id";
                                            $result = $conn->query($query);
                                            
                                            while($row = $result->fetch_assoc()) {
                                                echo "<tr>";
                                                echo "<td>".$row['id']."</td>";
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

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                   
                                </div>
                                <div class="card-body">
                                <h3 class="card-title">Serious Offense</h3>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <div class="d-flex align-items-center">
                                                <label class="mr-2 mb-0">Show</label>
                                                <select id="entriesSelect2" class="form-control form-control-sm" style="width: 60px;">
                                                    <option value="10">10</option>
                                                    <option value="25">25</option>
                                                    <option value="50">50</option>
                                                    <option value="100">100</option>
                                                </select>
                                                <label class="ml-2 mb-0">entries</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="d-flex justify-content-end">
                                                <label class="mr-2 mb-0">Search:</label>
                                                <input type="search" class="form-control form-control-sm" id="searchInput2" style="width: 200px;">
                                            </div>
                                        </div>
                                    </div>
                                    <table id="seriousOffensesTable" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Offense Description</th>
                                                <th>1st Sanction</th>
                                                <th>2nd Sanction</th>
                                                <th>3rd Sanction</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $query = "SELECT * FROM sec2 WHERE category = 'Serious' ORDER BY id";
                                            $result = $conn->query($query);
                                            
                                            while($row = $result->fetch_assoc()) {
                                                echo "<tr>";
                                                echo "<td>".$row['id']."</td>";
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

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    
                                </div>
                                <div class="card-body">
                                <h3 class="card-title">Very Serious Offense</h3>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <div class="d-flex align-items-center">
                                                <label class="mr-2 mb-0">Show</label>
                                                <select id="entriesSelect3" class="form-control form-control-sm" style="width: 60px;">
                                                    <option value="10">10</option>
                                                    <option value="25">25</option>
                                                    <option value="50">50</option>
                                                    <option value="100">100</option>
                                                </select>
                                                <label class="ml-2 mb-0">entries</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="d-flex justify-content-end">
                                                <label class="mr-2 mb-0">Search:</label>
                                                <input type="search" class="form-control form-control-sm" id="searchInput3" style="width: 200px;">
                                            </div>
                                        </div>
                                    </div>
                                    <table id="verySeriousOffensesTable" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Offense Description</th>
                                                <th>1st Sanction</th>
                                                <th>2nd Sanction</th>
                                                <th>3rd Sanction</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $query = "SELECT * FROM sec2 WHERE category = 'Very Serious' ORDER BY id";
                                            $result = $conn->query($query);
                                            
                                            while($row = $result->fetch_assoc()) {
                                                echo "<tr>";
                                                echo "<td>".$row['id']."</td>";
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
                                <label>Category</label>
                                <select class="form-control" name="category" required>
                                    <option value="">Select Category</option>
                                    <option value="Light">Light</option>
                                    <option value="Serious">Serious</option>
                                    <option value="Very Serious">Very Serious</option>
                                </select>
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
                                <label>Category</label>
                                <select class="form-control" name="category" id="edit_category" required>
                                    <option value="Light">Light</option>
                                    <option value="Serious">Serious</option>
                                    <option value="Very Serious">Very Serious</option>
                                </select>
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
            // Initialize DataTables with custom controls
            var lightTable = $('#lightOffensesTable').DataTable({
                "responsive": true,
                "autoWidth": false,
                "pageLength": 10,
                "lengthChange": false,
                "searching": true,
                "ordering": true,
                "info": true,
                "paging": true
            });

            var seriousTable = $('#seriousOffensesTable').DataTable({
                "responsive": true,
                "autoWidth": false,
                "pageLength": 10,
                "lengthChange": false,
                "searching": true,
                "ordering": true,
                "info": true,
                "paging": true
            });

            var verySeriousTable = $('#verySeriousOffensesTable').DataTable({
                "responsive": true,
                "autoWidth": false,
                "pageLength": 10,
                "lengthChange": false,
                "searching": true,
                "ordering": true,
                "info": true,
                "paging": true
            });

            // Custom length menu for each table
            $('#entriesSelect1').on('change', function() {
                lightTable.page.len($(this).val()).draw();
            });

            $('#entriesSelect2').on('change', function() {
                seriousTable.page.len($(this).val()).draw();
            });

            $('#entriesSelect3').on('change', function() {
                verySeriousTable.page.len($(this).val()).draw();
            });

            // Custom search for each table
            $('#searchInput1').on('keyup', function() {
                lightTable.search(this.value).draw();
            });

            $('#searchInput2').on('keyup', function() {
                seriousTable.search(this.value).draw();
            });

            $('#searchInput3').on('keyup', function() {
                verySeriousTable.search(this.value).draw();
            });

            // Add new offense
            $('#saveOffense').click(function() {
                var formData = $('#addOffenseForm').serialize() + '&action=add';
                
                // Validate form
                var category = $('#addOffenseForm select[name="category"]').val();
                var description = $('#addOffenseForm textarea[name="description"]').val();
                var firstSanction = $('#addOffenseForm input[name="first_sanction"]').val();
                var secondSanction = $('#addOffenseForm input[name="second_sanction"]').val();
                var thirdSanction = $('#addOffenseForm input[name="third_sanction"]').val();

                if (!category || !description || !firstSanction || !secondSanction || !thirdSanction) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: 'Please fill in all fields'
                    });
                    return;
                }

                $.ajax({
                    url: 'sec2_actions.php',
                    method: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function(response) {
                        if(response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: 'Offense added successfully'
                            }).then(() => {
                                $('#addOffenseModal').modal('hide');
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message || 'Failed to add offense'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred while saving the offense. Please try again.'
                        });
                        console.error(xhr.responseText);
                    }
                });
            });

            // Load offense data for editing
            $('.edit-offense').click(function() {
                const id = $(this).data('id');
                $.ajax({
                    url: 'sec2_actions.php',
                    method: 'POST',
                    data: { action: 'get', id: id },
                    success: function(response) {
                        if(response.success) {
                            const offense = response.data;
                            $('#edit_offense_id').val(offense.id);
                            $('#edit_description').val(offense.description);
                            $('#edit_category').val(offense.category);
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
                    url: 'sec2_actions.php',
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
                            url: 'sec2_actions.php',
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

            // Store table references
            const tables = {
                light: $('#lightOffensesTable').DataTable(),
                serious: $('#seriousOffensesTable').DataTable(),
                verySerious: $('#verySeriousOffensesTable').DataTable()
            };

            // Global search functionality
            $('#globalSearch').on('keyup', function() {
                const searchTerm = $(this).val().toLowerCase();
                const $resultsContainer = $('#searchResults');
                
                // Remove the minimum character check
                // Clear previous results
                $resultsContainer.empty();

                if (searchTerm === '') {
                    $resultsContainer.removeClass('show');
                    return;
                }

                // Search in all tables
                let results = [];
                
                // Helper function to get table name
                const getTableName = (category) => {
                    switch(category) {
                        case 'Light': return 'Light Offenses';
                        case 'Serious': return 'Serious Offenses';
                        case 'Very Serious': return 'Very Serious Offenses';
                        default: return '';
                    }
                };

                // Function to highlight matching text
                const highlightMatch = (text, term) => {
                    if (!term) return text;
                    const regex = new RegExp(`(${term})`, 'gi');
                    return text.replace(regex, '<span class="highlight-text">$1</span>');
                };

                // Search in Light Offenses
                $('#lightOffensesTable tbody tr').each(function() {
                    const $row = $(this);
                    const text = $row.text().toLowerCase();
                    const description = $row.find('td:eq(1)').text();
                    if (text.includes(searchTerm)) {
                        results.push({
                            category: 'Light',
                            description: description,
                            highlightedDescription: highlightMatch(description, searchTerm),
                            element: $row
                        });
                    }
                });

                // Search in Serious Offenses
                $('#seriousOffensesTable tbody tr').each(function() {
                    const $row = $(this);
                    const text = $row.text().toLowerCase();
                    const description = $row.find('td:eq(1)').text();
                    if (text.includes(searchTerm)) {
                        results.push({
                            category: 'Serious',
                            description: description,
                            highlightedDescription: highlightMatch(description, searchTerm),
                            element: $row
                        });
                    }
                });

                // Search in Very Serious Offenses
                $('#verySeriousOffensesTable tbody tr').each(function() {
                    const $row = $(this);
                    const text = $row.text().toLowerCase();
                    const description = $row.find('td:eq(1)').text();
                    if (text.includes(searchTerm)) {
                        results.push({
                            category: 'Very Serious',
                            description: description,
                            highlightedDescription: highlightMatch(description, searchTerm),
                            element: $row
                        });
                    }
                });

                // Display results
                if (results.length > 0) {
                    // Add search summary
                    $resultsContainer.append(`
                        <div class="dropdown-header">
                            Found ${results.length} match${results.length === 1 ? '' : 'es'}
                        </div>
                    `);

                    results.forEach(result => {
                        const $item = $(`<a class="dropdown-item" href="#">
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="badge badge-${result.category === 'Light' ? 'info' : result.category === 'Serious' ? 'warning' : 'danger'} mb-1">
                                    ${result.category}
                                </small>
                            </div>
                            ${result.highlightedDescription}
                        </a>`);

                        $item.on('click', function(e) {
                            e.preventDefault();
                            const $target = result.element;
                            const $table = $target.closest('.card');
                            
                            // Scroll to the table
                            $('html, body').animate({
                                scrollTop: $table.offset().top - 70
                            }, 500);

                            // Highlight the row
                            $target.addClass('highlight');
                            setTimeout(() => {
                                $target.removeClass('highlight');
                            }, 3000);

                            // Close dropdown
                            $resultsContainer.removeClass('show');
                        });

                        $resultsContainer.append($item);
                    });

                    $resultsContainer.addClass('show');
                } else if (searchTerm) {
                    $resultsContainer.append(`
                        <div class="dropdown-header">No results found</div>
                        <span class="dropdown-item-text text-muted">
                            No offenses found matching "${searchTerm}"
                        </span>
                    `);
                    $resultsContainer.addClass('show');
                }
            });

            // Close search results when clicking outside
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.input-group').length) {
                    $('#searchResults').removeClass('show');
                }
            });

            // Remove individual table search boxes
            $('.card-body .row.mb-3').remove();
        });
    </script>

    <style>
        @media (max-width: 768px) {
            .btn.position-fixed {
                bottom: 20px;
                right: 20px;
                width: 50px;
                height: 50px;
            }
        }
        
        .btn.position-fixed:hover {
            transform: scale(1.1);
            transition: transform 0.2s;
        }

        .btn.position-fixed {
            transition: transform 0.2s;
        }

        .highlight {
            background-color: #fff3cd !important;
            transition: background-color 0.5s ease;
        }
        
        #searchResults {
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            border: 1px solid #ddd;
        }
        
        #searchResults .dropdown-item {
            padding: 10px 15px;
            border-bottom: 1px solid #eee;
            white-space: normal;
        }
        
        #searchResults .dropdown-item:hover {
            background-color: #f8f9fa;
        }
        
        #searchResults .text-muted {
            font-size: 0.8em;
        }
        
        .input-group-text {
            background-color: #007bff;
            color: white;
            border: none;
        }
        
        #globalSearch:focus {
            box-shadow: none;
            border-color: #007bff;
        }

        .highlight-text {
            background-color: #ffeeba;
            padding: 0 2px;
            border-radius: 2px;
        }

        .dropdown-header {
            background-color: #f8f9fa;
            padding: 8px 15px;
            font-weight: bold;
            border-bottom: 1px solid #dee2e6;
        }

        #searchResults .badge {
            font-size: 0.8em;
            padding: 3px 8px;
        }

        #searchResults .dropdown-item {
            white-space: normal;
            padding: 10px 15px;
            border-bottom: 1px solid #eee;
        }

        #searchResults .dropdown-item:hover {
            background-color: #f8f9fa;
        }

        #searchResults {
            margin-top: 5px;
            max-height: 400px;
            overflow-y: auto;
        }

        .input-group {
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            border-radius: 4px;
        }

        #globalSearch {
            border-right: none;
            padding-left: 15px;
        }

        #globalSearch::placeholder {
            color: #adb5bd;
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