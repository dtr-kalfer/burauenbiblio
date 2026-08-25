<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details. --F. Tumulak 1/30/2026
 */
require_once("../shared/guard_doggy.php");

$qrSize     = 140;
$textArea  = 35;
$imgWidth  = $qrSize;
$imgHeight = $qrSize + $textArea;
$fontSize  = 13;

$QR_MAX_IMAGES = 48; // max unique images in batch folder (up to 48 for 1-copy/cell mode, 16 for 3-copies mode)