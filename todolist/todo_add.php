<?php 
		/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
		 * See the file COPYRIGHT.html for more details. --F.Tumulak
		 */
usleep(500000);
require_once("../shared/guard_token.php");
verify_token_or_die('guard_token_key');

require_once("../todolist/db_mysql.php");

if (!empty($_POST['todo'])) {
    $task = mysqli_real_escape_string($connection, $_POST['todo']);
    $sql = "INSERT INTO todos (task) VALUES ('$task')";
    mysqli_query($connection, $sql);
}

include '../todolist/todo_list.php';
