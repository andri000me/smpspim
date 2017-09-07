<?php

$pdf = $this->fpdf;
$pdf->AddPage("P", "A4");
$pdf->SetAutoPageBreak(true, 0);

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 5, 'JUMLAH KELAS SETIAP GURU', 0, 0, 'C');
$pdf->Ln();

$pdf->Cell(0, 5, 'TAHUN AJARAN ' . $this->session->userdata('NAMA_TA_ACTIVE'), 0, 0, 'C');
$pdf->Ln(8);

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(10, 5, 'NO', 1, 0, 'C');
$pdf->Cell(30, 5, 'NIP', 1, 0, 'C');
$pdf->Cell(70, 5, 'NAMA', 1, 0, 'C');
$pdf->Cell(30, 5, 'JUMLAH KELAS', 1, 0, 'C');
$pdf->Ln();

$pdf->SetFont('Arial', '', 10);
$no = 1;
foreach ($guru as $detail_guru) {
    $pdf->Cell(10, 5, $no++, 1, 0, 'C');
    $pdf->Cell(30, 5, $detail_guru->NIP_PEG, 1, 0, 'C');
    $pdf->Cell(70, 5, $this->cetak->nama_peg_print($detail_guru), 1, 0, 'L');
    $pdf->Cell(30, 5, $jadwal[$detail_guru->ID_PEG], 1, 0, 'C');
    $pdf->Ln();
}

$pdf->Output();
?>