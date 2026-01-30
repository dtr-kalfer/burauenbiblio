<style>
.tagged-table {
    width: 750px;
    max-height: 505px;   /* pick what feels right */
    overflow-y: auto; /* make the y-axis scrollable when overflow is reached */
    border-radius: 6px;
    font-size: 14px;
}

.tagged-row {
    display: grid;
    grid-template-columns: 80px 1fr 1fr 1fr;
    padding: 3px 8px;
    border-bottom: 1px solid #666;
    align-items: start; /* important for multi-line text */
}

.tagged-row.header {
    position: sticky;
    top: 0;
    z-index: 2;
    background: #f4f4f4;
    font-weight: bold;
    border-bottom: 2px solid #555;
}

/*-- this should preceed before any hover styling --*/
/*-- .tagged-row:nth-child(n):hover --*/
.tagged-row:nth-child(even):not(.header) {
    background: #eee;
}

.tagged-row:hover:not(.header) {
    background: bisque;
		cursor: pointer;
}

.tagged-cell {
    padding: 2px 6px;
}

.notagfound {
	 border: 1px dashed #ccc;
   padding: 12px;
   background: #fafafa;
   font-style: italic;
   color: #555;
	 text-align: center;
}

.tagged-cell.wrap {
    white-space: normal;
    word-break: break-word;
    overflow-wrap: anywhere;
}

.tagged-cell.nowrap {
    white-space: nowrap;
}

h3.tagged {
	background-color: red;
}
.totalc {
	font-weight: bold;
	text-align: center;
}
</style>
<?php 
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details. --Ferdinand Tumulak
 */
// Nav::node('cataloging/tagged', T("Tagged"), "../catalog/taggedItems.php");

require_once("../shared/common.php");
$tab = "cataloging";
$nav = "tagged";	

require_once(REL(__FILE__, "../shared/logincheck.php"));

Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));

require_once("../shared/guard_doggy.php");

require_once __DIR__ . '/../autoload.php'; // adjust the ../ if necessary depending on your source path.

use Tagged\Tagged;

$tagged = new Tagged();
$rows = $tagged->getTaggedBibItems();
$total = count($rows);
?>
<h3 class="tagged">📚 <?php echo T("The Tagged Items Cart"); ?> 📚</h3>
<p>
The Tagged Items Cart is a *temporary holding space* for catalog items that may have:</p>
<ul>
	<li>Some damages or defects present in the book.</li>
	<li>Incomplete biblio details.</li>
	<li>Conflicting call numbers.</li>
	<li>Duplicate Biblio Records.</li>
</ul>

<h3 class="tagged">
	📚 <?php echo T("List of tagged items for review"); ?> 📚
</h2>
<p class="totalc">Total tagged items: <?php echo $total; ?></p>
<section>
<?php 
if (empty($rows)) {
    echo "<div class='notagfound'>" . T("No tagged items found in the cart.") . "</div>";
} else {

    echo "<div class='tagged-table'>";

    /* Header */
    echo "
    <div class='tagged-row header'>
        <div class='tagged-cell wrap'>BibID</div>
				<div class='tagged-cell wrap'>Call Number</div>
        <div class='tagged-cell wrap'>Title</div>
        <div class='tagged-cell wrap'>Author</div>
    </div>
    ";

    /* Rows */
    foreach ($rows as $row) {
        echo "
        <div class='tagged-row'>
            <div class='tagged-cell nowrap'>{$row['bibid']}</div>
						<div class='tagged-cell wrap'>{$row['call_number']}</div>
            <div class='tagged-cell wrap'>{$row['title']}</div>
            <div class='tagged-cell wrap'>{$row['author']}</div>
        </div>
        ";
    }

    echo "</div>";
}
?>
</section>
