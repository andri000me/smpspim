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
    $pdf->Cell(0, 5, 'DAFTAR HADIR DAUROH BAHASA ARAB TAHUN AJARAN ' . $TA, 0, 0, 'C');
    $pdf->Ln(8);

    // $pdf->SetFont('Arial', 'B', 9);
    // $pdf->Cell(20, 4, 'Jenjang');
    // $pdf->SetFont('Arial', '', 9);
    // $pdf->Cell(0, 4, ': ' . $DETAIL['KELAS']->NAMA_DEPT);
    // $pdf->Ln();

    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell(10, 4, 'Kelas');
    $pdf->Cell(40, 4, ': ' . $DETAIL['KELAS']->NAMA_KELAS);
    $pdf->Cell(20, 4, 'Wali Kelas');
    $pdf->Cell(60, 4, ': ' . $this->cetak->nama_peg_print($DETAIL['KELAS']));
    $pdf->Cell(20, 4, 'Pengampu :');
    $pdf->Ln();

    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell(5, 5, 'No', 1, 0, 'C');
    $pdf->Cell(21, 5, 'NIS', 1, 0, 'C');
    $pdf->Cell($widthNama, 5, 'Nama', 1, 0, 'C');
    for ($tanggal = 1; $tanggal <= 31; $tanggal++) {
        $pdf->Cell(4, 5, $tanggal, 1, 0, 'C');
    }
    $pdf->Ln();

    $i = 1;
    $pdf->SetFont('Arial', '', 9);
    foreach ($DETAIL['DATA'] as $SISWA) {
        if($SISWA->AKTIF_AS) $pdf->setFillColor(255, 255, 255);
        else $pdf->setFillColor(128,128,128);
        
        $pdf->Cell(5, 5, $SISWA->NO_ABSEN_AS, 1, 0, 'L', TRUE);
        $pdf->Cell(21, 5, $SISWA->AKTIF_AS ? $SISWA->NIS_SISWA : 'KELUAR', 1, 0, $SISWA->AKTIF_AS ? 'L' : 'C', TRUE);
        $pdf->Cell($widthNama, 5, $this->pdf_handler->cut_text($pdf, $SISWA->NAMA_SISWA, $widthNama), 1, 0, 'L', TRUE);
        for ($tanggal = 1; $tanggal <= 31; $tanggal++) {
            $pdf->Cell(4, 5, '', 1, 0, 'C', TRUE);
        }
        $pdf->Ln();
    }

    $pdf->Cell(0, 4, 'Keterangan: *: Masuk, S: Sakit, I: Izin, L: Lari, T: Terlambat');
    $pdf->Ln();
    $pdf->Cell(10);
    $pdf->MultiCell(0, 4, 'Izin susulan karena sakit bisa diterima apabila ada surat keterangan sakit dari dokter yang tidak melebihi 3 hari terhitung sejak hari pertama siswa tidak masuk sekolah disertai izin dari orang tua/pengasuh.');
    $pdf->Ln(1);
    $pdf->Cell(10);
    $pdf->Cell(0, 4, 'Surat harus disampaikan wali kelas.');
    $pdf->Ln();
    $pdf->Cell(0, 4, 'Catatan:');
    $pdf->Ln();
    $pdf->Cell(10);
    $pdf->Cell(0, 4, '_____________________________________________________________________________________________________');
    $pdf->Ln();
    $pdf->Cell(10);
    $pdf->Cell(0, 4, '_____________________________________________________________________________________________________');
    $pdf->Ln();
    $pdf->Cell(10);
    $pdf->Cell(0, 4, '_____________________________________________________________________________________________________');

    // while ($i <= 46) {
    //     $pdf->Cell(5, 5.5, $i++, 1, 0, 'L');
    //     $pdf->Cell(21, 5.5, '', 1, 0, 'L');
    //     $pdf->Cell($widthNama, 5.5, '', 1, 0, 'L');
    //     for ($tanggal = 1; $tanggal <= 31; $tanggal++) {
    //         $pdf->Cell(4, 5.5, '', 1, 0, 'C');
    //     }
    //     $pdf->Ln();
    // }
    // break;
}
$pdf->Output();
