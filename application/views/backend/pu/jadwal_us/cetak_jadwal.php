<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 * 
  $this->CoreFonts = array('courier', 'helvetica', 'times', 'symbol', 'zapfdingbats');
 * 
 */
$size_font = 8;

$pdf = $this->fpdf;

$pdf->AddPage("P", $this->pengaturan->getUkuranF4());

$pdf->SetFont('Arial', 'B', $size_font + 2);
$pdf->Cell(0, 4, 'JADWAL UJIAN SEKOLAH', 0, 0, 'C');
$pdf->Ln();

$pdf->Cell(0, 4, strtoupper($this->pengaturan->getNamaLembaga().' ('.$this->pengaturan->getNamaLembagaSingk().')'), 0, 0, 'C');
$pdf->Ln();

$pdf->SetFont('Arial', 'B', $size_font);
$pdf->Cell(0, 4, 'TAHUN AJARAN '.$this->session->userdata('NAMA_TA_ACTIVE').' CAWU '.$this->session->userdata('ID_CAWU_ACTIVE'), 0, 0, 'C');
$pdf->Ln(15);

$pdf->Cell(10, 6, 'NO', 1, 0, 'C');
$pdf->Cell(30, 6, 'KELAS', 1, 0, 'C');
$pdf->Cell(30, 6, 'TANGGAL', 1, 0, 'C');
$pdf->Cell(35, 6, 'JAM', 1, 0, 'C');
$pdf->Cell(83, 6, 'MAPEL', 1, 0, 'C');
$pdf->Ln();

$pdf->SetFont('Arial', '', $size_font);
$x = 1;
$temp_mapel = NULL;
$temp_tingkat = NULL;
$temp_tanggal = NULL;
foreach ($data as $value) {
    $pdf->Cell(10, 6, $x++, 1, 0, 'C');
    $pdf->Cell(30, 6, ($temp_tingkat == $value['TINGKAT_PUM']) ? '' : $value['DEPT_TINGK'].' KELAS: '.$value['NAMA_TINGK'], ($temp_tingkat == $value['TINGKAT_PUM']) ? 'RL' : 'RLT', 0, 'C');
    $pdf->Cell(30, 6, ($temp_tanggal == $value['TANGGAL_PUJ']) ? (($temp_tingkat == $value['TINGKAT_PUM']) ? '' : $this->date_format->to_print($value['TANGGAL_PUJ'])) : $this->date_format->to_print($value['TANGGAL_PUJ']), ($temp_tanggal == $value['TANGGAL_PUJ']) ? (($temp_tingkat == $value['TINGKAT_PUM']) ? 'RL' : 'RLT') : 'RLT', 0, 'C');
    $pdf->Cell(35, 6, date('H:i', strtotime($value['JAM_MULAI_PUJ'])).' - '.date('H:i', strtotime($value['JAM_SELESAI_PUJ'])).' WIS', 1, 0, 'C');
    $pdf->Cell(83, 6, $value['NAMA_MAPEL'], 1, 0, 'L');
    $pdf->Ln();
    
    $temp_mapel = $value['MAPEL_PUM'];
    $temp_tingkat = $value['TINGKAT_PUM'];
    $temp_tanggal = $value['TANGGAL_PUJ'];
}
$pdf->Cell(183, 6, '', 'T', 0, 'L');
$pdf->Ln(15);

$pdf->Cell(140);
$pdf->Cell(0, 4, 'Ketua Panitia Ujian,', 0, 0, 'C');
$pdf->Ln(20);

$pdf->SetFont('Arial', 'BU', $size_font);
$pdf->Cell(140);
$pdf->Cell(0, 4, $ketua->NAMA_PEG, 0, 0, 'C');
$pdf->Ln();

$pdf->SetFont('Arial', '', $size_font);
$pdf->Cell(140);
$pdf->Cell(0, 4, 'NIP. '.$ketua->NIP_PEG, 0, 0, 'C');

$pdf->Output();