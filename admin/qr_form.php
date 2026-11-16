<style>
.qr-preview img {
  border: 1px dashed #ccc;
  padding: 6px;
  background: #fff;
}

.qr-preview {
  text-align: center;
  border: 5px dashed red;
  padding: 20px;
  background-color: #add;
}

#qr-print-preview {
  /* overflow-y: scroll; */
  height: auto;
}
.wrapper-section {
  overflow-y: scroll;
  width: 830px;
}
.preview {
  text-align: center;
  padding: 10px;
  border: 1px solid green;

}

</style>
<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
 // Use this as a generic template for informational query + custom sql --F.Tumulak

  function countQrImages($dir) {
      return count(glob($dir . '/*.png'));
  }

  require_once("../shared/common.php");

  $tab="admin";
  $nav="qr_code";
  if (isset($_REQUEST['tab'])) {
    $tab = $_REQUEST['tab'];
  }
  if (isset($_REQUEST['nav'])) {
    $nav = $_REQUEST['nav'];
  }
  require_once(REL(__FILE__, "../shared/logincheck.php"));

  Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));

  require_once("../shared/guard_doggy.php");

  $qrDir = __DIR__ . '/qr_code_images';
  $qrCount = is_dir($qrDir) ? count(glob($qrDir . '/*.png')) : 0;

?>
<!-- HTMX script (local or CDN) -->
<script src="../htmx_cdn/htmx.min.js"></script>

<section class="wrapper-section">
  <h3 class="tagged">📚 <?php echo T("QR Generator"); ?> 📚</h3>

  <p><?= T("This section will generate a 6x8") ?></p>
  <p><?= T("Each QR-code requires a 13") ?><br></p>

  <form id="qr_form"
    hx-get="qr_preview.php"
    hx-target="#qr-result"
    hx-swap="innerHTML"
  >
    <label>
      13-digit Code:
      <input
        type="text"
        name="code"
        maxlength="13"
        pattern="[0-9]{13}"
        placeholder="0000000001234"
        required
      >
    </label>
    <button type="submit"><?= T("Generate QR") ?></button>
  </form>
  <div class="preview" style="display:flex; justify-content:center; gap:10px; flex-wrap:wrap; align-items:center;">
    <label style="font-weight:bold;"><?= T("Print Layout") ?>:</label>
    <select id="layoutCopies" name="copies" style="padding:4px 8px;">
      <option value="1"><?= T("1 Copy per QR") ?></option>
      <option value="3"><?= T("3 Copies per QR") ?></option>
    </select>
    <button
        hx-get="qr_a4_preview.php"
        hx-include="#layoutCopies"
        hx-target="#qr-print-preview"
        hx-swap="innerHTML"
        class="showlayoutbtn"
    >
    🖨 <?= T("Preview A4 Layout") ?>
    </button>
  </div>

  <!-- ================= MEMBER BATCH SECTION (QR + name + ID) ================= -->
  <?php
  require_once(__DIR__ . '/qr_member_lib.php');
  try {
    $qrm = new QrMembers;
    $mbrList = $qrm->listWithQr();
  } catch (\Throwable $e) {
    $mbrList = [];
    echo "<p style='color:red'>⚠️ Could not load members: " . htmlspecialchars($e->getMessage()) . "</p>";
  }
  ?>
  <fieldset style="margin-top:25px; border:1px solid #7a9;">
    <legend>👤 <?= T("Batch Print - Member QR Labels") ?></legend>
    <p><?= T("Generates labels with the member") . $QR_MAX_IMAGES . T("per sheet") ?></p>
    <label>
      <?= T("Select member") ?>:
      <select id="mbrQrPick" name="mbrid" style="min-width:320px;">
        <option value=""><?= T("choose a member with a QR number") ?></option>
        <?php foreach ($mbrList as $m): ?>
          <option value="<?php echo (int) $m['mbrid']; ?>">
            <?php echo htmlspecialchars($m['last_name'] . ', ' . $m['first_name']
              . '  [' . $m['qrcode'] . ']'); ?>
          </option>
        <?php endforeach; ?>
      </select>
    </label>

    <button
      type="button"
      hx-get="qr_member_save.php"
      hx-include="#mbrQrPick"
      hx-target="#mbr-save-status"
      hx-swap="innerHTML"
    >➕ <?= T("Add Selected Member") ?></button>

    <button
      type="button"
      hx-get="qr_member_save.php?all=1"
      hx-target="#mbr-save-status"
      hx-swap="innerHTML"
    >📦 <?= T("Add ALL Members (batch)") ?></button>

    <button
      type="button"
      hx-get="qr_clear.php"
      hx-target="#mbr-save-status"
      hx-swap="innerHTML"
    >🧹 <?= T("Clear Batch") ?></button>

    <div id="mbr-save-status" style="margin-top:10px;"></div>
  </fieldset>
  <!-- ================= /MEMBER BATCH SECTION ================= -->

  <p>📦 <?= T("Saved QR images..") ?>
  <strong id="qr-counter"><?php echo $qrCount; ?></strong>
  </p>

  <section id="qr-result" style="margin-top:20px;"></section>
  <div id="qr-print-preview" style="margin-top:20px;"></div>
</section>
