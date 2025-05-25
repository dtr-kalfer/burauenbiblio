<?php
	/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
	 * See the file COPYRIGHT.html for more details.
	 * This is an add-on feature for Openbiblio developed by Ferdinand Tumulak
	 * for bibid card catalog printing.
	 * it can still be further improved using PDO or use Openbiblio built-in class functions if you are up to it.
	 */
	class Qtest { // my own custom class Qtest, make sure dbParams.php is present --F.Tumulak
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