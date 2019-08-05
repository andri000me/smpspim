<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$this->load->library('barcode_handler');

$pdf = $this->fpdf;

if (is_array($siswa)) {
    if (count($siswa) == 0) {
        echo "<h1>TIDAK ADA DATA YANG AKAN DICETAK</h1>";
        exit();
    }
    foreach ($siswa as $detail) {
        $pdf = cetak($pdf, $detail, $title);
    }
} else {
    $pdf = cetak($pdf, $siswa);
}

function cetak($pdf, $data, $title) {
    $CI = & get_instance();

    $margin_title = 10;
    $length = 85.6;
    $width = 54;
    $margin_content = 22;
    $header_content = 13;
    $font = 8;

    $posisi_x = 3;
    $posisi_y = 2;

    $pdf->SetMargins(3, 3);
    $pdf->AddPage("P", array($length, $width));
    $pdf->SetAutoPageBreak(true, 0);

    $pdf->Image(base_url($CI->pengaturan->getLogo()), $posisi_x, $posisi_y, 9, 9, '', '');

    $pdf->SetLineWidth(0.05);
    $pdf->Line(1, 1, $width - 1, 1);
    $pdf->Line(1, $length - 1, $width - 1, $length - 1);
    $pdf->Line(1, 1, 1, $length - 1);
    $pdf->Line($width - 1, 1, $width - 1, $length - 1);

    $width -= 6;

    $pdf->SetFont('Arial', 'B', $font + 1);
    $pdf->Cell($margin_title + 3);
    $pdf->Cell($width, 3, 'PERGURUAN ISLAM', 0, 0, 'L');
    $pdf->Ln(4);

    $pdf->SetFont('Arial', 'B', $font + 3);
    $pdf->Cell($margin_title);
    $pdf->Cell($width, 3, "MATHALI'UL FALAH", 0, 0, 'L');
    $pdf->Ln();

    $pdf->SetLineWidth(0.30);
    $pdf->Line(2, 12, 52, 12);

    $pdf->Ln(4);

    $pdf->SetFont('Arial', 'B', $font + 2);
    $pdf->Cell($width, 3, $title, 0, 0, 'C');
    $pdf->Ln(5);

    $pdf->SetFont('Arial', '', $font + 1);
    $pdf->Cell($width, 3, $data->NAMA_SISWA, 0, 0, 'C');
    $pdf->Ln(4);

    $pdf->SetFont('Arial', '', $font);
    $pdf->Cell($width, 3, $data->NIS, 0, 0, 'C');
    $pdf->Ln(4);

    $pdf->SetFont('Arial', '', $font + 1);
    $pdf->Cell($width, 3, $data->NAMA_KELAS, 0, 0, 'C');
    $pdf->Ln(4);

    $file_name = 'files/barcode/' . $data->ID_SISWA . '.png';
    $text = $data->NIS;

    $CI->barcode_handler->create($file_name, $text);

    $pdf->Image(base_url('files/barcode/' . $data->ID_SISWA . '.png'), $posisi_x + 4, $posisi_y + 72, 40);

    if (file_exists('files/siswa/' . $data->NIS . '.jpg'))
        $pdf->Image(base_url('files/siswa/' . $data->NIS . '.jpg'), $posisi_x + 8, $posisi_y + 30, 30, 40, '', '');
    elseif (file_exists('files/siswa/' . $data->ID_SISWA . '.png'))
        $pdf->Image(base_url('files/siswa/' . $data->ID_SISWA . '.png'), $posisi_x + 8, $posisi_y + 30, 30, 40, '', '');
    else
        $pdf->Image(base_url('files/no_image.jpg'), $posisi_x + 8, $posisi_y + 30, 30, 40, '', '');

    return $pdf;
}

$pdf->Output();
