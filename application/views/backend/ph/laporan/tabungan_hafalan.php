<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 * 
  $this->CoreFonts = array('Arial', 'helvetica', 'times', 'symbol', 'zapfdingbats');
 * 
 */

$pdf = $this->fpdf;

$pdf->AddPage("P", 'A4');
//$pdf->SetAutoPageBreak(true, 0);

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 5, 'REKAPITULASI TABUNGAN HAFALAN SISWA PERJENJANG', 0, 0, 'C');
$pdf->Ln();

$pdf->SetFont('Arial', '', 11);
$pdf->Cell(0, 5, 'TAHUN AJARAN ' . strtoupper($this->session->userdata('NAMA_TA_ACTIVE')), 0, 0, 'C');
$pdf->Ln(10);

$pdf->SetFont('Arial', 'I', 10);
$pdf->Cell(0, 5, 'Dicetak tanggal ' . date('d-m-Y H:i:s') . ' WIB', 0, 0, 'R');
$pdf->Ln();

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(10, 5, 'No', 1, 0, 'C');
$pdf->Cell(80, 5, 'Kelas', 1, 0, 'C');
$pdf->Cell(50, 5, 'Jumlah Siswa', 1, 0, 'C');
$pdf->Ln();

$pdf->SetFont('Arial', '', 10);
$nomor = 1;
foreach ($kelas as $detail) {
    $pdf->Cell(10, 5, $nomor++, 1, 0, 'C');
    $pdf->Cell(80, 5, $detail->NAMA_KELAS, 1, 0, 'L');
    $pdf->Cell(50, 5, isset($tabungan[$detail->ID_KELAS]) ? $tabungan[$detail->ID_KELAS] : '-', 1, 0, 'C');

    $pdf->Ln();
}

$pdf->Output();
