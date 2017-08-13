<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 * 
  $this->CoreFonts = array('Arial', 'helvetica', 'times', 'symbol', 'zapfdingbats');
 * 
 */

$pdf = $this->fpdf;

$pdf->AddPage("L", "A5");
$pdf->SetMargins(6, 0);
$pdf->SetAutoPageBreak(true, 0);

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 5, strtoupper($this->pengaturan->getNamaLembaga()), 0, 0, 'C');
$pdf->Ln();

$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 5, $STATUS_PSB ? 'PANITIA PENDAFTARAN SISWA BARU' : 'BIDANG KEUANGAN', 0, 0, 'C');
$pdf->Ln();

$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 5, $this->pengaturan->getDesa().' - '.$this->pengaturan->getKecamatan().' - '.$this->pengaturan->getKabupaten().' '.$this->pengaturan->getKodepos().' Telp. '.$this->pengaturan->getTelp().' Fax. '.$this->pengaturan->getFax(), 0, 0, 'C');
$pdf->Ln(8);

$pdf->SetLineWidth(0.40);
$pdf->Line(6, 25, 202, 25);

$pdf->SetLineWidth(0.20);
$pdf->Line(6, 26, 202, 26);

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 5, 'KWITANSI PEMBAYARAN', 0, 0, 'C');
$pdf->Ln(8);

$pdf->SetFont('Arial', '', 10);

$pdf->Cell(22, 5, $SISWA->NIS_SISWA == NULL? 'NO. UM' : 'NIS', 0, 0, 'L');
$pdf->Cell(90, 5, ': ' . ($SISWA->NIS_SISWA == NULL ? ($SISWA->NO_UM_SISWA == NULL ? '-' : $this->pengaturan->getKodeUM($SISWA)) : $SISWA->NIS_SISWA), 0, 0, 'L');
$pdf->Cell(22, 5, $SISWA->NAMA_TA == NULL ? 'ALAMAT' : 'TA', 0, 0, 'L');
$pdf->Cell(0, 5, ': '.($SISWA->NAMA_TA == NULL ? $SISWA->ALAMAT_SISWA : $SISWA->NAMA_TA), 0, 0, 'L');
$pdf->Ln();
$pdf->Cell(22, 5, 'NAMA', 0, 0, 'L');
$pdf->Cell(90, 5, ': ' . $SISWA->NAMA_SISWA, 0, 0, 'L');
$pdf->Cell(22, 5, $SISWA->KETERANGAN_TINGK_NOW == NULL ? '' : 'TINGKAT', 0, 0, 'L');
$pdf->Cell(0, 5, ($SISWA->KETERANGAN_TINGK_NOW == NULL ? 'Kec. '.$SISWA->NAMA_KEC_SISWA.', '.(str_replace("Kabupaten", "Kab.", $SISWA->NAMA_KAB_SISWA)) : ': '.$SISWA->KETERANGAN_TINGK_NOW), 0, 0, 'L');
$pdf->Ln();
$pdf->Cell(22, 5, $SISWA->NAMA_PEG == NULL ? 'TTL' : 'WALI KELAS', 0, 0, 'L');
$pdf->Cell(90, 5, ': '.($SISWA->NAMA_PEG == NULL ? $SISWA->TEMPAT_LAHIR_SISWA.', '.$this->date_format->to_print_text($SISWA->TANGGAL_LAHIR_SISWA) : $this->cetak->nama_peg_print($SISWA)), 0, 0, 'L');
$pdf->Cell(22, 5, $SISWA->NAMA_KELAS == NULL ? 'NAMA AYAH' : 'KELAS', 0, 0, 'L');
$pdf->Cell(0, 5, ': ' . ($SISWA->NAMA_KELAS == NULL ? $SISWA->AYAH_NAMA_SISWA : $SISWA->NAMA_KELAS), 0, 0, 'L');
$pdf->Ln(6);

$pdf->SetLineWidth(0.20);
$pdf->Line(6, 52, 202, 52);

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(5, 5, '#', 0, 0, 'L');
$pdf->Cell(22, 5, 'TA', 0, 0, 'L');
$pdf->Cell(50, 5, 'TAGIHAN', 0, 0, 'L');
$pdf->Cell(83, 5, 'DETAIL', 0, 0, 'L');
$pdf->Cell(37, 5, 'NOMINAL', 0, 0, 'L');
$pdf->Ln();

$pdf->Line(6, 57, 202, 57);

$pdf->SetFont('Arial', '', 10);
$no = 1;
$total = 0;
foreach ($PEMBAYARAN as $detail) {
    $total += $detail->NOMINAL_BAYAR;
    $pdf->Cell(5, 5, $no++, 0, 0, 'L');
    $pdf->Cell(22, 5, $detail->NAMA_TA, 0, 0, 'L');
    $pdf->Cell(50, 5, $detail->NAMA_TAG, 0, 0, 'L');
    $pdf->Cell(83, 5, $detail->NAMA_DT, 0, 0, 'L');
    $pdf->Cell(37, 5, $this->money->format($detail->NOMINAL_BAYAR), 0, 0, 'R');
    $pdf->Ln();
}

$pdf->SetY(90);
$pdf->Line(6, 90, 202, 90);

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(160, 5, 'TOTAL', 0, 0, 'R');
$pdf->Cell(37, 5, $this->money->format($total), 0, 0, 'R');
$pdf->Ln(8);

$pdf->Line(6, 95, 202, 95);

$pdf->SetFont('Arial', '', 10);
$pdf->Cell(140, 5, 'KETERANGAN:', 0, 0, 'L');
$pdf->Cell(0, 5, 'PETUGAS', 0, 0, 'L');
$pdf->Ln();

$pdf->MultiCell(130, 5, $KETERANGAN);
$pdf->Ln(2);

if($STATUS_PSB) {
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 5, $STATUS_PSB ? ($SISWA->NO_UM_SISWA == NULL ? 'DAFTAR DI: '.$SISWA->DEPT_MJD.'-'.$SISWA->MASUK_TINGKAT_SISWA.' (TANPA UM)' : 'NO. UM: '.$this->pengaturan->getKodeUM($SISWA)) : '', 0, 0, 'L');
    $pdf->Ln();
}

$pdf->SetY(120);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(140, 5, $STATUS_PSB ? 'PEMBAYARAN PSB: '.($STATUS_LUNAS ? 'LUNAS' : 'BELUM LUNAS') : '', 0, 0, 'L');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 5, $this->session->userdata('FULLNAME_USER'), 0, 0, 'L');
$pdf->Ln();

$pdf->SetFont('Arial', 'I', 8);
$pdf->Cell(0, 5, 'Kode: '.$NOTA->KODE_NOTA.' Dibuat: ' . $NOTA->CREATED_NOTA.' Dicetak: '. date('Y-m-d H:i:s'), 0, 0, 'L');

$pdf->Ln();

$pdf->Output();
