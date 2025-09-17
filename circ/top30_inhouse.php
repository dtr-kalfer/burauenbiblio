<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once("../shared/common.php");
$tab = "circulation/analytics";
$nav = "top30_inhouse";	

require_once(REL(__FILE__, "../shared/logincheck.php"));

Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));

//require_once("top30_function.php"); 
require_once("../catalog/class/Qtest.php");

		require_once __DIR__ . '/../autoload.php'; // adjust the ../ if necessary depending on your source path.
		use BookUtilization\Top30inhouse;
		
		$result = new Top30inhouse();
		$stmt = $result->make_top30inhouselist();


// Get current date
date_default_timezone_set('Asia/Manila');
$endDate = new DateTime(); // today
$startDate = (clone $endDate)->modify('-1 months');

// Format dates
$startFormatted = $startDate->format('F j, Y'); // e.g., January 22, 2025
$endFormatted = $endDate->format('F j, Y');     // e.g., July 22, 2025

// Output the dynamic heading
$title_top30 = "<h2>" . T("top30active_inhouse") . " (" . $startFormatted . " - " . $endFormatted . ") </h2>";
?>
<section style="width: 700px; background-color: #F5DEB3; " id="top30_section">
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
			require_once("top30_inhouse_portion.php");
		?>

  </tbody>
</table>
</section>


