<?php
	/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
	 * See the file COPYRIGHT.html for more details.
	 * This is an add-on feature for Burauenbiblio developed by Ferdinand Tumulak
	 * For bibid card catalog printing use.
	 * it now uses prepared statements and built-in class functions.
	 */
		// Guard Doggy - Ensure authentication & permissions
		require_once("../shared/guard_doggy.php");

		require_once __DIR__ . '/../autoload.php'; // adjust the ../ if necessary depending on your source path.
		use Card_catalog\CardCatalog;
		
		$catalog = new CardCatalog();
		
		$errors = [];
		
		$bibid1 = $_POST["bibid_fpdf"];
		$bibid2 = $_POST["bibid_fpdf2"];
		if (!$catalog->hasBibid($bibid1)) {
				$errors[] = "❌ BibID $bibid1 not found.";
		}


		if (!$catalog->hasBibid($bibid2)) {
			 $errors[] = "❌ BibID $bibid2 not found.";
		}

		if (!empty($errors)) {
				echo "<div class='error'>" . implode("<br>", $errors) . "</div>";
		} else {
				// ✅ Both valid → redirect to PDF
				$query = http_build_query(['bibid_fpdf' => $bibid1, 'bibid_fpdf2' => $bibid2]);
				echo "<script>window.location.href = 'print_bibid_pdf2.php?$query';</script>";
		}
		
		exit;
