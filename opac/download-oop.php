<?php
		/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
		 * See the file COPYRIGHT.html for more details. --F.Tumulak
		 */
require_once(__DIR__ . "/../classes/GutendexClient.php");

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) die("Missing book ID.");

$client = new GutendexClient();
$book = $client->getBook($id);

if (!$book) die("Error fetching book info.");

$formats = $book['formats'] ?? [];
$preferred = $client->getPreferredDownload($formats);

if (!$preferred) {
    echo "Format not available.<br><pre>";
    print_r(array_keys($formats));
    echo "</pre>";
    exit;
}

$title = $book['title'] ?? "GutenbergBook";
$title = $client->sanitizeFilename($title, $id);
$filename = "{$title}.{$preferred['ext']}";
$url = $preferred['url'];

header("Content-Type: application/octet-stream");
header("Content-Disposition: attachment; filename=\"" . $filename . "\"");
readfile($url);
exit;
?>
