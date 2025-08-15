<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */ 
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
			
		// ------------------------------ BIBID SET A ---------------------------- 

		$bibid1 = mysql_prep($_GET["bibid_fpdf"]);
		$book = [
    'call_no'   => get_marc_subfields($bibid1, '099', 'a'),
    'author'    => get_marc_subfields($bibid1, '100', 'a'),
    'title'     => get_marc_subfields($bibid1, '245', 'a'),
    'sub_title' => get_marc_subfields($bibid1, '245', 'b'),
    'stmt_resp' => get_marc_subfields($bibid1, '245', 'c'),
    'place'     => get_marc_subfields($bibid1, '260', 'a'),
    'publisher' => get_marc_subfields($bibid1, '260', 'b'),
    'year'      => get_marc_subfields($bibid1, '260', 'c'),
    'extent'    => get_marc_subfields($bibid1, '300', 'a'),
    'other_det' => get_marc_subfields($bibid1, '300', 'b'),
    'dimension' => get_marc_subfields($bibid1, '300', 'c'),
    'note'      => get_marc_subfields($bibid1, '504', 'a'),
    'isbn'      => get_marc_subfields($bibid1, '020', 'a'),
    'subjects'  => implode(" ", [
        get_marc_subfields($bibid1, '650', 'a'),
        get_marc_subfields($bibid1, '650', 'b'),
        get_marc_subfields($bibid1, '650', 'c'),
        get_marc_subfields($bibid1, '650', 'd')
				])
		];

		// replace $arr_a with the explicit $book

		// get book barcode info

		$barcodes =  get_trimmed_barcodes($bibid1);

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
		
		$call_no = explode(" " , $book['call_no']); // break down call no. accdg. to space between
		// check if call number is properly set
		$call_no[0] = isset($call_no[0]) ? $call_no[0] : ' ';
		$call_no[1] = isset($call_no[1]) ? $call_no[1] : ' ';
		$call_no[2] = isset($call_no[2]) ? $call_no[2] : ' ';
		$call_no[3] = isset($call_no[3]) ? $call_no[3] : ' ';
		
		$pdf->Cell(55,5,'',0,1);
		$pdf->Cell(55,5,$call_no[0],0,1);
		$pdf->Cell(55,5,$call_no[1],0,1);
		$pdf->Cell(55,5,$call_no[2],0,0);		$pdf->Cell(141,5,$book['stmt_resp'],0,1); 
		$pdf->Cell(55,5,$call_no[3],0,0);

		//max length for merge_text01 is 299 characters , 5 lines
		
 		if (trim($book['title']) == '') { //no title, only subtitle
			$title = '        ';
		} else {
			$title = '        ' . $book['title'] . ': ';
		}

		$merge_text01 =  $title . $book['sub_title'] . "/ " . $book['author'] . ".-- " . $book['place'] . ": " . $book['publisher'] . ", " . $book['year'] . ".\n" 
														. "        " . $book['extent'] . "; " .  $book['other_det'] . ": " . $book['dimension'];
		// prevent negative padding
		$length_append = max(0, 291 - strlen($merge_text01));
		
		$merge_text01 .= str_repeat(" ", $length_append); // appends length to maintain consistent amount of characters for 4 lines
		
		$pdf->MultiCell(141,5,$merge_text01,0,'L');
		
		$pdf->Cell(55,5,'',0,0);
		
		$bib_etc = "        " . $book['note'] ;
		$pdf->Cell(141,5,$bib_etc,0,1);
		$pdf->Cell(55,5,'',0,0);
		
		$isbn = "        ISBN: " . $book['isbn'];
		$pdf->Cell(141,5,$isbn,0,1);
		
		$pdf->Cell(55,5,'',0,0);
		$pdf->Cell(141,5,'',0,1);
		
		$pdf->Cell(55,20,'',0,0);
		
 		$merge_text02 = "        " . $book['subjects'];

		$length_append = 227 - strlen($merge_text02);
		
		$merge_text02 .= str_repeat(" ", $length_append);
		
		$pdf->MultiCell(141,5,$merge_text02,0,'L');
		
		// barcode starts here
		
		$pdf->Cell(18,5,safe_barcode($barcodes, 0),0,0); $pdf->Cell(18,5,safe_barcode($barcodes, 7),0,0); $pdf->Cell(18,5,safe_barcode($barcodes, 14),0,0); $pdf->Cell(18,5,safe_barcode($barcodes, 21),0,0); $pdf->Cell(18,5,safe_barcode($barcodes, 28),0,1);
		$pdf->Cell(18,5,safe_barcode($barcodes, 1),0,0); $pdf->Cell(18,5,safe_barcode($barcodes, 8),0,0); $pdf->Cell(18,5,safe_barcode($barcodes, 15),0,0); $pdf->Cell(18,5,safe_barcode($barcodes, 22),0,0); $pdf->Cell(18,5,safe_barcode($barcodes, 29),0,1);
		$pdf->Cell(18,5,safe_barcode($barcodes, 2),0,0); $pdf->Cell(18,5,safe_barcode($barcodes, 9),0,0); $pdf->Cell(18,5,safe_barcode($barcodes, 16),0,0); $pdf->Cell(18,5,safe_barcode($barcodes, 23),0,0); $pdf->Cell(18,5,safe_barcode($barcodes, 30),0,1);
		$pdf->Cell(18,5,safe_barcode($barcodes, 3),0,0); $pdf->Cell(18,5,safe_barcode($barcodes, 10),0,0); $pdf->Cell(18,5,safe_barcode($barcodes, 17),0,0); $pdf->Cell(18,5,safe_barcode($barcodes, 24),0,0);$pdf->Cell(18,5,safe_barcode($barcodes, 31),0,1);
		$pdf->Cell(18,5,safe_barcode($barcodes, 4),0,0); $pdf->Cell(18,5,safe_barcode($barcodes, 11),0,0); $pdf->Cell(18,5,safe_barcode($barcodes, 18),0,0); $pdf->Cell(18,5,safe_barcode($barcodes, 25),0,0);$pdf->Cell(18,5,safe_barcode($barcodes, 32),0,1);
		$pdf->Cell(18,5,safe_barcode($barcodes, 5),0,0); $pdf->Cell(18,5,safe_barcode($barcodes, 12),0,0); $pdf->Cell(18,5,safe_barcode($barcodes, 19),0,0); $pdf->Cell(18,5,safe_barcode($barcodes, 26),0,0);$pdf->Cell(18,5,safe_barcode($barcodes, 33),0,1);
		$pdf->Cell(18,5,safe_barcode($barcodes, 6),0,0); $pdf->Cell(18,5,safe_barcode($barcodes, 13),0,0); $pdf->Cell(18,5,safe_barcode($barcodes, 20),0,0); $pdf->Cell(18,5,safe_barcode($barcodes, 27),0,0);$pdf->Cell(18,5,safe_barcode($barcodes, 34),0,1);		
		
		$pdf->Cell(55,5,'',0,1); //spacers before another bibid
		$pdf->Cell(55,5,'',0,1); //spacers before another bibid
		$pdf->Cell(55,5,'',0,1); //spacers before another bibid
		$pdf->Cell(55,5,'',0,1); //spacers before another bibid
		
		
		//Horizontal-Line-No-Background.png
		//$pdf->Image('./images/5x8outline.png',10,150,196,127);
		$pdf->Image('./horizontal.png',0,257,210,50);
		$call_no_1 = $book['call_no']; //save the call_no_1 entried for file saving purpose in pdf

		// ------------------------------ BIBID SET B ---------------------------- 

		$bibid2 = mysql_prep($_GET["bibid_fpdf2"]);

		$book = [
    'call_no'   => get_marc_subfields($bibid2, '099', 'a'),
    'author'    => get_marc_subfields($bibid2, '100', 'a'),
    'title'     => get_marc_subfields($bibid2, '245', 'a'),
    'sub_title' => get_marc_subfields($bibid2, '245', 'b'),
    'stmt_resp' => get_marc_subfields($bibid2, '245', 'c'),
    'place'     => get_marc_subfields($bibid2, '260', 'a'),
    'publisher' => get_marc_subfields($bibid2, '260', 'b'),
    'year'      => get_marc_subfields($bibid2, '260', 'c'),
    'extent'    => get_marc_subfields($bibid2, '300', 'a'),
    'other_det' => get_marc_subfields($bibid2, '300', 'b'),
    'dimension' => get_marc_subfields($bibid2, '300', 'c'),
    'note'      => get_marc_subfields($bibid2, '504', 'a'),
    'isbn'      => get_marc_subfields($bibid2, '020', 'a'),
    'subjects'  => implode(" ", [
        get_marc_subfields($bibid2, '650', 'a'),
        get_marc_subfields($bibid2, '650', 'b'),
        get_marc_subfields($bibid2, '650', 'c'),
        get_marc_subfields($bibid2, '650', 'd')
				])
		];		

		// get book barcode info

		$barcodes =  get_trimmed_barcodes($bibid2);
		
		// --------------------------------------------------------
		
		$call_no = explode(" " , $book['call_no']); // break down call no. accdg. to space between
		// check if call number is properly set
		$call_no[0] = isset($call_no[0]) ? $call_no[0] : ' ';
		$call_no[1] = isset($call_no[1]) ? $call_no[1] : ' ';
		$call_no[2] = isset($call_no[2]) ? $call_no[2] : ' ';
		$call_no[3] = isset($call_no[3]) ? $call_no[3] : ' ';

		$pdf->Cell(55,5,'',0,1);
		$pdf->Cell(55,5,$call_no[0],0,1);
		$pdf->Cell(55,5,$call_no[1],0,1);
		$pdf->Cell(55,5,$call_no[2],0,0);		$pdf->Cell(141,5,$book['stmt_resp'],0,1); 
		$pdf->Cell(55,5,$call_no[3],0,0);

		//max length for merge_text01 is 299 characters , 5 lines
		
 		if (trim($book['title']) == '') { //no title, only subtitle
			$title = '        ';
		} else {
			$title = '        ' . $book['title'] . ': ';
		}

		$merge_text01 =  $title . $book['sub_title'] . "/ " . $book['author'] . ".-- " . $book['place'] . ": " . $book['publisher'] . ", " . $book['year'] . ".\n" 
														. "        " . $book['extent'] . "; " .  $book['other_det'] . ": " . $book['dimension'];
		// prevent negative padding
		$length_append = max(0, 291 - strlen($merge_text01));
		
		$merge_text01 .= str_repeat(" ", $length_append); // appends length to maintain consistent amount of characters for 4 lines
		
		$pdf->MultiCell(141,5,$merge_text01,0,'L');
		
		$pdf->Cell(55,5,'',0,0);
		
		$bib_etc = "        " . $book['note'];
		$pdf->Cell(141,5,$bib_etc,0,1);
		$pdf->Cell(55,5,'',0,0);
		
		$isbn = "        ISBN: " . $book['isbn'];
		$pdf->Cell(141,5,$isbn,0,1);
		
		$pdf->Cell(55,5,'',0,0);
		$pdf->Cell(141,5,'',0,1);
		
		$pdf->Cell(55,20,'',0,0);
		
		$merge_text02 = "        " . $book['subjects'];

		$length_append = 227 - strlen($merge_text02);
		
		$merge_text02 .= str_repeat(" ", $length_append);
		
		$pdf->MultiCell(141,5,$merge_text02,0,'L');
		
		// barcode starts here
		
		$pdf->Cell(18,5,safe_barcode($barcodes, 0),0,0); $pdf->Cell(18,5,safe_barcode($barcodes, 7),0,0); $pdf->Cell(18,5,safe_barcode($barcodes, 14),0,0); $pdf->Cell(18,5,safe_barcode($barcodes, 21),0,0); $pdf->Cell(18,5,safe_barcode($barcodes, 28),0,1);
		$pdf->Cell(18,5,safe_barcode($barcodes, 1),0,0); $pdf->Cell(18,5,safe_barcode($barcodes, 8),0,0); $pdf->Cell(18,5,safe_barcode($barcodes, 15),0,0); $pdf->Cell(18,5,safe_barcode($barcodes, 22),0,0); $pdf->Cell(18,5,safe_barcode($barcodes, 29),0,1);
		$pdf->Cell(18,5,safe_barcode($barcodes, 2),0,0); $pdf->Cell(18,5,safe_barcode($barcodes, 9),0,0); $pdf->Cell(18,5,safe_barcode($barcodes, 16),0,0); $pdf->Cell(18,5,safe_barcode($barcodes, 23),0,0); $pdf->Cell(18,5,safe_barcode($barcodes, 30),0,1);
		$pdf->Cell(18,5,safe_barcode($barcodes, 3),0,0); $pdf->Cell(18,5,safe_barcode($barcodes, 10),0,0); $pdf->Cell(18,5,safe_barcode($barcodes, 17),0,0); $pdf->Cell(18,5,safe_barcode($barcodes, 24),0,0);$pdf->Cell(18,5,safe_barcode($barcodes, 31),0,1);
		$pdf->Cell(18,5,safe_barcode($barcodes, 4),0,0); $pdf->Cell(18,5,safe_barcode($barcodes, 11),0,0); $pdf->Cell(18,5,safe_barcode($barcodes, 18),0,0); $pdf->Cell(18,5,safe_barcode($barcodes, 25),0,0);$pdf->Cell(18,5,safe_barcode($barcodes, 32),0,1);
		$pdf->Cell(18,5,safe_barcode($barcodes, 5),0,0); $pdf->Cell(18,5,safe_barcode($barcodes, 12),0,0); $pdf->Cell(18,5,safe_barcode($barcodes, 19),0,0); $pdf->Cell(18,5,safe_barcode($barcodes, 26),0,0);$pdf->Cell(18,5,safe_barcode($barcodes, 33),0,1);
		$pdf->Cell(18,5,safe_barcode($barcodes, 6),0,0); $pdf->Cell(18,5,safe_barcode($barcodes, 13),0,0); $pdf->Cell(18,5,safe_barcode($barcodes, 20),0,0); $pdf->Cell(18,5,safe_barcode($barcodes, 27),0,0);$pdf->Cell(18,5,safe_barcode($barcodes, 34),0,1);		
				
		//$pdf->Image('./images/5x8outline.png',10,10,196,127);
		
		$call_no_2 = $book['call_no']; //save the call_no_2 entried for file saving purpose in pdf
		
		$filename_format = 'Author_card-' . $call_no_1 . '--'. $call_no_2  . '.pdf';
		$pdf->Output($filename_format, 'D');
		//$pdf->Output('F', 'reports/report.pdf'); alternative way of saving the pdf
		
		// -----------------------------------------------------
