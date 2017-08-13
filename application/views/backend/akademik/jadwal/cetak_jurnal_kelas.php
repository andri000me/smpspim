<?php

$pdf = $this->fpdf;

$pdf->AddPage("P", "A4");
$pdf->SetAutoPageBreak(true, 0);

foreach ($hari as $detail_hari) {

    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(0, 5, 'Hari/Tanggal: ' . $detail_hari->NAMA_HARI . ', ................................................20.........', 0, 0, 'L');
    $pdf->Ln();

    $pdf->Cell(10, 5, 'Jam', 'RTL', 0, 'C');
    $pdf->Cell(40, 10, 'Materi Pelajaran', 1, 0, 'C');
    $pdf->Cell(30, 10, 'Nama Guru', 1, 0, 'C');
    $pdf->Cell(40, 10, 'Pokok Bahasan', 1, 0, 'C');
    $pdf->Cell(15, 5, 'Jumlah', 'RTL', 0, 'C');
    $pdf->Cell(35, 5, 'Tandatangan', 1, 0, 'C');
    $pdf->Cell(20, 10, 'Keterangan', 1, 0, 'C');
    $pdf->Cell(0, 5, '');
    $pdf->Ln();

    $pdf->Cell(10, 5, 'ke', 'RBL', 0, 'C');
    $pdf->Cell(110);
    $pdf->Cell(15, 5, 'murid', 'RBL', 0, 'C');
    $pdf->Cell(35 / 2, 5, 'Guru', 1, 0, 'C');
    $pdf->Cell(35 / 2, 5, 'Wakil', 1, 0, 'C');
    $pdf->Ln();

    for ($no = 0; $no < 8; $no++) {
        $pdf->Cell(10, 7, $no + 1, 1, 0, 'C');
        $pdf->Cell(40, 7, '', 1);
        $pdf->Cell(30, 7, '', 1);
        $pdf->Cell(40, 7, '', 1);
        $pdf->Cell(15, 7, '', 1);
        $pdf->Cell(35, 7, '', 1);
        $pdf->Cell(20, 7, '', 1);
        $pdf->Ln();
    }
    
    $pdf->Cell(0, 5, 'Catatan penting:');
    $pdf->Ln();
    
    $pdf->MultiCell(0, 5, '...............................................................................................................................................................................................................................................................................................................................................................................................');
    $pdf->Ln();
}

$pdf->Output();
?>