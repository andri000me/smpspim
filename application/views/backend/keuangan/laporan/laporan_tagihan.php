<?php

$pdf = $this->fpdf;

foreach ($pegawai as $id_peg => $detail_peg) {
    $pdf->AddPage("P", "A4");
//$pdf->SetAutoPageBreak(true, 0);

    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 5, 'REKAPITULASI PEMBAYARAN SISWA', 0, 0, 'C');
    $pdf->Ln();

    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(0, 5, 'TELLER ' . strtoupper($this->cetak->nama_peg_print($detail_peg)), 0, 0, 'C');
    $pdf->Ln();

    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(0, 5, 'TAHUN AJARAN ' . strtoupper($this->session->userdata('NAMA_TA_ACTIVE')), 0, 0, 'C');
    $pdf->Ln(10);

    $pdf->SetFont('Arial', 'I', 10);
    $pdf->Cell(0, 5, 'Dicetak tanggal ' . date('d-m-Y H:i:s') . ' WIB', 0, 0, 'R');
    $pdf->Ln();

    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(10, 6, 'NO', 1, 0, 'C');
    $pdf->Cell(30, 6, 'JENJANG', 1, 0, 'C');
    $pdf->Cell(60, 6, 'BULAN', 1, 0, 'C');
    $pdf->Cell(60, 6, 'JUMLAH', 1, 0, 'C');
    $pdf->Ln();

    $pdf->SetFont('Arial', '', 10);

    $nomor = 1;
    foreach ($jenjang as $detail_jenjang) {
        $start = true;
        foreach ($detail_tag[$detail_jenjang->ID_DEPT] as $id_dt => $detail) {
            $pdf->Cell(10, 6, $nomor++, 1, 0, 'C');

            if ($start) {
                $pdf->Cell(30, 6, $detail_jenjang->ID_DEPT, 'RLT', 0, 'C');
                $start = false;
            } else {
                $pdf->Cell(30, 6, '', 'RL', 0, 'C');
            }

            $pdf->Cell(60, 6, $detail->NAMA_DT, 1, 0, 'L');

            if (isset($pembayaran[$id_peg][$detail_jenjang->ID_DEPT][$id_dt])) {
                $pdf->Cell(60, 6, $this->money->format($pembayaran[$id_peg][$detail_jenjang->ID_DEPT][$id_dt]->NOMINAL), 1, 0, 'R');
            } else {
                $pdf->Cell(60, 6, "-", 1, 0, 'R');
            }

            $pdf->Ln();
        }
    }

    $pdf->Cell(100, 6, '', 'T', 0, 'C');
}

$pdf->Output();
?>