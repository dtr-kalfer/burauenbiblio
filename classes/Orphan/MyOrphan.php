<?php
	/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
	 * See the file COPYRIGHT.html for more details. --F.Tumulak
	 */
namespace Orphan;

class MyOrphan extends \ConnectDB
{
    
    public function MyOrphan_find()
    {
        try {
            
            $sql = "
							SELECT COUNT(*) AS orphaned_bibs
							FROM biblio AS b
							LEFT JOIN biblio_copy AS bc
										 ON b.bibid = bc.bibid
							WHERE bc.copyid IS NULL;
            ";
						
						$stmt = $this->selectValue($sql);
            return 
						[
							'success' => true,
							'message' => "✅ Total orphaned biblios found: " . $stmt
						];

        } catch (\Exception $e) {
								$this->rollback();
								return [
										'success' => false,
										'message' => '❌ Error: ' . $e->getMessage()
								];
        }
    }
		
    public function MyOrphan_remove()
    {
        try {
            
            $sql = "
						  DELETE b
							FROM biblio AS b
							LEFT JOIN biblio_copy AS bc
										 ON b.bibid = bc.bibid
							WHERE bc.copyid IS NULL
            ";

						$stmt = $this->execute($sql,"",[],true);
            return 
						[
						'success' => true,
						'message' => "✅ Deleted orphans in Biblio Records: " . $stmt
						];

        } catch (\Exception $e) {
								$this->rollback();
								return [
										'success' => false,
										'message' => '❌ Error: ' . $e->getMessage()
								];
        }
    }		
		
}
