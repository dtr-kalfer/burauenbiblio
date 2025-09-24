<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details. --F.Tumulak
 */
require_once("../shared/guard_doggy.php"); //mini csrf-protection F.Tumulak

require_once __DIR__ . '/../autoload.php'; // adjust the ../ if necessary depending on your source path.
use Daily_Tally\Daily_Tally;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $barcode = trim($_POST['barcode'] ?? '');

    if ($barcode === '') {
        echo "<span style='color:red;'>Barcode is empty.</span>";
        exit;
    }

    // ✅ sanitize barcode
    $barcode = preg_replace('/\D/', '', $barcode);
    $barcode = str_pad($barcode, 13, "0", STR_PAD_LEFT);

    // ✅ Use logged-in session user
    $session_user = $_SESSION["username"] ?? "guest";

    $tally = new Daily_Tally();
    $result = $tally->tally_process($barcode, $session_user);

    if ($result['success']) {
        echo "<div style='color:blue; text-align: center;'>"
           . "Barcode <b>{$barcode}</b> daily tally updated."
           . "</div>";
    } else {
        echo "<div style='color:red; text-align: center;'>"
           . $result['message']
           . "</div>";
    }
}
