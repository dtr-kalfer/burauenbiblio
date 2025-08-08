<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

function staff_menu() {
	if ($_SESSION["hasCircAuth"] ?? false) {
		Nav::node('welcome', T("Welcome"), '../admin/noauth.php');
		
		Nav::node('circulation', T("Circulation"), '../circ/memberForms.php');
		Nav::node('circulation/searchform', T("Members"), '../circ/memberForms.php');
		Nav::node('circulation/search', T("SearchResults"));

		Nav::node('circulation/bookings', T("Bookings"), '../circ/bookings.php?type=holds');
		Nav::node('circulation/bookings/cart', T("Booking Cart")); //booking cart removed --Ferdinand Tumulak
		Nav::node('circulation/bookings/pending', T("Pending Bookings"));

		if (isset($_REQUEST['bookingid'])) {
			$params = 'bookingid='.U($_REQUEST['bookingid']);
			if (isset($_REQUEST['rpt']) and isset($_REQUEST['seqno'])) {
				$params .= '&rpt='.U($_REQUEST['rpt']);
				$params .= '&seqno='.U($_REQUEST['seqno']);
			}
			Nav::node('circulation/bookings/view', T("Booking Info"), '../circ/booking_view.php?'.$params);
			Nav::node('circulation/bookings/deleted', T("Deleted"));
		}

		Nav::node('circulation/bookings/book', T("Create Booking"));
		Nav::node('circulation/checkin', T("Check In"), '../circ/checkinForms.php');

		Nav::node('circulation/tally', T("daily book tally"), '../circ/dailytally.php');
		
		Nav::node('circulation/overdue', T("Overdue"), '../circ/overdue_items.php?type=overdue'); // new feature overdue calc -- F.Tumulak
		Nav::node('circulation/analytics', T("analytics"),'../circ/circ_report2.php');
			Nav::node('circulation/analytics/monthly', T("Circ. Report"),'../circ/circ_report2.php');
			Nav::node('circulation/analytics/top30', T("top30"),'../circ/top30.php');
			Nav::node('circulation/analytics/top30_inhouse', T("top30_in-house"),'../circ/top30_inhouse.php');
	}

	##-------------------------------------------------------------------------------------##
	if ($_SESSION["hasCatalogAuth"] ?? false) {
		Nav::node('cataloging', T("Cataloging"), '../catalog/srchForms.php');
		Nav::node('cataloging/localSearch', T("Existing Items"), "../catalog/srchForms.php");
		Nav::node('cataloging/newItem', T("New Item"), "../catalog/newItemForms.php");
		

		//if (isset($_SESSION['rpt_BiblioSearch'])) {
		//	Nav::node('cataloging/search', T("old search results"), '../shared/biblio_search.php?searchType=previous&tab='.U($tab));
		//}

		Nav::node('cataloging/cart', T("Tagged Items"), '../shared/req_cart2.php?type=request_cart');
			$params = 'bibid='.U(isset($_REQUEST["bibid"]));
			if (isset($_REQUEST['rpt']) and isset($_REQUEST['seqno'])) {
				$params .= '&rpt='.U($_REQUEST['rpt']);
				$params .= '&seqno='.U($_REQUEST['seqno']);
			}

			$menu_params = $menu_params ?? [];

			
		//	Nav::node('cataloging/biblio/editmarc', T("Edit MARC"), "../catalog/biblio_marc_edit_form.php?".$params);
		//	Nav::node('cataloging/biblio/editstock', T("Edit Stock Info"));
		//	Nav::node('cataloging/biblio/newlike', T("New Like"), "../catalog/biblio_new_like.php?".$menu_params);
			
		//	Nav::node('cataloging/biblio/bookings', T("Item Bookings"), "../reports/run_report.php?type=bookings");
		//	Nav::node('cataloging/biblio/holds', T("Hold Requests"), "../catalog/biblio_hold_list.php?".$params);

		
		// Nav::node('cataloging/upload_csv', T("CSVImport"), "../catalog/importCsvForms.php"); // Sorry, the backend part is not working or still work in progress by orig. author -F.Tumulak
		Nav::node('cataloging/doiSearch', T("doiSearch"), '../opac/doiSearchForms.php');
		Nav::node('cataloging/print_card_catalog', T("Print Catalog"), "../catalog/print_card_catalog.php"); // new feature Card catalog print --F.Tumulak 
		Nav::node('cataloging/missing_thumbnails', T("Thumbnail check"), "../catalog/thumbnail_check.php"); // new feature Card catalog print --F.Tumulak 
	}
	
	##-------------------------------------------------------------------------------------##

	##-------------------------------------------------------------------------------------##
	if ($_SESSION["hasAdminAuth"] ?? false) {
		// use this format --> Nav::node('parent dir./child dir.', T("locale"), '../path_to/file.php');
		Nav::node('admin', T("Admin"), '../admin/index.php');
		Nav::node('admin/info', T("App. Info"), '../admin/app_stats.php'); // Added this app. information status -->F.Tumulak
		
		Nav::node('admin/analytics', T("analytics"),'../circ/attendance_form.php');
			Nav::node('admin/analytics/attendance', T("attendance"),'../circ/attendance_form.php');
			Nav::node('admin/analytics/attendance_chart', T("attendance_chart"),'../circ/attendance_chart2.php');	

		Nav::node('admin/settings', T("Library Settings"), '../admin/settingsForm.php');
		Nav::node('admin/staff', T("Staff Admin"), '../admin/staffForm.php');
		Nav::node('admin/biblioFields', T("Biblio Fields"),'../admin/biblioFldsForm.php');
		Nav::node('admin/biblioCopyFields', T("Biblio Copy Fields"),'../admin/biblioCopyFldsForm.php');
		Nav::node('admin/calendar', T("Calendar Manager"), '../admin/calendarForm.php');
		Nav::node('admin/collections', T("Collections"), '../admin/collectionsForm.php?type=change_borrow_expiry');
		Nav::node('admin/media', T("Media Types"), '../admin/mediaForm.php');

		Nav::node('admin/upload_usmarc', T("MARC Import"), "../catalog/importMarcForms.php"); // This one is now working good --F.Tumulak

		if (isset($_SESSION["hasReportsAuth"]) && $_SESSION["hasReportsAuth"]):
			Nav::node('admin/bulk_delete', T("Bulk Delete"), "../catalog/bulkDelForm.php"); // moved bulk delete to its new home --F.Tumulak
		endif;

		
		Nav::node('admin/memberTypes', T("Member Types"), '../admin/memberTypeForm.php');
		// Nav::node('admin/memberFields', T("Member Fields"), '../admin/memberFldsForm.php'); // disabled it messes up the 'add custom member fields', crash a member entry.
		Nav::node('admin/onlineOpts', T("Online Options"), '../admin/onlineOptsForm.php');
		Nav::node('admin/onlineHosts', T("Online Hosts"), '../admin/onlineHostsForm.php');
		Nav::node('admin/openHours', T("Hours open"), '../admin/hoursForm.php');
		Nav::node('admin/sites', T("Sites"), '../admin/sitesForm.php');
		Nav::node('admin/states', T("States"), '../admin/statesForm.php');
		// Nav::node('admin/themes', T("Themes"), '../admin/themeForm.php'); // this was explicitly stated by orig. authors as 'not working' ---F.Tumulak
		// establish directly the OPAC link to avoid extra navigation --F.T.
		Nav::node('admin/opac', T("View Opac"), '../catalog/srchForms.php?tab=OPAC');
		Nav::node('admin/dbChkr', T("Database checker"), '../admin/dbChkrForms.php');
	}
	
	##-------------------------------------------------------------------------------------##
	if ($_SESSION["hasReportsAuth"] ?? false) {
		Nav::node('reports', T("Reports"), '../reports/index.php');
		Nav::node('reports/reportlist', T("Report List"), '../reports/index.php');
		if (isset($_SESSION['rpt_Report'])) {
			Nav::node('reports/results', T("Report Results"), '../reports/run_report.php?type=previous');
		}
	}

	##-------------------------------------------------------------------------------------##
	if ($_SESSION["hasToolsAuth"] ?? false) {
		Nav::node('tools', T("Tools"), '../tools/index.php');
		Nav::node('tools/settings', T("System Settings"), '../tools/settings_edit_form.php?reset=Y');
		Nav::node('tools/plugins', T("Plugin Manager"), '../tools/plugMgr_form.php');
		//Nav::node('tools/valid', T("Input Validations"), '../tools/validForm.php');
		//Nav::node('tools/system', T("WebServerInformation"), '../install/phpinfo.php');
		//Nav::node('tools/system', T("DbServerInformation"), '../tools/DBConfigForms.php');
		//Nav::node('tools/system', T("SystemDocumentation"), '../docs/index.php');
		//Nav::node('tools/system', T("Crude YAZ Test"), '../tools/yazTest.php');
		//Nav::node('install/system', T("Install"), '../install/index.php');

	}
	
	##-------------------------------------------------------------------------------------##
		//Nav::node('working', T("Under Construction"), '../working/index.php');
		//Nav::node('working/testApp', T("MARCImport"), "../catalog/importMarcForms.php");
		//Nav::node('working/testApp', T("CSVImport"), "../catalog/importCsvForms.php");

	##-------------------------------------------------------------------------------------##
	// $text = Settings::get('help_link');
	// $helpurl = "javascript:popSecondary('" . $text . "');";

	// Nav::node('help', T("Help"), $helpurl);
	
	##-------------------------------------------------------------------------------------##
	## #######################################
	## For plug-in support
	## #######################################
	$list = getPlugIns('nav.nav');
    sort($list);
	for ($x=0; $x<count($list); $x++) {
		include_once ($list[$x]);
	}
	## #######################################
}
