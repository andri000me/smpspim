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

$size_font = 8;

$pdf = $this->fpdf;

$data_ruang = array();

foreach ($data as $detail) {
    $tanggal = $detail['TANGGAL'];
    $denah = json_decode($detail['DENAH'], TRUE);
    
    foreach ($denah as $jk => $data_denah) {
        $temp_jenjang = array_values(array_unique($data_denah['JENJANG']));
        $temp_jenjang_reset = array_fill(0, count($temp_jenjang), 0);
        $temp_last_id = array_combine($temp_jenjang, $temp_jenjang_reset);
        
        $jumlah_peruang = $data_denah['JUMLAH_PERUANG'];
        
        foreach ($data_denah['DENAH'] as $ruang => $value) {
            for ($i = 0; $i < $jumlah_peruang; $i++) {
                if (isset($data_denah['DENAH'][$ruang][$i])) {
                    $id_tingkat = $data_denah['DENAH'][$ruang][$i];
                    $id_jenjang = $data_denah['JENJANG'][$id_tingkat];      
                    $id_siswa = $data_denah['DATA_SISWA_RANDOM'][$id_jenjang][$temp_last_id[$id_jenjang]];
                    $temp_last_id[$id_jenjang]++;

                    $data_ruang[$id_siswa] = array(
                        'RUANG' => $data_denah["RUANG"][$ruang],
                        'NOMOR_KURSI' => $i
                    );
                }
            }
            
        }
    }
}


$temp_ID_TINGK = NULL;
$temp_JK_SISWA = NULL;

foreach ($siswa as $detail) {
    if(($detail->JK_SISWA != $temp_JK_SISWA) || ($detail->ID_TINGK != $temp_ID_TINGK)) {
        if(($temp_ID_TINGK != NULL) || ($temp_JK_SISWA != NULL)) {
            // $pdf->Ln(10);    
            // $pdf->Cell(100);
            // $pdf->Cell(0, 5, 'Penilai', 0, 0, 'L');
            // $pdf->Ln(15);    
            // $pdf->Cell(100);
            // $pdf->Cell(0, 5, '-----------------------------', 0, 0, 'L');
        }

        $pdf->AddPage("P", "A4");
        $pdf->SetAutoPageBreak(true, 0);

        $temp_JK_SISWA = $detail->JK_SISWA;
        $temp_ID_TINGK = $detail->ID_TINGK;

        if ($temp_JK_SISWA == 'L') $nama_jk = 'BANIN';
        else $nama_jk = 'BANAT';

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 5, 'PANITIA UJIAN', 0, 0, 'C');
        $pdf->Ln();

        $pdf->Cell(0, 5, 'DAFTAR RUANG UJIAN SISWA '.$nama_jk.' '.$detail->KETERANGAN_TINGK, 0, 0, 'C');
        $pdf->Ln();

        $pdf->Cell(0, 5, 'TAHUN '.date('Y'), 0, 0, 'C');
        $pdf->Ln(10);

        $pdf->SetFont('Arial', '', 10);
        $no = 1;
        $pdf->Cell(10, 5, 'No', 1, 0, 'C');
        $pdf->Cell(25, 5, 'No. UM', 1, 0, 'C');
        $pdf->Cell(60, 5, 'Nama', 1, 0, 'C');
        $pdf->Cell(25, 5, 'Kode Ruang', 1, 0, 'C');
        $pdf->Cell(55, 5, 'Nama Ruang', 1, 0, 'C');
        $pdf->Cell(15, 5, 'No. Kursi', 1, 0, 'C');
        $pdf->Ln();
    }

    $pdf->Cell(10, 5, $no++, 1, 0, 'C');
    $pdf->Cell(25, 5, $this->pengaturan->getKodeUM($detail), 1, 0, 'L');
    $pdf->Cell(60, 5, $detail->NAMA_SISWA, 1, 0, 'L');
    $pdf->Cell(25, 5, $data_ruang[$detail->ID_SISWA]['RUANG']['KODE_RUANG'], 1, 0, 'C');
    $pdf->Cell(55, 5, $data_ruang[$detail->ID_SISWA]['RUANG']['NAMA_RUANG'], 1, 0, 'L');
    $pdf->Cell(15, 5, $data_ruang[$detail->ID_SISWA]['NOMOR_KURSI'], 1, 0, 'C');
    $pdf->Ln();
}

$pdf->Output();

$pdf->Output();