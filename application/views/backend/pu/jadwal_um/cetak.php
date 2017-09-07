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

$pdf->AddPage("L", "A5");
//	$pdf->SetMargins(6, 0);
$pdf->SetAutoPageBreak(true, 0);

$pdf = $this->cetak->header_yayasan($pdf);

$pdf->SetFont('courier', 'B', 12);
$pdf->Cell(0, 5, 'KWITANSI PEMBAYARAN', 0, 0, 'C');
$pdf->Ln();

$pdf->SetFont('courier', '', 10);

$pdf->Cell(22, 5, 'NIS', 0, 0, 'L');
$pdf->Cell(90, 5, ': ' . ($SISWA->NIS_SISWA == NULL ? '-' : $SISWA->NIS_SISWA), 0, 0, 'L');
$pdf->Cell(22, 5, 'TA', 0, 0, 'L');
$pdf->Cell(0, 5, ': -', 0, 0, 'L');
$pdf->Ln();
$pdf->Cell(22, 5, 'NAMA', 0, 0, 'L');
$pdf->Cell(90, 5, ': ' . $SISWA->NAMA_SISWA, 0, 0, 'L');
$pdf->Cell(22, 5, 'TINGKAT', 0, 0, 'L');
$pdf->Cell(0, 5, ': -', 0, 0, 'L');
$pdf->Ln();
$pdf->Cell(22, 5, 'WALI KELAS', 0, 0, 'L');
$pdf->Cell(90, 5, ': -', 0, 0, 'L');
$pdf->Cell(22, 5, 'KELAS', 0, 0, 'L');
$pdf->Cell(0, 5, ': ' . ($SISWA->KELAS_SISWA == NULL ? '-' : $SISWA->KELAS_SISWA), 0, 0, 'L');
$pdf->Ln();

$pdf->SetLineWidth(0.20);
$pdf->Line(11, 56, 200, 56);

$pdf->Cell(5, 5, '#', 0, 0, 'L');
$pdf->Cell(22, 5, 'TA', 0, 0, 'L');
$pdf->Cell(50, 5, 'TAGIHAN', 0, 0, 'L');
$pdf->Cell(83, 5, 'DETAIL', 0, 0, 'L');
$pdf->Cell(39, 5, 'NOMINAL', 0, 0, 'L');
$pdf->Ln();

$pdf->Line(11, 61, 200, 61);

$no = 1;
$total = 0;
foreach ($PEMBAYARAN as $detail) {
    $total += $detail->NOMINAL_BAYAR;
    $pdf->Cell(5, 5, $no++, 0, 0, 'L');
    $pdf->Cell(22, 5, $detail->NAMA_TA, 0, 0, 'L');
    $pdf->Cell(50, 5, $detail->NAMA_TAG, 0, 0, 'L');
    $pdf->Cell(75, 5, $detail->NAMA_DT, 0, 0, 'L');
    $pdf->Cell(39, 5, $this->money->format($detail->NOMINAL_BAYAR), 0, 0, 'R');
    $pdf->Ln();
}

$pdf->SetY(115);
$pdf->Line(11, 115, 200, 115);

$pdf->Cell(141, 5, 'TOTAL', 0, 0, 'R');
$pdf->Cell(50, 5, $this->money->format($total), 0, 0, 'R');
$pdf->Ln();

$pdf->Line(11, 120, 200, 120);

$pdf->Cell(140, 5, 'KETERANGAN:', 0, 0, 'L');
$pdf->Cell(0, 5, 'PETUGAS', 0, 0, 'L');
$pdf->Ln();
$pdf->Cell(140, 5, $KETERANGAN, 0, 0, 'L');
$pdf->Ln();

$pdf->SetFont('courier', 'B', 14);
$pdf->Cell(0, 5, $STATUS_PSB ? ($SISWA->NO_UM_SISWA == NULL ? 'TANPA UJIAN MASUK' : 'NO. UM: '.$this->pengaturan->getKodeUM($SISWA)) : '', 0, 0, 'L');
$pdf->Ln();

$pdf->SetFont('courier', 'B', 12);
$pdf->Cell(140, 5, $STATUS_PSB ? 'PEMBAYARAN PSB: '.($STATUS_LUNAS ? 'LUNAS' : 'BELUM LUNAS') : '', 0, 0, 'L');
$pdf->SetFont('courier', '', 10);
$pdf->Cell(0, 5, $this->session->userdata('FULLNAME_USER'), 0, 0, 'L');
$pdf->Ln();

$pdf->SetFont('Arial', 'I', 8);
$pdf->Cell(0, 5, 'Kode: '.$NOTA->KODE_NOTA.' Dibuat: ' . $NOTA->CREATED_NOTA.' Dicetak: '. date('Y-m-d H:i:s'), 0, 0, 'L');

$pdf->Ln();

$pdf->Output();