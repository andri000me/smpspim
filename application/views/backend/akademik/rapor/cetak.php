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

foreach ($DATA as $DETAIL) {
    if($DETAIL['SISWA']->NAIK_AS != NULL && $CAWU == 3) continue;
    
    $pdf->AddPage("P", "A4");
//	$pdf->SetMargins(6, 0);
    $pdf->SetAutoPageBreak(true, 0);

    $pdf = $this->cetak->header_yayasan($pdf);

    $pdf->Ln(2);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 5, 'LAPORAN HASIL BELAJAR SISWA', 0, 0, 'C');
    $pdf->Ln(10);

    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(25, 5, 'Nama Siswa');
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(110, 5, ': ' . $DETAIL['SISWA']->NIS_SISWA . ' - ' . $DETAIL['SISWA']->NAMA_SISWA);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(25, 5, 'Catur Wulan');
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(0, 5, ': ' . $CAWU);
    $pdf->Ln();

    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(25, 5, 'Kelas');
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(110, 5, ': ' . $DETAIL['SISWA']->NAMA_KELAS);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(25, 5, 'Tahun Ajaran');
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(0, 5, ': ' . $TA);
    $pdf->Ln();

    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(10, 10, 'No', 1, 0, 'C');
    $pdf->Cell(100, 10, 'Mata Pelajaran', 1, 0, 'C');
    $pdf->Cell(80, 5, 'Nilai', 1, 0, 'C');
    $pdf->Ln();

    $pdf->SetY(63);
    $pdf->Cell(110);
    $pdf->Cell(20, 5, 'Angka', 1, 0, 'C');
    $pdf->Cell(60, 5, 'Huruf', 1, 0, 'C');
    $pdf->Ln();

    $pdf->SetFont('Arial', '', 10);

    $no = 1;
    $NILAI = 0;
    foreach ($DETAIL['NILAI'] as $nilai) {
        $NILAI += $nilai->NILAI_SISWA;
        $pdf->Cell(10, 5, $no++, 1, 0, 'C');
        $pdf->Cell(100, 5, $nilai->NAMA_MAPEL, 1, 0, 'L');
        $pdf->Cell(20, 5, $nilai->NILAI_SISWA, 1, 0, 'C');
//        $pdf->Cell(60, 5, $this->money->terbilang($nilai->NILAI_SISWA), 1, 0, 'L');
        $pdf->Cell(60, 5, '-', 1, 0, 'L');
        $pdf->Ln();
    }
    
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(110, 5, 'Rata-rata', 1, 0, 'L');
    $pdf->Cell(20, 5, number_format(($NILAI/($no - 1)), 2, ',', '.'), 'LBT', 0, 'C');
    $pdf->Cell(60, 5, '', 'TBR', 0, 'L');
    $pdf->Ln();
    
    $pdf->SetFont('Arial', '', 10);
    
//    for ($no = $no; $no < 28; $no++) {
//        $pdf->Cell(10, 5, $no, 1, 0, 'C');
//        $pdf->Cell(100, 5, '-', 1, 0, 'L');
//        $pdf->Cell(20, 5, '-', 1, 0, 'C');
//        $pdf->Cell(60, 5, '-', 1, 0, 'L');
//        $pdf->Ln();
//    } 
    $pdf->Ln(3);
    
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(50, 5, 'Ketidakhadiran', 1, 0, 'C');
    $pdf->Cell(20, 5, 'Jumlah', 1, 0, 'C');
    $pdf->Cell(5);
    $pdf->Cell(115, 5, 'Catatan: ', 'TRL');
    $pdf->Ln();
    
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(50, 5, 'Sakit', 1, 0, 'L');
    $pdf->Cell(20, 5, $DETAIL['ABSEN']['SAKIT'], 1, 0, 'C');
    $pdf->Cell(5);
    $pdf->Cell(115, 5, '', 'RL');
    $pdf->Ln();
    
    $pdf->Cell(50, 5, 'Izin', 1, 0, 'L');
    $pdf->Cell(20, 5, $DETAIL['ABSEN']['IZIN'], 1, 0, 'C');
    $pdf->Cell(5);
    $pdf->Cell(115, 5, '', 'RL');
    $pdf->Ln();
    
    $pdf->Cell(50, 5, 'Lari', 1, 0, 'L');
    $pdf->Cell(20, 5, $DETAIL['ABSEN']['ALPHA'], 1, 0, 'C');
    $pdf->Cell(5);
    $pdf->Cell(115, 5, '', 'RL');
    $pdf->Ln();
    
    $pdf->Cell(20, 5, 'STATUS', 1, 0, 'L');
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(50, 5, ($DETAIL['SISWA']->NAIK_AS ? 'NAIK KELAS' : ($DETAIL['SISWA']->NAIK_AS == NULL ? 'BELUM DITENTUKAN' : 'TIDAK NAIK KELAS')), 1, 0, 'C');
    $pdf->Cell(5);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(115, 5, '', 'BRL');
    $pdf->Ln(13);
    
    $pdf->Cell(130);
    $pdf->Cell(0, 5, $this->pengaturan->getDesa().', '.$this->date_format->to_print_text(date('d-m-Y')));
    $pdf->Ln();
    
    $pdf->Cell(130, 5, 'Orang Tua,');
    $pdf->Cell(0, 5, 'Wali Kelas,');
    $pdf->Ln(18);
    
    $pdf->SetFont('Arial', 'U', 10);
    $pdf->Cell(130, 5, '(                            )');
    $pdf->Cell(0, 5, $this->cetak->nama_peg_print($DETAIL['SISWA']));
    $pdf->Ln();
    
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(130);
    $pdf->Cell(0, 5, 'NIP. '.$DETAIL['SISWA']->NIP_PEG);
    $pdf->Ln();

}

$pdf->Output();
