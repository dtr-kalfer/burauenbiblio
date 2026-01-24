<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 *
 *
 * @author Fred LaPlante, June 2017
 * modified by F.Tumulak, May 2025
 * Added Gutentex API search , Oct. 2025 -- F. Tumulak
 */
?>
<style>
</style>
    <nav id="accordion" role="navigation" aria-label="site" tabindex="-1">
  	<section class="menuSect">
       	<h3 class="navHeading">OPAC Search Mode</h3>
				<div class="navContent" id="navSearchMenu">
			  <a href="../catalog/srchForms.php?tab=OPAC" title="search">📚 OPAC Library Search</a><br />
			  <a href="../opac/doiSearchForms.php?tab=OPAC" title="doi">📚 DOI Search</a><br />
				<a href="../opac/free-ebook-gutenberg-oop.php?tab=OPAC" title="gutentex">📚 Gutentex E-book Search</a><br />
				</div>
   	</section>

		<section class="menuSect">
			<h3 class="navHeading" id="defaultOpen">About Library</h3>
			<div class="navContent about" id="aboutlib">About Library<br />
				<?php if (Settings::get('library_image_url') != "") {
					echo '<img id="logo"'.' src="'.Settings::get("library_image_url").'" />';
				} ?>

				<!-- Libname is defined in header_top.php -->
				<span id="library_name" ><?php echo $libName; ?></span>

				<hr class="hdrSpacer">
				<?php echo $open_hours->displayOpenHours(); ?>

				<hr class="hdrSpacer" />
				

				<footer>
					<div id="obLogo" >
						<a href="https://github.com/dtr-kalfer" target="_blank">
							<img src="../images/burauen_biblio_sm.webp" border="0" alt="BurauenBiblio" />
						</a>
						<br />
					</div>
					<p class="version_bib"><?php echo H(OBIB_VARIANT) . " " . H(OBIB_CODE_VERSION);?></p>
					
					<br />
					<h3 class="endfooter">
						<a  href="../COPYRIGHT.html" target="_blank">COPYRIGHT</a>
					</h3>
				</footer>
	        </div>
	    </section>
	</nav>
