<?php
		/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
		 * See the file COPYRIGHT.html for more details. --F.Tumulak
		 */


require '../todolist/db.php';

$id = $_POST['id'] ?? null;
$done = $_POST['done'] ?? null;

if (isset($id, $done)) {
    $stmt = $db->prepare("UPDATE todos SET is_done = :done WHERE id = :id");
    $stmt->execute([
        ':done' => filter_var($done, FILTER_VALIDATE_BOOLEAN) ? 1 : 0,
        ':id' => $id
    ]);
}

include '../todolist/todo_list.php';
