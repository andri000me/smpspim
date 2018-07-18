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
$pdf->SetLeftMargin(25);
$pdf->SetRightMargin(25);
$pdf->SetTopMargin(45);
$pdf->AddPage("P", array(215, 330));
$pdf->SetAutoPageBreak(true, 0);

$pdf->SetMargins(25, 25);
$pdf->Ln(7);
$pdf->SetFont('Times', 'BU', 16);
$pdf->Cell(0, 7, 'SURAT KETERANGAN BELAJAR', 0, 0, 'C');
$pdf->Ln();

$pdf->SetLineWidth(0.30);
$pdf->Line(135, 70, 135, 70);

$pdf->SetFont('Times', '', 12);
$pdf->Cell(0, 7, 'Nomor: KM/      /A-II/PIM/' . (date('Y') - $this->pengaturan->getTahunBerdiri()) . '/' . $this->date_format->toRomawi(date('n')) . '/' . date('Y'), 0, 0, 'C');
$pdf->Ln(10);

$pdf->MultiCell(0, 7, 'Direktur Perguruan Islam Mathali\'ul Falah Kajen Margoyoso Pati, menerangkan dengan sesungguhnya bahwa :');
$pdf->Ln();

$pdf->Cell(20);
$pdf->Cell(30, 7, 'Nama');
$pdf->Cell(0, 7, ': ' . $siswa->NAMA_SISWA);
$pdf->Ln();

$pdf->Cell(20);
$pdf->Cell(30, 7, 'No. Induk');
$pdf->Cell(0, 7, ': ' . $siswa->NIS_SISWA);
$pdf->Ln();

$pdf->Cell(20);
$pdf->Cell(30, 7, 'TTL');
$pdf->Cell(0, 7, ': ' . $siswa->TEMPAT_LAHIR_SISWA . ', ' . $this->date_format->to_print_text($siswa->TANGGAL_LAHIR_SISWA));
$pdf->Ln();

$pdf->Cell(20);
$pdf->Cell(30, 7, 'Orang tua');
$pdf->Cell(0, 7, ': ' . $siswa->AYAH_NAMA_SISWA);
$pdf->Ln();

$pdf->Cell(20);
$pdf->Cell(30, 7, 'Alamat');
$pdf->Cell(0, 7, ': ' . $siswa->ALAMAT_SISWA);
$pdf->Ln();

$pdf->Cell(52);
$pdf->Cell(0, 7, 'Kec. ' . $siswa->NAMA_KEC . ', ' . $siswa->NAMA_KAB . ', ' . $siswa->NAMA_PROV);
$pdf->Ln();

$pdf->Cell(20);
$pdf->Cell(30, 7, 'Kelas');
$pdf->Cell(0, 7, ': ' . $siswa->NAMA_KELAS);
$pdf->Ln(12);

$pdf->MultiCell(0, 7, 'adalah benar-benar aktif belajar di kelas ' . $this->date_format->toRomawi($siswa->NAMA_TINGK) . ' (' . trim($this->money->terbilang($siswa->NAMA_TINGK)) . ') ' . ucwords(strtolower($siswa->NAMA_DEPT)) . ' Perguruan Islam Mathali\'ul Falah Kajen Margoyoso Pati Jawa Tengah pada tahun ajaran ' . $this->session->userdata('NAMA_TA_ACTIVE') . '.');
$pdf->Ln();

$pdf->MultiCell(0, 7, 'Demikian surat ini dibuat untuk dipergunakan sebagaimana mestinya.');
$pdf->Ln();

$pdf->Cell(90);
$pdf->Cell(0, 7, 'Kajen, ' . $this->date_format->to_print_text(date('Y-m-d')));
$pdf->Ln(7);

$pdf->Cell(90);
$pdf->Cell(0, 7, 'An. Direktur,');
$pdf->Ln();

$pdf->Cell(90);
$pdf->Cell(0, 7, 'Pembantu Direktur Bidang');
$pdf->Ln();

$pdf->Cell(90);
$pdf->Cell(0, 7, 'Ketata Usahaan dan Keuangan');
$pdf->Ln(30);

$pdf->Image(base_url('files/aplikasi/ttd_wahib.png'), 105, 202, 60);
$pdf->SetFont('Times', 'UB', 12);

$pdf->Cell(90);
$pdf->Cell(0, 7, $this->cetak->nama_peg_print($this->pengaturan->getPDTUKeuangan2(), FALSE));
$pdf->Ln();

$pdf->Output();
