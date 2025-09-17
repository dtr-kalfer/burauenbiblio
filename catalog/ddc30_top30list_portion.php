<?php
		require_once("../shared/guard_doggy.php");
		$categoryColors = [
				'000' => '#A5D6A7',  // Light green - General Works
				'100' => '#90CAF9',  // Light blue - Philosophy & Psychology
				'200' => '#CE93D8',  // Light purple - Religion
				'300' => '#FFCC80',  // Light orange - Social Sciences
				'400' => '#F48FB1',  // Light pink - Language
				'500' => '#80DEEA',  // Light cyan - Science
				'600' => '#C5E1A5',  // Light green - Technology
				'700' => '#FFE082',  // Light yellow - Arts & Recreation
				'800' => '#9FA8DA',  // Light indigo - Literature
				'900' => '#EF9A9A',  // Light red - History & Geography
				'???' => '#E0E0E0'   // Light grey - Unclassified
		];

		$rank = 1;
		foreach ($stmt as $row):
				// Extract the first three digits, handle possible non-numeric DDCs safely
				$ddcPrefix = substr($row['ddc'], 0, 3);

				// Check if prefix is numeric; otherwise, use fallback color
				if (is_numeric($ddcPrefix)) {
						$ddcGroup = str_pad(floor((int)$ddcPrefix / 100) * 100, 3, '0', STR_PAD_LEFT);
						$bgColor = isset($categoryColors[$ddcGroup]) ? $categoryColors[$ddcGroup] : $categoryColors['???'];
				} else {
						$bgColor = $categoryColors['???'];
				}
		?>
				<tr style="background-color: <?= $bgColor ?>; color: #000;">
						<td><?= $rank++ ?></td>
						<td><?= htmlspecialchars($row['ddc']) ?></td>
						<td><?= htmlspecialchars($row['main']) ?></td>
						<td><?= htmlspecialchars($row['division']) ?></td>
						<td><?= htmlspecialchars($row['topic']) ?></td>
						<td><?= $row['total'] ?></td>
				</tr>
		<?php endforeach; ?>

  </tbody>
</table>
