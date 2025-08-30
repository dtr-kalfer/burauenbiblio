<?php
/*
 * Orphaned Biblios Cleanup Script for OpenBiblio
 * ---------------------------------------------
 * Safely deletes all biblio records that have no associated copies.
 * Always make sure to BACK UP your database before using this tool.
 *
 * -- Improved by ChatGPT & F. Tumulak
 */

// Guard Doggy - Ensure authentication & permissions
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
    die("<p style='color:red;'><strong>❌ Database connection failed:</strong> " . mysqli_connect_error() . "</p>");
}

// Start transaction for safety
mysqli_begin_transaction($db);

try {
    // SQL to delete orphaned biblios
    $deleteOrphan = "
        DELETE b
        FROM biblio AS b
        LEFT JOIN biblio_copy AS bc
               ON b.bibid = bc.bibid
        WHERE bc.copyid IS NULL
    ";

    // Execute query
    if (!$db->query($deleteOrphan)) {
        throw new Exception("Query failed: " . $db->error);
    }

    // Get the number of affected rows
    $deletedCount = mysqli_affected_rows($db);

    // Commit transaction
    mysqli_commit($db);

    // Output success message
    echo "<p style='color:green; font-weight:bold;'>
            ✅ Successfully removed {$deletedCount} orphaned biblios.
          </p>";

} catch (Exception $e) {
    // Rollback if something goes wrong
    mysqli_rollback($db);
    echo "<p style='color:red; font-weight:bold;'>
            ❌ Error: {$e->getMessage()}
          </p>";
}

// Close connection
mysqli_close($db);
