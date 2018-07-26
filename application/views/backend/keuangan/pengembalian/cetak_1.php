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

$pdf->AddPage("L", array(241.3, 279.4));
//$pdf->AddPage("L", "A5");
//	$pdf->SetMargins(6, 0);
$pdf->SetAutoPageBreak(true, 0);

$pdf = $this->cetak->header_yayasan_dotmartrix($pdf);
$pdf->Ln(2);

$pdf->SetFont('courier', 'B', 16);
$pdf->Cell(0, 5, 'KWITANSI PENGEMBALIAN TAGIHAN', 0, 0, 'C');
$pdf->Ln(10);

$pdf->SetFont('courier', '', 11);

$pdf->Cell(25, 5, 'NIS', 0, 0, 'L');
$pdf->Cell(140, 5, ': ' . ($SISWA->NIS_SISWA == NULL ? '-' : $SISWA->NIS_SISWA), 0, 0, 'L');
$pdf->Cell(25, 5, 'TA', 0, 0, 'L');
$pdf->Cell(0, 5, ': -', 0, 0, 'L');
$pdf->Ln();
$pdf->Cell(25, 5, 'NAMA', 0, 0, 'L');
$pdf->Cell(140, 5, ': ' . $SISWA->NAMA_SISWA, 0, 0, 'L');
$pdf->Cell(25, 5, 'TINGKAT', 0, 0, 'L');
$pdf->Cell(0, 5, ': -', 0, 0, 'L');
$pdf->Ln();
$pdf->Cell(25, 5, 'WALI KELAS', 0, 0, 'L');
$pdf->Cell(140, 5, ': -', 0, 0, 'L');
$pdf->Cell(25, 5, 'KELAS', 0, 0, 'L');
$pdf->Cell(0, 5, ': ' . ($SISWA->KELAS_SISWA == NULL ? '-' : $SISWA->KELAS_SISWA), 0, 0, 'L');
$pdf->Ln();

$pdf->SetLineWidth(0.20);
$pdf->Line(11, 68, 268, 68);

$pdf->Cell(10, 5, '#', 0, 0, 'L');
$pdf->Cell(32, 5, 'TA', 0, 0, 'L');
$pdf->Cell(60, 5, 'TAGIHAN', 0, 0, 'L');
$pdf->Cell(103, 5, 'DETAIL', 0, 0, 'L');
$pdf->Cell(59, 5, 'NOMINAL', 0, 0, 'L');
$pdf->Ln();

$pdf->Line(11, 63, 268, 63);

$no = 1;
$total = 0;
foreach ($PEMBAYARAN as $detail) {
    $total += $detail->NOMINAL_BAYAR;
    $pdf->Cell(10, 5, $no++, 0, 0, 'L');
    $pdf->Cell(32, 5, $detail->NAMA_TA, 0, 0, 'L');
    $pdf->Cell(60, 5, $detail->NAMA_TAG, 0, 0, 'L');
    $pdf->Cell(95, 5, $detail->NAMA_DT, 0, 0, 'L');
    $pdf->Cell(59, 5, $this->money->format($detail->NOMINAL_BAYAR), 0, 0, 'R');
    $pdf->Ln();
}

$pdf->SetY(165);
$pdf->Line(11, 165, 268, 165);

$pdf->Cell(197, 5, 'TOTAL', 0, 0, 'R');
$pdf->Cell(59, 5, $this->money->format($total), 0, 0, 'R');
$pdf->Ln(10);

$pdf->Line(11, 170, 268, 170);

$pdf->Cell(197, 5, 'KETERANGAN:', 0, 0, 'L');
$pdf->Cell(0, 5, 'PETUGAS', 0, 0, 'L');
$pdf->Ln();
$pdf->Cell(140, 5, $KETERANGAN, 0, 0, 'L');
$pdf->Ln(10);

$pdf->SetFont('courier', 'B', 12);
$pdf->Cell(197);
$pdf->SetFont('courier', '', 11);
$pdf->Cell(0, 5, $this->session->userdata('FULLNAME_USER'), 0, 0, 'L');
$pdf->Ln(15);

$pdf->SetFont('Arial', 'I', 8);
$pdf->Cell(0, 5, 'Kode: '.$NOTA->KODE_NOTA.' Dibuat: ' . $NOTA->CREATED_NOTA.' Dicetak: '. date('Y-m-d H:i:s'), 0, 0, 'L');

$pdf->Output();