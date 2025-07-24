<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once("../shared/common.php");
$tab = "circulation/analytics";
$nav = "monthly";	


require_once(REL(__FILE__, "../shared/logincheck.php"));

Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));

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
?>
<script src="./js/chart.js"></script>
<section style="width: 600px;" id="circ_section">
<?php 


$startMonth = $_GET['start'] ?? '2025-01';
$endMonth = $_GET['end'] ?? '2025-06';

$chartDataJSON = getChartDataJSON($pdo, $startMonth, $endMonth);

	//$chartDataJSON = getChartDataJSON($pdo);
	echo "<script>const chartData = $chartDataJSON;</script>";
?>
</section>
<form method="get" style="margin-bottom: 20px;">
  <label for="start">Start Month:</label>
  <input type="month" id="start" name="start" value="<?php echo $_GET['start'] ?? ''; ?>" required>

  <label for="end">End Month:</label>
  <input type="month" id="end" name="end" value="<?php echo $_GET['end'] ?? ''; ?>" required>

  <button type="submit">Generate Report</button>
</form>

<canvas id="myChart"></canvas>

<?php 
	$data = json_decode($chartDataJSON, true); // Convert JSON string into PHP array -- F.Tumulak
	echo "<table border='1' cellpadding='6' style='margin-top: 20px; border-collapse: collapse;'>";
	echo "<thead><tr><th>Month</th><th>Total Checkouts</th><th>Total Check-ins</th></tr></thead>";
	echo "<tbody>";
	foreach ($data['labels'] as $i => $month) {
			$checkouts = $data['datasets'][0]['data'][$i];
			$checkins = $data['datasets'][1]['data'][$i];
			echo "<tr><td>{$month}</td><td>{$checkouts}</td><td>{$checkins}</td></tr>";
	}
	echo "</tbody></table>";
?>

	<script>
		const ctx = document.getElementById('myChart').getContext('2d');
		new Chart(ctx, {
			type: 'bar',
			data: chartData,
			options: {
				responsive: true,
				plugins: {
					legend: { position: 'top' },
					title: { display: true, text: 'Monthly Book Checkouts and Check-ins' }
				}
			}
		});
	</script>
