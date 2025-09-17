<?php
	/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
	 * See the file COPYRIGHT.html for more details. --F.Tumulak
	 */
namespace DDC;

class DDC_Toplist extends \ConnectDB
{
    
    public function make_toplist()
    {
        try {
            // Query: Top 30 DDCs
            $sql = "
							SELECT *
							FROM (
								SELECT ddc, classification AS main, classification_div AS division, classification_adv AS topic, COUNT(*) AS total
								FROM extract_ddc
								GROUP BY ddc
								ORDER BY total DESC
								LIMIT 30
								) AS top_ddc
							ORDER BY ddc ASC
            ";

            return $this->select($sql);

        } catch (\Exception $e) {
								$this->rollback();
								return [
										'success' => false,
										'message' => "❌ Error: " . $e->getMessage()
								];
        }
    }
}
