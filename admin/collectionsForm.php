<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
	require_once("../shared/common.php");
	$tab = "admin";
	$nav = "collections";
	require_once(REL(__FILE__, "../shared/logincheck.php"));
	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));
?>
    <h3><?php echo T("Collections"); ?></h3>
    <div id="listDiv" style="display: none;">
        <form role="form" id="showForm" name="showForm">
        <fieldset>
        <table id="showList">
        <thead>
        	<tr>
       	    <th colspan="1">&nbsp;</th>
        		<th valign="top"><?php echo T("Code"); ?></th>
        		<th valign="top"><?php echo T("Description"); ?></th>
        		<th valign="top"><?php echo T("Type"); ?></th>
        		<th valign="top"><?php echo T("Item Count"); ?></th>
        		<th valign="top"><?php echo T("Default"); ?></th>
        	</tr>
        </thead>
        <tbody class="striped">
        	<tr><td colspan="4"><?php echo T("No collections have been defined."); ?> </td></tr>
        </tbody>
        </table>
        </fieldset>
        </form>
    </div>
	<hr />
<?php
  require_once(REL(__FILE__,'../shared/footer.php'));
	
	//require_once(REL(__FILE__, "../classes/AdminJs.php"));
	//require_once(REL(__FILE__, "collectionsJs.php"));
	require_once(REL(__FILE__, "../classes/JSAdmin.php"));
	require_once(REL(__FILE__, "../admin/collectionsJs6.php"));
?>
</body>
</html>
