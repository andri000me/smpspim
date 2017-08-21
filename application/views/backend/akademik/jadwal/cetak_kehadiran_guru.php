<?php

$pdf = $this->fpdf;

$JK = array(
    'L' => 'BANIN',
    'P' => 'BANAT',
);

foreach ($hari as $detail_hari) {
    foreach ($jk as $detail_jk) {
        if(count($guru[$detail_hari->ID_HARI][$detail_jk->ID_JK]) == 0) 
            continue;
        
        $jumlah_jp = $jam_pelajaran[$detail_hari->ID_HARI][$detail_jk->ID_JK];

        $pdf->AddPage("P", "A4");
        $pdf->SetAutoPageBreak(true, 0);

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 5, 'DAFTAR KEHADIRAN GURU', 0, 0, 'C');
        $pdf->Ln();

        $pdf->Cell(0, 5, 'PERGURUAN ISLAM MATHALI\'UL FALAH', 0, 0, 'C');
        $pdf->Ln();

        $pdf->Cell(0, 5, 'TAHUN AJARAN ' . $this->session->userdata('NAMA_TA_ACTIVE'), 0, 0, 'C');
        $pdf->Ln(8);

        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(0, 5, 'Hari, Tanggal : ' . $detail_hari->NAMA_HARI . ', ...................................');
        $pdf->Ln();

        $pdf->SetFont('Arial', 'I', 8);
        $pdf->Cell(0, 4, 'Ket: Demi ketertiban administrasi, semua guru dimohon membubuhkan tanda tangan pada kolom dibawah ini.');
        $pdf->Ln();

        $pdf->SetFont('Arial', 'B', 9);

        $pdf->Cell(10, 10, 'No', 1, 0, 'C');
        $pdf->Cell(50, 10, 'Nama Guru', 1, 0, 'C');
        $pdf->Cell(100, 5, 'Jam Pelajaran', 1, 0, 'C');
        $pdf->Cell(30, 10, 'Keterangan', 1, 0, 'C');
        $pdf->Ln(5);

        $pdf->Cell(60);
        for ($jp = 0; $jp < $jumlah_jp; $jp++) {
            $pdf->Cell((100 / $jumlah_jp), 5, $jp + 1, 1, 0, 'C');
        }
        $pdf->Ln();

        $pdf->SetFont('Arial', '', 9);

        $no = 1;
        foreach ($guru[$detail_hari->ID_HARI][$detail_jk->ID_JK] as $ID_PEG => $detail_guru) {
            $pdf->Cell(10, 5, $no++, 1, 0, 'C');
            $pdf->Cell(50, 5, $this->cetak->nama_peg_print($detail_guru), 1, 0, 'L');
            for ($jp = 0; $jp < $jumlah_jp; $jp++) {
                $pdf->Cell((100 / $jumlah_jp), 5, isset($jadwal[$detail_hari->ID_HARI][$detail_jk->ID_JK][$ID_PEG][$jp]) ? $jadwal[$detail_hari->ID_HARI][$detail_jk->ID_JK][$ID_PEG][$jp]->NAMA_KELAS : '-', 1, 0, 'C');
            }
            $pdf->Cell(30, 5, '', 1, 0, 'C');
            $pdf->Ln();
        }

        $pdf->SetXY(160, 20);
        $pdf->SetFont('Arial', 'B', 18);
        $pdf->Cell(0, 10, $JK[$detail_jk->ID_JK], 1, 0, 'C');
    }
}

$pdf->Output();
?>