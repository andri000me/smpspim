<?php

$cpdf = $this->tcpdf;

$cpdf->SetPrintHeader(false);
$cpdf->SetPrintFooter(false);

foreach ($data as $detail) {
    $siswa = $detail['DATA'];
    if ($siswa->ID_DEPT == 'DU' || $siswa->ID_DEPT == 'DW') {
        $nilai = $detail['NILAI_ARAB'];
        $cpdf->SetMargins(PDF_MARGIN_LEFT + 7, PDF_MARGIN_TOP - 15, PDF_MARGIN_RIGHT + 7);

        $cpdf->AddPage("P", 'A5');
        $cpdf->SetAutoPageBreak(true, 0);

        $cpdf->setRTL(true);

        $cpdf->SetFont('aefurat', '', 14);
        $cpdf->Cell(0, 7, 'قَائِمَةُ كَشْفِ الدّرَجَاتِ فِى الإِخْتِبَارِ النّهَانِي', 0, 0, 'C');
        $cpdf->Ln();

        $cpdf->SetFont('aefurat', '', 12);
        $cpdf->Cell(0, 7, 'لِلْعَام الدّرَاسِي : ' . $this->translasi_handler->proses($siswa->NAMA_TA) . ' م', 0, 0, 'C');
        $cpdf->Ln(10);

        $cpdf->Cell(15, 12, 'الرّقْمُ', 1, 0, 'C');
        $cpdf->Cell(50, 12, 'المَوَادّ الدّرَسِيّةُ', 1, 0, 'C');
        $cpdf->Cell(40, 6, 'الدّرَجَاتُ', 1, 0, 'C');
        $cpdf->Ln();

        $cpdf->Cell(65);
        $cpdf->Cell(15, 6, 'رُقْما', 1, 0, 'C');
        $cpdf->Cell(25, 6, 'كِتَابَة', 1, 0, 'C');
        $cpdf->Ln();

        $no = 1;
        foreach ($nilai as $detail_nilai) {
            $cpdf->Cell(15, 6, $this->translasi_handler->proses($no++), 1, 0, 'C');
            $cpdf->Cell(50, 6, $detail_nilai->NAMA_ARAB_MAPEL, 1);
            $cpdf->Cell(15, 6, $this->translasi_handler->proses($detail_nilai->NILAI_SISWA), 1, 0, 'C');
            $cpdf->Cell(25, 6, $this->translasi_handler->terbilang($detail_nilai->NILAI_SISWA), 1);
            $cpdf->Ln();
        }

        $offset = 0;

        $cpdf->SetLineWidth(0.30);
        $cpdf->Line(22, 161 + $offset, 60, 161 + $offset);

        $cpdf->SetY(155 + $offset);

        $cpdf->Cell(50);
        $cpdf->Cell(15, 10, 'حَاجَيْن: ');
        $cpdf->Cell(0, 5, $this->translasi_handler->proses($post['TANGGAL_HIJRIYAH']) . ' ه', 0, 0, 'R');
        $cpdf->Ln();

        $cpdf->Cell(65);
        $cpdf->Cell(0, 5, $this->translasi_handler->proses($post['TANGGAL_MASEHI']) . ' م', 0, 0, 'R');
        $cpdf->Ln(9);

        $cpdf->Cell(50);
        $cpdf->Cell(0, 5, 'المُدِيْر', 0, 0, 'C');
        $cpdf->Ln(17);

        $cpdf->Cell(50);
        $cpdf->Cell(0, 5, $this->translasi_handler->proses($post['DIREKTUR']), 0, 0, 'C');
        $cpdf->Ln();

        $cpdf->setRTL(false);
    } else {
        $nilai = $detail['NILAI_LATIN'];

        $offset_y = 50;


        $cpdf->SetMargins(20, 20);
        $cpdf->AddPage("P", "A4");
        $cpdf->SetAutoPageBreak(true, 0);

        $cpdf->SetY($offset_y);

        $cpdf->SetFont('helvetica', '', 12);
        $cpdf->Cell(0, 8, 'TRANSKRIP NILAI UJIAN AKHIR', 0, 0, 'C');
        $cpdf->Ln();

        $cpdf->SetFont('helvetica', 'B', 12);
        $cpdf->Cell(0, 8, $siswa->NAMA_DEPT, 0, 0, 'C');
        $cpdf->Ln();

        $cpdf->SetFont('helvetica', '', 12);
        $cpdf->Cell(0, 8, 'TAHUN AJARAN ' . $siswa->NAMA_TA, 0, 0, 'C');
        $cpdf->Ln(10);

        $cpdf->SetFont('helvetica', '', 10);
        $cpdf->Cell(30, 7, 'Nama ');
        $cpdf->Cell(85, 7, ': ' . $siswa->NAMA_SISWA);
        $cpdf->Cell(25, 7, 'NIS');
        $cpdf->Cell(80, 7, ': ' . $siswa->NIS_NIS);
        $cpdf->Ln();

        $cpdf->Cell(30, 7, 'Tempat, tgl. lahir');
        $cpdf->Cell(85, 7, ': ' . $siswa->TEMPAT_LAHIR_SISWA . ', ' . $this->date_format->to_print_text($siswa->TANGGAL_LAHIR_SISWA));
        $cpdf->Cell(25, 7, 'No. Ijasah');
        $cpdf->Cell(80, 7, ': ' . $siswa->NOMOR_IJASAH_NIS);
        $cpdf->Ln(8);

        $cpdf->Cell(10, 10, 'No.', 1, 0, 'C');
        $cpdf->Cell(90, 10, 'MATA PELAJARAN', 1, 0, 'C');
        $cpdf->Cell(70, 5, 'NILAI', 1, 0, 'C');
        $cpdf->Ln();

        $cpdf->Cell(100);
        $cpdf->Cell(20, 5, 'Angka', 1, 0, 'C');
        $cpdf->Cell(50, 5, 'Huruf', 1, 0, 'C');
        $cpdf->Ln();

        $no = 1;
        $total = 0;
        foreach ($nilai as $detail_nilai) {
            $total += $detail_nilai->NILAI_SISWA;
            $cpdf->Cell(10, 5, $no++, 1, 0, 'C');
            $cpdf->Cell(90, 5, $detail_nilai->NAMA_MAPEL, 1);
            $cpdf->Cell(20, 5, $detail_nilai->NILAI_SISWA, 1, 0, 'C');
            $cpdf->Cell(50, 5, $this->money->terbilang_simpel($detail_nilai->NILAI_SISWA), 1);
            $cpdf->Ln();
        }

        $cpdf->Cell(10, 5, '', 1, 0, 'C');
        $cpdf->Cell(90, 5, 'Jumlah', 1, 0, 'C');
        $cpdf->Cell(20, 5, $total, 1, 0, 'C');
        $cpdf->Cell(50, 5, $this->money->terbilang_simpel($total), 1);
        $cpdf->Ln();

        $rata_rata = number_format($total / ($no - 1), 2, '.', '.');
        $cpdf->Cell(10, 5, '', 1, 0, 'C');
        $cpdf->Cell(90, 5, 'Rata-rata', 1, 0, 'C');
        $cpdf->Cell(20, 5, $rata_rata, 1, 0, 'C');
        $cpdf->Cell(50, 5, $this->money->terbilang_simpel($rata_rata), 1);
        $cpdf->Ln(15);

        $offset = 40;

        $cpdf->Cell(150 - $offset);
        $cpdf->Cell(15, 10, 'Kajen, ');
        $cpdf->SetFont('helvetica', 'U', 10);
        $cpdf->Cell(0, 5, $post['TANGGAL_HIJRIYAH'] . ' H.');
        $cpdf->Ln();
        $cpdf->SetFont('helvetica', '', 10);
        $cpdf->Cell(150 + 15 - $offset);
        $cpdf->Cell(0, 5, $post['TANGGAL_MASEHI'] . ' M.');
        $cpdf->Ln(8);

        $cpdf->Cell(170 - $offset);
        $cpdf->Cell(0, 5, 'Direktur,');
        $cpdf->Ln(20);

        $cpdf->Cell(148 - $offset);
        $cpdf->Cell(0, 5, $post['DIREKTUR']);
    }
}

$cpdf->Output();
?>