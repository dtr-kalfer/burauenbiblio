<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details. - F. Tumulak
 */
// download.php
$id = $_GET['id'] ?? null;

if (!$id) {
    die("Missing book ID.");
}

// Fetch book data from Gutendex
$apiUrl = "https://gutendex.com/books/{$id}/";
$response = @file_get_contents($apiUrl);
if (!$response) {
    die("Error fetching book info.");
}

$data = json_decode($response, true);
$formats = $data['formats'] ?? [];

// Prefer EPUB → TXT → MOBI
$preferredOrder = [
    'application/epub+zip',
    'text/plain; charset=utf-8',
    'text/plain; charset=us-ascii',
    'application/x-mobipocket-ebook'
];

$downloadUrl = null;
$ext = 'dat'; // fallback

foreach ($preferredOrder as $mime) {
    if (!empty($formats[$mime])) {
        $downloadUrl = $formats[$mime];
        switch ($mime) {
            case 'application/epub+zip': $ext = 'epub'; break;
            case 'application/x-mobipocket-ebook': $ext = 'mobi'; break;
            case 'text/plain; charset=utf-8':
            case 'text/plain; charset=us-ascii': $ext = 'txt'; break;
        }
        break;
    }
}

if (!$downloadUrl) {
    echo "Format not available.<br><pre>";
    print_r(array_keys($formats));
    echo "</pre>";
    exit;
}

// Get book title for filename
$title = $data['title'] ?? "GutenbergBook";

// Clean up title for filesystem safety
$title = preg_replace('/[^\w\s\-\(\)\[\]\.,_]/u', '', $title); // remove special chars
$title = preg_replace('/\s+/', ' ', $title); // normalize spaces
$title = trim($title);
if ($title === '') $title = 'Book_' . $id;

// Build final filename
$filename = "{$title}.{$ext}";

// Clean and normalize download URL just in case
$filename = str_replace(['..', '/', '\\'], '', $filename);

// Force browser to download
header("Content-Type: application/octet-stream");
header("Content-Disposition: attachment; filename=\"" . $filename . "\"");
readfile($downloadUrl);
exit;
?>
