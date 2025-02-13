<?php
require_once '../config/database.php';

// Check if vendor directory exists
if (!file_exists(__DIR__ . '/../vendor/autoload.php')) {
    die('Please run "composer install" in the project root directory to install required dependencies.');
}

require __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

try {
    // Get valid programs
    $query = "SELECT code FROM programs ORDER BY code";
    $result = mysqli_query($conn, $query);
    $programs = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $programs[] = $row['code'];
    }

    // Get sections
    $query = "SELECT DISTINCT section FROM sections ORDER BY section";
    $result = mysqli_query($conn, $query);
    $sections = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $sections[] = $row['section'];
    }

    // Set headers for CSV download
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment;filename="student_list_template.csv"');
    header('Cache-Control: no-cache');
    header('Pragma: no-cache');

    // Create output stream
    $output = fopen('php://output', 'w');
    
    // Add UTF-8 BOM for Excel compatibility
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

    // Function to create empty columns
    function createEmptyColumns($count) {
        return array_fill(0, $count, '');
    }

    // Prepare the rows with data and notes side by side
    $rows = [];

    // Headers row with spacing
    $headers = ['ID Number', 'Full Name', 'Program', 'Section', 'Contact Number', 'Email'];
    $headerRow = array_merge(
        $headers,
        createEmptyColumns(7), // Add 7 empty columns for spacing
        ['NOTES:']
    );
    $rows[] = $headerRow;

    // First sample data row with first note
    $dataRow1 = array_merge(
        ['132100', 'Juan Dela Cruz', 'BSIT', '1A', '09123456789', 'juan.delacruz@example.com'],
        createEmptyColumns(7),
        ['1. All fields are required except Contact Number']
    );
    $rows[] = $dataRow1;

    // Second sample data row with second note
    $dataRow2 = array_merge(
        ['132101', 'Maria Garcia', 'BSCS', '2A', '09987654321', 'maria.garcia@example.com'],
        createEmptyColumns(7),
        ['2. Valid Programs: ' . implode(', ', $programs)]
    );
    $rows[] = $dataRow2;

    // Empty data rows with remaining notes
    $emptyDataColumns = createEmptyColumns(6); // Empty columns for data section
    
    // Add remaining notes
    $rows[] = array_merge(
        $emptyDataColumns,
        createEmptyColumns(7),
        ['3. Valid Sections: ' . implode(', ', $sections)]
    );

    $rows[] = array_merge(
        $emptyDataColumns,
        createEmptyColumns(7),
        ['4. Contact number format: 09XXXXXXXXX']
    );

    $rows[] = array_merge(
        $emptyDataColumns,
        createEmptyColumns(7),
        ['5. Email must be valid format']
    );

    // Write all rows to the CSV file
    foreach ($rows as $row) {
        fputcsv($output, $row);
    }

    // Close the output stream
    fclose($output);

} catch (Exception $e) {
    error_log("Error in download_template.php: " . $e->getMessage());
    die('Error generating template file. Please try again or contact support.');
} 