<?php

if (!isset($data) || count($data) == 0) {
    echo '<h1>TIDAK ADA DATA YANG AKAN DICETAK.</h1>';

    exit();
}

$pdf = $this->fpdf;

$temp_kelas = null;
foreach ($data as $detail) {
    $PONDOK = $detail['PONDOK'];
    $DATA = $detail['DATA'];
    $pdf->AddPage("P", $this->pengaturan->getUkuranF4());

    $pdf = $this->cetak->header_yayasan($pdf, 0, 'F4');

    $pdf->Ln(5);

    $pdf->SetFont('Times', '', 12);
    $pdf->Cell(15, 4, 'Nomor');
    $pdf->Cell(0, 4, ': KM/       /A-II/PIM/' . (date('Y') - $this->pengaturan->getTahunBerdiri()) . '/' . $this->date_format->toRomawi(date('n')) . '/' . date('Y'));
    $pdf->Ln(5);

    $pdf->SetFont('Times', '', 12);
    $pdf->Cell(15, 4, 'Hal');
    $pdf->SetFont('Times', 'BI', 12);
    $pdf->Cell(90, 4, ': Pemberitahuan');
    $pdf->SetFont('Times', '', 12);
    $pdf->Cell(0, 4, 'Kepada Yang Terhormat;');
    $pdf->Ln(5);

    $pdf->SetFont('Times', '', 12);
    $pdf->Cell(15, 4, 'Sifat');
    $pdf->Cell(90, 4, ': Sangat Penting');
    $pdf->SetFont('Times', 'B', 12);
    $pdf->Cell(0, 4, 'Bapak/Ibu Pengasuh ');
    $pdf->Ln(5);

    $pdf->SetFont('Times', 'B', 12);
    $pdf->Cell(105);
    $pdf->Cell(0, 4, $PONDOK->NAMA_PONDOK_MPS);
    $pdf->Ln(5);

    $pdf->SetFont('Times', '', 12);
    $pdf->Cell(105);
    $pdf->Cell(0, 5, 'di tempat.');
    $pdf->Ln(15);

    $pdf->SetFont('Times', 'I', 12);
    $pdf->Cell(0, 4, 'Assalamu\'alaikum Warahmatullahi Wabarakatuh');
    $pdf->Ln(8);

    $pdf->SetFont('Times', '', 12);
    $pdf->MultiCell(0, 5, 'Puji dan syukur kehadirat Allah SWT. Atas segala nikmat yang diberikan-Nya kepada kita. Shalawat dan salam selalu tercurah kepada Nabi Muhammad SAW.');
    $pdf->Ln();

    $pdf->MultiCell(0, 5, 'Sebagai upaya meletakkan nilai kepribadian dan kemampuan peserta didik, Perguruan Islam Mathali\'ul Falah senantiasa berkomitmen untuk mempersiapkan peserta didik menjadi "manusia yang sholih dan akrom". Selanjutnya berdasarkan laporan dan hasil pendataan Komisi Disiplin Siswa Perguruan Islam Mathali’ul Falah dalam keberlangsungannya proses pembelajaran dan pengajaran tahun ajaran 2017/2018, maka kami beritahukan kepada Bapak bahwa nama-nama terlampir telah melakukan pelanggar tata tertib dan peraturan Perguruan Islam Mathali\'ul Falah dengan akumulasi skor sebagaimana terlampir (data update perbulan).');
    $pdf->Ln();

    $pdf->MultiCell(0, 5, 'Selanjutnya kami mohon dengan hormat, bila terdapat data santri yang tidak sesuai (pindah pondok atau tambahan) mohon untuk dapat konfirmasi ke nomor WA 082300024234 atau email: komdis@pim.sch.id.');
    $pdf->Ln();

    $pdf->MultiCell(0, 5, 'Demikian surat ini dibuat, atas perhatian serta kerjasamanya dari Bapak/Ibu kami haturkan terima kasih.');
    $pdf->Ln();

    $pdf->SetFont('Times', 'I', 12);
    $pdf->MultiCell(0, 5, 'Wallahul Muwaffiq ila aqwamith Thariq.');
    $pdf->Ln();

    $pdf->Cell(0, 4, 'Wassalamu\'alaikum Warahmatullahi Wabarakatuh');
    $pdf->Ln(15);

    $pdf->SetFont('Times', '', 12);
    $pdf->Cell(110);
    $pdf->Cell(0, 5, 'Kajen, ' . $this->date_format->to_print_text(date('d-m-Y')));
    $pdf->Ln();

    $pdf->Cell(110);
    $pdf->Cell(0, 5, 'A/n. Direktur,');
    $pdf->Ln();

    $pdf->Cell(110);
    $pdf->Cell(0, 5, 'Pembantu Direktur Bidang Kesiswaan');
    $pdf->Ln(20);

    $pdf->Image(base_url('files/aplikasi/ttd_h_muhammad_mulin_niam.png'), 120, 200, 60);

    $pdf->SetFont('Times', 'BU', 12);
    $pdf->Cell(110);
    $pdf->Cell(0, 5, 'Muhammad Mulin Ni\'am, MA');
    $pdf->Ln();



    $pdf->AddPage("P", $this->pengaturan->getUkuranF4());
//	$pdf->SetMargins(6, 0);
    $pdf->SetAutoPageBreak(true, 0);

    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 4, 'KOMISI DISIPLIN SISWA', 0, 0, 'C');
    $pdf->Ln();

    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(0, 4, 'DATA PERKEMBANGAN POINT', 0, 0, 'C');
    $pdf->Ln(8);

    $pdf->SetFont('Arial', 'B', 9);

    $pdf->Cell(15, 4, 'Pondok');
    $pdf->Cell(0, 4, ': ' . $PONDOK->NAMA_PONDOK_MPS);
    $pdf->Ln();

    $pdf->SetFont('Arial', '', 8);
    $pdf->Cell(7, 8, 'No', 'LTR', 0, 'C');
    $pdf->Cell(20, 8, 'NIS', 1, 0, 'C');
    $pdf->Cell(43, 8, 'Nama', 1, 0, 'C');
    $pdf->Cell(7, 4, 'Poin', 'LTR', 0, 'C');
    $pdf->Cell(7, 8, 'L', 1, 0, 'C');
    $pdf->Cell(20, 8, 'Surat', 1, 0, 'C');
    $pdf->Cell(65, 4, 'Bulan', 1, 0, 'C');
    $pdf->Cell(18, 4, 'Cawu', 1, 0, 'C');
    $pdf->Cell(7, 8, 'Jml', 1, 0, 'C');
    $pdf->Ln(4);
    $pdf->Cell(7);
    $pdf->Cell(63);
    $pdf->Cell(7, 4, 'Lalu', 'LBR', 0, 'C');
    $pdf->Cell(27);
    $pdf->Cell(65 / 12, 4, '07', 1, 0, 'C');
    $pdf->Cell(65 / 12, 4, '08', 1, 0, 'C');
    $pdf->Cell(65 / 12, 4, '09', 1, 0, 'C');
    $pdf->Cell(65 / 12, 4, '10', 1, 0, 'C');
    $pdf->Cell(65 / 12, 4, '11', 1, 0, 'C');
    $pdf->Cell(65 / 12, 4, '12', 1, 0, 'C');
    $pdf->Cell(65 / 12, 4, '01', 1, 0, 'C');
    $pdf->Cell(65 / 12, 4, '02', 1, 0, 'C');
    $pdf->Cell(65 / 12, 4, '03', 1, 0, 'C');
    $pdf->Cell(65 / 12, 4, '04', 1, 0, 'C');
    $pdf->Cell(65 / 12, 4, '05', 1, 0, 'C');
    $pdf->Cell(65 / 12, 4, '06', 1, 0, 'C');
    $pdf->Cell(6, 4, '1', 1, 0, 'C');
    $pdf->Cell(6, 4, '2', 1, 0, 'C');
    $pdf->Cell(6, 4, '3', 1, 0, 'C');
    $pdf->Ln(4);

    $jumlah_sp = 0;
    $jumlah_po_1 = 0;
    $jumlah_po_2 = 0;
    $jumlah_takliq = 0;
    $jumlah_mutasi = 0;
    $jumlah_siswa = 0;
    foreach ($DATA as $DETAIL) {
//        if(!isset($DETAIL->NAMA_KELAS))
//            continue;

        if (is_array($DETAIL))
            $DETAIL = json_decode(json_encode($DETAIL));

        if ($temp_kelas != $DETAIL->NAMA_KELAS) {
            $no = 1;
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell(194, 4, 'KELAS ' . strtoupper($DETAIL->NAMA_KELAS), 1, 0, 'L');
            $pdf->Ln();

            $temp_kelas = $DETAIL->NAMA_KELAS;
        }

        if ($DETAIL->ID_KJT == 1)
            $jumlah_sp++;
        elseif ($DETAIL->ID_KJT == 2)
            $jumlah_po_1++;
        elseif ($DETAIL->ID_KJT == 3)
            $jumlah_po_2++;
        elseif ($DETAIL->ID_KJT == 4)
            $jumlah_takliq++;
        elseif ($DETAIL->ID_KJT == 5)
            $jumlah_mutasi++;

        if ($DETAIL->AKTIF_AS)
            $pdf->setFillColor(255, 255, 255);
        else
            $pdf->setFillColor(128, 128, 128);

        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(7, 4, $no++, 1, 0, 'C', TRUE);
        $pdf->Cell(20, 4, $DETAIL->NIS_SISWA == NULL ? 'KELUAR' : $DETAIL->NIS_SISWA, 1, 0, 'C', TRUE);
        $pdf->Cell(43, 4, $DETAIL->NAMA_SISWA, 1, 0, 'L', TRUE);
        $pdf->Cell(7, 4, $DETAIL->POIN_TAHUN_LALU_KSH, 1, 0, 'C', TRUE);
        $pdf->Cell(7, 4, $DETAIL->TOTAL_LARI, 1, 0, 'C', TRUE);
        $pdf->Cell(20, 4, $DETAIL->NAMA_KJT, 1, 0, 'L', TRUE);
        $pdf->Cell(65 / 12, 4, $DETAIL->B07, 1, 0, 'C', TRUE);
        $pdf->Cell(65 / 12, 4, $DETAIL->B08, 1, 0, 'C', TRUE);
        $pdf->Cell(65 / 12, 4, $DETAIL->B09, 1, 0, 'C', TRUE);
        $pdf->Cell(65 / 12, 4, $DETAIL->B10, 1, 0, 'C', TRUE);
        $pdf->Cell(65 / 12, 4, $DETAIL->B11, 1, 0, 'C', TRUE);
        $pdf->Cell(65 / 12, 4, $DETAIL->B12, 1, 0, 'C', TRUE);
        $pdf->Cell(65 / 12, 4, $DETAIL->B01, 1, 0, 'C', TRUE);
        $pdf->Cell(65 / 12, 4, $DETAIL->B02, 1, 0, 'C', TRUE);
        $pdf->Cell(65 / 12, 4, $DETAIL->B03, 1, 0, 'C', TRUE);
        $pdf->Cell(65 / 12, 4, $DETAIL->B04, 1, 0, 'C', TRUE);
        $pdf->Cell(65 / 12, 4, $DETAIL->B05, 1, 0, 'C', TRUE);
        $pdf->Cell(65 / 12, 4, $DETAIL->B06, 1, 0, 'C', TRUE);
        $pdf->Cell(6, 4, $DETAIL->CAWU_1 == 0 ? '' : $DETAIL->CAWU_1, 1, 0, 'C', TRUE);
        $pdf->Cell(6, 4, $DETAIL->CAWU_2 == 0 ? '' : $DETAIL->CAWU_2, 1, 0, 'C', TRUE);
        $pdf->Cell(6, 4, $DETAIL->CAWU_3 == 0 ? '' : $DETAIL->CAWU_3, 1, 0, 'C', TRUE);
        $jumlah = $DETAIL->CAWU_1 + $DETAIL->CAWU_2 + $DETAIL->CAWU_3;
        $pdf->Cell(7, 4, $jumlah == 0 ? '' : $jumlah, 1, 0, 'C', TRUE);
        $pdf->Ln();

        $jumlah_siswa++;
    }
    $pdf->Cell(0, 4, 'NB.');
    $pdf->Ln();

    $pdf->Cell(5);
    $pdf->Cell(0, 4, 'Data dicetak tanggal: ' . $this->date_format->to_print_text(date('Y-m-d')));
    $pdf->Ln();

    $pdf->Cell(5);
    $pdf->Cell(0, 4, 'Data pelanggaran terkahir: ' . $this->date_format->to_print_text($TANGGAL));
    $pdf->Ln();

    $pdf->Cell(5);
    $pdf->Cell(0, 4, 'Jumlah SP: ' . $jumlah_sp);
    $pdf->Ln();

    $pdf->Cell(5);
    $pdf->Cell(0, 4, 'Jumlah PO1: ' . $jumlah_po_1);
    $pdf->Ln();

    $pdf->Cell(5);
    $pdf->Cell(0, 4, 'Jumlah PO2: ' . $jumlah_po_2);
    $pdf->Ln();

    $pdf->Cell(5);
    $pdf->Cell(0, 4, 'Jumlah Ta\'liq: ' . $jumlah_takliq);
    $pdf->Ln();

    $pdf->Cell(5);
    $pdf->Cell(0, 4, 'Jumlah Luar Batas: ' . $jumlah_mutasi);
    $pdf->Ln();

    $pdf->Cell(5);
    $pdf->Cell(0, 4, 'Jumlah Siswa: ' . $jumlah_siswa);
    $pdf->Ln();
}
$pdf->Output();
