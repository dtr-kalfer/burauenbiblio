<?php
		/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
		 * See the file COPYRIGHT.html for more details. --F.Tumulak
		 */
require_once("../shared/guard_token.php");
verify_token_or_die('guard_token_key');

require_once("../todolist/db_mysql.php");

$sql = "SELECT * FROM todos ORDER BY id ASC";
$result = mysqli_query($connection, $sql);

while ($todo = mysqli_fetch_assoc($result)): ?>
  <li class="<?= $todo['is_done'] ? 'done' : '' ?>">
    <input type="checkbox"
      <?= $todo['is_done'] ? 'checked' : '' ?>
      hx-post="../todolist/todo_update.php"
      hx-vals='{
        "id": <?= $todo['id'] ?>,
        "done": <?= $todo['is_done'] ? 'false' : 'true' ?>,
        "guard_token_key": "<?= $_SESSION['guard_token_key'] ?? $_GET['guard_token_key'] ?>"
      }'
      hx-target="#todo-list"
      hx-swap="innerHTML"
			hx-disabled-elt="checkbox"
    >
    <?= htmlspecialchars($todo['task']) ?>
    <button
      hx-post="../todolist/todo_delete.php"
      hx-vals='{
        "id": <?= $todo['id'] ?>,
        "guard_token_key": "<?= $_SESSION['guard_token_key'] ?? $_GET['guard_token_key'] ?>"
      }'
      hx-target="#todo-list"
      hx-swap="innerHTML"
			hx-disabled-elt="button"
    >🗑</button>
  </li>
<?php endwhile; ?>
