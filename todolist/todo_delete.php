<?php
require_once("../shared/guard_token.php"); 
verify_token_or_die('guard_token_key'); // your custom key

		/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
		 * See the file COPYRIGHT.html for more details. --F.Tumulak
		 */
require '../todolist/db.php';

if (!empty($_POST['todo'])) {
    $stmt = $db->prepare("INSERT INTO todos (task) VALUES (:task)");
    $stmt->bindValue(':task', $_POST['todo'], PDO::PARAM_STR);
    $stmt->execute();
}

echo '<div id="todo-list">';
include '../todolist/todo_list.php';
echo '</div>';
