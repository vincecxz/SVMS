<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SAO - Add Student</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="../dist/css/adminlte.min.css">
    <!-- Sweet Alert -->
    <link rel="stylesheet" href="../plugins/sweetalert2/sweetalert2.min.css">
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
    <?php 
    include '../includes/admin/navbar.php';
    include '../includes/admin/sidebar.php';
    ?>

    <!-- Content Wrapper -->
    <div class="content-wrapper">
        <!-- Content Header -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Add Student</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                            <li class="breadcrumb-item active">Add Student</li>
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
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Student Information</h3>
                            </div>
                            <form id="addStudentForm" method="POST" action="process_add_student.php">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="idNumber">ID Number</label>
                                                <input type="text" class="form-control" id="idNumber" name="idNumber">
                                            </div>
                                            <div class="form-group">
                                                <label for="fullName">Full Name<span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="fullName" name="fullName" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="courseYearSection">Course, Year and Section<span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="courseYearSection" name="courseYearSection" placeholder="e.g., BSIT 1-A" required>
                                                <small class="form-text text-muted">Format: Course Year-Section (e.g., BSIT 1-A)</small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="contactNumber">Contact Number</label>
                                                <input type="tel" class="form-control" id="contactNumber" name="contactNumber" pattern="[0-9]{11}" placeholder="09XXXXXXXXX">
                                            </div>
                                            <div class="form-group">
                                                <label for="email">Email Address<span class="text-danger">*</span></label>
                                                <input type="email" class="form-control" id="email" name="email" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary">Add Student</button>
                                    <button type="reset" class="btn btn-secondary">Clear Form</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <?php include '../includes/admin/footer.php'; ?>
</div>

<!-- REQUIRED SCRIPTS -->
<!-- jQuery -->
<script src="../plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap -->
<script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE -->
<script src="../dist/js/adminlte.js"></script>
<!-- Sweet Alert -->
<script src="../plugins/sweetalert2/sweetalert2.min.js"></script>

<script>
$(document).ready(function() {
    // Form validation and submission
    $('#addStudentForm').on('submit', function(e) {
        e.preventDefault();
        
        // Get form data
        var formData = $(this).serialize();
        
        // Submit form using AJAX
        $.ajax({
            type: 'POST',
            url: 'process_add_student.php',
            data: formData,
            success: function(response) {
                if(response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Student added successfully.',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(function() {
                        // Clear form
                        $('#addStudentForm')[0].reset();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: response.message || 'Failed to add student.'
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Failed to connect to server.'
                });
            }
        });
    });

    // Input validations
    $('#idNumber').on('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
    });

    $('#contactNumber').on('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
        if (this.value.length > 11) {
            this.value = this.value.slice(0, 11);
        }
    });
});
$(document).ready(function() {
    // Initialize AdminLTE Sidebar
    if (typeof $.fn.Treeview !== 'undefined') {
        $('[data-widget="treeview"]').Treeview('init');
    }
});
</script>
</body>
</html> 