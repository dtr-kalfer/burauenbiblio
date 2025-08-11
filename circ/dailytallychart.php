<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
require_once("../shared/common.php");
$tab = "circulation/analytics";
$nav = "dailytally";	

require_once(REL(__FILE__, "../shared/logincheck.php"));

Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));

//require_once("top30_function.php"); 
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

// Default dates (last 7 days)
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
//$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (isset($_GET['export']) && $_GET['export'] === 'json') {
    header('Content-Type: application/json');
    echo json_encode($rows, JSON_PRETTY_PRINT);
    exit;
}

// Prepare data for Chart.js
$labels = [];
$data   = [];

foreach ($rows as $row) {
    $labels[] = $row['activity_date'];
    $data[]   = $row['total_books'];
}
?>
<script src="./js/chart.js"></script>
<section>
	<h2>Daily Book Tally (Not Borrowed - Total number of books per day)</h2>

	<form method="GET">
			Start Date: <input type="date" name="start_date" value="<?= htmlspecialchars($start_date) ?>">
			End Date: <input type="date" name="end_date" value="<?= htmlspecialchars($end_date) ?>">
			<button type="submit">Show</button>

			<a href="daily_tally_export.php?start_date=<?= urlencode($start_date) ?>&end_date=<?= urlencode($end_date) ?>" target="_blank">
				Export to JSON
			</a>
	</form>
	
	<canvas id="tallyChart" width="800" height="400"></canvas>
</section>

<script>
const ctx = document.getElementById('tallyChart').getContext('2d');
const tallyChart = new Chart(ctx, {
    type: 'bar', // Change to 'line' if you prefer
    data: {
        labels: <?= json_encode($labels) ?>,
        datasets: [{
            label: 'Total Books',
            data: <?= json_encode($data) ?>,
            backgroundColor: 'rgba(54, 162, 235, 0.6)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: { beginAtZero: true }
        }
    }
});
</script>
