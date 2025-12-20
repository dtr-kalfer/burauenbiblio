	
<!DOCTYPE html >
<!-- there are many lines here with obscure comments. For more info see http://html5boilerplate.com/ -->

<!-- language is set by user (default is 'en') -->
<html lang="en" class="no-js no-obInstall" >

<head>
  <!-- charset MUST be specified within first 1024 char of file start to be effective -->
  <meta charset="UTF-8" />
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
    BCC LEARNING RESOURCE CENTER  </title>

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
  <link rel="stylesheet" href="../themes/default/style_day.css" />

  		<link rel="stylesheet" href="../opac/opac_new.css" />
		</head>
	<body>

    <!--[if lt IE 10]>
      <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
    <![endif]-->

	<!-- defines a SVG sprite for later use in menu -->
	<svg style="display:none">
		<symbol id="navicon" viewbox="0 0 20 20">
			<path d="m0-0v4h20v-4h-20zm0 8v4h20v-4h-20zm0 8v4h20v-4h-20z" fill="currentColor" />
		</symbol>
	</svg>


<aside id="sidebar">
    <div id="skiptocontent"><a href="#content" class="warning">Skip to main content</a></div>

	<header class="notForInstall">

		<h3 class="theHead">
		
					<!-- this button allows user to expand menu. Intended for phone & tablet users -->
			<span>
				<button id="menuBtn" aria-expanded="false">
					<svg><use xlink:href=#navicon></use></svg>
				</button>
			</span>
		
		<!-- Libname is defined in header_top.php -->
		<span id=\"library_name\" >BCC LEARNING RESOURCE CENTER</span>

				</h3>
		
			</header>

	<style>
</style>
    <nav id="accordion" role="navigation" aria-label="site" tabindex="-1">
  	<section class="menuSect">
       	<h3 class="navHeading">OPAC Search Mode</h3>
				<div class="navContent">
			  <a href="../catalog/srchForms.php?tab=OPAC" title="search">📚 OPAC Library Search</a><br />
			  <a href="../opac/doiSearchForms.php?tab=OPAC" title="doi">📚 DOI Search</a><br />
				<a href="../opac/free-ebook-gutenberg-oop.php?tab=OPAC" title="gutentex">📚 Gutentex E-book Search</a><br />
				</div>
   	</section>

		<section class="menuSect">
			<h3 class="navHeading" id="defaultOpen">About Library</h3>
			<div class="navContent about">
                <a href="../opac/aboutForm.php?tab=OPAC" title="Info">About Library</a><br />
				<img id="logo" src="../images/bcc-library-png-200x200.webp" />
				<!-- Libname is defined in header_top.php -->
				<span id="library_name" >BCC LEARNING RESOURCE CENTER</span>

				<hr class="hdrSpacer">
				<br />
<b>Fatal error</b>:  Uncaught Error: Call to undefined function jddayofweek() in /var/www/burauenbiblio/classes/Week.php:11
Stack trace:
#0 /var/www/burauenbiblio/model/OpenHours.php(72): Week-&gt;__construct()
#1 /var/www/burauenbiblio/opac/nav.php(35): OpenHours-&gt;displayOpenHours()
#2 /var/www/burauenbiblio/themes/default/header.php(97): include('/var/www/buraue...')
#3 /var/www/burauenbiblio/classes/Page.php(20): require_once('/var/www/buraue...')
#4 /var/www/burauenbiblio/catalog/srchForms.php(33): Page::header(Array)
#5 {main}
  thrown in <b>/var/www/burauenbiblio/classes/Week.php</b> on line <b>11</b><br />
<script defer src="https://static.cloudflareinsights.com/beacon.min.js/vcd15cbe7772f49c399c6a5babf22c1241717689176015" integrity="sha512-ZpsOmlRQV6y907TI0dKBHq9Md29nnaEIPlkf84rnaERnq6zvWvPUqr2ft8M1aS28oN72PdrCzSjY4U6VaAw1EQ==" data-cf-beacon='{"version":"2024.11.0","token":"49491bcbd94440368ba2721fe950e483","r":1,"server_timing":{"name":{"cfCacheStatus":true,"cfEdge":true,"cfExtPri":true,"cfL4":true,"cfOrigin":true,"cfSpeedBrain":true},"location_startswith":null}}' crossorigin="anonymous"></script>
