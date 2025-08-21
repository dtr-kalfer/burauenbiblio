<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
  	$cache = NULL;
  	require_once("../shared/common.php");

//  $tab = "user";
	$tab = strToLower($_REQUEST['tab']);
  	$nav = "doiSearch";
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
			<input size="50" type="text" id="doiCd" name="doiCd" required \><br />
		  <input type="submit" id="srchBtn" value="<?php echo T("Search"); ?>" />
			</form>
			<br>
			<p>Here are some more DOI examples:</p><br>
			<p>Benson, O. (2023). Design, Fabrication, and Performance <br>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Evaluation of a Petrol-Driven Refrigerating System <br>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;for Effective Vaccine Storage in Remote Areas. <br>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;doi:<span style="background: yellow;">10.13140/RG.2.2.34185.83040</span></p>
			<br>
			<p>He, M.-G., Song, X.-Z., Liu, H., & Zhang, Y. (2014). <br>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Application of natural refrigerant propane and <br>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;propane/isobutane in large capacity chest freezer. <br>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Applied Thermal Engineering, 70(1), 732–736. <br>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;doi:<span style="background: yellow;">10.1016/j.applthermaleng.2014.05.097</span></p>
			<br>
			<p>Salamanca, C. L. M. A., & Pabilona, L. L. (2024). <br>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Quantitative analysis of coefficient of performance <br>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;of liquefied petroleum gas refrigeration system. <br>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Mindanao Journal of Science and Technology, 22(2). <br>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;doi:<span style="background: yellow;">10.61310/mjst.v22i2.2182</span></p>
			
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
