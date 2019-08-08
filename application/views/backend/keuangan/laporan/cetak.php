<?php

$pdf = $this->fpdf;
$pdf->AddPage("P", "A4");
//$pdf->SetAutoPageBreak(true, 0);

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 5, 'REKAPITULASI PEMBAYARAN SISWA', 0, 0, 'C');
$pdf->Ln();

$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 5, 'TELLER ' . strtoupper($this->session->userdata('FULLNAME_USER')), 0, 0, 'C');
$pdf->Ln();

$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 5, 'TANGGAL ' . date('d-m-Y', strtotime($start)) . ' SAMPAI ' . date('d-m-Y', strtotime($end)), 0, 0, 'C');
$pdf->Ln(10);

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(10, 6, 'NO', 1, 0, 'C');
$pdf->Cell(30, 6, 'TANGGAL', 1, 0, 'C');
$pdf->Cell(50, 6, 'JUMLAH', 1, 0, 'C');
$pdf->Ln();

$pdf->SetFont('Arial', '', 10);
$no = 1;
while (true) {
    $pdf->Cell(10, 6, $no++, 1, 0, 'C');
    $pdf->Cell(30, 6, date('d-m-Y', strtotime($end)), 1, 0, 'C');
    $pdf->Cell(50, 6, isset($pembayaran[$end]) ? $this->money->format($pembayaran[$end]) : "-", 1, 0, 'R');
    $pdf->Ln();

    if ($start == $end)
        break;

    $end = date('Y-m-d', strtotime($end . "-1 days"));
}

$pdf->Output();
?>