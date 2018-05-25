<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$pdf = $this->fpdf;

$GLOBALS['check_kartu'] = array();

if (is_array($siswa)) {
    if (count($siswa) == 0) {
        echo "<h1>TIDAK ADA DATA YANG AKAN DICETAK</h1>";
        exit();
    }

    foreach ($siswa as $detail) {
        $pdf = cetak($pdf, $detail, 'KARTU TES SUMATIF CAWU ' . $this->session->userdata('ID_CAWU_ACTIVE') . ' TA ' . $this->session->userdata('NAMA_TA_ACTIVE'));
    }
} else {
    $pdf = cetak($pdf, $siswa);
}

function cetak($pdf, $data, $title) {
    $CI = & get_instance();

    $margin_title = 16;
    $length = 90;
    $margin_content = 1;
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
    $pdf->Ln(4);

    $pdf->SetFont('Arial', '', $font);

    $pdf->Cell($margin_content);
    $pdf->Cell($header_content, 3, 'No. Absen', 0, 0, 'L');
    $pdf->SetFont('Arial', 'B', $font);
    $pdf->Cell($length, 3, ': ' . $data['AKAD_SISWA']['NO_ABSEN_AS'], 0, 0, 'L');
    $pdf->Ln();

    $pdf->SetFont('Arial', '', $font);

    $pdf->Cell($margin_content);
    $pdf->Cell($header_content, 3, 'N I S', 0, 0, 'L');
    $pdf->SetFont('Arial', 'B', $font);
    $pdf->Cell($length, 3, ': ' . $data['AKAD_SISWA']['NIS_SISWA'], 0, 0, 'L');
    $pdf->Ln();

    $pdf->SetFont('Arial', '', $font);

    $pdf->Cell($margin_content);
    $pdf->Cell($header_content, 3, 'Nama', 0, 0, 'L');
    $pdf->SetFont('Arial', 'B', $font);
    $pdf->Cell($length, 3, ': ' . $data['AKAD_SISWA']['NAMA_SISWA'], 0, 0, 'L');
    $pdf->Ln();

    $pdf->SetFont('Arial', '', $font);

    $pdf->Cell($margin_content);
    $pdf->Cell($header_content, 3, 'Kelas', 0, 0, 'L');
    $pdf->Cell($length, 3, ': ' . $data['AKAD_SISWA']['NAMA_KELAS'], 0, 0, 'L');
    $pdf->Ln();


    $pdf->Cell($margin_content);
    $pdf->Cell($header_content, 3, 'Orangtua', 0, 0, 'L');
    $pdf->Cell($length, 3, ': ' . $data['AKAD_SISWA']['AYAH_NAMA_SISWA'], 0, 0, 'L');
    $pdf->Ln();

    $pdf->Cell($margin_content);
    $pdf->Cell($header_content, 3, 'Alamat', 0, 0, 'L');
    $pdf->Cell($length, 3, ': ' . $data['AKAD_SISWA']['ALAMAT_SISWA'], 0, 0, 'L');
    $pdf->Ln();

    $pdf->Cell($margin_content);
    $pdf->Cell($header_content, 3, '', 0, 0, 'L');
    $pdf->Cell($length, 3, ' Kec. ' . $data['AKAD_SISWA']['NAMA_KEC'] . ', ' . str_replace('Kabupaten', 'Kab', $data['AKAD_SISWA']['NAMA_KAB']), 0, 0, 'L');
    $pdf->Ln();

    $pdf->Cell(1.5);
    $pdf->Cell(14, 3, 'I', 1, 0, 'C');
    $pdf->Cell(14, 3, 'II', 1, 0, 'C');
    $pdf->Cell(14, 3, 'III', 1, 0, 'C');
    $pdf->Cell(14, 3, 'IV', 1, 0, 'C');
    $pdf->Cell(14, 3, 'V', 1, 0, 'C');
    $pdf->Cell(14, 3, 'VI', 1, 0, 'C');
    $pdf->Ln();

    for ($aaa=0; $aaa < 6; $aaa++) { 
        if(!isset($data['DENAH'][$aaa]))
            $GLOBALS['check_kartu'][$data['AKAD_SISWA']['NAMA_KELAS']][$aaa][] = $data['AKAD_SISWA']['NIS_SISWA'].' - '.$data['AKAD_SISWA']['NAMA_SISWA'];
    }

    $pdf->Cell(1.5);
    $pdf->Cell(14, 4, isset($data['DENAH'][0]) ? $data['DENAH'][0]['RUANG']['NOMOR'] . ' ' . $data['DENAH'][0]['RUANG']['ID'] : '', 1, 0, 'C');
    $pdf->Cell(14, 4, isset($data['DENAH'][1]) ? $data['DENAH'][1]['RUANG']['NOMOR'] . ' ' . $data['DENAH'][1]['RUANG']['ID'] : '', 1, 0, 'C');
    $pdf->Cell(14, 4, isset($data['DENAH'][2]) ? $data['DENAH'][2]['RUANG']['NOMOR'] . ' ' . $data['DENAH'][2]['RUANG']['ID'] : '', 1, 0, 'C');
    $pdf->Cell(14, 4, isset($data['DENAH'][3]) ? $data['DENAH'][3]['RUANG']['NOMOR'] . ' ' . $data['DENAH'][3]['RUANG']['ID'] : '', 1, 0, 'C');
    $pdf->Cell(14, 4, isset($data['DENAH'][4]) ? $data['DENAH'][4]['RUANG']['NOMOR'] . ' ' . $data['DENAH'][4]['RUANG']['ID'] : '', 1, 0, 'C');
    $pdf->Cell(14, 4, isset($data['DENAH'][5]) ? $data['DENAH'][5]['RUANG']['NOMOR'] . ' ' . $data['DENAH'][5]['RUANG']['ID'] : '', 1, 0, 'C');
    $pdf->Ln();

    $pdf->SetFont('Arial', 'I', $font - 2);
    $pdf->Cell(0, 3, 'Kartu ini harus dibawa setiap mengikuti ujian', 0, 0, 'C');

    $posisi_x = 73;
    $posisi_y = 3;

    if (file_exists('files/siswa/' . $data['AKAD_SISWA']['NIS_SISWA'] . '.jpg'))
        $pdf->Image(base_url('files/siswa/' . $data['AKAD_SISWA']['NIS_SISWA'] . '.jpg'), $posisi_x, $posisi_y + 15, 15, 20, '', '');
    elseif (file_exists('files/siswa/' . $data['AKAD_SISWA']['ID_SISWA'] . '.png'))
        $pdf->Image(base_url('files/siswa/' . $data['AKAD_SISWA']['ID_SISWA'] . '.png'), $posisi_x, $posisi_y + 18, 15, 20, '', '');
    else {
        $posisi_x = 70;

        $pdf->Image(base_url('files/no_image.jpg'), $posisi_x, $posisi_y + 18, 20, 20, '', '');
    }

    return $pdf;
}

// foreach ($GLOBALS['check_kartu'] as $kelas => $detail1) {
//     echo 'KELAS: '.$kelas.'<br>';
//     foreach ($detail1 as $hari => $detail2) {
//         echo '&nbsp;&nbsp;&nbsp;&nbsp;HARI KE-'.($hari+1).'<br>';
//         foreach ($detail2 as $siswa) {
//             echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;SISWA: '.$siswa.'<br>';
//         }
//     }
//     echo "<hr>";
// }
// exit();
$pdf->Output();
