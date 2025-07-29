<?php
require_once("../shared/guard_doggy.php");

require_once("circ_function2.php"); 
require_once("../catalog/class/Qtest.php");

$mypass = new Qtest;

try {
    $dsn = 'mysql:host=' . $mypass->getDSN2("host") . ';dbname=' . $mypass->getDSN2("database") . ';charset=utf8mb4';
    $pdo = new PDO($dsn, $mypass->getDSN2("username"), $mypass->getDSN2("pwd"));

    // Enable exception mode
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    die("PDO Connection failed: " . $e->getMessage());
}

if (!isset($_GET['start'], $_GET['end'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required parameters']);
    exit;
}

$startMonth = $_GET['start'];
$endMonth = $_GET['end'];

// Output JSON headers
header('Content-Type: application/json');
header('Content-Disposition: attachment; filename="report_' . $startMonth . '_to_' . $endMonth . '.json"');

// Output the data
echo getDataJSON($pdo, $startMonth, $endMonth);
