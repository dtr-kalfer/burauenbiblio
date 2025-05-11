<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");

	$tab = "admin";
	$nav = "hours";
	require_once(REL(__FILE__, "../shared/logincheck.php"));

	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));
?>
<h3 id="listHdr"><?php echo T("Hours open"); ?></h3>

<div id="listDiv" style="display: none;">

<form role="form" id="showForm" name="showForm">
<input type="button" class="newBtn" value="<?php echo T("Add New"); ?>" />
<fieldset>
<table id="showList" name="showList"">
	<thead>
  	<tr>
  	  <th colspan="1">&nbsp;</th>
 			<th><?php echo T("Site"); ?></th>
			<th><?php echo T("Day"); ?></th>
			<th><?php echo T("Opening"); ?></th>
			<th><?php echo T("Closing"); ?></th>
			<th><?php echo T("By appointment"); ?></th>
			<th><?php echo T("Start Date - Effective"); ?></th>
			<th><?php echo T("End Date - Effective"); ?></th>
			<th><?php echo T("Note - public"); ?></th>
			<th><?php echo T("Note - private"); ?></th>
		</tr>
	</thead>
	<tbody class="striped">
	  <tr><td colspan="9"><?php echo T("No hours have been defined."); ?> </td></tr>
	</tbody>
	<tfoot>
  	<tr>
  		<!-- spacer used to slightly seperate button from form body -->
    	<td><input type="hidden" id="xxx" name="xxx" value=""></td>
  	</tr>
	</tfoot>
</table>
</fieldset>
<input type="submit" class="newBtn" value="<?php echo T("Add New"); ?>" />
</form>
</div>

<div id="editDiv" style="display: none;">
<form role="form" id="editForm" name="editForm">
<h5 id="reqdNote">*<?php echo T("Required note"); ?></h5>
<fieldset>
	<legend id="editHdr"></legend>
	<ul id="editTbl">
    <li>
      <label for="siteid"><?php echo T("Site"); ?>:</label>
      <select id="siteid" name="siteid" required aria-required="true"></select>
      <span class="reqd">*</span>    
    </li>
    <li>
      <label for="day"><?php echo T("Day"); ?>:</label>
      <select id="day" name="day" required aria-required="true"></select>
      <span class="reqd">*</span>    
    </li>
    <li>
      <label for="start_time"><?php echo T("Opening"); ?>:</label>
      <input id="start_time" name="start_time" type="time" />
    </li>
    <li>
      <label for="end_time"><?php echo T("Closing"); ?>:</label>
      <input id="end_time" name="end_time" type="time" />
    </li>
    <li>
      <label for="public_note"><?php echo T("Note - public"); ?>:</label>
      <input id="public_note" name="public_note" type="text" size="32" />
    </li>
    <li>
      <label for="private_note"><?php echo T("Note - private"); ?>:</label>
      <input id="private_note" name="private_note" type="text" size="32" />
    </li>
    <li>
      <label for="by_appointment"><?php echo T("By appointment"); ?>:</label>
      <input id="by_appointment" name="by_appointment" type="checkbox" />
    </li>
    <li>
			<input type="hidden" id="cat" name="cat" value="hours" />
			<input type="hidden" id="hourid" name="hourid" value="" />
			<input type="hidden" id="mode" name="mode" value="" />
    </li>
	</ul>
	<ul id="btnRow">
    <li><input type="submit" id="addBtn"  name="addBtn" class="actnBtns" value="<?php echo T("Add"); ?>" /></li>
    <li><input type="submit" id="updtBtn" name="updtBtn" class="actnBtns" value="<?php echo T("Update"); ?>" /></li>
    <li><input type="button" id="cnclBtn" name="cnclBtn" value="<?php echo T("Cancel"); ?>" /></li>
    <li><input type="submit" id="deltBtn" name="deltBtn" class="actnBtns" value="<?php echo T("Delete"); ?>" /></li>
	</ul>
</fieldset>
</form>
</div>

<?php
	require_once(REL(__FILE__,'../shared/footer.php'));

    require_once(REL(__FILE__, "../classes/JSAdmin.php"));
	require_once(REL(__FILE__, "hoursJs6.php"));
?>	
</body>
</html>
