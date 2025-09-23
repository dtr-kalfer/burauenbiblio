<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details. --Ferdinand Tumulak
 */

require_once("../shared/common.php");
$tab = "cataloging";
$nav = "missing_thumbnails";	

require_once(REL(__FILE__, "../shared/logincheck.php"));

Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));

require_once("../shared/guard_doggy.php");

require_once __DIR__ . '/../autoload.php'; // adjust the ../ if necessary depending on your source path.
use Thumbnail\Thumbnail;

$results = new Thumbnail();

$missingThumbs = $results->thumbnail_find();

echo "<h2 style='width: 750px; background-color: red;'>" . T("bad url or missing files") . "</h2>";

// Output results
if ($missingThumbs['success'] === true) {
	echo "<p style='text-align: center;'>" . $missingThumbs['message'] . "</p>";
?>

	<table border='1' cellpadding='5'>
	<tr><th>Count</th><th>BibID</th><th>imgurl</th><th>url</th><th>issues</th></tr>
	
<?php
	$somecounter = 0;
	foreach ($missingThumbs['content'] as $row) {
			$somecounter += 1;
			echo "<tr>";
			echo "<td>{$somecounter}</td>";
			echo "<td>{$row['bibid']}</td>";
			echo "<td>{$row['imgurl']}</td>";
			echo "<td>{$row['url']}</td>";
			echo "<td>{$row['issues']}</td>";
			echo "</tr>";
	}
	echo "</table>";
	} else {
		echo '<p>' . $missingThumbs['message'] . '</p>';
	}

	$missing_thumbnails = $results->thumbnail_FailtoAdd();
	$count = 0;
	echo "<h3>BibID Records that didn't have image thumbnails (Add thumbnail cancelled):</h3>";
	if (!empty($missing_thumbnails)) {
			
			foreach ($missing_thumbnails as $row) {
					$append = "<br>";
					$count++;
					echo htmlspecialchars($row['bibid']);
					if ($count > 15) {echo "<br>"; $count = 0;} else {echo ", ";}
			}
			
	} else {
			echo "✅ None found!";
	}
	
?>