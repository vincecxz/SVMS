<?php
require_once '../config/database.php';

// Clean output buffer to prevent corruption
if (ob_get_length()) ob_end_clean();

// Get sorting options
$sort_by = isset($_POST['sort_by']) ? $_POST['sort_by'] : 'date';
$sort_order = isset($_POST['sort_order']) ? $_POST['sort_order'] : 'desc';

// Build ORDER BY clause based on sorting options
$order_by = "";
if ($sort_by === 'name') {
    $order_by = "ORDER BY s.full_name " . ($sort_order === 'asc' ? 'ASC' : 'DESC');
} else {
    $order_by = "ORDER BY vr.incident_datetime " . ($sort_order === 'asc' ? 'ASC' : 'DESC');
}

// Use PhpSpreadsheet if available, otherwise fallback to CSV
$usePhpSpreadsheet = false;
if (file_exists('../vendor/autoload.php')) {
    require_once '../vendor/autoload.php';
    if (class_exists('PhpOffice\\PhpSpreadsheet\\Spreadsheet')) {
        $usePhpSpreadsheet = true;
    }
}

$query = "SELECT vr.*, s.full_name as student_name,
        CASE 
            WHEN vr.section_type = 'section1' THEN 'Academic'
            WHEN vr.section_type = 'section2' THEN 'Non-Academic'
        END as section_display,
        CASE 
            WHEN vr.section_type = 'section1' THEN sec1.description
            WHEN vr.section_type = 'section2' THEN sec2.description
        END as offense_description
        FROM violation_reports vr
        LEFT JOIN students s ON vr.student_id = s.id
        LEFT JOIN sec1 ON (vr.section_type = 'section1' AND vr.offense_id = sec1.id)
        LEFT JOIN sec2 ON (vr.section_type = 'section2' AND vr.offense_id = sec2.id)
        $order_by";

$result = $conn->query($query);

$columns = [
    'Date & Time',
    'Student Name',
    'Section',
    'Level',
    'Offense',
    'Violation Count',
    'Sanction',
    'Status'
];

$rows = [];
while ($row = $result->fetch_assoc()) {
    $date_time = date('M d, Y h:i A', strtotime($row['incident_datetime']));
    $rows[] = [
        $date_time,
        $row['student_name'],
        $row['section_display'],
        $row['offense_level'] ? $row['offense_level'] : 'N/A',
        $row['offense_description'],
        $row['violation_count'],
        $row['sanction'],
        $row['status']
    ];
}

if ($usePhpSpreadsheet) {
    try {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray($columns, NULL, 'A1');
        $sheet->fromArray($rows, NULL, 'A2');
        // Make header row bold
        $headerCellRange = 'A1:' . $sheet->getHighestColumn() . '1';
        $sheet->getStyle($headerCellRange)->getFont()->setBold(true);
        // Auto-size columns
        $colCount = count($columns);
        for ($col = 1; $col <= $colCount; $col++) {
            $sheet->getColumnDimensionByColumn($col)->setAutoSize(true);
        }
        $filename = 'violation_reports_' . date('Ymd_His') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    } catch (Exception $e) {
        // Fallback to CSV if Excel export fails
        if (ob_get_length()) ob_end_clean();
        $filename = 'violation_reports_' . date('Ymd_His') . '.csv';
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        $output = fopen('php://output', 'w');
        fputcsv($output, $columns);
        foreach ($rows as $row) {
            fputcsv($output, $row);
        }
        fclose($output);
        exit;
    }
} else {
    // Fallback to CSV export
    $filename = 'violation_reports_' . date('Ymd_His') . '.csv';
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    $output = fopen('php://output', 'w');
    fputcsv($output, $columns);
    foreach ($rows as $row) {
        fputcsv($output, $row);
    }
    fclose($output);
    exit;
} 