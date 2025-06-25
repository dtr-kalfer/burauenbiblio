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
				
				
				<li><a href="#photoPage"><?php echo T("CoverPhotos"); ?></a></li>
				<li><a href="#opacPage"><?php echo T("OPAC"); ?></a></li>
				
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
					<fieldset class="inlineFldSet">
						<img id="libImg" width="150" height="150" />
					</fieldset>
				</div>


				<div id="photoPage" class="block">
                    <label for="use_image_flg"><?php echo T("Use Image"); ?></label>
                    <input type="checkbox" id="use_image_flg" name="use_image_flg" value="Y" />
					<br />
					<label for="camera"><?php echo T("Select a camera"); ?></label>
					<select id="camera" name="camera" ><option >Not available</option></select>
					<br />
					<label for="items_per_page"><?php echo T("Photos per Page"); ?></label>
					<input type="number" id="items_per_page" name="items_per_page" maxlength="18" value="25" required aria-required />
					<br />
					<!-- no longer required, using html5/CSS3 flex-box, see .../opac/imageBrowseForm.php for example -->
					<!--label for="item_columns"><?php echo T("Photo Columns"); ?></label>
					<input type="number" id="item_columns" name="item_columns" maxlength="17" value="5" required aria-required  />
					<br /-->
					<label for="thumbnail_width"><?php echo T("Photo Width"); ?></label>
					<input type="number" id="thumbnail_width" name="thumbnail_width" maxlength="19" value="100" required aria-required />(mm)
					<br />
					<label for="thumbnail_height"><?php echo T("Photo Height"); ?></label>
					<input type="number" id="thumbnail_height" name="thumbnail_height" maxlength="19" value="120" required aria-required />(mm)
					<br />
					<label for="thumbnail_rotation"><?php echo T("Photo Rotation"); ?></label>
					<input type="number" id="thumbnail_rotation" name="thumbnail_rotation" maxlength="19" value="0" required aria-required />(deg)
					<br />
					<input type="button" id="fotoTestBtn" value="Test" />
				</div>
				<div id="opacPage" class="block">
					<label for="opac_url"><?php echo T("OPAC URL"); ?></label>
					<input type="text" id="opac_url" name="opac_url" size="17" maxlength="33" />
                    <br />
                    <label for="opac_site_mode"><?php echo T("OpacUserSelectsSite"); ?></label>
                    <input type="checkbox" id="opac_site_mode" name="opac_site_mode" value="Y" />
				</div>

				<hr>
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
