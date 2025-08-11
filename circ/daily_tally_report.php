<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 *
 * daily_tally_export.php
 * Standalone JSON export for daily book tally
 * Works with start_date & end_date GET parameters
 */
require_once("../shared/guard_doggy.php");
require_once("../catalog/class/Qtest.php");

$mypass = new Qtest;

try {
    $dsn = 'mysql:host=' . $mypass->getDSN2("host") . ';dbname=' . $mypass->getDSN2("database") . ';charset=utf8mb4';
    $pdo = new PDO($dsn, $mypass->getDSN2("username"), $mypass->getDSN2("pwd"));
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "PDO Connection failed: " . $e->getMessage()]);
    exit;
}

// Get date range (defaults: last 7 days)
$start_date = $_GET['start_date'] ?? date('Y-m-d', strtotime('-7 days'));
$end_date   = $_GET['end_date'] ?? date('Y-m-d');

$sql = "
SELECT 
    DATE(start_date + INTERVAL n DAY) AS activity_date,
    COALESCE(COUNT(a.id), 0) AS total_books
FROM (
    SELECT @start := DATE(?) AS start_date,
           @end   := DATE(?) AS end_date
) params
JOIN (
    SELECT 0 n UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 
         UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6
         UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9
         UNION ALL SELECT 10 UNION ALL SELECT 11 UNION ALL SELECT 12
         UNION ALL SELECT 13 UNION ALL SELECT 14 UNION ALL SELECT 15
         UNION ALL SELECT 16 UNION ALL SELECT 17 UNION ALL SELECT 18
         UNION ALL SELECT 19 UNION ALL SELECT 20 UNION ALL SELECT 21
         UNION ALL SELECT 22 UNION ALL SELECT 23 UNION ALL SELECT 24
         UNION ALL SELECT 25 UNION ALL SELECT 26 UNION ALL SELECT 27
         UNION ALL SELECT 28 UNION ALL SELECT 29
) numbers
    ON DATE(@start + INTERVAL numbers.n DAY) <= @end
LEFT JOIN obib_book_activity a
    ON DATE(a.activity_time) = DATE(@start + INTERVAL numbers.n DAY)
GROUP BY activity_date
ORDER BY activity_date;
";

$stmt = $pdo->prepare($sql);
$stmt->execute([$start_date, $end_date]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Output JSON
header('Content-Type: application/json');
header('Content-Disposition: attachment; filename="daily_tally.json"');
echo json_encode($rows, JSON_PRETTY_PRINT);
exit;

