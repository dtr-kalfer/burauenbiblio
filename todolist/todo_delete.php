<?php
require_once("../shared/guard_token.php"); 
verify_token_or_die('guard_token_key'); // your custom key

		/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
		 * See the file COPYRIGHT.html for more details. --F.Tumulak
		 */
require '../todolist/db.php';

$id = $_POST['id'] ?? null;

if ($id) {
    $stmt = $db->prepare("DELETE FROM todos WHERE id = :id");
    $stmt->execute([':id' => $id]);
}

include '../todolist/todo_list.php';
