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
    'calon_siswa_model' => 'calon_siswa',
));

$tahun_exp = explode('/', $this->session->userdata('NAMA_PSB_ACTIVE'));
$tahun = $tahun_exp[0];
$jumlah_perbaris = $this->pengaturan->getJumlahSiswaPerbaris();

// PENGATURAN FOTO SISWA
$start_x = 14;
$margin_left = 91;
$margin_top = 60;
$width_photo = 33;
$height_photo = 31;

$size_font = 12;

$pdf = $this->fpdf;

foreach ($data as $detail) {
    $tanggal = $detail['TANGGAL'];
    $denah = json_decode($detail['DENAH'], TRUE);
    
    foreach ($denah as $jk => $data_denah) {
        if ($jk == 'L') $jk = 'BANIN';
        else $jk = 'BANAT';
        
        $jumlah_peruang = $data_denah['JUMLAH_PERUANG'];
        $jumlah_perbaris = 3;
        
        $temp_jenjang = array_values(array_unique($data_denah['JENJANG']));
        $temp_jenjang_reset = array_fill(0, count($temp_jenjang), 0);
        $temp_last_id = array_combine($temp_jenjang, $temp_jenjang_reset);
        
        foreach ($data_denah['JENJANG'] as $dept) {
            $data_denah['KODE_JENJANG'][] = $CI->departemen->get_id_by_jenjang($dept);
        }
        
        foreach ($data_denah['DENAH'] as $ruang => $value) {

            $pdf->AddPage("L", "A4");
            $pdf->SetAutoPageBreak(true, 0);

            $pdf->SetFont('Arial', 'B', $size_font + 2);
            $pdf->Cell(0, 5, 'KARTU MEJA UJIAN MASUK '.$jk, 0, 0, 'C');
            $pdf->Ln();
            
            $pdf->Cell(5);
            $pdf->Cell(214, 5, 'RUANG UJIAN: '.$data_denah["RUANG"][$ruang]['KODE_RUANG'].' - '.$data_denah["RUANG"][$ruang]['NAMA_RUANG'], 0, 0, 'L');
            $pdf->Cell(0, 5, 'TANGGAL: '.$this->date_format->to_print($tanggal), 0, 0, 'C');
            $pdf->Ln();
            
            $posisi_y = 0;
            $z = 0;
            $start_y = 42;
            for ($i = 0; $i < $jumlah_peruang; $i++) {
                
                $pdf->SetFont('Arial', 'B', $size_font);
                
                $pdf->Cell(3);
                $pdf->Cell(88, 5, strtoupper($this->pengaturan->getNamaYayasan()), 'RTL', 0, 'C');
                
                $posisi_x = 0;
                if(((($i + 1)%$jumlah_perbaris) == 0) || (($i + 1) == $jumlah_peruang)) {
                    
                    $pdf->Ln();
                    
                    for ($x = (($i + 1) == $jumlah_peruang) ? $i : ($i + 1 - $jumlah_perbaris); $x <= $i; $x++) {
                        $pdf->Cell(3);
                        $pdf->Cell(88, 5, strtoupper($this->pengaturan->getNamaLembaga()), 'LR', 0, 'C');
                    }
                    
                    $pdf->SetFont('Arial', '', $size_font);
                    
                    $pdf->Ln();
                    
                    for ($x = (($i + 1) == $jumlah_peruang) ? $i : ($i + 1 - $jumlah_perbaris); $x <= $i; $x++) {
                        $pdf->Cell(3);
                        $pdf->Cell(88, 5, $this->pengaturan->getDesa().' - '.$this->pengaturan->getKecamatan().' - '.$this->pengaturan->getKabupaten().' '.$this->pengaturan->getKodepos(), 'LBR', 0, 'C');
                    }
                    
                    $pdf->SetFont('Arial', 'B', $size_font);
                    
                    $pdf->Ln();
                    
                    for ($x = (($i + 1) == $jumlah_peruang) ? $i : ($i + 1 - $jumlah_perbaris); $x <= $i; $x++) {
                        $pdf->Cell(3);
                        $pdf->Cell(88, 7, 'UJIAN MASUK TAHUN '.$tahun, 'LR', 0, 'C');
                    }
                    
                    $pdf->SetFont('Arial', '', $size_font);
                    
                    $pdf->Ln();
                    
                    for ($x = (($i + 1) == $jumlah_peruang) ? $i : ($i + 1 - $jumlah_perbaris); $x <= $i; $x++) {
                        $pdf->Cell(3);
                        $pdf->Cell($width_photo + 2, 5, '', 'L', 0, 'C');
                        $pdf->Cell(88 - $width_photo - 2, 5, 'No.: '.($x + 1), 'R', 0, 'L');
                    }
                    
                    $pdf->Ln();
                    
                    $baris_ke = (($z + 1)/$jumlah_perbaris) - 1;
                    
                    if($baris_ke > 0) $posisi_y += $margin_top;
                    else $posisi_y += $start_y;
                
                    $data_siswa = array();
                    $kolom_ke = 0;
                    for ($x = (($i + 1) == $jumlah_peruang) ? $i : ($i + 1 - $jumlah_perbaris); $x <= $i; $x++) {
                        if($kolom_ke > 0) $posisi_x += $margin_left;
                        else $posisi_x += $start_x;
                        
                        $kolom_ke++;
                        
                        if (isset($data_denah['DENAH'][$ruang][$x])) {
                            $id_tingkat = $data_denah['DENAH'][$ruang][$x];
                            $id_jenjang = $data_denah['JENJANG'][$id_tingkat];      
                            $id_siswa = $data_denah['DATA_SISWA_RANDOM'][$id_jenjang][$temp_last_id[$id_jenjang]];
                            $data_siswa[$x] = $CI->calon_siswa->get_by_id_simple($id_siswa);
                            $temp_last_id[$id_jenjang]++;
                            
                            $pdf->Cell(3);
                            
                            $pdf->Cell($width_photo + 2, 5, '', 'L', 0, 'C');
                            $pdf->Cell(88 - $width_photo - 2, 5, 'Kode: '.$this->pengaturan->getKodeUM($data_siswa[$x]), 'R', 0, 'L');

                            if (file_exists('files/siswa/' . $data_siswa[$x]->NIS_SISWA . '.jpg'))
                                $pdf->Image(base_url('files/siswa/' . $data_siswa[$x]->NIS_SISWA . '.jpg'), $posisi_x, $posisi_y, $width_photo - 7,75, $height_photo, '', '');
                            elseif (file_exists('files/siswa/' . $data_siswa[$x]->ID_SISWA . '.png'))
                                $pdf->Image(base_url('files/siswa/' . $data_siswa[$x]->ID_SISWA . '.png'), $posisi_x, $posisi_y, $width_photo - 7,75, $height_photo, '', '');
                            else
                                $pdf->Image(base_url('files/no_image.jpg'), $posisi_x, $posisi_y, $width_photo, $height_photo, '', '');
                        } else {
                            $pdf->Cell(3);
                            $pdf->Cell(88, 5, '', 'RL', 0, 'C');
                        }
                    }
                    
                    $pdf->Ln();
                    
                    for ($x = (($i + 1) == $jumlah_peruang) ? $i : ($i + 1 - $jumlah_perbaris); $x <= $i; $x++) {
                        if (isset($data_denah['DENAH'][$ruang][$x])) {
                            $id_tingkat = $data_denah['DENAH'][$ruang][$x];
                            
                            $pdf->Cell(3);
                            $pdf->Cell($width_photo + 2, 5, '', 'L', 0, 'C');
                            $pdf->Cell(88 - $width_photo - 2, 5, 'Nama: '.$this->cetak->nama_pendek_3($data_siswa[$x]->NAMA_SISWA), 'R', 0, 'L');
                        } else {
                            $pdf->Cell(3);
                            $pdf->Cell(88, 5, '', 'RL', 0, 'C');
                        }       
                    }
                    
                    $pdf->Ln();
                    
                    for ($x = (($i + 1) == $jumlah_peruang) ? $i : ($i + 1 - $jumlah_perbaris); $x <= $i; $x++) {
                        if (isset($data_denah['DENAH'][$ruang][$x])) {
                            $id_tingkat = $data_denah['DENAH'][$ruang][$x];
                            
                            $pdf->Cell(3);
                            $pdf->Cell($width_photo + 2, 5, '', 'L', 0, 'C');
                            $pdf->Cell(88 - $width_photo - 2, 5, 'Jenjang: '.$data_denah['KODE_JENJANG'][$id_tingkat].', Tingkat: '.$data_siswa[$x]->MASUK_TINGKAT_SISWA, 'R', 0, 'L');
                        } else {
                            $pdf->Cell(3);
                            $pdf->Cell(88, 5, '', 'RL', 0, 'C');
                        }       
                    }
                    
                    $pdf->Ln();
                    
                    for ($x = (($i + 1) == $jumlah_peruang) ? $i : ($i + 1 - $jumlah_perbaris); $x <= $i; $x++) {
                        $pdf->Cell(3);
                        $pdf->Cell(88, 3, '', 'RL', 0, 'C');
                    }
                    
                    $pdf->Ln();
                    
                    for ($x = (($i + 1) == $jumlah_peruang) ? $i : ($i + 1 - $jumlah_perbaris); $x <= $i; $x++) {
                        if (isset($data_denah['DENAH'][$ruang][$x])) {
                            $pdf->Cell(3);
                            $pdf->Cell($width_photo + 15, 5, '', 'L', 0, 'C');
                            $pdf->Cell(88 - $width_photo - 15, 5, 'Panitia Ujian,', 'R', 0, 'C');
                        } else {
                            $pdf->Cell(3);
                            $pdf->Cell(88, 5, '', 'RL', 0, 'C');
                        }       
                    }
                    
                    $pdf->Ln();
                    
                    for ($x = (($i + 1) == $jumlah_peruang) ? $i : ($i + 1 - $jumlah_perbaris); $x <= $i; $x++) {
                        if (isset($data_denah['DENAH'][$ruang][$x])) {
                            $pdf->Cell(3);
                            $pdf->Cell($width_photo + 15, 5, '', 'LB', 0, 'C');
                            $pdf->Cell(88 - $width_photo - 15, 5, 'ttd', 'RB', 0, 'C');
                        } else {
                            $pdf->Cell(3);
                            $pdf->Cell(88, 5, '', 'RLB', 0, 'C');
                        }       
                    }
                    
                    $pdf->Ln(10);
                }
                
                $z++;
                
                if ((($i + 1)%9) == 0) {
                    $pdf->AddPage("L", "A4");
                    $pdf->SetAutoPageBreak(true, 0);

                    $posisi_y = 0;
                    $z = 0;
                    $start_y = 32;
                }
            }
        }
    }
}

$pdf->Output();