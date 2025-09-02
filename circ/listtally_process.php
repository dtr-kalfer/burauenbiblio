<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details. --F.Tumulak
 */
require_once("../shared/guard_doggy.php"); // mini CSRF protection
require_once("../catalog/class/Qtest.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $mypass = new Qtest;
    $connection = mysqli_connect(
        $mypass->getDSN2("host"),
        $mypass->getDSN2("username"),
        $mypass->getDSN2("pwd"),
        $mypass->getDSN2("database")
    );

    if (mysqli_connect_errno()) {
        echo "<span style='color:red;'>Connection failed: " . mysqli_connect_error() . "</span>";
        exit;
    }

    // ✅ Fetch today's scanned books
    $query = "
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

    // ✅ Use query() instead of prepare() since no input is bound
    $result = $connection->query($query);

    if (!$result) {
        echo "<span style='color:red;'>Query failed: " . $connection->error . "</span>";
        exit;
    }

    $count = 1;

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
		while ($row = $result->fetch_assoc()) {
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
    $connection->close();
}
?>
