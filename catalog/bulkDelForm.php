<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");
	//$tab = "cataloging"; 
	//$nav = "bulk_delete";
	
	// move the bulk delete to a new home --F.Tumulak
	$tab = "admin";
	$nav = "bulk_delete";	
	
//	$restrictInDemo = true;
	require_once(REL(__FILE__, "../shared/logincheck.php"));
	require_once(REL(__FILE__, "../functions/inputFuncs.php"));

	$focus_form_name = 'bulk_delete';
	$focus_form_field = 'barcodes';

	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));
?>
	<h3><?php echo T("Bulk Delete"); ?></h3>
	<?php //print_r($_SESSION); // for debugging ?>

	<div id="crntMbrDiv_new">To be filled by server --F.T.</div>
	<p id="errSpace" class="error">to be filled by server</p>

	<!-- ------------------------------------------------------------------------ -->
	<div id="bulkDel_formDiv">
      <fieldset>
          <p class="note">This will delete Biblio ID and all its copies! Be careful</p>
          <br />
          <form role="form" id="bulkDel_form" name="bulkDel_form" >
        	    <label for="del_items">
        	     </label>
        	     <label for="byBibid">
        	     <!--?php echo inputfield('checkbox','byBibidd','1','',($vars['byBibidd']?'1':'')); ?-->
               <input type="radio" id=byBibid" name="bulkBarcd" value="1" checked />
        		   <?php echo T("Delete by Biblio Id") ?>
               </label>
        	     <!--?php //echo inputfield('hidden','posted','1'); ?-->
        	     <?php echo inputfield('textarea','bibids','',array('rows'=>'3'),H(isset($vars['bibids']) && $vars['bibids'])); ?>
        	     <br /><br />
		      </form>

    		  <input type="button" id="bulkDel_btn" value="<?php echo T("Submit");?>" />
      </fieldset>
	</div>

<?php
  require_once(REL(__FILE__,'../shared/footer.php'));
	
	require_once(REL(__FILE__, "../catalog/bulkDelJs.php"));
?>	
</body>
</html>
