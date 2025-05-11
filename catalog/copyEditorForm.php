<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
 
    require_once("../shared/common.php");
 	require_once(REL(__FILE__, "../model/BiblioCopyFields.php"));
	require_once(REL(__FILE__, "../model/CopyStatus.php"));	
?>

	<p class="note"><?php echo T("Fields marked are required"); ?></p>

	<form role="form" id="copyForm" name="copyForm" >
	<fieldset>
	<legend id="copyLegend"><?php echo T("Add New Copy"); ?></legend>
	<table id="copyTbl" >
		<tbody id="dfltFlds" class="unstriped">
			<tr>
				<td><label for="copyBarcode_nmbr"><?php echo T("Barcode Number"); ?></label></td>
				<td>
					<input id="copyBarcode_nmbr" name="barcode_nmbr" type="number" size="20" title="zero-filled barcode" required aria-required="true" />
					<span class="reqd">*</span>
				</td>
			</tr>
			<tr>
				<td><label for="autobarco"><?php echo T("Auto Barcode"); ?></label></td>
				<td>
					<input id="autobarco" name="autobarco" type="checkbox" value="Y"
						<?php
						// Use of undefined constant checked - assumed 'checked' (this will throw an Error in a future version of PHP)
                  // in catalog/copyEditorForm.php on line 29
                  // request: "GET /circ/memberForms.php HTTP/1.1"
                  // referrer: admin/dbChkrForms.php?tab=auto&rtnTo=/circ/memberForms.php
						if ($_SESSION['item_autoBarcode_flg'] == 'Y') { echo 'checked'; }
						?> />
				</td>
			</tr>
			<tr>
				<td><label for="copyDesc"><?php echo T("Description"); ?></label></td>
				<td><input id="copyDesc" name="copy_desc" type="text" size="40" />
					<span class="reqd">*</span>
				</td>
			</tr>
		<?php // Not to be shown when in normal (non multisite mode)
		if($_SESSION['multi_site_func'] > 0){
		?>
			<tr>
				<td><label for="copySite"><?php echo T("Location"); ?></label></td>
				<td><select id="copySite" name="copy_site">to be filled in by server.</select>
					<span class="reqd">*</span>
				</td>
			</tr>
		<?php } ?>
			<tr>
				<td><label for="status_cd"><?php echo T("Status");?>:</label></td>
				<td>
					<!--?php
						$status = new CopyStatus();
						$status_select = $status->getSelect();
						echo inputfield(select, status_cd, "in", null, $status_select);
					?-->
                    <select id="status_cd">
					<select>
				</td>
			</tr>
		</tbody>
		<tbody id="cstmFlds" class="unstriped">
			<!-- Custom fields /-->
			<?php
				$ptr = new BiblioCopyFields;
				$rows = $ptr->getAll();
				//while ($row = $rows->fetch_assoc()) {
                foreach ($rows as $row) {
					echo "<tr>";
					echo "<td nowrap=\"true\" valign=\"top\"><label for=\"copyCustom_". $row["code"] . "\">" . T($row["description"]) . "</td>";
					echo "<td valign=\"top\" \">" . inputfield('text', 'copyCustom_'.$row["code"], "",NULL) . "</td>";
					echo "</tr>";
				}
			?>
			<tr>
				<td colspan="2">
					<input type="hidden" id="copyBibid" name="bibid" value="" />
					<input type="hidden" id="copyMode" name="mode" value="" />
				</td>
			</tr>
		</tbody>

	</table>
	<div class="btnRow gridded">
		<input type="submit" id="copySubmitBtn" class="col1" value="<?php echo T("Submit"); ?>" />
		<input type="button" id="copyCancelBtn" class="col4" value="<?php echo T("Cancel"); ?>" />
	</div>
	</fieldset>
	</form>

<?php
	include_once ("../shared/jsLibJs.php");
	include_once(REL(__FILE__,'../catalog/copyEditorJs.php'));
?>
