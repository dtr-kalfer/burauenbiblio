<?php
/* 
 * This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 * 
 * DDC Classification Main Class Mapping (Level 1)
 * Source: Wikipedia - Dewey Decimal Classification
 * https://en.wikipedia.org/wiki/List_of_Dewey_Decimal_classes
 *
 * This file references materials licensed under the
 * Creative Commons Attribution-ShareAlike 4.0 International License.
 * License details: https://creativecommons.org/licenses/by-sa/4.0/legalcode
 *
 * -- F. Tumulak
 */
    require_once("../shared/common.php");
    $tab = "admin/analytics";
    $nav = "ddc_chart";    

    require_once(REL(__FILE__, "../shared/logincheck.php"));

    Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));

    require_once("../catalog/class/Qtest.php");

    $mypass = new Qtest;
    $connection = mysqli_connect(
        $mypass->getDSN2("host"),
        $mypass->getDSN2("username"),
        $mypass->getDSN2("pwd"),
        $mypass->getDSN2("database")
    );
    if (mysqli_connect_errno()) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Fetch top 30 DDCs
    $sql = "
        SELECT ddc, classification, COUNT(*) AS total
        FROM extract_ddc
        GROUP BY ddc, classification
        ORDER BY total DESC
        LIMIT 30
    ";
    $result = $connection->query($sql);

    $labels = [];
    $totals = [];
    $classifications = [];

    while ($row = $result->fetch_assoc()) {
        $labels[] = $row['ddc'];
        $totals[] = (int)$row['total'];
        $classifications[] = $row['classification'];
    }

    $connection->close();
		
		$categoryColors = [
    '000' => '#4CAF50',  // General Works
    '100' => '#2196F3',   // Philosophy & Psychology
    '200' => '#9C27B0',   // Religion
    '300' => '#FF9800',   // Social Sciences
    '400' => '#E91E63',  // Language
    '500' => '#00BCD4',   // Science
    '600' => '#8BC34A',   // Technology
    '700' => '#FFC107',  // Arts & Recreation
    '800' => '#3F51B5',  // Literature
    '900' => '#F44336',  // History & Geography
		'???' => '#9E9E9E'  // unclassified
];

?>
<style>
.chart-container {
    width: 1000px;
    
}
.legend-section {
	width: 1000px;
    margin-top: 15px;
    padding: 10x;
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    background-color: #f9f9f9;
    border-radius: 8px;
    border: 1px solid #ddd;
}
.legend-item {
    display: flex;
    align-items: center;
    gap: 4px;
    font-size: 14px;
    min-width: 200px;
}
.legend-color-box {
    width: 40px;
    height: 10px;
    border-radius: 4px;
    border: 1px solid #aaa;
}
#downloadJson {
    margin-top: 20px;
    padding: 8px 12px;
    background-color: #007BFF;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}
#downloadJson:hover {
    background-color: #0056b3;
}

        .buttons {
            margin-top: 20px;
        }
        .btn {
            display: inline-block;
            padding: 8px 16px;
            margin: 5px;
            background-color: #007BFF;
            color: white;
            font-weight: bold;
            border-radius: 6px;
            text-decoration: none;
            transition: background-color 0.2s ease;
        }
        .btn:hover {
            background-color: #0056b3;
        }

</style>

<section>
    <h2 style="text-align:center;">📊 Top 30 Dewey Decimal Stats</h2>
</section>

<section class="chart-container">
    <canvas id="ddcChart"></canvas>
</section>

<!-- Color legend container -->
<div id="colorLegend" class="legend-section"></div>

<!-- Load Chart.js (Use CDN for reliability) -->
<script src="../circ/js/chart.js"></script>
<section class="legend-section">
				<?php foreach ($categoryColors as $range => $color): ?>
						<div class="legend-item">
								<span class="legend-color-box" style="background-color: <?= $color; ?>"></span>
								<span><strong><?= $range; ?></strong> —
										<?php
												switch ($range) {
														case '000': echo "General Works"; break;
														case '100': echo "Philosophy & Psychology"; break;
														case '200': echo "Religion"; break;
														case '300': echo "Social Sciences"; break;
														case '400': echo "Language"; break;
														case '500': echo "Science"; break;
														case '600': echo "Technology"; break;
														case '700': echo "Arts & Recreation"; break;
														case '800': echo "Literature"; break;
														case '900': echo "History & Geography"; break;
														default : echo "Unknown or Invalid"; break;
												}
										?>
								</span>
						</div>
				<?php endforeach; ?>
</section>

<section class="buttons">
		<!-- Download JSON button -->
		<button id="downloadJson">⬇ Download JSON</button>		
		<!-- Wikipedia Link -->
		<a href="https://en.wikipedia.org/wiki/List_of_Dewey_Decimal_classes" class="btn" target="_blank">
				📖 Detailed Dewey Decimal Classes (Wikipedia)
		</a>
</section>
<p style="text-align: center;">DDC category descriptions are adapted from <a href="https://en.wikipedia.org/wiki/List_of_Dewey_Decimal_classes">Wikipedia</a> under the <a href="https://creativecommons.org/licenses/by-sa/4.0/">CC BY-SA 4.0 License</a></p>
<script>
	// PHP data → JavaScript
	const labels = <?php echo json_encode($labels); ?>;
	const data = <?php echo json_encode($totals); ?>;
	const classifications = <?php echo json_encode($classifications); ?>;

	// Consistent category colors
	const categoryColors = {
			"General Works": "#4CAF50",
			"Philosophy and Psychology": "#2196F3",
			"Religion": "#9C27B0",
			"Social Sciences": "#FF9800",
			"Language": "#E91E63",
			"Science": "#00BCD4",
			"Technology": "#8BC34A",
			"Arts and Recreation": "#FFC107",
			"Literature": "#3F51B5",
			"History and Geography": "#F44336",
			"Unclassified": "#9E9E9E"
	};

	// Sort labels numerically
	const sorted = labels
			.map((ddc, i) => ({ ddc, total: data[i], classification: classifications[i] }))
			.sort((a, b) => parseFloat(a.ddc) - parseFloat(b.ddc));

	const sortedLabels = sorted.map(item => item.ddc);
	const sortedData = sorted.map(item => item.total);
	const sortedClassifications = sorted.map(item => item.classification);
	const sortedColors = sortedClassifications.map(cat => categoryColors[cat] || "#999999");

	// Create chart
	const ctx = document.getElementById("ddcChart").getContext("2d");
	const ddcChart = new Chart(ctx, {
			type: "bar",
			data: {
					labels: sortedLabels,
					datasets: [{
							label: "Top 30 DDC Categories",
							data: sortedData,
							backgroundColor: sortedColors,
							borderWidth: 1
					}]
			},
			options: {
					responsive: true,
					plugins: {
							legend: { display: false }
					},
					scales: {
							x: { title: { display: true, text: "DDC Code" }},
							y: { beginAtZero: true, title: { display: true, text: "Total Copies" }}
					}
			}
	});

	// JSON download
	document.getElementById("downloadJson").addEventListener("click", () => {
			const jsonData = sorted.map(item => ({
					ddc: item.ddc,
					classification: item.classification,
					total: item.total
			}));
			const blob = new Blob([JSON.stringify(jsonData, null, 2)], { type: "application/json" });
			const url = URL.createObjectURL(blob);
			const link = document.createElement("a");
			link.href = url;
			link.download = "ddc_top30.json";
			link.click();
			URL.revokeObjectURL(url);
	});
</script>
