<?php
require_once("../shared/guard_doggy.php");

$code = $_GET['code'] ?? '';

if (!preg_match('/^[0-9]{13}$/', $code)) {
    echo "<p style='color:red'>Invalid code</p>";
    exit;
}

// cache buster prevents browser reuse
$ts = time();
?>

<div class="qr-preview">
  <img
    src="qr_generate.php?code=<?php echo $code ?>&t=<?php echo time(); ?>"
    alt="QR Code"
  >

  <br><br>

  <button
    hx-get="qr_save.php?code=<?php echo $code ?>"
    hx-target="#qr-save-status"
    hx-swap="innerHTML"
  >
    💾 Add image to batch
  </button>

  <div id="qr-save-status" style="margin-top:10px; border: none;"></div>
	
	<button
  hx-get="qr_clear.php"
  hx-target="#qr-save-status"
  hx-swap="innerHTML"
	>
		🧹 Clear Batch
	</button>

</div>
