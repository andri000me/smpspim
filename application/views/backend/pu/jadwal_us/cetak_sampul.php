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
));

$jumlah_perbaris = $this->pengaturan->getJumlahSiswaPerbaris();

$size_font = 10;

$pdf = $this->fpdf;

foreach ($data as $detail) {
    $tanggal = $detail['TANGGAL'];
    $denah = json_decode($detail['DENAH'], TRUE);

    foreach ($denah as $jk => $data_denah) {
        if ($jk == 'L')
            $jk_text = 'BANIN';
        else
            $jk_text = 'BANAT';
        
        if($jk != $detail['JK_PUJ'])
            continue;

        $jumlah_peruang = $data_denah['JUMLAH_PERUANG'];
        $jumlah_perbaris = $data_denah['JUMLAH_PERBARIS'];

        foreach ($data_denah['JENJANG'] as $dept) {
            $data_denah['KODE_JENJANG'][] = $CI->departemen->get_id_by_jenjang($dept);
        }

        foreach ($data_denah['DENAH'] as $ruang => $value) {

            $pdf->AddPage("P", $this->pengaturan->getUkuranF4());
            $pdf->SetAutoPageBreak(true, 0);

            $pdf->Image(base_url($this->pengaturan->getLogo()), 88, 80, 40, 41, '', '');

            $pdf->Image(base_url('files/aplikasi/kop_1.png'), 130, 10, 55, 7, '', '');
            $pdf->Image(base_url('files/aplikasi/kop_2.png'), 142, 17, 30, 7, '', '');
            $pdf->Image(base_url('files/aplikasi/kop_3.png'), 139, 23, 35, 6, '', '');
            $pdf->Image(base_url('files/aplikasi/kop_4.png'), 130, 28, 60, 6, '', '');

            $pdf->SetFont('Arial', 'B', $size_font);
            $pdf->Cell(110, 5, strtoupper($this->pengaturan->getNamaLembaga()), 0, 0, 'C');
            $pdf->Ln(5);

            $pdf->SetFont('Arial', 'B', $size_font + 2);
            $pdf->Cell(110, 5, strtoupper($this->pengaturan->getNamaYayasan()), 0, 0, 'C');
            $pdf->Ln(5);

            $pdf->SetFont('Arial', '', $size_font - 1);
            $pdf->Cell(110, 5, strtoupper($this->pengaturan->getDesa() . ' - ' . $this->pengaturan->getKecamatan() . ' - ' . $this->pengaturan->getKabupaten() . ' ' . $this->pengaturan->getKodepos()), 0, 0, 'C');
            $pdf->Ln(4);

            $pdf->Cell(110, 5, strtoupper($this->pengaturan->getProvinsi() . ' - ' . $this->pengaturan->getNegara()), 0, 0, 'C');
            $pdf->Ln(4);

            $pdf->Cell(110, 5, 'TELP. ' . $this->pengaturan->getTelp() . ' FAX. ' . $this->pengaturan->getFax(), 0, 0, 'C');
            $pdf->Ln(15);

            $pdf->SetLineWidth(0.50);
            $pdf->Line(11, 35, 200, 35);
            $pdf->SetLineWidth(0.30);
            $pdf->Line(11, 36, 200, 36);

            $pdf->SetFont('Arial', 'B', $size_font + 7);
            $pdf->Cell(0, 4, 'SOAL UJIAN SEKOLAH ' . $jk_text, 0, 0, 'C');
            $pdf->Ln(10);
            $pdf->SetFont('Arial', 'B', $size_font + 5);
            $pdf->Cell(0, 4, 'TAHUN AJARAN ' . $this->session->userdata("NAMA_TA_ACTIVE"), 0, 0, 'C');
            $pdf->Ln(6);
            $pdf->Cell(0, 4, 'CATUR WULAN ' . $this->session->userdata("ID_CAWU_ACTIVE"), 0, 0, 'C');
            $pdf->Ln(80);

            $pdf->SetFont('Arial', 'B', $size_font + 10);
            $pdf->Cell(0, 4, '', 'RLT', 0, 'C');
            $pdf->Ln();
            $pdf->Cell(0, 6, 'DOKUMEN RAHASIA', 'RL', 0, 'C');
            $pdf->Ln();
            $pdf->Cell(0, 4, '', 'RLB', 0, 'C');
            $pdf->Ln(20);

            $pdf->SetFont('Arial', 'B', $size_font + 5);
            $pdf->Cell(0, 4, 'RUANG', 0, 0, 'C');
            $pdf->Ln(7);
            $pdf->SetFont('Arial', 'B', $size_font + 7);
            $pdf->Cell(0, 4, $data_denah["RUANG"][$ruang]['KODE_RUANG'] . ' - ' . $data_denah["RUANG"][$ruang]['NAMA_RUANG'], 0, 0, 'C');
            $pdf->Ln(10);

//            $data_pengawas = $this->pengawas->get_by_jadwal_ruang($ID, $jk, $data_denah["RUANG"][$ruang]['KODE_RUANG']);
//
//            $pdf->SetFont('Arial', 'B', $size_font + 5);
//            $pdf->Cell(0, 4, 'PENGAWAS', 0, 0, 'C');
//            $pdf->Ln(7);
//            $pdf->SetFont('Arial', 'B', $size_font + 7);
//            $pdf->Cell(0, 4, ($data_pengawas == NULL) ? '................................................................' : $data_pengawas->NIP_PEG . ' - ' . $this->cetak->nama_peg_print($data_pengawas), 0, 0, 'C');
//            $pdf->Ln(10);

            $pdf->SetFont('Arial', 'B', $size_font + 5);
            $pdf->Cell(0, 4, 'TANGGAL', 0, 0, 'C');
            $pdf->Ln(7);
            $pdf->SetFont('Arial', 'B', $size_font + 3);
            $pdf->Cell(0, 4, strtoupper($this->date_format->get_day($detail['TANGGAL']) . ', ' . $this->date_format->to_print_text($detail['TANGGAL'])), 0, 0, 'C');
            $pdf->Ln(6);

            $pdf->SetFont('Arial', 'B', $size_font + 3);
            $pdf->Cell(0, 4, 'JAM: ' . $this->time_format->jam_menit($detail['JAM_MULAI']) . ' - ' . $this->time_format->jam_menit($detail['JAM_SELESAI']) . ' WIS', 0, 0, 'C');
            $pdf->Ln(10);

            $pdf->SetFont('Arial', 'B', $size_font + 2);
            foreach ($data_denah["JENJANG"] as $index => $jenjang) {
                $data_relasi = $this->jadwal->relasi_jenjang_departemen($ID, $jenjang, $data_denah["TINGKAT"][$index]);
//                if ($data_relasi == NULL) {
//                    $pdf->Cell(35, 8, $data_denah["KODE_JENJANG"][$index] . ' KELAS ' . $data_denah["TINGKAT"][$index], 1, 0, 'L');
//                    $pdf->Cell(110, 8, 'MAPEL: -', 1, 0, 'L');
//                    $pdf->Cell(45, 8, 'JUMLAH: - ORANG', 1, 0, 'L');
//                } else {
                    if(($data_denah['ATURAN_DENAH'][$ruang][$index] > 0) && ($data_relasi != NULL)) {
                        $pdf->Cell(35, 8, $data_relasi->DEPT_TINGK . ' KELAS ' . $data_relasi->NAMA_TINGK, 1, 0, 'L');
                        $pdf->Cell(110, 8, 'MAPEL: ' . $data_relasi->NAMA_MAPEL, 1, 0, 'L');
                        $pdf->Cell(45, 8, 'JUMLAH: ' . $data_denah['ATURAN_DENAH'][$ruang][$index] . ' ORANG', 1, 0, 'L');
                        $pdf->Ln();
                    }
//                }
            }
        }
    }
}

$pdf->Output();
