<?php
	/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
	 * See the file COPYRIGHT.html for more details.
	 * This is an add-on feature for Openbiblio developed by Ferdinand Tumulak
	 * for bibid card catalog printing.
	 * it can still be further improved using PDO or use Openbiblio built-in class functions if you are up to it.
	 */
	function confirm_query($result_set) {
	if (!$result_set) {
			die("Database query failed.");
		}		
	}
	
	function mysql_prep($string) {
		global $connection;
		$escaped_string = mysqli_real_escape_string($connection, $string);
		return $escaped_string;
	}	
	
	function get_marc_subfields($bibid, $tag, $subfield_cd) {
			global $connection;
			
			$sql = "SELECT s.subfield_data
							FROM biblio_field AS f
							JOIN biblio_subfield AS s 
								ON f.fieldid = s.fieldid
							WHERE f.bibid = ?
								AND f.tag = ?
								AND s.subfield_cd = ?
							ORDER BY f.fieldid, s.subfieldid
							LIMIT 1";

			$stmt = mysqli_prepare($connection, $sql);
			if ($stmt === false) {
					die("MySQL prepare failed: " . mysqli_error($connection) . "\nSQL: " . $sql);
			}

			mysqli_stmt_bind_param($stmt, "iss", $bibid, $tag, $subfield_cd);
			mysqli_stmt_execute($stmt);
			$result = mysqli_stmt_get_result($stmt);
			$row = mysqli_fetch_assoc($result);
			return $row ? $row['subfield_data'] : '';
	}

	function safe_barcode($barcodes, $index) {
			return isset($barcodes[$index]) ? $barcodes[$index] : '';
	}


	function get_trimmed_barcodes($bibid) {
			global $connection;
			$sql = "SELECT RIGHT(barcode_nmbr, 6) AS barcode_short
							FROM biblio_copy
							WHERE bibid = ?
							ORDER BY barcode_nmbr ASC";
			$stmt = mysqli_prepare($connection, $sql);
			mysqli_stmt_bind_param($stmt, "i", $bibid);
			mysqli_stmt_execute($stmt);
			$result = mysqli_stmt_get_result($stmt);
			$barcodes = [];
			while ($row = mysqli_fetch_assoc($result)) {
					$barcodes[] = $row['barcode_short'];
			}
			return $barcodes;
	}

	function get_subfield_all_from_bibid($bibid=0) {
		global $connection;
		$safe_bibid = mysqli_real_escape_string($connection, $bibid);
			
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