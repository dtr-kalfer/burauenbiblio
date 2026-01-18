<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

require_once("../shared/common.php");
require_once(REL(__FILE__, "../model/Staff.php"));

$dbConst = "";
$pageErrors = "";
if (count($_POST) == 0) {
	header("Location: ../shared/myloginform.php");
	exit();
}

$username = $_POST["username"];
$error_found = false;
if ($username == "") {
	$error_found = true;
	$pageErrors["username"] = T("Username is required.");
}
$pwd = $_POST["pwd"];
if ($pwd == "") {
	$error_found = true;
	$pageErrors["pwd"] = T("Password is required.");
}

if (!$error_found) {

	$staff = new Staff($dbConst);
	$rows = $staff->getMatches(array('username'=>$username, 'pwd'=>md5($pwd)));
	$user = $rows->fetch(PDO::FETCH_ASSOC);

    if ($user === false) {
		$error_found = true;
		$pageErrors = array();
		$pageErrors["pwd"] = T("Invalid signon.");
	}
}

if ($error_found == true) {
    echo "login error found<br />\n";
	$_SESSION["postVars"] = $_POST;
	$_SESSION["pageErrors"] = $pageErrors;
    header("Location: ../shared/myloginform.php");
	exit();
}

if ($user['suspended_flg'] == 'Y') {
    header("Location: ../shared/suspended.php");
	exit();
}

unset($_SESSION["postVars"]);
unset($_SESSION["pageErrors"]);

if(isset($_REQUEST['selectSite'])){
	$_SESSION['current_site'] = $_REQUEST['selectSite'];
	setcookie("OpenBiblioSiteID", $_SESSION['current_site'], time()+60*60*24*365);
}

$_SESSION["username"] = $user['username'];
$_SESSION["userid"] = $user['userid'];
$_SESSION["start_page"] = $user['start_page'];
$_SESSION["secret_key"] = $user['secret_key'];
$_SESSION["loginAttempts"] = 0;
$_SESSION["hasAdminAuth"] = ($user['admin_flg'] == 'Y');
$_SESSION["hasCircAuth"] = ($user['circ_flg'] == 'Y');
$_SESSION["hasCircMbrAuth"] = ($user['circ_mbr_flg'] == 'Y');
$_SESSION["hasCatalogAuth"] = ($user['catalog_flg'] == 'Y');
$_SESSION["hasReportsAuth"] = ($user['reports_flg'] == 'Y');
$_SESSION["hasToolsAuth"] = ($user['tools_flg'] == 'Y');
// echo "in login ln#92<br />\n";

// generate guard token key
$_SESSION["guard_token_key"] = bin2hex(random_bytes(16));

// Step 2: Use that key name to store some secret value
$_SESSION[ $_SESSION["guard_token_key"] ] = "secretvalue";

// Step 3: Validate in another PHP file (copy below) to load guard_doggy 🐶🐕 --F.Tumulak
// require_once("../shared/guard_doggy.php");

setSessionFmSettings();
//lets make this noauth.php as default load page for both admin and staff users. --F.Tumulak
header("Location: ../admin/noauth.php");
exit();
