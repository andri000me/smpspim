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

/*
KETUA PD TU DAN KEUANGAN 1
 *  */
$pdf->AddPage("P", "A4");
//	$pdf->SetMargins(6, 0);
$pdf->SetAutoPageBreak(true, 0);

$pdf = $this->cetak->header_yayasan($pdf);

$pdf->Ln(7);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 5, 'SURAT KETERANGAN', 0, 0, 'C');
$pdf->Ln();

$pdf->SetLineWidth(0.30);
$pdf->Line(75, 48, 135, 48);

$pdf->SetFont('Arial', '', 10);
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
$pdf->Cell(0, 5, ': '.$siswa->NAMA_KELAS);
$pdf->Ln(10);

$pdf->MultiCell(0, 5, 'adalah benar-benar aktif belajar di kelas '.$siswa->NAMA_TINGK.' ('. trim($this->money->terbilang($siswa->NAMA_TINGK)).') '. ucwords(strtolower($siswa->NAMA_DEPT)).' Perguruan Islam Mathali\'ul Falah Kajen Margoyoso Pati Jawa Tengah pada tahun ajaran '.$this->session->userdata('NAMA_TA_ACTIVE').'.');
$pdf->Ln();

$pdf->MultiCell(0, 5, 'Demikian surat ini dibuat untuk menjadikan maklum adanya.');
$pdf->Ln(15);

$pdf->Cell(120);
$pdf->Cell(0, 5, 'Kajen, '.$this->date_format->to_print_text(date('Y-m-d')));
$pdf->Ln(7);

$pdf->Cell(120);
$pdf->Cell(0, 5, 'An. Direktur,');
$pdf->Ln();

$pdf->Cell(120);
$pdf->Cell(0, 5, 'Pembantu Direktur Bidang');
$pdf->Ln();

$pdf->Cell(120);
$pdf->Cell(0, 5, 'Ketata Usahaan Dan Keuangan');
$pdf->Ln(20);

$pdf->SetFont('Arial', 'UB', 10);

$pdf->Cell(120);
$pdf->Cell(0, 5, $this->cetak->nama_peg_print($this->pengaturan->getPDTUKeuangan1(), FALSE));
$pdf->Ln();


/*
KETUA PD TU DAN KEUANGAN 2
 *  */
$pdf->AddPage("P", "A4");
//	$pdf->SetMargins(6, 0);
$pdf->SetAutoPageBreak(true, 0);

$pdf = $this->cetak->header_yayasan($pdf);

$pdf->Ln(7);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 5, 'SURAT KETERANGAN', 0, 0, 'C');
$pdf->Ln();

$pdf->SetLineWidth(0.30);
$pdf->Line(75, 48, 135, 48);

$pdf->SetFont('Arial', '', 10);
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
$pdf->Cell(0, 5, ': '.$siswa->NAMA_KELAS);
$pdf->Ln(10);

$pdf->MultiCell(0, 5, 'adalah benar-benar aktif belajar di kelas '.$siswa->NAMA_TINGK.' ('. trim($this->money->terbilang($siswa->NAMA_TINGK)).') '. ucwords(strtolower($siswa->NAMA_DEPT)).' Perguruan Islam Mathali\'ul Falah Kajen Margoyoso Pati Jawa Tengah pada tahun ajaran '.$this->session->userdata('NAMA_TA_ACTIVE').'.');
$pdf->Ln();

$pdf->MultiCell(0, 5, 'Demikian surat ini dibuat untuk menjadikan maklum adanya.');
$pdf->Ln(15);

$pdf->Cell(120);
$pdf->Cell(0, 5, 'Kajen, '.$this->date_format->to_print_text(date('Y-m-d')));
$pdf->Ln(7);

$pdf->Cell(120);
$pdf->Cell(0, 5, 'An. Direktur,');
$pdf->Ln();

$pdf->Cell(120);
$pdf->Cell(0, 5, 'Pembantu Direktur Bidang');
$pdf->Ln();

$pdf->Cell(120);
$pdf->Cell(0, 5, 'Ketata Usahaan Dan Keuangan');
$pdf->Ln(20);

$pdf->SetFont('Arial', 'UB', 10);

$pdf->Cell(120);
$pdf->Cell(0, 5, $this->cetak->nama_peg_print($this->pengaturan->getPDTUKeuangan2(), FALSE));
$pdf->Ln();

$pdf->Output();
