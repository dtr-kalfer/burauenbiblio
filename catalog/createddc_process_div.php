<?php
/* 
 * This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 * -- F. Tumulak
 */
// Guard Doggy
		require_once("../shared/guard_doggy.php");

		require_once __DIR__ . '/../autoload.php'; // adjust the ../ if necessary depending on your source path.
		use DDC\DDC2;

		$ddc = new DDC2();

		$result = $ddc->process_ddc2();
		
		if ($result['success']) {
		echo "<p>{$result['message']}</p>";
		echo "<p>Total records inserted: <strong>{$result['count']}</strong></p>";
		} else {
				echo "<p style='color:red;'>{$result['message']}</p>";
		}
?>
