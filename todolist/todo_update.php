<?php
		/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
		 * See the file COPYRIGHT.html for more details. --F.Tumulak
		 */
require_once("../shared/guard_token.php");
verify_token_or_die('guard_token_key');

require_once("../todolist/db_mysql.php");

$id = (int)($_POST['id'] ?? 0);
$done = isset($_POST['done']) && $_POST['done'] === "true" ? 1 : 0;

if ($id > 0) {
    $sql = "UPDATE todos SET is_done = $done WHERE id = $id";
    mysqli_query($connection, $sql);
}

include '../todolist/todo_list.php';
usleep(300000);
