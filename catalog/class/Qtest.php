<?php
	class Qtest { // my own custom class Qtest
			public function getDSN2 ($key) {
					$fn = '../dbParams.php';
					if (file_exists($fn) ) {
							include($fn); 
					} else {
						// echo some error here
					}
					return $this->dsn[$key];
			}
	}