<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 * 
  $this->CoreFonts = array('Arial', 'helvetica', 'times', 'symbol', 'zapfdingbats');
 * 
 */

if (count($BULAN) != 4) {
    echo '<h1>TIDAK DAPAT MENCETAK KARENA BULAN TIDAK BERJUMLAH 4 BUAH</h1>';

    exit();
}

$pdf = $this->fpdf;

$pdf->SetLeftMargin(6);
$pdf->SetRightMargin(6);
$pdf->AddPage("P", array(215, 330));
$pdf->SetAutoPageBreak(true, 0);

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 5, 'MONITORING HAFALAN SISWA', 0, 0, 'C');
$pdf->Ln();

$pdf->Cell(0, 5, 'TAHUN AJARAN ' . $this->session->userdata('NAMA_TA_ACTIVE'), 0, 0, 'C');
$pdf->Ln(10);

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(20, 5, 'Kelas');
$pdf->Cell(100, 5, ': ' . $KELAS->NAMA_KELAS);
$pdf->Cell(20, 5, 'Wali Kelas');
$pdf->Cell(0, 5, ': ' . $this->cetak->nama_peg_print($KELAS));
$pdf->Ln();

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(10, 5, 'No', 1, 0, 'C');
$pdf->Cell(60, 5, 'Nama Siswa', 1, 0, 'C');

if(in_array('Dzulhijjah', $BULAN) && in_array('Muharrom', $BULAN)) {
    $BULAN[count($BULAN)] = $BULAN[0];
    unset($BULAN[0]);
}
        
foreach ($BULAN as $DETAIL_BULAN) {
    $pdf->Cell(33, 5, $DETAIL_BULAN, 1, 0, 'C');
}
$pdf->Ln();

$pdf->SetFont('Arial', '', 10);
foreach ($SISWA as $DETAIL) {
    $DATA_SISWA = $DETAIL['DETAIL'];

    $pdf->Cell(10, 5, $DATA_SISWA->NO_ABSEN_AS, 1, 0, 'C');
    $pdf->Cell(60, 5, $this->pdf_handler->cut_text($pdf, $DATA_SISWA->NAMA_SISWA, 70), 1, 0, 'L');
    foreach ($BULAN as $DETAIL_BULAN) {
        for ($i = 0; $i < 4; $i++) {
            $pdf->Cell(33 / 4, 5, '', 1, 0, 'C');
        }
    }
    $pdf->Ln();
}

$pdf->Cell(20, 5, 'Keterangan:');
$pdf->Ln();

for ($i = 0; $i < 4; $i++) {
    $pdf->Cell(0, 5, '_______________________________________________________________________________________________________');
    $pdf->Ln();
}

$pdf->Output();
