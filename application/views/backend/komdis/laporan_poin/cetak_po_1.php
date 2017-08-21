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
//$pdf->SetAutoPageBreak(true, 0);

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

$pdf->Cell(0, 5, $this->cetak->nama_peg_print($this->pengaturan->getDataKetuaKomdis()));
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
        $alamat = $DETAIL['ALAMAT_SISWA'].', Kec. '.$DETAIL['NAMA_KEC'].', '. str_replace("Kabupaten", "Kab.", $DETAIL['NAMA_KAB']);
        $data_detail = array(
            array('align' => 'C','width' => 10, 'text' => $no++),
            array('align' => 'L','width' => 35, 'text' => $DETAIL['NAMA_SISWA']),
            array('align' => 'L','width' => 25, 'text' => $DETAIL['NAMA_KELAS']),
            array('align' => 'L','width' => 40, 'text' => $DETAIL['WALI_KELAS']),
            array('align' => 'L','width' => 35, 'text' => $DETAIL['AYAH_NAMA_SISWA']),
            array('align' => 'L','width' => 42, 'text' => $alamat),
            array('align' => 'L','width' => 42, 'text' => (($DETAIL['PONDOK_SISWA'] == NULL || $DETAIL['PONDOK_SISWA'] == 1) ? $alamat : $DETAIL['NAMA_PONDOK_MPS'].' '.$DETAIL->ALAMAT_MPS)),
            array('align' => 'C','width' => 15, 'text' => $DETAIL['POIN_TAHUN_LALU_KSH']),
            array('align' => 'C','width' => 15, 'text' => $DETAIL['POIN_KSH']),
            array('align' => 'C','width' => 15, 'text' => $DETAIL['LARI_KSH']),
        );

        $pdf = $this->pdf_handler->wrap_row_table($pdf, $data_detail);
    }
    
    $pdf->Ln();
}


foreach ($DETAIL_PELANGGARAN as $detail) {
    $siswa = $detail['siswa'];
    $pelanggaran = $detail['pelanggaran'];

    $pdf->AddPage("P", "A4");
//	$pdf->SetMargins(6, 0);
    $pdf->SetAutoPageBreak(true, 0);

    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 5, 'DETAIL PELANGGARAN SISWA', 0, 0, 'C');
    $pdf->Ln();

    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(0, 5, 'KOMISI DISIPLIN SISWA', 0, 0, 'C');
    $pdf->Ln(8);

    $pdf->SetFont('Arial', '', 10);

//$pdf->Cell(20, 5, 'T A');
//$pdf->Cell(100, 5, ': ' . $siswa->NAMA_TA);
//$pdf->Cell(20, 5, 'Catur Wulan');
//$pdf->Cell(0, 5, ': ' . $siswa->NAMA_CAWU);
//$pdf->Ln();

    $pdf->Cell(20, 5, 'Nama');
    $pdf->Cell(100, 5, ': ' . $siswa['NAMA_SISWA']);
    $pdf->Cell(20, 5, 'N I S');
    $pdf->Cell(0, 5, ': ' . $siswa['NIS_SISWA']);
    $pdf->Ln();

    $pdf->Cell(20, 5, 'Domisili');
    $pdf->Cell(100, 5, ': ' . $this->pdf_handler->cut_text($pdf, ($siswa['PONDOK_SISWA'] == NULL || $siswa['PONDOK_SISWA'] == 1) ? 'Belum Mondok' : $siswa['NAMA_PONDOK_MPS'] . ' ' . $siswa->ALAMAT_MPS, 100));
    $pdf->Cell(20, 5, 'Kelas');
    $pdf->Cell(0, 5, ': ' . $siswa['NAMA_KELAS']);
    $pdf->Ln();

    $pdf->Cell(20, 5, 'Alamat');
    $pdf->Cell(100, 5, ': ' . $this->pdf_handler->cut_text($pdf, $siswa['ALAMAT_SISWA'] . ', Kec. ' . $siswa['NAMA_KEC'] . ', ' . str_replace('kabupaten', 'Kab.', strtolower($siswa['NAMA_KAB'])), 100));
    $pdf->Cell(20, 5, 'Jumlah Poin');
    $pdf->Cell(0, 5, ': ' . $siswa['POIN_KSH']);
    $pdf->Ln();

    $pdf->Cell(20, 5, 'Wali Murid');
    $pdf->Cell(100, 5, ': ' . $siswa['AYAH_NAMA_SISWA']);
    $pdf->Cell(20, 5, 'Surat');
    $pdf->Cell(0, 5, ': ' . ($siswa['NAMA_KJT'] == NULL ? '-' : $siswa['NAMA_KJT']));
    $pdf->Ln();

    $data_header = array(
        array('align' => 'C', 'width' => 10, 'height' => 5, 'text' => 'No'),
        array('align' => 'C', 'width' => 35, 'text' => 'Tanggal Memasukan Data'),
        array('align' => 'C', 'width' => 90, 'text' => 'Pelanggaran'),
        array('align' => 'C', 'width' => 40, 'text' => 'Tanggal Pelanggaran'),
        array('align' => 'C', 'width' => 15, 'text' => 'Poin')
    );

    $pdf->SetFont('Arial', 'B', 10);
    $pdf = $this->pdf_handler->wrap_row_table($pdf, $data_header);

    $pdf->SetFont('Arial', '', 10);
    $i = 1;
    foreach ($pelanggaran as $detail) {
        $data_header = array(
            array('align' => 'C', 'width' => 10, 'height' => 5, 'text' => $i++),
            array('align' => 'L', 'width' => 35, 'text' => $this->date_format->get_day($detail['TANGGAL_INPUT']) . ', ' . $this->date_format->to_print_short($detail['TANGGAL_INPUT'])),
            array('align' => 'L', 'width' => 90, 'text' => $detail['NAMA_KJP']),
            array('align' => 'L', 'width' => 40, 'text' => $this->date_format->get_day($detail['TANGGAL_KS']) . ', ' . $this->date_format->to_print_short($detail['TANGGAL_KS'])),
            array('align' => 'C', 'width' => 15, 'text' => $detail['POIN_KJP'])
        );

        $pdf = $this->pdf_handler->wrap_row_table($pdf, $data_header);
    }
    $pdf->SetFont('Arial', 'I', 8);
    $pdf->Cell(0, 5, 'Dicetak tanggal: ' . $this->date_format->to_print_short(date('Y-m-d')), 0, 0, 'R');
}

$pdf->Output();

