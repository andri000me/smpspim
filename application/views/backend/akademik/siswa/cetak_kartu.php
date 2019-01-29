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
        if (isset($title)) {
            $pdf = cetak($pdf, $detail, $title);
        } else {
            $pdf = cetak($pdf, $detail, 'KARTU PELAJAR');
            $pdf = cetak($pdf, $detail, 'KARTU SHOLAT JAMAAH');
        }
    }
} else {
    $pdf = cetak($pdf, $siswa);
}

function cetak($pdf, $data, $title) {
    $CI = & get_instance();

    $margin_title = 16;
    $length = 90;
    $margin_content = 22;
    $header_content = 13;
    $font = 8;

    $posisi_x = 4;
    $posisi_y = 3;

    $pdf->SetMargins(3, 3);
    $pdf->AddPage("L", array($length + 3, 53));
    $pdf->SetAutoPageBreak(true, 0);

    $pdf->SetLineWidth(0.05);
    $pdf->Line(1, 1, $length + 2, 1);
    $pdf->Line(1, 52, $length + 2, 52);
    $pdf->Line(1, 1, 1, 52);
    $pdf->Line($length + 2, 1, $length + 2, 52);

    $pdf->Image(base_url($CI->pengaturan->getLogo()), $posisi_x, $posisi_y, 13, 13, '', '');

    $pdf->SetFont('Arial', 'B', $font + 1);
    $pdf->Cell($margin_title);
    $pdf->Cell($length, 3, strtoupper($CI->pengaturan->getNamaYayasan()), 0, 0, 'L');
    $pdf->Ln();

    $pdf->SetFont('Arial', 'B', $font + 2);
    $pdf->Cell($margin_title);
    $pdf->Cell($length, 3, strtoupper($CI->pengaturan->getNamaLembaga()), 0, 0, 'L');
    $pdf->Ln();

    $pdf->SetFont('Arial', '', $font);
    $pdf->Cell($margin_title);
    $pdf->Cell($length, 3, strtoupper($CI->pengaturan->getDesa() . ' - ' . $CI->pengaturan->getKecamatan() . ' - ' . $CI->pengaturan->getKabupaten() . ' ' . $CI->pengaturan->getKodepos() . ' ' . $CI->pengaturan->getProvinsi()), 0, 0, 'L'); //  . ' - ' . $CI->pengaturan->getNegara()
    $pdf->Ln();

    $pdf->Cell($margin_title);
    $pdf->Cell($length, 3, 'TELP. ' . $CI->pengaturan->getTelp() . ' FAX. ' . $CI->pengaturan->getFax(), 0, 0, 'L');
    $pdf->Ln(5);

    $pdf->SetLineWidth(0.30);
    $pdf->Line(19, 15, 90, 15);

    $pdf->SetFont('Arial', 'B', $font + 2);
    $pdf->Cell($length, 3, $title, 0, 0, 'C');
    $pdf->Ln(5);

    $pdf->SetFont('Arial', '', $font);

    $pdf->Cell($margin_content);
    $pdf->Cell($header_content, 3, 'N I S', 0, 0, 'L');
    $pdf->SetFont('Arial', 'B', $font);
    $pdf->Cell($length, 3, ': ' . $data->NIS, 0, 0, 'L');
    $pdf->Ln();

    $pdf->SetFont('Arial', '', $font);

    $pdf->Cell($margin_content);
    $pdf->Cell($header_content, 3, 'Nama', 0, 0, 'L');
    $pdf->SetFont('Arial', 'B', $font);
    $pdf->Cell($length, 3, ': ' . $data->NAMA_SISWA, 0, 0, 'L');
    $pdf->Ln();

    $pdf->SetFont('Arial', '', $font);

    $pdf->Cell($margin_content);
    $pdf->Cell($header_content, 3, 'TTL', 0, 0, 'L');
    $pdf->Cell($length, 3, ': ' . $data->TEMPAT_LAHIR_SISWA . ', ' . $CI->date_format->to_print($data->TANGGAL_LAHIR_SISWA), 0, 0, 'L');
    $pdf->Ln();

    $pdf->Cell($margin_content);
    $pdf->Cell($header_content, 3, 'Orangtua', 0, 0, 'L');
    $pdf->Cell($length, 3, ': ' . $data->AYAH_NAMA_SISWA, 0, 0, 'L');
    $pdf->Ln();

    $pdf->Cell($margin_content);
    $pdf->Cell($header_content, 3, 'Alamat', 0, 0, 'L');
    $pdf->Cell($length, 3, ': ' . $data->ALAMAT_SISWA, 0, 0, 'L');
    $pdf->Ln();

    $pdf->Cell($margin_content);
    $pdf->Cell($header_content, 3, '', 0, 0, 'L');
    $pdf->Cell($length, 3, ' Kec. ' . $data->NAMA_KEC_SISWA . ', ' . str_replace('Kabupaten', 'Kab', $data->NAMA_KAB_SISWA), 0, 0, 'L');
    $pdf->Ln(8);

//    $pdf->Cell($header_content - 4, 3, 'TA', 0, 0, 'L');
//    $pdf->Cell($length, 3, ': ' . $CI->session->userdata('NAMA_TA_ACTIVE'), 0, 0, 'L');
//    $pdf->Ln();
//
//    $pdf->Cell($header_content - 4, 3, 'Kelas', 0, 0, 'L');
//    $pdf->Cell($length, 3, ': ' . $data->NAMA_KELAS, 0, 0, 'L');
//    $pdf->Ln();

    $file_name = 'files/barcode/' . $data->ID_SISWA . '.png';
    $text = $data->NIS;

    $CI->barcode_handler->create($file_name, $text);

    $pdf->Image(base_url('files/barcode/' . $data->ID_SISWA . '.png'), $posisi_x + 40, $posisi_y + 39, 40);

    if ($title == 'KARTU SHOLAT JAMAAH') {

        $pdf->Cell($header_content - 4, 3, 'TA', 0, 0, 'L');
        $pdf->Cell($length, 3, ': ' . $CI->session->userdata('NAMA_TA_ACTIVE'), 0, 0, 'L');
        $pdf->Ln();

        $pdf->Cell($header_content - 4, 3, 'Kelas', 0, 0, 'L');
        $pdf->Cell($length, 3, ': ' . $data->NAMA_KELAS, 0, 0, 'L');
        $pdf->Ln();

        $pdf->SetLineWidth(0.30);
        $pdf->Line($posisi_x, $posisi_y + 18, $posisi_x, $posisi_y + 18 + 20);
        $pdf->Line($posisi_x, $posisi_y + 18 + 5, $posisi_x + 20, $posisi_y + 18 + 5);
        $pdf->Line($posisi_x + 20, $posisi_y + 18, $posisi_x + 20, $posisi_y + 18 + 20);
        $pdf->Line($posisi_x, $posisi_y + 18, $posisi_x + 20, $posisi_y + 18);
        $pdf->Line($posisi_x, $posisi_y + 18 + 20, $posisi_x + 20, $posisi_y + 18 + 20);

        $pdf->SetXY($posisi_x, $posisi_y + 18);

        $pdf->SetFont('Arial', 'B', $font + 1);
        $pdf->Cell(20, 5, 'NO. ABSEN', 0, 0, 'C');
        $pdf->Ln();

        $pdf->SetFont('Arial', 'B', $font + 20);
        $pdf->Cell(20, 15, $data->NO_ABSEN_AS, 0, 0, 'C');
    } else {
        if (file_exists('files/siswa/' . $data->NIS . '.jpg'))
            $pdf->Image(base_url('files/siswa/' . $data->NIS . '.jpg'), $posisi_x, $posisi_y + 15, 20, 26.6, '', '');
        elseif (file_exists('files/siswa/' . $data->ID_SISWA . '.png'))
            $pdf->Image(base_url('files/siswa/' . $data->ID_SISWA . '.png'), $posisi_x, $posisi_y + 18, 20, 26.6, '', '');
        else
            $pdf->Image(base_url('files/no_image.jpg'), $posisi_x, $posisi_y + 18, 20, 26.6, '', '');
    }

    return $pdf;
}

$pdf->Output();
