<?php
$pdf = $this->fpdf;

$guru_terjadwal = array();
foreach ($guru as $detail_guru) {
    if(isset($jadwal[$detail_guru->ID_PEG])) 
        $guru_terjadwal[$detail_guru->ID_PEG] = $detail_guru->NAMA_PEG;
    else 
        continue;
    
    $pdf->AddPage("P", "A4");
    $pdf->SetAutoPageBreak(true, 0);
    
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 5, 'JADWAL MENGAJAR', 0, 0, 'C');
    $pdf->Ln();
    
    $pdf->Cell(0, 5, 'TAHUN AJARAN '.$this->session->userdata('NAMA_TA_ACTIVE'), 0, 0, 'C');
    $pdf->Ln(8);
    
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(0, 5, 'NAMA GURU: ' . $detail_guru->NIP_PEG.' - '.$this->cetak->nama_peg_print($detail_guru));
    $pdf->Ln(7);
    
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(10, 5, 'NO', 1, 0, 'C');
    $pdf->Cell(20, 5, 'HARI', 1, 0, 'C');
    $pdf->Cell(30, 5, 'JAM', 1, 0, 'C');
    $pdf->Cell(40, 5, 'KELAS', 1, 0, 'C');
    $pdf->Cell(90, 5, 'MATAPELAJARAN', 1, 0, 'C');
    $pdf->Ln();
    
    $pdf->SetFont('Arial', '', 10);
    $no = 1;
    foreach ($jadwal[$detail_guru->ID_PEG] as $detail_jadwal) {
        $pdf->Cell(10, 5, $no++, 1, 0, 'L');
        $pdf->Cell(20, 5, $detail_jadwal->NAMA_HARI, 1, 0, 'L');
        $pdf->Cell(30, 5, substr($detail_jadwal->MULAI_MJP, -8, 5).' - '. substr($detail_jadwal->AKHIR_MJP, -8, 5).' WIS', 1, 0, 'L');
        $pdf->Cell(40, 5, $detail_jadwal->NAMA_KELAS, 1, 0, 'L');
        $pdf->Cell(90, 5, $detail_jadwal->NAMA_MAPEL, 1, 0, 'L');
        $pdf->Ln();
    }
}

$pdf->Output();

?>