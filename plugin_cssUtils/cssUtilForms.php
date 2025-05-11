<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
 
  	$cache = NULL;
	require_once("../shared/common.php");

  	$tab = "tools";
  	$nav = "cssUtil";
  	//$focus_form_name = "utilForm";
  	//$focus_form_field = "collSet";
	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));

?>

	<h1 id="pageHdr" class="title"><?php echo T("cssUtilities"); ?></h1>
	<h3 id="pageHdr" class="title"><?php echo T("ForDeveloperUse"); ?></h3>

	<section id="entry">
		<fieldset id="orfnArea">
		<legend><?php echo T("Check4Unused"); ?></legend>
			<p class="note">
			This modeule is intended style classes that are unused, or undefined.
			</p>
		  <input type="button" id="orfnChkBtn" value="<?php echo T("Scan"); ?>" />
		</fieldset> 
	</section>
	
	<section id="rsltsArea">
		<fieldset id="rslts">
		</fieldset>
	</section>
	
	<div id="msgDiv"><fieldSet id="userMsg"></fieldset></div>

<?php
  	require_once(REL(__FILE__,'../shared/footer.php'));
  	require_once(REL(__FILE__,'../plugin_cssUtils/cssUtilJs.php'));
?>	
</body>
</html>
