<?php
require_once("../catalog/class/Qtest.php");

$mypass = new Qtest;

try {
    $dsn = 'mysql:host=' . $mypass->getDSN2("host") . ';dbname=' . $mypass->getDSN2("database") . ';charset=utf8mb4';
    $pdo = new PDO($dsn, $mypass->getDSN2("username"), $mypass->getDSN2("pwd"));
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("PDO Connection failed: " . $e->getMessage());
}

header('Content-Type: application/json');
header('Content-Disposition: attachment; filename="attendance_data.json"');

$isStudentsOnly = isset($_GET['students_only']) && $_GET['students_only'] == '1';

// Get GET data
$start = $_GET['start_month'] ?? '';
$end = $_GET['end_month'] ?? '';

// Validate date format (YYYY-MM)
if (!preg_match('/^\d{4}-\d{2}$/', $start) || !preg_match('/^\d{4}-\d{2}$/', $end)) {
    echo json_encode(['error' => 'Invalid date format. Use YYYY-MM']);
    exit;
}

// Add day parts to complete valid dates
$start_date = $start . '-01';
$end_date = $end . '-01';

// OLD sql format removed --F.Tumulak

$sql = "
    SELECT 
        user_type,
        course,
        DATE_FORMAT(date, '%Y-%m') AS month,
        SUM(count) AS total
    FROM library_attendance
    WHERE date BETWEEN ? AND ?
";

// Filter only students if need to be --F.Tumulak
if ($isStudentsOnly) {
    $sql .= " AND user_type = 'Student'";
}

$sql .= "
    GROUP BY user_type, course, month
    ORDER BY month ASC
";

$stmt = $pdo->prepare($sql);
$stmt->execute([$start_date, $end_date]);

$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($data, JSON_PRETTY_PRINT);
