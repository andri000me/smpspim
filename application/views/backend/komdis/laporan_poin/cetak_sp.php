<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 * 
  $this->CoreFonts = array('courier', 'helvetica', 'times', 'symbol', 'zapfdingbats');
 * 
 */

$alamat_siswa = $data->ALAMAT_SISWA.', Kec. '.$data->NAMA_KEC.', '.$data->NAMA_KAB.', Prov '.$data->NAMA_PROV;

$pdf = $this->fpdf;

$pdf->AddPage("P", "A5");
//	$pdf->SetMargins(6, 0);
$pdf->SetAutoPageBreak(true, 0);

$pdf = $this->cetak->header_panitia_a5($pdf, $nama_panitia);

$pdf->Ln(5);

$pdf->SetFont('Arial', 'U', 18);
$pdf->Cell(0, 5, 'SURAT PERINGATAN', 0, 0, 'C');
$pdf->Ln();

$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 5, 'Nomor: '.$nomor_surat, 0, 0, 'C');
$pdf->Ln(12);

$pdf->Cell(12);
$pdf->Cell(0, 5, 'Kepada Saudara: ');
$pdf->Ln(8);

$pdf->Cell(12);
$pdf->Cell(20, 5, 'Nama');
$pdf->Cell(0, 5, ': '.$data->NAMA_SISWA);
$pdf->Ln(6);

$pdf->Cell(12);
$pdf->Cell(20, 5, 'Kelas');
$pdf->Cell(0, 5, ': '.(($data->NAMA_KELAS == NULL) ? 'Belum mempunyai kelas' : $data->NAMA_KELAS));
$pdf->Ln(6);

$pdf->Cell(12);
$pdf->Cell(20, 5, 'Alamat');
$pdf->MultiCell(0, 5, ': '.$alamat_siswa);
$pdf->Ln(1);

$pdf->Cell(12);
$pdf->Cell(20, 5, 'Domisili');
$pdf->MultiCell(0, 5, ': '.(($data->PONDOK_SISWA == NULL || $data->PONDOK_SISWA == 1) ? $alamat_siswa : ($data->NAMA_PONDOK_MPS.' '.$data->ALAMAT_MPS)));
$pdf->Ln(4);

$pdf->MultiCell(0, 5, 'Diberikan PERINGATAN sesuai dengan Peraturan Pelengkap Tata Tertib Siswa Perguruan Islam Mathali\'ul Falah Tahun 2010 Bab III tentang Pembinaan dan Sanksi Pasal 11 huruf b yang berbunyi: "Peringatan tertulis jika akumulasi skor pelanggaran antara 21 hingga 40 poin" dikarenakan telah memiliki akumulasi poin '.$data->POIN_KSH.'. Adapun data pelanggaran sebagaimana terlampir.');
$pdf->Ln(4);

$pdf->Cell(12);
$pdf->MultiCell(0, 5, 'Demikian untuk menjadi perhatian.');
$pdf->Ln(8);

$pdf->Cell(70);
$pdf->Cell(0, 5, $this->pengaturan->getDesa().', '.$this->date_format->to_print_text($tanggal));
$pdf->Ln();

$pdf->Cell(0, 5, 'Mengetahui');
$pdf->Ln();

$pdf->Cell(70, 5, 'Wali Kelas '.$data->NAMA_KELAS);
$pdf->Cell(0, 5, 'Ketua');
$pdf->Ln(18);

$pdf->Cell(70, 5, $data->WALI_KELAS);
$pdf->Cell(0, 5, $data->NAMA_TANGGUNGJAWAB);

$pdf->Output();