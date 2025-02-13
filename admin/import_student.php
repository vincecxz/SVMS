<?php
session_start();
include '../config/database.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

// Include PHPSpreadsheet if available
if (file_exists('../vendor/autoload.php')) {
    require '../vendor/autoload.php';
}

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

$success_message = $error_message = "";
$setup_required = false;
$setup_instructions = [];

// Check for required PHP extensions
if (!extension_loaded('gd')) {
    $setup_required = true;
    $setup_instructions[] = "PHP GD extension is not enabled. Please enable it in your php.ini file by uncommenting the line ';extension=gd'";
}

if (!extension_loaded('zip')) {
    $setup_required = true;
    $setup_instructions[] = "PHP ZIP extension is not enabled. Please enable it in your php.ini file by uncommenting the line ';extension=zip'";
}

// Check if composer and PHPSpreadsheet are installed
if (!file_exists('../vendor/autoload.php')) {
    $setup_required = true;
    $setup_instructions[] = "PHPSpreadsheet is not installed. Please follow the installation steps below.";
}

if (!$setup_required && $_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["excel_file"])) {
    $allowed_ext = ['xls', 'xlsx'];
    $file = $_FILES["excel_file"];
    $file_ext = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));

    if (in_array($file_ext, $allowed_ext)) {
        try {
            $reader = new Xlsx();
            $spreadsheet = $reader->load($file["tmp_name"]);
            $worksheet = $spreadsheet->getActiveSheet();
            $worksheet_arr = $worksheet->toArray();

            // Remove header row
            unset($worksheet_arr[0]);

            $success_count = 0;
            $error_count = 0;
            $error_details = [];

            foreach ($worksheet_arr as $row_index => $row) {
                if (!empty($row[0])) { // Check if ID number exists
                    // Validate required fields
                    if (empty($row[1]) || empty($row[2]) || empty($row[3]) || empty($row[4])) {
                        $error_count++;
                        $error_details[] = "Row " . ($row_index + 1) . ": Missing required fields";
                        continue;
                    }

                    $id_number = mysqli_real_escape_string($conn, trim($row[0]));
                    $full_name = mysqli_real_escape_string($conn, trim($row[1]));
                    $course_year_section = mysqli_real_escape_string($conn, trim($row[2]));
                    $contact_number = mysqli_real_escape_string($conn, trim($row[3]));
                    $email = mysqli_real_escape_string($conn, trim($row[4]));

                    // Validate email format
                    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $error_count++;
                        $error_details[] = "Row " . ($row_index + 1) . ": Invalid email format";
                        continue;
                    }

                    // Check if student already exists
                    $check_query = "SELECT id_number FROM students WHERE id_number = '$id_number'";
                    $check_result = mysqli_query($conn, $check_query);

                    if (mysqli_num_rows($check_result) > 0) {
                        // Update existing student
                        $query = "UPDATE students SET 
                                full_name = '$full_name',
                                course_year_section = '$course_year_section',
                                contact_number = '$contact_number',
                                email = '$email'
                                WHERE id_number = '$id_number'";
                    } else {
                        // Insert new student
                        $query = "INSERT INTO students (id_number, full_name, course_year_section, contact_number, email) 
                                VALUES ('$id_number', '$full_name', '$course_year_section', '$contact_number', '$email')";
                    }

                    if (mysqli_query($conn, $query)) {
                        $success_count++;
                    } else {
                        $error_count++;
                        $error_details[] = "Row " . ($row_index + 1) . ": " . mysqli_error($conn);
                    }
                }
            }

            $success_message = "Import completed. Successfully imported/updated $success_count records.";
            if ($error_count > 0) {
                $error_message = "$error_count records failed to import.<br>";
                $error_message .= "<ul>";
                foreach ($error_details as $error) {
                    $error_message .= "<li>$error</li>";
                }
                $error_message .= "</ul>";
            }

        } catch (Exception $e) {
            $error_message = "Error reading file: " . $e->getMessage();
        }
    } else {
        $error_message = "Please upload only Excel files (.xls or .xlsx)";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import Students - SAO</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include '../includes/admin/navbar.php'; ?>
    
    <div class="container-fluid">
        <div class="row">
            <?php include '../includes/admin/sidebar.php'; ?>
            
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                <h2>Import Students</h2>
                
                <?php if ($setup_required): ?>
                    <div class="alert alert-warning" role="alert">
                        <h4 class="alert-heading">Setup Required!</h4>
                        <p>The following items need to be set up before you can use the import feature:</p>
                        <ul>
                            <?php foreach ($setup_instructions as $instruction): ?>
                                <li><?php echo $instruction; ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <hr>
                        <p class="mb-0">Complete Setup Instructions:</p>
                        <ol>
                            <li>Open XAMPP Control Panel</li>
                            <li>Click on "Config" button for Apache</li>
                            <li>Select "PHP (php.ini)"</li>
                            <li>Find and uncomment these lines (remove the semicolon at the start):
                                <ul>
                                    <li><code>;extension=gd</code></li>
                                    <li><code>;extension=zip</code></li>
                                </ul>
                            </li>
                            <li>Save the file and restart Apache in XAMPP Control Panel</li>
                            <li>Download and install Composer from <a href="https://getcomposer.org/download/" target="_blank">https://getcomposer.org/download/</a></li>
                            <li>Open Command Prompt as Administrator</li>
                            <li>Navigate to your project directory: <code>cd <?php echo realpath('../'); ?></code></li>
                            <li>Run: <code>composer require phpoffice/phpspreadsheet</code></li>
                            <li>Refresh this page after completing all steps</li>
                        </ol>
                    </div>
                <?php else: ?>
                    <?php if ($success_message): ?>
                        <div class="alert alert-success" role="alert">
                            <?php echo $success_message; ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($error_message): ?>
                        <div class="alert alert-danger" role="alert">
                            <?php echo $error_message; ?>
                        </div>
                    <?php endif; ?>

                    <div class="card">
                        <div class="card-body">
                            <form method="POST" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label for="excel_file" class="form-label">Upload Excel File</label>
                                    <input type="file" class="form-control" id="excel_file" name="excel_file" accept=".xls,.xlsx" required>
                                    <div class="form-text">
                                        Please upload an Excel file with the following columns:<br>
                                        ID Number, Full Name, Course-Year-Section, Contact Number, Email
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary" <?php echo file_exists('../vendor/autoload.php') ? '' : 'disabled'; ?>>Import</button>
                            </form>
                        </div>
                    </div>

                    <div class="mt-4">
                        <h4>Instructions:</h4>
                        <ol>
                            <li>Prepare your Excel file with these columns in order:
                                <ul>
                                    <li>ID Number (required)</li>
                                    <li>Full Name (required)</li>
                                    <li>Course-Year-Section (required)</li>
                                    <li>Contact Number (required)</li>
                                    <li>Email (required, must be valid email format)</li>
                                </ul>
                            </li>
                            <li>Make sure all required fields are filled in</li>
                            <li>Save your Excel file in .xlsx or .xls format</li>
                            <li>Click "Choose File" and select your Excel file</li>
                            <li>Click "Import" to start the import process</li>
                        </ol>
                    </div>
                <?php endif; ?>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
