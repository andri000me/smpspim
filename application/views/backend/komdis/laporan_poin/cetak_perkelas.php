<?php

if (!isset($data) || count($data) == 0) {
    echo '<h1>TIDAK ADA DATA YANG AKAN DICETAK.</h1>';

    exit();
}

$pdf = $this->fpdf;

foreach ($data as $detail) {
    $KELAS = $detail['KELAS'];
    $DATA = $detail['DATA'];

    $pdf->AddPage("P", "A4");
//	$pdf->SetMargins(6, 0);
    $pdf->SetAutoPageBreak(true, 0);

    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 4, 'KOMISI DISIPLIN SISWA', 0, 0, 'C');
    $pdf->Ln();

    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(0, 4, 'DATA PERKEMBANGAN POINT', 0, 0, 'C');
    $pdf->Ln(8);

    $pdf->SetFont('Arial', '', 9);

    $pdf->Cell(10, 4, 'Kelas');
    $pdf->Cell(40, 4, ': ' . $KELAS->NAMA_KELAS);
    $pdf->Cell(15, 4, 'Wali Kelas');
    $pdf->Cell(0, 4, ': ' . $this->cetak->nama_peg_print($KELAS));
    $pdf->Ln();

    $pdf->SetFont('Arial', '', 8);
    $pdf->Cell(7, 4, 'No', 'LTR', 0, 'C');
    $pdf->Cell(20, 8, 'NIS', 1, 0, 'C');
    $pdf->Cell(40, 8, 'Nama', 1, 0, 'C');
    $pdf->Cell(7, 4, 'Poin', 'LTR', 0, 'C');
    $pdf->Cell(7, 8, 'L', 1, 0, 'C');
    $pdf->Cell(20, 8, 'Surat', 1, 0, 'C');
    $pdf->Cell(65, 4, 'Bulan', 1, 0, 'C');
    $pdf->Cell(18, 4, 'Cawu', 1, 0, 'C');
    $pdf->Cell(7, 8, 'Jml', 1, 0, 'C');
    $pdf->Ln(4);
    $pdf->Cell(7, 4, 'Absn', 'LBR', 0, 'C');
    $pdf->Cell(60);
    $pdf->Cell(7, 4, 'Lalu', 'LBR', 0, 'C');
    $pdf->Cell(27);
    $pdf->Cell(65 / 12, 4, '07', 1, 0, 'C');
    $pdf->Cell(65 / 12, 4, '08', 1, 0, 'C');
    $pdf->Cell(65 / 12, 4, '09', 1, 0, 'C');
    $pdf->Cell(65 / 12, 4, '10', 1, 0, 'C');
    $pdf->Cell(65 / 12, 4, '11', 1, 0, 'C');
    $pdf->Cell(65 / 12, 4, '12', 1, 0, 'C');
    $pdf->Cell(65 / 12, 4, '01', 1, 0, 'C');
    $pdf->Cell(65 / 12, 4, '02', 1, 0, 'C');
    $pdf->Cell(65 / 12, 4, '03', 1, 0, 'C');
    $pdf->Cell(65 / 12, 4, '04', 1, 0, 'C');
    $pdf->Cell(65 / 12, 4, '05', 1, 0, 'C');
    $pdf->Cell(65 / 12, 4, '06', 1, 0, 'C');
    $pdf->Cell(6, 4, '1', 1, 0, 'C');
    $pdf->Cell(6, 4, '2', 1, 0, 'C');
    $pdf->Cell(6, 4, '3', 1, 0, 'C');
    $pdf->Ln(4);

    $jumlah_sp = 0;
    $jumlah_po_1 = 0;
    $jumlah_po_2 = 0;
    $jumlah_takliq = 0;
    foreach ($DATA as $DETAIL) {
        if ($DETAIL->ID_KJT == 1)
            $jumlah_sp++;
        elseif ($DETAIL->ID_KJT == 2)
            $jumlah_po_1++;
        elseif ($DETAIL->ID_KJT == 3)
            $jumlah_po_2++;
        elseif ($DETAIL->ID_KJT == 4)
            $jumlah_takliq++;

        if ($DETAIL->AKTIF_AS)
            $pdf->setFillColor(255, 255, 255);
        else
            $pdf->setFillColor(128, 128, 128);

        $pdf->Cell(7, 4, $DETAIL->NO_ABSEN_AS, 1, 0, 'C', TRUE);
        $pdf->Cell(20, 4, $DETAIL->NIS_SISWA == NULL ? 'KELUAR' : $DETAIL->NIS_SISWA, 1, 0, 'C', TRUE);
        $pdf->Cell(40, 4, $DETAIL->NAMA_SISWA, 1, 0, 'L', TRUE);
        $pdf->Cell(7, 4, ($DETAIL->POIN_TAHUN_LALU_KSH > 0 ? $DETAIL->POIN_TAHUN_LALU_KSH : ''), 1, 0, 'C', TRUE);
        $pdf->Cell(7, 4, ($DETAIL->TOTAL_LARI > 0 ? $DETAIL->TOTAL_LARI : ''), 1, 0, 'C', TRUE);
        $pdf->Cell(20, 4, $DETAIL->NAMA_KJT, 1, 0, 'L', TRUE);
        $pdf->Cell(65 / 12, 4, $DETAIL->B07, 1, 0, 'C', TRUE);
        $pdf->Cell(65 / 12, 4, $DETAIL->B08, 1, 0, 'C', TRUE);
        $pdf->Cell(65 / 12, 4, $DETAIL->B09, 1, 0, 'C', TRUE);
        $pdf->Cell(65 / 12, 4, $DETAIL->B10, 1, 0, 'C', TRUE);
        $pdf->Cell(65 / 12, 4, $DETAIL->B11, 1, 0, 'C', TRUE);
        $pdf->Cell(65 / 12, 4, $DETAIL->B12, 1, 0, 'C', TRUE);
        $pdf->Cell(65 / 12, 4, $DETAIL->B01, 1, 0, 'C', TRUE);
        $pdf->Cell(65 / 12, 4, $DETAIL->B02, 1, 0, 'C', TRUE);
        $pdf->Cell(65 / 12, 4, $DETAIL->B03, 1, 0, 'C', TRUE);
        $pdf->Cell(65 / 12, 4, $DETAIL->B04, 1, 0, 'C', TRUE);
        $pdf->Cell(65 / 12, 4, $DETAIL->B05, 1, 0, 'C', TRUE);
        $pdf->Cell(65 / 12, 4, $DETAIL->B06, 1, 0, 'C', TRUE);
        $pdf->Cell(6, 4, $DETAIL->CAWU_1 == 0 ? '' : $DETAIL->CAWU_1, 1, 0, 'C', TRUE);
        $pdf->Cell(6, 4, $DETAIL->CAWU_2 == 0 ? '' : $DETAIL->CAWU_2, 1, 0, 'C', TRUE);
        $pdf->Cell(6, 4, $DETAIL->CAWU_3 == 0 ? '' : $DETAIL->CAWU_3, 1, 0, 'C', TRUE);
        $jumlah = $DETAIL->CAWU_1 + $DETAIL->CAWU_2 + $DETAIL->CAWU_3;
        $pdf->Cell(7, 4, $jumlah == 0 ? '' : $jumlah, 1, 0, 'C', TRUE);
        $pdf->Ln();
    }
    $pdf->Cell(0, 4, 'NB.');
    $pdf->Ln();

    $pdf->Cell(5);
    $pdf->Cell(0, 4, 'Data dicetak tanggal: ' . $this->date_format->to_print_text(date('Y-m-d')));
    $pdf->Ln();

    $pdf->Cell(5);
    $pdf->Cell(0, 4, 'Data pelanggaran terkahir: ' . $this->date_format->to_print_text($TANGGAL));
    $pdf->Ln();

    $pdf->Cell(5);
    $pdf->Cell(0, 4, 'Jumlah SP: ' . $jumlah_sp);
    $pdf->Ln();

    $pdf->Cell(5);
    $pdf->Cell(0, 4, 'Jumlah PO1: ' . $jumlah_po_1);
    $pdf->Ln();

    $pdf->Cell(5);
    $pdf->Cell(0, 4, 'Jumlah PO2: ' . $jumlah_po_2);
    $pdf->Ln();

    $pdf->Cell(5);
    $pdf->Cell(0, 4, 'Jumlah Ta\'liq: ' . $jumlah_takliq);
    $pdf->Ln();
}

$pdf->Output();
