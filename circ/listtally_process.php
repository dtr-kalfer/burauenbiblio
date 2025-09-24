<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details. --F.Tumulak
 */
require_once("../shared/guard_doggy.php"); // mini CSRF protection

require_once __DIR__ . '/../autoload.php'; // adjust the ../ if necessary depending on your source path.
use Daily_Tally\Daily_Tally;

$log = new Daily_Tally();
$rows = $log->get_today_log();

if (isset($rows['error'])) {
    echo "<span style='color:red;'>{$rows['message']}</span>";
    exit;
}

if (empty($rows)) {
    echo "<p style='text-align:center;'>No activities recorded today.</p>";
    exit;
}

// ✅ Render table
echo '<table align="left" border="1" cellspacing="0" cellpadding="5" width="100%" style="border-collapse:collapse; font-family:Arial; font-size:14px;">';
echo '<thead style="background-color:#f2f2f2; text-align:left;">';
echo '<tr>';
echo '<th>#</th>';
echo '<th>Title</th>';
echo '<th>Author</th>';
echo '<th>Barcode</th>';
echo '<th>Activity Time</th>';
echo '</tr>';
echo '</thead>';
echo '<tbody>';

$count = 1;
foreach ($rows as $row) {
    echo "<tr>";
    echo "<td>" . $count++ . "</td>";
    echo "<td>" . htmlspecialchars($row['title']) . "</td>";
    echo "<td>" . htmlspecialchars($row['author']) . "</td>";
    echo "<td>" . htmlspecialchars($row['barcode']) . "</td>";
    echo "<td>" . htmlspecialchars($row['activity_time']) . "</td>";
    echo "</tr>";
}

echo '</tbody>';
echo '</table>';
