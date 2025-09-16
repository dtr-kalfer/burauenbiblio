<?php
namespace DDC;

class DDC3 extends \ConnectDB
{
	public function process_ddc3(): array
    {
        try {
            // Start transaction
            $this->beginTransaction();

						// 4. Update classification based on DDC ranges
						require_once __DIR__ . '/../../catalog/ddc_wiki/adv_sql_ddc.php';
						
						$this->execute($updateSQL);
						
            // Commit transaction
            $this->commit();						
						
						// Get number of rows inserted for feedback
						$count = $this->selectValue("SELECT COUNT(*) AS total FROM extract_ddc");

								return [
										'success' => true,
										'message' => "<p>✅ Successfully created and populated <strong>DDC Level III Topic Mapping</strong> table. See DDC Top 30 List.</p>",
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