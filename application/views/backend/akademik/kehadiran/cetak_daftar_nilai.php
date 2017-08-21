<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 * 
  $this->CoreFonts = array('courier', 'helvetica', 'times', 'symbol', 'zapfdingbats');
 * 
 */

$widthNama = 48;
$pdf = $this->tcpdf;

$pdf->SetPrintHeader(false);
$pdf->SetPrintFooter(false);

if (!isset($DATA)) {
    echo "<h1>TIDAK ADA SISWA DIKELAS INI</h1>";
    exit();
}

foreach ($DATA as $DETAIL) {
    $pdf->SetLeftMargin(10);
    $pdf->SetRightMargin(6);
    $pdf->SetTopMargin(6);
    $pdf->AddPage("P", array(215, 330));
    $pdf->SetAutoPageBreak(true, 0);

//    $pdf = $this->cetak->header_yayasan($pdf);

    $pdf->Ln(2);
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 5, 'DAFTAR NILAI TAHUN AJARAN ' . $TA, 0, 0, 'C');
    $pdf->Ln(10);

    // $pdf->SetFont('helvetica', 'B', 9);
    // $pdf->Cell(22, 4, 'Jenjang');
    // $pdf->SetFont('helvetica', '', 9);
    // $pdf->Cell(0, 4, ': ' . $DETAIL['KELAS']->NAMA_DEPT);
    // $pdf->Ln();

    $pdf->SetFont('helvetica', 'B', 9);
    $pdf->Cell(10, 4, 'Kelas');
    $pdf->Cell(40, 4, ': ' . $DETAIL['KELAS']->NAMA_KELAS);
    $pdf->Cell(17, 4, 'Wali Kelas');
    $pdf->Cell(70, 4, ': ' . $this->cetak->nama_peg_print($DETAIL['KELAS']));
    $pdf->Cell(22, 4, 'Matapelajaran :');
    $pdf->Ln();

    $widthNama = 40;
    $widthNilai = 7.5;

    $pdf->SetFont('helvetica', 'B', 9);

    $pdf->Cell(5, 22, 'No.', 1, 0, 'C');
    $pdf->Cell(22, 22, 'No. Induk', 1, 0, 'C');
    $pdf->Cell($widthNama, 22, 'Nama', 1, 0, 'C');

    for ($i = 0; $i < 3; $i++) {
        $pdf->Cell($widthNilai * 3, 22, 'Tugas', 1, 0, 'C');
        $x = $pdf->GetX();
        $y = $pdf->GetY();
        $pdf->SetXY($x, $y + 22);
        $pdf->StartTransform();
        $pdf->Rotate(90);
        $pdf->Cell(22, $widthNilai, 'Sub Sumatif', 1, 0, 'C');
        $pdf->StopTransform();
        $pdf->SetXY($x + $widthNilai, $y + 22);
        $x = $pdf->GetX();
        $y = $pdf->GetY();
        $pdf->StartTransform();
        $pdf->Rotate(90);
        $pdf->Cell(22, $widthNilai, 'U. Cawu ' . $this->date_format->toRomawi($i + 1), 1, 0, 'C');
        $pdf->StopTransform();
        $pdf->SetXY($x + $widthNilai, $y);
        $x = $pdf->GetX();
        $y = $pdf->GetY();
        $pdf->StartTransform();
        $pdf->Rotate(90);
        $pdf->Cell(22, $widthNilai, 'Rapor', 1, 0, 'C');
        $pdf->StopTransform();
        $pdf->SetXY($x + $widthNilai, $y - 22);
    }
    $pdf->Ln(22);

    $pdf->SetFont('helvetica', '', 9);

    $i = 1;
    foreach ($DETAIL['DATA'] as $SISWA) {
        if($SISWA->AKTIF_AS) $pdf->setFillColor(255, 255, 255);
        else $pdf->setFillColor(128,128,128);
        
        $pdf->Cell(5, 5.5, $SISWA->NO_ABSEN_AS, 1, 0, 'L', TRUE);
        $pdf->Cell(22, 5.5, $SISWA->AKTIF_AS ? $SISWA->NIS_SISWA : 'KELUAR', 1, 0, $SISWA->AKTIF_AS ? 'L' : 'C', TRUE);
        $pdf->Cell($widthNama, 5.5, $this->pdf_handler->cut_text($pdf, $SISWA->NAMA_SISWA, $widthNama), 1, 0, 'L', TRUE);
        for ($tanggal = 1; $tanggal <= 18; $tanggal++) {
            $pdf->Cell($widthNilai, 5.5, '', 1, 0, 'C', TRUE);
        }
        $pdf->Ln();
    }
}
$pdf->Output();
