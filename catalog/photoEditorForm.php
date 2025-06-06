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
		
		<div id="fotoDiv" hidden>
			<!-- video element will be inserted here when JS is activated -->
	  	    <video id="video" preload="auto" />
		 	<canvas id="canvasIn" /></canvas>
		</div>
		

		<div id="fotoCntlDiv">
			<form role="form" id="fotoForm">
				<fieldset class="inline">
			 		<canvas id="canvasOut"  /></canvas>
				</fieldset>
				<fieldset class="inline">
                    <!-- source choices -->
					<fieldset id="fotoSources">
						<legend><?php echo T("Select an image Source"); ?></legend>
						<p>Note: Press CTRL+F5 to refresh image into latest update</p>
							<!-- <label for="useCapture"><?php //echo T("Camera"); ?></label> -->
							<!-- <input checked type="radio" id="useCapture" name="imgSrce" value="cam" class="fotoSrceBtns" \> -->
							<!-- <label for="useBrowse">  <?php //echo T("Browse"); ?></label> -->
						
						  <input checked="checked" type="hidden" id="useBrowse" name="imgSrce" value="brw" class="fotoSrceBtns" />
					</fieldset>

                    <br />

                    <!-- action controls -->
                    <span id="fotoControls">
										
										<input type="file" id="browse" name="browse" accept="image/png image/jpg" />
                    </span>
                    <br />
                    <!-- user information -->
                    <span id="fotoInfo">
					   <label for="fotoFolder" class="note italic" ><?php echo T("StoreAt"); ?>:</label>
					   <input id="fotoFolder" class="note italic" READONLY value="<?php echo '../photos/'.T("filename").'.jpg'; ?>" />
					   <br />
					   
					   <input type="hidden" id="fotoName" name="url" size="32"
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
