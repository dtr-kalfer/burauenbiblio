<?php
	/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
	 * See the file COPYRIGHT.html for more details. --F.Tumulak
	 */
namespace DDC;

class DDC_Chart extends \ConnectDB
{
    /**
     * Fetch top 30 DDCs and prepare chart data
     * 
     * @return array|false Returns structured chart data or false on failure
     */
    public function make_chart()
    {
        try {
            // Query: Top 30 DDCs
            $sql = "
                SELECT ddc, classification, COUNT(*) AS total
                FROM extract_ddc
                GROUP BY ddc, classification
                ORDER BY total DESC
                LIMIT 30
            ";

            $rows = $this->select($sql);

            // If no rows, return empty arrays instead of error
            if (!$rows) {
                return [
                    "labels"          => [],
                    "totals"          => [],
                    "classifications" => []
                ];
            }

            $labels = [];
            $totals = [];
            $classifications = [];

            foreach ($rows as $row) {
                $labels[]          = $row['ddc'];
                $totals[]          = (int)$row['total'];
                $classifications[] = $row['classification'];
            }

            return [
                "labels"          => $labels,
                "totals"          => $totals,
                "classifications" => $classifications
            ];

        } catch (\Exception $e) {
            // $this->rollback();
            // You might log $e->getMessage() here
            return false;
        }
    }
}
