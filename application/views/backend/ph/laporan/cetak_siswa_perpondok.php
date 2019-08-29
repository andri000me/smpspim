<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 * 
  $this->CoreFonts = array('helvetica', 'helvetica', 'times', 'symbol', 'zapfdingbats');
 * 
 */

$pdf = $this->tcpdf;
$pdf->SetPrintHeader(false);
$pdf->SetPrintFooter(false);

$pdf->AddPage("L", $this->pengaturan->getUkuranF4());
//$pdf->SetAutoPageBreak(true, 0);

$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 5, 'DATA HAFALAN SISWA PERPONDOK', 0, 0, 'C');
$pdf->Ln();

$pdf->SetFont('helvetica', '', 11);
$pdf->Cell(0, 5, 'PONDOK ' . $pondok->NAMA_PONDOK_MPS, 0, 0, 'C');
$pdf->Ln(10);

$pdf->SetFont('helvetica', 'I', 10);
$pdf->Cell(0, 5, 'Dicetak tanggal ' . date('d-m-Y H:i:s') . ' WIB', 0, 0, 'R');
$pdf->Ln();

$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(10, 5, 'NO', 1, 0, 'C');
$pdf->Cell(28, 5, 'NIS', 1, 0, 'C');
$pdf->Cell(70, 5, 'NAMA', 1, 0, 'C');
$pdf->Cell(45, 5, 'KELAS', 1, 0, 'C');
$pdf->Cell(55, 5, 'KITAB', 1, 0, 'C');
$pdf->Cell(70, 5, 'BATASAN', 1, 0, 'C');
$pdf->Cell(30, 5, 'NILAI', 1, 0, 'C');
$pdf->Ln();

//$pdf->setRTL(true);
$pdf->SetFont('aefurat', '', 12);
$nomor = 1;
$temp = null;
foreach ($siswa as $detail) {
    if ($temp != $detail['ID_SISWA']) {
        $pdf->Cell(10, 5, $nomor++, "LRT", 0, 'C');
        $pdf->Cell(28, 5, $detail['NIS_SISWA'] == NULL ? "KELUAR" : $detail['NIS_SISWA'], "LRT", 0, 'C');
        $pdf->Cell(70, 5, $detail['NAMA_SISWA'], "LRT", 0, 'L');
        $pdf->Cell(45, 5, $detail['NAMA_KELAS'], "LRT", 0, 'L');
        $temp = $detail['ID_SISWA'];
    } else {
        $pdf->Cell(10, 5, "", "RL", 0, 'C');
        $pdf->Cell(28, 5, "", "RL", 0, 'C');
        $pdf->Cell(70, 5, "", "RL", 0, 'L');
        $pdf->Cell(45, 5, "", "RL", 0, 'L');
    }

    $pdf->Cell(55, 5, $detail['NAMA_KITAB'], 1, 0, 'R');
    $pdf->Cell(70, 5, $detail['AWAL_BATASAN'] . ' - ' . $detail['AKHIR_BATASAN'], 1, 0, 'R');
    $pdf->Cell(30, 5, $detail['NILAI_PHN'], 1, 0, 'C');
    $pdf->Ln();
}

$pdf->Cell(10, 5, "", "T", 0, 'C');
$pdf->Cell(28, 5, "", "T", 0, 'C');
$pdf->Cell(70, 5, "", "T", 0, 'L');
$pdf->Cell(45, 5, "", "T", 0, 'L');
//$pdf->setRTL(false);

$pdf->Output();
