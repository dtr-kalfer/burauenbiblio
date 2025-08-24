<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details. --F.Tumulak
 */
require_once("../shared/common.php");
$tab = "admin/analytics";
$nav = "create_ddc";	

require_once(REL(__FILE__, "../shared/logincheck.php"));
Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));
?>
<script src="../htmx_cdn/htmx.min.js"></script>
<style>
		.form-container-createddc {
				width: 500px;
		}

		form {
			text-align: center;
		}
		
		p,input,button {
			margin: 10px;
		}
</style>
<section class="form-container-createddc">
		<section class="create-ddc">
			<h2>📚 Create DDC Table</h2>
			<div style="text-align: center;"><img src="../images/sample_portion_ddc_chart.webp" /></div>
			<p>
				This tool allows creation of DDC table needed to make a graph tally (Number of Copies VS. DDC code). 
				It requires a certain amount of catalogued books using DDC to get a meaningful chart.
				The graph tally helps:
			</p>
			
			<ul>
				<li>Identify which classes/discipline dominate the collection.</li>
				<li>Spot underrepresented categories where more resources may be needed.</li>
				<li>Make data-informed decision for future acquisitions, inventory reviews and budget planning.</li>
				<li>Demonstrate the diversity of holdings to stakeholders, management, or partner institutions.</li>
				<li>Saves time compared to manual catalog analysis.</li>
			</ul>
			
			<p>
				The table generated will create a graph for <strong>Top 30 DDC Stats (DDC# Chart)</strong>.
			</p>
		</section>

    <form id="createddc-form" 
          hx-target="#result"
          hx-swap="innerHTML"
          autocomplete="off">
        <button type="submit">Create DDC Table</button>
    </form>

    <div id="result"></div>
		
</section>
<script>
document.getElementById('createddc-form').addEventListener('submit', function(e) {
    e.preventDefault(); // stop default HTMX behavior

    const form = e.target;
    const input = document.getElementById("barcode");

    // Optional: show processing message immediately
    document.getElementById("result").innerHTML = "⏳ Processing...";

    // Wait 500ms before submitting
		form.querySelector("button").disabled = true;
    setTimeout(() => {
        htmx.ajax('POST', 'createddc_process.php', {
            target: '#result',
            swap: 'innerHTML',

        });
				form.querySelector("button").disabled = false;
    }, 3000);
});
</script>