<?php
$cpdf = $this->tcpdf;

// ===================================================  START =================================================== 

// SILAHKAN RUBAH VARIABEL INI
$ukuran_font = 18;

$jarak_dari_atas_tahun_ajaran = 40;
$jarak_dari_kanan_tahun_ajaran = 30;

$jarak_dari_atas_detail_siswa = 60;
$jarak_dari_kanan_detail_siswa = 30;
$jarak_antar_detail_siswa = 10;

$jarak_dari_atas_tabel_nilai = 100;
$jarak_dari_kanan_tabel_kehadiran = 40;
$jarak_dari_kanan_tabel_syafawi = 60;
$jarak_dari_kanan_tabel_tahriri = 70;
$jarak_dari_kanan_tabel_angka_total_nilai = 80;
$jarak_dari_kanan_tabel_arab_total_nilai = 90;
$jarak_dari_kanan_tabel_taqdir = 100;

$jarak_dari_atas_tanggal_masehi = 150;
$jarak_dari_kanan_tanggal_masehi = 80;

$jarak_dari_atas_tanggal_hijriah = 170;
$jarak_dari_kanan_tanggal_hijriah = 80;

$jarak_dari_atas_ketua_panitia = 200;
$jarak_dari_kanan_ketua_panitia = 80;

// ===================================================  END =================================================== 

foreach ($data as $detail) {
    $cpdf->SetPrintHeader(false);
    $cpdf->SetPrintFooter(false);

    $cpdf->SetMargins(PDF_MARGIN_LEFT + 7, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT + 10);

    $cpdf->AddPage("L", 'A4');
    $cpdf->SetAutoPageBreak(true, 0);
    
    $cpdf->SetFont('aefurat', '', $ukuran_font); 
    
    $cpdf->setRTL(true);
    
    $cpdf->SetXY($jarak_dari_kanan_tahun_ajaran, $jarak_dari_atas_tahun_ajaran); 
    
    $nama_ta_exp = explode('/', $nama_ta);
    
    $cpdf->Cell(25, 7, $this->translasi_handler->proses($nama_ta_exp[0]).' - '.$this->translasi_handler->proses($nama_ta_exp[1]).' م');

    $cpdf->SetXY($jarak_dari_kanan_detail_siswa, $jarak_dari_atas_detail_siswa);
    $cpdf->Cell(0, 7, $this->translasi_handler->proses($detail->NAMA_SISWA));
    $cpdf->SetXY($jarak_dari_kanan_detail_siswa, $jarak_dari_atas_detail_siswa + ($jarak_antar_detail_siswa * 1));
    $cpdf->Cell(0, 7, $this->translasi_handler->proses($detail->TEMPAT_LAHIR_SISWA).' , '.$this->translasi_handler->proses($this->date_format->to_print_text($detail->TANGGAL_LAHIR_SISWA)));
    $cpdf->SetXY($jarak_dari_kanan_detail_siswa, $jarak_dari_atas_detail_siswa + ($jarak_antar_detail_siswa * 2));
    $cpdf->Cell(0, 7, $detail->NAMA_ARAB_TINGK);
    $cpdf->SetXY($jarak_dari_kanan_detail_siswa, $jarak_dari_atas_detail_siswa + ($jarak_antar_detail_siswa * 3));
    $cpdf->Cell(0, 7, $detail->TAQDIR_LN);
    
    
    $cpdf->SetXY($jarak_dari_kanan_tabel_kehadiran, $jarak_dari_atas_tabel_nilai);
    $cpdf->Cell(0, 7, $this->translasi_handler->proses($detail->KEHADIRAN_LN));
    $cpdf->SetXY($jarak_dari_kanan_tabel_syafawi, $jarak_dari_atas_tabel_nilai);
    $cpdf->Cell(0, 7, $this->translasi_handler->proses($detail->SYAFAWI_LN));
    $cpdf->SetXY($jarak_dari_kanan_tabel_tahriri, $jarak_dari_atas_tabel_nilai);
    $cpdf->Cell(0, 7, $this->translasi_handler->proses($detail->TAHRIRI_LN));
    $cpdf->SetXY($jarak_dari_kanan_tabel_angka_total_nilai, $jarak_dari_atas_tabel_nilai);
    $cpdf->Cell(0, 7, $this->translasi_handler->proses($detail->TOTAL_LN));
    $cpdf->SetXY($jarak_dari_kanan_tabel_arab_total_nilai, $jarak_dari_atas_tabel_nilai);
    $cpdf->Cell(0, 7, $this->translasi_handler->proses($detail->TOTAL_LN));  // BILANGAN DALAM BAHASA ARAB
    $cpdf->SetXY($jarak_dari_kanan_tabel_taqdir, $jarak_dari_atas_tabel_nilai);
    $cpdf->Cell(0, 7, $detail->TAQDIR_LN);
    
    $cpdf->SetXY($jarak_dari_kanan_tanggal_masehi, $jarak_dari_atas_tanggal_masehi.' م');
    $cpdf->Cell(0, 7, $this->translasi_handler->proses($this->date_format->to_print_text(date('Y-m-d'))));
    
    $cpdf->SetXY($jarak_dari_kanan_tanggal_hijriah, $jarak_dari_atas_tanggal_hijriah.' ه');
    $cpdf->Cell(0, 7, $tanggal_hijriyah);
    
    $cpdf->SetXY($jarak_dari_kanan_ketua_panitia, $jarak_dari_atas_ketua_panitia);
    $cpdf->Cell(0, 7, $ketua_panitia);
    
    $cpdf->setRTL(false);
}

$cpdf->Output();
?>