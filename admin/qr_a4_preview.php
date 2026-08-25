<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details. --F. Tumulak 1/30/2026
 */
require_once("../shared/guard_doggy.php");
require_once("qr_settings.php");

$imgDir = __DIR__ . '/qr_code_images';
$webDir = 'qr_code_images';

// Copies per unique QR: 1 (single copy, up to 48 items) or 3 (default, up to 16 items)
$copies = isset($_GET['copies']) ? max(1, min(3, (int)$_GET['copies'])) : 3;
$maxFiles = ($copies === 1) ? 48 : 16;

// get png files
$files = glob($imgDir . '/*.png');

// sort for consistency (by name)
sort($files);

// take appropriate number of items based on layout mode
$files = array_slice($files, 0, $maxFiles);

if (empty($files)) {
    echo "<p style='color:red'>No saved QR images found.</p>";
    exit;
}
?>

<style>
/* --- SCREEN PREVIEW --- */
.qr-sheet-wrapper {
  background: #eee;
  padding: 3px;
}

/* --- PRINT PAGE SETUP --- */
@media print {
  @page {
    size: 210mm 297mm;
    margin: 0;
  }

  body {
    margin: 0;
  }

  .qr-sheet-wrapper {
    padding: 0;
    background: none;
  }

  .print-btn, #qr_form, #sidebar, h3, p, button, .preview, fieldset {
    display: none !important;
  }

  * {
  border: none;
  }

  .qr-preview {
    display: none;
  }

}

/* --- SHEET --- */
.qr-sheet {
  width: 210mm;
  height: 270mm;
  display: grid;
  grid-template-columns: repeat(6, 1fr);
  grid-template-rows: repeat(8, 1fr);
  padding: 1mm 3mm;

  box-sizing: border-box;
  background: white;

}

/* --- QR CELL --- */
.qr-cell {
  display: flex;
  align-items: center;
  justify-content: center;
  border: 1px dashed #999;
}

.qr-cell img {
  max-width: 75%;

}

/* Member labels (200x200, name + ID under QR) get a bit more room */
.qr-cell img[src*="qr_mbr_"] {
  max-width: 92%;
}
</style>

<div class="qr-sheet-wrapper">

  <button class="print-btn" onclick="window.print()">
    🖨 Print / Export PDF (Layout: <?php echo $copies; ?> cop<?php echo ($copies === 1 ? 'y' : 'ies' ); ?> per QR)
  </button>

  <div class="qr-sheet">
    <?php
      foreach ($files as $file) {
          $filename = basename($file);
          $src = $webDir . '/' . $filename;

          for ($i = 0; $i < $copies; $i++) {
              echo "
                <div class='qr-cell'>
                  <img src='{$src}'>
                </div>
              ";
          }
      }
    ?>
  </div>

</div>