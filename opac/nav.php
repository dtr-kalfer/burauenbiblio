<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 *
 *
 * @author Fred LaPlante, June 2017
 * modified by F.Tumulak, May 2025
 */
?>
<style>
</style>
    <nav id="accordion" role="navigation" aria-label="site" tabindex="-1">
		<section class="menuSect">
			<h3 class="navHeading" id="defaultOpen">About Library</h3>
			<div class="navContent about">
                <a href="../opac/aboutForm.php?tab=OPAC" title="Info">About Library</a><br />
				<?php if (Settings::get('library_image_url') != "") {
					echo '<img id="logo"'.' src="'.Settings::get("library_image_url").'" />';
				} ?>

				<!-- Libname is defined in header_top.php -->
				<span id="library_name" ><?php echo $libName; ?></span>

				<hr class="hdrSpacer">
				<?php echo $open_hours->displayOpenHours(); ?>

				<hr class="hdrSpacer">
				<div id="library_phone"><?php echo Settings::get('library_phone'); ?></div>

				<hr class="hdrSpacer" />
				<footer>
				  <div id="obLogo" style="width: 100%; padding: 0;" >
						<a href="https://github.com/dtr-kalfer/burauenbiblio">
							<img src="../images/burauen_biblio_sm.webp" border="0" alt="BurauenBiblio" />
						</a>
						<br />
					</div>

					<?php // echo H(OBIB_CODE_VERSION);?>
					<br />
					<h3><a style="color: white; background-color: transparent; font-weight: bold;" href="../COPYRIGHT.html">COPYRIGHT</a></h3>
				</footer>
	        </div>
	    </section>
	</nav>
