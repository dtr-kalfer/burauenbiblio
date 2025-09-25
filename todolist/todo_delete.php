<?php
		/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
		 * See the file COPYRIGHT.html for more details. --F.Tumulak
		 */
require_once("../shared/guard_doggy.php");
require_once __DIR__ . '/../autoload.php'; // adjust the ../ if necessary depending on your source path.

use  Todo_List\Todo_List;

$todo = new Todo_List();

$id = (int)($_POST['id'] ?? 0);
if ($id > 0) {
    $todo->deleteTask($id);
}

include '../todolist/todo_list.php';
usleep(300000);
