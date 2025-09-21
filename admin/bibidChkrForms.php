<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details. --F.Tumulak
 */
require_once("../shared/common.php");
$tab = "admin";
$nav = "bibidchecker";	

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
			 <h2>BibID Checker - Orphaned BibID Cleanup</h2>
			<div style="text-align: center;"><img src="../images/sample0copy.webp" /></div>
			<p>
				Orphaned Biblios are created when a biblio record is added but no corresponding barcode copies are saved, often caused by canceling or skipping the barcode entry step.
			</p>
			<p>
				Over time, these “dangling” records can accumulate, making the OpenBiblio database unnecessarily large and potentially slowing down search performance.
			</p>
			
			<p>This tool allows: <p>
			<ul>
				<li>Count the total number of orphaned BibIDs.</li>
				<li>Delete orphaned BibIDs to keep the database optimized and responsive.</li>
			</ul>
			<p>
			Note: BibIDs with at least one barcode copy are automatically excluded from deletion to ensure no valid records are lost.
			</p>
		</section>

    <form id="findorphans-form" 
          hx-target="#result"
          hx-swap="innerHTML"
          autocomplete="off">
        <button type="submit">Check for orphaned biblios</button>
    </form>
		
    <form id="deleteorphans-form" 
          hx-target="#result"
          hx-swap="innerHTML"
          autocomplete="off">
        <button type="submit" onclick="return confirm('Are you sure you want to delete all orphaned BibIDs? This action cannot be undone.');">Remove all orphaned biblios</button>
    </form>
			<p>
			Note: After removing orphaned biblios, you may run <b>Database Checker</b> to check for unattached MARC fields.
			</p>
    <div id="result"></div>
		
</section>
<script>
document.getElementById('findorphans-form').addEventListener('submit', function(e) {
    e.preventDefault(); // stop default HTMX behavior

    const form = e.target;
    

    // Optional: show processing message immediately
    document.getElementById("result").innerHTML = "⏳ Finding orphaned biblios...";

    // Wait 500ms before submitting
		form.querySelector("button").disabled = true;
    setTimeout(() => {
        htmx.ajax('POST', 'findorphan_process.php', {
            target: '#result',
            swap: 'innerHTML',

        });
				form.querySelector("button").disabled = false;
    }, 3000);
});

document.getElementById('deleteorphans-form').addEventListener('submit', function(e) {
    e.preventDefault(); // stop default HTMX behavior

    const form = e.target;
    

    // Optional: show processing message immediately
    document.getElementById("result").innerHTML = "⏳ Removing orphans on relevant tables...";

    // Wait 500ms before submitting
		form.querySelector("button").disabled = true;
    setTimeout(() => {
        htmx.ajax('POST', 'removeorphans_process.php', {
            target: '#result',
            swap: 'innerHTML',

        });
				form.querySelector("button").disabled = false;
    }, 3000);
});

</script>