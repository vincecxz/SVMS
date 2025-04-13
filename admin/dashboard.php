<?php
require_once '../includes/auth_check.php';
check_auth('admin'); // Require admin role

// Database connection
require_once '../config/database.php';

// Get total students count
$query = "SELECT COUNT(*) as total_students FROM students";
$result = mysqli_query($conn, $query);
$total_students = mysqli_fetch_assoc($result)['total_students'];

// Get total violations count
$query = "SELECT COUNT(*) as total_violations FROM violation_reports";
$result = mysqli_query($conn, $query);
$total_violations = mysqli_fetch_assoc($result)['total_violations'];

// Get active violations count
$query = "SELECT COUNT(*) as active_violations FROM violation_reports WHERE status = 'Active'";
$result = mysqli_query($conn, $query);
$active_violations = mysqli_fetch_assoc($result)['active_violations'];

// Get resolved violations count
$query = "SELECT COUNT(*) as resolved_violations FROM violation_reports WHERE status = 'Resolved'";
$result = mysqli_query($conn, $query);
$resolved_violations = mysqli_fetch_assoc($result)['resolved_violations'];

// Get program statistics
$query = "SELECT p.code as program_name, COUNT(s.id) as student_count 
          FROM programs p 
          LEFT JOIN students s ON p.id = s.program_id 
          GROUP BY p.id";
$result = mysqli_query($conn, $query);
$program_stats = [];
while ($row = mysqli_fetch_assoc($result)) {
    $program_stats[] = $row;
}

// Get recent violations
$query = "SELECT vr.*, s.full_name, s.id_number 
          FROM violation_reports vr 
          JOIN students s ON vr.student_id = s.id 
          ORDER BY vr.created_at DESC 
          LIMIT 5";
$result = mysqli_query($conn, $query);
$recent_violations = [];
while ($row = mysqli_fetch_assoc($result)) {
    $recent_violations[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SAO - Admin Dashboard</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="../dist/css/adminlte.min.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Dashboard</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Dashboard</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <!-- Info boxes -->
                <div class="row">
                    <div class="col-lg-3 col-6">
                        <!-- Total Students box -->
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3><?php echo $total_students; ?></h3>
                                <p>Total Students</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <a href="student_masterlist.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <!-- Total Violations box -->
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3><?php echo $total_violations; ?></h3>
                                <p>Total Violations</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <a href="report_list.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <!-- Active Violations box -->
                        <div class="small-box bg-danger">
                            <div class="inner">
                                <h3><?php echo $active_violations; ?></h3>
                                <p>Active Violations</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-exclamation-circle"></i>
                            </div>
                            <a href="report_list.php?status=active" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <!-- Resolved Violations box -->
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3><?php echo $resolved_violations; ?></h3>
                                <p>Resolved Violations</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <a href="report_list.php?status=resolved" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Program Statistics -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Students per Program</h3>
                            </div>
                            <div class="card-body">
                                <canvas id="programChart" style="min-height: 250px;"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Violations -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Recent Violations</h3>
                            </div>
                            <div class="card-body p-0">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Student</th>
                                            <th>ID Number</th>
                                            <th>Date</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recent_violations as $violation): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($violation['full_name']); ?></td>
                                            <td><?php echo htmlspecialchars($violation['id_number']); ?></td>
                                            <td><?php echo date('M d, Y', strtotime($violation['incident_datetime'])); ?></td>
                                            <td>
                                                <span class="badge badge-<?php echo $violation['status'] == 'Active' ? 'danger' : ($violation['status'] == 'Resolved' ? 'success' : 'warning'); ?>">
                                                    <?php echo $violation['status']; ?>
                                                </span>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
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

<!-- REQUIRED SCRIPTS -->
<!-- jQuery -->
<script src="../plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap -->
<script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE -->
<script src="../dist/js/adminlte.js"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Initialize program statistics chart
const programCtx = document.getElementById('programChart').getContext('2d');
new Chart(programCtx, {
    type: 'bar',
    data: {
        labels: [<?php echo implode(',', array_map(function($item) { return '"' . $item['program_name'] . '"'; }, $program_stats)); ?>],
        datasets: [{
            label: 'Number of Students',
            data: [<?php echo implode(',', array_map(function($item) { return $item['student_count']; }, $program_stats)); ?>],
            backgroundColor: [
                'rgba(54, 162, 235, 0.2)',
                'rgba(255, 99, 132, 0.2)',
                'rgba(255, 206, 86, 0.2)'
            ],
            borderColor: [
                'rgba(54, 162, 235, 1)',
                'rgba(255, 99, 132, 1)',
                'rgba(255, 206, 86, 1)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});

// Initialize dark mode functionality
function applyDashboardDarkMode() {
    document.querySelector('.content-wrapper').classList.toggle('dark-mode');
    document.querySelectorAll('.small-box').forEach(box => box.classList.toggle('dark-mode'));
    document.querySelectorAll('.small-box-footer').forEach(footer => footer.classList.toggle('dark-mode'));
}

function loadDashboardTheme() {
    const theme = localStorage.getItem('theme');
    if (theme === 'dark') {
        applyDashboardDarkMode();
    }
}

document.addEventListener('DOMContentLoaded', loadDashboardTheme);
</script>

</body>
</html> 