<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
require_once("../shared/common.php");

// We don't need a full page render for a JSON export, just the data.
// So, we skip the header, nav, and other HTML.

require_once("../catalog/class/Qtest.php");

$mypass = new Qtest;

try {
    $dsn = 'mysql:host=' . $mypass->getDSN2("host") . ';dbname=' . $mypass->getDSN2("database") . ';charset=utf8mb4';
    $pdo = new PDO($dsn, $mypass->getDSN2("username"), $mypass->getDSN2("pwd"));
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // If connection fails, return a JSON error response
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode(['error' => 'PDO Connection failed: ' . $e->getMessage()]);
    exit;
}

$sql = "
    SELECT
        bc.bibid,
        bc.barcode_nmbr,
        ucase(bc.copy_desc),
        MAX(CASE WHEN bf.tag='099' AND bs.subfield_cd='a' THEN bs.subfield_data END) AS call_number,
        MAX(CASE WHEN bf.tag='100' AND bs.subfield_cd='a' THEN bs.subfield_data END) AS author,
        MAX(CASE WHEN bf.tag='245' AND bs.subfield_cd='a' THEN bs.subfield_data END) AS title
    FROM biblio_copy bc
    LEFT JOIN biblio_field bf ON bf.bibid = bc.bibid
    LEFT JOIN biblio_subfield bs ON bs.fieldid = bf.fieldid
    WHERE ucase(bc.copy_desc) = 'ITEM-IS-MISSING'
    GROUP BY bc.bibid, bc.barcode_nmbr, bc.copy_desc
";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Set headers to trigger a file download
$datetime = new DateTime();
$filename_suffix = $datetime->format('Y-m-d_H-i-s');
$filename = "missing_books_" . $filename_suffix . ".json";

header('Content-Type: application/json');
header('Content-Disposition: attachment; filename="' . $filename . '"');

// Output the JSON data
echo json_encode($rows, JSON_PRETTY_PRINT);
exit;
?>