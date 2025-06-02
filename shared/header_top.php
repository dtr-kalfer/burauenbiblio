<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	### following needed since this is included from within a class method -- Fred
	global $Locale, $CharSet, $nav, $tab, $focus_form_name, $focus_form_field, $doing_install;
?>
	
<!DOCTYPE html >
<!-- there are many lines here with obscure comments. For more info see http://html5boilerplate.com/ -->

<!-- language is set by user (default is 'en') -->
<html lang="<?php echo $Locale; ?>" class="no-js <?php echo ($doing_install?'obInstall':'no-obInstall'); ?>" >

<head>
  <!-- charset MUST be specified within first 1024 char of file start to be effective -->
  <meta charset="<?php echo $CharSet; ?>" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

  <!-- Mobile viewport optimized: j.mp/bplateviewport -->
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- select browser-top icon -->
  <link rel="shortcut icon" href="../favicon.ico" type="image/x-icon" />
  <!--link rel="apple-touch-icon" href="../apple-touch-icon.png"-->
  <style>
    html, body {
      background-color: #111;
    }
  </style>
  <!-- build title using library's name (or current-site name) from database -->
  <title>
    <?php
    if (!isset($doing_install) or !$doing_install) {
    	// If the cookie contains a site id, we take this one, otherwise the default.
    	// Adjusted, so that if 'library_name' contains a string, the site is put by default on 1.
    	$libName  = Settings::get('library_name');
    	if(empty($_SESSION['current_site'])) {
    		if(isset($_COOKIE['OpenBiblioSiteID'])) {
    			$site = new Sites;
    			$exists_in_db = $site->maybeGetOne($_COOKIE['OpenBiblioSiteID']);
    			if ($exists_in_db['siteid'] != $_COOKIE['OpenBiblioSiteID']) {
                    $_COOKIE['OpenBiblioSiteID'] = 1;
    			}
        			$_SESSION['current_site'] = $_COOKIE['OpenBiblioSiteID'];
    		} elseif($_SESSION['multi_site_func'] > 0){
    			$_SESSION['current_site'] = $_SESSION['multi_site_func'];
    		} else {
    			$_SESSION['current_site'] = 1;
    		}
    	}

			if ($_SESSION['multi_site_func'] > 0) {
					$sit = new Sites;
					$lib = $sit->maybeGetOne($_SESSION['current_site']);

					if (!is_array($lib) || $lib['siteid'] != $_SESSION['current_site']) {
							$lib = $sit->getOne(1);  // fallback
					}

					if (is_array($lib)) {
							$libName = $lib['name'];
					} else {
							$libName = 'Unknown Site';
					}
			}


    	echo $libName;
		//    	if($params['title']) {
		//    		echo ': '.H($params['title']);
		//    	}
		$_SESSION['libName'] = $libName;
    }
    ?>
  </title>

	<!-- Project Metadata for OpenBiblio -->
	<meta name="description" content="OpenBiblio Library System - Originally developed as version 1.0a">
	<meta name="author" content="Luuk Jansen, Fred LaPlante, Jane Sandberg, Micah Stetson">
	<meta name="generator" content="OpenBiblio 1.0a - https://obiblio.sourceforge.net/">
	<meta name="tester" content="Neil Redgate, Charlie Tudor">
	<meta name="burauenbiblio-maintainer" content="Ferdinand Tumulak">
	<meta name="burauenbiblio-fork-date" content="2025-05-07">
	<meta name="project-url" content="https://github.com/dtr-kalfer">
	
	<script src="../shared/modernizr-2.6.2.min.js"></script>
  <script src="../shared/jquery/jquery-3.2.1.min.js"></script>
  <link rel="stylesheet" href="../shared/style6.css" />

  <link rel="stylesheet" href="../shared/jquery/jquery-ui.min.css" />
  <link rel="stylesheet" href="<?php echo H($params['theme_dir_url']) ?>/style_day.css" />

  <?php if ($tab == 'opac') { ?>
		<link rel="stylesheet" href="../opac/opac_new.css" />
	<?php } ?>
