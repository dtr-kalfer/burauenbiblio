<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once("../shared/common.php");
$tab = "admin/analytics";
$nav = "attendance";	

require_once(REL(__FILE__, "../shared/logincheck.php"));

Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));

require_once("../catalog/class/Qtest.php");

$mypass = new Qtest;
$connection = mysqli_connect(
    $mypass->getDSN2("host"),
    $mypass->getDSN2("username"),
    $mypass->getDSN2("pwd"),
    $mypass->getDSN2("database")
);
if (mysqli_connect_errno()) {
    die("Connection failed: " . mysqli_connect_error());
}

// Handle form submission
$success = false;
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle deletion
    if (isset($_POST['delete_id'])) {
        $delete_id = intval($_POST['delete_id']);
        $stmt = $connection->prepare("DELETE FROM library_attendance WHERE id = ?");
        $stmt->bind_param("i", $delete_id);
        if ($stmt->execute()) {
            $success = true;
        } else {
            $error = "Failed to delete record: " . $stmt->error;
        }
        $stmt->close();
    } else {
        // Handle insert (your existing code)
        $date = $_POST['date'] ?? '';
        $user_type = $_POST['user_type'] ?? '';
        $course = $_POST['course'] ?? null;
        $count = intval($_POST['count'] ?? 1);

        $stmt = $connection->prepare("INSERT INTO library_attendance (date, user_type, course, count) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $date, $user_type, $course, $count);
        if ($stmt->execute()) {
            $success = true;
        } else {
            $error = "Error: " . $stmt->error;
        }
        $stmt->close();
    }
}

?>
<style>
		.form-container-attendance {
				width: 900px;
		}
</style>
<section class="form-container-attendance">
	<h2><?= T("Library Attendance") ?></h2>

	<?php if ($success): ?>
			<p class="success"><?= T("Attendance successfully recorded!") ?></p>
	<?php elseif ($error): ?>
			<p class="error"><?= htmlspecialchars($error) ?></p>
	<?php endif; ?>

	<form method="POST" action="">
			<label for="date"><?= T("Date of Visit") ?></label>
			<input type="date" name="date" id="date" required>

			<label for="user_type"><?= T("User Type") ?></label>
			<select name="user_type" id="user_type" required onchange="toggleCourse(this.value)">
					<option value="">-- Select --</option>
					<option value="Student">Student</option>
					<option value="Faculty">Faculty</option>
					<option value="Visitor">Visitor</option>
			</select>

			<span id="courseField" style="display: none;">
					<label for="course">Course</label>
					<select name="course" id="course">
							<option value="">-- Select Course --</option>
							<?php
							$courses = file('courses.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
							foreach ($courses as $course) {
									echo "<option value=\"" . htmlspecialchars($course) . "\">" . htmlspecialchars($course) . "</option>";
							}
							?>
					</select>
			</span>

			<label for="count"><?= T("Number of Attendees") ?></label>
			<input type="number" name="count" id="count" min="1" value="1" size="4" required>

			<button type="submit" ><?= T("Submit") ?></button>
	</form>
	
	
<hr>
<h3>Library Attendance Records (Use monthly tally per program to avoid clutter)</h3>

<table border="1" cellpadding="8" cellspacing="0">
    <thead>
        <tr>
            <th>ID</th>
            <th>Date (YYYY-MM-DD)</th>
            <th>User Type</th>
            <th>Course</th>
            <th>Count</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // Reconnect (or reuse) DB connection if needed

        $result = $connection->query("SELECT * FROM library_attendance ORDER BY ID");

        while ($row = $result->fetch_assoc()):
        ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['date']) ?></td>
            <td><?= htmlspecialchars($row['user_type']) ?></td>
            <td><?= htmlspecialchars($row['course'] ?? '-') ?></td>
            <td><?= htmlspecialchars($row['count']) ?></td>
            <td>
                <form method="POST" action="" onsubmit="return confirm('Delete this record?');">
                    <input type="hidden" name="delete_id" value="<?= $row['id'] ?>">
                    <button type="submit" style="padding: 0 5px; margin: 0;">Del</button>
                </form>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>
</section>
<script>
		function toggleCourse(userType) {
				const courseField = document.getElementById('courseField');
				if (userType === 'Student') {
						courseField.style.display = 'inline';
				} else {
						courseField.style.display = 'none';
						document.getElementById('course').value = '';
				}
		}
</script>