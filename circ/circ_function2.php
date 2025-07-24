<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
 
function getChartDataJSON(PDO $pdo, string $startMonth, string $endMonth): string {
    $start = new DateTime($startMonth);
    $end = new DateTime($endMonth);

    // Normalize start to first day of month and end to last day of month
    $start->modify('first day of this month');
    $end->modify('first day of next month'); // so loop stops at proper last month

    $monthList = [];
    $period = new DatePeriod($start, new DateInterval('P1M'), $end);
    foreach ($period as $dt) {
        $monthList[] = $dt->format('Y-m');
    }

    // Build inline UNION SELECT
    $monthQueryParts = array_map(function($m) {
        return "SELECT '{$m}' AS month";
    }, $monthList);
    $monthQuery = implode(" UNION ALL ", $monthQueryParts);

    // SQL with dynamic list of months
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

    $stmt = $pdo->query($sql);
    $labels = [];
    $checkouts = [];
    $checkins = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $labels[] = $row['month'];
        $checkouts[] = (int)$row['total_checkouts'];
        $checkins[] = (int)$row['total_checkins'];
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
