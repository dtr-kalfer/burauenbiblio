<?php
	/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
	 * See the file COPYRIGHT.html for more details.
	 */
require_once("../shared/common.php");
$tab = "circulation/analytics";
$nav = "top30";    

require_once(REL(__FILE__, "../shared/logincheck.php"));

Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));

require_once __DIR__ . '/../autoload.php';
use BookUtilization\Top30borrow;

$result = new Top30borrow();
$stmt = $result->make_top30borrowlist();

// Get current date

$endDate = new DateTime(); // today
$startDate = (clone $endDate)->modify('-6 months');

// Format dates
$startFormatted = $startDate->format('F j, Y');
$endFormatted = $endDate->format('F j, Y');

if ($stmt['success'] === true) {
?>

<section style="width: 700px; padding: 15px;" id="top30_section">

    <!-- Export Form Control -->
    <div style="margin-bottom: 15px; background: #fff; padding: 15px; border: 1px solid #ccc; display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
        <div>
            <label for="export_month"><strong><?php echo T("Export Month:"); ?></strong></label>
            <input type="month" id="export_month" name="export_month" value="<?= date('Y-m') ?>">
        </div>

        <div>
            <label for="export_limit"><strong><?php echo T("Set Limit:"); ?></strong></label>
            <select id="export_limit" name="export_limit">
                <option value="30" selected><?php echo T('Top 30'); ?></option>
                <option value="50"><?php echo T('Top 50'); ?></option>
                <option value="100"><?php echo T('Top 100'); ?></option>
                <option value="500"><?php echo T('Top 500'); ?></option>
                <option value="all"><?php echo T('All Books (No Limit)'); ?></option>
            </select>
        </div>

        <button type="button" onclick="triggerJsonExport()"><?php echo T("Export to JSON"); ?></button>
    </div>

    <table border="1" cellpadding="8" cellspacing="0" style="width: 100%;">
        <thead>
            <tr>
                <th colspan="5">
                    <?php 
                        echo "<h2>" . T("top30active") . " (" . $startFormatted . " - " . $endFormatted . ") </h2>";
                        echo '<p style="text-align: center;">' . T("Retrieved from: Circulation activity") . '</p>';
                    ?>
                </th>
            </tr>
            <tr>
                <th><?php echo T("Rank"); ?></th>
                <th><?php echo T("Title"); ?></th>
                <th><?php echo T("Author"); ?></th>
                <th><?php echo T("ISBN"); ?></th>
                <th><?php echo T("Count"); ?></th>
            </tr>
        </thead>
        <tbody>
        
        <?php 
            require_once("top30_portion.php");
        } else {
            echo '<p style="text-align: center;">' . $stmt['message'] . '</p>';
        }
        ?>

        </tbody>
    </table>
</section>

<script>
function triggerJsonExport() {
    const monthVal = document.getElementById('export_month').value;
    const limitVal = document.getElementById('export_limit').value;
    
    if (!monthVal) {
        alert('<?php echo T("Please select a valid month (YYYY-MM)."); ?>');
        return;
    }
    
    // Redirect browser to the export script with month and limit parameters
    const url = 'export_borrow_json.php?month=' + encodeURIComponent(monthVal) + '&limit=' + encodeURIComponent(limitVal);
    window.location.href = url;
}
</script>