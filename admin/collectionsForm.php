<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
	require_once("../shared/common.php");
	// $tab = "admin";
	// $nav = "collections";
	// require_once(REL(__FILE__, "../shared/logincheck.php"));
	// Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));
	
	$tab = "admin";
	$nav = "collections";
	$focus_form_name = "reportcriteriaform";
	
	require_once(REL(__FILE__, "../classes/Report.php"));

	if (isset($_SESSION['postVars']['type'])) {
		$type = $_SESSION['postVars']['type'];
	} elseif (isset($_REQUEST['type'])) {
		$type = $_REQUEST['type'];
	} else {
		header('Location: ../reports/index.php');
		exit(0);
	}

	$rpt = Report::create($type);
	if (!$rpt) {
		header('Location: ../reports/index.php');
		exit(0);
	}

	Nav::node('reports/reportcriteria',T("Report Criteria"));
	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));

	#****************************************************************************
	#*  getting form vars
	#****************************************************************************
	require(REL(__FILE__, "../shared/get_form_vars.php"));

	echo '<h3>'.T($rpt->title()).'</h3>';

	if ($_REQUEST['msg']) {
		echo '<p class="error">'.H($_REQUEST['msg']).'</p>';
	}	
	
?>
<form name="reportcriteriaform" method="get" action="../reports/run_report.php">
<fieldset>
<input type="hidden" name="type" value="<?php echo H($rpt->type()) ?>" />

<?php
	Params::printForm($rpt->paramDefs());
?>

<input type="submit" value="<?php echo T("Submit"); ?>" class="button" />
</fieldset>
</form>


    <h3><?php echo T("Collections"); ?></h3>
    <div id="listDiv" style="display: none;">
        <form role="form" id="showForm" name="showForm">
        <fieldset>
        <table id="showList">
        <thead>
        	<tr>
       	    <th colspan="1">&nbsp;</th>
        		<th valign="top"><?php echo T("Code"); ?></th>
        		<th valign="top"><?php echo T("Description"); ?></th>
        		<th valign="top"><?php echo T("Type"); ?></th>
        		<th valign="top"><?php echo T("Item Count"); ?></th>
        		<th valign="top"><?php echo T("Default"); ?></th>
        	</tr>
        </thead>
        <tbody class="striped">
        	<tr><td colspan="4"><?php echo T("No collections have been defined."); ?> </td></tr>
        </tbody>
        </table>
        </fieldset>
        </form>
    </div>
	<hr />
<!-- ------------------------>
<?php


?>



<!-- --------------------------->	
	
<?php
  require_once(REL(__FILE__,'../shared/footer.php'));
	require_once(REL(__FILE__, "../classes/JSAdmin.php"));
	require_once(REL(__FILE__, "../admin/collectionsJs6.php"));
?>
</body>
</html>
