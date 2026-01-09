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
<style>
/* -------------------------------
   Mobile-first DOI Search
-------------------------------- */

#entry {
    width: 95%;
    max-width: 900px;
    margin: auto;
}

#srchArea {
    border-radius: 8px;
    padding: 1rem;
}

/* Text content */
#srchArea p {
    line-height: 1.5;
    margin-bottom: 0.75rem;
}

/* Form layout */
#doiForm {
    display: flex;
    flex-direction: column;
    gap: 0.6rem;
    margin-top: 1rem;
    align-items: center;
}

/* Input */
#doiCd {
    width: 95%;
    max-width: 100%;
    padding: 0.55rem;
    font-size: 1rem;
    text-align: center;
    border-radius: 6px;
}

/* Button */
#srchBtn {
    width: 95%;
    padding: 0.6rem;
    font-size: 1rem;
    border: none;
    border-radius: 6px;
    background: #0078d7;
    color: white;
    cursor: pointer;
}

/* DOI highlight readability */
#srchArea span {
    padding: 0.1rem 0.3rem;
    border-radius: 4px;
}
</style>
	<h4 class="title"><?php echo T("doiSearch"); ?></h4>
	<section id="entry">
		<fieldset id="srchArea">
		
			<p>	A <b>DOI (Digital Object Identifier)</b> is like a permanent ID for academic papers, journal articles, e-books, and other documents	published on the internet. </p><br>
			<p>The DOI Search opens a new tab which redirects to the URL equivalent of the DOI.</p>
			<form role="form" id="doiForm">
			<label for="doiCd">Paste any DOI here to locate the official article.</label><br />
			<input size="50" type="text" id="doiCd" name="doiCd" required \><br />
		  <input type="submit" id="srchBtn" value="<?php echo T("Search"); ?>" />
			</form>
			<br>
			<h3>Here are some DOI examples found on academic references:</h3><br>
			<p>Benson, O. (2023). Design, Fabrication, and Performance <br>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Evaluation of a Petrol-Driven Refrigerating System <br>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;for Effective Vaccine Storage in Remote Areas. <br>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;doi:<span style="background: yellow;">10.13140/RG.2.2.34185.83040</span></p>
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
