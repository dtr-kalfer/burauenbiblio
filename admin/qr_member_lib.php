<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details. --F.Tumulak
 *
 * Shared helper for MEMBER QR label generation & batch saving.
 * Label layout: QR code + member name + student/faculty ID number.
 */
require_once(__DIR__ . '/../shared/guard_doggy.php');
require_once(__DIR__ . '/../classes/ConnectDB.php');
require_once(__DIR__ . '/../shared/phpqrcode/qrlib.php');
require_once(__DIR__ . '/qr_settings.php');

$fontFile = __DIR__ . '/../shared/fonts/DejaVuSansMono.ttf';

class QrMembers extends ConnectDB {
  /** All members that have a QR (student/faculty) number encoded */
  public function listWithQr() {
    return $this->select(
      "SELECT mbrid, barcode_nmbr, qrcode, last_name, first_name
         FROM member
        WHERE qrcode IS NOT NULL AND qrcode <> ''
        ORDER BY last_name, first_name
        LIMIT 500"
    );
  }

  public function getByMbrid($mbrid) {
    return $this->selectOne(
      "SELECT mbrid, barcode_nmbr, qrcode, last_name, first_name
         FROM member WHERE mbrid = ?",
      "i", [$mbrid]
    );
  }
}

/**
 * Build the member label image (GD resource).
 * Square-ish 200x200: QR on top, name + ID number underneath.
 */
function qrBuildMemberLabel($mbr) {
  global $fontFile;

  $imgWidth  = 200;
  $qrSize    = 140;
  $imgHeight = 200;
  $nameSize  = 12;
  $codeSize  = 13;

  $name = strtoupper($mbr['last_name']) . ', ' . $mbr['first_name'];
  $code = (string) $mbr['qrcode'];

  /* --- raw QR --- */
  $tempFile = tempnam(sys_get_temp_dir(), 'qr_');
  QRcode::png($code, $tempFile, QR_ECLEVEL_L, 5, 1);
  $qrImg = imagecreatefrompng($tempFile);

  /* --- final canvas --- */
  $finalImg = imagecreatetruecolor($imgWidth, $imgHeight);
  $white = imagecolorallocate($finalImg, 255, 255, 255);
  $black = imagecolorallocate($finalImg, 0, 0, 0);
  imagefill($finalImg, 0, 0, $white);

  /* --- QR centered horizontally --- */
  imagecopyresampled(
    $finalImg, $qrImg,
    (int) (($imgWidth - $qrSize) / 2), 0,
    0, 0,
    $qrSize, $qrSize,
    imagesx($qrImg), imagesy($qrImg)
  );

  /* --- centered TTF helper (auto-shrink to fit width) --- */
  $drawCentered = function ($text, $size, $y) use ($finalImg, $black, $fontFile, $imgWidth) {
    while ($size > 6) {
      $bbox = imagettfbbox($size, 0, $fontFile, $text);
      $textWidth = $bbox[2] - $bbox[0];
      if ($textWidth <= $imgWidth - 10) break;
      $size--;
    }
    $bbox = imagettfbbox($size, 0, $fontFile, $text);
    $x = (int) (($imgWidth - ($bbox[2] - $bbox[0])) / 2);
    imagettftext($finalImg, $size, 0, $x, $y, $black, $fontFile, $text);
  };

  $drawCentered($name, $nameSize, $qrSize + 24); // name
  $drawCentered($code, $codeSize, $qrSize + 46); // ID number

  imagedestroy($qrImg);
  unlink($tempFile);

  return $finalImg;
}

/** Save one member label into the A4 batch folder. Returns false if batch full. */
function qrSaveMemberLabel($mbr) {
  global $QR_MAX_IMAGES;

  $saveDir = __DIR__ . '/qr_code_images';
  if (!is_dir($saveDir)) {
    mkdir($saveDir, 0755, true);
  }
  if (count(glob($saveDir . '/*.png')) >= $QR_MAX_IMAGES) {
    return false;
  }

  $filename = $saveDir . '/qr_mbr_' . (int) $mbr['mbrid'] . '.png';
  if (!file_exists($filename)) {
    $img = qrBuildMemberLabel($mbr);
    imagepng($img, $filename);
    imagedestroy($img);
  }
  return true;
}