<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$this->load->library('barcode_handler');

$pdf = $this->fpdf;

foreach ($siswa as $detail) {
    $pdf = cetak($pdf, $detail['siswa'], $detail['tagihan']);
}

function cetak($pdf, $data, $tagihan) {
    $CI = & get_instance();

    $margin_title = 17;
    $length = 90;
    $margin_content = 22;
    $header_content = 13;
    $font = 8;

    $posisi_x = 4;
    $posisi_y = 3;

    $pdf->SetMargins(3, 3);
    $pdf->AddPage("P", array($length + 3, 106));
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
    $pdf->Cell($length, 3, 'KARTU PEMBAYARAN KHOIROT SYAHRIYAH', 0, 0, 'C');
    $pdf->Ln(5);

    $pdf->SetFont('Arial', '', $font);

    $pdf->Cell($margin_content);
    $pdf->Cell($header_content, 3, 'N I S', 0, 0, 'L');
    $pdf->SetFont('Arial', 'B', $font);
    $pdf->Cell($length, 3, ': ' . $data->NIS_SISWA, 0, 0, 'L');
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
    $pdf->Cell($length, 3, ' Kec. ' . $data->NAMA_KEC . ', ' . str_replace('Kabupaten', 'Kab', $data->NAMA_KAB), 0, 0, 'L');
    $pdf->Ln(4.5);

    $pdf->Cell($header_content - 4, 3, 'TA', 0, 0, 'L');
    $pdf->Cell($length, 3, ': ' . $CI->session->userdata('NAMA_TA_ACTIVE'), 0, 0, 'L');
    $pdf->Ln();

    $pdf->Cell($header_content - 4, 3, 'Kelas', 0, 0, 'L');
    $pdf->Cell($length, 3, ': ' . $data->NAMA_KELAS, 0, 0, 'L');
    $pdf->Ln();

//    if ($data->FOTO_SISWA == NULL || !file_exists('files/siswa/' . $data->ID_SISWA . '.png'))
//        $pdf->Image(base_url('files/no_image.jpg'), $posisi_x, $posisi_y + 20, 20, 20, '', '');
//    else
//        $pdf->Image(base_url('files/siswa/' . $data->ID_SISWA . '.png'), $posisi_x, $posisi_y + 20, 20, 20, '', '');

    $file_name = 'files/barcode/' . $data->ID_SISWA . '.png';
    $text = $data->NIS_SISWA;

    $CI->barcode_handler->create($file_name, $text);

    $pdf->Image(base_url('files/barcode/' . $data->ID_SISWA . '.png'), $posisi_x + 46, $posisi_y + 39, 40);

    $pdf->SetY($posisi_y + 51);

    $length_parsing = ($length / 3) - 2.5;
    $nominal = 0;
    $j = 0;
    $k = 0;
    $index = 0;
    for ($i = 1; $i <= count($tagihan); $i++) {
        $index = $j + 4*$k;
        $k++;
        if($k == 3) {
            $j++;
            $k = 0;
        }
        
        $nominal = $tagihan[$index]->NOMINAL_DT;
        $pdf->Cell($length_parsing, 4, str_replace('Syahriyah ', '', $tagihan[$index]->NAMA_DT), 'LTR', 0, 'L');

        if ($i % 3 == 0) {
            $pdf->Ln();
            $pdf->Cell($length_parsing, 8, '', 'LBR', 0, 'L');
            $pdf->Cell(2);
            $pdf->Cell($length_parsing, 8, '', 'LBR', 0, 'L');
            $pdf->Cell(2);
            $pdf->Cell($length_parsing, 8, '', 'LBR', 0, 'L');
            $pdf->Ln();
        } else {
            $pdf->Cell(2, 5, '', 0, 0, 'L');
        }
    }

    $pdf->SetLineWidth(0.30);
    $pdf->Line($posisi_x, $posisi_y + 18, $posisi_x, $posisi_y + 18 + 19);
    $pdf->Line($posisi_x, $posisi_y + 18 + 5, $posisi_x + 20, $posisi_y + 18 + 5);
    $pdf->Line($posisi_x + 20, $posisi_y + 18, $posisi_x + 20, $posisi_y + 18 + 19);
    $pdf->Line($posisi_x, $posisi_y + 18, $posisi_x + 20, $posisi_y + 18);
    $pdf->Line($posisi_x, $posisi_y + 18 + 19, $posisi_x + 20, $posisi_y + 18 + 19);

    $pdf->SetXY($posisi_x, $posisi_y + 18);

    $pdf->SetFont('Arial', 'B', $font + 1);
    $pdf->Cell(20, 5, 'JENJANG', 0, 0, 'C');
    $pdf->Ln();

    $pdf->SetFont('Arial', 'B', $font + 20);
    $pdf->Cell(20, 15, $data->DEPT_TINGK, 0, 0, 'C');

    $pdf->SetY($posisi_y + 43);

    $pdf->Ln(2);
    $pdf->SetFont('Arial', 'B', $font);
    $pdf->Cell($length, 3, 'Khoirot tiap bulan: Rp. ' . number_format($nominal, 0, '.', '.'), 0, 0, 'L');
    $pdf->Ln();

    $pdf->SetY($posisi_y + 99.5);
    $pdf->SetFont('Arial', 'B', $font);
    $pdf->Cell($length, 3, 'Pembayaran paling lambat tanggal 10 setiap bulan', 0, 0, 'C');
    $pdf->Ln();

    return $pdf;
}

$pdf->Output();
