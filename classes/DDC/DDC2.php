<?php
namespace DDC;

class DDC2 extends \ConnectDB
{
	public function process_ddc2(): array
    {
        try {
            // Start transaction
            $this->beginTransaction();

						// 4. Update classification based on DDC ranges
						require_once __DIR__ . '/../../catalog/ddc_wiki/div_sql_ddc.php';
						
						$this->execute($updateSQL);
						
            // Commit transaction
            $this->commit();						
						
						// Get number of rows inserted for feedback
						$count = $this->selectValue("SELECT COUNT(*) AS total FROM extract_ddc");

								return [
										'success' => true,
										'message' => "<p>✅ Successfully created and populated <strong>DDC Level II Division Mapping</strong> table.</p>",
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