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

	<p>This section will generate a 6x8 QR Code Sheet</p>
	<p>
	Each QR-code requires a 13-digit number and will be printed on a full size A4 paper. Generate 16 qr-code to maximize the A4 size paper.
	Each QR-code have 3 copies.<br>
	*Note: Please set printer paper size to A4.
	<br>
	</p>

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
    <button type="submit">Generate QR</button>
  </form>
	<div class="preview">
		<button
				hx-get="qr_a4_preview.php?code=<?= htmlspecialchars($code ?? '') ?>"
				hx-target="#qr-print-preview"
				hx-swap="innerHTML"
				class="showlayoutbtn"
		>
		🖨 Preview A4 (full) Layout
		</button>
	</div>
	<p>
  📦 Saved QR images (max: 16 per print):
  <strong id="qr-counter"><?php echo $qrCount; ?></strong>
	</p>
	

	<section id="qr-result" style="margin-top:20px;"></section>
	<div id="qr-print-preview" style="margin-top:20px;"></div>


	
</section>


