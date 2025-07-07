<?php
require_once("../shared/common.php");
require_once("../model/Staff.php");

	class ObServer {
	public static function check_hmac() {
			$headers = array_change_key_case(ObServer::get_headers(), CASE_LOWER);
			$token = null;

			if (isset($headers['authcheck'])) {
					if (preg_match('/Token token="(.+?)"/', $headers['authcheck'], $matches)) {
							$token = $matches[1];
					} else {
							return 0;
					}
			} else {
					return 0;
			}

			$timeout = Settings::get('hmac_timeout') ?: (28 * 24 * 60); // default: 4 weeks
			$earliestLegitSendTime = time() - ($timeout * 60);

			if (!isset($_POST['timestamp']) || $earliestLegitSendTime > $_POST['timestamp']) {
					return 0;
			}

			$requestor = new Staff;
			$rows = $requestor->getMatches(['username' => $_POST['username']]);

			foreach ($rows as $row) {
					$expected_hash = hash_hmac('md5', $_POST['mode'] . '-' . $row['username'] . '-' . $_POST['timestamp'], $row['secret_key']);
					return ($expected_hash === $token); // return immediately on match
			}

			return 0; // no match found
	}

	public static function get_headers() {
		if (!function_exists('getallheaders'))  {
			$headers = array();
        		if (is_array($_SERVER)) {
        			foreach ($_SERVER as $name => $value) {
            				if (substr($name, 0, 5) == 'HTTP_') {
                				$headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            				}
        			}
        		}
          		return $headers;
		} else {
			return getallheaders();
		}

	}
}
?>
