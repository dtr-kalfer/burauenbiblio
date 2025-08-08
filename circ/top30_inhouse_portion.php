<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
		require_once("../shared/guard_doggy.php");
		
	  // Assuming $pdo is your PDO connection and this query was prepared
    $stmt = $pdo->query("
			SELECT 
				titles.subfield_data AS title,
				COUNT(*) AS view_count
			FROM obib_book_activity ba
			JOIN biblio_field bf ON bf.bibid = ba.bibid AND bf.tag = '245'
			JOIN biblio_subfield titles ON titles.fieldid = bf.fieldid AND titles.subfield_cd = 'a'
			WHERE ba.activity_time BETWEEN DATE_SUB(CURDATE(), INTERVAL 1 MONTH) AND NOW()
			GROUP BY titles.subfield_data
			ORDER BY view_count DESC
			LIMIT 30;
    ");

    $rank = 1;
    foreach ($stmt as $row): ?>
      <tr>
        <td><?= $rank++ ?></td>
        <td class="topbooks"><?= htmlspecialchars($row['title']) ?></td>
        <td><?= $row['view_count'] ?></td>
      </tr>
	<?php endforeach; ?>

  </tbody>
</table>
