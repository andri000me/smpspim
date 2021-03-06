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
    $data_jadwal = $detail['DATA'];
    $tanggal_jadwal = $detail['TANGGAL'];
    $denah = json_decode($detail['DENAH'], TRUE);

    foreach ($denah as $jk => $data_denah) {
        if ($detail['JK_PUJ'] != $jk)
            continue;

        if ($jk == 'L')
            $nama_jk = 'BANIN';
        else
            $nama_jk = 'BANAT';

        $temp_last_id = array_fill(0, count($data_denah['TINGKAT']), 0);

        foreach ($data_denah['JENJANG'] as $dept) {
            $data_denah['KODE_JENJANG'][] = $CI->departemen->get_id_by_jenjang($dept);
        }

        $jumlah_siswa_test = 0;
        $id_tingkat_test = 15;
        $iterasi_3 = 0;
        foreach ($data_denah['DENAH'] as $ruang => $value) {
            $iterasi_3++;
            $jumlah_peruang = $data_denah['RUANG'][$ruang]['KAPASITAS_UJIAN_RUANG'];
            if (count($data_denah['DENAH'][$ruang]) > $jumlah_peruang)
                $jumlah_peruang = count($data_denah['DENAH'][$ruang]);

            $ruang_baru = true;
            $temp_data_denah = array();
            foreach ($data_jadwal as $item) {
                if ($ID != $item['ID_PUJ'])
                    continue;

                $pdf->AddPage("P", $this->pengaturan->getUkuranF4());
                $pdf->SetAutoPageBreak(true, 0);

                $pdf->SetFont('Arial', 'B', $size_font + 2);
                $pdf->Cell(0, 4, 'DAFTAR HADIR PESERTA UJIAN SEKOLAH ' . $nama_jk, 0, 0, 'C');
                $pdf->Ln();

                $pdf->Cell(0, 4, strtoupper($this->pengaturan->getNamaLembaga() . ' (' . $this->pengaturan->getNamaLembagaSingk() . ')'), 0, 0, 'C');
                $pdf->Ln();

                $pdf->SetFont('Arial', 'B', $size_font);
                $pdf->Cell(0, 4, 'TAHUN AJARAN ' . $this->session->userdata('NAMA_TA_ACTIVE') . ' CAWU ' . $this->session->userdata('ID_CAWU_ACTIVE'), 0, 0, 'C');
                $pdf->Ln(10);

                $pdf->Cell(80, 5, 'Ruang ujian: ' . $data_denah["RUANG"][$ruang]['KODE_RUANG'] . ' - ' . $data_denah["RUANG"][$ruang]['NAMA_RUANG'], 0, 0, 'L');
                $pdf->Cell(0, 5, 'Hari, Tanggal, Jam: ' . $this->date_format->get_day($detail['TANGGAL']) . ', ' . $this->date_format->to_print_text($detail['TANGGAL']) . ', ' . $this->time_format->jam_menit($item['JAM_MULAI_PUJ']) . '-' . $this->time_format->jam_menit($item['JAM_SELESAI_PUJ']), 0, 0, 'R');
                $pdf->Ln();

//            $pdf->Cell(22, 4, 'Hari, Tanggal', 0, 0, 'L');
//            $pdf->Cell(130, 4, ': ' . $this->date_format->get_day($detail['TANGGAL']) . ', ' . $this->date_format->to_print_text($detail['TANGGAL']), 0, 0, 'L');
//            $pdf->Cell(7, 4, 'Jam', 0, 0, 'L');
//            $pdf->Cell(0, 4, ': ' . $this->time_format->jam_menit($detail['JAM_MULAI']) . ' - ' . $this->time_format->jam_menit($detail['JAM_SELESAI']) . ' WIS', 0, 0, 'R');
//            $pdf->Ln();

                $pdf->Cell(7, 5, 'NO', 1, 0, 'C');
                $pdf->Cell(23, 5, 'NIS', 1, 0, 'C');
                $pdf->Cell(65, 5, 'NAMA', 1, 0, 'C');
                $pdf->Cell(40, 5, 'KELAS', 1, 0, 'C');
                $pdf->Cell(62, 5, 'TTD', 1, 0, 'C');
                $pdf->Ln();
//                $jam_ke = 1;
//                $pdf->Cell(87 / count($data_jadwal), 5, 'Jam ke-' . $jam_ke++, 1, 0, 'C');
////                $pdf->Cell(87 / count($data_jadwal), 5, $this->time_format->jam_menit($item['JAM_MULAI_PUJ']) . '-' . $this->time_format->jam_menit($item['JAM_SELESAI_PUJ']), 1, 0, 'C');
//                $pdf->Ln();

                $pdf->SetFont('Arial', '', $size_font);
                $ganjil = true;
                $jenjang = array();
                for ($i = 0; $i < $jumlah_peruang; $i++) {
                    if (isset($data_denah['DENAH'][$ruang][$i])) {
                        $id_tingkat = $data_denah['DENAH'][$ruang][$i];
                        $id_jenjang = $data_denah['JENJANG'][$id_tingkat];
//                        if ($ruang_baru && isset($data_denah['DATA_SISWA_RANDOM'][$id_tingkat][$temp_last_id[$id_tingkat]]['ID_SISWA'])) {
                        if (isset($data_denah['DATA_SISWA_RANDOM'][$id_tingkat][$temp_last_id[$id_tingkat]]['ID_SISWA'])) {
                            $id_siswa = $data_denah['DATA_SISWA_RANDOM'][$id_tingkat][$temp_last_id[$id_tingkat]]['ID_SISWA'];
                            $data_siswa = $CI->siswa->get_by_id_simple($id_siswa);
                            $temp_data_denah[$i] = $data_siswa;
                            $temp_last_id[$id_tingkat] ++;
//                        } else {
//                            $data_siswa = $temp_data_denah[$i];
//                        }
//                    if ($id_tingkat == $id_tingkat_test) {
//                        $jumlah_siswa_test++;
//                        echo $id_tingkat . ' - ' . $id_jenjang . '| ' . $data_denah["RUANG"][$ruang]['KODE_RUANG'] . ' - ', ($i + 1) . ' | ' . $id_siswa . ' => ' . $data_siswa->NIS_SISWA . ' - ' . $data_siswa->NAMA_SISWA . '<br>';
//                    }

                            $pdf->Cell(7, 5, $i + 1, 1, 0, 'C');
                            $pdf->Cell(23, 5, $data_siswa->NIS_SISWA, 1, 0, 'L');
                            $pdf->Cell(65, 5, $this->pdf_handler->cut_text($pdf, $data_siswa->NAMA_SISWA, 65), 1, 0, 'L');
                            $pdf->Cell(40, 5, $data_siswa->NAMA_KELAS, 1, 0, 'L');

                            $jenjang_siswa = $data_siswa->NAMA_TINGK . ' ' . $data_siswa->DEPT_TINGK;
                            if (isset($jenjang[$jenjang_siswa]))
                                $jenjang[$jenjang_siswa] ++;
                            else
                                $jenjang[$jenjang_siswa] = 1;
                        } else {
                            $pdf->Cell(7, 5, $i + 1, 1, 0, 'C');
                            $pdf->Cell(23, 5, '-', 1, 0, 'L');
                            $pdf->Cell(65, 5, '-', 1, 0, 'L');
                            $pdf->Cell(40, 5, '-', 1, 0, 'L');
                        }
                    } else {
                        $pdf->Cell(7, 5, $i + 1, 1, 0, 'C');
                        $pdf->Cell(23, 5, '-', 1, 0, 'L');
                        $pdf->Cell(65, 5, '-', 1, 0, 'L');
                        $pdf->Cell(40, 5, '-', 1, 0, 'L');
                    }
                    $pdf->Cell(62 / 2, 5, $ganjil ? $i + 1 : '', $ganjil ? 'LTR' : 'LBR', 0, 'L');
                    $pdf->Cell(62 / 2, 5, $ganjil ? '' : $i + 1, $ganjil ? 'LTR' : 'LBR', 0, 'L');

                    $ganjil = !$ganjil;

                    $pdf->Ln();
                }

                if (($jumlah_peruang % 2) > 0) {
                    $pdf->Cell(7, 5, '', 1, 0, 'C');
                    $pdf->Cell(23, 5, '', 1, 0, 'L');
                    $pdf->Cell(65, 5, '', 1, 0, 'L');
                    $pdf->Cell(40, 5, '', 1, 0, 'L');
                    $pdf->Cell(62, 5, '', $ganjil ? 'LTR' : 'LBR', 0, 'L');
                    $pdf->Cell(62, 5, '', $ganjil ? 'LTR' : 'LBR', 0, 'L');
                }

                $pdf->Ln(5);


//            $data_pengawas = $this->pengawas->get_by_jadwal_ruang($ID, $jk, $data_denah["RUANG"][$ruang]['KODE_RUANG']);
//
//            $pdf->SetFont('Arial', 'BU', $size_font);
//            $pdf->Cell(140);
//            $pdf->Cell(0, 4, ($data_pengawas == NULL) ? '..............................................' : $this->cetak->nama_peg_print($data_pengawas), 0, 0, 'C');
//            $pdf->Ln();
//
//            $pdf->SetFont('Arial', '', $size_font);
//            $pdf->Cell(140);
//            $pdf->Cell(0, 4, 'NIP. ' . ($data_pengawas == NULL ? '..................................' : $data_pengawas->NIP_PEG), 0, 0, 'C');

                $y_akhir = $pdf->GetY();

                $pdf->SetFont('Arial', '', $size_font);
                $pdf->Cell(100);
                $pdf->Cell(0, 4, 'Pengawas,', 0, 0, 'C');
                $pdf->Ln(15);
                $pdf->SetFont('Arial', 'BU', $size_font);
                $pdf->Cell(100);
                $pdf->Cell(0, 4, '..............................................', 0, 0, 'C');
                $pdf->Ln();
//                $pdf->SetFont('Arial', '', $size_font);
//                $pdf->Cell(100);
//                $pdf->Cell(0, 4, 'NIP. ..................................', 0, 0, 'C');

                $pdf->SetY($y_akhir);

                ksort($jenjang);
                $pdf->SetFont('Arial', '', $size_font);
                foreach ($jenjang as $nama_jenjang => $jumlah_siswa) {
                    $pdf->Cell(30, 4, $nama_jenjang . ': ' . $jumlah_siswa . ' siswa', 1, 0, 'L');
                    $pdf->Ln();
                }

                $ruang_baru = false;
            }
//            if ($iterasi_3 == 4)
//                break;
        }
//        break;
//        echo 'ID ' . $this->date_format->to_print_text($detail['TANGGAL']);
//        echo '<br>';
//        echo 'ID ' . $id_tingkat_test;
//        echo '<br>';
//        echo 'ID ' . json_encode($data_denah['DATA_SISWA_RANDOM'][$id_tingkat_test]);
//        echo '<br>';
//        echo 'JUMLAH REAL ' . count($data_denah['DATA_SISWA_RANDOM'][$id_tingkat_test]);
//        echo '<br>';
//        echo 'JUMLAH CARD ' . $jumlah_siswa_test;
//        echo '<br>';
//        echo 'SELISIH ' . (count($data_denah['DATA_SISWA_RANDOM'][$id_tingkat_test]) - $jumlah_siswa_test);
//        echo '<br>';
//        echo '<hr>';
//
//
//        for ($a = $jumlah_siswa_test; $a < count($data_denah['DATA_SISWA_RANDOM'][$id_tingkat_test]); $a++) {
//            $id_siswa = $data_denah['DATA_SISWA_RANDOM'][$id_tingkat_test][$temp_last_id[$id_tingkat_test]]['ID_SISWA'];
//            $data_siswa = $CI->siswa->get_by_id_simple($id_siswa);
//            $temp_last_id[$id_tingkat_test] ++;
//            $jumlah_siswa_test++;
//            echo $data_siswa->NIS_SISWA . ',' . $data_siswa->NAMA_SISWA . ','.$data_siswa->NAMA_KELAS.';';
//        }
    }
//    break;
}
//exit();
$pdf->Output();
