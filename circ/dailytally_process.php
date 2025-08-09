<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details. --F.Tumulak
 */
require_once("../shared/guard_doggy.php"); //mini csrf-protection F.Tumulak
require_once("../catalog/class/Qtest.php");
// session_start();  // Make sure sessions are enabled but it is started already by guard_doggy. --F.Tumulak

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $barcode = trim($_POST['barcode'] ?? '');

    if ($barcode === '') {
        echo "<span style='color:red;'>Barcode is empty.</span>";
        exit;
    }

		$barcode = trim($_POST['barcode'] ?? '');
		$barcode = preg_replace('/\D/', '', $barcode); // strip non-digits
		$barcode = str_pad($barcode, 13, "0", STR_PAD_LEFT); // ensure 13-digit padded

    $mypass = new Qtest;
    $connection = mysqli_connect(
        $mypass->getDSN2("host"),
        $mypass->getDSN2("username"),
        $mypass->getDSN2("pwd"),
        $mypass->getDSN2("database")
    );

    if (mysqli_connect_errno()) {
        echo "<span style='color:red;'>Connection failed: " . mysqli_connect_error() . "</span>";
        exit;
    }

    // ✅ Corrected column name
    $query = "SELECT bibid FROM biblio_copy WHERE barcode_nmbr = ?";
    $stmt = $connection->prepare($query);

    if (!$stmt) {
        echo "<span style='color:red;'>Query prepare failed: " . $connection->error . "</span>";
        exit;
    }

    $stmt->bind_param("s", $barcode);
    $stmt->execute();
    $result = $stmt->get_result();
    $bibrow = $result->fetch_assoc();

    if (!$bibrow) {
        echo "<div style='color:red; text-align: center;'>Barcode not found.</div>";
        exit;
    }

    $bibid = $bibrow['bibid'];

    // ✅ Use session username
    $session_user = $_SESSION["username"] ?? "guest";

    // Insert book activity log
    $insert = "INSERT INTO obib_book_activity (bibid, barcode, session_user) VALUES (?, ?, ?)";
    $stmt2 = $connection->prepare($insert);

    if (!$stmt2) {
        echo "<span style='color:red;'>Insert prepare failed: " . $connection->error . "</span>";
        exit;
    }

    $stmt2->bind_param("iss", $bibid, $barcode, $session_user);

    if ($stmt2->execute()) {
        echo "<div style='color:blue; text-align: center;'>Barcode <b>" . $barcode . "</b> daily tally updated. </div>";
    } else {
        echo "<div style='color:red; text-align: center;'>Failed to save activity.</div>";
    }

    $stmt->close();
    $stmt2->close();
    $connection->close();
}
?>
