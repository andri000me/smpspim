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

$width = 210;
$height = 297 / 4;
$margin = 8;

$margin_detail = 20;
$width_detail = 25;

$MULAI_NOMOR_SURAT = $post['MULAI_NOMOR_SURAT'];

foreach ($data['DETAIL_PELANGGARAN'] as $detail) {
    $siswa = $detail['siswa'];
    $pelanggaran = $detail['pelanggaran'];

    // =================================== AMPLOP =================================== 

    $pdf->SetMargins($margin + 14, $margin);
    $pdf->AddPage("P", $this->pengaturan->getUkuranF4());
    $pdf->SetAutoPageBreak(true, 0);

    $pdf->SetTextColor(2, 116, 54);
    $pdf->SetDrawColor(2, 116, 54);
    $pdf = $this->cetak->header_yayasan($pdf, $margin, 'f4');
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetDrawColor(0, 0, 0);

    $pdf->Ln(8);

    $pdf->SetFont('Arial', '', 10);

    $pdf->Cell(13, 5, 'Nomor');
    $pdf->Cell(0, 5, ': KM/' . $MULAI_NOMOR_SURAT . '/A-II/PIM/' . (date('Y') - $this->pengaturan->getTahunBerdiri()) . '/' . $this->date_format->toRomawi(date('n')) . '/' . date('Y'));
    $pdf->Ln();
    $MULAI_NOMOR_SURAT++;

    $pdf->Cell(13, 5, 'Lamp.');
    $pdf->Cell(80, 5, ': 1 (satu) lembar');
    $pdf->Ln();

    $pdf->Cell(13, 5, 'Hal');
    $pdf->SetFont('Arial', 'IB', 10);
    $pdf->Cell(80, 5, ': Pemberitahuan & Undangan');
    $pdf->Ln();

    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(13, 5, 'Sifat');
    $pdf->Cell(80, 5, ': Sangat penting');
    $pdf->Ln();

    $pdf->SetY($height * 0.6);
    $pdf->SetFont('Arial', '', 10);

    $pdf->SetX(120);
    $pdf->Cell(0, 5, 'Kepada Yang Terhormat,');
    $pdf->Ln();

    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetX(120);
    $pdf->Cell(0, 5, 'Bapak/Ibu Wali Murid');
    $pdf->Ln();

    $pdf->SetX(120);
    $pdf->Cell(0, 5, '' . $this->cetak->nama_wali_siswa($siswa));
    $pdf->Ln();

    $pdf->SetFont('Arial', '', 10);
    $pdf->SetX(120);
    $pdf->Cell(0, 5, 'di');
    $pdf->Ln();

    $pdf->SetX(130);
    $pdf->Cell(0, 5, 'Tempat');
    $pdf->Ln(12);

    $pdf->SetFont('Arial', 'I', 10);
    $pdf->MultiCell(0, 5, 'Assalamu\'alaikum Warahmatullahi Wabarakatuh');
    $pdf->Ln(2);

    $pdf->SetFont('Arial', '', 10);
    $pdf->MultiCell(0, 5, 'Salam sejahtera, semoga kita semua senantiasa mendapatkan Ridlo dan Inayah dari Allah SWT. Amin.');
    $pdf->Ln(2);

    $pdf->MultiCell(0, 5, 'Selanjutnya berdasarkan laporan dan hasil pendataan Komisi Disiplin Siswa Perguruan Islam Mathali\'ul Falah tahun ajaran ' . $this->session->userdata('NAMA_TA_ACTIVE') . ', maka kami beritahukan kepada Bapak bahwa:');
    $pdf->Ln(2);

    $pdf->Cell($margin_detail);
    $pdf->Cell($width_detail, 5, 'No. Induk');
    $pdf->Cell(0, 5, ': ' . $siswa['NIS_SISWA']);
    $pdf->Ln();

    $pdf->Cell($margin_detail);
    $pdf->Cell($width_detail, 5, 'Nama');
    $pdf->Cell(0, 5, ': ' . $siswa['NAMA_SISWA']);
    $pdf->Ln();

    $pdf->Cell($margin_detail);
    $pdf->Cell($width_detail, 5, 'Orang Tua');
    $pdf->Cell(0, 5, ': ' . $siswa['AYAH_NAMA_SISWA']);
    $pdf->Ln();

    $pdf->Cell($margin_detail);
    $pdf->Cell($width_detail, 5, 'Alamat');
    $pdf->MultiCell(0, 5, ': ' . ($siswa['ALAMAT_SISWA'] . ', Kec. ' . $siswa['NAMA_KEC'] . ', ' . str_replace('Kabupaten', 'Kab.', $siswa['NAMA_KAB'])));

    $pdf->Cell($margin_detail);
    $pdf->Cell($width_detail, 5, 'Kelas');
    $pdf->Cell(0, 5, ': ' . $siswa['NAMA_KELAS']);
    $pdf->Ln(7);

    $pdf->MultiCell(0, 5, 'Siswa tersebut telah melakukan pelanggaran tata tertib siswa Perguruan Islam Mathali\'ul Falah hingga akumulasi pelanggaran telah melebihi batas maksimal 100 point. Sesuai Tata Tertib Siswa tahun 2016 tentang pembinaan dan sanksi pasal 29 di bab X peraturan pelengkap yang berbunyi: "Dicabut seluruh haknya sebagai siswa secara tetap, jika akumulasi skor pelanggaran telah mencapai Ta`liq (janji)".');
    $pdf->Ln(2);

    $pdf->MultiCell(0, 5, 'Oleh karena itu, dengan hormat kami mengharap atas kehadiran Bapak besok pada:');
    $pdf->Ln(2);

    $pdf->Cell($margin_detail);
    $pdf->Cell($width_detail, 5, 'Hari');
    $pdf->Cell(0, 5, ': ' . $this->date_format->get_day($post['TANGGAL_SURAT']) . ', ' . $this->date_format->to_print_text($post['TANGGAL_SURAT']));
    $pdf->Ln();

    $pdf->Cell($margin_detail);
    $pdf->Cell($width_detail, 5, 'Jam');
    $pdf->Cell(0, 5, ': ' . date('H:i', strtotime($post['JAM_SURAT'])) . ' WIB');
    $pdf->Ln();

    $pdf->Cell($margin_detail);
    $pdf->Cell($width_detail, 5, 'Tempat');
    $pdf->Cell(0, 5, ': ' . $post['TEMPAT_SURAT']);
    $pdf->Ln();

    $pdf->Cell($margin_detail);
    $pdf->Cell($width_detail, 5, 'Keperluan');
    $pdf->Cell(0, 5, ': Menghadap Bapak Pembantu Direktur');
    $pdf->Ln(7);

    $pdf->MultiCell(0, 5, 'Demikian surat pemberitahuan ini kami buat atas perhatiannya kami haturkan banyak terima kasih.');
    $pdf->Ln(2);

    $pdf->SetFont('Arial', 'I', 10);
    $pdf->MultiCell(0, 5, 'Wassalamu\'alaikum Warahmatullahi Wabarakatuh');
    $pdf->Ln();

    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(90);
    $pdf->Cell(0, 5, 'Kajen, ' . $this->date_format->to_print_text(date('Y-m-d')));
    $pdf->Ln(8);

    $pdf->Cell(90);
    $pdf->Cell(0, 5, 'A/n Direktur,');
    $pdf->Ln();

    $pdf->Cell(90);
    $pdf->Cell(0, 5, 'Pembantu Direktur Bidang Kesiswaan');
    $ttd = strtolower($post['TTD_SURAT']);
    $ttd = str_replace('.', '', $ttd);
    $ttd = str_replace(' ', '_', $ttd);
    $pdf->Image(base_url('files/aplikasi/ttd_' . $ttd . '.png'), 105, $pdf->GetY(), 50, 23, '', '');
    $pdf->Ln(18);

    $pdf->SetFont('Arial', 'UB', 10);
    $pdf->Cell(90);
    $pdf->Cell(0, 5, $post['TTD_SURAT']);
    $pdf->Ln(15);

    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(0, 5, 'Tembusan disampaikan kepada yth:');
    $pdf->Ln();

    $pdf->Cell(0, 5, 'Wali kelas ' . $siswa['NAMA_KELAS'] . ': ' . $this->cetak->nama_peg_print_title($siswa['GELAR_AWAL_WALI_KELAS'], $siswa['WALI_KELAS'], $siswa['GELAR_AKHIR_WALI_KELAS']));
    $pdf->Ln();

    // =================================== DETAIL =================================== 

    $pdf->SetMargins(13, 10);
    $pdf->AddPage("P", $this->pengaturan->getUkuranF4());
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
    $pdf->Cell(100, 5, ': ' . $siswa['NIS_SISWA'] . ' - ' . $siswa['NAMA_SISWA']);
    $pdf->Cell(20, 5, 'Telp.');
    $pdf->Cell(0, 5, ': ' . (!isset($siswa['NOHP_SISWA']) ? '-' : $siswa['NOHP_SISWA']));
    $pdf->Ln();

    $pdf->Cell(20, 5, 'Domisili');
    $pdf->Cell(100, 5, ': ' . $this->pdf_handler->cut_text($pdf, ($siswa['PONDOK_SISWA'] == NULL || $siswa['PONDOK_SISWA'] == 1) ? 'Belum Mondok' : $siswa['NAMA_PONDOK_MPS'] . ' ' . $siswa['ALAMAT_MPS'], 100));
    $pdf->Cell(20, 5, 'Kelas');
    $pdf->Cell(0, 5, ': ' . $siswa['NAMA_KELAS']);
    $pdf->Ln();

    $pdf->Cell(20, 5, 'Alamat');
    $pdf->Cell(100, 5, ': ' . $this->pdf_handler->cut_text($pdf, $siswa['ALAMAT_SISWA'] . ', Kec. ' . $siswa['NAMA_KEC'] . ', ' . str_replace('kabupaten', 'Kab.', strtolower($siswa['NAMA_KAB'])), 100));
    $pdf->Cell(20, 5, 'Surat');
    $pdf->Cell(0, 5, ': ' . ($siswa['NAMA_KJT'] == NULL ? '-' : $siswa['NAMA_KJT']));
    $pdf->Ln();

    $pdf->Cell(20, 5, 'Wali Santri');
    $pdf->Cell(100, 5, ': ' . $this->cetak->nama_wali_siswa($siswa));
    $pdf->Cell(20, 5, 'Jumlah Poin');
    $pdf->Cell(0, 5, ': ' . $siswa['JUMLAH_POIN_KSH']);
    $pdf->Ln();

    $pdf->Cell(20, 5, 'Wali Kelas');
    $pdf->Cell(100, 4, ': ' . $this->cetak->nama_peg_print_title($siswa['GELAR_AWAL_WALI_KELAS'], $siswa['WALI_KELAS'], $siswa['GELAR_AKHIR_WALI_KELAS']).' ('.(!isset($siswa['NOMOR_HP_WALI_KELAS']) ? '-' : $siswa['NOMOR_HP_WALI_KELAS']).')');
    $pdf->Cell(20, 5, 'Jumlah Lari');
    $pdf->Cell(0, 5, ': ' . $siswa['JUMLAH_LARI_KSH']);
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


$pdf->AddPage("P", $this->pengaturan->getUkuranF4());

for ($i = 0; $i < 350; $i++) {
    $pdf->Line(0, $i, 250, $i);
}

$pdf->SetY(100);
$pdf->SetFont('Arial', 'B', 40);
$pdf->Cell(0, 5, 'DIVIDER', 0, 0, 'C');
$pdf->Ln();

$MULAI_NOMOR_SURAT = $post['MULAI_NOMOR_SURAT'];
foreach ($data['DETAIL_PELANGGARAN'] as $detail) {
    $siswa = $detail['siswa'];
    $pelanggaran = $detail['pelanggaran'];
    $pdf = cetak($pdf, $siswa, $post, $MULAI_NOMOR_SURAT);
    $MULAI_NOMOR_SURAT++;
}

function cetak($pdf, $siswa, $data, $nomor_surat) {
    $CI = & get_instance();

    $f4 = $CI->pengaturan->getUkuranF4();
    $width = $f4[0];
    $height = $f4[1] / 3;
    $margin = 4;
    $penerima_margin = 110;

    $pdf->SetMargins($margin + 6, $margin);
    $pdf->AddPage("L", array($width, $height));
    $pdf->SetAutoPageBreak(true, 0);

    $pdf->SetTextColor(2, 116, 54);
    $pdf->SetDrawColor(2, 116, 54);
    $pdf = $CI->cetak->header_yayasan($pdf, $margin, 'f4');
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetDrawColor(0, 0, 0);

    $pdf->SetFont('Arial', '', 10);
    $pdf->Ln(2);
    $pdf->Cell(13, 5, 'Nomor');
    $pdf->Cell(0, 5, ': KM/' . $nomor_surat . '/A-II/PIM/' . (date('Y') - $CI->pengaturan->getTahunBerdiri()) . '/' . $CI->date_format->toRomawi(date('n')) . '/' . date('Y'));
    $pdf->Ln();

    $pdf->Cell(13, 5, 'Hal');
    $pdf->SetFont('Arial', 'I', 10);
    $pdf->MultiCell(80, 5, ': Pemberitahuan & Undangan');
    $pdf->Ln();

    $pdf->SetY($height * 0.6);
    $pdf->SetFont('Arial', '', 10);

    $pdf->Cell($penerima_margin);
    $pdf->Cell(0, 5, 'Kepada Yang Terhormat,');
    $pdf->Ln(8);

    $pdf->Cell($penerima_margin);
    $pdf->Cell(0, 5, 'Bapak/Ibu Wali Murid');
    $pdf->Ln();

    $pdf->Cell($penerima_margin);
    $pdf->Cell(0, 5, '' . $CI->cetak->nama_wali_siswa($siswa));
    $pdf->Ln();

    $pdf->Cell($penerima_margin);
    $pdf->Cell(0, 5, $siswa['ALAMAT_SISWA'] . ', Kec. ' . $siswa['NAMA_KEC']);
    $pdf->Ln();

    $pdf->Cell($penerima_margin);
    $pdf->Cell(0, 5, str_replace('Kabupaten', 'Kab.', $siswa['NAMA_KAB']));
    $pdf->Ln();

    return $pdf;
}

$pdf->Output();

