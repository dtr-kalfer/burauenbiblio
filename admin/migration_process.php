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
 *
 * This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details. --F.Tumulak
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
        "sql"  => "CREATE TABLE IF NOT EXISTS tbl_courses (
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
				// Fix error zero-date defaults (0000-00-00 00:00:00) which errors on InnoDB engine
    [
        "name" => "Fix error: invalid datetime defaults biblio_copy InnoDB migrate",
				"sql"  => "ALTER TABLE `biblio_copy`
										MODIFY `create_dt` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
										MODIFY `last_change_dt` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
										ON UPDATE CURRENT_TIMESTAMP		
									"		
    ],
    [
        "name" => "Fix error: invalid datetime defaults biblio InnoDB migrate",
				"sql"  => "ALTER TABLE `biblio`
										MODIFY `create_dt` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
										MODIFY `last_change_dt` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
										ON UPDATE CURRENT_TIMESTAMP	
									"		
    ],

		// Make sure all create tables are set before updating the engine
		
		// Update openbiblio table engine to InnoDB
    [
        "name" => "Change to InnoDB table `usmarc_indicator_dm`",
        "sql"  => "ALTER TABLE `usmarc_indicator_dm` ENGINE=InnoDB"
    ],
    [
        "name" => "Change to InnoDB table `cutter`",
        "sql"  => "ALTER TABLE `cutter` ENGINE=InnoDB"
    ],
    [
        "name" => "Change to InnoDB table `member_account`",
        "sql"  => "ALTER TABLE `member_account` ENGINE=InnoDB"
    ],
    [
        "name" => "Change to InnoDB table `lookup_settings`",
        "sql"  => "ALTER TABLE `lookup_settings` ENGINE=InnoDB"
    ],
    [
        "name" => "Change to InnoDB table `open_hours`",
        "sql"  => "ALTER TABLE `open_hours` ENGINE=InnoDB"
    ],
    [
        "name" => "Change to InnoDB table `transsections`",
        "sql"  => "ALTER TABLE `transsections` ENGINE=InnoDB"
    ],
    [
        "name" => "Change to InnoDB table `material_fields`",
        "sql"  => "ALTER TABLE `material_fields` ENGINE=InnoDB"
    ],
    [
        "name" => "Change to InnoDB table `validation_dm`",
        "sql"  => "ALTER TABLE `validation_dm` ENGINE=InnoDB"
    ],
    [
        "name" => "Change to InnoDB table `report_displays`",
        "sql"  => "ALTER TABLE `report_displays` ENGINE=InnoDB"
    ],
    [
        "name" => "Change to InnoDB table `state_dm`",
        "sql"  => "ALTER TABLE `state_dm` ENGINE=InnoDB"
    ],
    [
        "name" => "Change to InnoDB table `translinksectionlocale`",
        "sql"  => "ALTER TABLE `translinksectionlocale` ENGINE=InnoDB"
    ],
    [
        "name" => "Change to InnoDB table `session`",
        "sql"  => "ALTER TABLE `session` ENGINE=InnoDB"
    ],
    [
        "name" => "Change to InnoDB table `calendar_dm`",
        "sql"  => "ALTER TABLE `calendar_dm` ENGINE=InnoDB"
    ],
    [
        "name" => "Change to InnoDB table `transkeys`",
        "sql"  => "ALTER TABLE `transkeys` ENGINE=InnoDB"
    ],
    [
        "name" => "Change to InnoDB table `biblio_status_dm`",
        "sql"  => "ALTER TABLE `biblio_status_dm` ENGINE=InnoDB"
    ],
    [
        "name" => "Change to InnoDB table `usmarc_block_dm`",
        "sql"  => "ALTER TABLE `usmarc_block_dm` ENGINE=InnoDB"
    ],
    [
        "name" => "Change to InnoDB table `mbr_classify_dm`",
        "sql"  => "ALTER TABLE `mbr_classify_dm` ENGINE=InnoDB"
    ],
    [
        "name" => "Change to InnoDB table `cart`",
        "sql"  => "ALTER TABLE `cart` ENGINE=InnoDB"
    ],
    [
        "name" => "Change to InnoDB table `view_fields`",
        "sql"  => "ALTER TABLE `view_fields` ENGINE=InnoDB"
    ],
    [
        "name" => "Change to InnoDB table `biblio_hold`",
        "sql"  => "ALTER TABLE `biblio_hold` ENGINE=InnoDB"
    ],
    [
        "name" => "Change to InnoDB table `collection_dist`",
        "sql"  => "ALTER TABLE `collection_dist` ENGINE=InnoDB"
    ],
    [
        "name" => "Change to InnoDB table `translocales`",
        "sql"  => "ALTER TABLE `translocales` ENGINE=InnoDB"
    ],
    [
        "name" => "Change to InnoDB table `biblio_subfield`",
        "sql"  => "ALTER TABLE `biblio_subfield` ENGINE=InnoDB"
    ],
    [
        "name" => "Change to InnoDB table `usmarc_subfield_dm`",
        "sql"  => "ALTER TABLE `usmarc_subfield_dm` ENGINE=InnoDB"
    ],
    [
        "name" => "Change to InnoDB table `member_fields`",
        "sql"  => "ALTER TABLE `member_fields` ENGINE=InnoDB"
    ],
    [
        "name" => "Change to InnoDB table `php_sess`",
        "sql"  => "ALTER TABLE `php_sess` ENGINE=InnoDB"
    ],
    [
        "name" => "Change to InnoDB table `biblio_copy_fields_dm`",
        "sql"  => "ALTER TABLE `biblio_copy_fields_dm` ENGINE=InnoDB"
    ],
    [
        "name" => "Change to InnoDB table `biblio_status_hist`",
        "sql"  => "ALTER TABLE `biblio_status_hist` ENGINE=InnoDB"
    ],
    [
        "name" => "Change to InnoDB table `usmarc_tag_dm`",
        "sql"  => "ALTER TABLE `usmarc_tag_dm` ENGINE=InnoDB"
    ],
    [
        "name" => "Change to InnoDB table `material_type_dm`",
        "sql"  => "ALTER TABLE `material_type_dm` ENGINE=InnoDB"
    ],
    [
        "name" => "Change to InnoDB table `transaction_type_dm`",
        "sql"  => "ALTER TABLE `transaction_type_dm` ENGINE=InnoDB"
    ],
    [
        "name" => "Change to InnoDB table `biblio_copy_fields`",
        "sql"  => "ALTER TABLE `biblio_copy_fields` ENGINE=InnoDB"
    ],
    [
        "name" => "Change to InnoDB table `site`",
        "sql"  => "ALTER TABLE `site` ENGINE=InnoDB"
    ],
    [
        "name" => "Change to InnoDB table `images`",
        "sql"  => "ALTER TABLE `images` ENGINE=InnoDB"
    ],
    [
        "name" => "Change to InnoDB table `member_fields_dm`",
        "sql"  => "ALTER TABLE `member_fields_dm` ENGINE=InnoDB"
    ],
    [
        "name" => "Change to InnoDB table `booking`",
        "sql"  => "ALTER TABLE `booking` ENGINE=InnoDB"
    ],
    [
        "name" => "Change to InnoDB table `staff`",
        "sql"  => "ALTER TABLE `staff` ENGINE=InnoDB"
    ],
    [
        "name" => "Change to InnoDB table `calendar`",
        "sql"  => "ALTER TABLE `calendar` ENGINE=InnoDB"
    ],

    [
        "name" => "Change to InnoDB table `booking_member`",
        "sql"  => "ALTER TABLE `booking_member` ENGINE=InnoDB"
    ],
    [
        "name" => "Change to InnoDB table `lookup_hosts`",
        "sql"  => "ALTER TABLE `lookup_hosts` ENGINE=InnoDB"
    ],
    [
        "name" => "Change to InnoDB table `transentries`",
        "sql"  => "ALTER TABLE `transentries` ENGINE=InnoDB"
    ],
    [
        "name" => "Change to InnoDB table `biblio_stock`",
        "sql"  => "ALTER TABLE `biblio_stock` ENGINE=InnoDB"
    ],
    [
        "name" => "Change to InnoDB table `collection_circ`",
        "sql"  => "ALTER TABLE `collection_circ` ENGINE=InnoDB"
    ],
    [
        "name" => "Change to InnoDB table `member`",
        "sql"  => "ALTER TABLE `member` ENGINE=InnoDB"
    ],
    [
        "name" => "Change to InnoDB table `biblio_field`",
        "sql"  => "ALTER TABLE `biblio_field` ENGINE=InnoDB"
    ],
    [
        "name" => "Change to InnoDB table `collection_dm`",
        "sql"  => "ALTER TABLE `collection_dm` ENGINE=InnoDB"
    ],
    [
        "name" => "Change to InnoDB table `theme`",
        "sql"  => "ALTER TABLE `theme` ENGINE=InnoDB"
    ],
    [
        "name" => "Change to InnoDB table `settings`",
        "sql"  => "ALTER TABLE `settings` ENGINE=InnoDB"
    ],
		[
        "name" => "Change to InnoDB table `biblio`",
        "sql"  => "ALTER TABLE `biblio` ENGINE=InnoDB"
    ],
		[
        "name" => "Change to InnoDB table `biblio_copy`",
        "sql"  => "ALTER TABLE `biblio_copy` ENGINE=InnoDB"
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
            // Do no break, just continue --F.Tumulak
						//break; // Stop if something fails
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
