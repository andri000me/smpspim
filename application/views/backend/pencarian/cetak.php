<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 * 
  $this->CoreFonts = array('courier', 'helvetica', 'times', 'symbol', 'zapfdingbats');
 * 
 */

if (!isset($data) || count($data) == 0) {
    echo '<h1>TIDAK ADA DATA YANG AKAN DICETAK.</h1>';

    exit();
}

$pdf = $this->fpdf;

$atas = 35;
$posisi_x_foto = 187;
$posisi_y_foto = $atas;
$lebar_foto = 45;
$tinggi_foto = 60;

function create_row($pdf, $kolom, $label, $value, $header = FALSE) {
    $margin_left = 10;
    $padding_left = 5;
    $lebar = 130;
    $lebar_label = 35;

    if ($header) {
        $pdf->Ln(3);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetX((($kolom - 1) * $lebar) + $margin_left);
        $pdf->Cell(0, 6, $label);
        $pdf->Ln();
    } else {
        $pdf->SetFont('Arial', '', 11);
        $pdf->SetX((($kolom - 1) * $lebar) + $margin_left + $padding_left);
        $pdf->Cell($lebar_label, 6, $label);
        $pdf->Cell(0, 6, ': ' . ($value == NULL ? '-' : $value));
        $pdf->Ln();
    }

    return $pdf;
}

