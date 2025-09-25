<script src="../htmx_cdn/htmx.min.js"></script>
<style>
	li.done { text-decoration: line-through; color: gray; }
</style>
<?php 
		/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
		 * See the file COPYRIGHT.html for more details. --F.Tumulak
		 */
require_once("../shared/guard_doggy.php");
?>
<div style="width: 45vw;">
	<h3>To-Do List</h3>
	<form hx-disabled-elt="button" hx-post="../todolist/todo_add.php" hx-target="#todo-list" hx-swap="innerHTML" style="display: flex; align-items: center; gap: 5px;">
		<input type="text" name="todo" id="todo-input" size="44" placeholder="Enter a new todo" required>
		
		<!-- Emoji Dropdown -->
		<select id="emoji-selector" title="Add emoji" style="padding: 4px;">
			<option value="">😊</option>
			<option value="📚">📚</option>
			<option value="✅">✅</option>
			<option value="🎉">🎉</option>
			<option value="📅">📅</option>
			<option value="😊">😊</option>
			<option value="⚠️">⚠️</option>
			<option value="📝">📝</option>
			<option value="📌">📌</option>
			<option value="🛒">🛒</option>
			<option value="🎂">🎂</option>
			<option value="💡">💡</option>
			<option value="💻">💻</option>
		</select>

		<input type="hidden" name="guard_token_key" value="<?php echo $_SESSION['guard_token_key']; ?>">
		<button hx-indicator="#myspinner" type="submit" style="padding-right: 30px;">
			<img id="myspinner" class="htmx-indicator" width="15" src="../images/t_loading.webp" alt="" />
			Add Task
		</button>
		
	</form>

	<ul id="todo-list-container">
		<ul id="todo-list"
			hx-get="../todolist/todo_list.php?guard_token_key=<?php echo $_SESSION['guard_token_key']; ?>"
			hx-trigger="load">
			<!-- List loaded here -->
		</ul>
	</ul>
</div>

<script>
  document.addEventListener("DOMContentLoaded", function () {
    const emojiSelector = document.getElementById("emoji-selector");
    const todoInput = document.getElementById("todo-input");

    emojiSelector.addEventListener("change", function () {
      const emoji = this.value;
      if (emoji && todoInput) {
        // Append emoji to current input value with a space
        todoInput.value = todoInput.value.trim() + " " + emoji;
        todoInput.focus();
        this.selectedIndex = 0; // Reset dropdown to default
      }
    });
  });
</script>

