<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once("../shared/common.php");
$tab = "admin/analytics";
$nav = "ddc_top30list";	

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

// Output the dynamic heading
$title_top30 = "<h2>Top 30 DDC List with 3 Level Mapping</h2>";
?>
<section style="width: 700px; background-color: #F5DEB3; " id="top30_section">
	<table border="1" cellpadding="8" cellspacing="0">
		<thead>
			<?= $title_top30; ?>
			<tr>
				<th>Row</th>
				<th>DDC</th>
				<th>Level 1 Main Class</th>
				<th>Level 2 Division Mapping</th>
				<th>Level 3 Topic Mapping</th>
				<th>Count</th>
			</tr>
		</thead>
		<tbody>
		
		<?php 
			require_once("ddc30_top30list_portion.php");
		?>

  </tbody>
</section>


