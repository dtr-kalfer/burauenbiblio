<?php
require_once("../shared/common.php");
$tab = "cataloging";
$nav = "print_card_catalog";	

// validate if logged in --F.Tumulak
require_once(REL(__FILE__, "../shared/logincheck.php"));
require_once(REL(__FILE__, "../functions/inputFuncs.php"));

Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));

?>

<!-- HTMX script (local or CDN) -->
<script src="../htmx_cdn/htmx.min.js"></script>

<h3><?php echo T("Print Card Catalog"); ?></h3>

<h3 id="crntMbrDiv_new" style="background-color: skyblue;" >...</h3>
<p id="errSpace" class="error"></p>

<div id="bulkDel_formDiv" style="display: block;">
  <fieldset>
    <p>To get better result similar to example, use space between call number <br>
    i.e. FIL 332.10959 C11 2020 and also fill up other details on biblio records.</p><br>

    <form name="print_bibid" method="post" 
          hx-post="print_bibid.php"
          hx-target="#crntMbrDiv_new"
          hx-swap="innerHTML"
          onsubmit="return confirm('Print Bibid ID?');">

      <p><b>Enter two BibID below:</b></p><br>
			<label for="bibid_fpdf">bibID #1</label>
      <input type="text" name="bibid_fpdf"
             oninput="this.value = this.value.replace(/\D/g, '').slice(0, 5)"
             pattern="\d*" maxlength="5" required />
			<label for="bibid_fpdf2">bibID #2</label>
      <input type="text" name="bibid_fpdf2"
             oninput="this.value = this.value.replace(/\D/g, '').slice(0, 5)"
             pattern="\d*" maxlength="5" required />
						 
			<input type="hidden" name="guard_token_key" value="<?php echo $_SESSION['guard_token_key']; ?>">
      
			<input type="submit" value="Print Bibid ID" />
    </form>
		<h3>Sample Printout</i></h3>
    <img src="../images/card_catalog_demo_sample.webp" alt="sample card catalog" />
  </fieldset>
</div>

<?php
require_once(REL(__FILE__, '../shared/footer.php'));
?>
</body>
</html>
