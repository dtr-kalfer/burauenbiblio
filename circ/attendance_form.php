<?php
	/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
	 * See the file COPYRIGHT.html for more details. --F.Tumulak
	 */
	require_once("../shared/common.php");
	$tab = "admin/analytics";
	$nav = "attendance";	
	require_once(REL(__FILE__, "../shared/logincheck.php"));
	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));

	// require_once __DIR__ . '/../Classes/Attendance.php'; //extends ConnectDB class
	
	require_once __DIR__ . '/../autoload.php'; // adjust the ../ if necessary depending on your source path.
	use LibraryAttendance\Attendance;
	
	$db = new Attendance();
	$success = false;

	// ---------------------
	// ATTENDANCE HANDLER
	// ---------------------
	$attendance_success = false;
	$attendance_error = '';

	if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === "attendance") {
			if (isset($_POST['delete_id'])) {
					$delete_id = intval($_POST['delete_id']);
					$success = $db->deleteAttendance($delete_id); 
					if ($success) {
							$attendance_success = true;
					} else {
							$attendance_error = "Failed to delete record: " . $stmt->error;
					}
					$db->close();
			} else {
					$date = $_POST['date'] ?? '';
					$user_type = $_POST['user_type'] ?? '';
					$course = $_POST['course'] ?? null;
					$count = intval($_POST['count'] ?? 1);
					
					$success = $db->addAttendance($date, $user_type, $course, $count);
					if ($success) {
							$attendance_success = true;
					} else {
							$attendance_error = "Error: " . $stmt->error;
					}
					$db->close();
			}
			
	}

	// ---------------------
	// COURSE MANAGEMENT HANDLER
	// ---------------------
	$course_success = false;
	$course_error = '';

	if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === "courses") {
			// ADD NEW COURSE
			if (isset($_POST['new_course']) && !empty(trim($_POST['new_course']))) {
					$new_course = trim($_POST['new_course']);
					
					$success = $db->addCourse($new_course);

					if ($success) {
							$course_success = "Course added successfully!";
					} else {
							$course_error = "Failed to add course: " . $stmt->error;
					}
					$db->close();
			}

			// DELETE COURSE
			if (isset($_POST['delete_course_id'])) {
					$delete_course_id = intval($_POST['delete_course_id']);

					$success = $db->delCourse($delete_course_id); 

					if ($success) {
							$course_success = "Course deleted successfully!";
					} else {
							$course_error = "Failed to delete course: " . $stmt->error;
					}
					$db->close();
			}
	}

	$toggle = isset($_POST['action']) && $_POST['action'] === 'courses';

	// Fetch courses for dropdown

	$courses = $db->getAllCourses();
?>
<style>
    .form-container-attendance {
        width: 900px;
        margin-bottom: 30px;
    }
    .manage-courses {
        width: 600px;
        margin-top: 30px;
        padding: 15px;
        border: 1px solid #ccc;
        border-radius: 6px;
        background: #f9f9f9;
    }
    .success { color: green; font-weight: bold; }
    .error { color: red; font-weight: bold; }
</style>

