<?php
	/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
	 * See the file COPYRIGHT.html for more details.
	 */
require_once("../shared/common.php");
require_once(REL(__FILE__, "../shared/logincheck.php"));
require_once __DIR__ . '/../autoload.php';

use BookUtilization\Top30borrow;

$month = $_GET['month'] ?? '';
$limit = $_GET['limit'] ?? '30';

// Validate month parameter format (YYYY-MM)
if (!preg_match('/^\d{4}-\d{2}$/', $month)) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid month format. Expected YYYY-MM."]);
    exit;
}

$exporter = new Top30borrow();
$result = $exporter->make_top30borrowlist($month, $limit);

if (!$result['success']) {
    http_response_code(500);
    echo json_encode(["error" => "Failed to retrieve data."]);
    exit;
}

// Prepare payload
$exportData = [
    'month'          => $month,
    'limit_set'      => $limit === 'all' ? 'No Limit (All)' : (int)$limit,
    'generated_at'   => date('Y-m-d H:i:s'),
    'total_borrowed' => count($result['content']),
    'data'           => $result['content']
];

// Set headers for JSON file download
$filename = "borrowed_books_" . $month . ".json";
header('Content-Type: application/json; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

echo json_encode($exportData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
exit;