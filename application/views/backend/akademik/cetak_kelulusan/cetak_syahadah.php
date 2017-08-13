<?php

$jenjang_arab = array(
    'MI' => 'الإِبْتِدَائِيّةِ',
    'TS' => 'الثّانَوِيّةِ',
    'AL' => 'العَالِيّةِ',
    'DU' => 'الدّيْنِيّةِ الأُلَى',
    'DW' => 'الدّيْنِيّةِ الوُسْطَى',
);

$cpdf = $this->tcpdf;

//$fontname = $cpdf->addTTFfont('files/andalus/andiso.ttf', 'TrueTypeUnicode', '', 32);

foreach ($data as $detail) {
    $siswa = $detail['DATA'];
    $nilai = $detail['NILAI'];

    $cpdf->SetPrintHeader(false);
    $cpdf->SetPrintFooter(false);

    $cpdf->SetMargins(PDF_MARGIN_LEFT + 7, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT + 10);

    $cpdf->AddPage("P", 'A5');
    $cpdf->SetAutoPageBreak(true, 0);

    $cpdf->Image(base_url('files/aplikasi/background_syahadah.png'), 11, 11);
    $cpdf->Image(base_url('files/aplikasi/pim_arab_black.png'), 55, 40, 40);
    $cpdf->Image(base_url('files/aplikasi/syahadah.png'), 62, 60, 23);

    $cpdf->SetLineWidth(0.30);
    $cpdf->Line(22, 151, 60, 151);
    
    $cpdf->Line(80, 153, 100, 153);
    $cpdf->Line(80, 178, 100, 178);
    $cpdf->Line(80, 153, 80, 178);
    $cpdf->Line(100, 153, 100, 178);
//    $cpdf->Line(80, 153, 100, 178);
//    $cpdf->Line(80, 178, 100, 153);

    $cpdf->SetY(22);
    $cpdf->setRTL(true);

    $cpdf->SetFont('aefurat', '', 12);

    $cpdf->Cell(65, 7, 'الرّقْمُ : ' . $this->translasi_handler->proses($siswa->NOMOR_SYAHADAH_NIS), 0, 0, 'R');
    $cpdf->Cell(20, 7, 'رَقْمُ القَيْدِ', 0, 0, 'L');
    $cpdf->Cell(0, 7, ':', 0, 0, 'R');
    $cpdf->Ln(13);

    $cpdf->SetFont('andalus', 'B', 14);
    $cpdf->Cell(0, 7, '‬المدرسة الدينية الﻷولى', 0, 0, 'C');
    $cpdf->Ln(17);

    $cpdf->SetFont('aefurat', '', 12);

    $cpdf->Cell(0, 7, '‬حاجين مرقاياصا فاطى', 0, 0, 'C');
    $cpdf->Ln(25);

    $cpdf->Cell(25);
    $cpdf->Cell(25, 7, 'مُتِحَتْ للطّالِبِ', 0, 0, 'R');
    $cpdf->Cell(0, 7, ': ' . $this->translasi_handler->proses($siswa->NAMA_SISWA), 0, 0, 'R');
    $cpdf->Ln();

    $cpdf->Cell(25);
    $cpdf->Cell(25, 7, 'المَوْلُوْد', 0, 0, 'R');
    $cpdf->Cell(0, 7, ': ' . $this->translasi_handler->proses($siswa->TEMPAT_LAHIR_SISWA), 0, 0, 'R');
    $cpdf->Ln();

    $cpdf->Cell(25);
    $cpdf->Cell(25, 7, 'تَارِيْح المِيْلَاد', 0, 0, 'R');
    $cpdf->Cell(0, 7, ': ' . $this->translasi_handler->proses($this->date_format->to_print_text($siswa->TANGGAL_LAHIR_SISWA)), 0, 0, 'R');
    $cpdf->Ln();

    $cpdf->Cell(25);
    $cpdf->Cell(25, 7, 'الوَالِد', 0, 0, 'R');
    $cpdf->Cell(0, 7, ': ' . $this->translasi_handler->proses($siswa->AYAH_NAMA_SISWA), 0, 0, 'R');
    $cpdf->Ln(10);

    $cpdf->Cell(25, 7, 'STATUS', 0, 0, 'R');
    $cpdf->Ln(10);

    $cpdf->WriteHTML('فِي الإِخْتِبَارِ النِهَانِي لِلْمَدْرَسَةِ ' . $jenjang_arab[$siswa->ID_DEPT] . ' مَطَالِعُ الفَلاحِ حَاجَيْن , المُعَقّدِ مِنَ التّارِيْخِ ' . $this->translasi_handler->proses($this->date_format->to_print_text($siswa->TANGGAL_MULAI_UJIAN)) . ' إلَى التّارِيْخِ ' . $this->translasi_handler->proses($this->date_format->to_print_text($siswa->TANGGAL_SELESAI_UJIAN)) . ' م.', true, 0, true, 0);
    $cpdf->Ln(3);

    $cpdf->WriteHTML('وَ اللّه يَنْفَعُهُ بِعُلُمِهِ وَ  يُوَفّقُهُ لِمَا فِيْهِ رِضَاهُ.', true, 0, true, 0);
    $cpdf->Ln(5);

    $cpdf->SetY(145);

    $cpdf->Cell(50);
    $cpdf->Cell(13, 10, 'حَاجَيْن: ');
    $cpdf->Cell(0, 5, $this->translasi_handler->proses($post['TANGGAL_HIJRIYAH']) . ' ه', 0, 0, 'R');
    $cpdf->Ln();

    $cpdf->Cell(63);
    $cpdf->Cell(0, 5, $this->translasi_handler->proses($post['TANGGAL_MASEHI']) . ' م', 0, 0, 'R');
    $cpdf->Ln(9);

    $cpdf->Cell(50);
    $cpdf->Cell(0, 5, 'المُدِيْر', 0, 0, 'C');
    $cpdf->Ln(17);

    $cpdf->Cell(50);
    $cpdf->Cell(0, 5, $this->translasi_handler->proses($post['DIREKTUR']), 0, 0, 'C');
    $cpdf->Ln();

    $cpdf->setRTL(false);   
    

    $cpdf->SetPrintHeader(false);
    $cpdf->SetPrintFooter(false);

    $cpdf->SetMargins(PDF_MARGIN_LEFT + 7, PDF_MARGIN_TOP - 15, PDF_MARGIN_RIGHT + 7);

    $cpdf->AddPage("P", 'A5');
    $cpdf->SetAutoPageBreak(true, 0);
    
    $cpdf->setRTL(true);
    
    $cpdf->SetFont('aefurat', '', 14);
    $cpdf->Cell(0, 7, 'قَائِمَةُ كَشْفِ الدّرَجَاتِ فِى الإِخْتِبَارِ النّهَانِي', 0, 0, 'C');
    $cpdf->Ln();
    
    $cpdf->SetFont('aefurat', '', 12);
    $cpdf->Cell(0, 7, 'لِلْعَام الدّرَاسِي : '.$this->translasi_handler->proses($siswa->NAMA_TA).' م', 0, 0, 'C');
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
}

$cpdf->Output();
?>