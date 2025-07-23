<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
		if (!isset($_SESSION['guard_token_key'])) {
			  http_response_code(403);
        echo "⛔ Access denied!";
        exit;;
		} 
	  // Assuming $pdo is your PDO connection and this query was prepared
    $stmt = $pdo->query("
			SELECT 
				titles.subfield_data AS title,
				COUNT(*) AS checkout_count
			FROM booking b
			JOIN biblio_field bf ON bf.bibid = b.bibid AND bf.tag = '245'
			JOIN biblio_subfield titles ON titles.fieldid = bf.fieldid AND titles.subfield_cd = 'a'
			WHERE b.out_dt BETWEEN DATE_SUB(CURDATE(), INTERVAL 6 MONTH) AND CURDATE()
			GROUP BY titles.subfield_data
			ORDER BY checkout_count DESC
			LIMIT 30;
    ");

    $rank = 1;
    foreach ($stmt as $row): ?>
      <tr>
        <td><?= $rank++ ?></td>
        <td class="topbooks"><?= htmlspecialchars($row['title']) ?></td>
        <td><?= $row['checkout_count'] ?></td>
      </tr>
	<?php endforeach; ?>

  </tbody>
</table>
