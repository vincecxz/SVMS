<?php
session_start();
require_once '../config/database.php';

// Check if user is logged in and is a student
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'student') {
    header('Location: ../index.php');
    exit;
}

// Get student violations
$student_id = $_SESSION['user_id'];
$query = "SELECT 
            v.incident_datetime,
            v.section_type,
            v.offense_level,
            v.sanction,
            v.status,
            CASE 
                WHEN v.section_type = 'section1' THEN s1.description
                ELSE s2.description
            END as offense_description
          FROM violation_reports v 
          LEFT JOIN sec1 s1 ON v.offense_id = s1.id AND v.section_type = 'section1'
          LEFT JOIN sec2 s2 ON v.offense_id = s2.id AND v.section_type = 'section2'
          WHERE v.student_id = ? 
          ORDER BY v.incident_datetime DESC";

try {
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $violations = $result->fetch_all(MYSQLI_ASSOC);
} catch (Exception $e) {
    error_log("Error fetching violations: " . $e->getMessage());
    $error = "An error occurred while fetching violations.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Student Violations</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="../plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="../plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="../dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
    
    <?php include '../includes/student/navbar.php'; ?>
    <?php include '../includes/student/sidebar.php'; ?>
   

    <!-- Content Wrapper -->
    <div class="content-wrapper">
        <!-- Content Header -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>My Violations</h1>
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
                            <div class="card-body">
                                <?php if (isset($error)): ?>
                                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                                <?php endif; ?>

                                <table id="violationsTable" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Section</th>
                                            <th>Offense</th>
                                            <th>Level</th>
                                            <th>Sanction</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($violations)): ?>
                                            <?php foreach ($violations as $violation): ?>
                                                <tr>
                                                    <td><?php echo date('M d, Y', strtotime($violation['incident_datetime'])); ?></td>
                                                    <td><?php echo $violation['section_type'] === 'section1' ? 'Academic' : 'Non-Academic'; ?></td>
                                                    <td><?php echo htmlspecialchars($violation['offense_description']); ?></td>
                                                    <td>
                                                        <span class="badge badge-<?php 
                                                            if ($violation['section_type'] === 'section2') {
                                                                switch($violation['offense_level']) {
                                                                    case 'Light':
                                                                        echo 'warning';
                                                                        break;
                                                                    case 'Serious':
                                                                        echo 'danger';
                                                                        break;
                                                                    case 'Very Serious':
                                                                        echo 'dark';
                                                                        break;
                                                                    default:
                                                                        echo 'secondary';
                                                                }
                                                            } else {
                                                                echo 'info';
                                                            }
                                                        ?>">
                                                            <?php echo htmlspecialchars($violation['offense_level']); ?>
                                                        </span>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($violation['sanction']); ?></td>
                                                    <td>
                                                        <span class="badge badge-<?php 
                                                            echo $violation['status'] === 'Active' ? 'danger' : 'success'; 
                                                        ?>">
                                                            <?php echo htmlspecialchars($violation['status']); ?>
                                                        </span>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Footer -->
    <?php include '../includes/student/footer.php'; ?>
    
</div>

<!-- jQuery -->
<script src="../plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- DataTables & Plugins -->
<script src="../plugins/datatables/jquery.dataTables.min.js"></script>
<script src="../plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="../plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="../plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<!-- AdminLTE App -->
<script src="../dist/js/adminlte.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Page specific script -->
<script>
$(function () {
    $('#violationsTable').DataTable({
        "paging": true,
        "lengthChange": false,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "responsive": true,
        "order": [[0, 'desc']] // Sort by date descending
    });
});
</script>
</body>
</html> 