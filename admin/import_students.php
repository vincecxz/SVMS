<?php
require_once '../config/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit;
}

if (!isset($_FILES['import_file']) || $_FILES['import_file']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'error' => 'Please select a valid file to import']);
    exit;
}

try {
    $file = $_FILES['import_file'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    // Read the file content
    $rows = [];
    if ($ext === 'csv') {
        // Read CSV file with UTF-8 encoding
        $handle = fopen($file['tmp_name'], 'r');
        if ($handle === false) {
            throw new Exception('Failed to open CSV file');
        }

        // Skip UTF-8 BOM if present
        fgets($handle, 4) === "\xEF\xBB\xBF" ? rewind($handle) : rewind($handle);
        
        // Read and store all rows
        while (($data = fgetcsv($handle)) !== false) {
            // Skip completely empty rows
            if (empty(array_filter($data, function($cell) {
                return trim($cell) !== '';
            }))) {
                continue;
            }
            $rows[] = array_map('trim', $data);
        }
        fclose($handle);

        // Check if we have any data
        if (empty($rows)) {
            throw new Exception('No data found in the CSV file');
        }
    } else {
        // For Excel files, require PhpSpreadsheet
        if (!file_exists(__DIR__ . '/../vendor/autoload.php')) {
            throw new Exception('Please run "composer install" to process Excel files.');
        }
        require __DIR__ . '/../vendor/autoload.php';
        
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file['tmp_name']);
        $rows = $spreadsheet->getActiveSheet()->toArray();
    }

    // Store original headers for validation
    $headers = array_shift($rows);
    if (empty($headers)) {
        throw new Exception('No headers found in the file');
    }

    // Clean headers (remove spaces, asterisks, and convert to lowercase)
    $clean_headers = array_map(function($header) {
        return strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $header));
    }, $headers);

    // Expected headers
    $expected_headers = [
        'idnumber',
        'fullname',
        'program',
        'section',
        'contactnumber',
        'email'
    ];

    // Validate headers
    if (count($clean_headers) < count($expected_headers)) {
        throw new Exception('Invalid file format. Missing required columns. Expected: ID Number, Full Name, Program, Section, Contact Number, Email');
    }

    // Check each required header
    foreach ($expected_headers as $index => $expected) {
        if ($clean_headers[$index] !== $expected) {
            error_log("Header mismatch at column " . ($index + 1) . ": Expected '$expected', Got '{$clean_headers[$index]}'");
            throw new Exception("Invalid file format. Column " . ($index + 1) . " should be '" . $headers[$index] . "' but got something different. Please use the template file.");
        }
    }

    // Initialize counters
    $imported = 0;
    $errors = [];

    // Get valid programs
    $query = "SELECT id, code FROM programs";
    $result = mysqli_query($conn, $query);
    if (!$result) {
        throw new Exception('Failed to fetch programs: ' . mysqli_error($conn));
    }
    $programs = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $programs[$row['code']] = $row['id'];
    }

    // Get valid sections
    $query = "SELECT section FROM sections";
    $result = mysqli_query($conn, $query);
    if (!$result) {
        throw new Exception('Failed to fetch sections: ' . mysqli_error($conn));
    }
    $valid_sections = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $valid_sections[] = $row['section'];
    }

    foreach ($rows as $index => $row) {
        // Skip empty rows
        if (empty(array_filter($row))) {
            continue;
        }

        // Skip rows that start with "Notes:"
        if (isset($row[0]) && (strtolower(substr(trim($row[0]), 0, 6)) === 'notes:')) {
            continue;
        }

        $row_num = $index + 2;

        // Ensure all required columns are present
        if (count($row) < 6) {
            $errors[] = "Row $row_num: Missing columns. Each row must have 6 columns.";
            continue;
        }

        $id_number = trim($row[0]);
        $full_name = trim($row[1]);
        $program_code = strtoupper(trim($row[2]));
        $section = trim($row[3]);
        $contact_number = trim($row[4]);
        $email = trim($row[5]);

        // Debug log
        error_log("Processing row $row_num: ID=$id_number, Name=$full_name, Program=$program_code, Section=$section");

        // Validate required fields
        if (empty($id_number) || empty($full_name) || empty($program_code) || empty($section) || empty($email)) {
            $errors[] = "Row $row_num: Required fields cannot be empty";
            continue;
        }

        // Validate program
        if (!isset($programs[$program_code])) {
            $errors[] = "Row $row_num: Invalid program code '$program_code'. Valid programs are: " . implode(', ', array_keys($programs));
            continue;
        }

        // Validate section
        if (!in_array($section, $valid_sections)) {
            $errors[] = "Row $row_num: Invalid section '$section'. Valid sections are: " . implode(', ', $valid_sections);
            continue;
        }

        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Row $row_num: Invalid email format for '$email'";
            continue;
        }

        // Validate contact number if provided
        if (!empty($contact_number) && !preg_match('/^09\d{9}$/', $contact_number)) {
            $errors[] = "Row $row_num: Invalid contact number format for '$contact_number'. Must be in format 09XXXXXXXXX";
            continue;
        }

        // Check for duplicate ID number or email
        $check_query = "SELECT id_number, email FROM students WHERE id_number = ? OR email = ?";
        $stmt = mysqli_prepare($conn, $check_query);
        if (!$stmt) {
            throw new Exception('Failed to prepare duplicate check query: ' . mysqli_error($conn));
        }

        mysqli_stmt_bind_param($stmt, "ss", $id_number, $email);
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception('Failed to execute duplicate check query: ' . mysqli_stmt_error($stmt));
        }

        $check_result = mysqli_stmt_get_result($stmt);
        if (mysqli_num_rows($check_result) > 0) {
            $existing = mysqli_fetch_assoc($check_result);
            if ($existing['id_number'] === $id_number) {
                $errors[] = "Row $row_num: Student with ID number $id_number already exists";
            } else {
                $errors[] = "Row $row_num: Student with email $email already exists";
            }
            continue;
        }

        // Insert student
        $program_id = $programs[$program_code];
        $insert_query = "INSERT INTO students (id_number, full_name, program_id, section, contact_number, email) 
                        VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $insert_query);
        if (!$stmt) {
            throw new Exception('Failed to prepare insert query: ' . mysqli_error($conn));
        }

        mysqli_stmt_bind_param($stmt, "ssisss", $id_number, $full_name, $program_id, $section, $contact_number, $email);
        
        if (mysqli_stmt_execute($stmt)) {
            $imported++;
            error_log("Successfully imported student: $id_number");
        } else {
            $errors[] = "Row $row_num: Failed to import student. Error: " . mysqli_stmt_error($stmt);
        }
    }

    // Prepare response
    $response = [
        'success' => true,
        'message' => "$imported students imported successfully"
    ];

    if (!empty($errors)) {
        $response['warning'] = "Some records could not be imported:";
        $response['errors'] = $errors;
    }

    echo json_encode($response);

} catch (Exception $e) {
    error_log("Error in import_students.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'Failed to process the import file: ' . $e->getMessage()
    ]);
} 