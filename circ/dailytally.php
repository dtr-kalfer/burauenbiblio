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
				width: 500px;
		}

		form {
			text-align: center;
		}
</style>
<section class="form-container-attendance">
		<h2>📚 In-House Book Activity Tracker (Daily Book Tally)</h2>
		<p>This section allows library staff to 📙 log and 📘 monitor actively viewed books within library premises — even without official check-outs. It identifies ✅ high-interest materials for future collection development.</p>
		<p>Use of barcode scanner OR just type in the relevant barcode numbers and the zeroes are auto-prefixed to make it 13-digit.</p>
		<br>
    <h2> ✅ Scan OR enter book barcode:</h2>

    <form id="barcode-form" 
          hx-target="#result"
          hx-swap="innerHTML"
          autocomplete="off">
        <input type="text" name="barcode" id="barcode" value="" oninput="this.value = this.value.replace(/\D/g, '').slice(0, 13)" pattern="\d*" autofocus required>
        <button type="submit">Submit</button>
    </form>

    <div id="result"></div>
		
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
</script>