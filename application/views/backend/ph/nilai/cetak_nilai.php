<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 * 
  $this->CoreFonts = array('courier', 'helvetica', 'times', 'symbol', 'zapfdingbats');
 * 
 */

$pdf = $this->tcpdf;

$pdf->SetPrintHeader(false);
$pdf->SetPrintFooter(false);

$pdf->AddPage("P", $this->pengaturan->getUkuranF4());
$pdf->SetAutoPageBreak(true, 0);

foreach ($data as $detail) {
    $kelas = $detail['kelas'];
    $kitab = $detail['kitab'];
    $siswa = $detail['siswa'];

    $pdf->SetFont('helvetica', 'B', 11);
    $pdf->Cell(0, 5, 'NILAI HAFALAN KELAS ' . strtoupper($kelas->NAMA_KELAS), 0, 0, 'C');
    $pdf->Ln(10);

    $pdf->SetFont('helvetica', 'B', 9);
    $pdf->Cell(8, 15, 'No', 1, 0, 'C');
    $pdf->Cell(24, 15, 'NIS', 1, 0, 'C');
    $pdf->Cell(45, 15, 'Nama', 1, 0, 'C');
    $pdf->Cell(10, 15, 'Lari', 1, 0, 'C');
    $pdf->Cell(75, 5, 'Nilai', 1, 0, 'C');
    $pdf->Cell(10, 15, 'Total', 1, 0, 'C');
    $pdf->Cell(24, 15, 'Hasil', 1, 0, 'C');
    $pdf->Ln(5);

    $pdf->setRTL(true);
    $pdf->SetFont('aefurat', '', 9);
    $pdf->Cell(33);
    foreach ($kitab as $item) {
        $pdf->Cell(75 / count($kitab), 5, $item->NAMA_KITAB, 'RLT', 0, 'C');
    }
    $pdf->Ln();
    
    $pdf->Cell(33);
    foreach ($kitab as $item) {
        $pdf->Cell(75 / count($kitab), 5, $item->AWAL_BATASAN.' - '.$item->AKHIR_BATASAN, 'RLB', 0, 'C');
    }
    $pdf->setRTL(false);
    $pdf->Ln(5);

    $pdf->SetFont('helvetica', '', 9);
    foreach ($siswa as $item) {
        $pdf->Cell(8, 5, $item['NO_ABSEN_AS'], 1, 0, 'L');
        $pdf->Cell(24, 5, $item['NIS_SISWA'], 1, 0, 'L');
        $pdf->Cell(45, 5, $this->pdf_handler->cut_text($pdf, $item['NAMA_SISWA'], 45), 1, 0, 'L');
        $pdf->Cell(10, 5, $item['LARI'], 1, 0, 'C');
        foreach ($kitab as $item_kitab) {
            $pdf->Cell(75 / count($kitab), 5, $item[$item_kitab->ID_KITAB], 1, 0, 'C');
        }
        $pdf->Cell(10, 5, $item['NILAI_PNH'], 1, 0, 'C');
        $pdf->Cell(24, 5, $item['STATUS_PNH'], 1, 0, 'L');
        $pdf->Ln();
    }
}

$pdf->Output();
