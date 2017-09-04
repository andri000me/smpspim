<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


if (!isset($data) || count($data) == 0) {
    echo '<h1>TIDAK ADA DATA YANG AKAN DICETAK.</h1>';

    exit();
}

$pdf = $this->fpdf;

$temp_kelas = null;
foreach ($data as $detail) {
    if ($temp_kelas != $detail->NAMA_KELAS) {
        $temp_kelas = $detail->NAMA_KELAS;

        $pdf->AddPage("P", "A4");
        //	$pdf->SetMargins(6, 0);
        $pdf->SetAutoPageBreak(true, 0);

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 5, 'DATA LARI SISWA LEBIH DARI 2', 0, 0, 'C');
        $pdf->Ln(8);

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(100, 5, 'Kelas: ' . $detail->NAMA_KELAS);
        $pdf->Cell(0, 5, 'Waki Kelas: ' . $this->cetak->nama_peg_print_title($detail->GELAR_AWAL_PEG, $detail->NAMA_PEG, $detail->GELAR_AKHIR_PEG));
        $pdf->Ln();

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(20, 5, 'No Absen', 1, 0, 'C');
        $pdf->Cell(25, 5, 'NIS', 1, 0, 'C');
        $pdf->Cell(55, 5, 'Nama', 1, 0, 'C');
        $pdf->Cell(40, 5, 'Nama Ayah', 1, 0, 'C');
        $pdf->Cell(25, 5, 'Jumlah Poin', 1, 0, 'C');
        $pdf->Cell(25, 5, 'Jumlah Lari', 1, 0, 'C');
        $pdf->Ln();
        
        $pdf->SetFont('Arial', '', 10);
    }

    $pdf->Cell(20, 5, $detail->NO_ABSEN_AS, 1, 0, 'C');
    $pdf->Cell(25, 5, $detail->NIS_SISWA, 1, 0, 'C');
    $pdf->Cell(55, 5, $detail->NAMA_SISWA, 1, 0, 'L');
    $pdf->Cell(40, 5, $detail->AYAH_NAMA_SISWA, 1, 0, 'L');
    $pdf->Cell(25, 5, $detail->JUMLAH_POIN_KSH, 1, 0, 'C');
    $pdf->Cell(25, 5, $detail->JUMLAH_LARI_KSH, 1, 0, 'C');
    $pdf->Ln();
}

$pdf->Output();
