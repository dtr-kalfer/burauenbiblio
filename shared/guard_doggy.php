<?php 
if (session_status() === PHP_SESSION_NONE) {
	session_start();
}
if (!isset($_SESSION["guard_token_key"]) ||
    !isset($_SESSION[$_SESSION["guard_token_key"]])) {
    echo "🐶 Bowow! 🐕 ❌ No! Please Login first!"; die();
}