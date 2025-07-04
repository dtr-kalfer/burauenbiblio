<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");

	$tab = "circulation";
	$nav = "searchform";
	$restrictToMbrAuth = TRUE;
	if ($_SESSION['mbrBarcode_flg'] == 'Y') {
		$focus_form_name = "barCdSrchForm";
		$focus_form_field = "searchByBarcd";
	} else {
		$focus_form_name = "nameSrchForm";
		$focus_form_field = "nameFrag";
	}

	if ($tab != "opac") {
		require_once(REL(__FILE__, "../shared/logincheck.php"));
	}
	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));

?>
<h3><?php echo T("Current Members"); ?></h3>

<!-- ------------------------------------------------------------------------ -->
<div id="msgDiv"><fieldSet id="userMsg"></fieldset></div>

<!-- ------------------------------------------------------------------------ -->
<p id="errSpace" class="error"></p>
<div id="searchDiv">
	<form role="form" id="barCdSrchForm" name="barCdSrchForm" action="">
	<fieldset>
	<legend><?php echo T("Find Member by Code"); ?></legend>
	<table>
		<tr>
			<td nowrap="true">
				<label for="searchByBarcd"><?php echo T("Library Card Number");?>:</label>
				<!-- This oninput pattern match will force text-input to accept numbers only with 6 digits F. Tumulak -->
				<input type="text" oninput="this.value = this.value.replace(/\D/g, '').slice(0, 8)" pattern="\d*" id="searchByBarcd" name="searchByBarcd" size="20" />
				<input type="button" id="barCdSrchBtn" value="<?php echo T("Search"); ?>" />
			</td>
		</tr>
	</table>
	</fieldset>
	</form>
	<hr>
	<form role="form" id="nameSrchForm" name="nameSrchForm" action="">
	<fieldset>
	<legend><?php echo T("Find Member by Surname"); ?></legend>
	<table>
		<tr>
			<td nowrap="true">
				<label for="nameFrag"><?php echo T("Last Name Contains");?>:</label>
				<input type="text" id="nameFrag" name="nameFrag" size="20" maxlength="50" />
				<input type="button" id="nameSrchBtn" value="<?php echo T("Search"); ?>" />
			</td>
		</tr>
	</table>
	</fieldset>
	</form>
	<hr>
	<h3><?php echo T("Add a new member"); ?></h3>
	<fieldset>
		<input type="button" id="addNewMbrBtn" value="<?php echo T("Add New Member"); ?>" />
	</fieldset>
</div>

<!-- ------------------------------------------------------------------------ -->
<div id="mbrDiv">
	<input type="button" class="gobkMbrBtn" value="<?php echo T("Go Back"); ?>" />
	<fieldset id="identity">
		<legend align="center" ><?php echo T("Member Information");?></legend>
		<label for="mbrName"><?php echo T("Name");?>:</label>
		<input type="text" readonly="readonly" id="mbrName" />
		<input type="button" value="<?php echo T("Details");?>" id="mbrDetlBtn" />
		<input type="button" value="<?php echo T("Account");?>" id="mbrAcntBtn" />
		<input type="button" value="<?php echo T("History");?>" id="mbrHistBtn" />
		<input type="button" value="<?php echo T("Help!");?>" id="mbrHelpBtn" />
		<br />
		<label for="mbrSite"><?php echo T("Site");?>:</label>
		<input type="text" readonly id="mbrSite" />
		<br />
		<label for="MbrCardNo"><?php echo T("Card Number");?>:</label>
		<input type="number" readonly id="mbrCardNo" />
		<br />
		<label for="newmemberType"><?php echo T("Member Type");?>:</label>
		<input type="text" readonly id="newmemberType" />
		<label for="loan_Allotment"><?php echo T("Loan Allotment");?>:</label>
		<input type="text" id="loan_Allotment" size="3" oninput="this.value = this.value.replace(/\D/g, '').slice(0, 3)" pattern="\d*" />
	</fieldset>
<!-- -->
	<br>
	<hr>
	<fieldset id="newLoans">
		<legend align="center" for="ckoutBarcd"><?php echo T("Check Out"); ?></legend>
		<p style="text-align: center; ">Date Today:  <?php echo date("D, M j, Y"); ?></p><br>
		<label><?php echo T("Barcode Number");?>:</label>
		<input type="text" id="ckoutBarcd" size="20" oninput="this.value = this.value.replace(/\D/g, '').slice(0, 13)" pattern="\d*" />
		<input type="button" value="<?php echo T("Check Out");?>" id="chkOutBtn" />
		<p class="error" id="chkOutMsg"></p>
	</fieldset>
