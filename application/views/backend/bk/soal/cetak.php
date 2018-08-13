<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 * 
  $this->CoreFonts = array('courier', 'helvetica', 'times', 'symbol', 'zapfdingbats');
 * 
 */

$pdf = $this->fpdf;

$pdf->AddPage("P", 'A4');
//	$pdf->SetMargins(6, 0);
$pdf->SetAutoPageBreak(true, 0);

$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 5, 'SOAL DCM', 0, 0, 'C');
$pdf->Ln();

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 5, 'BIMBINGAN KONSELING', 0, 0, 'C');
$pdf->Ln();

$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 5, strtoupper($this->pengaturan->getNamaLembaga() . ' (' . $this->pengaturan->getNamaLembagaSingk() . ') ' . $this->pengaturan->getDesa()), 0, 0, 'C');
$pdf->Ln(10);

$pdf->Line(10, 25, 200, 25);

$pdf->SetFont('Arial', '', 11);
$pdf->Cell(23, 5, 'NIS/Nama', 0, 0, 'L');
$pdf->Cell(0, 5, ': _____________________/________________________________', 0, 0, 'L');
$pdf->Ln(6);

$pdf->Cell(23, 5, 'Kelas', 0, 0, 'L');
$pdf->Cell(0, 5, ': __________________________________________', 0, 0, 'L');
$pdf->Ln(8);

$temp_kel = null;
$temp_kat = null;

foreach ($data as $detail) {
    if ($temp_kel != $detail->ID_BKKEL) {
        $temp_kel = $detail->ID_BKKEL;

        $pdf->Ln(2);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(5, 5, $detail->URUTAN_BKKEL . '.', 0, 0, 'L');
        $pdf->Cell(0, 5, $detail->NAMA_BKKEL, 0, 0, 'L');
        $pdf->Ln();
    }

    if ($temp_kat != $detail->ID_BKKAT) {
        $temp_kat = $detail->ID_BKKAT;

        $pdf->Ln(1);
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(5);
        $pdf->Cell(5, 5, $detail->URUTAN_BKKAT . '.', 0, 0, 'L');
        $pdf->Cell(0, 5, $detail->NAMA_BKKAT, 0, 0, 'L');
        $pdf->Ln();
    }

    $pdf->SetFont('Arial', '', 11);
    $pdf->Cell(10);
    $pdf->Cell(5, 4, $detail->URUTAN_BKSOAL . '.', 0, 0, 'L');
    $pdf->Cell(171, 4, $detail->KONTEN_BKSOAL, 0, 0, 'L');
    $pdf->Cell(4, 4, "   ", 1, 0, 'L');
    $pdf->Ln(5);
}

$pdf->Output();
