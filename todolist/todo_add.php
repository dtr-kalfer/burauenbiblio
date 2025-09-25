<?php
		/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
		 * See the file COPYRIGHT.html for more details. --F.Tumulak
		 */
require_once("../shared/guard_doggy.php");

usleep(500000);
require_once __DIR__ . '/../autoload.php'; // adjust the ../ if necessary depending on your source path.

use  Todo_List\Todo_List;

$todo = new Todo_List();

if (!empty($_POST['todo'])) {
    $todo->addTask($_POST['todo']);
}

include 'todo_list.php';
