<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$CI = & get_instance();
$CI->load->library('barcode_handler');

$pdf = $this->fpdf;

$temp_kelas = null;
$i = 0;
foreach ($data as $detail) {
    if ($detail->ID_KELAS != $temp_kelas) {
        $temp_kelas = $detail->ID_KELAS;
        $ganjil = TRUE;
        $posisi_x_ganjil = 115;
        $posisi_x_genap = 155;

        $pdf->AddPage("P", 'A4');
        $pdf->SetAutoPageBreak(true, 0);

        $pdf->SetFont('Arial', 'B', '12');

        $pdf->Cell(0, 5, 'CETAK UNTUK PEMOTRETAN KELAS ' . $detail->NAMA_KELAS, 0, 0, 'C');
        $pdf->Ln(8);

        $pdf->SetFont('Arial', 'B', '9');

        $pdf->Cell(20, 5, 'NO ABSEN', 1, 0, 'C');
        $pdf->Cell(25, 5, 'NIS', 1, 0, 'C');
        $pdf->Cell(85, 5, 'NAMA', 1, 0, 'C');
        $pdf->Cell(60, 5, 'BARCODE', 1, 0, 'C');
        $pdf->Ln();

        $pdf->SetFont('Arial', '', '9');
    }

    $pdf->Cell(20, 12, $detail->NO_ABSEN_AS, 'BT', 0, 'L');
    $pdf->Cell(25, 12, $detail->NIS_SISWA, 'BT', 0, 'L');
    $pdf->Cell(85, 12, $detail->NAMA_SISWA, 'BT', 0, 'L');
    $pdf->Cell(60, 12, '', 'BT', 0, 'L');
    $pdf->Ln();
    $posisi_y = $pdf->GetY();

    $file_name = 'files/barcode/' . $detail->ID_SISWA . '.png';
    $text = $detail->NIS_SISWA;

    $CI->barcode_handler->create($file_name, $text);

    $pdf->Image(base_url('files/barcode/' . $detail->ID_SISWA . '.png'), 150, $posisi_y - 11, 40); //24.5
    
//    $ganjil = !$ganjil;
//    $i++;
//    
//    if ($i == 10) break;
}

$pdf->Output();
