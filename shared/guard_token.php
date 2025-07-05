<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details. --F.Tumulak
 */ 
// Usage: verify_token_or_die('your_key_name');


// This will exit the script with a 403 if the token doesn't match
function verify_token_or_die(string $token_key = 'form_token') {
    if (session_status() === PHP_SESSION_NONE) {
			session_start();
		}

    // Optional: auto-initialize session token if not present
    if (!isset($_SESSION[$token_key])) {
        $_SESSION[$token_key] = bin2hex(random_bytes(16));
    }

    $session_token = $_SESSION[$token_key];
		
		$submitted_token = $_POST[$token_key] 
										?? $_GET[$token_key] 
										?? $_SERVER['HTTP_X_TOKEN'] 
										?? null;

    if (!$submitted_token || $submitted_token !== $session_token) {
        http_response_code(403);
        echo "⛔ Access denied: invalid or missing token.";
        exit;
    }
}
