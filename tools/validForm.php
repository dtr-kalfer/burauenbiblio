<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
 
  $cache = NULL;
  require_once("../shared/common.php");

  $tab = "tools";
  $nav = "valid";
  $focus_form_name = "showForm";
  $focus_form_field = "";

  require_once(REL(__FILE__, "../functions/inputFuncs.php"));
  require_once(REL(__FILE__, "../shared/logincheck.php"));
  
	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));

?>
<h3 id="listHdr"><?php echo T("Validation Patterns"); ?></h3>

<div id="listDiv" style="display: none;">
<h5 id="updateMsg"></h5>

<form role="form" id="showForm" name="showForm">
<input type="button" class="newBtn" value="<?php echo T("Add New"); ?>" />
<fieldset>
<legend id="listHdr"> </legend>
<table id="showList" name="showList">
	<thead>
  <tr>
    <th colspan="1">&nbsp;</th>
    <th><?php echo T("Code"); ?></th>
    <th><?php echo T("Description"); ?></th>
    <th><?php echo T("Pattern"); ?></th>
  </tr>
	</thead>
	<tbody class="striped">
	  <tr><td colspan="4"><?php echo T("No patterns have been defined"); ?></td></tr>
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


<div id="editDiv">
<form role="form" id="editForm" name="editForm">
<h5 id="reqdNote">*<?php echo T("Required note"); ?></h5>
<fieldset>
<legend id="fieldsHdr"> </legend>
<table id="editTbl">
  <tbody>
  <tr>
    <td>
      <label for="code"><?php echo T("Code"); ?>:</label>
    </td>
    <td>
      <input id="code" name="code" type="text" size="20" maxlength="20" required aria-required="true" />
			<span class="addOnly reqd">*</span>    
    </td>
  </tr>
  <tr>
    <td>
      <label for="description"><?php echo T("Description"); ?>:</label>
    </td>
    <td>
      <input id="description" name="description" type="text" size="32" required aria-required="true" />
			<span class="reqd">*</span>
		</td>
  </tr>
  <tr>
    <td>
      <label for="pattern"><?php echo T("Pattern"); ?>:</label>
    </td>
    <td>
      <textarea id="pattern" name="pattern" cols="100" wrap="soft" required aria-required="true" /></textarea>
			<span class="reqd">*</span>
		</td>
  </tr>
  <tr>
    <td><input type="hidden" id="cat" name="cat" value="valid"></td>
    <td><input type="hidden" id="mode" name="mode" value=""></td>
  </tr>
  <tfoot>
  <tr>
    <td colspan="1" align="left">
			<input type="submit" id="addBtn" class="actnBtns" value="<?php echo T("Add"); ?>" />
			<input type="submit" id="updtBtn" class="actnBtns" value="<?php echo T("Update"); ?>" />
			<input type="button" id="cnclBtn" value="<?php echo T("Cancel"); ?>" />
    </td>
    <td colspan="1" align="right">
			<input type="submit" id="deltBtn" class="actnBtns" value="<?php echo T("Delete"); ?>" />
    </td>
  </tr>
  </tfoot>
  </tbody>

</table>
</fieldset>
</form>
</div>

<div id="msgDiv" style="display: none;"><fieldSet id="userMsg"></fieldset></div>

<?php
  require_once(REL(__FILE__,'../shared/footer.php'));
	
	require_once(REL(__FILE__, "../classes/JSAdmin.php"));
	require_once(REL(__FILE__, "../tools/validJs6.php"));
?>	

</body>
</html>
