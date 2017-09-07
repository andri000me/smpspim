<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 * 
  $CI->CoreFonts = array('Arial', 'helvetica', 'times', 'symbol', 'zapfdingbats');
 * 
 */

$pdf = $this->fpdf;

foreach ($SISWA as $detail) {
    $pdf = cetak($pdf, $detail, $DATA, $PEGAWAI);
}

function cetak($pdf, $siswa, $data, $pegawai) {
    $CI =& get_instance();

    $width = 210;
    $height = 297 / 4;

    $length_left = $width * 0.35;
    $length_right = $width * 0.6;
    $margin_between = 3;

    $pdf->SetMargins(3, 3);
    $pdf->AddPage("L", array($width, $height));
    $pdf->SetAutoPageBreak(true, 0);

    $pdf->SetLineWidth(0.10);
    $pdf->Line($length_left + $margin_between + 1, 0, $length_left + $margin_between + 1, $height);

    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell($length_left, 4, strtoupper($CI->pengaturan->getNamaLembaga()));
    $pdf->Cell($margin_between);
    $pdf->Cell($length_right, 4, strtoupper($CI->pengaturan->getNamaLembaga()));
    $pdf->Ln();

    $pdf->SetFont('Arial', '', 9);
    $pdf->Cell($length_left, 4, strtoupper($CI->pengaturan->getDesa() . ' - ' . $CI->pengaturan->getKecamatan() . ' - ' . $CI->pengaturan->getKabupaten() . ' ' . $CI->pengaturan->getKodepos()));
    $pdf->Cell($margin_between);
    $pdf->Cell($length_right, 4, strtoupper($CI->pengaturan->getDesa() . ' - ' . $CI->pengaturan->getKecamatan() . ' - ' . $CI->pengaturan->getKabupaten() . ' ' . $CI->pengaturan->getKodepos()));
    $pdf->Ln();

    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell($length_left, 4, 'TANDA BUKTI PEMBAYARAN');
    $pdf->Cell($margin_between);
    $pdf->Cell($length_right, 4, 'TANDA BUKTI PEMBAYARAN');
    $pdf->Ln();

    $pdf->SetLineWidth(0.40);
    $pdf->Line(4, 15, $length_left, 15);

    $pdf->SetLineWidth(0.40);
    $pdf->Line($length_left + $margin_between + 4, 15, $length_right + 78, 15);

    $pdf->SetFont('Arial', '', 9);
    $pdf->Cell($length_left, 4, 'Telah menerima uang dari:');
    $pdf->Cell($margin_between);
    $pdf->Cell($length_right, 4, 'Yang bertanda tangan di bawah ini telah menerima uang dari:');
    $pdf->Ln();

    $pdf->Cell($margin_between);
    $pdf->Cell($length_left * 0.2, 4, 'Nama');
    $pdf->Cell($length_left * 0.8, 4, ': '.$siswa->NAMA_SISWA);
    $pdf->Cell($margin_between * 2);
    $pdf->Cell($length_right * 0.15, 4, 'Nama');
    $pdf->Cell($length_right * 0.85, 4, ': '.$siswa->NAMA_SISWA);
    $pdf->Ln();
    
    $pdf->Cell($margin_between);
    $pdf->Cell($length_left * 0.2, 4, 'Orangtua');
    $pdf->Cell($length_left * 0.8, 4, ': '.$siswa->AYAH_NAMA_SISWA);
    $pdf->Cell($margin_between * 2);
    $pdf->Cell($length_right * 0.15, 4, 'Orangtua');
    $pdf->Cell($length_right * 0.85, 4, ': '.$siswa->AYAH_NAMA_SISWA);
    $pdf->Ln();
    
    $pdf->Cell($margin_between);
    $pdf->Cell($length_left * 0.2, 4, 'Alamat');
    $pdf->Cell($length_left * 0.8, 4, ': '.$siswa->ALAMAT_SISWA.', '.$siswa->NAMA_KEC);
    $pdf->Cell($margin_between * 2);
    $pdf->Cell($length_right * 0.15, 4, 'Alamat');
    $pdf->Cell($length_right * 0.85, 4, ': '.$siswa->ALAMAT_SISWA.', '.$siswa->NAMA_KEC.', '.$siswa->NAMA_KAB);
    $pdf->Ln();
    
    $pdf->Cell($margin_between);
    $pdf->Cell($length_left * 0.2, 4, 'NIS');
    $pdf->Cell($length_left * 0.8, 4, ': '.$siswa->NIS_SISWA);
    $pdf->Cell($margin_between * 2);
    $pdf->Cell($length_right * 0.15, 4, 'NIS');
    $pdf->Cell($length_right * 0.85, 4, ': '.$siswa->NIS_SISWA);
    $pdf->Ln();
    
    $pdf->Cell($margin_between);
    $pdf->Cell($length_left * 0.2, 4, 'Kelas');
    $pdf->Cell($length_left * 0.8, 4, ': '.$siswa->NAMA_KELAS);
    $pdf->Cell($margin_between * 2);
    $pdf->Cell($length_right * 0.15, 4, 'Kelas');
    $pdf->Cell($length_right * 0.85, 4, ': '.$siswa->NAMA_KELAS);
    $pdf->Ln();
    
    $x_start = $pdf->GetX();
    $y_start = $pdf->GetY();
    $pdf->MultiCell($length_left, 4, 'Sejumlah '.$data['TEMP_NOMINAL'].' ('.$CI->money->terbilang($data['NOMINAL']).'rupiah) guna '.$data['KETERANGAN'].'.');
    $x_end = $pdf->GetX();
    $y_end = $pdf->GetY();
    
    $pdf->SetXY($x_start, $y_start);
    $pdf->Cell($length_left + $margin_between);
    $pdf->MultiCell($length_right, 4, 'Sejumlah '.$data['TEMP_NOMINAL'].' ('.$CI->money->terbilang($data['NOMINAL']).'rupiah) guna '.$data['KETERANGAN'].'.');
    
    $pdf->SetY($height * 0.7);
    $pdf->Cell($length_left * 0.4);
    $pdf->Cell($length_left * 0.6, 4, 'Kajen, ..........................');
    $pdf->Cell($margin_between);
    $pdf->Cell($length_right * 0.5);
    $pdf->Cell($length_right * 0.5, 4, 'Kajen, ...................................');
    $pdf->Ln();
    
    $pdf->Cell($length_left * 0.4);
    $pdf->Cell($length_left * 0.6, 4, 'Penerima,');
    $pdf->Cell($margin_between);
    $pdf->Cell($length_right * 0.5);
    $pdf->Cell($length_right * 0.5, 4, 'Penerima,');
    $pdf->Ln(8);
    
    $pdf->SetFont('Arial', 'U', 9);
    $pdf->Cell($length_left * 0.4);
    $pdf->Cell($length_left * 0.6, 4, $this->cetak->nama_peg_print($pegawai));
    $pdf->Cell($margin_between);
    $pdf->Cell($length_right * 0.5);
    $pdf->Cell($length_right * 0.5, 4, $this->cetak->nama_peg_print($pegawai));
    $pdf->Ln();
    
    $pdf->SetFont('Arial', '', 9);
    $pdf->Cell($length_left * 0.4);
    $pdf->Cell($length_left * 0.6, 4, $pegawai->NIP_PEG);
    $pdf->Cell($margin_between);
    $pdf->Cell($length_right * 0.5);
    $pdf->Cell($length_right * 0.5, 4, $pegawai->NIP_PEG);
    $pdf->Ln();

    return $pdf;
}

$pdf->Output();
