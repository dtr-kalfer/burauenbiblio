<style>
.tagged-table {
    width: 900px;
    max-height: 525px;   /* pick what feels right */
    overflow-y: auto; /* make the y-axis scrollable when overflow is reached */
    border-radius: 6px;
    font-size: 14px;
}

.tagged-row {
    display: grid;
    grid-template-columns: 140px 2fr 1fr 1fr 2fr 1fr;
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
  background-color: darkblue;
}
.totalc {
  font-weight: bold;
  text-align: center;
}

/* ── Clickable member link styling ───────────────────────────────────── */
.member-link {
    color: #1a56db;
    text-decoration: none;
    font-weight: 500;
    border-bottom: 1px dashed #1a56db;
    transition: color 0.15s, border-color 0.15s;
}
.member-link:hover {
    color: #0d3b9e;
    border-bottom-style: solid;
}
.member-link::after {
    content: " ↗";
    font-size: 0.75em;
    opacity: 0.5;
}
.barcode-link {
    color: #333;
    text-decoration: none;
    font-family: monospace;
    border-bottom: 1px dotted #999;
}
.barcode-link:hover {
    color: #1a56db;
    border-bottom-color: #1a56db;
}
</style>
<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
 // Use this as a generic template for informational query + custom sql --F.Tumulak

  require_once("../shared/common.php");

  $tab="circulation";
  $nav="bookings_v2";
  if (isset($_REQUEST['tab'])) {
    $tab = $_REQUEST['tab'];
  }
  if (isset($_REQUEST['nav'])) {
    $nav = $_REQUEST['nav'];
  }
  require_once(REL(__FILE__, "../shared/logincheck.php"));

  Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));

  require_once("../shared/guard_doggy.php");

  require_once __DIR__ . '/../autoload.php'; // adjust the ../ if necessary depending on your source path.

  use Booking\Booking;

$booked = new Booking();
$rows = $booked->getBookedItems();
$total = count($rows);
?>
<h3 class="tagged">📚 <?php echo T("The Booked Items Cart"); ?> 📚</h3>
<p>
The Booked Items Cart is a *Reservation feature* for patrons that may request:</p>
<ul>
  <li>Reservation for a specific book.</li>
  <li>Books that are checked out constantly.</li>
  <li>First come first served reservation basis</li>
</ul>

<h3 class="tagged">
  📚 <?php echo T("List of booked items"); ?> 📚
</h2>
<p class="totalc">
  Total booked items: <?php echo $total; ?>
  <br />
  <small style="font-weight: normal; font-size: 0.85em;">
    💡 <?php echo T("Click a patron name to view their circulation status."); ?>
  </small>
</p>
<section>
<?php
if (empty($rows)) {
    echo "<div class='notagfound'>" . T("No booked items found in the cart.") . "</div>";
} else {

    echo "<div class='tagged-table'>";

    /* Header */
    echo "
    <div class='tagged-row header'>
        <div class='tagged-cell nowrap'>Barcode</div>
        <div class='tagged-cell wrap'>Title</div>
        <div class='tagged-cell wrap'>Status</div>
        <div class='tagged-cell wrap'>Request Date</div>
        <div class='tagged-cell wrap'>Requested by</div>
        <div class='tagged-cell wrap'>Type</div>
    </div>
    ";

    /* Rows */
    foreach ($rows as $row) {
        $mbrid = $row['mbrid'] ?? '';
        $memberUrl = "memberForms.php?mbrid=" . urlencode((string) $mbrid);
        $memberName = H($row['member'] ?? 'Unknown');
        $barcode = H($row['barcode_nmbr'] ?? '');

        echo "
        <div class='tagged-row'>
            <div class='tagged-cell nowrap'>
                <a href='{$memberUrl}' class='barcode-link' title='" . T("View member status") . "'>{$barcode}</a>
            </div>
            <div class='tagged-cell wrap'>" . H($row['title'] ?? '') . "</div>
            <div class='tagged-cell wrap'>" . H($row['status'] ?? '') . "</div>
            <div class='tagged-cell wrap'>" . H($row['hold_begin'] ?? '') . "</div>
            <div class='tagged-cell wrap'>
                <a href='{$memberUrl}' class='member-link' title='" . T("View member status") . "'>{$memberName}</a>
            </div>
            <div class='tagged-cell wrap'>" . H($row['classification_name'] ?? '') . "</div>
        </div>
        ";
    }

    echo "</div>";
}
?>
</section>