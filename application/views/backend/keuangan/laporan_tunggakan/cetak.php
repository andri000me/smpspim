<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 * 
  $pdf->CoreFonts = array('Arial', 'helvetica', 'times', 'symbol', 'zapfdingbats');
 * 
 */

$pdf = $this->fpdf;

$pdf->AddPage("P", 'A4');
//$pdf->SetAutoPageBreak(true, 0);

$pdf->SetFont('Arial', 'B', 12);

$pdf->Cell(0, 5, 'LAPORAN REKAPITULASI TAGIHAN SISWA', 0, 0, 'C');
$pdf->Ln();

$pdf->SetFont('Arial', '', 11);
$pdf->Cell(0, 5, 'DICETAK TANGGAL ' . date('d-m-Y H:i:s').' WIB', 0, 0, 'C');
$pdf->Ln(10);

$pdf->SetFont('Arial', 'B', 10);

$pdf->Cell(8, 12, 'No', 1, 0, 'C');
$pdf->Cell(20, 12, 'TA', 1, 0, 'C');
$pdf->Cell(20, 12, 'JENJANG', 1, 0, 'C');
$pdf->Cell(70, 6, 'PUNYA TAGIHAN', 1, 0, 'C');
$pdf->Cell(70, 6, 'TIDAK PUNYA TAGIHAN', 1, 0, 'C');
$pdf->Ln(6);

$pdf->Cell(48);
$pdf->Cell(32, 6, 'JUMLAH SISWA', 1, 0, 'C');
$pdf->Cell(38, 6, 'JUMLAH NOMINAL', 1, 0, 'C');
$pdf->Cell(32, 6, 'JUMLAH SISWA', 1, 0, 'C');
$pdf->Cell(38, 6, 'JUMLAH NOMINAL', 1, 0, 'C');
$pdf->Ln(6);

$pdf->SetFont('Arial', '', 10);
$nomor = 1;
foreach ($ta as $detail_ta) {
    $pdf->Cell(8, 6, $nomor++, 1, 0, 'C');
    $pdf->Cell(20, 6, $detail_ta->NAMA_TA, 'LRT', 0, 'L');
    $start = true;
    foreach ($dept as $detail_dept) {
        if (!$start) {
            $pdf->Cell(8, 6, $nomor++, 1, 0, 'C');
            $pdf->Cell(20, 6, '', 'RL', 0, 'L');
        }

        $pdf->Cell(20, 6, $detail_dept->ID_DEPT, 1, 0, 'C');

        if (isset($tagihan[$detail_ta->ID_TA][$detail_dept->ID_DEPT])) {
            $pdf->Cell(32, 6, number_format($tagihan[$detail_ta->ID_TA][$detail_dept->ID_DEPT]->JUMLAH_SISWA, 0, ',', '.'), 1, 0, 'R');
            $pdf->Cell(38, 6, $this->money->format($tagihan[$detail_ta->ID_TA][$detail_dept->ID_DEPT]->JUMLAH_NOMINAL), 1, 0, 'R');
        } else {
            $pdf->Cell(32, 6, '-', 1, 0, 'C');
            $pdf->Cell(38, 6, '-', 1, 0, 'C');
        }

        if (isset($non_tagihan[$detail_ta->ID_TA][$detail_dept->ID_DEPT])) {
            $pdf->Cell(32, 6, number_format($non_tagihan[$detail_ta->ID_TA][$detail_dept->ID_DEPT]->JUMLAH_SISWA, 0, ',', '.'), 1, 0, 'R');
            $pdf->Cell(38, 6, $this->money->format($non_tagihan[$detail_ta->ID_TA][$detail_dept->ID_DEPT]->JUMLAH_NOMINAL), 1, 0, 'R');
        } else {
            $pdf->Cell(32, 6, '-', 1, 0, 'C');
            $pdf->Cell(38, 6, '-', 1, 0, 'C');
        }

        $start = false;
        $pdf->Ln();
    }
}
$pdf->Cell(48, 6, '', 'T');

$pdf->Output();
