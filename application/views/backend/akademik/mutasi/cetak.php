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

$alasan = array(
    1 => 'atas permintaan sendiri dan orang tua karena pindah sekolah',
    2 => '',
    3 => 'atas permintaan sendiri dan orang tua (mengundurkan diri)',
    4 => 'karena meninggal dunia',
    5 => 'atas permintaan sendiri dan orang tua karena pindah tempat tinggal',
    98 => 'atas permintaan sendiri dan orang tua karena pindah sekolah',
    99 => 'karena telah lulus',
);

    $pdf->SetLeftMargin(25);
    $pdf->SetRightMargin(25);
    $pdf->SetTopMargin(45);
    $pdf->AddPage("P", $this->pengaturan->getUkuranF4());
    $pdf->SetAutoPageBreak(true, 0);

    $pdf->Ln(7);
    $pdf->SetFont('Times', 'BU', 16);
    $pdf->Cell(0, 7, 'SURAT KETERANGAN PINDAH', 0, 0, 'C');
    $pdf->Ln();

    $pdf->SetFont('Times', '', 12);
    $pdf->Cell(0, 7, 'Nomor: KM/      /A-II/PIM/' . (date('Y') - $this->pengaturan->getTahunBerdiri()) . '/' . $this->date_format->toRomawi(date('n')) . '/' . date('Y'), 0, 0, 'C');
    $pdf->Ln(10);

    $pdf->MultiCell(0, 7, 'Direktur Perguruan Islam Mathali\'ul Falah Kajen Margoyoso Pati, menerangkan dengan sesungguhnya bahwa :');
    $pdf->Ln();

    $pdf->Cell(20);
    $pdf->Cell(20, 7, 'No. Induk');
    $pdf->Cell(0, 7, ': ' . $siswa->NIS_NIS);
    $pdf->Ln();

    $pdf->Cell(20);
    $pdf->Cell(20, 7, 'Nama');
    $pdf->Cell(0, 7, ': ' . $siswa->NAMA_SISWA);
    $pdf->Ln();

    $pdf->Cell(20);
    $pdf->Cell(20, 7, 'TTL');
    $pdf->Cell(0, 7, ': ' . $siswa->TEMPAT_LAHIR_SISWA . ', ' . $this->date_format->to_print_text($siswa->TANGGAL_LAHIR_SISWA));
    $pdf->Ln();

    $pdf->Cell(20);
    $pdf->Cell(20, 7, 'Orang tua');
    $pdf->Cell(0, 7, ': ' . $siswa->AYAH_NAMA_SISWA);
    $pdf->Ln();

    $pdf->Cell(20);
    $pdf->Cell(20, 7, 'Alamat');
    $pdf->Cell(0, 7, ': ' . $siswa->ALAMAT_SISWA);
    $pdf->Ln();

    $pdf->Cell(42);
    $pdf->Cell(0, 7, 'Kec. ' . $siswa->NAMA_KEC . ', ' . $siswa->NAMA_KAB . ', ' . $siswa->NAMA_PROV);
    $pdf->Ln();

    $pdf->Cell(20);
    $pdf->Cell(20, 7, 'Kelas');
    $pdf->Cell(0, 7, ': ' . ($siswa->NAMA_KELAS == NULL ? '-' : $siswa->NAMA_KELAS));
    $pdf->Ln(10);

    $pdf->MultiCell(0, 7, 'adalah benar-benar belajar ' . ($siswa->NAMA_TINGK == NULL ? 'pelajar di' : ('di kelas ' . $siswa->NAMA_TINGK . ' (' . trim($this->money->terbilang($siswa->NAMA_TINGK)) . ') ' . ucwords(strtolower($siswa->NAMA_DEPT)))) . ' Perguruan Islam Mathali\'ul Falah Kajen Margoyoso Pati Jawa Tengah pada tahun ajaran ' . $this->session->userdata('NAMA_TA_ACTIVE') . '.');
    $pdf->Ln();

    $pdf->MultiCell(0, 7, 'Siswa tersebut keluar sekolah pada tanggal ' . $this->date_format->to_print_text($siswa->TANGGAL_MUTASI_SISWA) . ' ' . (isset($alasan[$siswa->ID_MUTASI]) ? $alasan[$siswa->ID_MUTASI] : 'atas permintaan sendiri dan orang tua karena pindah sekolah') . '.');
    $pdf->Ln();

    $pdf->MultiCell(0, 7, 'Demikian surat ini dibuat untuk menjadikan maklum adanya.');
    $pdf->Ln(15);

    $pdf->Cell(90);
    $pdf->Cell(0, 7, 'Kajen, ' . $this->date_format->to_print_text(date('Y-m-d')));
    $pdf->Ln(7);

    $pdf->Cell(90);
    $pdf->Cell(0, 7, 'An. Direktur,');
    $pdf->Ln();

    $pdf->Cell(90);
    $pdf->Cell(0, 7, 'Pembantu Direktur Bidang');
    $pdf->Ln();

    $pdf->Cell(90);
    $pdf->Cell(0, 7, 'Ketata Usahaan Dan Keuangan');
    $pdf->Ln(30);

    $pdf->Image(base_url('files/aplikasi/ttd_wahib.png'), 105, 230, 60);
    $pdf->SetFont('Times', 'UB', 12);

    $pdf->Cell(90);
    $pdf->Cell(0, 7, $this->cetak->nama_peg_print($this->pengaturan->getPDTUKeuangan2(), FALSE));
    $pdf->Ln();

$pdf->Output();
