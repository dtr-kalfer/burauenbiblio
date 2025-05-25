<?php
	/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
	 * See the file COPYRIGHT.html for more details.
	 * This is an add-on feature for Openbiblio developed by Ferdinand Tumulak
	 * for bibid card catalog printing.
	 * it can still be further improved using PDO or use Openbiblio built-in class functions if you are up to it.
	 */
	//---------------- dependencies here
	require_once("../includes/fpdf/fpdf.php"); 
	require_once("functions/card_catalog.php"); 
	require_once("class/Qtest.php"); 

	$mypass = new Qtest;
	$a_host = $mypass->getDSN2("host");
	$a_user = $mypass->getDSN2("username");
	$a_pwd = $mypass->getDSN2("pwd");
	$a_db = $mypass->getDSN2("database");
	
	$connection = mysqli_connect($a_host, $a_user, $a_pwd, $a_db);
	// ----- test if connection occurred. -----
	if (mysqli_connect_errno()) {
		die("Database connection failed: " . 
			mysqli_connect_error() . 
			" (" . mysqli_connect_errno() . ")"
		);
	}
	
	// A4 width: 219 mm
	// margins: 10 mm (both sides)
	// writable horizontal: 219mm - 10*2 = 189mm
	
	/* 
	
mysql> SELECT subfield_data FROM biblio_subfield where bibid = 1626 ORDER BY fieldid;
+-------------------+
| subfield_data     |
+-------------------+
| call number       |0
| author            |1
| title             |2
| corpo_name        |3
| subtitle          |4
| addtl_contri      |5
| statement_resp    |6
| edition           |7
| place_of_pub      |8
| publisher         |9
| date_pub          |10
| extent            |11
| other_phy_detail  |12
| dimension         |13
| accompanying_matl |14
| biblio_note_etc   |15
| isbn              |16
| subj.topical      |17
| subj.personal     |18
| subj.corpo        |19
| subj.geo          |20
| contents          |21
+-------------------+
22 rows in set (0.00 sec)

mysql> select bibid, barcode_nmbr from biblio_copy where bibid = 1389;
+-------+---------------+
| bibid | barcode_nmbr  |
+-------+---------------+
|  1389 | 0000000001956 |
|  1389 | 0000000001958 |
|  1389 | 0000000001957 |
+-------+---------------+
3 rows in set (0.00 sec)
*/
	
	if (isset($_POST['print_bibid'])) {
		
		// get book local info
		
		// ------------------------------ BIBID SET A ---------------------------- 
		
		$bibid1 = mysql_prep($_POST["bibid_fpdf"]);
		$bibid_set_a = get_subfield_all_from_bibid($bibid1);
		
		$arr_a = array();
		$i = 0;
		while($bookinfo = mysqli_fetch_assoc($bibid_set_a)) {
			$arr_a[$i] = $bookinfo['subfield_data'];
			$i+=1;
			
			// echo $arr_a[$i] . "<br />";
		}
		mysqli_free_result($bibid_set_a);
		
		// get book barcode info
		
		$arr_b = array();
		for ($j=0; $j<50; $j++) {
			$arr_b[$j] = "";
		}
		
		$bibid_set_b = get_barcode_all_from_bibid($bibid1);
		$j = 0;
		
		while($bookbarcode = mysqli_fetch_assoc($bibid_set_b)) {
			$arr_b[$j] = substr($bookbarcode['barcode_nmbr'], 7);
			
			$j+=1;
			
			// echo $arr_b[$j] . "<br />";
		}
		mysqli_free_result($bibid_set_b);
		
		// -----------------------------------------------------
		// -----------------------------------------------------
		// -----------------------------------------------------
		// -----------------------------------------------------
		// -----------------------------------------------------
		
		// PREPARE PDF 
		// 215.9mm x 279.4mm short bond
		// writable = 216 - 10x2 = 196
		
		// $pdf = new FPDF('P','mm',array(215,330)); <---------- setting paper size manually, in mm
		
		$pdf = new FPDF('p', 'mm', array(215,330));
		$pdf->AddPage();
		
		//$pdf->SetMargins(13,15);
		// set font to Arial, Bold, 14pt
		$pdf->SetFont('Courier', 'B', 10);
		
		//$pdf->Image('./images/5x8outline.png',10,10,196,127);
		$pdf->Image('./horizontal.png',0,117,210,50);
		
		// Cell syntax
		// Cell(width,height,text,border,endline,[align]) where [] means optional C,R,L
		// L = 201mm, H = 125mm
		
		$call_no = explode(" " , $arr_a[0]); // break down call no. accdg. to space between
		$pdf->Cell(55,5,'',0,1);
		$pdf->Cell(55,5,$call_no[0],0,1);
		$pdf->Cell(55,5,$call_no[1],0,1);
		$pdf->Cell(55,5,$call_no[2],0,0);
		$pdf->Cell(141,5,$arr_a[6],0,1); 
		
		$pdf->Cell(55,5,$call_no[3],0,0);

		//max length for merge_text01 is 299 characters , 5 lines
		
 		if (trim($arr_a[2]) == '') { //no title, only subtitle
			$title = '        ';
		} else {
			$title = '        ' . $arr_a[2] . ': ';
		}

		$merge_text01 =  $title . $arr_a[4] . "/ " . $arr_a[1] . ".-- " . $arr_a[8] . ": " . $arr_a[9] . ", " . $arr_a[10] . ".\n" 
														. "        " . $arr_a[11] . "; " .  $arr_a[12] . ": " . $arr_a[13];
		$length_append = 291 - strlen($merge_text01);
		
		$merge_text01 .= str_repeat(" ", $length_append); // appends length to maintain consistent amount of characters for 4 lines
		
		$pdf->MultiCell(141,5,$merge_text01,0,'L');
		
		$pdf->Cell(55,5,'',0,0);
		
		$bib_etc = "        " . $arr_a[15] ;
		$pdf->Cell(141,5,$bib_etc,0,1);
		$pdf->Cell(55,5,'',0,0);
		
		$isbn = "        ISBN: " . $arr_a[16];
		$pdf->Cell(141,5,$isbn,0,1);
		
		$pdf->Cell(55,5,'',0,0);
		$pdf->Cell(141,5,'',0,1);
		
		$pdf->Cell(55,20,'',0,0);
		
 		$merge_text02 = "        " . $arr_a[17] . " " . $arr_a[18] . " " . $arr_a[19] . " " . $arr_a[20];

		$length_append = 227 - strlen($merge_text02);
		
		$merge_text02 .= str_repeat(" ", $length_append);
		
		$pdf->MultiCell(141,5,$merge_text02,0,'L');
		
		// barcode starts here
		
		$pdf->Cell(18,5,$arr_b[0],0,0); $pdf->Cell(18,5,$arr_b[7],0,0); $pdf->Cell(18,5,$arr_b[14],0,1);
		$pdf->Cell(18,5,$arr_b[1],0,0); $pdf->Cell(18,5,$arr_b[8],0,0); $pdf->Cell(18,5,$arr_b[15],0,1);
		$pdf->Cell(18,5,$arr_b[2],0,0); $pdf->Cell(18,5,$arr_b[9],0,0); $pdf->Cell(18,5,$arr_b[16],0,1);
		$pdf->Cell(18,5,$arr_b[3],0,0); $pdf->Cell(18,5,$arr_b[10],0,0);$pdf->Cell(18,5,$arr_b[17],0,1);
		$pdf->Cell(18,5,$arr_b[4],0,0); $pdf->Cell(18,5,$arr_b[11],0,0);$pdf->Cell(18,5,$arr_b[18],0,1);
		$pdf->Cell(18,5,$arr_b[5],0,0); $pdf->Cell(18,5,$arr_b[12],0,0);$pdf->Cell(18,5,$arr_b[19],0,1);
		$pdf->Cell(18,5,$arr_b[6],0,0); $pdf->Cell(18,5,$arr_b[13],0,0);$pdf->Cell(18,5,$arr_b[20],0,1);		
		
		$pdf->Cell(55,5,'',0,1); //spacers before another bibid
		$pdf->Cell(55,5,'',0,1); //spacers before another bibid
		$pdf->Cell(55,5,'',0,1); //spacers before another bibid
		$pdf->Cell(55,5,'',0,1); //spacers before another bibid
		
		
		//Horizontal-Line-No-Background.png
		//$pdf->Image('./images/5x8outline.png',10,150,196,127);
		$pdf->Image('./horizontal.png',0,257,210,50);
		$call_no_1 = $arr_a[0]; //save the call_no_1 entried for file saving purpose in pdf

		// ------------------------------ BIBID SET B ---------------------------- 

		$bibid2 = mysql_prep($_POST["bibid_fpdf2"]);
		$bibid_set_a = get_subfield_all_from_bibid($bibid2);
		
		$arr_a = array();
		$i = 0;
		while($bookinfo = mysqli_fetch_assoc($bibid_set_a)) {
			$arr_a[$i] = $bookinfo['subfield_data'];
			$i+=1;
			
			// echo $arr_a[$i] . "<br />";
		}
		mysqli_free_result($bibid_set_a);

		// get book barcode info
		
		$arr_b = array();
		for ($j=0; $j<50; $j++) {
			$arr_b[$j] = "";
		}
		
		$bibid_set_b = get_barcode_all_from_bibid($bibid2);
		$j = 0;
		
		while($bookbarcode = mysqli_fetch_assoc($bibid_set_b)) {
			$arr_b[$j] = substr($bookbarcode['barcode_nmbr'], 7);
			
			$j+=1;
			
			// echo $arr_b[$j] . "<br />";
		}
		mysqli_free_result($bibid_set_b);
		
		// --------------------------------------------------------
		// --------------------------------------------------------
		// --------------------------------------------------------
		// --------------------------------------------------------
		// --------------------------------------------------------
		
		$call_no = explode(" " , $arr_a[0]); // break down call no. accdg. to space between
		$pdf->Cell(55,5,'',0,1);
		$pdf->Cell(55,5,$call_no[0],0,1);
		$pdf->Cell(55,5,$call_no[1],0,1);
		$pdf->Cell(55,5,$call_no[2],0,0);
		$pdf->Cell(141,5,$arr_a[6],0,1); 
		
		$pdf->Cell(55,5,$call_no[3],0,0);

		//max length for merge_text01 is 299 characters , 5 lines
		
 		if (trim($arr_a[2]) == '') { //no title, only subtitle
			$title = '        ';
		} else {
			$title = '        ' . $arr_a[2] . ': ';
		}

		$merge_text01 =  $title . $arr_a[4] . "/ " . $arr_a[1] . ".-- " . $arr_a[8] . ": " . $arr_a[9] . ", " . $arr_a[10] . ".\n" 
														. "        " . $arr_a[11] . "; " .  $arr_a[12] . ": " . $arr_a[13];
		$length_append = 291 - strlen($merge_text01);
		
		$merge_text01 .= str_repeat(" ", $length_append); // appends length to maintain consistent amount of characters for 4 lines
		
		$pdf->MultiCell(141,5,$merge_text01,0,'L');
		
		$pdf->Cell(55,5,'',0,0);
		
		$bib_etc = "        " . $arr_a[15] ;
		$pdf->Cell(141,5,$bib_etc,0,1);
		$pdf->Cell(55,5,'',0,0);
		
		$isbn = "        ISBN: " . $arr_a[16];
		$pdf->Cell(141,5,$isbn,0,1);
		
		$pdf->Cell(55,5,'',0,0);
		$pdf->Cell(141,5,'',0,1);
		
		$pdf->Cell(55,20,'',0,0);
		
 		$merge_text02 = "        " . $arr_a[17] . " " . $arr_a[18] . " " . $arr_a[19] . " " . $arr_a[20];

		$length_append = 227 - strlen($merge_text02);
		
		$merge_text02 .= str_repeat(" ", $length_append);
		
		$pdf->MultiCell(141,5,$merge_text02,0,'L');
		
		// barcode starts here
		
		$pdf->Cell(18,5,$arr_b[0],0,0); $pdf->Cell(18,5,$arr_b[7],0,0); $pdf->Cell(18,5,$arr_b[14],0,1);
		$pdf->Cell(18,5,$arr_b[1],0,0); $pdf->Cell(18,5,$arr_b[8],0,0); $pdf->Cell(18,5,$arr_b[15],0,1);
		$pdf->Cell(18,5,$arr_b[2],0,0); $pdf->Cell(18,5,$arr_b[9],0,0); $pdf->Cell(18,5,$arr_b[16],0,1);
		$pdf->Cell(18,5,$arr_b[3],0,0); $pdf->Cell(18,5,$arr_b[10],0,0);$pdf->Cell(18,5,$arr_b[17],0,1);
		$pdf->Cell(18,5,$arr_b[4],0,0); $pdf->Cell(18,5,$arr_b[11],0,0);$pdf->Cell(18,5,$arr_b[18],0,1);
		$pdf->Cell(18,5,$arr_b[5],0,0); $pdf->Cell(18,5,$arr_b[12],0,0);$pdf->Cell(18,5,$arr_b[19],0,1);
		$pdf->Cell(18,5,$arr_b[6],0,0); $pdf->Cell(18,5,$arr_b[13],0,0);$pdf->Cell(18,5,$arr_b[20],0,1);
		
		//$pdf->Image('./images/5x8outline.png',10,10,196,127);
		
		$call_no_2 = $arr_a[0]; //save the call_no_2 entried for file saving purpose in pdf
		
		$filename_format = 'Author_card-' . $call_no_1 . '--'. $call_no_2  . '.pdf';
		$pdf->Output($filename_format, 'D');
		//$pdf->Output('F', 'reports/report.pdf'); alternative way of saving the pdf
		
		// -----------------------------------------------------
		// -----------------------------------------------------
		// -----------------------------------------------------
		// -----------------------------------------------------
		// -----------------------------------------------------
		
	}