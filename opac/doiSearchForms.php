<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
 
  	$cache = NULL;
  	require_once("../shared/common.php");

  $tab = "cataloging";
	$nav = "doiSearch";
	//$tab = strToLower($_REQUEST['tab']);
  	$focus_form_name = "doiForm";
  	$focus_form_field = "doiCd";
	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));

?>

	<h4  class="title"><?php echo T("doiSearch"); ?></h4>
	<section id="entry">
		<fieldset id="srchArea">
		<legend style="border: 1px solid black;"><?php echo T("Search4DocumentInfo"); ?></legend>
			<p class="note">
			A DOI, or Digital Object Identifier, is a string of numbers, <br>
			letters and symbols used to uniquely identify an article or  <br>
			document, and to provide it with a permanent web address <br> (URL)
			</p>
			<form role="form" id="doiForm">
			<label for="doiCd"><?php echo T("EnterDOI2Resolve"); ?></label><br />
			<input type="text" id="doiCd" name="doiCd" required \><br />
		  <input type="submit" id="srchBtn" value="<?php echo T("Search"); ?>" />
			</form>
		</fieldset> 
	</section>
	
	<section id="rsltsArea">
		<fieldset id="rslts">
		</fieldset>
	</section>
	
	<div id="msgDiv"><fieldSet id="userMsg"></fieldset></div>

<?php
  require_once(REL(__FILE__,'../shared/footer.php'));
  require_once(REL(__FILE__,'../opac/doiSearchJs.php'));
?>	

</body>
</html>
