<?php

$pdf = $this->fpdf;



$peg = null;
foreach ($data as $detail) {
    if ($peg != $detail->ID_PEG) {
        $peg = $detail->ID_PEG;
        $pdf->AddPage("P", "A4");

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 5, 'REKAPITULASI KEHADIRAN GURU', 0, 0, 'C');
        $pdf->Ln();

        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(0, 5, strtoupper($this->cetak->nama_peg_print($detail)), 0, 0, 'C');
        $pdf->Ln();

        $pdf->Cell(0, 5, $detail->NAMA_CAWU.' TAHUN AJARAN ' . $this->session->userdata('NAMA_TA_ACTIVE'), 0, 0, 'C');
        $pdf->Ln(10);

        $pdf->SetFont('Arial', 'B', 10);

        $pdf->Cell(10, 12, 'NO', 1, 0, 'C');
        $pdf->Cell(30, 12, 'BULAN', 1, 0, 'C');
        $pdf->Cell(80, 6, 'JUMLAH', 1, 0, 'C');
        $pdf->Ln();

        $pdf->Cell(40);
        $pdf->Cell(20, 6, 'HADIR', 1, 0, 'C');
        $pdf->Cell(20, 6, 'SAKIT', 1, 0, 'C');
        $pdf->Cell(20, 6, 'IZIN', 1, 0, 'C');
        $pdf->Cell(20, 6, 'ALPHA', 1, 0, 'C');
        $pdf->Ln();

        $nomor = 1;
        $pdf->SetFont('Arial', '', 10);
    }

    $pdf->Cell(10, 5, $nomor++, 1, 0, 'C');
    $pdf->Cell(30, 5, $detail->BULAN, 1, 0, 'C');
    $pdf->Cell(20, 5, $detail->JUMLAH_HADIR, 1, 0, 'C');
    $pdf->Cell(20, 5, $detail->JUMLAH_SAKIT, 1, 0, 'C');
    $pdf->Cell(20, 5, $detail->JUMLAH_IZIN, 1, 0, 'C');
    $pdf->Cell(20, 5, $detail->JUMLAH_ALPHA, 1, 0, 'C');
    $pdf->Ln();
}

$pdf->Output();
?>