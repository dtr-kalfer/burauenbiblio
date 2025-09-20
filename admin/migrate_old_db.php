<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details. --F.Tumulak
 */
require_once("../shared/common.php");
$tab = "admin";
$nav = "migratedb";	

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
			 <h2>Database Migration Manager</h2>
			<div style="text-align: center;"><img src="../images/new_schema.webp" /></div>
			<p><b>
				Warning: Please ensure you have a backup of your original DB.
			</b></p>
			<p>
				If you wish to try out Burauenbiblio to your existing Openbiblio database, you may use this tool to make your db compatible.
				This utility allows the database structure to evolve safely as new features are introduced — without breaking the existing database.
			</p>
			
			<p>This tool: <p>
			<ul>
				<li>✅ Safely applies new schema updates</li>
				<li>✅ Skips previously executed migrations</li>
				<li>✅ Enables smooth upgrades as BurauenBiblio evolves</li>
				<li>✅ Tested on old Openbiblio 1.0 db</li>
				<li>✅ Updates all Openbiblio table engine to InnoDB</li>
			</ul>
			<p>
			Note: Please understand that there are several OpenBiblio-based databases out there, each with slightly different schemas. Test migrated database carefully on development setup before applying them to production.
			</p>
		</section>

    <form id="migrate-form" 
          hx-target="#result"
          hx-swap="innerHTML"
          autocomplete="off">
        <button type="submit">Migrate my old database</button>
    </form>

    <div id="result"></div>
		
</section>
<script>
document.getElementById('migrate-form').addEventListener('submit', function(e) {
    e.preventDefault(); // stop default HTMX behavior

    const form = e.target;
    

    // Optional: show processing message immediately
    document.getElementById("result").innerHTML = "⏳ Preparing...";

    // Wait 500ms before submitting
		form.querySelector("button").disabled = true;
    setTimeout(() => {
        htmx.ajax('POST', 'migration_process.php', {
            target: '#result',
            swap: 'innerHTML',

        });
				form.querySelector("button").disabled = false;
    }, 3000);
});

</script>