<?php
		/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
		 * See the file COPYRIGHT.html for more details. --F.Tumulak
		 */
require_once("../catalog/class/Qtest.php");

$mypass = new Qtest;
$connection = mysqli_connect(
    $mypass->getDSN2("host"),
    $mypass->getDSN2("username"),
    $mypass->getDSN2("pwd"),
    $mypass->getDSN2("database")
);

if (mysqli_connect_errno()) {
    die("MySQL connection failed: " . mysqli_connect_error());
}
