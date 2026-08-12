<?php
  /* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
   * See the file COPYRIGHT.html for more details.
   */
  require_once("../shared/guard_doggy.php");
  require_once("../includes/fpdf/fpdf.php");
  require_once __DIR__ . '/../autoload.php';
  use Card_catalog\CardCatalog;

  $catalog = new CardCatalog();

    function draw_card($pdf, $book, $card_type, $y_offset) {
        // Main heading (Author, Title, or Subject)
        $pdf->SetXY(40, $y_offset + 5);
        $pdf->SetFont('Courier', 'B', 11);
        if ($card_type == 'title') {
            $heading = $book['title'];
        } else if ($card_type == 'subject') {
            $heading = strtoupper($book['subjects']);
        } else { // Author is the default
            $heading = $book['author'];
        }
        $pdf->Cell(0, 5, $heading, 0, 1);

        // Call Number
        $pdf->SetFont('Courier', '', 10);
        $pdf->SetXY(20, $y_offset + 15);
        $call_no_parts = explode(" ", $book['call_no']);
        foreach ($call_no_parts as $part) {
            $pdf->Cell(20, 5, $part, 0, 2);
            $pdf->SetX(20);
        }

        // Main content block (indented)
        $pdf->SetXY(40, $pdf->GetY() + 5); // Position relative to call number
        $title_line = $book['title'];
        if (!empty($book['sub_title'])) {
            $title_line .= ": " . $book['sub_title'];
        }
        if (!empty($book['stmt_resp'])) {
            $title_line .= " / " . $book['stmt_resp'];
        }
        $pdf->MultiCell(150, 5, $title_line, 0, 'L');

        $pdf->SetX(40);
        $pub_line = $book['place'] . ": " . $book['publisher'] . ", " . $book['year'] . ".";
        $pdf->MultiCell(150, 5, $pub_line, 0, 'L');

        $pdf->SetX(40);
        $phys_desc = $book['extent'];
        if (!empty($book['other_det'])) {
            $phys_desc .= " : " . $book['other_det'];
        }
        if (!empty($book['dimension'])) {
            $phys_desc .= " ; " . $book['dimension'];
        }
        $pdf->MultiCell(150, 5, $phys_desc, 0, 'L');
        $pdf->Ln(5);

        // Notes and ISBN
        if (!empty($book['note'])) {
            $pdf->SetX(40);
            $pdf->MultiCell(150, 5, $book['note'], 0, 'L');
        }
        if (!empty($book['isbn'])) {
            $pdf->SetX(40);
            $pdf->MultiCell(150, 5, "ISBN: " . $book['isbn'], 0, 'L');
        }
        $pdf->Ln(10);

        // Tracings (Subjects and other entries)
        $pdf->SetX(45);
        $pdf->MultiCell(150, 5, "1. " . $book['subjects'] . ".", 0, 'L');
        $pdf->SetX(45);
        $pdf->MultiCell(150, 5, "I. Title.", 0, 'L');
    }

    // --- Main Script ---

    $bibid1 = (int)$_GET["bibid_fpdf"];
    $bibid2 = (int)$_GET["bibid_fpdf2"];
    $card_type = $_GET["card_type"];

    $book1_data = $catalog->get_book_details($bibid1);
    $book2_data = $catalog->get_book_details($bibid2);

    $pdf = new FPDF('P', 'mm', array(215.9, 279.4)); // Standard Letter size
    $pdf->SetMargins(10, 10);
    $pdf->AddPage();

    // First Card (top half of the page)
    draw_card($pdf, $book1_data, $card_type, 10);

    // Second Card (bottom half of the page)
    draw_card($pdf, $book2_data, $card_type, 145); // 10mm top margin + 135mm for first card area

  $filename_format = ucfirst($card_type) . '_card-' . $book1_data['call_no'] . '--'. $book2_data['call_no']  . '.pdf';
  $pdf->Output($filename_format, 'D');
?>