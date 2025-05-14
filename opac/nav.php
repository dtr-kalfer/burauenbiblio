<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 *
 *
 * @author Fred LaPlante, June 2017
 */
?>
    <nav id="accordion" role="navigation" aria-label="site" tabindex="-1">
      	<section class="menuSect">
        	<h3 class="navHeading">Library Holdings</h3>
				<div class="navContent">
				  <a href="../catalog/srchForms.php?tab=OPAC" title="search">Library Search</a><br />
				  <a href="../opac/doiSearchForms.php?tab=OPAC" title="doi">use DOI</a><br />
				  <a href="../opac/imageBrowseForm.php?tab=OPAC" title="Photos">CoverPhotos</a><br />
				  <a href="../shared/req_cart.php?tab=opac#main" title="cart">Cart</a><br />
				</div>
      	</section>
			  
		<section class="menuSect">
			<h3 class="navHeading">My Account</h3>
			<div class="navContent">
                <a href="../opac/my_account.php?tab=OPAC" title="Info">Account Information</a><br />
                <a href="../opac/edit_account.php?tab=OPAC" title="Edit">Edit Account</a><br />
                <a href="../opac/bookings.php?tab=OPAC" title="Bookings">Bookings</a><br />
	        </div>
	    </section>

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
						<a href="#">
							<img src="../images/burauen_biblio_sm.webp" border="0" alt="BurauenBiblio" />
						</a>
						<br />
					</div>

					<?php // echo H(OBIB_CODE_VERSION);?>
					<br />
					<a href="../COPYRIGHT.html">Copyright Info.</a>.
				</footer>
	        </div>
	    </section>
	</nav>
