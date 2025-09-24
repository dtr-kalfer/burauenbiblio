<?php

namespace Circ_Analytics;

use DateTime;
use DatePeriod;
use DateInterval;

class Circ_Analytics extends \ConnectDB {

    /**
     * Validate YYYY-MM month format (e.g., 2025-09)
     */
    public function isValidMonthFormat(string $month): bool {
        return preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $month);
    }

    /**
     * Generate month list between start and end
     */
    private function generateMonthList(string $startMonth, string $endMonth): array {
        $start = new DateTime($startMonth);
        $end   = new DateTime($endMonth);

        $start->modify('first day of this month');
        $end->modify('first day of next month');

        $monthList = [];
        $period = new DatePeriod($start, new DateInterval('P1M'), $end);
        foreach ($period as $dt) {
            $monthList[] = $dt->format('Y-m');
        }
        return $monthList;
    }

    /**
     * Build inline UNION query part
     */
    private function buildMonthQuery(array $monthList): string {
        $parts = array_map(fn($m) => "SELECT '{$m}' AS month", $monthList);
        return implode(" UNION ALL ", $parts);
    }

    /**
     * Core fetcher: return rows with month, total_checkouts, total_checkins
     */
    private function fetchData(string $startMonth, string $endMonth): array {
        $monthList   = $this->generateMonthList($startMonth, $endMonth);
        $monthQuery  = $this->buildMonthQuery($monthList);

        $sql = "
            SELECT
                ym.month,
                COALESCE(c.total_checkouts, 0) AS total_checkouts,
                COALESCE(r.total_checkins, 0) AS total_checkins
            FROM
                ( $monthQuery ) AS ym
            LEFT JOIN (
                SELECT DATE_FORMAT(out_dt, '%Y-%m') AS month, COUNT(*) AS total_checkouts
                FROM booking
                WHERE out_dt IS NOT NULL
                GROUP BY month
            ) AS c ON ym.month = c.month
            LEFT JOIN (
                SELECT DATE_FORMAT(ret_dt, '%Y-%m') AS month, COUNT(*) AS total_checkins
                FROM booking
                WHERE ret_dt IS NOT NULL
                GROUP BY month
            ) AS r ON ym.month = r.month
            ORDER BY ym.month;
        ";

        return $this->select($sql); // ✅ uses ConnectDB::select()
    }

    /**
     * Chart.js JSON format
     */
    public function getChartDataJSON(string $startMonth, string $endMonth): string {
        $rows = $this->fetchData($startMonth, $endMonth);

        $labels    = [];
        $checkouts = [];
        $checkins  = [];

        foreach ($rows as $row) {
            $labels[]    = $row['month'];
            $checkouts[] = (int)$row['total_checkouts'];
            $checkins[]  = (int)$row['total_checkins'];
        }

        return json_encode([
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Checkouts',
                    'data' => $checkouts,
                    'backgroundColor' => '#007bff'
                ],
                [
                    'label' => 'Check-ins',
                    'data' => $checkins,
                    'backgroundColor' => '#28a745'
                ]
            ]
        ]);
    }

    /**
     * Simple JSON array format
     */
    public function getDataJSON(string $startMonth, string $endMonth): string {
        $rows = $this->fetchData($startMonth, $endMonth);

        $results = [];
        foreach ($rows as $row) {
            $results[] = [
                'month'     => $row['month'],
                'checkouts' => (int)$row['total_checkouts'],
                'checkins'  => (int)$row['total_checkins']
            ];
        }
        return json_encode($results);
    }
}