<!-- -->
	
	<fieldset id="onLoan" >
		<legend align="center" ><?php echo T("Items Currently Checked Out");?></legend>
		<table id="chkOutList">
			<thead>
			<tr>
				<th class="center"><?php echo T("Checked Out");?></th>
				<th class="center"><?php echo T("Media");?></th>
				<th class="center"><?php echo T("Barcode");?></th>
				<th class="center"><?php echo T("Title");?></th>
				<th class="center"><?php echo T("Due Date");?></th>
				<th class="center"><?php echo T("Days Late");?></th>
				<th class="center"><?php echo T("AmountOwed");?></th>
			</tr>
			</thead>
			<tbody class="striped"></tbody>
			<tfoot class="topBorder">
			<tr>
				<th><?php echo T("Limit");?></th>
				<td style="text-align: center;" id="maxFine"></td>
				<td colspan="3">&nbsp</td>
        <th><?php echo T("Total");?></th>
				<td style="text-align: center;" id="ttlOwed" class="number"></td>
			</tr>
      </tfoot>
		</table>
	</fieldset>
<!-- -->

	<br>
	<hr>

	<!-- -->
	<br>
	
	<fieldset id="newHolds">
		<legend align="center" ><?php echo T("Place a booking");?></legend>
			<label for="holdBarcd"><?php echo T("Barcode Number");?></label>
			<input type="text" id="holdBarcd" size="20" oninput="this.value = this.value.replace(/\D/g, '').slice(0, 13)" pattern="\d*" />
			<!--a href="javascript:popSecondaryLarge('../opac/index.php?lookup=Y')"Search</a-->
			<input type="hidden" name="mbrid" value="" />
			<input type="hidden" name="classification" value="" />
			<input type="button" value="<?php echo T("Make booking");?>" id="holdBtn" />
	</fieldset>
<!-- -->
	<fieldset id="onHold">
		<legend align="center" ><?php echo T("Booking/s requested by this member");?></legend>
		<table id="holdList">
		<thead>
			<tr>
				<td>&nbsp;</td>
				<th class="center"><?php echo T("On Hold");?></th>
				<th class="center"><?php echo T("Barcode");?></th>
				<th class="center"><?php echo T("Title");?></th>
				<th class="center"><?php echo T("Status");?></th>
				<th class="center"><?php echo T("Due Back");?></th>
			</tr>
		</thead>
		<tbody class="striped"></tbody>
		</table>
	</fieldset>
	<input type="button" class="gobkMbrBtn" value="<?php echo T("Go Back"); ?>" />
</div>

<!-- ------------------------------------------------------------------------ -->
<div id="listDiv">
	<h4 id="searchResultsTitle" ><?php echo T("SearchResults"); ?></h4>
	<div id="results_found">
	</div>
	<table>
	<thead>
	<tr>
		<td><input type="button" class="gobkBtn" value="<?php echo T("Go Back"); ?>" /></td>
		<td width="80%" align="right">
			<input type="button" class="goPrevBtn PgBtn" value="<?php echo T("Previous Page"); ?>">
			<span class="rsltQuan"></span>
			<input type="button" class="goNextBtn PgBtn" value="<?php echo T("Next Page"); ?>">
		</td>
	</tr>
	</thead>
	<tbody>
	<tr>
	  <td colspan="3">
			<fieldset>
				<table id="listTbl" width="100%">
					<tbody id="srchRslts" class="striped"><tr><td>filled by server</td></tr></tbody>
				</table>
			</fieldset>
		</td>
	</tr>
	</tbody>
	<tfoot>
	<tr>
		<td><input type="button" class="gobkBtn" value="<?php echo T("Go Back"); ?>" /></td>
		<td width="80%" align="right">
			<input type="button" class="goPrevBtn PgBtn" value="<?php echo T("Previous Page"); ?>">
			<span class="rsltQuan"></span>
			<input type="button" class="goNextBtn PgBtn" value="<?php echo T("Next Page"); ?>">
		</td>
	</tr>
	</tfoot>
	</table>
</div>

<!-- ------------------------------------------------------------------------ -->
<div id="editDiv">
	<form role="form" id="editForm" name="editForm" >
	<p> Fields marked with * are required.</p>
	<fieldset>
		<legend id="editHdr"></legend>
		<table>
		<tbody>
			<!-- all input fields are constructed empty by server -->
			<?php include(REL(__FILE__, "../circ/mbrFields.php")); ?>	
		</tbody>	
		<tfoot>
			<tr>
				<td colspan="2">
					<input type="hidden" id="editMode" name="mode" value="">
					<input type="hidden" id="mbrid" name="mbrid" value="">
				</td>
			</tr>
			<tr>
				<td colspan="2" cl>
					<input type="submit" id="addMbrBtn" value="<?php echo T("Add"); ?>" />
					<input type="submit" id="updtMbrBtn" value="<?php echo T("Update"); ?>" />
					<input type="button" class="gobkUpdtBtn" value="<?php echo T("Go Back"); ?>" />
					<input type="button" class="gobkNewBtn" value="<?php echo T("Go Back"); ?>" />
					
					<?php if (isset($_SESSION["hasReportsAuth"]) && $_SESSION["hasReportsAuth"]): ?>
						<input type="button" id="deltMbrBtn" value="<?php echo T("Delete"); ?>" />
					<?php endif; ?>
					
				</td>
			</tr>
		</tfoot>
	</table>
	</fieldset>
	</form>
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
<div id="copyEditorDiv">
	<?php require_once(REL(__FILE__,"../catalog/copyEditorForm.php"));?>
