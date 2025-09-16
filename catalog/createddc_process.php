<?php
/* 
 * This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 * 
 * DDC Classification Mapping (Level 1)
 * Source: Wikipedia - Dewey Decimal Classification
 * https://en.wikipedia.org/wiki/List_of_Dewey_Decimal_classes
 *
 * This file references materials licensed under the
 * Creative Commons Attribution-ShareAlike 4.0 International License.
 * License details: https://creativecommons.org/licenses/by-sa/4.0/legalcode
 *
 * -- F. Tumulak
 */
// Guard Doggy
		require_once("../shared/guard_doggy.php");

	require_once __DIR__ . '/../autoload.php'; // adjust the ../ if necessary depending on your source path.
	use DDC\DDC1;
		
	$ddc = new DDC1();
	$result = $ddc->process_ddc1();

	if ($result['success']) {
			echo "<p>{$result['message']}</p>";
			echo "<p>Total records inserted: <strong>{$result['count']}</strong></p>";
			echo "<p>🎉 You can now view the DDC stats graph!</p>";
	} else {
			echo "<p style='color:red;'>{$result['message']}</p>";
	}
	
?>
