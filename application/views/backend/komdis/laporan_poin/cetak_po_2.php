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

$pdf->AddPage("P", "A4");
//	$pdf->SetMargins(6, 0);
$pdf->SetAutoPageBreak(true, 0);

$pdf = $this->cetak->header_panitia_a4($pdf, $nama_panitia);

$pdf->Ln(2);

$pdf->SetFont('Arial', '', 10);
$pdf->Cell(15, 5, 'Nomor', 0, 0, 'L');
$pdf->Cell(0, 5, ': '.$nomor_surat);
$pdf->Ln();

$pdf->Cell(15, 5, 'Hal');
$pdf->Cell(0, 5, ': Rekomendasi');
$pdf->Ln();

$pdf->Cell(15, 5, 'Lamp');
$pdf->Cell(0, 5, ': 2 lembar');
$pdf->Ln(10);

$pdf->Cell(0, 5, 'Kepada yang terhormat,');
$pdf->Ln(10);

$pdf->Cell(0, 5, 'Bapak Direktur '.$this->pengaturan->getNamaLembaga().',');
$pdf->Ln();

$pdf->Cell(0, 5, 'Di ');
$pdf->Ln();

$pdf->Cell(15);
$pdf->Cell(0, 5, 'Mathali\'ul Falah');
$pdf->Ln(15);

$pdf->Cell(0, 5, 'Assalamu\'alaikum Warahamtullahi Wabarakatuh');
$pdf->Ln(10);

$pdf->MultiCell(0, 5, 'Sesuai dengan Peraturan Pelengkap Tata Tertib Siswa Perguruan Islam Mathali\'ul Falah Tahun 2010 Bab III tentang Pembinaan dan Sanksi Pasal 11 huruf c yang berbunyi: "Pemanggilan wali siswa jika akumulasi skor pelanggaran antara 41 hingga 60 poin", maka dengan ini kami dari  Komisi Disiplin Siswa Perguruan Islam Mathali\'ul Falah, merekomendasikan pemanggilan orang tua/wali dari siswa-siswa berikut ini:');
$pdf->Ln();

$pdf->MultiCell(0, 5, 'Adapun data-data pelanggaran siswa sebagaimana terlampir.');
$pdf->Ln();

$pdf->MultiCell(0, 5, 'Demikian surat rekomendasi ini kami buat atas perhatiannya kami haturkan banyak terima kasih.');
$pdf->Ln();

$pdf->Cell(0, 5, 'Wassalamu\'alaikum Warahmatullahi Wabarakatuh.');
$pdf->Ln(20);

$pdf->Cell(0, 5, $this->pengaturan->getDesa().', '.$this->date_format->to_print_text($tanggal));
$pdf->Ln(8);

$pdf->Cell(0, 5, 'Ketua');
$pdf->Ln(18);

$pdf->Cell(0, 5, $NAMA_TANGGUNGJAWAB);
$pdf->Ln(15);

$pdf->SetFont('Arial', 'U', 10);
$pdf->Cell(0, 5, 'Tembusan surat ini diberikan kepada yang terhormat:');
$pdf->Ln();

$pdf->SetFont('Arial', '', 10);
$pdf->Cell(5);
$pdf->Cell(0, 5, '1. Wali Kelas');
$pdf->Ln();

$pdf->Cell(5);
$pdf->Cell(0, 5, '2. Pembantu Direktur II ');
$pdf->Ln();

$pdf->Cell(5);
$pdf->Cell(0, 5, '3. Pembantu Direktur III');
$pdf->Ln();

$pdf->Cell(5);
$pdf->Cell(0, 5, '4. Pertinggal');
$pdf->Ln();

$pdf->AddPage("L", "A4");
$pdf->SetAutoPageBreak(true, 0);

$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 5, 'Lampiran surat nomor: '.$nomor_surat);
$pdf->Ln();

foreach ($JENJANG as $ID_DEPT => $NAMA_DEPT) {
    
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(0, 5, 'TINGKAT '.$NAMA_DEPT, 0, 0, 'C');
    $pdf->Ln();
    
    $data_header = array(
        array('align' => 'C','width' => 10, 'text' => 'No'),
        array('align' => 'C','width' => 35, 'text' => 'Nama'),
        array('align' => 'C','width' => 25, 'text' => 'Kelas'),
        array('align' => 'C','width' => 40, 'text' => 'Wali Kelas'),
        array('align' => 'C','width' => 35, 'text' => 'Orang Tua'),
        array('align' => 'C','width' => 42, 'text' => 'Alamat'),
        array('align' => 'C','width' => 42, 'text' => 'Domisili'),
        array('align' => 'C','width' => 15, 'text' => 'Poin Tahun Lalu'),
        array('align' => 'C','width' => 15, 'text' => 'Poin Skrg'),
        array('align' => 'C','width' => 15, 'text' => 'Lari'),
    );
    
    $pdf->SetFont('Arial', 'B', 10);
    $pdf = $this->pdf_handler->wrap_row_table($pdf, $data_header);
    
    $pdf->SetFont('Arial', '', 10);    
    $no = 1;
    foreach ($data[$ID_DEPT] as $DETAIL) {
        $alamat = $DETAIL->ALAMAT_SISWA.', Kec. '.$DETAIL->NAMA_KEC.', '. str_replace("Kabupaten", "Kab.", $DETAIL->NAMA_KAB);
        $data_detail = array(
            array('align' => 'C','width' => 10, 'text' => $no++),
            array('align' => 'L','width' => 35, 'text' => $DETAIL->NAMA_SISWA),
            array('align' => 'L','width' => 25, 'text' => $DETAIL->NAMA_KELAS),
            array('align' => 'L','width' => 40, 'text' => $DETAIL->WALI_KELAS),
            array('align' => 'L','width' => 35, 'text' => $DETAIL->AYAH_NAMA_SISWA),
            array('align' => 'L','width' => 42, 'text' => $alamat),
            array('align' => 'L','width' => 42, 'text' => (($DETAIL->PONDOK_SISWA == NULL || $DETAIL->PONDOK_SISWA == 1) ? $alamat : $DETAIL->NAMA_PONDOK_MPS.' '.$DETAIL->ALAMAT_MPS)),
            array('align' => 'C','width' => 15, 'text' => $DETAIL->POIN_TAHUN_LALU_KSH),
            array('align' => 'C','width' => 15, 'text' => $DETAIL->POIN_KSH),
            array('align' => 'C','width' => 15, 'text' => $DETAIL->LARI_KSH),
        );

        $pdf = $this->pdf_handler->wrap_row_table($pdf, $data_detail);
    }
    
    $pdf->Ln();
}

$pdf->Output();

