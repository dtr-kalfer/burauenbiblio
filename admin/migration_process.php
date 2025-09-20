<?php
/*
 * BurauenBiblio Migration
 * -----------------------------
 * Upgrades an old OpenBiblio database schema to make it compatible with BurauenBiblio.
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

require_once __DIR__ . '/../autoload.php'; // adjust the ../ if necessary depending on your source path.
use Migration\MigrationManager;
		

$migrator = new MigrationManager("../classes/Migration/migration_codes.php");
$migrator->runMigrations();

?>
