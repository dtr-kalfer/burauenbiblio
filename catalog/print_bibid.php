<?php
	require_once("../includes/fpdf/fpdf.php"); 
	function get_subfield_all_from_bibid($bibid=0) {
		global $connection;
		$safe_bibid = mysqli_real_escape_string($connection, $bibid);
		
		//SELECT subfield_data FROM biblio_subfield where bibid = 1626 ORDER BY fieldid;
			
			$query  = " SELECT subfield_data ";
			$query .= " FROM biblio_subfield ";
			$query .= " WHERE bibid = {$bibid} ";
			$query .= " ORDER BY fieldid ";
			
			$bookinfo_set = mysqli_query($connection, $query);	
			confirm_query($bookinfo_set);
			if ($bookinfo_set) {
				return $bookinfo_set;
			} else {
				return null;
			}		
	}
	
	function get_barcode_all_from_bibid($bibid) {
		global $connection;
		$safe_bibid = mysqli_real_escape_string($connection, $bibid);
		
		//select bibid, barcode_nmbr from biblio_copy where bibid = 1389;
			
			$query  = " SELECT bibid, barcode_nmbr ";
			$query .= " FROM biblio_copy ";
			$query .= " WHERE bibid = {$bibid} ";
			
			$bookinfo_set = mysqli_query($connection, $query);	
			confirm_query($bookinfo_set);
			if ($bookinfo_set) {
				return $bookinfo_set;
			} else {
				return null;
			}		
	}	