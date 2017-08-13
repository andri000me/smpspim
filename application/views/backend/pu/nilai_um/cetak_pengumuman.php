<?php

$pdf = $this->fpdf;

$temp_ID_TINGK = NULL;
$temp_JK_SISWA = NULL;


$pdf->AddPage("P", "A4");
$pdf->SetAutoPageBreak(true, 0);

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 5, 'PANITIA UJIAN', 0, 0, 'C');
$pdf->Ln();

$pdf->Cell(0, 5, 'HASIL KEPUTUSAN KELULUSAN PSB', 0, 0, 'C');
$pdf->Ln();

$pdf->Cell(0, 5, 'TAHUN ' . $this->pengaturan->getTahunPSBAwal(), 0, 0, 'C');
$pdf->Ln(10);

$pdf->SetFont('Arial', 'B', 10);

$no = 1;
$pdf->Cell(10, 6, 'No', 1, 0, 'C');
$pdf->Cell(40, 6, 'No. UM', 1, 0, 'C');
$pdf->Cell(100, 6, 'Nama', 1, 0, 'C');
$pdf->Cell(40, 6, 'Diterima di Kelas', 1, 0, 'C');
$pdf->Ln();

$pdf->SetFont('Arial', '', 10);

foreach ($siswa as $detail) {
    $pdf->Cell(10, 6, $no++, 1, 0, 'C');
    $pdf->Cell(40, 6, $this->pengaturan->getKodeUM($detail), 1, 0, 'L');
    $pdf->Cell(100, 6, $detail->NAMA_SISWA, 1, 0, 'L');
    $pdf->Cell(40, 6, $detail->NAMA_TINGK_NOW.' - '.$detail->DEPT_TINGK_NOW, 1, 0, 'C');
    $pdf->Ln();
}

$pdf->Output();
?>