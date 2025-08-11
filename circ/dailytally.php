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
		
		p,input,button {
			margin: 10px;
		}
</style>
<section class="form-container-attendance">
		<section class="book-activity">
			<h2>📚 In-House Book Activity Tracker <small>(Daily Book Tally)</small></h2>
			<p>
				This tool allows library staff to record books frequently used or viewed by patrons before reshelving them.
				Tracking these activities helps identify high-demand titles for future collection development.
			</p>
			<p>
				All entries will contribute to the <strong>Top 30 Most-Viewed Books</strong> report and also to <strong>Daily T. (Chart)</strong>.
			</p>
			<p>
				You may use a barcode scanner or manually type the relevant barcode numbers. Leading zeroes will be automatically prefixed to ensure a 13-digit format.
			</p>

			<h2>✅ Scan or Enter Book Barcode:</h2>
		</section>

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