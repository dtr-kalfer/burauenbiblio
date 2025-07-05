<?php
		/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
		 * See the file COPYRIGHT.html for more details. --F.Tumulak
		 */

require_once("../shared/guard_token.php"); 
verify_token_or_die('guard_token_key'); // your custom key

$db = new PDO('sqlite:' . __DIR__ . '/db.sqlite');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
