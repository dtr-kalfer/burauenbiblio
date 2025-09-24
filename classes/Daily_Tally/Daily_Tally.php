<?php
namespace Daily_Tally;

class Daily_Tally extends \ConnectDB
{
    public function tally_process(string $barcode, string $username = "guest"): array
    {
        try {
            $this->beginTransaction();

            // ✅ Step 1: Check if barcode exists
            $sql = "SELECT bibid FROM biblio_copy WHERE barcode_nmbr = ?";
            $rows = $this->selectOne($sql, "s", [$barcode]);

            if (empty($rows)) {
                $this->rollback();
                return [
                    'success' => false,
                    'message' => "❌ Barcode not found.",
                ];
            }

            $bibid = $rows['bibid'];

            // ✅ Step 2: Insert activity log
            $sql = "INSERT INTO obib_book_activity (bibid, barcode, session_user) VALUES (?, ?, ?)";
            $stmt = $this->execute($sql, "iss", [$bibid, $barcode, $username]);

            $this->commit(); // commit transaction

            return [
                'success' => true,
                'message' => "✅ Daily tally insert success.",
                'data'    => [
                    'bibid'   => $bibid,
                    'barcode' => $barcode,
                    'user'    => $username,
                ]
            ];

        } catch (\Exception $e) {
            $this->rollback();
            return [
                'success' => false,
                'message' => "❌ Error: " . $e->getMessage(),
            ];
        }
    }
		
		public function get_today_log(): array
    {
        try {
            $sql = "
                SELECT 
                    ba.barcode,
                    ba.bibid,
                    COALESCE(titles.subfield_data, '[Untitled]') AS title,
                    COALESCE(authors.subfield_data, '[Unknown Author]') AS author,
                    ba.activity_time,
                    ba.session_user
                FROM obib_book_activity AS ba
                JOIN biblio_field AS bf_title
                    ON bf_title.bibid = ba.bibid
                    AND bf_title.tag = '245'
                JOIN biblio_subfield AS titles
                    ON titles.fieldid = bf_title.fieldid
                    AND titles.subfield_cd = 'a'
                LEFT JOIN biblio_field AS bf_author
                    ON bf_author.bibid = ba.bibid
                    AND bf_author.tag = '100'
                LEFT JOIN biblio_subfield AS authors
                    ON authors.fieldid = bf_author.fieldid
                    AND authors.subfield_cd = 'a'
                WHERE DATE(ba.activity_time) = CURDATE()
                ORDER BY ba.activity_time DESC
            ";

            return $this->select($sql); // ✅ reuse parent select()
            
        } catch (\Exception $e) {
            return [
                'error' => true,
                'message' => "❌ Error: " . $e->getMessage()
            ];
        }
    }		
		
}
