<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 * 
  $this->CoreFonts = array('courier', 'helvetica', 'times', 'symbol', 'zapfdingbats');
 * 
 */

if(!isset($KETUA->NAMA_PEG)) {
    echo "<h1>TENTUKAN TERLEBIH DAHULU KETUA PU DI MASTER DATA -> APLIKASI</h1>";
    exit();
}

$pdf = $this->fpdf;
$margin = 25;

if (count($SISWA) == 0) {
    echo "<h1>TIDAK ADA DATA YANG DITAMPILKAN</h1>";
    exit();
}

foreach ($SISWA as $DETAIL) {
    $pdf->AddPage("P", "A5");
    $pdf->SetAutoPageBreak(true, 0);

    $pdf->Image(base_url($this->pengaturan->getLogo()), 13, 10, 21, 22, '', '');

    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell($margin);
    $pdf->Cell(0, 7, strtoupper($this->pengaturan->getNamaLembaga()), 0, 0, 'C');
    $pdf->Ln();

    // $pdf->SetFont('Arial', '', 10);
    // $pdf->Cell($margin);
    // $pdf->Cell(0, 5, 'PANITIA UJIAN', 0, 0, 'C');
    // $pdf->Ln();

    $pdf->Cell($margin);
    $pdf->Cell(0, 7, 'PENERIMAAN MURID BARU', 0, 0, 'C');
    $pdf->Ln();

    $pdf->Cell($margin);
    $pdf->Cell(0, 7, 'TAHUN AJARAN '.$this->session->userdata('NAMA_PSB_ACTIVE'), 0, 0, 'C');
    $pdf->Ln(15);

    $pdf->SetLineWidth(0.50);
    $pdf->Line(37, 31, 135, 31);
    $pdf->SetLineWidth(0.30);
    $pdf->Line(37, 32, 135, 32);

    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 5, 'SURAT KETERANGAN', 0, 0, 'C');
    $pdf->Ln(10);

    $pdf->SetFont('Arial', '', 10);
    $pdf->MultiCell(0, 5, 'Berdasarkan hasil Ujian Penerimaan Murid Baru '.$this->pengaturan->getNamaLembaga().' yang diselenggarakan pada tanggal '.$this->date_format->to_print_text($TANGGAL).', Direktur '.$this->pengaturan->getNamaLembaga().' menerangkan bahwa calon siswa: ');
    $pdf->Ln(3);

    $pdf->Cell(20, 5, 'Nama', 0, 0, 'L');
    $pdf->Cell(0, 5, ': '.$DETAIL->NAMA_SISWA, 0, 0, 'L');
    $pdf->Ln();
    
    $pdf->Cell(20, 5, 'No. Ujian', 0, 0, 'L');
    $pdf->Cell(0, 5, ': '.$this->pengaturan->getKodeUM($DETAIL), 0, 0, 'L');
    $pdf->Ln(8);

    $pdf->MultiCell(0, 5, 'dinyatakan diterima di kelas '.$DETAIL->NAMA_TINGK_NOW.' '.str_replace("MADRASAH", "", $DETAIL->NAMA_DEPT_NOW).' '.$this->pengaturan->getNamaLembaga().' pada Tahun Ajaran '.$this->session->userdata('NAMA_PSB_ACTIVE').'.');
    $pdf->Ln(3);

    $pdf->MultiCell(0, 5, 'Demikian pihak-pihak yang berkepentingan diharap maklum.');
    $pdf->Ln();

    $pdf->Cell(70);
    $pdf->Cell(0, 5, $this->pengaturan->getDesa().', '.$this->date_format->to_print_text(date('d-m-Y')), 0, 0, 'L');
    $pdf->Ln();

    $pdf->Cell(70);
    $pdf->Cell(0, 5, 'A.n. Direktur', 0, 0, 'L');
    $pdf->Ln();

    $pdf->Cell(70);
    $pdf->Cell(0, 5, 'Pembantu Direktur', 0, 0, 'L');
    $pdf->Ln();

    $pdf->Cell(70);
    $pdf->Cell(0, 5, 'Bidang Pendidikan dan Kurikulum', 0, 0, 'L');
    $pdf->Ln();

    $pdf->SetLineWidth(0.50);
    $pdf->Line(22, 117, 48, 117); // TOP
    $pdf->Line(22, 145, 48, 145); // BOTTOM
    $pdf->Line(22, 117, 22, 145); // LEFT
    $pdf->Line(48, 117, 48, 145); // RIGHT

    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(50, 5, $DETAIL->NAMA_TINGK_NOW, 0, 0, 'C');
    $pdf->Ln(8);

    $pdf->Cell(50, 5, $DETAIL->DEPT_TINGK_NOW, 0, 0, 'C');
    $pdf->Ln(7);

    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(70);
    $pdf->Cell(0, 5, 'H. Ah. Nadhif, Lc.', 0, 0, 'L');
    // $pdf->Cell(0, 5, ($KETUA->GELAR_AWAL_PEG == NULL ? '' : $KETUA->GELAR_AWAL_PEG.'. ').$KETUA->NAMA_PEG.($KETUA->GELAR_AKHIR_PEG == NULL ? '' : '. '.$KETUA->GELAR_AKHIR_PEG), 0, 0, 'L');
    $pdf->Ln(14);

    $pdf->Image(base_url('files/aplikasi/ttd_gus_nadhif.png'), 80, 124, 45);

    $pdf->SetFont('Arial', 'I', 9);
    $pdf->MultiCell(0, 5, 'Surat ini diserahkan kepada Wali Kelas '.$DETAIL->NAMA_TINGK_NOW.' '.str_replace("MADRASAH", "", $DETAIL->NAMA_DEPT_NOW).' selambat-lambatnya pada tanggal '.$this->date_format->to_print_text($MILADIYAH).' / '.$HIJRIYAH.' bersama dengan kwitansi berstempel LUNAS.');
    $pdf->Ln();

    // break;
}

$pdf->Output();