<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details. --F. Tumulak 1/30/2026
 */
require_once("../shared/guard_doggy.php");
require_once __DIR__ . '/../shared/phpqrcode/qrlib.php';

$fontFile = __DIR__ . '/../shared/fonts/DejaVuSansMono.ttf';
$code = $_GET['code'] ?? '';
$save = isset($_GET['save']) && $_GET['save'] == '1';

if (!preg_match('/^[0-9]{13}$/', $code)) {
    die('Invalid code. Must be exactly 13 digits.');
}

/* --- CONFIG --- */
require_once("qr_settings.php");

/* --- GENERATE QR TEMP --- */
$tempFile = tempnam(sys_get_temp_dir(), 'qr_');

QRcode::png(
    $code,
    $tempFile,
    QR_ECLEVEL_L,
    5,
    1
);

$qrImg = imagecreatefrompng($tempFile);

/* --- FINAL IMAGE --- */
$finalImg = imagecreatetruecolor($imgWidth, $imgHeight);
$white = imagecolorallocate($finalImg, 255, 255, 255);
$black = imagecolorallocate($finalImg, 0, 0, 0);

imagefill($finalImg, 0, 0, $white);

/* --- COPY QR (scaled) --- */
imagecopyresampled(
    $finalImg,
    $qrImg,
    0, 0,
    0, 0,
    $qrSize, $qrSize,
    imagesx($qrImg),
    imagesy($qrImg)
);

/* --- CENTER TEXT --- */
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

/* --- SAVE OPTIONAL --- */
$saveDir = __DIR__ . '/qr_code_images';
if (!is_dir($saveDir)) {
    mkdir($saveDir, 0755, true);
}

$filename = $saveDir . '/qr_' . $code . '.png';

if ($save && !file_exists($filename)) {
    imagepng($finalImg, $filename);
}

/* --- OUTPUT --- */
header('Content-Type: image/png');
imagepng($finalImg);

/* --- CLEANUP --- */
imagedestroy($qrImg);
imagedestroy($finalImg);
unlink($tempFile);
