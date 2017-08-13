<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 * 
  $this->CoreFonts = array('dejavusans', 'helvetica', 'times', 'symbol', 'zapfdingbats');
 * 
 */

$pdf = $this->tcpdf;

foreach ($SISWA as $DETAIL) {
    $DATA_SISWA = $DETAIL['DETAIL'];
    $DATA_KITAB = $DETAIL['KITAB'];

    $pdf->SetPrintHeader(false);
    $pdf->SetPrintFooter(false);

    $pdf->SetMargins(6, 6);
    $pdf->AddPage("L", "A5");
    $pdf->SetAutoPageBreak(true, 0);

    $pdf->SetFont('dejavusans', '', 12);
    $pdf->Cell(0, 5, 'PANITIA PELAKSANAAN DAN PENYEMAAN HAFALAN', 0, 0, 'L');
    $pdf->Ln();

    $pdf->SetFont('dejavusans', 'B', 14);
    $pdf->Cell(0, 5, strtoupper($this->pengaturan->getNamaLembaga()), 0, 0, 'L');
    $pdf->Ln();

    $pdf->SetFont('dejavusans', '', 10);
    $pdf->Cell(0, 5, strtoupper($this->pengaturan->getDesa() . ' - ' . $this->pengaturan->getKecamatan() . ' - ' . $this->pengaturan->getKabupaten() . ' ' . $this->pengaturan->getKodepos()) . ' TELP. ' . $this->pengaturan->getTelp() . ' FAX. ' . $this->pengaturan->getFax(), 0, 0, 'L');
    $pdf->Ln(8);

    $pdf->SetLineWidth(0.50);
    $pdf->Line(6, 22, 204, 22);
    $pdf->SetLineWidth(0.30);
    $pdf->Line(6, 23, 204, 23);

    $pdf->SetFont('dejavusans', 'B', 12);
    $pdf->Cell(0, 5, 'BUKTI PENYEMAAN HAFALAN', 0, 0, 'C');
    $pdf->Ln(8);

    $pdf->SetFont('dejavusans', '', 10);
    $pdf->Cell(0, 5, 'Yang bertanda tangan dibawah ini:');
    $pdf->Ln(7);

    $pdf->Cell(10);
    $pdf->Cell(23, 5, 'NIS - Nama', 0, 0, 'L');
    $pdf->Cell(0, 5, ': ' . $DATA_SISWA->NIS_SISWA . ' - ' . $DATA_SISWA->NAMA_SISWA, 0, 0, 'L');
    $pdf->Ln();

    $pdf->Cell(10);
    $pdf->Cell(23, 5, 'Kelas', 0, 0, 'L');
    $pdf->Cell(0, 5, ': ' . $DETAIL['KELAS'], 0, 0, 'L');
    $pdf->Ln();

    $pdf->Cell(10);
    $pdf->Cell(23, 5, 'No. Absen', 0, 0, 'L');
    $pdf->Cell(0, 5, ': ' . $DATA_SISWA->NO_ABSEN_AS, 0, 0, 'L');
    $pdf->Ln(7);

    $pdf->Cell(0, 5, 'telah mengajukan penyemaan hafalan dan dinyatakan hafal dengan nilai sebagaimana tercantum dibawah ini: ');
    $pdf->Ln(6);

    $pdf->Cell(7, 10, 'No', 1, 0, 'C');
    $pdf->Cell(30, 10, 'Nama Kitab', 1, 0, 'C');
    $pdf->Cell(45, 10, 'Uraian', 1, 0, 'C');
    $pdf->Cell(70, 5, 'Kesalahan (Item)', 1, 0, 'C');
    $pdf->Cell(23, 10, 'Nilai', 1, 0, 'C');
    $pdf->Cell(23, 10, 'Keterangan', 1, 0, 'C');
    $pdf->Cell(5, 5, '', 0, 0, 'C');
    $pdf->Ln();

    $pdf->Cell(82, 5, '', 0, 0, 'C');
    for ($i = 0; $i < 10; $i++) {
        $pdf->Cell(7, 5, $i + 1, 1, 0, 'C');
    }
    $pdf->Ln();

    $pdf->setRTL(true);
    $pdf->SetFont('aefurat', '', 12);
    $no = 1;
    $temp = 0;
    foreach ($DATA_KITAB as $KITAB) {
        $pdf->Cell(23, 5, '', 1, 0, 'C');
        $pdf->Cell(23, 5, '', 1, 0, 'C');
        $kolom_putih = round($KITAB->NILAI_MAKS_BATASAN / 10);
        $temp_kolom_akhir = $temp;
        for ($i = 10; $i > 0; $i--) {
            if ($i > $temp_kolom_akhir && $i <= ($kolom_putih + $temp_kolom_akhir)) {
                $pdf->setFillColor(255, 255, 255);
                $temp++;
            } else {
                $pdf->setFillColor(128, 128, 128);
            }
            if ($temp == 10)
                $temp = 0;

            $pdf->Cell(7, 5, '', 1, 0, 'C', TRUE);
        }
        $pdf->Cell(45, 5, $KITAB->AWAL_BATASAN . ($KITAB->AWAL_BATASAN == $KITAB->AKHIR_BATASAN ? '' : ' - ' . $KITAB->AKHIR_BATASAN), 1, 0, 'C');
        $pdf->Cell(30, 5, $KITAB->NAMA_KITAB, 1, 0, 'C');
        $pdf->Cell(7, 5, $no++, 1, 0, 'C');
        $pdf->Ln();
    }

    if ($DETAIL['TINGKAT_KELAS'] != 14) {
        $pdf->Cell(23, 5, '', 1, 0, 'C');
        $pdf->Cell(23, 5, '', 1, 0, 'C');
        for ($i = 10; $i > 0; $i--) {
            $pdf->setFillColor(255, 255, 255);
            $pdf->Cell(7, 5, '', 1, 0, 'C', TRUE);
        }
        $pdf->Cell(45, 5, '', 1, 0, 'C');
        $pdf->Cell(30, 5, '', 1, 0, 'C');
        $pdf->Cell(7, 5, $no++, 1, 0, 'C');
        $pdf->Ln();
        $no--;
    }

    $pdf->setRTL(false);
    $pdf->Ln(2);

    $pdf->SetFont('dejavusans', '', 10);
    for ($ttd = 1; $ttd < $no; $ttd++) {
        $pdf->Cell(40, 5, $this->pengaturan->getDesa() . ', ............. 20....', 0, 0, 'L');
    }
    $pdf->SetX(165);
    $pdf->Cell(40, 5, $this->pengaturan->getDesa() . ', ............. 20....', 0, 0, 'L');
    $pdf->Ln();

    for ($ttd = 1; $ttd < $no; $ttd++) {
        $pdf->Cell(40, 5, 'Penyemak ' . $ttd, 0, 0, 'L');
    }
    $pdf->SetX(165);
    $pdf->Cell(40, 5, 'Ketua P3H', 0, 0, 'L');
    $pdf->Ln(14);

    for ($ttd = 1; $ttd < $no; $ttd++) {
        $pdf->Cell(40, 5, 'TTD & Nama Terang', 0, 0, 'L');
    }
    $pdf->SetX(165);
    $pdf->Cell(40, 5, $this->pengaturan->getDataKetuaP3H($DETAIL['JK_KELAS'])->NAMA_PEG, 0, 0, 'L');
    $pdf->Ln(7);

    $pdf->Cell(20, 5, 'Perhatian:', 0, 0, 'L');
    $pdf->Cell(0, 5, '1. Lembar 1 untuk siswa/i (warna putih)', 0, 0, 'L');
    $pdf->Ln();

    $pdf->Cell(20);
    $pdf->Cell(0, 5, '2. Lembar 2 untuk wali kelas (warna merah)', 0, 0, 'L');
    $pdf->Ln();

    $pdf->Cell(20);
    $pdf->Cell(0, 5, '3. Lembar 3 untuk P3H (warna kuning)', 0, 0, 'L');
    $pdf->Ln();
}

$pdf->Output();
