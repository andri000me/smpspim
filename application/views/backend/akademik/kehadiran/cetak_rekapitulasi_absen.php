<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 * 
  $this->CoreFonts = array('courier', 'helvetica', 'times', 'symbol', 'zapfdingbats');
 * 
 */

$widthNama = 95;
$pdf = $this->fpdf;

if (!isset($DATA)) {
    echo "<h1>TIDAK ADA SISWA DIKELAS INI</h1>";
    exit();
}

foreach ($DATA as $DETAIL) {
    $pdf->SetLeftMargin(12);
    $pdf->SetRightMargin(6);
    $pdf->AddPage("P", array(215, 330));
    $pdf->SetAutoPageBreak(true, 0);

    $pdf = $this->cetak->header_yayasan($pdf);

    $pdf->Ln(2);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 5, 'REKAPITULASI ABSENSI KBM ' . $this->session->userdata('NAMA_CAWU_ACTIVE') . ' TAHUN AJARAN ' . $TA, 0, 0, 'C');
    $pdf->Ln(8);

    // $pdf->SetFont('Arial', 'B', 9);
    // $pdf->Cell(20, 4, 'Jenjang');
    // $pdf->SetFont('Arial', '', 9);
    // $pdf->Cell(0, 4, ': ' . $DETAIL['KELAS']->NAMA_DEPT);
    // $pdf->Ln();

    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell(20, 4, 'Kelas');
    $pdf->Cell(40, 4, ': ' . $DETAIL['KELAS']->NAMA_KELAS);
    $pdf->Cell(0, 4, 'Wali Kelas: ' . $this->cetak->nama_peg_print($DETAIL['KELAS']), 0, 0, 'C');
    $pdf->Ln(6);

    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell(10, 5, 'No.', 1, 0, 'C');
    $pdf->Cell(25, 5, 'NIS', 1, 0, 'C');
    $pdf->Cell($widthNama, 5, 'Nama', 1, 0, 'C');
    $pdf->Cell(20, 5, 'SAKIT', 1, 0, 'C');
    $pdf->Cell(20, 5, 'IZIN', 1, 0, 'C');
    $pdf->Cell(20, 5, 'LARI', 1, 0, 'C');
    $pdf->Ln();

    $i = 1;
    $pdf->SetFont('Arial', '', 9);
    foreach ($DETAIL['DATA'] as $SISWA) {
        if ($SISWA->AKTIF_AS)
            $pdf->setFillColor(255, 255, 255);
        else
            $pdf->setFillColor(128, 128, 128);

        $pdf->Cell(10, 6, $SISWA->NO_ABSEN_AS, 1, 0, 'C', TRUE);
        $pdf->Cell(25, 6, $SISWA->AKTIF_AS ? $SISWA->NIS_SISWA : 'KELUAR', 1, 0, $SISWA->AKTIF_AS ? 'L' : 'C', TRUE);
        $pdf->Cell($widthNama, 6, $this->pdf_handler->cut_text($pdf, $SISWA->NAMA_SISWA, $widthNama), 1, 0, 'L', TRUE);
        $pdf->Cell(20, 6, $SISWA->JUMLAH_SAKIT, 1, 0, 'C', TRUE);
        $pdf->Cell(20, 6, $SISWA->JUMLAH_IZIN, 1, 0, 'C', TRUE);
        $pdf->Cell(20, 6, $SISWA->JUMLAH_ALPHA, 1, 0, 'C', TRUE);
        $pdf->Ln();
    }
}
$pdf->Output();
