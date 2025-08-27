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

    // 1. Create extract_ddc table if not exists
    $createTableSQL = "
        CREATE TABLE IF NOT EXISTS extract_ddc (
            bibid INT NOT NULL,
            barcode_nmbr VARCHAR(20),
            ddc VARCHAR(20),
            classification VARCHAR(100),
						classification_div VARCHAR(150),
						classification_adv VARCHAR(150)
        )
    ";
    $db->query($createTableSQL);

    // 2. Delete existing data in extract_ddc
    $db->query("DELETE FROM extract_ddc");

    // 3. Insert new data into extract_ddc
    $insertSQL = "
        INSERT INTO extract_ddc (bibid, barcode_nmbr, ddc)
        SELECT 
            bc.bibid, 
            bc.barcode_nmbr, 
            TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(bs.field_entry, ' ', 2), ' ', -1)) AS ddc
        FROM biblio_copy bc
        JOIN (
            SELECT 
                bibid,
                GROUP_CONCAT(subfield_data ORDER BY subfield_data SEPARATOR ' ') AS field_entry
            FROM biblio_subfield
            WHERE (bibid, fieldid) IN (
                SELECT bibid, MIN(fieldid) 
                FROM biblio_subfield
                GROUP BY bibid
            )
            GROUP BY bibid
        ) bs ON bc.bibid = bs.bibid
    ";
    $db->query($insertSQL);

    // 4. Update classification based on DDC ranges (https://en.wikipedia.org/wiki/List_of_Dewey_Decimal_classes)
    $updateSQL = "
			UPDATE extract_ddc
			SET classification = CASE
					WHEN LEFT(ddc, 3) BETWEEN '000' AND '099' THEN 'General Works'
					WHEN LEFT(ddc, 3) BETWEEN '100' AND '199' THEN 'Philosophy and Psychology'
					WHEN LEFT(ddc, 3) BETWEEN '200' AND '299' THEN 'Religion'
					WHEN LEFT(ddc, 3) BETWEEN '300' AND '399' THEN 'Social Sciences'
					WHEN LEFT(ddc, 3) BETWEEN '400' AND '499' THEN 'Language'
					WHEN LEFT(ddc, 3) BETWEEN '500' AND '599' THEN 'Science'
					WHEN LEFT(ddc, 3) BETWEEN '600' AND '699' THEN 'Technology'
					WHEN LEFT(ddc, 3) BETWEEN '700' AND '799' THEN 'Arts and Recreation'
					WHEN LEFT(ddc, 3) BETWEEN '800' AND '899' THEN 'Literature'
					WHEN LEFT(ddc, 3) BETWEEN '900' AND '999' THEN 'History and Geography'
					ELSE 'Unclassified'
			END
			WHERE ddc REGEXP '^[0-9]{1,3}(\\.[0-9]+)?$';
    ";
    $db->query($updateSQL);

    // Commit transaction
    $db->query("COMMIT");

    // Get number of rows inserted for feedback
    $result = $db->query("SELECT COUNT(*) AS total FROM extract_ddc");
    $row = $result->fetch_assoc();
    $count = $row['total'];

    echo "<p>✅ Successfully created and populated <strong>DDC Table + Process Level I Main Class Mapping</strong> table.</p>";
    echo "<p>Total records inserted: <strong>{$count}</strong></p>";
    echo "<p>🎉 You can now view the DDC stats graph!</p>";

} catch (Exception $e) {
    // Rollback if something goes wrong
    $db->query("ROLLBACK");
    echo "<p style='color:red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?>
