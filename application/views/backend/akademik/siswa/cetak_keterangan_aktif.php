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

$pdf->SetMargins(25, 25);
$pdf->Ln(7);
$pdf->SetFont('Times', 'BU', 16);
$pdf->Cell(0, 6, 'SURAT KETERANGAN BELAJAR', 0, 0, 'C');
$pdf->Ln();

$pdf->SetLineWidth(0.30);
$pdf->Line(135, 70,135, 70);

$pdf->SetFont('Times', '', 12);
$pdf->Cell(0, 6, 'Nomor: KM/      /A-II/PIM/'.(date('Y') - $this->pengaturan->getTahunBerdiri()).'/'.$this->date_format->toRomawi(date('n')).'/'. date('Y'), 0, 0, 'C');
$pdf->Ln(10);

$pdf->MultiCell(0, 6, 'Direktur Perguruan Islam Mathali\'ul Falah Kajen Margoyoso Pati, menerangkan dengan sesungguhnya bahwa :');
$pdf->Ln();

$pdf->Cell(20);
$pdf->Cell(20, 6, 'Nama');
$pdf->Cell(0, 6, ': '.$siswa->NAMA_SISWA);
$pdf->Ln();

$pdf->Cell(20);
$pdf->Cell(20, 6, 'No. Induk');
$pdf->Cell(0, 6, ': '.$siswa->NIS_SISWA);
$pdf->Ln();

$pdf->Cell(20);
$pdf->Cell(20, 6, 'TTL');
$pdf->Cell(0, 6, ': '.$siswa->TEMPAT_LAHIR_SISWA.', '.$this->date_format->to_print_text($siswa->TANGGAL_LAHIR_SISWA));
$pdf->Ln();

$pdf->Cell(20);
$pdf->Cell(20, 6, 'Orang tua');
$pdf->Cell(0, 6, ': '.$siswa->AYAH_NAMA_SISWA);
$pdf->Ln();

$pdf->Cell(20);
$pdf->Cell(20, 6, 'Alamat');
$pdf->Cell(0, 6, ': '.$siswa->ALAMAT_SISWA);
$pdf->Ln();

$pdf->Cell(42);
$pdf->Cell(0, 6, 'Kec. '.$siswa->NAMA_KEC.', '.$siswa->NAMA_KAB.', '.$siswa->NAMA_PROV);
$pdf->Ln();

$pdf->Cell(20);
$pdf->Cell(20, 6, 'Kelas');
$pdf->Cell(0, 6, ': '.$siswa->NAMA_KELAS);
$pdf->Ln(12);

$pdf->MultiCell(0, 6, 'adalah benar-benar aktif belajar di kelas '.$siswa->NAMA_TINGK.' ('. trim($this->money->terbilang($siswa->NAMA_TINGK)).') '. ucwords(strtolower($siswa->NAMA_DEPT)).' Perguruan Islam Mathali\'ul Falah Kajen Margoyoso Pati Jawa Tengah pada tahun ajaran '.$this->session->userdata('NAMA_TA_ACTIVE').'.');
$pdf->Ln();

$pdf->MultiCell(0, 6, 'Demikian surat ini dibuat untuk dipergunakan sebagaimana mestinya.');
$pdf->Ln();

$pdf->Cell(90);
$pdf->Cell(0, 6, 'Kajen, '.$this->date_format->to_print_text(date('Y-m-d')));
$pdf->Ln(7);

$pdf->Cell(90);
$pdf->Cell(0, 6, 'An. Direktur,');
$pdf->Ln();

$pdf->Cell(90);
$pdf->Cell(0, 6, 'Pembantu Direktur Bidang');
$pdf->Ln();

$pdf->Cell(90);
$pdf->Cell(0, 6, 'Ketata Usahaan dan Keuangan');
$pdf->Ln(25);

$pdf->Image(base_url('files/aplikasi/ttd_wahib.png'), 105, 175, 55);
$pdf->SetFont('Times', 'UB', 12);

$pdf->Cell(90);
$pdf->Cell(0, 6, $this->cetak->nama_peg_print($this->pengaturan->getPDTUKeuangan2(), FALSE));
$pdf->Ln();

$pdf->Output();
