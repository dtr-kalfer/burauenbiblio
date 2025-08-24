<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details. --F.Tumulak
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
            classification VARCHAR(100)
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

    // 4. Update classification based on DDC ranges
    $updateSQL = "
        UPDATE extract_ddc
        SET classification = CASE
            WHEN CAST(CONCAT(SUBSTRING_INDEX(ddc, '.', 1), '.', LEFT(SUBSTRING_INDEX(ddc, '.', -1), 3)) AS DECIMAL(6,3)) BETWEEN 000.000 AND 099.999 THEN 'General Works'
            WHEN CAST(CONCAT(SUBSTRING_INDEX(ddc, '.', 1), '.', LEFT(SUBSTRING_INDEX(ddc, '.', -1), 3)) AS DECIMAL(6,3)) BETWEEN 100.000 AND 199.999 THEN 'Philosophy and Psychology'
            WHEN CAST(CONCAT(SUBSTRING_INDEX(ddc, '.', 1), '.', LEFT(SUBSTRING_INDEX(ddc, '.', -1), 3)) AS DECIMAL(6,3)) BETWEEN 200.000 AND 299.999 THEN 'Religion'
            WHEN CAST(CONCAT(SUBSTRING_INDEX(ddc, '.', 1), '.', LEFT(SUBSTRING_INDEX(ddc, '.', -1), 3)) AS DECIMAL(6,3)) BETWEEN 300.000 AND 399.999 THEN 'Social Sciences'
            WHEN CAST(CONCAT(SUBSTRING_INDEX(ddc, '.', 1), '.', LEFT(SUBSTRING_INDEX(ddc, '.', -1), 3)) AS DECIMAL(6,3)) BETWEEN 400.000 AND 499.999 THEN 'Language'
            WHEN CAST(CONCAT(SUBSTRING_INDEX(ddc, '.', 1), '.', LEFT(SUBSTRING_INDEX(ddc, '.', -1), 3)) AS DECIMAL(6,3)) BETWEEN 500.000 AND 599.999 THEN 'Science'
            WHEN CAST(CONCAT(SUBSTRING_INDEX(ddc, '.', 1), '.', LEFT(SUBSTRING_INDEX(ddc, '.', -1), 3)) AS DECIMAL(6,3)) BETWEEN 600.000 AND 699.999 THEN 'Technology'
            WHEN CAST(CONCAT(SUBSTRING_INDEX(ddc, '.', 1), '.', LEFT(SUBSTRING_INDEX(ddc, '.', -1), 3)) AS DECIMAL(6,3)) BETWEEN 700.000 AND 799.999 THEN 'Arts and Recreation'
            WHEN CAST(CONCAT(SUBSTRING_INDEX(ddc, '.', 1), '.', LEFT(SUBSTRING_INDEX(ddc, '.', -1), 3)) AS DECIMAL(6,3)) BETWEEN 800.000 AND 899.999 THEN 'Literature'
            WHEN CAST(CONCAT(SUBSTRING_INDEX(ddc, '.', 1), '.', LEFT(SUBSTRING_INDEX(ddc, '.', -1), 3)) AS DECIMAL(6,3)) BETWEEN 900.000 AND 999.999 THEN 'History and Geography'
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

    echo "<p>✅ Successfully created and populated <strong>DDC</strong> table.</p>";
    echo "<p>Total records inserted: <strong>{$count}</strong></p>";
    echo "<p>🎉 You can now view the DDC stats graph!</p>";

} catch (Exception $e) {
    // Rollback if something goes wrong
    $db->query("ROLLBACK");
    echo "<p style='color:red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?>
