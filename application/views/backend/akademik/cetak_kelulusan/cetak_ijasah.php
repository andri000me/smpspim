<?php

$jenjang_arab = array(
    'MI' => 'الإِبْتِدَائِيّةِ',
    'TS' => 'الثّانَوِيّةِ',
    'AL' => 'العَالِيّةِ',
    'DU' => 'الدّيْنِيّةِ الأُلَى',
    'DW' => 'الدّيْنِيّةِ الوُسْطَى',
);

$cpdf = $this->tcpdf;

$cpdf->SetPrintHeader(false);
$cpdf->SetPrintFooter(false);

foreach ($data as $siswa) {
    if ($siswa->ID_DEPT == 'DU' || $siswa->ID_DEPT == 'DW') {
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

        $cpdf->SetFont('aefurat', '', 14);
        $cpdf->Cell(0, 7, $siswa->JK_SISWA == 'L' ? 'نجح' : 'نجحت', 0, 0, 'C');
        $cpdf->Ln(10);

        $cpdf->SetFont('aefurat', '', 12);
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
        
    } else {

        $offset_y = 65;

//        $cpdf->SetMargins(55, 65);
        $cpdf->AddPage("L", "A4");
        $cpdf->SetAutoPageBreak(true, 0);

        $cpdf->SetY(18);
        $cpdf->SetX(30);
        $cpdf->SetFont('helvetica', '', 12);
        $cpdf->Cell(30, 7, 'Nomor');
        $cpdf->Cell(0, 7, ': ' . $siswa->NOMOR_IJASAH_NIS);
        $cpdf->Ln();

        $cpdf->SetX(30);
        $cpdf->Cell(30, 7, 'Tahun Ajaran');
        $cpdf->Cell(0, 7, ': ' . $siswa->NAMA_TA);
        $cpdf->Ln();

        $cpdf->SetY($offset_y);

        $cpdf->SetFont('helvetica', 'B', 14);
        $cpdf->Cell(0, 7, $siswa->NAMA_DEPT, 0, 0, 'C');
        $cpdf->Ln(10);

        $cpdf->SetFont('helvetica', '', 12);
        $cpdf->Cell(0, 7, 'Diberikan kepada:', 0, 0, 'C');
        $cpdf->Ln(10);

        $cpdf->SetFont('franklin', 'B', 17);
        $cpdf->Cell(0, 7, strtoupper($siswa->NAMA_SISWA), 0, 0, 'C');
        $cpdf->Ln(8);

        $cpdf->SetFont('helvetica', '', 12);

        $cpdf->Cell(100);
        $cpdf->Cell(30, 7, 'Lahir di');
        $cpdf->Cell(0, 7, ': ' . $siswa->TEMPAT_LAHIR_SISWA);
        $cpdf->Ln();

        $cpdf->Cell(100);
        $cpdf->Cell(30, 7, 'Pada tanggal');
        $cpdf->Cell(0, 7, ': ' . $this->date_format->to_print_text($siswa->TANGGAL_LAHIR_SISWA));
        $cpdf->Ln();

        $cpdf->Cell(100);
        $cpdf->Cell(30, 7, 'Anak');
        $cpdf->Cell(0, 7, ': ' . $siswa->AYAH_NAMA_SISWA);
        $cpdf->Ln();

        $cpdf->Cell(100);
        $cpdf->Cell(30, 7, 'NIS');
        $cpdf->Cell(0, 7, ': ' . $siswa->NIS_NIS);
        $cpdf->Ln(8);

        $cpdf->SetFont('helvetica', 'B', 12);
        $cpdf->Cell(0, 7, 'LULUS', 0, 0, 'C');
        $cpdf->Ln(8);

        $cpdf->SetFont('helvetica', '', 12);
        $cpdf->SetX(55);
        $cpdf->MultiCell(180, 5, 'Pada Ujian Akhir ' . $siswa->NAMA_DEPT . ' Perguruan Islam MATHALI\'UL FALAH Kajen, yang ');

        $cpdf->SetX(55);
        $cpdf->Cell(220, 7, 'diselenggarakan mulai tanggal ' . $this->date_format->to_print_text($siswa->TANGGAL_MULAI_UJIAN) . ' sampai dengan ' . $this->date_format->to_print_text($siswa->TANGGAL_SELESAI_UJIAN) . '.');
        $cpdf->Ln(9);

        $cpdf->SetX(55);
        $cpdf->Cell(220, 7, 'Semoga ilmu yang diperoleh bermanfaat dan barokah. Amin.');
        $cpdf->Ln(10);

        $cpdf->SetX(174);
        $cpdf->Cell(15, 10, 'Kajen, ');        
        
        $cpdf->SetX(187);
        $cpdf->SetFont('helvetica', '', 12);
        $cpdf->Cell(0, 5, $post['TANGGAL_HIJRIYAH'] . ' H.');
        $cpdf->Ln();
        
        $cpdf->SetX(187);
        $cpdf->SetFont('helvetica', '', 12);
        $cpdf->Cell(0, 5, $post['TANGGAL_MASEHI'] . ' M.');
        $cpdf->Ln(8);

        $cpdf->SetX(193);
        $cpdf->Cell(0, 5, 'Direktur,');
        $cpdf->Ln(20);

        $cpdf->SetFont('helvetica', 'B', 12);
        $cpdf->Cell(148 + 10);
        $cpdf->Cell(0, 5, $post['DIREKTUR']);

        $cpdf->SetLineWidth(0.30);

        $cpdf->Line(187, 159.5, 232, 159.5);
        
        $cpdf->Line(135, $offset_y + 95, 160, $offset_y + 95);
        $cpdf->Line(135, $offset_y + 120, 160, $offset_y + 120);
        $cpdf->Line(135, $offset_y + 95, 135, $offset_y + 120);
        $cpdf->Line(160, $offset_y + 95, 160, $offset_y + 120);
    }
}

$cpdf->Output();
?>