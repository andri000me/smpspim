<?php

$pdf = $this->fpdf;

$temp_ID_TINGK = NULL;
$temp_JK_SISWA = NULL;

foreach ($siswa as $detail) {
    if(($detail->JK_SISWA != $temp_JK_SISWA) || ($detail->ID_TINGK != $temp_ID_TINGK)) {
        if(($temp_ID_TINGK != NULL) || ($temp_JK_SISWA != NULL)) {
            // $pdf->Ln(10);    
            // $pdf->Cell(100);
            // $pdf->Cell(0, 5, 'Penilai', 0, 0, 'L');
            // $pdf->Ln(15);    
            // $pdf->Cell(100);
            // $pdf->Cell(0, 5, '-----------------------------', 0, 0, 'L');
        }

        $pdf->AddPage("P", "A4");
        $pdf->SetAutoPageBreak(true, 0);

        $temp_JK_SISWA = $detail->JK_SISWA;
        $temp_ID_TINGK = $detail->ID_TINGK;

        if ($temp_JK_SISWA == 'L') $nama_jk = 'BANIN';
        else $nama_jk = 'BANAT';

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 5, 'PANITIA UJIAN', 0, 0, 'C');
        $pdf->Ln();

        $pdf->Cell(0, 5, 'BLANGKO PENILAIAN LEMBAR TES PENERIMAAN SISWA BARU', 0, 0, 'C');
        $pdf->Ln();

        $pdf->Cell(0, 5, 'TAHUN '.$this->pengaturan->getTahunPSBAwal(), 0, 0, 'C');
        $pdf->Ln(10);

        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(80, 5, 'MATERI: .....................................', 0, 0, 'L');
        $pdf->Cell(0, 5, 'JENJANG/TINGKAT: '.$detail->KETERANGAN_TINGK.' [ '.$nama_jk.' ]', 0, 0, 'R');
        $pdf->Ln();

        $no = 1;
        $pdf->Cell(10, 5, 'No', 1, 0, 'C');
        $pdf->Cell(40, 5, 'No. UM', 1, 0, 'C');
        $pdf->Cell(110, 5, 'Nama', 1, 0, 'C');
        $pdf->Cell(30, 5, 'Nilai', 1, 0, 'C');
        $pdf->Ln();
    }

    $pdf->Cell(10, 5, $no++, 1, 0, 'C');
    $pdf->Cell(40, 5, $this->pengaturan->getKodeUM($detail), 1, 0, 'L');
    $pdf->Cell(110, 5, $detail->NAMA_SISWA, 1, 0, 'L');
    $pdf->Cell(30, 5, '', 1, 0, 'C');
    $pdf->Ln();
}

$pdf->Output();

?>