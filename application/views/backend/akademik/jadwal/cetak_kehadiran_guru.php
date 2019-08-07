<?php

$pdf = $this->fpdf;

foreach ($hari as $detail_hari) {
    $pdf->AddPage("P", "A4");
//    $pdf->SetAutoPageBreak(true, 0);

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
    $pdf->Cell(8, 6, 'No', 1, 0, 'C');
    $pdf->Cell(15, 6, 'NIP', 1, 0, 'C');
    $pdf->Cell(50, 6, 'Nama Guru', 1, 0, 'C');
    $pdf->Cell(110, 6, 'Tanda Tangan', 1, 0, 'C');
    $pdf->Ln();

    $pdf->SetFont('Arial', '', 8);
    $nomor = 1;
    foreach ($guru as $detail_guru) {
        $data_mapel = array();
        $data_jam = array();
        $data_kelas = array();
        for ($urutan = 1; $urutan < 9; $urutan++) {
            if ($urutan == 5)
                continue;

            if (isset($jadwal[$detail_hari->ID_HARI][$detail_guru->ID_PEG][$urutan])) {
                $jadwal_guru = $jadwal[$detail_hari->ID_HARI][$detail_guru->ID_PEG][$urutan];
                if (count($jadwal_guru) > 0) {
                    $data_mapel[$urutan] = $jadwal_guru[0]->NAMA_MAPEL;
                    $data_jam[$urutan] = $jadwal_guru[0]->JAM_PELAJARAN_HM;
                    $data_kelas[$urutan] = $jadwal_guru[0]->NAMA_KELAS;
                }
            }
        }

        if (count($data_mapel) == 0)
            continue;

        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(8, 12, $nomor++, 1, 0, 'C');
        $pdf->Cell(15, 12, $detail_guru->NIP_PEG, 1, 0, 'L');
        $pdf->Cell(50, 12, $this->cetak->nama_peg_print($detail_guru), 1, 0, 'L');

        $pdf->SetFont('Arial', '', 5);

        for ($baris = 1; $baris <= 4; $baris++) {
            if ($baris > 1)
                $pdf->Cell(73);

            for ($urutan = 1; $urutan < 9; $urutan++) {
                if ($urutan == 5)
                    continue;

                if ($baris == 1) {
                    if (isset($data_jam[$urutan]))
                        $pdf->Cell(110 / 7, 2, $data_jam[$urutan], 'LTR', 0, 'C');
                    else
                        $pdf->Cell(110 / 7, 2, '', 'LTR', 0, 'L');
                } elseif ($baris == 2) {
                    if (isset($data_mapel[$urutan]))
                        $pdf->Cell(110 / 7, 2, $data_mapel[$urutan], 'LR', 0, 'C');
                    else
                        $pdf->Cell(110 / 7, 2, '', 'LR', 0, 'L');
                } elseif ($baris == 3) {
                    if (isset($data_kelas[$urutan]))
                        $pdf->Cell(110 / 7, 2, $data_kelas[$urutan], 'LR', 0, 'C');
                    else
                        $pdf->Cell(110 / 7, 2, '-', 'LR', 0, 'C');
                } elseif ($baris == 4) {
                    $pdf->Cell(110 / 7, 6, '', 'LBR', 0, 'L');
                }
            }

            $pdf->Ln();
        }
    }
}

$pdf->Output();
?>