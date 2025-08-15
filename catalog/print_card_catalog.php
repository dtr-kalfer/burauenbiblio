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
    i.e. FIL 332.10959 C11 2020 and also fill up <input type="button" name="other details" id="toggle_display" value="other details" /> for every biblio records.</p><br>

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
		
		<div id="sample_card_catalog">
		<h3>Sample Printout</h3>
    <img id="" src="../images/card_catalog_demo_sample.webp" alt="sample card catalog" />
		</div>
		
		<div id="other_details" style="display: none;">
			<h3>Completing these info. will fill out the details of your Printed Card Catalog.</h3>
			<table border="1" cellpadding="5" cellspacing="0">
			<thead>
				<tr>
					<th>MARC Tag</th>
					<th>Description</th>
				</tr>
			</thead>
			<tbody>
				<tr><td>099$a</td><td>Call Number</td></tr>
				<tr><td>100$a</td><td>Author</td></tr>
				<tr><td>245$a</td><td>Title</td></tr>
				<tr><td>245$b</td><td>Subtitle</td></tr>
				<tr><td>700$a</td><td>Additional contributors</td></tr>
				<tr><td>245$c</td><td>Statement of Responsibility</td></tr>
				<tr><td>260$a</td><td>Place of Publication</td></tr>
				<tr><td>260$b</td><td>Publisher</td></tr>
				<tr><td>260$c</td><td>Date of Publication</td></tr>
				<tr><td>300$a</td><td>Extent</td></tr>
				<tr><td>300$b</td><td>Other physical details</td></tr>
				<tr><td>300$c</td><td>Dimensions</td></tr>
				<tr><td>504$a</td><td>Bibliography, etc. note</td></tr>
				<tr><td>020$a</td><td>ISBN</td></tr>
				<tr><td>650$a</td><td>Subject: Topical</td></tr>
				<tr><td>650$b</td><td>Subject: Personal</td></tr>
				<tr><td>650$c</td><td>Subject: Corporate</td></tr>
				<tr><td>650$d</td><td>Subject: Geographic</td></tr>
			</tbody>
		</table>
		</div>

  </fieldset>
</div>

<?php
require_once(REL(__FILE__, '../shared/footer.php'));
?>
</body>
<script>
document.getElementById('toggle_display').addEventListener('click', function() {
    const sample = document.getElementById('sample_card_catalog');
    const details = document.getElementById('other_details');

    if (sample.style.display !== 'none') {
        sample.style.display = 'none';
        details.style.display = 'block';
    } else {
        sample.style.display = 'block';
        details.style.display = 'none';
    }
});
</script>
</html>
