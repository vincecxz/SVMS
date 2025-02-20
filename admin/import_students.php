<?php
require_once '../config/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit;
}

// Set UTF-8 connection charset
mysqli_set_charset($conn, "utf8mb4");

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

        // Set internal encoding to UTF-8
        mb_internal_encoding('UTF-8');
        
        // Skip UTF-8 BOM if present
        fgets($handle, 4) === "\xEF\xBB\xBF" ? rewind($handle) : rewind($handle);
        
        // Read and store all rows
        while (($data = fgetcsv($handle)) !== false) {
            // Convert encoding to UTF-8 if needed
            $data = array_map(function($cell) {
                // Detect encoding and convert to UTF-8 if necessary
                $encoding = mb_detect_encoding($cell, ['UTF-8', 'ISO-8859-1', 'Windows-1252'], true);
                return $encoding === 'UTF-8' ? $cell : mb_convert_encoding($cell, 'UTF-8', $encoding);
            }, $data);
            
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
        
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader(
            $ext === 'xlsx' ? 'Xlsx' : 'Xls'
        );
        $reader->setInputEncoding('UTF-8');
        
        // Read cell values as they are entered (raw)
        if ($ext === 'xlsx') {
            $reader->setReadDataOnly(true);
            $reader->setPreCalculateFormulas(false);
        }
        
        $spreadsheet = $reader->load($file['tmp_name']);
        $worksheet = $spreadsheet->getActiveSheet();
        
        // Get the highest row and column numbers referenced in the worksheet
        $highestRow = $worksheet->getHighestRow();
        $highestColumn = $worksheet->getHighestColumn();
        
        // Convert the rows to array while preserving raw values
        $rows = [];
        for ($row = 1; $row <= $highestRow; $row++) {
            $rowData = [];
            for ($col = 'A'; $col <= $highestColumn; $col++) {
                $cell = $worksheet->getCell($col . $row);
                
                // Get the raw value for sections (column D)
                if ($col === 'D') {
                    $value = $cell->getValue();
                    // If it's a date value, get the original entered text
                    if (PHPExcel_Shared_Date::isDateTime($cell)) {
                        $value = $cell->getOldCalculatedValue();
                    }
                } else {
                    $value = $cell->getValue();
                }
                
                if (is_string($value)) {
                    $encoding = mb_detect_encoding($value, ['UTF-8', 'ISO-8859-1', 'Windows-1252'], true);
                    $value = $encoding === 'UTF-8' ? $value : mb_convert_encoding($value, 'UTF-8', $encoding);
                }
                
                $rowData[] = $value;
            }
            $rows[] = $rowData;
        }
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
    $skipped = 0;
    $errors = [];
    $validation_results = [];
    $new_records = [];
    $existing_records = [];

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

    // Get valid sections and prepare them for comparison
    $query = "SELECT section FROM sections";
    $result = mysqli_query($conn, $query);
    if (!$result) {
        throw new Exception('Failed to fetch sections: ' . mysqli_error($conn));
    }
    $valid_sections = [];
    $valid_section_patterns = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $valid_sections[] = $row['section'];
        // Create pattern by replacing any hyphen with an optional hyphen
        $pattern = str_replace('-', '-?', $row['section']);
        $valid_section_patterns[] = $pattern;
    }

    // First pass: Validate all records and check for duplicates
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
        // Trim both spaces and quotes from section
        $section = trim(trim($row[3]), '"\'');
        $contact_number = trim($row[4]);
        $email = trim($row[5]);

        // Store record for validation
        $record = [
            'row_num' => $row_num,
            'id_number' => $id_number,
            'full_name' => htmlspecialchars($full_name, ENT_QUOTES, 'UTF-8'),
            'program_code' => $program_code,
            'section' => $section, // Store the cleaned section without quotes
            'contact_number' => $contact_number,
            'email' => $email
        ];

        // Basic validation
        $validation_error = false;
        
        // Validate required fields (contact_number removed from required fields)
        if (empty($id_number) || empty($full_name) || empty($program_code) || empty($section) || empty($email)) {
            $errors[] = "Row $row_num: Required fields (ID Number, Full Name, Program, Section, Email) cannot be empty";
            $validation_error = true;
        }

        // Validate program
        if (!isset($programs[$program_code])) {
            $errors[] = "Row $row_num: Invalid program code '$program_code'. Valid programs are: " . implode(', ', array_keys($programs));
            $validation_error = true;
        }

        // Validate section with more flexible matching
        $section_valid = false;
        // Clean and normalize the input section
        $clean_section = trim(str_replace(['"', "'"], '', $section));
        $normalized_section = preg_replace('/\s*-\s*/', '', $clean_section); // Remove hyphens and surrounding spaces

        foreach ($valid_sections as $valid_section) {
            // Clean and normalize the valid section
            $clean_valid = trim(str_replace(['"', "'"], '', $valid_section));
            $normalized_valid = preg_replace('/\s*-\s*/', '', $clean_valid);

            // Try different matching patterns
            if (
                strcasecmp($normalized_section, $normalized_valid) === 0 || // Match without hyphens
                strcasecmp($clean_section, $clean_valid) === 0 || // Match with hyphens
                strcasecmp($section, $valid_section) === 0 // Direct match
            ) {
                $section_valid = true;
                // Use the cleaned section format (without quotes)
                $record['section'] = $clean_valid;
                break;
            }
        }
        
        if (!$section_valid) {
            $errors[] = "Row $row_num: Invalid section '$section'. Valid sections are: " . implode(', ', $valid_sections);
            $validation_error = true;
        }

        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Row $row_num: Invalid email format for '$email'";
            $validation_error = true;
        }

        // Validate contact number only if provided (now optional)
        if (!empty($contact_number)) {
            if (!preg_match('/^09\d{9}$/', $contact_number)) {
                $errors[] = "Row $row_num: Invalid contact number format for '$contact_number'. Must be in format 09XXXXXXXXX";
                $validation_error = true;
            }
        } else {
            // Set contact_number to NULL if empty
            $record['contact_number'] = null;
        }

        if ($validation_error) {
            continue;
        }

        // Check for duplicate ID number or email in database
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
                $existing_records[] = "Row $row_num: Student with ID number $id_number";
            } else {
                $existing_records[] = "Row $row_num: Student with email $email";
            }
            $validation_results[$row_num] = 'existing';
        } else {
            $new_records[] = $record;
            $validation_results[$row_num] = 'new';
        }
    }

    // Prepare validation summary
    $total_records = count($new_records) + count($existing_records);
    $validation_summary = [
        'total_records' => $total_records,
        'new_records' => count($new_records),
        'existing_records' => count($existing_records),
        'error_records' => count($errors)
    ];

    // Second pass: Insert new records
    foreach ($new_records as $record) {
        $program_id = $programs[$record['program_code']];
        $insert_query = "INSERT INTO students (id_number, full_name, program_id, section, contact_number, email) 
                        VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $insert_query);
        if (!$stmt) {
            throw new Exception('Failed to prepare insert query: ' . mysqli_error($conn));
        }

        mysqli_stmt_bind_param($stmt, "ssisss", 
            $record['id_number'], 
            $record['full_name'], 
            $program_id, 
            $record['section'], 
            $record['contact_number'], 
            $record['email']
        );
        
        if (mysqli_stmt_execute($stmt)) {
            $imported++;
            error_log("Successfully imported student: " . $record['id_number']);
        } else {
            $errors[] = "Row " . $record['row_num'] . ": Failed to import student. Error: " . mysqli_stmt_error($stmt);
        }
    }

    // Prepare detailed response
    $response = [
        'success' => true,
        'summary' => [
            'total_processed' => $total_records,
            'imported' => $imported,
            'skipped' => count($existing_records),
            'errors' => count($errors)
        ],
        'message' => sprintf(
            "Processed %d records: %d imported successfully, %d existing records skipped",
            $total_records,
            $imported,
            count($existing_records)
        )
    ];

    if (!empty($existing_records)) {
        $response['existing_records'] = [
            'message' => 'The following records already exist in the database:',
            'details' => $existing_records
        ];
    }

    if (!empty($errors)) {
        $response['errors'] = [
            'message' => 'The following records had errors:',
            'details' => $errors
        ];
    }

    echo json_encode($response);

} catch (Exception $e) {
    error_log("Error in import_students.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'Failed to process the import file: ' . $e->getMessage()
    ]);
} 