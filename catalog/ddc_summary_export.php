<?php
require_once("../shared/common.php");
require_once("../shared/guard_doggy.php");
require_once __DIR__ . '/../autoload.php';

use DDC\DDCSummary;

$summary = new DDCSummary();
$data = $summary->generate_summary();

$filename = "ddc_summary_" . date("Y-m-d") . ".csv";

header("Content-Type: text/csv");
header("Content-Disposition: attachment; filename={$filename}");

$output = fopen("php://output", "w");

// Add headers to the CSV file
fputcsv($output, ['SUBJECT AREA', 'BOOK HOLDINGS', '', 'Copyrighted within the last 10 years', '', 'Copyrighted within the last 5 years', '']);
fputcsv($output, ['', 'Titles', 'Volumes', 'Titles', 'Volumes', 'Titles', 'Volumes']);
fputcsv($output, ['I. BOOKS', '', '', '', '', '', '']);

// Add data to the CSV file
foreach ($data as $row) {
    fputcsv($output, [
        $row['subject_area'],
        $row['titles'],
        $row['volumes'],
        $row['titles_last_10_years'],
        $row['volumes_last_10_years'],
        $row['titles_last_5_years'],
        $row['volumes_last_5_years'],
    ]);
}

fclose($output);
exit;