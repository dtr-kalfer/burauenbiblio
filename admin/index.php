<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once("../shared/common.php");
require_once(REL(__FILE__, "../classes/ReportDisplaysUI.php"));

$tab = "admin";
$nav = "summary";

require_once(REL(__FILE__, "../shared/logincheck.php"));
Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));
?>

    <h1><img src="../images/admin.png" border="0" width="30" height="30" align="top"> <?php echo T("Admin") ?></h1>
    <fieldset> <?php echo T("adminIndexDesc"); ?> </fieldset>
		<h3><?php  
			date_default_timezone_set('Asia/Manila'); // Philippine timezone
			echo "Welcome to BurauenBiblio! Current Date and Time: <b>" . date("m-d-Y H:i:s") . "</b>";
		?> </h3>
<?php
    ReportDisplaysUI::display('admin');
