<?php
namespace DDC;

class DDC1 extends \ConnectDB
{
	public function process_ddc1(): array
    {
        try {
            // Start transaction
            $this->beginTransaction();

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
            $this->execute($createTableSQL);

            // 2. Clear old data
            $this->execute("DELETE FROM extract_ddc");

            // 3. Insert new data
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
            $this->execute($insertSQL);

            // 4. Update classification ranges
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
            $this->execute($updateSQL);

            // Commit transaction
            $this->commit();
						
						$count = $this->selectValue("SELECT COUNT(*) AS total FROM extract_ddc");
            
            return [
										'success' => true,
										'message' => "✅ Successfully processed DDC table.",
										'count'   => $count
										];

						} catch (\Exception $e) {
								$this->rollback();
								return [
										'success' => false,
										'message' => "❌ Error: " . $e->getMessage()
								];
						}

	}
}