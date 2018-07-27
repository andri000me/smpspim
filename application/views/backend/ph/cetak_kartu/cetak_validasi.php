<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 * 
  $this->CoreFonts = array('dejavusans', 'helvetica', 'times', 'symbol', 'zapfdingbats');
 * 
 */

$pdf = $this->tcpdf;
$f4 = $this->pengaturan->getUkuranF4();

$pdf->SetPrintHeader(false);
$pdf->SetPrintFooter(false);

$pdf->AddPage("P", $f4);
$pdf->SetAutoPageBreak(true, 0);

$pdf->SetFont('times', '', 14);
$pdf->Cell(0, 5, 'PANITIA PELAKSANAAN DAN PENYEMAAN HAFALAN TA ' . $this->session->userdata("NAMA_TA_ACTIVE"), 0, 0, 'C');
$pdf->Ln();

$pdf->SetFont('times', 'B', 16);
$pdf->Cell(0, 5, strtoupper($this->pengaturan->getNamaLembaga()), 0, 0, 'C');
$pdf->Ln();

$pdf->SetFont('times', '', 12);
$pdf->Cell(0, 5, strtoupper($this->pengaturan->getDesa() . ' - ' . $this->pengaturan->getKecamatan() . ' - ' . $this->pengaturan->getKabupaten() . ' ' . $this->pengaturan->getKodepos()), 0, 0, 'C');
$pdf->Ln(10);

$pdf->SetFont('times', '', 12);
$pdf->MultiCell(0, 5, 'Bersama ini kami daftarkan siswa/siswi ' . $this->pengaturan->getNamaLembaga() . ' untuk dilakukan penyemaan hafalan oleh penyemak P3H sebagaimana ketentuan yang berlaku.', 0, 'L');
$pdf->Ln(3);

$pdf->SetFont('times', 'B', 12);
$pdf->Cell(0, 5, 'Kelas: ' . $KELAS->NAMA_KELAS);
$pdf->Ln(7);

$pdf->SetFont('times', 'B', 12);

$pdf->Cell(8, 11, 'No', 1, 0, 'C');
$pdf->Cell(10, 11, 'Abs', 1, 0, 'C');
$pdf->Cell(25, 11, 'NIS', 1, 0, 'C');
$pdf->Cell(52, 11, 'Nama', 1, 0, 'C');
$pdf->Cell(30, 5, 'Batasan Kitab', 1, 0, 'C');
$pdf->Cell(40, 11, 'Penyemak', 1, 0, 'C');
$pdf->Cell(34, 5, 'Status', 1, 0, 'C');
$pdf->Ln();

$pdf->Cell(95);
$i = 0;
foreach ($KITAB as $DETAIL) {
    $i++;
    $pdf->Cell(30 / count($KITAB), 5, $this->date_format->toRomawi($i), 1, 0, 'C');
}
$pdf->Cell(40);
$pdf->Cell(17, 5, 'HAFAL', 1, 0, 'C');
$pdf->Cell(17, 5, 'TIDAK', 1, 0, 'C');
$pdf->Ln();

$pdf->SetFont('times', '', 11);
$no = 1;
foreach ($SISWA as $DETAIL) {
    $DATA_SISWA = $DETAIL['DETAIL'];
//    $DATA_NILAI = $DETAIL['NILAI'];

    $pdf->Cell(8, 5, $no++, 1, 0, 'C');
    $pdf->Cell(10, 5, $DATA_SISWA->NO_ABSEN_AS, 1, 0, 'C');
    $pdf->Cell(25, 5, $DATA_SISWA->NIS_SISWA, 1, 0, 'L');
    $pdf->Cell(52, 5, $DATA_SISWA->NAMA_SISWA, 1, 0, 'L');
    foreach ($KITAB as $DETAIL_KITAB) {
        $pdf->Cell(30 / count($KITAB), 5, '', 1, 0, 'C');
    }
    $pdf->Cell(40, 5, '', 1, 0, 'C');
    $pdf->Cell(17, 5, '', 1, 0, 'C');
    $pdf->Cell(17, 5, '', 1, 0, 'C');
    $pdf->Ln();
}

$pdf->Ln(5);

$pdf->SetFont('aefurat', '', 11);
$i = 0;
foreach ($KITAB as $DETAIL) {
    $i++;
    $pdf->Cell(5, 5, $this->date_format->toRomawi($i), 0, 0, 'L');
    $pdf->Cell(5, 5, ' : ', 0, 0, 'L');
    $pdf->Cell(0, 5, '[ ' . $DETAIL->NAMA_KITAB . ' ] ' . $DETAIL->AWAL_BATASAN . ' - ' . $DETAIL->AKHIR_BATASAN, 0, 0, 'L');
    $pdf->Ln();
}

$pdf->Output();
