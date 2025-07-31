<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once("../shared/common.php");
$tab = "circulation/analytics";
$nav = "monthly";	

require_once("circ_function2.php"); 
// Handle export early — before Page::header() is called
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['start'], $_GET['end'], $_GET['action']) && $_GET['action'] === 'export') {
    $startMonth = $_GET['start'];
    $endMonth = $_GET['end'];

		// check if date is valid --> F.Tumulak
		if (!isValidMonthFormat($startMonth) || !isValidMonthFormat($endMonth)) {
				echo "<h3 style='background-color: red; padding: 10px;'>" . T('invalid_month_format') . "</h3>";
				echo "<div style='text-align: center;'><a href='./circ_report2.php' >Try Again</a></div>";
				die;
		}

    // Redirect to separate export script before headers are sent
    $params = http_build_query([
        'start' => $startMonth,
        'end' => $endMonth
    ]);
    header("Location: exportcirctojson.php?$params");
    exit;
}

Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));
require_once(REL(__FILE__, "../shared/logincheck.php"));

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

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['start'], $_GET['end'], $_GET['action'])) {
    $startMonth = $_GET['start'];
    $endMonth = $_GET['end'];
    $action = $_GET['action'];

    if ($action === 'generate') {
        // Show report logic here
				
				// check if date is valid --> F.Tumulak
				if (!isValidMonthFormat($startMonth) || !isValidMonthFormat($endMonth)) {
						echo "<h3 style='background-color: red; padding: 10px;'>" . T('invalid_month_format') . "</h3>";
						echo "<div style='text-align: center;'><a href='./circ_report2.php' >Try Again</a></div>";
						die;
				}
				$chartDataJSON = getChartDataJSON($pdo, $startMonth, $endMonth);

				//$chartDataJSON = getChartDataJSON($pdo);
				echo "<script>const chartData = $chartDataJSON;</script>";
								
    } elseif ($action === 'export') {
				// Redirect to external exporter script with query parameters

		}
} else {
	// do the default --> F.Tumulak
	
		// Set timezone (optional but good for consistency)
		date_default_timezone_set('Asia/Manila');

		// Get current month in YYYY-MM format
		$endMonth = $_GET['end'] ?? date('Y-m');

		// Get month 12 months ago in YYYY-MM format
		$startMonth = $_GET['start'] ?? date('Y-m', strtotime('-11 months', strtotime($endMonth . '-01')));

	$chartDataJSON = getChartDataJSON($pdo, $startMonth, $endMonth);
	echo "<script>const chartData = $chartDataJSON;</script>";
}
?>
</section>
<form method="get" style="margin-bottom: 20px;">
  <label for="start"><?= T("Start Month:") ?></label>
  <input type="month" id="start" name="start" value="<?php echo $startMonth; ?>" required>

  <label for="end"><?= T("End Month:") ?></label>
  <input type="month" id="end" name="end" value="<?php echo $endMonth; ?>" required>

  <button type="submit" name="action" value="generate"><?= T("Generate Report") ?></button>
  <button type="submit" name="action" value="export"><?= T("Export to JSON") ?></button>
</form>

<canvas id="myChart"></canvas>

<?php 
	$data = json_decode($chartDataJSON, true); // Convert JSON string into PHP array -- F.Tumulak
	echo "<table border='1' cellpadding='6' style='margin-top: 20px; border-collapse: collapse;'>";
	echo "<thead><tr><th>" . T("Month") . "</th><th>" . T("Total Checkouts") . "</th><th>" . T("Total Check-ins") . "</th></tr></thead>";
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