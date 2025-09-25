<link rel="stylesheet" type="text/css" href="../jscalendar/jsCalendar.min.css">
<script type="text/javascript" src="../jscalendar/jsCalendar.min.js"></script>
<style>
	#content {
		background-color: #eee !important;
	}
</style>
<div id='welcomediv' >
	<?php

		require_once("../shared/common.php");

		$tab = "welcome";
		$nav = "";
		

		require_once(REL(__FILE__, "../shared/logincheck.php"));
		Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));
		date_default_timezone_set('Asia/Manila'); // Philippine timezone
		echo '<h3 >To-do List Section</h3>';
		echo '<p class="info" style="text-align: center;">🧑 Welcome ' . $_SESSION["username"] . T(", you are now logged in.") . '</p>';
	?>
	
	<div style="display: flex;">
		<section id="calendar" class="classic-theme orange"
			data-month-format="month YYYY" style='padding: 10px; margin: auto;'>
		</section>
		
		<section style='padding-top: 12px;  margin-right: 10px;'>
			<?php require_once("../todolist/index.php"); ?>
		</section>
	</div>
	<?php echo '<p  style="text-align: center;" >' . T("showhowtouse") . '</p>'; ?>
	<h3><?php echo T("Today is: ") . date("m-d-Y"); ?></h3>

</div>
<script>
  document.addEventListener("DOMContentLoaded", function () {
    const calendarElement = document.getElementById("calendar");
    const todoInput = document.getElementById("todo-input");

    if (calendarElement && todoInput) {
      // Create the jsCalendar manually to get its instance
      const calendar = jsCalendar.new(calendarElement);

      calendar.onDateClick(function (event, date) {
        const formatted = `${String(date.getMonth() + 1).padStart(2, '0')}-${String(date.getDate()).padStart(2, '0')}-${date.getFullYear()}`;
        todoInput.value = formatted + ': ';
        todoInput.focus();
      });
    }
  });
</script>