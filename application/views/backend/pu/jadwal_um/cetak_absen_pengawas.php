<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 * 
  $this->CoreFonts = array('courier', 'helvetica', 'times', 'symbol', 'zapfdingbats');
 * 
 */

$tahun_exp = explode('/', $this->session->userdata('NAMA_PSB_ACTIVE'));
$tahun = $tahun_exp[0];

$size_font = 8;

$pdf = $this->fpdf;


foreach ($data as $jk => $detail) {
    if ($jk == 'L') $jk = 'BANIN';
    else $jk = 'BANAT';

    $pdf->AddPage("P", "A4");

    $pdf->SetFont('Arial', 'B', $size_font + 2);
    $pdf->Cell(0, 4, 'DAFTAR HADIR PENGAWAS UJIAN MASUK '.$jk, 0, 0, 'C');
    $pdf->Ln();

    $pdf->Cell(0, 4, strtoupper($this->pengaturan->getNamaLembaga().' ('.$this->pengaturan->getNamaLembagaSingk().')'), 0, 0, 'C');
    $pdf->Ln();

    $pdf->SetFont('Arial', 'B', $size_font);
    $pdf->Cell(0, 4, 'TAHUN MASUK '.$tahun, 0, 0, 'C');
    $pdf->Ln(15);
    
    $pdf->Cell(28, 4, 'Hari, Tanggal', 0, 0, 'L');
    $pdf->Cell(0, 4, ': '.$this->date_format->get_day($jadwal->TANGGAL_PUJ).', '.$this->date_format->to_print_text($jadwal->TANGGAL_PUJ), 0, 0, 'L');
    $pdf->Ln();
    
    $pdf->Cell(28, 4, 'Jam', 0, 0, 'L');
    $pdf->Cell(0, 4, ': '.$this->time_format->jam_menit($jadwal->JAM_MULAI_PUJ).' - '.$this->time_format->jam_menit($jadwal->JAM_SELESAI_PUJ).' WIS', 0, 0, 'L');
    $pdf->Ln();

    $pdf->Cell(10, 5, 'NO', 1, 0, 'C');
    $pdf->Cell(25, 5, 'NIP', 1, 0, 'C');
    $pdf->Cell(85, 5, 'NAMA', 1, 0, 'C');
    $pdf->Cell(25, 5, 'RUANG', 1, 0, 'C');
    $pdf->Cell(47, 5, 'TTD', 1, 0, 'C');
    $pdf->Ln();

    $pdf->SetFont('Arial', '', $size_font);
    $x = 1;
    foreach ($detail as $value) {
        $pdf->Cell(10, 5, $x++, 1, 0, 'C');
        $pdf->Cell(25, 5, $value['NIP_PEG'], 1, 0, 'C');
        $pdf->Cell(85, 5, $value['GELAR_AWAL_PEG'].' '.$this->cetak->nama_peg_print($value), 1, 0, 'L').','.$value['GELAR_AKHIR_PEG'];
        $pdf->Cell(25, 5, $value['KODE_RUANG'], 1, 0, 'L');
        $pdf->Cell(47, 5, '', 1, 0, 'C');
        $pdf->Ln();
    }
    
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
}

$pdf->Output();