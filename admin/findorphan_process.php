<?php
/*
 * Orphaned Biblios Cleanup Script for OpenBiblio
 * ---------------------------------------------
 * Checks all biblio records that have no associated copies.
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
        die("Connection failed: " . mysqli_connect_error());
    }

	try {
			// Start transaction for safety
			$db->query("START TRANSACTION");

			// 1. Create sql to count number or orphaned bibs
			$countOrphan = "
				SELECT COUNT(*) AS orphaned_bibs
				FROM biblio AS b
				LEFT JOIN biblio_copy AS bc
							 ON b.bibid = bc.bibid
				WHERE bc.copyid IS NULL;
						";

			// Get number of rows inserted for feedback
			$result = $db->query($countOrphan);
			$row = $result->fetch_assoc();
			$count = $row['orphaned_bibs'];
			
	    // Commit transaction
			mysqli_commit($db);

			echo "<p>Total orphaned biblios found: <strong>{$count}</strong></p>";
			
	} catch (Exception $e) {
    // Rollback if something goes wrong
    mysqli_rollback($db);
    echo "<p style='color:red; font-weight:bold;'>
            ❌ Error: {$e->getMessage()}
          </p>";
	}

// Close connection
mysqli_close($db);