foreach ($data as $detail) {
    $SISWA = $detail['SISWA'];

//    $pdf->SetMargins(6, 0);
    $pdf->AddPage("L", "A3");
    $pdf->SetAutoPageBreak(true, 0);

    $pdf->SetFont('Arial', 'B', 18);
    $pdf->Cell(0, 5, 'BUKU INDUK SISWA', 0, 0, 'C');
    $pdf->Ln(8);

    $pdf->SetLineWidth(0.05);
    $pdf->Line(3, 22, 417, 22);
    $pdf->Line(3, 278, 417, 278);

    if (file_exists('files/siswa/' . $SISWA->NIS_SISWA . '.jpg'))
        $pdf->Image(base_url('files/siswa/' . $SISWA->NIS_SISWA . '.jpg'), $posisi_x_foto, $posisi_y_foto, $lebar_foto, $tinggi_foto, '', '');
    elseif (file_exists('files/siswa/' . $SISWA->ID_SISWA . '.png'))
        $pdf->Image(base_url('files/siswa/' . $SISWA->ID_SISWA . '.png'), $posisi_x_foto, $posisi_y_foto, $lebar_foto, $tinggi_foto, '', '');
    else
        $pdf->Image(base_url('files/no_image.jpg'), $posisi_x_foto, $posisi_y_foto, $lebar_foto, $tinggi_foto, '', '');

    $kolom = 1;

    $pdf->SetY($atas);

    $pdf = create_row($pdf, $kolom, 'A. DATA DIRI', NULL, TRUE);
    $pdf = create_row($pdf, $kolom, 'NIS', $SISWA->NIS_SISWA);
    $pdf = create_row($pdf, $kolom, 'NISN', $SISWA->NISN_SISWA);
    $pdf = create_row($pdf, $kolom, 'Nomor KK', $SISWA->KK_SISWA);
    $pdf = create_row($pdf, $kolom, 'NIK', $SISWA->NIK_SISWA);
    $pdf = create_row($pdf, $kolom, 'Nama Lengkap', $SISWA->NAMA_SISWA);
    $pdf = create_row($pdf, $kolom, 'Nama Penggilan', $SISWA->PANGGILAN_SISWA);
    $pdf = create_row($pdf, $kolom, 'Angkatan', $SISWA->ANGKATAN_SISWA);
    $pdf = create_row($pdf, $kolom, 'Jenis Kelamin', $SISWA->NAMA_JK);
    $pdf = create_row($pdf, $kolom, 'Tempat Lahir', $SISWA->TEMPAT_LAHIR_SISWA);
    $pdf = create_row($pdf, $kolom, 'Tanggal Lahir', $this->date_format->to_print_text($SISWA->TANGGAL_LAHIR_SISWA));
    $pdf = create_row($pdf, $kolom, 'Umur', (date('Y') - date('Y', strtotime($SISWA->TANGGAL_LAHIR_SISWA))) . ' tahun');
    $pdf = create_row($pdf, $kolom, 'Agama', $SISWA->NAMA_AGAMA);
    $pdf = create_row($pdf, $kolom, 'Kewarganegaraan', $SISWA->NAMA_WARGA);
    $pdf = create_row($pdf, $kolom, 'Anak ke', $SISWA->ANAK_KE_SISWA);
    $pdf = create_row($pdf, $kolom, 'Jumlah Saudara', $SISWA->JUMLAH_SDR_SISWA);
    $pdf = create_row($pdf, $kolom, 'Kondisi', $SISWA->NAMA_KONDISI);
    $pdf = create_row($pdf, $kolom, 'Berat Badan (kg)', $SISWA->BERAT_SISWA);
    $pdf = create_row($pdf, $kolom, 'Tinggi Badan (cm)', $SISWA->TINGGI_SISWA);

    $pdf = create_row($pdf, $kolom, 'B. KONTAK', NULL, TRUE);
    $pdf = create_row($pdf, $kolom, 'Alamat', $SISWA->ALAMAT_SISWA);
    $pdf = create_row($pdf, $kolom, 'Kecamatan', $SISWA->NAMA_KEC);
    $pdf = create_row($pdf, $kolom, 'Kabupaten', trim(str_replace("Kabupaten", '', $SISWA->NAMA_KAB)));
    $pdf = create_row($pdf, $kolom, 'Provinsi', $SISWA->NAMA_PROV);
    $pdf = create_row($pdf, $kolom, 'Kode Pos', $SISWA->KODE_POS_SISWA);
    $pdf = create_row($pdf, $kolom, 'No HP', $SISWA->NOHP_SISWA);
    $pdf = create_row($pdf, $kolom, 'Email', $SISWA->EMAIL_SISWA);

    $pdf = create_row($pdf, $kolom, 'C. PONDOK PESANTREN', NULL, TRUE);
    $pdf = create_row($pdf, $kolom, 'Nama', $SISWA->NAMA_PONDOK_MPS);
    $pdf = create_row($pdf, $kolom, 'Pengasuh', $SISWA->PENGASUH_MPS);
    $pdf = create_row($pdf, $kolom, 'Alamat', $SISWA->ALAMAT_MPS);
    $pdf = create_row($pdf, $kolom, 'Jarak (m)', $SISWA->JARAK_MPS);
    $pdf = create_row($pdf, $kolom, 'Telp', $SISWA->TELP_MPS);
    $pdf = create_row($pdf, $kolom, 'Email', $SISWA->EMAIL_MPS);

    $pdf = create_row($pdf, $kolom, 'D. KESEHATAN', NULL, TRUE);
    $pdf = create_row($pdf, $kolom, 'Golongan Darah', $SISWA->NAMA_DARAH);
    $pdf = create_row($pdf, $kolom, 'Riwayat Penyakit', $SISWA->RIWAYAT_KESEHATAN_SISWA);

    $kolom = 2;

    $pdf->SetY($atas + 65);

    $pdf = create_row($pdf, $kolom, 'E. PENDIDIKAN ASAL', NULL, TRUE);
    $pdf = create_row($pdf, $kolom, 'Status Asal', $SISWA->NAMA_ASSAN);
    $pdf = create_row($pdf, $kolom, 'Nomor UM', $this->pengaturan->getKodeUM($SISWA));
    $pdf = create_row($pdf, $kolom, 'Masuk kejenjang', $SISWA->NAMA_JS_AS);
    $pdf = create_row($pdf, $kolom, 'Masuk ketingkat', $SISWA->MASUK_TINGKAT_SISWA);
    $pdf = create_row($pdf, $kolom, 'Nama Sekolah', $SISWA->NAMA_AS);
    $pdf = create_row($pdf, $kolom, 'Kecamatan Sekolah', $SISWA->NAMA_KEC_AS);
    $pdf = create_row($pdf, $kolom, 'Kabupaten Sekolah', $SISWA->NAMA_KAB_AS);
    $pdf = create_row($pdf, $kolom, 'Provinsi Sekolah', $SISWA->NAMA_PROV_AS);
    $pdf = create_row($pdf, $kolom, 'No Ijasah', $SISWA->NO_IJASAH_SISWA);
    $pdf = create_row($pdf, $kolom, 'Tanggal Ijasah', $this->date_format->to_print($SISWA->TANGGAL_IJASAH_SISWA));

    $pdf = create_row($pdf, $kolom, 'F. DATA AYAH', NULL, TRUE);
    $pdf = create_row($pdf, $kolom, 'NIK', $SISWA->AYAH_NIK_SISWA);
    $pdf = create_row($pdf, $kolom, 'Nama', $SISWA->AYAH_NAMA_SISWA);
    $pdf = create_row($pdf, $kolom, 'Status Hidup', $SISWA->NAMA_SO_AYAH);
    $pdf = create_row($pdf, $kolom, 'Tempat Lahir', $SISWA->AYAH_TEMPAT_LAHIR_SISWA);
    $pdf = create_row($pdf, $kolom, 'Tanggal Lahir', $this->date_format->to_print_text($SISWA->AYAH_TANGGAL_LAHIR_SISWA));
    $pdf = create_row($pdf, $kolom, 'Pendidikan', $SISWA->NAMA_JP_AYAH);
    $pdf = create_row($pdf, $kolom, 'Pekerjaan', $SISWA->NAMA_JENPEK_AYAH);

    $pdf = create_row($pdf, $kolom, 'G. DATA IBU', NULL, TRUE);
    $pdf = create_row($pdf, $kolom, 'NIK', $SISWA->IBU_NIK_SISWA);
    $pdf = create_row($pdf, $kolom, 'Nama', $SISWA->IBU_NAMA_SISWA);
    $pdf = create_row($pdf, $kolom, 'Status Hidup', $SISWA->NAMA_SO_IBU);
    $pdf = create_row($pdf, $kolom, 'Tempat Lahir', $SISWA->IBU_TEMPAT_LAHIR_SISWA);
    $pdf = create_row($pdf, $kolom, 'Tanggal Lahir', $this->date_format->to_print_text($SISWA->IBU_TANGGAL_LAHIR_SISWA));
    $pdf = create_row($pdf, $kolom, 'Pendidikan', $SISWA->NAMA_JP_IBU);
    $pdf = create_row($pdf, $kolom, 'Pekerjaan', $SISWA->NAMA_JENPEK_IBU);

    $kolom = 3;

    $pdf->SetY($atas + 65);

    $pdf = create_row($pdf, $kolom, 'H. DATA WALI', NULL, TRUE);
    $pdf = create_row($pdf, $kolom, 'NIK', $SISWA->WALI_NIK_SISWA);
    $pdf = create_row($pdf, $kolom, 'Nama', $SISWA->WALI_NAMA_SISWA);
    $pdf = create_row($pdf, $kolom, 'Hubungan', $SISWA->NAMA_HUB);
    $pdf = create_row($pdf, $kolom, 'Pendidikan', $SISWA->NAMA_JP_WALI);
    $pdf = create_row($pdf, $kolom, 'Pekerjaan', $SISWA->NAMA_JENPEK_WALI);

    $pdf = create_row($pdf, $kolom, 'I. DATA ORANGTUA', NULL, TRUE);
    $pdf = create_row($pdf, $kolom, 'Penghasilan', $SISWA->NAMA_HASIL);
    $pdf = create_row($pdf, $kolom, 'No. HP (1)', $SISWA->ORTU_NOHP1_SISWA);
    $pdf = create_row($pdf, $kolom, 'No. HP (2)', $SISWA->ORTU_NOHP2_SISWA);
    $pdf = create_row($pdf, $kolom, 'Email', $SISWA->ORTU_EMAIL_SISWA);
    $pdf = create_row($pdf, $kolom, 'Alamat', $SISWA->ORTU_ALAMAT_SISWA);
    $pdf = create_row($pdf, $kolom, 'Kecamatan', $SISWA->NAMA_KEC_ORTU);
    $pdf = create_row($pdf, $kolom, 'Kabupaten', trim(str_replace("Kabupaten", '', $SISWA->NAMA_KAB_ORTU)));
    $pdf = create_row($pdf, $kolom, 'Provinsi', $SISWA->NAMA_PROV_ORTU);

    $pdf->SetFont('Arial', 'B', 25);
    $pdf->SetXY($posisi_x_foto + $lebar_foto + 20, $atas + 10);
    $pdf->Cell(0, 5, $SISWA->NIS_SISWA);
    $pdf->SetXY($posisi_x_foto + $lebar_foto + 20, $atas + 25);
    $pdf->MultiCell(0, 5, strtoupper($SISWA->NAMA_SISWA));
    
    $pdf->SetY(285);
    $pdf->SetFont('Arial', 'I', 10);
    $pdf->Cell(0, 5, 'Dicetak dari '.$this->pengaturan->getNamaApp().' pada tanggal '.$this->date_format->to_print_text(date('Y-m-d')), 0, 0, 'R');
}

$pdf->Output();
