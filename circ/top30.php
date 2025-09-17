<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once("../shared/common.php");
$tab = "circulation/analytics";
$nav = "top30";	

require_once(REL(__FILE__, "../shared/logincheck.php"));

Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));

		require_once __DIR__ . '/../autoload.php'; // adjust the ../ if necessary depending on your source path.
		use BookUtilization\Top30borrow;
		
		$result = new Top30borrow();
		$stmt = $result->make_top30borrowlist();

// Get current date
date_default_timezone_set('Asia/Manila');
$endDate = new DateTime(); // today
$startDate = (clone $endDate)->modify('-6 months');

// Format dates
$startFormatted = $startDate->format('F j, Y'); // e.g., January 22, 2025
$endFormatted = $endDate->format('F j, Y');     // e.g., July 22, 2025

// Output the dynamic heading
$title_top30 = "<h2>" . T("top30active") . " (" . $startFormatted . " - " . $endFormatted . ") </h2>";
?>
<section style="width: 700px;" id="top30_section">
	<table border="1" cellpadding="8" cellspacing="0">
		<thead>
			<?= $title_top30; ?>
			<tr>
				<th>Rank</th>
				<th>Title</th>
				<th>Author</th>
				<th>ISBN</th>
				<th>Count</th>
			</tr>
		</thead>
		<tbody>
		
		<?php 
			require_once("top30_portion.php");
		?>

  </tbody>
</table>
</section>