<section class="form-container-attendance" id="attendanceSection" style="display: <?= $toggle ? 'none' : 'block' ?>;">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <h2 style="width: 80%;"><?= T("Library Attendance") ?></h2>
        <button type="button" onclick="toggleSections()" style="padding: 5px 10px; cursor: pointer;">
            <?= "Manage Courses" ?>
        </button>
    </div>

    <?php if ($attendance_success): ?>
        <p class="success"><?= T("Attendance successfully recorded!") ?></p>
    <?php elseif ($attendance_error): ?>
        <p class="error"><?= htmlspecialchars($attendance_error) ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <input type="hidden" name="action" value="attendance">
        <label for="date"><?= T("Date of Visit") ?></label>
        <input type="date" name="date" id="date" required>

        <label for="user_type"><?= T("User Type") ?></label>
        <select name="user_type" id="user_type" required onchange="toggleCourse(this.value)">
            <option value="">-- Select --</option>
            <option value="Student">Student</option>
            <option value="Faculty">Faculty</option>
            <option value="Visitor">Visitor</option>
        </select>

        <span id="courseField" style="display:none;">
            <label for="course">Course</label>
            <select name="course" id="course">
                <option value="">-- Select Course --</option>
                <?php foreach ($courses as $course): ?>
                    <option value="<?= htmlspecialchars($course['course_name']) ?>">
                        <?= htmlspecialchars($course['course_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </span>

        <label for="count"><?= T("Number of Attendees") ?></label>
        <input type="number" name="count" id="count" min="1" value="1" size="4" required>

        <button type="submit" style="padding: 5px 10px; cursor: pointer;"><?= T("Submit") ?></button>
    </form>

    <hr>
    <h3>Library Attendance Records</h3>
    <table border="1" cellpadding="8" cellspacing="0">
        <thead>
            <tr>
                <th>ID</th>
                <th>Date</th>
                <th>User Type</th>
                <th>Course</th>
                <th>Count</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
						$result = $db->getAllAttendance();
						
            // $result = $connection->query("SELECT * FROM library_attendance ORDER BY id DESC");
            
						if (!empty($result)) {
						    foreach ($result as $row):
            ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['date']) ?></td>
                <td><?= htmlspecialchars($row['user_type']) ?></td>
                <td><?= htmlspecialchars($row['course'] ?? '-') ?></td>
                <td><?= htmlspecialchars($row['count']) ?></td>
                <td>
                    <form method="POST" action="" onsubmit="return confirm('Delete this record?');">
                        <input type="hidden" name="action" value="attendance">
                        <input type="hidden" name="delete_id" value="<?= $row['id'] ?>">
                        <button type="submit" style="padding: 0 5px;">Del</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; } ?>
        </tbody>
    </table>
</section>

<!-- =================== MANAGE COURSES =================== -->
<section class="manage-courses" id="coursesSection" style="display: <?= $toggle ? 'block' : 'none' ?>;">

    <div style="display: flex; justify-content: space-between; align-items: center;">
        <h3  style="width: 80%;"><?= T("Manage Courses") ?></h3>
        <button type="button" onclick="toggleSections()" style="padding: 5px 10px; cursor: pointer;">
            <?= T("Go Back") ?>
        </button>
    </div>

    <?php if ($course_success): ?>
        <p class="success"><?= htmlspecialchars($course_success) ?></p>
    <?php elseif ($course_error): ?>
        <p class="error"><?= htmlspecialchars($course_error) ?></p>
    <?php endif; ?>

    <form method="POST" action="" style="margin-bottom: 15px;">
        <input type="hidden" name="action" value="courses">
        <label for="new_course"><?= T("Add New Course") ?></label>
        <input type="text" name="new_course" id="new_course" required>
        <button type="submit" id="addCourseBtn"><?= T("Add") ?></button>
    </form>

    <table border="1" cellpadding="8" cellspacing="0">
        <thead>
            <tr>
                <th>ID</th>
                <th>Course Name</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($courses as $course): ?>
            <tr>
                <td><?= $course['id'] ?></td>
                <td><?= htmlspecialchars($course['course_name']) ?></td>
                <td>
                    <form method="POST" action="" onsubmit="return confirm('Delete this course?');" style="display:inline;">
                        <input type="hidden" name="action" value="courses">
                        <input type="hidden" name="delete_course_id" value="<?= $course['id'] ?>">
                        <button type="submit" id="deleteCourseBtn" style="padding: 0 5px;">Del</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>

<script>
    function toggleCourse(userType) {
        const courseField = document.getElementById('courseField');
        courseField.style.display = (userType === 'Student') ? 'inline' : 'none';
        if (userType !== 'Student') {
            document.getElementById('course').value = '';
        }
    }
		
    function toggleSections() {
        const attendance = document.getElementById("attendanceSection");
        const courses = document.getElementById("coursesSection");

        if (attendance.style.display === "none") {
            attendance.style.display = "block";
            courses.style.display = "none";
        } else {
            attendance.style.display = "none";
            courses.style.display = "block";
        }
    }
	
</script>