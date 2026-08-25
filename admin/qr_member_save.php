<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details. --F.Tumulak
 *
 * HTMX endpoint: save member QR label(s) into the A4 batch folder.
 *   ?mbrid=123   -> add one member
 *   ?all=1       -> add every member that has a qrcode (up to batch max)
 */
require_once(__DIR__ . '/../shared/guard_doggy.php');
require_once(__DIR__ . '/../classes/ConnectDB.php');
require_once(__DIR__ . '/../shared/phpqrcode/qrlib.php');
require_once(__DIR__ . '/qr_settings.php');

$fontFile = __DIR__ . '/../shared/fonts/DejaVuSansMono.ttf';

class QrMembers extends ConnectDB {
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

function qrBuildMemberLabel($mbr) {
    global $fontFile;

    $imgWidth  = 200;
    $qrSize    = 140;
    $imgHeight = 200;
    $nameSize  = 12;
    $codeSize  = 13;

    $name = strtoupper($mbr['last_name']) . ', ' . $mbr['first_name'];
    $code = (string) $mbr['qrcode'];

    $tempFile = tempnam(sys_get_temp_dir(), 'qr_');
    QRcode::png($code, $tempFile, QR_ECLEVEL_L, 5, 1);
    $qrImg = imagecreatefrompng($tempFile);

    $finalImg = imagecreatetruecolor($imgWidth, $imgHeight);
    $white = imagecolorallocate($finalImg, 255, 255, 255);
    $black = imagecolorallocate($finalImg, 0, 0, 0);
    imagefill($finalImg, 0, 0, $white);

    imagecopyresampled(
        $finalImg, $qrImg,
        (int) (($imgWidth - $qrSize) / 2), 0,
        0, 0,
        $qrSize, $qrSize,
        imagesx($qrImg), imagesy($qrImg)
    );

    // Draw centered name
    $nSize = $nameSize;
    while ($nSize > 6) {
        $bbox = imagettfbbox($nSize, 0, $fontFile, $name);
        if (($bbox[2] - $bbox[0]) <= ($imgWidth - 10)) {
            break;
        }
        $nSize--;
    }
    $bbox = imagettfbbox($nSize, 0, $fontFile, $name);
    $nx = (int) (($imgWidth - ($bbox[2] - $bbox[0])) / 2);
    imagettftext($finalImg, $nSize, 0, $nx, $qrSize + 24, $black, $fontFile, $name);

    // Draw centered ID number
    $cSize = $codeSize;
    while ($cSize > 6) {
        $bbox = imagettfbbox($cSize, 0, $fontFile, $code);
        if (($bbox[2] - $bbox[0]) <= ($imgWidth - 10)) {
            break;
        }
        $cSize--;
    }
    $bbox = imagettfbbox($cSize, 0, $fontFile, $code);
    $cx = (int) (($imgWidth - ($bbox[2] - $bbox[0])) / 2);
    imagettftext($finalImg, $cSize, 0, $cx, $qrSize + 46, $black, $fontFile, $code);

    imagedestroy($qrImg);
    unlink($tempFile);

    return $finalImg;
}

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

/* ----------------- REQUEST HANDLER ----------------- */
$qrm = new QrMembers;
$added = 0;
$skippedFull = 0;

if (isset($_GET['all'])) {
    foreach ($qrm->listWithQr() as $mbr) {
        if (qrSaveMemberLabel($mbr)) {
            $added++;
        } else {
            $skippedFull++;
        }
    }
    $msg = "✅ Added {$added} member QR label(s) to the batch."
         . ($skippedFull > 0 ? " ⚠️ {$skippedFull} skipped — batch full." : "");
} else {
    $mbr = $qrm->getByMbrid((int) ($_GET['mbrid'] ?? 0));
    if (!$mbr || empty($mbr['qrcode'])) {
        echo "<span style='color:red'>❌ Member not found or has no QR number.</span>";
        exit;
    }
    if (!qrSaveMemberLabel($mbr)) {
        echo "<span style='color:red'>❌ Maximum of {$QR_MAX_IMAGES} QR codes reached. Clear the batch first.</span>";
        exit;
    }
    $msg = "✅ Saved: <b>" . htmlspecialchars($mbr['last_name'] . ', ' . $mbr['first_name'])
         . "</b> (" . htmlspecialchars($mbr['qrcode']) . ")";
}

$count = count(glob(__DIR__ . '/qr_code_images/*.png'));

echo "<p style='color:green'>{$msg}</p>
<script>
  document.getElementById('qr-counter').innerText = {$count};
</script>";