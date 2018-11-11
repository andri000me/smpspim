<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 * 
  $CI->CoreFonts = array('Arial', 'helvetica', 'times', 'symbol', 'zapfdingbats');
 * 
 */

$pdf = $this->fpdf;

$pdf->SetMargins(3, 3);
$pdf->AddPage("L", 'A5');
$pdf->SetAutoPageBreak(true, 0);

for ($i = 0; $i < 2; $i++) {
    $offset = $i * 78;

    $pdf->Image('files/aplikasi/kwitansi_keu_tu.jpg', 0, $offset + 0, 210);

    $pdf->SetXY(53, $offset + 4);
    $pdf->SetFont('Arial', '', 11);
    $pdf->MultiCell(120, 5, $ID_TJ . '/' . $KELOMPOK_TJ . '/' . date('m', strtotime($CREATED_TJ)) . '/' . date('Y', strtotime($CREATED_TJ)));

    $pdf->SetXY(83, $offset + 12);
    $pdf->SetFont('Arial', '', 11);
    $pdf->MultiCell(120, 5, $NAMA_TJ);

    $pdf->SetXY(83, $offset + 29);
    $pdf->SetFont('Arial', '', 11);
    $pdf->MultiCell(120, 5, $KETERANGAN_TJ);

    $pdf->SetXY(85, $offset + 20);
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(120, 5, $this->money->format($NOMINAL_TJ));

    $pdf->SetXY(140, $offset + 51);
    $pdf->SetFont('Arial', '', 11);
    $pdf->MultiCell(120, 5, 'Kajen, ' . date('d-m-'));

    $pdf->SetXY(193, $offset + 51);
    $pdf->SetFont('Arial', '', 11);
    $pdf->MultiCell(120, 5, date('y'));

    $terbilang = $this->money->terbilang($NOMINAL_TJ);
    $pdf->SetXY(73, $offset + 65);
    $pdf->SetFont('Arial', '', 10);
    $pdf->MultiCell(120, 5, ucwords(trim($terbilang)));
}

$pdf->Output();
