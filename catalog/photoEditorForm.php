<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
?>
	<h3 id="fotoHdr"><?php echo T("PhotoEditor"); ?></h3>
	<h5 id="reqdNote">*<?php echo T("Required note"); ?></h5>

	<fieldset>
		<legend id="fotoEdLegend"></legend>

		<!-- to reduce annoyance, only load video components if wanted-->
		<?php if ($_SESSION['show_item_photos'] == 'Y') { ?>
		<div id="fotoDiv" >
			<!-- video element will be inserted here when JS is activated -->
	  	    <video id="video" preload="auto" />
		 	<canvas id="canvasIn" />
		</div>
		<?php } ?>

		<div id="fotoCntlDiv">
			<form role="form" id="fotoForm">
				<fieldset class="inline">
			 		<canvas id="canvasOut"  />
				</fieldset>
				<fieldset class="inline">
                    <!-- source choices -->
					<fieldset id="fotoSources">
						<legend><?php echo T("Select an image Source"); ?></legend>
						<label for="useCapture"><?php echo T("Camera"); ?></label>
						  <input type="radio" id="useCapture" name="imgSrce" value="cam" checked class="fotoSrceBtns" \>
						<label for="useBrowse">  <?php echo T("Browse"); ?></label>
						  <input type="radio" id="useBrowse" name="imgSrce" value="brw" class="fotoSrceBtns" \>
					</fieldset>
                    <br />

                    <!-- action controls -->
                    <span id="fotoControls">
					   <input type="button" id="capture" name="capture" value="<?php echo T("Take Photograph"); ?>" />
					   <input type="file" id="browse" name="browse" accept="image/png image/jpg" />
                    </span>
                    <br />

                    <!-- user information -->
                    <span id="fotoInfo">
					   <label for="fotoFolder" class="note italic" ><?php echo T("StoreAt"); ?>:</label>
					   <input id="fotoFolder" class="note italic" READONLY value="<?php echo '../photos/'.T("filename").'.jpg'; ?>" />
					   <br />
					   <label for="fotoName"><?php echo T("FileName"); ?>:</label>
					   <input type="text" id="fotoName" name="url" size="32"
							 pattern="(.*?)(\.)(jpg|jpeg|png)$" required aria-required="true"
							 title="<?php echo T("OnlyJpgOrPngFiles"); ?>" />
							 <span class="reqd">*</span>
                    </span>
				</fieldset>

				<input type="hidden" id="fotoBibid" name="bibid" value="" />
			</form>
		</div>
	</fieldset>
<?php
	require_once ("../shared/jsLibJs.php");
	require_once(REL(__FILE__,'../catalog/photoEditorJs.php'));
?>
