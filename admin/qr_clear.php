<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details. --F. Tumulak 1/30/2026
 */
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
