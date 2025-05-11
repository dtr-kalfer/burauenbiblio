<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	$cache = NULL;
	require_once("../shared/common.php");
	require_once(REL(__FILE__, "../functions/inputFuncs.php"));

//	$tab = "working";
//	if (isset($_REQUEST["tab"])) {
  		$tab = strToLower($_REQUEST[tab]);
//	}
//	$_REQUEST['tab'] = $tab;


	$nav = "imageBrowseForm";
	$focus_form_name = "";
	$focus_form_field = "";
	if ($tab != "opac") {
		require_once(REL(__FILE__, "../shared/logincheck.php"));
	}

	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>'Cover Photos'));
?>

	<p id="errSpace" class="error"></p>
	<input type="hidden" id="tab" value="<?php echo $tab;?>" />

<!-- ------------------------------------------------------------------------ -->
	<div id="fotoDiv">
		<div class="cntlArea">
			<div class="btnBox">
				<ul class="btnRow">
					<li><button class="prevBtn"><?php echo T("Prev");?></button></li>
					<li><button class="nextBtn"><?php echo T("Next");?></button></li>
				</ul>
			</div>
			<div class="countBox"> <p> foto count goes here </p> </div>
			<div class="sortBox">
        <label= for="orderBy">Sort By:</label>
				<select id="orderBy">
					<option value="title">Title</option>
					<option value="author" SELECTED>Author</option>
					<option value="callno">Call No.</option>
				</select>
			</div>
		</div>

		<fieldset id="gallery">
			<!--table id="fotos"> </table-->
			<ul id="fotos" class="grid-display" > </ul>
		</fieldset>

		<div class="cntlArea">
			<div class="nmbrbox">
				<ul class="btnRow">
					<li><button class="prevBtn"><?php echo T("Prev");?></button></li>
					<li><button class="nextBtn"><?php echo T("Next");?></button></li>
				</ul>
			</div>
		</div>

	</div>

<!-- ------------------------------------------------------------------------ -->
<div id="biblioDiv">
	<ul class="btnRow">
		<li><input type="button" class="gobkBiblioBtn" value="<?php echo T("Go Back"); ?>" /></li>
	</ul>

		<?php include(REL(__FILE__,"../catalog/itemDisplayForm.php")); ?>

	<ul class="btnRow">
		<li><input type="button" class="gobkBiblioBtn" value="<?php echo T("Go Back"); ?>"></li>
	</ul>
</div>

<!-- ------------------------------------------------------------------------ -->
<div id="workDiv">
	<img id="img-dummy" src="../images/shim.gif" class="biblioImage" />
</div>
<!-- ------------------------------------------------------------------------ -->
<?php
  require_once(REL(__FILE__,'../shared/footer.php'));
	include "../opac/imageBrowseJs.php";
?>
</body>
</html>
