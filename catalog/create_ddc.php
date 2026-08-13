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

    .download_list {
      text-align: center;
      padding: 5px;
      a {
          border: 1px solid black;
          padding: 5px;

      }
    }

    .download_list a:hover {
      background-color: #ddd;
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
        The table generated will create a graph for <strong>DDC# Chart</strong>.
        You may also generate Level 2 & 3 division and topic mapping for your collection for <b>DDC Top 30 List</b>.
      </p>
    </section>

    <form id="createddc-form"
          hx-target="#result"
          hx-swap="innerHTML"
          autocomplete="off">
        <button type="submit">Create DDC Table + Process Level I (Main Class) Mapping</button>
    </form>

    <form id="createddc-form2"
          hx-target="#result"
          hx-swap="innerHTML"
          autocomplete="off">
        <button type="submit">Process Level II (Division Class) Mapping</button>
    </form>

    <form id="createddc-form3"
          hx-target="#result"
          hx-swap="innerHTML"
          autocomplete="off">
        <button type="submit">Process Level III (Topic Class) Mapping</button>
    </form>

    <p>
      Please click the three DDC Process levels first before downloading the complete list.
    </p>

    <p class="download_list"><a href="./ddc_export.php?format=json">Download Complete DDC1,2,3 List (.json)</a></p>
    <p class="download_list"><a href="./ddc_export.php?format=csv">Download Complete DDC1,2,3 List (.csv)</a></p>

    <div id="result"></div>

</section>
<script>
document.getElementById('createddc-form').addEventListener('submit', function(e) {
    e.preventDefault(); // stop default HTMX behavior

    const form = e.target;


    // Optional: show processing message immediately
    document.getElementById("result").innerHTML = "⏳ Processing create table + Level I Main Class Mapping...";

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

document.getElementById('createddc-form2').addEventListener('submit', function(e) {
    e.preventDefault(); // stop default HTMX behavior

    const form = e.target;


    // Optional: show processing message immediately
    document.getElementById("result").innerHTML = "⏳ Processing DDC Level II Division Class Mapping...";

    // Wait 500ms before submitting
    form.querySelector("button").disabled = true;
    setTimeout(() => {
        htmx.ajax('POST', 'createddc_process_div.php', {
            target: '#result',
            swap: 'innerHTML',

        });
        form.querySelector("button").disabled = false;
    }, 3000);
});

document.getElementById('createddc-form3').addEventListener('submit', function(e) {
    e.preventDefault(); // stop default HTMX behavior

    const form = e.target;


    // Optional: show processing message immediately
    document.getElementById("result").innerHTML = "⏳ Processing DDC Level III Topic Class Mapping...";

    // Wait 500ms before submitting
    form.querySelector("button").disabled = true;
    setTimeout(() => {
        htmx.ajax('POST', 'createddc_process_adv.php', {
            target: '#result',
            swap: 'innerHTML',

        });
        form.querySelector("button").disabled = false;
    }, 3000);
});
</script>
<p class='download_list'><a href='./ddc_summary_export.php'>Download DDC Summary Report (.csv)</a></p>