<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 * 
  $pdf->CoreFonts = array('Arial', 'helvetica', 'times', 'symbol', 'zapfdingbats');
 * 
 */

$pdf = $this->fpdf;

foreach ($tag as $id_tag => $detail_tag) {
    $pdf->AddPage("P", 'A4');
//$pdf->SetAutoPageBreak(true, 0);

    $pdf->SetFont('Arial', 'B', 12);

    $pdf->Cell(0, 5, 'LAPORAN REKAPITULASI TUNGGAKAN SISWA', 0, 0, 'C');
    $pdf->Ln();

    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(0, 5, 'TAGIHAN ' . strtoupper($detail_tag->NAMA_TAG), 0, 0, 'C');
//    $pdf->Cell(0, 5, 'WALI KELAS ' . $this->cetak->nama_peg_print($detail_kelas), 0, 0, 'C');
    $pdf->Ln(10);

    $pdf->SetFont('Arial', 'I', 9);
    $pdf->Cell(0, 5, 'Dicetak tanggal ' . date('d-m-Y H:i:s') . ' WIB', 0, 0, 'R');
    $pdf->Ln();

    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(10, 6, 'No', 1, 0, 'C');
    $pdf->Cell(55, 6, 'KELAS', 1, 0, 'C');
    $pdf->Cell(48, 6, 'NAMA SYAHRIAH', 1, 0, 'C');
    $pdf->Cell(33, 6, 'JUMLAH SISWA', 1, 0, 'C');
    $pdf->Cell(43, 6, 'JUMLAH NOMINAL', 1, 0, 'C');
    $pdf->Ln();

    $pdf->SetFont('Arial', '', 10);

    $nomor = 1;
    $kelas = null;
    $wali_kelas = null;
    foreach ($data[$id_tag] as $id_kelas => $detail_kelas) {
        foreach ($detail_kelas as $id_dt => $detail_dt) {
            $pdf->Cell(10, 6, $nomor++, 1, 0, 'C');

            if ($kelas != $id_kelas) {
                $pdf->Cell(55, 6, $detail_dt->NAMA_KELAS, 'RLT', 0, 'L');
                $kelas = $id_kelas;
            } elseif ($wali_kelas != $detail_dt->ID_PEG) {
                $pdf->SetFont('Arial', 'I', 8);
                $pdf->Cell(55, 6, $this->cetak->nama_peg_print($detail_dt), 'RL', 0, 'L');
                $pdf->SetFont('Arial', '', 10);
                $wali_kelas = $detail_dt->ID_PEG;
            } else {
                $pdf->Cell(55, 6, '', 'RL', 0, 'C');
            }

            $pdf->Cell(48, 6, $detail_dt->NAMA_DT, 1, 0, 'L');
            $pdf->Cell(33, 6, number_format($detail_dt->JUMLAH_SISWA, 0, ',', '.'), 1, 0, 'R');
            $pdf->Cell(43, 6, $this->money->format($detail_dt->JUMLAH_NOMINAL), 1, 0, 'R');
            $pdf->Ln();
        }
    }
    $pdf->Cell(100, 6, '', 'T', 0, 'C');

    $pdf->SetFont('Arial', 'B', 10);
}

$pdf->Output();
