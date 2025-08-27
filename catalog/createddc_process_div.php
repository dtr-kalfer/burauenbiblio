<?php
/* 
 * This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 * -- F. Tumulak
 */
// Guard Doggy
		require_once("../shared/guard_doggy.php");
// Connect to DB
    require_once("../catalog/class/Qtest.php");

    $mypass = new Qtest;
    $db = mysqli_connect(
        $mypass->getDSN2("host"),
        $mypass->getDSN2("username"),
        $mypass->getDSN2("pwd"),
        $mypass->getDSN2("database")
    );
    if (mysqli_connect_errno()) {
        die("Connection failed: " . mysqli_connect_error());
    }

try {
    // Start transaction for safety
    $db->query("START TRANSACTION");

    // 4. Update classification based on DDC ranges
		require_once("../catalog/ddc_wiki/div_sql_ddc.php");

    $db->query($updateSQL);

    // Commit transaction
    $db->query("COMMIT");

    // Get number of rows inserted for feedback
    $result = $db->query("SELECT COUNT(*) AS total FROM extract_ddc");
    $row = $result->fetch_assoc();
    $count = $row['total'];

    echo "<p>✅ Successfully created and populated <strong>DDC Level II Division Mapping</strong> table.</p>";
    echo "<p>Total records inserted: <strong>{$count}</strong></p>";
    
} catch (Exception $e) {
    // Rollback if something goes wrong
    $db->query("ROLLBACK");
    echo "<p style='color:red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?>
