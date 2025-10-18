<?php
		/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
		 * See the file COPYRIGHT.html for more details. --F.Tumulak
		 */
// classes/GutendexClient.php
class GutendexClient {
    private string $apiBase = "https://gutendex.com/books/";

    public function search(string $query): array {
        $url = $this->apiBase . "?search=" . urlencode($query);
        $response = @file_get_contents($url);
        if (!$response) return [];
        $data = json_decode($response, true);
        return $data['results'] ?? [];
    }

    public function getBook(int $id): ?array {
        $url = $this->apiBase . "{$id}/";
        $response = @file_get_contents($url);
        if (!$response) return null;
        return json_decode($response, true);
    }

    public function getPreferredDownload(array $formats): ?array {
        $preferredOrder = [
            'application/epub+zip' => 'epub',
            'text/plain; charset=utf-8' => 'txt',
            'text/plain; charset=us-ascii' => 'txt',
            'application/x-mobipocket-ebook' => 'mobi'
        ];

        foreach ($preferredOrder as $mime => $ext) {
            if (!empty($formats[$mime])) {
                return [
                    'url' => $formats[$mime],
                    'ext' => $ext
                ];
            }
        }
        return null;
    }

    public function sanitizeFilename(string $title, int $id): string {
        $title = preg_replace('/[^\w\s\-\(\)\[\]\.,_]/u', '', $title);
        $title = preg_replace('/\s+/', ' ', $title);
        $title = trim($title);
        if ($title === '') $title = 'Book_' . $id;
        return str_replace(['..', '/', '\\'], '', $title);
    }
}
?>
