<?php
/*
 * BurauenBiblio Migration Script
 * -----------------------------
 * Upgrades an old OpenBiblio database schema to make it compatible with BurauenBiblio.
 *
 * This script applies ALTER TABLE and CREATE TABLE commands safely.
 * Each migration is executed only once and logged in `schema_migrations`.
 *
 * ALWAYS BACK UP YOUR DATABASE BEFORE RUNNING THIS SCRIPT.
 *
 * -- Improved by ChatGPT & F. Tumulak
 */

// Guard Doggy - Ensure authentication & permissions
require_once("../shared/guard_doggy.php");

// Connect to DB
require_once("../catalog/class/Qtest.php");

$mypass = new Qtest;
$db = mysqli_connect(
    $mypass->getDSN2("host"),
    $mypass->getDSN2("username"),
    $mypass->getDSN2("pwd"),
    $mypass->getDSN2("database")
);

if (mysqli_connect_errno()) {
    die("<p style='color:red;'><strong>❌ Database connection failed:</strong> " . mysqli_connect_error() . "</p>");
}

// Create schema_migrations table if not exists
$db->query("
    CREATE TABLE IF NOT EXISTS schema_migrations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        migration_name VARCHAR(255) NOT NULL,
        applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )
");

// Define migrations 
//"sql"  => "ALTER TABLE mbr_classify_dm ADD loan_allotment SMALLINT(3) NOT NULL DEFAULT 0"
//"sql"  => "ALTER TABLE mbr_classify_dm MODIFY max_fines DECIMAL(6,2) NOT NULL"
//"sql"  => "ALTER TABLE member_account MODIFY create_dt DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP"
//"sql"  => "ALTER TABLE member_account DROP PRIMARY KEY, ADD PRIMARY KEY (transid), MODIFY transid INT(11) NOT NULL AUTO_INCREMENT"

$migrations = [
    [
        "name" => "Add_loan_allotment_feature",
				"sql"  => "ALTER TABLE mbr_classify_dm ADD loan_allotment SMALLINT(3) NOT NULL DEFAULT 0"
    ],
    [
        "name" => "Fix_fines_greater_than_99.99",
				"sql"  => "ALTER TABLE mbr_classify_dm MODIFY max_fines DECIMAL(6,2) NOT NULL"
    ],
    [
        "name" => "Enable_payments_penalty_history_mode_on_members_step1",
				"sql"  => "ALTER TABLE member_account MODIFY create_dt DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP"
    ],
    [
        "name" => "Enable_payments_penalty_history_mode_on_members_step2",
				"sql"  => "ALTER TABLE member_account DROP PRIMARY KEY, ADD PRIMARY KEY (transid), MODIFY transid INT(11) NOT NULL AUTO_INCREMENT"
    ],
    [
        "name" => "Create_library_attendance_table",
        "sql"  => "CREATE TABLE IF NOT EXISTS library_attendance (
            id INT AUTO_INCREMENT PRIMARY KEY,
            attend_date DATE NOT NULL,
            user_type ENUM('Student', 'Faculty', 'Visitor') NOT NULL,
            course VARCHAR(10) DEFAULT NULL,
            count INT NOT NULL DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )"
    ],
    [
        "name" => "Create_todos_table",
        "sql"  => "CREATE TABLE IF NOT EXISTS todos (
            id INT AUTO_INCREMENT PRIMARY KEY,
            task TEXT NOT NULL,
            is_done TINYINT(1) DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )"
    ],
    [
        "name" => "Create_obib_book_activity_table",
        "sql"  => "CREATE TABLE IF NOT EXISTS obib_book_activity (
            id INT AUTO_INCREMENT PRIMARY KEY,
            bibid INT NOT NULL,
            barcode VARCHAR(20) NOT NULL,
            activity_time DATETIME DEFAULT CURRENT_TIMESTAMP,
            session_user VARCHAR(50),
            notes TEXT,
            INDEX (bibid)
        )"
    ],
    [
        "name" => "Create_table_courses",
        "sql"  => "CREATE TABLE tbl_courses (
						id INT AUTO_INCREMENT PRIMARY KEY,
						course_name VARCHAR(100) NOT NULL UNIQUE
        )"
    ],		
    [
        "name" => "Populate_table_courses",
        "sql"  => "INSERT INTO tbl_courses (course_name) VALUES
					('BSABM'),
					('BSAE'),
					('BPA'),
					('BSE'),
					('BMLS')
        "
    ],				
    // Add more future migrations here...
];

// Get applied migrations
$result = $db->query("SELECT migration_name FROM schema_migrations");
$applied = [];
while ($row = $result->fetch_assoc()) {
    $applied[] = $row['migration_name'];
}

// Apply migrations
$appliedCount = 0;

foreach ($migrations as $migration) {
    if (!in_array($migration['name'], $applied)) {
        echo "<p>Applying migration: <strong>{$migration['name']}</strong>...</p>";

        if ($db->query($migration['sql']) === TRUE) {
            $stmt = $db->prepare("INSERT INTO schema_migrations (migration_name) VALUES (?)");
            $stmt->bind_param("s", $migration['name']);
            $stmt->execute();
            $stmt->close();

            $appliedCount++;
            echo "<p style='color:green;'>✅ Migration '{$migration['name']}' applied successfully.</p>";
        } else {
            echo "<p style='color:red;'>❌ Failed migration '{$migration['name']}': {$db->error}</p>";
            break; // Stop if something fails
        }
    } else {
        echo "<p style='color:blue;'>⏩ Skipping already applied migration: {$migration['name']}</p>";
    }
}

// Final summary
if ($appliedCount > 0) {
    echo "<p style='color:green; font-weight:bold;'>🎉 $appliedCount new migration(s) applied successfully!</p>";
} else {
    echo "<p style='color:blue; font-weight:bold;'>✅ Database schema is already up-to-date.</p>";
}

mysqli_close($db);
?>
