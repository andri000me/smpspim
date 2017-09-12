<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 * 
  $this->CoreFonts = array('courier', 'helvetica', 'times', 'symbol', 'zapfdingbats');
 * 
 */

$pdf = $this->fpdf;

$alasan = array(
    1 => 'atas permintaan sendiri dan orang tua karena pindah sekolah',
    2 => '',
    3 => 'atas permintaan sendiri dan orang tua (mengundurkan diri)',
    4 => 'karena meninggal dunia',
    5 => 'atas permintaan sendiri dan orang tua karena pindah tempat tinggal',
    98 => 'atas permintaan sendiri dan orang tua karena pindah sekolah',
    99 => 'karena telah lulus',
);

$pdf->SetLeftMargin(15);
$pdf->SetRightMargin(15);
$pdf->AddPage("P", "A4");
$pdf->SetAutoPageBreak(true, 0);

$pdf = $this->cetak->header_yayasan($pdf);

$pdf->Ln(7);
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 5, 'SURAT KETERANGAN', 0, 0, 'C');
$pdf->Ln();

$pdf->SetLineWidth(0.30);
$pdf->Line(75, 48, 135, 48);

$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 5, 'Nomor: KM/         /A-II/PIM/'.(date('Y') - $this->pengaturan->getTahunBerdiri()).'/'.$this->date_format->toRomawi(date('n')).'/'. date('Y'), 0, 0, 'C');
$pdf->Ln(10);

$pdf->MultiCell(0, 5, 'Direktur Perguruan Islam Mathali\'ul Falah Kajen Margoyoso Pati, menerangkan dengan sesungguhnya bahwa :');
$pdf->Ln();

$pdf->Cell(20);
$pdf->Cell(20, 5, 'Nama');
$pdf->Cell(0, 5, ': '.$siswa->NAMA_SISWA);
$pdf->Ln();

$pdf->Cell(20);
$pdf->Cell(20, 5, 'TTL');
$pdf->Cell(0, 5, ': '.$siswa->TEMPAT_LAHIR_SISWA.', '.$this->date_format->to_print_text($siswa->TANGGAL_LAHIR_SISWA));
$pdf->Ln();

$pdf->Cell(20);
$pdf->Cell(20, 5, 'Orang tua');
$pdf->Cell(0, 5, ': '.$siswa->AYAH_NAMA_SISWA);
$pdf->Ln();

$pdf->Cell(20);
$pdf->Cell(20, 5, 'Alamat');
$pdf->Cell(0, 5, ': '.$siswa->ALAMAT_SISWA);
$pdf->Ln();

$pdf->Cell(42);
$pdf->Cell(0, 5, 'Kec. '.$siswa->NAMA_KEC.' '.$siswa->NAMA_KAB.' '.$siswa->NAMA_PROV);
$pdf->Ln();

$pdf->Cell(20);
$pdf->Cell(20, 5, 'Kelas');
$pdf->Cell(0, 5, ': '.($siswa->NAMA_KELAS == NULL ? '-' : $siswa->NAMA_KELAS));
$pdf->Ln(10);

$pdf->MultiCell(0, 5, 'adalah benar-benar belajar '.($siswa->NAMA_TINGK == NULL ? 'pelajar di' : ('di kelas '.$siswa->NAMA_TINGK.' ('. trim($this->money->terbilang($siswa->NAMA_TINGK)).') '. ucwords(strtolower($siswa->NAMA_DEPT)))).' Perguruan Islam Mathali\'ul Falah Kajen Margoyoso Pati Jawa Tengah pada tahun ajaran '.$this->session->userdata('NAMA_TA_ACTIVE').'.');
$pdf->Ln();

$pdf->MultiCell(0, 5, 'Siswa tersebut keluar sekolah pada tanggal '.$this->date_format->to_print_text($siswa->TANGGAL_MUTASI_SISWA).' '.$alasan[$siswa->ID_MUTASI].'.');
$pdf->Ln();

$pdf->MultiCell(0, 5, 'Demikian surat ini dibuat untuk menjadikan maklum adanya.');
$pdf->Ln(15);

$pdf->Cell(100);
$pdf->Cell(0, 5, 'Kajen, '.$this->date_format->to_print_text(date('Y-m-d')));
$pdf->Ln(7);

$pdf->Cell(100);
$pdf->Cell(0, 5, 'An. Direktur,');
$pdf->Ln();

$pdf->Cell(100);
$pdf->Cell(0, 5, 'Pembantu Direktur Bidang');
$pdf->Ln();

$pdf->Cell(100);
$pdf->Cell(0, 5, 'Ketata Usahaan Dan Keuangan');
$pdf->Ln(20);

$pdf->SetFont('Arial', 'UB', 12);

$pdf->Cell(100);
$pdf->Cell(0, 5, 'H. Muhammad Ulin Nuha, Lc.');
$pdf->Ln();

$pdf->Output();
