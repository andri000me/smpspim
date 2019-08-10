<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 * 
  $this->CoreFonts = array('Arial', 'helvetica', 'times', 'symbol', 'zapfdingbats');
 * 
 */

$pdf = $this->fpdf;

$pdf->AddPage("P", 'A4');
//$pdf->SetAutoPageBreak(true, 0);

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 5, 'REKAPITULASI HAFALAN SISWA PERKELAS', 0, 0, 'C');
$pdf->Ln();

$pdf->SetFont('Arial', '', 11);
$pdf->Cell(0, 5, 'TAHUN AJARAN ' . strtoupper($this->session->userdata('NAMA_TA_ACTIVE')), 0, 0, 'C');
$pdf->Ln(10);

$pdf->SetFont('Arial', 'I', 10);
$pdf->Cell(0, 5, 'Dicetak tanggal ' . date('d-m-Y H:i:s') . ' WIB', 0, 0, 'R');
$pdf->Ln();

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(10, 10, 'No', 1, 0, 'C');
$pdf->Cell(50, 10, 'Kelas', 1, 0, 'C');
$pdf->Cell(130, 5, 'Jumlah Siswa', 1, 0, 'C');
$pdf->Ln();

$pdf->Cell(60);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell((130 / 7) - 2, 5, 'Total', 1, 0, 'C');
$pdf->Cell((130 / 7) - 2, 5, 'Setoran', 1, 0, 'C');
$pdf->Cell((130 / 7) + 5, 5, 'Belum Setoran', 1, 0, 'C');
$pdf->Cell((130 / 7) - 2, 5, 'Hafal', 1, 0, 'C');
$pdf->Cell((130 / 7) + 5, 5, 'Tidak Hafal', 1, 0, 'C');
$pdf->Cell((130 / 7) - 2, 5, 'Gugur', 1, 0, 'C');
$pdf->Cell((130 / 7) - 2, 5, 'Keluar', 1, 0, 'C');
$pdf->Ln();

$pdf->SetFont('Arial', '', 10);
$nomor = 1;
foreach ($nama_kelas as $detail) {
    $pdf->Cell(10, 5, $nomor++, 1, 0, 'C');
    $pdf->Cell(50, 5, $detail->x_label, 1, 0, 'L');

    $pdf->Cell((130 / 7) - 2, 5, get_data($detail, $jumlah_siswa), 1, 0, 'C');
    $pdf->Cell((130 / 7) - 2, 5, get_data($detail, $jumlah_siswa_setoran), 1, 0, 'C');
    $pdf->Cell((130 / 7) + 5, 5, get_data($detail, $jumlah_siswa_belum_setoran), 1, 0, 'C');
    $pdf->Cell((130 / 7) - 2, 5, get_data($detail, $jumlah_siswa_hafal), 1, 0, 'C');
    $pdf->Cell((130 / 7) + 5, 5, get_data($detail, $jumlah_siswa_tidak_hafal), 1, 0, 'C');
    $pdf->Cell((130 / 7) - 2, 5, get_data($detail, $jumlah_siswa_gugur), 1, 0, 'C');
    $pdf->Cell((130 / 7) - 2, 5, get_data($detail, $jumlah_siswa_keluar), 1, 0, 'C');

    $pdf->Ln();
}

function get_data($kelas, $data) {
    foreach ($data as $detail) {
        if ($kelas->x_label == $detail->x_label) {
            return $detail->data;
        }
    }
    return "-";
}

$pdf->Output();
