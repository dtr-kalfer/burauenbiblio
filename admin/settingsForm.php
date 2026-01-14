<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");

	$tab = "admin";
	$nav = "settings";
	require_once(REL(__FILE__, "../shared/logincheck.php"));

	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));
?>
<h3 id="listHdr"><?php echo T("Library Settings"); ?></h3>

<div id="editDiv">
	<div id="tabs">
		<fieldset>
			<ul class="controls inline">
				<!-- the displayed tab order will follow the order of the following links -->
				<li class="active"><a href="#libraryPage"><?php echo T("Library"); ?></a></li>
				
				
				
				
				
			</ul>

			<!-- Note titles/Labels in this form are 'placeholders only', actual labels will be downloaded from the database -->
			<form role="form" id="editSettingsForm" name="editSettingsForm" >
				<div id="libraryPage" class="block active">
					<fieldset  class="inlineFldSet">
						<label for="libraryName"><?php echo T("Library Title"); ?></label>
						<select id="libraryName" name="library_name" autofocus ></select>
						<br />
						<!--label for="libraryHours"><?php echo T("Library Hours"); ?></label>
						<textarea id="libraryHours" name="library_hours" rows="1" placeholder="M-F: 8am - 5pm<br />Sat:  9am - noon" /></textarea>
						<br /-->
						<label for="libraryPhone"><?php echo T("Library Phone"); ?></label>
						<input type="text" id="libraryPhone" name="library_phone" maxlength="32" />
						<br />
						<label for="library_home"><?php echo T("Library Address"); ?></label>
						<input type="text" id="library_home" name="library_home" maxlength="29" />
						<br />
						<label for="library_url"><?php echo T("Library URL"); ?></label>
						<input type="text" id="library_url" name="library_url" maxlength="36" />
						<br />
						<label for="library_image_url"><?php echo T("Library Image"); ?></label>
						<input type="text" id="library_image_url" name="library_image_url" maxlength="64" placeholder="photo of your choice" />
						<br />
						
					</fieldset>
					<fieldset class="inlineFldSet2">
						<img id="libImg" width="150" height="150" />
					</fieldset>
					<p>*note: Set logo background to transparent for best results.</p>
				</div>
				<input type="hidden" id="cat" name="cat" value="settings" />
				<input type="hidden" id="mode" name="mode" />
				<input type="submit" id="updtBtn" value="Update" />
			</form>
		</fieldset>
	</div>
</div>

<div id="photoEditorDiv">
	<?php require_once(REL(__FILE__,"../catalog/photoEditorForm.php"));?>
		<input type="button" id="fotoDoneBtn" value="Done" />
</div>

<?php
 	require_once(REL(__FILE__,'../shared/footer.php'));
	require_once(REL(__FILE__, "../admin/settingsJs.php"));
?>
</body>
</html>
