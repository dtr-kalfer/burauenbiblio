<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once("../shared/common.php");
$tab = "cataloging";
$nav = "missing_thumbnails";	

require_once(REL(__FILE__, "../shared/logincheck.php"));

Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));

require_once("../catalog/class/Qtest.php");

$mypass = new Qtest;

try {
    $dsn = 'mysql:host=' . $mypass->getDSN2("host") . ';dbname=' . $mypass->getDSN2("database") . ';charset=utf8mb4';
    $pdo = new PDO($dsn, $mypass->getDSN2("username"), $mypass->getDSN2("pwd"));

    // Enable exception mode
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    die("PDO Connection failed: " . $e->getMessage());
}

$sql = "SELECT bibid, imgurl, url, type FROM images";
$stmt = $pdo->query($sql);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Results container
$missingThumbs = [];

foreach ($results as $row) {
    $bibid = $row['bibid'];
    $imgurl = $row['imgurl'];
    $url = $row['url'];
    $type = $row['type'];

    $filename = basename($url);
    $fullpath = '';
    $issue = [];

    // Is URL malformed? (e.g. missing "../photos/")
    if (strpos($url, '../photos/') !== 0) {
        $issue[] = 'Faulty URL';
        $fullpath = __DIR__ . '/../photos/' . $filename;
    } else {
        $fullpath = __DIR__ . '/' . $url;
    }

    // Does file exist?
    if (!file_exists($fullpath)) {
        $issue[] = 'Missing file';
    }

    // Add to report if any issue found
    if (!empty($issue)) {
        $missingThumbs[] = [
            'bibid' => $bibid,
            'imgurl' => $imgurl,
            'url' => $url,
            'expected_path' => $fullpath,
            'issues' => implode(', ', $issue),
            'type' => $type,
        ];
    }
}

// Output results
echo "<h2 style='width: 750px; background-color: red;'>" . T("bad url or missing files") . "</h2>";
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>Count</th><th>BibID</th><th>imgurl</th><th>url</th><th>issues</th></tr>";
$somecounter = 0;
foreach ($missingThumbs as $row) {
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