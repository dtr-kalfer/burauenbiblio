<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details. --F.Tumulak
 */
require_once("../shared/common.php");
$tab = "circulation";
$nav = "tally";	

require_once(REL(__FILE__, "../shared/logincheck.php"));
Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));
?>
<script src="../htmx_cdn/htmx.min.js"></script>
<style>
		.form-container-attendance {
				width: 550px;
		}

		form {
			text-align: center;
		}
		
		p,input,button {
			margin: 10px;
		}
</style>
<section class="form-container-attendance">
		<section class="book-activity">
			<h2><?php echo T("In-House Book Activity Tracker (Daily Book Tally)")?></h2>
			<p><?php echo T("This tool allows library staff to record.."); ?></p>
			<p><?php echo T("All entries will contribute to the.."); ?></p>
			<p><?php echo T("You may use a barcode scanner or.."); ?></p>
			
			<h2><?php echo T("Scan or Enter Book Barcode"); ?></h2>
		</section>

    <form id="barcode-form" 
          hx-target="#result"
          hx-swap="innerHTML"
          autocomplete="off">
        <input type="text" name="barcode" id="barcode" value="" oninput="this.value = this.value.replace(/\D/g, '').slice(0, 13)" pattern="\d*" autofocus required>
        <button type="submit"><?php echo T("Submit"); ?></button>
    </form>

		<div id="result"></div>
		<h2><?php echo T("Check most recent listing"); ?></h2>
    <form id="listbooks-form" 
          hx-target="#result2"
          hx-swap="innerHTML"
          autocomplete="off">
        <button type="submit"><?php echo T("Check most recent listing"); ?></button>
    </form>

    <div id="result2"></div>
		
</section>
<script>
document.getElementById('barcode-form').addEventListener('submit', function(e) {
    e.preventDefault(); // stop default HTMX behavior

    const form = e.target;
    const input = document.getElementById("barcode");

    // Optional: show processing message immediately
    document.getElementById("result").innerHTML = "⏳ Processing...";

    // Wait 500ms before submitting
		form.querySelector("button").disabled = true;
    setTimeout(() => {
        htmx.ajax('POST', 'dailytally_process.php', {
            target: '#result',
            swap: 'innerHTML',
            values: {
                barcode: input.value
            }
        });
				form.querySelector("button").disabled = false;
    }, 500);
});

document.getElementById('listbooks-form').addEventListener('submit', function(e) {
    e.preventDefault(); // stop default HTMX behavior

    const form = e.target;
    

    // Optional: show processing message immediately
    document.getElementById("result2").innerHTML = "⏳ Processing...";

    // Wait 500ms before submitting
		form.querySelector("button").disabled = true;
    setTimeout(() => {
        htmx.ajax('POST', 'listtally_process.php', {
            target: '#result2',
            swap: 'innerHTML'
        });
				form.querySelector("button").disabled = false;
    }, 500);
});

</script>