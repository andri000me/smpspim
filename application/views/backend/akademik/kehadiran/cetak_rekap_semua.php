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

foreach ($DATA as $DETAIL) {
    $pdf->AddPage("P", "A4");
//	$pdf->SetMargins(6, 0);
    $pdf->SetAutoPageBreak(true, 0);

    $pdf = $this->cetak->header_yayasan($pdf);

    $pdf->Ln(2);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 5, 'DAFTAR HADIR SISWA', 0, 0, 'C');
    $pdf->Ln();

    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(0, 5, 'TAHUN AJARAN ' . $TA, 0, 0, 'C');
    $pdf->Ln(8);

    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell(20, 4, 'Jenjang');
    $pdf->SetFont('Arial', '', 9);
    $pdf->Cell(0, 4, ': ' . $DETAIL['KELAS']->NAMA_DEPT);
    $pdf->Ln();

    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell(20, 4, 'Kelas');
    $pdf->SetFont('Arial', '', 9);
    $pdf->Cell(0, 4, ': ' . $DETAIL['KELAS']->NAMA_KELAS);
    $pdf->Ln();

    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell(20, 4, 'Wali Kelas');
    $pdf->SetFont('Arial', '', 9);
    $pdf->Cell(110, 4, ': ' . $this->cetak->nama_peg_print($DETAIL['KELAS']));
    $pdf->Cell(20, 4, 'Bulan :');
    $pdf->Ln();

    $pdf->SetFont('Arial', 'B', 8);
    $pdf->Cell(7, 4, 'No.', 1, 0, 'C');
    $pdf->Cell(17, 4, 'NIS', 1, 0, 'C');
    $pdf->Cell(42, 4, 'Nama', 1, 0, 'C');
    for ($tanggal = 1; $tanggal <= 31; $tanggal++) {
        $pdf->Cell(4, 4, $tanggal, 1, 0, 'C');
    }
    $pdf->Ln();

    $i = 1;
    $pdf->SetFont('Arial', '', 7);
    foreach ($DETAIL['DATA'] as $SISWA) {
        $pdf->Cell(7, 4, $i++, 1, 0, 'L');
        $pdf->Cell(17, 4, $SISWA->NIS_SISWA, 1, 0, 'L');
        $pdf->Cell(42, 4, $SISWA->NAMA_SISWA, 1, 0, 'L');
        for ($tanggal = 1; $tanggal <= 31; $tanggal++) {
            $pdf->Cell(4, 4, '', 1, 0, 'C');
        }
        $pdf->Ln();
    }
}
$pdf->Output();
