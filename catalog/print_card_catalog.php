<?php
	/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
	 * See the file COPYRIGHT.html for more details.
	 * This is an add-on feature for Openbiblio developed by Ferdinand Tumulak
	 * for bibid card catalog printing.
	 * it can still be further improved using PDO or use Openbiblio built-in class functions if you are up to it.
	 */
	require_once("../shared/common.php");
	$tab = "admin";
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
	<div id="bulkDel_formDiv">
      <fieldset>
          <p class="note">Place a notice here!</p>
          <br />
          <!-- -------------------------->
						<form name="print_bibid" method="post" action="print_bibid.php">
						<p>Each author catalog results to 1/2 Letter page (Portrait), to complete entire page, use TWO bibid entries<br />
						Font type: Courier, size: 10pt bold <br />
						Enter Bibid ID# below:</p>
						<input type="text"  oninput="this.value = this.value.replace(/\D/g, '').slice(0, 5)" pattern="\d*" name="bibid_fpdf" value="" id="" maxlength="5" required />
						<input type="text"  oninput="this.value = this.value.replace(/\D/g, '').slice(0, 5)" pattern="\d*" name="bibid_fpdf2" value="" id="" maxlength="5" required />
						<input type="submit" value=" Print Bibid ID " name="print_bibid" onclick="return confirm('Print Bibid ID?');"/>
						</form>
					<!-- -------------------------->
      </fieldset>
	</div>
	<!-- ------------------------------------------------------------------------ -->
<?php
  require_once(REL(__FILE__,'../shared/footer.php'));
	require_once(REL(__FILE__, "../catalog/bulkDelJs.php"));
?>	
</body>
</html>
