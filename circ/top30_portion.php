<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
		require_once("../shared/guard_doggy.php");
		
	  // Assuming $pdo is your PDO connection and this query was prepared
    $stmt = $pdo->query("
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

			WHERE b.out_dt BETWEEN DATE_SUB(CURDATE(), INTERVAL 6 MONTH) AND CURDATE()

			GROUP BY titles.subfield_data, authors.subfield_data, isbns.subfield_data
			ORDER BY checkout_count DESC
			LIMIT 30;
    ");

    $rank = 1;
    foreach ($stmt as $row): ?>
      <tr>
        <td><?= $rank++ ?></td>
        <td><?= htmlspecialchars($row['title']) ?></td>
				<td><?= htmlspecialchars($row['author']) ?></td>
				<td><?= htmlspecialchars($row['isbn']) ?></td>
        <td><?= $row['checkout_count'] ?></td>
      </tr>
	<?php endforeach; ?>

  </tbody>
</table>
