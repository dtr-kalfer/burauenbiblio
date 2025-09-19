	<?php
	/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
	 * See the file COPYRIGHT.html for more details.
	 */
		require_once("../shared/guard_doggy.php");
		
		if ($stmt['success'] === true) {
		?>
			<p style="text-align: center;"><?= $stmt['message'] ?></p>
		<?php
			$rank = 1;
			foreach ($stmt['content'] as $row): 
	?>
		<tr>
			<td><?= $rank++ ?></td>
			<td><?= htmlspecialchars($row['title']) ?></td>
			<td><?= htmlspecialchars($row['author']) ?></td>
			<td><?= htmlspecialchars($row['isbn']) ?></td>
			<td><?= $row['view_count'] ?></td>
		</tr>
	<?php endforeach; 
		} else {
			echo $stmt['message'];
		}
	?>

  </tbody>
</table>
