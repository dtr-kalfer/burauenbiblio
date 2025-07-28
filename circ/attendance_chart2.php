<?php
	/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
	 * See the file COPYRIGHT.html for more details.
	 */

	require_once("../shared/common.php");
	$tab = "admin/analytics";
	$nav = "attendance_chart";	

	require_once(REL(__FILE__, "../shared/logincheck.php"));

	$action = $_GET['action'] ?? 'chart';

	if ($action === 'json') {
			// get post inputs
			$start = $_GET['start_month'] ?? date('Y-m');
			$end = $_GET['end_month'] ?? date('Y-m');
			$students_only = $_GET['students_only'] ?? '';
			// Redirect to exporter with query parameters
			$query = http_build_query([
					'start_month' => $start,
					'end_month' => $end,
					'students_only' => $students_only
			]);
			header("Location: export_attendance_json.php?$query");
			exit;
	} else {
		// Get month range from form
		$start = $_GET['start_month'] ?? '2025-01';
		$end = $_GET['end_month'] ?? '2025-06';
		$students_only = $_GET['students_only'] ?? '';
	}

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

	function isValidMonthFormat($month) {
			return preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $month);
	}

	if (!isValidMonthFormat($start) || !isValidMonthFormat($end)) {
			echo "<h3 style='background-color: red; padding: 10px;'>" . T('invalid_month_format') . "</h3>";
			echo "<section style='text-align: center;'><a href='./attendance_chart2.php' >Try Again</a></section>";
			die;
	}

	// Turn into proper date range
	$start_date = $start . '-01';
	$end_date = date('Y-m-t', strtotime($end . '-01')); // Last day of the end month

	$isStudentsOnly = isset($_GET['students_only']) && $_GET['students_only'] == '1';

	// Prepare data. --F.Tumulak

	$sql = "
			SELECT 
					user_type,
					course,
					DATE_FORMAT(date, '%Y-%m') AS month,
					SUM(count) AS total
			FROM library_attendance
			WHERE date BETWEEN ? AND ?
	";

	// Filter only students if needed
	if ($isStudentsOnly) {
			$sql .= " AND user_type = 'Student'";
	}

	$sql .= "
			GROUP BY user_type, course, month
			ORDER BY month ASC
	";
	
	$stmt = $connection->prepare($sql);
	$stmt->bind_param("ss", $start_date, $end_date);
	$stmt->execute();
	$result = $stmt->get_result();

	// Generate month list
	$months = [];
	$current = strtotime($start_date);
	$end_ts = strtotime($end_date);
	while ($current <= $end_ts) {
			$months[] = date('Y-m', $current);
			$current = strtotime('+1 month', $current);
	}

	// Create dataset structure
	$courses = file('courses.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

	// Assume $courses is already defined earlier
	if ($isStudentsOnly) {
			$labels = $courses;  // Students only --F.Tumulak
	} else {
			$labels = array_merge($courses, ['Faculty', 'Visitor']);  // All attendees merge with students --F.TUMULAK
	}

	$data_map = [];
	foreach ($labels as $label) {
			$data_map[$label] = array_fill(0, count($months), 0);
	}

	while ($row = $result->fetch_assoc()) {
			$month = $row['month'];
			$index = array_search($month, $months);
			if ($index !== false) {
					$key = ($row['user_type'] === 'Student') ? $row['course'] : $row['user_type'];
					if (isset($data_map[$key])) {
							$data_map[$key][$index] += (int)$row['total'];
					}
			}
	}
	$stmt->close();
	$connection->close();
	
?>
  <script src="./js/chart.js"></script>
    <style>
        .chart-container-attendance {
            width: 1000px;
            margin: auto;
        }
        form {
            text-align: center;
            margin-bottom: 20px;
        }
        input[type="month"] {
            padding: 5px;
            margin: 0 10px;
        }
        button {
            padding: 6px 15px;
        }
    </style>

<form method="GET" action="">
    <label>Start Month:</label>
    <input type="month" name="start_month" value="<?= htmlspecialchars($start) ?>" required>
    <label>End Month:</label>
    <input type="month" name="end_month" value="<?= htmlspecialchars($end) ?>" required>
    <label>
        <input type="checkbox" name="students_only" value="1" <?= isset($_GET['students_only']) ? 'checked' : '' ?>>
        Show Students Only
    </label>
    <button type="submit" name="action" value="chart"><?= T("Update Chart") ?></button>
    <button type="submit" name="action" value="json"><?= T("Export to JSON") ?></button>
</form>

		<section class="chart-container-attendance">
			<canvas id="attendanceChart"></canvas>
		</section>

    <script>
        const ctx = document.getElementById('attendanceChart').getContext('2d');
        const chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?= json_encode(array_map(function ($m) {
                    return date('F', strtotime($m . '-01'));
                }, $months)) ?>,
                datasets: [
                    <?php foreach ($data_map as $label => $values): ?>
                    {
                        label: '<?= $label ?>',
                        data: <?= json_encode($values) ?>,
                        backgroundColor: '<?= '#' . substr(md5($label), 0, 6) ?>'
                    },
                    <?php endforeach; ?>
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'top' },
                    title: {
                        display: true,
                        text: 'Library Attendance from <?= date('F Y', strtotime($start_date)) ?> to <?= date('F Y', strtotime($end_date)) ?>'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Number of Attendees'
                        }
                    }
                }
            }
        });
    </script>