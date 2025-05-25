<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
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
          <form role="form" id="generic_form" name="generic_form" >
		      </form>
    		  <input type="button" id="generic_btn" value="<?php echo T("Submit");?>" />
      </fieldset>
	</div>
	<!-- ------------------------------------------------------------------------ -->
<?php
  require_once(REL(__FILE__,'../shared/footer.php'));
	require_once(REL(__FILE__, "../catalog/bulkDelJs.php"));
?>	
</body>
</html>
