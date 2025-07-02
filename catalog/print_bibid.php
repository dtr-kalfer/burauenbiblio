<?php
// guard token protects this php from being poked at. --F.Tumulak
require_once("../shared/guard_token.php"); 
verify_token_or_die('guard_token_key'); // your custom key

require_once("functions/card_catalog.php"); 
require_once("class/Qtest.php"); 

$mypass = new Qtest;
$connection = mysqli_connect(
    $mypass->getDSN2("host"),
    $mypass->getDSN2("username"),
    $mypass->getDSN2("pwd"),
    $mypass->getDSN2("database")
);
if (mysqli_connect_errno()) {
    die("Connection failed: " . mysqli_connect_error());
}

// Sanitize inputs
$bibid1 = mysql_prep($_POST["bibid_fpdf"]);
$bibid2 = mysql_prep($_POST["bibid_fpdf2"]);

$errors = [];

if (mysqli_num_rows(get_subfield_all_from_bibid($bibid1)) == 0) {
    $errors[] = "BibID $bibid1 not found.";
}
if (mysqli_num_rows(get_subfield_all_from_bibid($bibid2)) == 0) {
    $errors[] = "BibID $bibid2 not found.";
}

if (!empty($errors)) {
    echo "<div class='error'>" . implode("<br>", $errors) . "</div>";
} else {
    // ✅ Both valid → redirect to PDF
    $query = http_build_query(['bibid_fpdf' => $bibid1, 'bibid_fpdf2' => $bibid2]);
    echo "<script>window.location.href = 'print_bibid_pdf.php?$query';</script>";
}
exit;
