<?php

	require_once("../shared/common.php");
	$tab = "cataloging";
	$nav = "print_card_catalog";	
	require_once(REL(__FILE__, "../shared/logincheck.php"));
	require_once(REL(__FILE__, "../functions/inputFuncs.php"));
	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));
?>
	<h3><?php echo T("Print Card Catalog"); ?></h3>
	<?php //print_r($_SESSION); // for debugging ?>
	<div id="crntMbrDiv_new">To be filled by server --F.T.</div>
	<p id="errSpace" class="error">to be filled by server</p>
	<!-- ------------------------------------------------------------------------ -->
	<div id="bulkDel_formDiv" style="display: block;">
      <fieldset>
          <p>To get better result similar to example, use space between call number <br>
					i.e. FIL 332.10959 C11 2020 and also fill up other details on biblio records.</p>
          <br />
          <!-- -------------------------->
						<form name="print_bibid" method="post" action="print_bibid.php">
						<p>Each author catalog results to 1/2 Letter page (Portrait), to complete 
						entire page,<br> use <b>TWO</b> bibid entries</p><br>
						<p><b>Enter Bibid ID# below:</b></p><br>
						<input type="text"  oninput="this.value = this.value.replace(/\D/g, '').slice(0, 5)" pattern="\d*" name="bibid_fpdf" value="" id="" maxlength="5" required />
						<input type="text"  oninput="this.value = this.value.replace(/\D/g, '').slice(0, 5)" pattern="\d*" name="bibid_fpdf2" value="" id="" maxlength="5" required />
						<input type="submit" value=" Print Bibid ID " name="print_bibid" onclick="return confirm('Print Bibid ID?');"/>
						</form>
					<img src="../images/card_catalog_demo_sample.webp" alt="sample card catalog" />
					<!-- -------------------------->
      </fieldset>
	</div>
	<!-- ------------------------------------------------------------------------ -->
<?php
  require_once(REL(__FILE__,'../shared/footer.php'));
?>	
</body>
</html>
