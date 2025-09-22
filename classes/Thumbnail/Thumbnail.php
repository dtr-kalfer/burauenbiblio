<?php
	/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
	 * See the file COPYRIGHT.html for more details. --F.Tumulak
	 */
namespace Thumbnail;

class Thumbnail extends \ConnectDB
{
    public function thumbnail_find(): array
    {
        try {

				// look for thumbnails
				$sql = "
					SELECT bibid, imgurl, url, type FROM images
				";

				$results = $this->select($sql);

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
										$fullpath = __DIR__ . '/../../photos/' . $filename;
								} else {
										$fullpath = __DIR__ . '/../' . $url;
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

						return 
						[
						'success' => true,
						'content' => $missingThumbs,
						'message' => '✅ Retrieved from: Biblio Records'
						];

        } catch (\Exception $e) {
								$this->rollback();
								return [
										'success' => false,
										'content' => [],
										'message' => "❌ Error: " . $e->getMessage()
								];
        }
    }
}