</div>

<!-- ------------------------------------------------------------------------ -->
<div id="acntDiv">
	<input type="button" class="gobkAcntBtn" value="<?php echo T("Go Back"); ?>" />
	<fieldset>
		<legend><?php echo T("Add a Transaction"); ?></legend>
		<form role="form" id="acntForm">
			<label for="transaction_type_cd"><?php echo T("Transaction Type"); ?>:</label>
			<select id="transaction_type_cd" name="transaction_type_cd"></select>
			<br />
			<label for="description"><?php echo T("Description"); ?>:</label>
			<input type="text" required size="40" maxlength="128" id="description" name="description" />
			<br />
			<label for="amount"><?php echo T("Amount"); ?>:</label>
			<input type="number" required size="12" id="amount" name="amount" 
						 pattern="^\d+\.\d{2}$" 
						 title="<?php echo T("A valid money amount");?>" />
			<br />
			<input type="hidden" name="mode" value="addAcntTrans" />
			<input type="hidden" id="acntMbrid" name="mbrid" value="" />
			<input type="submit" id="addTransBtn" value="<?php echo T("Add New"); ?>" />
		</form>
	</fieldset>
	
	<fieldset>
		<legend><?php echo T("Transaction Activity"); ?></legend>
		<table id="tranList">
			<thead>
			<tr>
				<th>&nbsp</th>
				<th><?php echo T("Date"); ?></th>
				<th><?php echo T("Trans Type"); ?></th>
				<th><?php echo T("Description"); ?></th>
				<th><?php echo T("Amount"); ?></th>
				<th><?php echo T("Balance"); ?></th>
			</tr>
			</thead>
			<tbody class="striped"></tbody>
		</table>
	</fieldset>
	<input type="button" class="gobkAcntBtn" value="<?php echo T("Go Back"); ?>" />
</div>

<!-- ------------------------------------------------------------------------ -->
<div id="histDiv">
	<input type="button" class="gobkHistBtn" value="<?php echo T("Go Back"); ?>" />
	<fieldset>
		<legend><?php echo T("Checkout History"); ?></legend>
		<table id="histList">
			<thead>
			<tr>
				<th><?php echo T("Item"); ?></th>
				<th><?php echo T("In/Out"); ?></th>
				<th><?php echo T("Date"); ?></th>
			</tr>
			</thead>
			<tbody class="striped"></tbody>
		</table>
	</fieldset>
	<input type="button" class="gobkHistBtn" value="<?php echo T("Go Back"); ?>" />
</div>

<!-- ------------------------------------------------------------------------ -->

<div id="helpDiv" style="width: 60vw;">
	<input type="button" class="gobkHistBtn" value="<?php echo T("Go Back"); ?>" />
  <h3>📚 Checkout Policy & Due Date Guide</h3>

  <p><strong>How Due Dates Are Calculated:</strong><br>
  The system determines the <em>due date</em> based on the <strong>greater</strong> value between:</p>
  <ul>
    <li><strong>Default Collection Loan</strong> – the standard loan duration (in days) based on the material's collection or type (e.g., Non-fiction, Reference). <b>Late rate fee</b> is the penalty amount per day overdue.</li>
		<a href="#"><img src="../images/view_collection_sample2.webp" alt="" title="" /></a>
    <li><strong>Loan Allotment</strong> – the maximum loan period (in days) granted to a borrower depending on their <strong>member type</strong> (e.g., Student, Faculty). <b>Limit (Max Fine)</b> -> If the overdue amount exceeds this limit, the patron's checkout feature gets disabled.</li>
		<a href="#"><img src="../images/loan_allotment_sample.webp" alt="" title="" /></a>
  </ul>

  <p><em>Example:</em> A "Non-Fiction" book might have a 1-day default loan, but if a Faculty member is allowed 5 days, the due date will be based on the <strong>5-day Loan Allotment</strong>.</p>

  <p><strong>Minimum Due Date:</strong><br>
  A non-fiction checked out <em>today</em> will have a minimum due date of <strong>+1 day</strong> (i.e., return expected by tomorrow).</p>

  <p><strong>Closed Days Handling:</strong><br>
  Due dates strictly <em>follow the Calendar Menu</em>:<br>
  If a calculated due date falls on a <strong>closed day</strong> (marked with a ❌ in the calendar), the system automatically moves it to the <strong>next open day</strong>.</p>

  <p><strong>Barcode Input Requirements:</strong><br>
  Barcodes must be <strong>numeric only</strong> and its length is 13-digit.<br>
  Letters and special characters (e.g., A, #, -) are <strong>not accepted</strong>.<br>
  You may manually type the barcode if the printed one is damaged — the system will automatically handle zero padding if needed, i.e. 1234 --> 0000000001234 </p>
</div>

<?php
  require_once(REL(__FILE__,'../shared/footer.php'));
	
	//include_once(REL(__FILE__,'./mbrEditorJs.php'));
	include_once(REL(__FILE__,'../catalog/itemDisplayJs.php'));
	include_once(REL(__FILE__,'../circ/memberJs.php'));
?>	

</body>
</html>
