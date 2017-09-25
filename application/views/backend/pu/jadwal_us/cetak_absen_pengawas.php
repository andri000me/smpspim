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

$size_font = 9;

$pdf = $this->fpdf;

$data_denah = json_decode($denah, TRUE);

foreach ($data as $jk => $detail) {

    $detail_denah = $data_denah[$jk]['DENAH'];
    $detail_ruang = $data_denah[$jk]['RUANG'];

    if ($jk == 'L')
        $jk = 'BANIN';
    else
        $jk = 'BANAT';

    $pdf->AddPage("P", $this->pengaturan->getUkuranF4());

    $pdf->SetFont('Arial', 'B', $size_font + 2);
    $pdf->Cell(0, 4, 'DAFTAR HADIR PENGAWAS UJIAN SEKOLAH ' . $jk, 0, 0, 'C');
    $pdf->Ln();

    $pdf->Cell(0, 4, strtoupper($this->pengaturan->getNamaLembaga() . ' (' . $this->pengaturan->getNamaLembagaSingk() . ')'), 0, 0, 'C');
    $pdf->Ln();

    $pdf->SetFont('Arial', 'B', $size_font);
    $pdf->Cell(0, 4, 'TAHUN AJARAN ' . $this->session->userdata('NAMA_TA_ACTIVE') . ' CAWU ' . $this->session->userdata('ID_CAWU_ACTIVE'), 0, 0, 'C');
    $pdf->Ln(10);

    $pdf->Cell(150, 4, 'Hari, Tanggal: ' . $this->date_format->get_day($jadwal->TANGGAL_PUJ) . ', ' . $this->date_format->to_print_text($jadwal->TANGGAL_PUJ), 0, 0, 'L');
    $pdf->Cell(0, 4, 'Jam: ' . $this->time_format->jam_menit($jadwal->JAM_MULAI_PUJ) . ' - ' . $this->time_format->jam_menit($jadwal->JAM_SELESAI_PUJ) . ' WIS', 0, 0, 'R');
    $pdf->Ln();

    $pdf->Cell(14, 5, 'NO', 1, 0, 'C');
    $pdf->Cell(25, 5, 'NIP', 1, 0, 'C');
    $pdf->Cell(85, 5, 'NAMA', 1, 0, 'C');
    $pdf->Cell(25, 5, 'RUANG', 1, 0, 'C');
    $pdf->Cell(47, 5, 'TTD', 1, 0, 'C');
    $pdf->Ln();

    $pdf->SetFont('Arial', '', $size_font);
    $x = 1;
    foreach ($detail_denah as $index_ruang => $item) {
        foreach ($detail as $data_pengawas) {
            if ($data_pengawas['KODE_RUANG'] == $detail_ruang[$index_ruang]['KODE_RUANG']) {
                $value = $data_pengawas;
            }
        }
        $pdf->Cell(14, 6, $x++, 1, 0, 'C');
        $pdf->Cell(25, 6, isset($value) ? $value['NIP_PEG'] : '', 1, 0, 'C');
        $pdf->Cell(85, 6, isset($value) ? $this->cetak->nama_peg_print($value) : '', 1, 0, 'L');
        $pdf->Cell(25, 6, $detail_ruang[$index_ruang]['KODE_RUANG'], 1, 0, 'L');
        $pdf->Cell(47, 6, '', 1, 0, 'C');
        $pdf->Ln();

        if (isset($value))
            unset($value);
    }

    $pdf->Ln(10);

    $pdf->Cell(140);
    $pdf->Cell(0, 4, 'Ketua Panitia Ujian,', 0, 0, 'C');
    $pdf->Ln(17);

    $pdf->SetFont('Arial', 'BU', $size_font);
    $pdf->Cell(140);
    $pdf->Cell(0, 4, $ketua->NAMA_PEG, 0, 0, 'C');
    $pdf->Ln();

    $pdf->SetFont('Arial', '', $size_font);
    $pdf->Cell(140);
    $pdf->Cell(0, 4, 'NIP. ' . $ketua->NIP_PEG, 0, 0, 'C');
}

$pdf->Output();
