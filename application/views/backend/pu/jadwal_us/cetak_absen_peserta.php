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
    'pengawas_pu_model' => 'pengawas',
));

$tahun_exp = explode('/', $this->session->userdata('NAMA_PSB_ACTIVE'));
$tahun = $tahun_exp[0];

$size_font = 9;

$pdf = $this->fpdf;

foreach ($data as $detail) {
    $tanggal = $detail['TANGGAL'];
    $denah = json_decode($detail['DENAH'], TRUE);

    foreach ($denah as $jk => $data_denah) {
        if ($jk == 'L')
            $nama_jk = 'BANIN';
        else
            $nama_jk = 'BANAT';

        $temp_last_id = array_fill(0, count($data_denah['TINGKAT']), 0);

        $jumlah_peruang = $data_denah['JUMLAH_PERUANG'];

        foreach ($data_denah['JENJANG'] as $dept) {
            $data_denah['KODE_JENJANG'][] = $CI->departemen->get_id_by_jenjang($dept);
        }

        foreach ($data_denah['DENAH'] as $ruang => $value) {

            $pdf->AddPage("P",  $this->pengaturan->getUkuranF4());
            $pdf->SetAutoPageBreak(true, 0);

            $pdf->SetFont('Arial', 'B', $size_font + 2);
            $pdf->Cell(0, 4, 'DAFTAR HADIR PESERTA UJIAN SEKOLAH ' . $nama_jk, 0, 0, 'C');
            $pdf->Ln();

            $pdf->Cell(0, 4, strtoupper($this->pengaturan->getNamaLembaga() . ' (' . $this->pengaturan->getNamaLembagaSingk() . ')'), 0, 0, 'C');
            $pdf->Ln();

            $pdf->SetFont('Arial', 'B', $size_font);
            $pdf->Cell(0, 4, 'TAHUN AJARAN ' . $this->session->userdata('NAMA_TA_ACTIVE') . ' CAWU ' . $this->session->userdata('ID_CAWU_ACTIVE'), 0, 0, 'C');
            $pdf->Ln(10);

            $pdf->Cell(22, 5, 'Ruang ujian');
            $pdf->Cell(0, 5, ': ' . $data_denah["RUANG"][$ruang]['KODE_RUANG'] . ' - ' . $data_denah["RUANG"][$ruang]['NAMA_RUANG'], 0, 0, 'L');
            $pdf->Ln();

            $pdf->Cell(22, 4, 'Hari, Tanggal', 0, 0, 'L');
            $pdf->Cell(130, 4, ': ' . $this->date_format->get_day($detail['TANGGAL']) . ', ' . $this->date_format->to_print_text($detail['TANGGAL']), 0, 0, 'L');
            $pdf->Cell(7, 4, 'Jam', 0, 0, 'L');
            $pdf->Cell(0, 4, ': ' . $this->time_format->jam_menit($detail['JAM_MULAI']) . ' - ' . $this->time_format->jam_menit($detail['JAM_SELESAI']) . ' WIS', 0, 0, 'R');
            $pdf->Ln();

            $pdf->Cell(10, 5, 'NO', 1, 0, 'C');
            $pdf->Cell(30, 5, 'KODE', 1, 0, 'C');
            $pdf->Cell(72, 5, 'NAMA', 1, 0, 'C');
            $pdf->Cell(45, 5, 'KELAS', 1, 0, 'C');
            $pdf->Cell(40, 5, 'TTD', 1, 0, 'C');
            $pdf->Ln();

            $pdf->SetFont('Arial', '', $size_font);
            for ($i = 0; $i < $jumlah_peruang; $i++) {
                if (isset($data_denah['DENAH'][$ruang][$i])) {
                    $id_tingkat = $data_denah['DENAH'][$ruang][$i];
                    $id_jenjang = $data_denah['JENJANG'][$id_tingkat];
                    $id_siswa = $data_denah['DATA_SISWA_RANDOM'][$id_tingkat][$temp_last_id[$id_tingkat]]['ID_SISWA'];
                    $data_siswa = $CI->siswa->get_by_id_simple($id_siswa);
                    $temp_last_id[$id_tingkat] ++;

                    $pdf->Cell(10, 5, $i + 1, 1, 0, 'C');
                    $pdf->Cell(30, 5, $data_siswa->NIS_SISWA, 1, 0, 'L');
                    $pdf->Cell(72, 5, $data_siswa->NAMA_SISWA, 1, 0, 'L');
                    $pdf->Cell(45, 5, $data_siswa->NAMA_KELAS, 1, 0, 'L');
                    $pdf->Cell(40, 5, '', 1, 0, 'C');
                } else {
                    $pdf->Cell(10, 5, $i + 1, 1, 0, 'C');
                    $pdf->Cell(30, 5, '-', 1, 0, 'L');
                    $pdf->Cell(72, 5, '-', 1, 0, 'L');
                    $pdf->Cell(45, 5, '-', 1, 0, 'L');
                    $pdf->Cell(40, 5, '', 1, 0, 'C');
                }

                $pdf->Ln();
            }

            $pdf->Ln(5);

            $pdf->SetFont('Arial', '', $size_font);
            $pdf->Cell(140);
            $pdf->Cell(0, 4, 'Pengawas,', 0, 0, 'C');
            $pdf->Ln(13);

            $data_pengawas = $this->pengawas->get_by_jadwal_ruang($ID, $jk, $data_denah["RUANG"][$ruang]['KODE_RUANG']);

            $pdf->SetFont('Arial', 'BU', $size_font);
            $pdf->Cell(140);
            $pdf->Cell(0, 4, ($data_pengawas == NULL) ? '..............................................' : $this->cetak->nama_peg_print($data_pengawas), 0, 0, 'C');
            $pdf->Ln();

            $pdf->SetFont('Arial', '', $size_font);
            $pdf->Cell(140);
            $pdf->Cell(0, 4, 'NIP. ' . ($data_pengawas == NULL ? '..................................' : $data_pengawas->NIP_PEG), 0, 0, 'C');
        }
    }
}

$pdf->Output();
