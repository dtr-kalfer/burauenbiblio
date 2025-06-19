<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once("../shared/common.php");

$tab = "admin";
$nav = "";

Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));
date_default_timezone_set('Asia/Manila'); // Philippine timezone

echo '<p class="error">' . T("adminNoauth") . '</p>';
echo '<h3>Welcome to BurauenBiblio! Current Date and Time: <b>' . date("m-d-Y H:i:s") . '</b></h3>' ;
 