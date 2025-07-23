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
        CONCAT(titles.subfield_data, ' ', IFNULL(sub.subfield_data, '')) AS title,
        COUNT(*) AS checkout_count
      FROM booking b
      JOIN booking_member bkm ON bkm.bookingid = b.bookingid
      JOIN member m ON m.mbrid = bkm.mbrid
      JOIN biblio_copy bc ON bc.histid = b.out_histid
      JOIN biblio_field titlef ON titlef.bibid = b.bibid
      JOIN biblio_subfield titles ON titles.fieldid = titlef.fieldid
      LEFT JOIN biblio_subfield sub ON sub.fieldid = titlef.fieldid AND sub.subfield_cd = 'b'
      WHERE 
        (
          (titlef.tag='240' AND titles.subfield_cd='a') OR
          (titlef.tag='245' AND titles.subfield_cd='a') OR
          (titlef.tag='246' AND titles.subfield_cd='a') OR
          (titlef.tag='773' AND titles.subfield_cd='a') OR
          (titlef.tag='773' AND titles.subfield_cd='t')
        )
        AND b.out_dt BETWEEN CURDATE() - INTERVAL 6 MONTH AND CURDATE()
      GROUP BY title
      ORDER BY checkout_count DESC
      LIMIT 30
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
