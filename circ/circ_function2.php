<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
function getChartDataJSON(PDO $pdo): string {
    // You can replace this $monthQuery with your own dynamic SQL generator
    $monthQuery = "
        SELECT DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL n MONTH), '%Y-%m') AS month
        FROM (
            SELECT 0 AS n UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 
            UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION SELECT 10 UNION SELECT 11
        ) AS months
    ";

    // Complete SQL query with $monthQuery injected
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

    // Output JSON structure compatible with Chart.js
    $chartData = [
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
    ];

    return json_encode($chartData);
}
