<?php
require_once("../shared/common.php");
require_once(REL(__FILE__, "../shared/logincheck.php"));
require_once __DIR__ . '/../autoload.php';

use DDC\DDC_Toplist;

$ddcInstance = new DDC_Toplist();
$data = $ddcInstance->make_full_list();

// Grab requested format, default to json
$format = isset($_GET['format']) ? $_GET['format'] : 'json';
$filename = "bcc_library_ddc_dataset_2026_" . date('Y-m-d');

if ($format === 'csv') {
    // Set headers to force download CSV
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '.csv"');
    
    $output = fopen('php://output', 'w');
    
    // Output headers matching your table layout
    fputcsv($output, ['DDC', 'Level 1 Main Class', 'Level 2 Division Mapping', 'Level 3 Topic Mapping', 'Count']);
    
    foreach ($data as $row) {
        fputcsv($output, [
            $row['ddc'],
            $row['main'],
            $row['division'],
            $row['topic'],
            $row['total']
        ]);
    }
    fclose($output);
    exit;

} else {
    // Default: Set headers to force download JSON
    header('Content-Type: application/json; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '.json"');
    
    // Pretty print so researchers can read it visually if they want to
    echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}