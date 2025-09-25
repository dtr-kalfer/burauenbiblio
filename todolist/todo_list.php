<?php
		/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
		 * See the file COPYRIGHT.html for more details. --F.Tumulak
		 */
require_once("../shared/guard_doggy.php");
require_once __DIR__ . '/../autoload.php'; // adjust the ../ if necessary depending on your source path.

use  Todo_List\Todo_List;

$todo   = new Todo_List();

$tasks = $todo->getAllTasks();

foreach ($tasks as $t): ?>
  <li class="<?= $t['is_done'] ? 'done' : '' ?>">
    <input type="checkbox"
      <?= $t['is_done'] ? 'checked' : '' ?>
      hx-post="../todolist/todo_update.php"
      hx-vals='{"id": <?= $t['id'] ?>, "done": <?= $t['is_done'] ? 'false' : 'true' ?>}'
      hx-target="#todo-list"
      hx-swap="innerHTML"
      hx-disabled-elt="checkbox"
    >
    <?= htmlspecialchars($t['task']) ?>
    <button
      hx-post="../todolist/todo_delete.php"
      hx-vals='{"id": <?= $t['id'] ?>}'
      hx-target="#todo-list"
      hx-swap="innerHTML"
      hx-disabled-elt="button"
    >🗑</button>
  </li>
<?php endforeach; ?>
