<?php
require_once("../shared/guard_doggy.php");
require_once("qr_settings.php");

$dir = __DIR__ . '/qr_code_images';

foreach (glob($dir . '/*.png') as $file) {
    unlink($file);
}

echo "
  <span style='color:green'>🧹 QR batch cleared.</span>
  <script>
    document.getElementById('qr-counter').innerText = 0;
  </script>
";
