<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 * 
  $this->CoreFonts = array('Arial', 'helvetica', 'times', 'symbol', 'zapfdingbats');
 * 
 */

if ((count($BULAN) != 4) && !(($JENJANG == 'MI') || ($JENJANG == 'DU'))) {
    echo '<h1>TIDAK DAPAT MENCETAK KARENA BULAN TIDAK BERJUMLAH 4 BUAH</h1>';

    exit();
}

$pdf = $this->fpdf;

$pdf->SetLeftMargin(6);
$pdf->SetRightMargin(6);
$pdf->AddPage("P", $this->pengaturan->getUkuranF4());
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
$pdf->Cell(8, 5, 'No', 1, 0, 'C');
$pdf->Cell(25, 5, 'NIS', 1, 0, 'C');
$pdf->Cell(47, 5, 'Nama Siswa', 1, 0, 'C');

if (($JENJANG == 'MI') || ($JENJANG == 'DU')) {
    for ($i = 0; $i < 5; $i++) {
        $pdf->Cell(30 * 4 / 5, 5, '...../...../20...', 1, 0, 'C');
    }
} else {
    if (in_array('Dzulhijjah', $BULAN) && in_array('Muharrom', $BULAN)) {
        $BULAN[count($BULAN)] = $BULAN[0];
        unset($BULAN[0]);
    }

    foreach ($BULAN as $DETAIL_BULAN) {
        $pdf->Cell(30, 5, $DETAIL_BULAN, 1, 0, 'C');
    }
}

$pdf->Ln();

$pdf->SetFont('Arial', '', 10);
foreach ($SISWA as $DETAIL) {
    $DATA_SISWA = $DETAIL['DETAIL'];

    $pdf->Cell(8, 5.5, $DATA_SISWA->NO_ABSEN_AS, 1, 0, 'C');
    $pdf->Cell(25, 5.5, $DATA_SISWA->NIS_SISWA, 1, 0, 'C');
    $pdf->Cell(47, 5.5, $this->pdf_handler->cut_text($pdf, $DATA_SISWA->NAMA_SISWA, 47), 1, 0, 'L');

    if (($JENJANG == 'MI') || ($JENJANG == 'DU')) {
        for ($i = 0; $i < 5; $i++) {
            $pdf->Cell(30 * 4 / 5, 5.5, '', 1, 0, 'C');
        }
    } else {
        foreach ($BULAN as $DETAIL_BULAN) {
            for ($i = 0; $i < 4; $i++) {
                $pdf->Cell(30 / 4, 5.5, '', 1, 0, 'C');
            }
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
