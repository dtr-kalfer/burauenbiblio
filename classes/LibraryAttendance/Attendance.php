<?php
	/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
	 * See the file COPYRIGHT.html for more details.
	 */
	
	namespace LibraryAttendance;

	class Attendance extends \ConnectDB { //use backslash (// root namespace if ConnectDB has no namespace)
			public function addAttendance($date, $user_type, $course, $count) {
					return $this->execute(
							"INSERT INTO library_attendance (date, user_type, course, count) VALUES (?, ?, ?, ?)",
							"sssi",
							[$date, $user_type, $course, $count]
					);
			}

			public function deleteAttendance($id) {
					return $this->execute(
							"DELETE FROM library_attendance WHERE id = ?",
							"i",
							[$id]
					);
			}

			public function addCourse($new_course) {
					return $this->execute(
							"INSERT INTO tbl_courses (course_name) VALUES (?)",
							"s",
							[$new_course]
					);
			}

			public function delCourse($delete_course_id) {
					return $this->execute(
							"DELETE FROM tbl_courses WHERE id = ?",
							"i",
							[$delete_course_id]
					);
			}

			public function getAllCourses() {
					$stmt = $this->select("SELECT * FROM tbl_courses ORDER BY course_name ASC");
					return $stmt ? $stmt : [];
			}

			public function getAllAttendance() {
					$stmt = $this->select("SELECT * FROM library_attendance ORDER BY id DESC");
					return $stmt ? $stmt : [];
			}

			public function getRangeAttendance($start_date, $end_date, $isStudentsOnly = false) {
					$sql = "
							SELECT 
									user_type,
									course,
									DATE_FORMAT(date, '%Y-%m') AS month,
									SUM(count) AS total
							FROM library_attendance
							WHERE date BETWEEN ? AND ?
					";

					// Add optional filter
					if ($isStudentsOnly) {
							$sql .= " AND user_type = 'Student'";
					}

					$sql .= "
							GROUP BY user_type, course, month
							ORDER BY month ASC
					";

					// Use the protected select() from ConnectDB
					$stmt = $this->select($sql, "ss", [$start_date, $end_date]); 
					return $stmt ? $stmt : [];
			}

			public function getListCourses() {
					$rows = $this->select("
							SELECT DISTINCT course
							FROM library_attendance
							WHERE user_type = 'Student'
								AND course IS NOT NULL
								AND course <> ''
							ORDER BY course ASC
					");

					return array_map(fn($row) => $row['course'], $rows);
					
					// Flatten into a simple array of strings
					/*
					from this
					[
						["course" => "BPA"],
						["course" => "BSA"],
						["course" => "BSCrim"],
						["course" => "BSISM"],
						["course" => "BSMA"]
					]
					into 
					["BPA", "BSA", "BSCrim", "BSISM", "BSMA"]
					*/
			}
}
