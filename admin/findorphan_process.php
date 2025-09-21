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

		require_once __DIR__ . '/../autoload.php'; // adjust the ../ if necessary depending on your source path.
		use Orphan\MyOrphan;
		
		$result = new MyOrphan();
		$stmt = $result->MyOrphan_find();
		
		echo "<p>" . $stmt['message'] . "</p>";
		
			
		
