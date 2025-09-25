<?php

require_once("../shared/common.php");
$tab = "circulation/analytics";
$nav = "monthly";	

require_once __DIR__ . '/../autoload.php'; // adjust the ../ if necessary depending on your source path.
use Circ_Analytics\Circ_Analytics;

// Handle export early — before Page::header() is called
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['start'], $_GET['end'], $_GET['action']) && $_GET['action'] === 'export') {
    $startMonth = $_GET['start'];
    $endMonth = $_GET['end'];
		
		$analytics = new Circ_Analytics();
		
		// check if date is valid --> F.Tumulak
		if (!$analytics->isValidMonthFormat($startMonth) || !$analytics->isValidMonthFormat($endMonth)) {
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

?>
<script src="./js/chart.js"></script>
<h2>Circulation Report</h2>
<section style="width: 600px;" id="circ_section">
<?php 

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['start'], $_GET['end'], $_GET['action'])) {
    $startMonth = $_GET['start'];
    $endMonth = $_GET['end'];
    $action = $_GET['action'];

    if ($action === 'generate') {
        // Show report logic here
				$analytics = new Circ_Analytics();
				
				// check if date is valid --> F.Tumulak
				if (!$analytics->isValidMonthFormat($startMonth) || !$analytics->isValidMonthFormat($endMonth)) {
						echo "<h3 style='background-color: red; padding: 10px;'>" . T('invalid_month_format') . "</h3>";
						echo "<div style='text-align: center;'><a href='./circ_report2.php' >Try Again</a></div>";
						die;
				}
				$chartDataJSON = $analytics->getChartDataJSON($startMonth, $endMonth);

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

	$analytics = new Circ_Analytics();

	$chartDataJSON = $analytics->getChartDataJSON($startMonth, $endMonth);
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