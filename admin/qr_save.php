<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details. --F. Tumulak 1/30/2026
 */
require_once("../shared/guard_doggy.php");
require_once __DIR__ . '/../shared/phpqrcode/qrlib.php';

$fontFile = __DIR__ . '/../shared/fonts/DejaVuSansMono.ttf';
$code = $_GET['code'] ?? '';

if (!preg_match('/^[0-9]{13}$/', $code)) {
    echo "<span style='color:red'>Invalid code</span>";
    exit;
}

$saveDir = __DIR__ . '/qr_code_images';
$filename = $saveDir . '/qr_' . $code . '.png';

/* --- CONFIG (MUST MATCH GENERATOR) --- */
require_once("qr_settings.php");

$files = glob($saveDir . '/*.png');
$count = count($files);

if ($count >= $QR_MAX_IMAGES) {
    echo "
      <span style='color:red'>
        ❌ Maximum of {$QR_MAX_IMAGES} QR codes reached.<br>
        Please clear the batch to continue.
      </span>
    ";
    exit;
}

/* --- GENERATE QR TEMP --- */
$tempFile = tempnam(sys_get_temp_dir(), 'qr_');
QRcode::png($code, $tempFile, QR_ECLEVEL_L, 5, 1);

$qrImg = imagecreatefrompng($tempFile);

/* --- FINAL IMAGE --- */
$finalImg = imagecreatetruecolor($imgWidth, $imgHeight);
$white = imagecolorallocate($finalImg, 255, 255, 255);
$black = imagecolorallocate($finalImg, 0, 0, 0);

imagefill($finalImg, 0, 0, $white);

/* --- COPY QR (SCALED) --- */
imagecopyresampled(
    $finalImg,
    $qrImg,
    0, 0,
    0, 0,
    $qrSize, $qrSize,
    imagesx($qrImg),
    imagesy($qrImg)
);

/* --- TTF TEXT (CENTERED) --- */
$bbox = imagettfbbox($fontSize, 0, $fontFile, $code);
$textWidth = $bbox[2] - $bbox[0];

$x = ($imgWidth - $textWidth) / 2;
$y = $qrSize + 20;

imagettftext(
    $finalImg,
    $fontSize,
    0,
    $x,
    $y,
    $black,
    $fontFile,
    $code
);

/* --- SAVE ONCE --- */
if (!is_dir($saveDir)) {
    mkdir($saveDir, 0755, true);
}

if (!file_exists($filename)) {
    imagepng($finalImg, $filename);
}

/* --- CLEANUP --- */
imagedestroy($qrImg);
imagedestroy($finalImg);
unlink($tempFile);

/* --- COUNT FILES --- */
$count = count(glob($saveDir . '/*.png'));

/* --- HTMX RESPONSE --- */
echo "
<p style='color:green'>Saved as qr_{$code}.png</p><br>
<script>
  document.getElementById('qr-counter').innerText = {$count};
</script>
";
