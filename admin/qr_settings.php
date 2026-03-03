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

$QR_MAX_IMAGES = 16;
