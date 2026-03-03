<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details. --F. Tumulak 1/30/2026
 */
require_once("../shared/guard_doggy.php");

$imgDir = __DIR__ . '/qr_code_images';
$webDir = 'qr_code_images';

// get png files
$files = glob($imgDir . '/*.png');

// sort for consistency (by name)
sort($files);

// take max 8
$files = array_slice($files, 0, 16);

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

  .print-btn, #qr_form, #sidebar, h3, p, button {
    display: none;
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
</style>

<div class="qr-sheet-wrapper">

  <button class="print-btn" onclick="window.print()">
    🖨 Print / Export PDF
  </button>

  <div class="qr-sheet">
    <?php
      foreach ($files as $file) {
          $filename = basename($file);
          $src = $webDir . '/' . $filename;

          // 3 duplicates per QR
          for ($i = 0; $i < 3; $i++) {
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
