<?php

$pdf = $this->fpdf;

$JK = array(
    'L' => 'BANIN',
    'P' => 'BANAT',
);

foreach ($kelas as $index => $detail_kelas) {
    $pdf->AddPage("L", "A4");
    $pdf->SetAutoPageBreak(true, 0);

    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 5, 'JADWAL PELAJARAN KELAS ' . strtoupper($detail_kelas->NAMA_KELAS), 0, 0, 'C');
    $pdf->Ln();

    $pdf->Cell(0, 5, 'PERGURUAN ISLAM MATHALI\'UL FALAH', 0, 0, 'C');
    $pdf->Ln();

    $pdf->Cell(0, 5, 'TAHUN AJARAN ' . $this->session->userdata('NAMA_TA_ACTIVE'), 0, 0, 'C');
    $pdf->Ln(10);

    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(23, 6, 'Jam', 1, 0, 'C');
    foreach ($hari as $detail_hari) {
        $pdf->Cell(42, 6, $detail_hari->NAMA_HARI, 1, 0, 'C');
    }
    $pdf->Ln();

    foreach ($jam_pelajaran[$detail_kelas->ID_KELAS][$detail_hari->ID_HARI] as $detail_jp) {
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(23, 14, date("H:i", strtotime($detail_jp->MULAI_MJP)) . " - " . date("H:i", strtotime($detail_jp->AKHIR_MJP)), 1, 0, 'C');

        foreach ($hari as $detail_hari) {
            $pdf->Cell(42, 2, "", 'TLR', 0, 'L');
        }
        $pdf->Ln();

        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(23);
        foreach ($hari as $detail_hari) {
            if (isset($jadwal[$detail_kelas->ID_KELAS][$detail_hari->ID_HARI][$detail_jp->URUTAN_MJP])) {
                $jadwal_kelas = $jadwal[$detail_kelas->ID_KELAS][$detail_hari->ID_HARI][$detail_jp->URUTAN_MJP];
                $mapel = '';
                foreach ($jadwal_kelas as $detail_jadwal) {
                    $mapel .= $detail_jadwal->NAMA_MAPEL;
                }
                $pdf->Cell(42, 6, $mapel, 'LR', 0, 'L');
            } else {
                $pdf->Cell(42, 6, "-", 'LR', 0, 'C');
            }
        }
        $pdf->Ln();

        $pdf->SetFont('Arial', 'I', 7);
        $pdf->Cell(23);
        foreach ($hari as $detail_hari) {
            if (isset($jadwal[$detail_kelas->ID_KELAS][$detail_hari->ID_HARI][$detail_jp->URUTAN_MJP])) {
                $jadwal_kelas = $jadwal[$detail_kelas->ID_KELAS][$detail_hari->ID_HARI][$detail_jp->URUTAN_MJP];
                $mapel = '';
                foreach ($jadwal_kelas as $detail_jadwal) {
                    $mapel .= $this->cetak->nama_peg_print($detail_jadwal);
                }
                $pdf->Cell(42, 4, $mapel, 'LR', 0, 'L');
            } else {
                $pdf->Cell(42, 4, "-", 'LR', 0, 'C');
            }
        }
        $pdf->Ln();

        $pdf->Cell(23);
        foreach ($hari as $detail_hari) {
            $pdf->Cell(42, 2, "", 'BLR', 0, 'L');
        }
        $pdf->Ln();
//        $pdf = $this->pdf_handler->wrap_row_table($pdf, $data_jadwal);
    }
//    $pdf->SetFont('Arial', '', 9);
//    foreach ($jam_pelajaran[$detail_kelas->ID_KELAS][$detail_hari->ID_HARI] as $detail_jp) {
//        $data_jadwal = array();
//        $data_jadwal[] = array('align' => 'C', 'width' => 23, 'height' => 8, 'text' => $detail_jp->MULAI_MJP . ' - ' . $detail_jp->AKHIR_MJP);
//        foreach ($hari as $detail_hari) {
//            if (isset($jadwal[$detail_kelas->ID_KELAS][$detail_hari->ID_HARI][$detail_jp->URUTAN_MJP])) {
//                $jadwal_kelas = $jadwal[$detail_kelas->ID_KELAS][$detail_hari->ID_HARI][$detail_jp->URUTAN_MJP];
//                $mapel = '';
//                foreach ($jadwal_kelas as $detail_jadwal) {
//                    $mapel .= $detail_jadwal->NAMA_MAPEL;
//                }
//                $data_jadwal[] = array('align' => 'L', 'width' => 42, 'height' => 8,'text' => $mapel);
//            } else {
//                $data_jadwal[] = array('align' => 'C', 'width' => 42, 'height' => 8, 'text' => '-');
//            }
//        }
//        $pdf = $this->pdf_handler->wrap_row_table($pdf, $data_jadwal);
//    }
}

$pdf->Output();
?>