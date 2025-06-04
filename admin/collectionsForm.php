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

	//Nav::node('admin/collections',T("Collections"));
	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));

	#****************************************************************************
	#*  getting form vars
	#****************************************************************************
	require(REL(__FILE__, "../shared/get_form_vars.php"));

	echo '<h3>'.T($rpt->title()).'</h3>';

	if (isset($_REQUEST['msg'])) {  // Warning: Undefined array key "msg" --F.Tumulak
		echo '<p class="error">'.H($_REQUEST['msg']).'</p>';
	}	
	
?>
<form name="reportcriteriaform" method="get" action="../admin/update_due_date.php">
<fieldset>
<input type="hidden" name="type" value="<?php echo H($rpt->type()) ?>" />

<?php
	Params::printForm($rpt->paramDefs());
?>

<input type="submit" value="<?php echo T("Submit"); ?>" class="button" />
</fieldset>
</form>
		<p>In order to promote fair and efficient access to library resources, <br>
		a borrowing time limit policy is implemented. This policy defines the <br>
		number of days a library item may be borrowed without penalty incurred. <br>
		It also includes a penalty rate, which can be adjusted for that collection</p>
<hr>
<!-- -------------------------------------------------->

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
    <div id="editDiv" style="display: none;">
        <form role="form" id="editForm" name="editForm">
        <h5 id="reqdNote">*<?php echo T("Options below for viewing only!"); ?></h5>
        <fieldset>
        	<legend id="editHdr"></legend>
        	<ul id="editTbl">
            <li>
              <label for="description"><?php echo T("Description"); ?>:</label>
              <input readonly id="description" name="description" type="text" size="32" required aria-required="true" />
        			<span class="reqd">*</span>
        		</li>
            <li>
              <label for="code"><?php echo T("Code"); ?>:</label>
              <input readonly id="code" name="code" type="text" size="3" />
        		</li>


            <li>
              <label><?php echo T("Default"); ?>:</label>
              <label for="default_Y">Y:<label>
              <input readonly id="default_Y" name="default_flg" type="radio" value="Y" required aria-required="true" />
              <label for="default_N">N:</label>
              <input readonly id="default_N" name="default_flg" type="radio" value="N" checked required aria-required="true" />
        			<span class="reqd">*</span>
            </li>

            <li class="calculator-simple">
              <label for="days_due_back" class="circOnly"><?php echo T("Allowed number of days"); ?>:</label>
              <input readonly id="days_due_back" name="days_due_back" class="" type="number" size="3" min="0" max="365" required aria-required="true" />
        			<span class="reqd circOnly">*</span>
            </li>

            <li>
              <label for="regular_late_fee" class="circOnly"><?php echo T("Late fee rate"); ?>:</label>
              <input readonly id="regular_late_fee" name="regular_late_fee" class="circOnly" type="number" size="5" min="0" max="99.99" required aria-required="true" />
        			<span class="reqd circOnly">*</span>
        		</li>
            <li>
        			<input type="hidden" id="mode" name="mode" value="">
        			<input type="hidden" id="cat" name="cat" value="collect">
        			
        		</li>
        	</ul>

	<hr />
        	<ul id="btnRow">
            <li><input type="submit" id="addBtn" class="actnBtns" value="<?php echo T("Add"); ?>" /></li>
            <!-- <li><input type="submit" id="updtBtn" class="actnBtns" value="<?php //echo T("Update"); ?>" /></li> -->
            <li><input type="button" id="cnclBtn" value="<?php echo T("Cancel"); ?>" /></li>
            <!-- <li><input type="submit" id="deltBtn" class="actnBtns" value="<?php //echo T("Delete"); ?>" /></li> -->
        	</ul>
        </fieldset>
        </form>



<!-- --------------------------->	
	
<?php
  require_once(REL(__FILE__,'../shared/footer.php'));
	require_once(REL(__FILE__, "../classes/JSAdmin.php"));
	require_once(REL(__FILE__, "../admin/collectionsJs6.php"));
?>

</body>
</html>
