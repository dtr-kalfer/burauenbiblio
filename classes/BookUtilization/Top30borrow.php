<?php
	/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
	 * See the file COPYRIGHT.html for more details.
	 */
namespace BookUtilization;

class Top30borrow extends \ConnectDB
{
    public function make_top30borrowlist(?string $selectedMonth = null, $limit = 30): array
    {
        try {
            // Determine date filtering logic
            if (!empty($selectedMonth) && preg_match('/^\d{4}-\d{2}$/', $selectedMonth)) {
                $startDate = $selectedMonth . "-01 00:00:00";
                $endDate   = date("Y-m-t 23:59:59", strtotime($startDate));

                $dateClause = "b.out_dt BETWEEN '{$startDate}' AND '{$endDate}'";
            } else {
                // Default fallback: Last 6 months
                $dateClause = "b.out_dt BETWEEN DATE_SUB(CURDATE(), INTERVAL 6 MONTH) AND NOW()";
            }

            // Determine limit clause
            $limitClause = "";
            if ($limit !== 'all' && (int)$limit > 0) {
                $cleanLimit = (int)$limit;
                $limitClause = "LIMIT {$cleanLimit}";
            }

            $sql = "
                SELECT 
                    titles.subfield_data AS title,
                    authors.subfield_data AS author,
                    isbns.subfield_data AS isbn,
                    COUNT(*) AS checkout_count
                FROM booking b
                JOIN biblio_field bf_title ON bf_title.bibid = b.bibid AND bf_title.tag = '245'
                JOIN biblio_subfield titles ON titles.fieldid = bf_title.fieldid AND titles.subfield_cd = 'a'

                LEFT JOIN biblio_field bf_author ON bf_author.bibid = b.bibid AND bf_author.tag = '100'
                LEFT JOIN biblio_subfield authors ON authors.fieldid = bf_author.fieldid AND authors.subfield_cd = 'a'

                LEFT JOIN biblio_field bf_isbn ON bf_isbn.bibid = b.bibid AND bf_isbn.tag = '020'
                LEFT JOIN biblio_subfield isbns ON isbns.fieldid = bf_isbn.fieldid AND isbns.subfield_cd = 'a'

                WHERE {$dateClause}
                GROUP BY titles.subfield_data, authors.subfield_data, isbns.subfield_data
                ORDER BY checkout_count DESC
                {$limitClause};
            ";

            $data = $this->select($sql);

            return [
                'success' => true,
                'content' => $data,
                'message' => '✅ Retrieved from: Circulation activity'
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'content' => [],
                'message' => '❌ Error: ' . $e->getMessage()
            ];
        }
    }
}