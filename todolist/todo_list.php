<?php
require_once("../shared/guard_token.php"); 
verify_token_or_die('guard_token_key'); // your custom key

		/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
		 * See the file COPYRIGHT.html for more details. --F.Tumulak
		 */
		 
require '../todolist/db.php';
$stmt = $db->query("SELECT * FROM todos ORDER BY id ASC");
$todos = $stmt->fetchAll(PDO::FETCH_ASSOC);
$token = $_SESSION['guard_token_key'] ?? $_GET['guard_token_key'] ?? '';

?>

<?php foreach ($todos as $todo): ?>
  <li class="<?= $todo['is_done'] ? 'done' : '' ?>">
    <input type="checkbox"
      <?= $todo['is_done'] ? 'checked' : '' ?>
      hx-post="../todolist/todo_update.php"
      hx-vals='{
        "id": <?= $todo['id'] ?>,
        "done": <?= $todo['is_done'] ? 'false' : 'true' ?>,
        "guard_token_key": "<?= $token ?>"
      }'
      hx-target="#todo-list"
      hx-swap="innerHTML"
    >
    <?= htmlspecialchars($todo['task']) ?>
    <button
      hx-post="../todolist/todo_delete.php"
      hx-vals='{
        "id": <?= $todo['id'] ?>,
        "guard_token_key": "<?= $token ?>"
      }'
      hx-target="#todo-list"
      hx-swap="innerHTML"
    >🗑</button>
  </li>
<?php endforeach; ?>

