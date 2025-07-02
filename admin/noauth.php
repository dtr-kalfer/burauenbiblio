<link rel="stylesheet" type="text/css" href="../jscalendar/jsCalendar.min.css">
<script type="text/javascript" src="../jscalendar/jsCalendar.min.js"></script>
<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once("../shared/common.php");

	$tab = "circulation";
	$nav = "";

// validate if logged in --F.Tumulak
require_once(REL(__FILE__, "../shared/logincheck.php"));


Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));
date_default_timezone_set('Asia/Manila'); // Philippine timezone

echo '<h3>Welcome to BurauenBiblio!</h3>';
echo '<p class="info" style="text-align: center;">' . $_SESSION["username"] . T(", you are now logged in.") . '</p>';
echo '<p>' . T("showstaffwelcome") . '</p>';

?>

<section class="auto-jsCalendar classic-theme red" data-month-format="month YYYY" style='padding: 10px; margin: auto; width: 81%;'></section>
<h3><?php echo T("Today is: ") . date("m-d-Y"); ?></h3>