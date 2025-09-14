<?php
/**
 * Simple PSR-4 style autoloader for your Classes/ directory
 * Drop this into project root as autoload.php 
 * This is still in its experimental state
 * Namespace is included in this autoloader
 format:
	openbiblio/
	│── autoload.php
	│── index.php
	└── Classes/
			└── LibraryAttendance/
					└── Attendance.php
						
	What it does:
	Takes the namespace and class name (e.g. LibraryAttendance\Attendance)
	Replaces \ with / → LibraryAttendance/Attendance
	Appends .php
	So it looks for Classes/LibraryAttendance/Attendance.php								
	-- F.Tumulak
 */

spl_autoload_register(function ($class) {
    // Define base directory for class files
    $baseDir = __DIR__ . '/classes/';

    // Normalize class name → file path
    // Example: "LibraryAttendance" → "Attendance.php"
    $file = $baseDir . str_replace('\\', '/', $class) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});
