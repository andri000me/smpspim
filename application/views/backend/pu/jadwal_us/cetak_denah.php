<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 * 
  $this->CoreFonts = array('courier', 'helvetica', 'times', 'symbol', 'zapfdingbats');
 * 
 */
$CI = & get_instance();

$CI->load->model(array(
    'departemen_model' => 'departemen',
    'siswa_model' => 'siswa',
));

$jumlah_perbaris = $this->pengaturan->getJumlahSiswaPerbaris();

// PENGATURAN FOTO SISWA
$start_x = 19;
$start_y = 0;
$margin_left = 38;
$margin_top = 0;
$width_photo = 25;
$height_photo = 23;
$width_box = 33;

$size_font = 12;

$pdf = $this->fpdf;

foreach ($data as $detail) {
    $tanggal = $detail['TANGGAL'];
    $denah = json_decode($detail['DENAH'], TRUE);

    foreach ($denah as $jk => $data_denah) {
        if ($jk == 'L')
            $jk = 'BANIN';
        else
            $jk = 'BANAT';

        $jumlah_perbaris = $data_denah['JUMLAH_PERBARIS'];

        $temp_last_id = array_fill(0, count($data_denah['JENJANG']), 0);

        foreach ($data_denah['JENJANG'] as $dept) {
            $data_denah['KODE_JENJANG'][] = $CI->departemen->get_id_by_jenjang($dept);
        }

        foreach ($data_denah['DENAH'] as $ruang => $value) {
            $jumlah_peruang = $data_denah['RUANG'][$ruang]['KAPASITAS_UJIAN_RUANG'];

            $pdf->AddPage("L", $this->pengaturan->getUkuranF4());
            $pdf->SetAutoPageBreak(true, 0);

            $pdf->SetFont('Arial', 'B', $size_font + 2);
            $pdf->Cell(0, 5, 'DENAH UJIAN SEKOLAH ' . $jk, 0, 0, 'L');
            $pdf->Ln();

            $pdf->Cell(0, 5, strtoupper($this->pengaturan->getNamaLembaga() . ' (' . $this->pengaturan->getNamaLembagaSingk() . ')'), 0, 0, 'L');
            $pdf->Ln();

            $pdf->SetFont('Arial', 'B', $size_font);
            $pdf->Cell(0, 5, 'TAHUN AJARAN ' . $this->session->userdata('NAMA_TA_ACTIVE') . ' CAWU ' . $this->session->userdata('ID_CAWU_ACTIVE'), 0, 0, 'L');
            $pdf->Ln(10);

            $posisi_y = 0;
            $temp_i = 0;
            for ($i = 0; $i < $jumlah_peruang; $i++) {
                $pdf->Cell(5);
                $pdf->SetFont('Arial', '', $size_font + 10);
                $pdf->Cell($width_box, 18, $i + 1, 'RLT', 0, 'C');
                $pdf->SetFont('Arial', 'B', $size_font + 5);

                $posisi_x = 0;
                if (((($i + 1) % $jumlah_perbaris) == 0) || ($i == ($jumlah_peruang - 1))) {
                    $pdf->Ln();

                    if ($i == ($jumlah_peruang - 1))
                        $start = $temp_i;
                    else
                        $start = $i + 1 - $jumlah_perbaris;
                    
                    for ($x = $start; $x <= $i; $x++) {
                        if (isset($data_denah['DENAH'][$ruang][$x])) {
                            $id_tingkat = $data_denah['DENAH'][$ruang][$x];

                            $pdf->Cell(5);
                            $pdf->Cell($width_box, 10, $data_denah['TINGKAT'][$id_tingkat] . ' ' . $data_denah['KODE_JENJANG'][$id_tingkat], 'RLB', 0, 'C');
                        } else {
                            $pdf->Cell(5);
                            $pdf->Cell($width_box, 10, '-', 'RLB', 0, 'C');
                        }
                    }
                    
                    $temp_i = $i;

                    $pdf->Ln(16);
                }
            }

            $pdf->SetXY(200, 10);

            $pdf->SetFont('Arial', 'B', $size_font + 10);
            $pdf->Cell(0, 15, 'RUANG ' . $data_denah["RUANG"][$ruang]['KODE_RUANG'], 1, 0, 'C');
        }
    }
}

$pdf->Output();
