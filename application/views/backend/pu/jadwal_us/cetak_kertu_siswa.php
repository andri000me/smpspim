<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 * 
  $this->CoreFonts = array('courier', 'helvetica', 'times', 'symbol', 'zapfdingbats');
 * 
 */

// PENGATURAN FOTO SISWA
$start_x = 13;
$start_y = 12;
$offset_x = 66;
$offset_y = 19;
$margin_top = 57;
$margin_right = 94;
$width_logo = 12;
$height_logo = 12;
$width_photo = 17;
$height_photo = 17;

$pdf = $this->fpdf;

function create_page($pdf) {
    $pdf->AddPage("P", "A4");
    $pdf->SetAutoPageBreak(true, 0);

    return $pdf;
}

function create_kop($pdf, $isset, $text, $bold = FALSE) {
    if($bold) $pdf->SetFont('Arial', 'B', 9);
    else $pdf->SetFont('Arial', '', 8);
    
    $pdf->Cell(17, 3, '', 'L', 0, 'C');
    $pdf->Cell(73, 3, $text, 'R', 0, 'L');
    $pdf->Cell(5);
    if($isset) {
    $pdf->Cell(17, 3, '', 'L', 0, 'C');
    $pdf->Cell(73, 3, $text, 'R', 0, 'L');
    }
    
    return $pdf;
}

function create_title($pdf, $isset, $text) {
    $pdf->Cell(90, 3, $text, 'RL', 0, 'C');
    $pdf->Cell(5);
    if($isset) {
    $pdf->Cell(90, 3, $text, 'RL', 0, 'C');
    }
    
    return $pdf;
}

function create_detail($pdf, $label, $value1, $value2) {
    $pdf->Cell(2, 3, '', 'L', 0, 'L');
    $pdf->Cell(14, 3, $label, 0, 0, 'L');
    $pdf->Cell(74, 3, (($label == '') ? '' : ': ').$value1, 'R', 0, 'L');
    $pdf->Cell(5);
    if($value2 != NULL) {
    $pdf->Cell(2, 3, '', 'L', 0, 'L');
    $pdf->Cell(14, 3, $label, 0, 0, 'L');
    $pdf->Cell(74, 3, (($label == '') ? '' : ': ').$value2, 'R', 0, 'L');
    }
    
    return $pdf;
}

$pdf = create_page($pdf);
$count_foto = 0;
$posisi_y = 0;
for ($i = 0;$i < count($siswa);$i++) {
    $isset = isset($siswa[$i + 1]);
    
    $posisi_y = $count_foto * $margin_top + $start_y;
    
    $pdf->Image(base_url($this->pengaturan->getLogo()), $start_x, $posisi_y, $width_logo, $height_logo, '', '');
    if($isset) $pdf->Image(base_url($this->pengaturan->getLogo()), $start_x + $margin_right, $posisi_y, $width_logo, $height_logo, '', '');
    
    $pdf->Image(base_url(($siswa[$i]['AKAD_SISWA']['FOTO_SISWA'] == NULL) ? 'files/no_image.jpg' : 'files/siswa/'.$siswa[$i]['AKAD_SISWA']['ID_SISWA'].'.png'), $start_x + $offset_x, $posisi_y + $offset_y, $width_photo, $height_photo, '', '');
    if($isset) $pdf->Image(base_url(($siswa[$i + 1]['AKAD_SISWA']['FOTO_SISWA'] == NULL) ? 'files/no_image.jpg' : 'files/siswa/'.$siswa[$i + 1]['AKAD_SISWA']['ID_SISWA'].'.png'), $start_x + $margin_right + $offset_x, $posisi_y + $offset_y, $width_photo, $height_photo, '', '');
    
    $pdf->Cell(90, 3, '', 'RTL', 0, 'C');
    $pdf->Cell(5);
    if($isset) {
    $pdf->Cell(90, 3, '', 'RTL', 0, 'C');
    }
    $pdf->Ln();
    
    $pdf = create_kop($pdf, $isset, strtoupper($this->pengaturan->getNamaYayasan()));
    $pdf->Ln();
    $pdf = create_kop($pdf, $isset, strtoupper($this->pengaturan->getNamaLembaga()), TRUE);
    $pdf->Ln();
    $pdf = create_kop($pdf, $isset, strtoupper($this->pengaturan->getDesa().' - '.$this->pengaturan->getKecamatan().' - '.$this->pengaturan->getKabupaten().' '.$this->pengaturan->getKodepos()));
    $pdf->Ln();
    $pdf = create_kop($pdf, $isset, 'TELP. '.$this->pengaturan->getTelp().' FAX. '.$this->pengaturan->getFax());
    $pdf->Ln();
    
    $pdf->Cell(90, 2, '', 'RTL', 0, 'C');
    $pdf->Cell(5);
    if($isset) {
    $pdf->Cell(90, 2, '', 'RTL', 0, 'C');
    }
    $pdf->Ln();
    
    $pdf->SetFont('Arial', 'B', 9);
    $pdf = create_title($pdf, $isset, 'KARTU TES SUMATIF CAWU '.$this->session->userdata('ID_CAWU_ACTIVE').' TA '.$this->session->userdata('NAMA_TA_ACTIVE'));
    $pdf->Ln();
    
    $pdf->Cell(90, 1, '', 'RL', 0, 'C');
    $pdf->Cell(5);
    if($isset) {
    $pdf->Cell(90, 1, '', 'RL', 0, 'C');
    }
    $pdf->Ln();
    
    $pdf->SetFont('Arial', '', 8);
    $pdf = create_detail($pdf, 'No. Absen', $siswa[$i]['AKAD_SISWA']['NO_ABSEN_AS'], $isset ? $siswa[$i + 1]['AKAD_SISWA']['NO_ABSEN_AS'] : NULL);
    $pdf->Ln();
    $pdf = create_detail($pdf, 'NIS', $siswa[$i]['AKAD_SISWA']['NIS_SISWA'], $isset ? $siswa[$i + 1]['AKAD_SISWA']['NIS_SISWA'] : NULL);
    $pdf->Ln();
    $pdf = create_detail($pdf, 'Nama', $siswa[$i]['AKAD_SISWA']['NAMA_SISWA'], $isset ? $siswa[$i + 1]['AKAD_SISWA']['NAMA_SISWA'] : NULL);
    $pdf->Ln();
    $pdf = create_detail($pdf, 'Kelas', $siswa[$i]['AKAD_SISWA']['NAMA_KELAS'], $isset ? $siswa[$i + 1]['AKAD_SISWA']['NAMA_KELAS'] : NULL);
    $pdf->Ln();
    $pdf = create_detail($pdf, 'Orang Tua', $siswa[$i]['AKAD_SISWA']['AYAH_NAMA_SISWA'], $isset ? $siswa[$i + 1]['AKAD_SISWA']['AYAH_NAMA_SISWA'] : NULL);
    $pdf->Ln();
    $pdf = create_detail($pdf, 'Alamat', $siswa[$i]['AKAD_SISWA']['ALAMAT_SISWA'], $isset ? $siswa[$i + 1]['AKAD_SISWA']['ALAMAT_SISWA'] : NULL);
    $pdf->Ln();
    $pdf = create_detail($pdf, '', 'Kec. '.$siswa[$i]['AKAD_SISWA']['NAMA_KEC'].' '. str_replace('Kabupaten', 'Kab.', $siswa[$i]['AKAD_SISWA']['NAMA_KAB']).' Prov. '.$siswa[$i]['AKAD_SISWA']['NAMA_PROV'], $isset ? 'Kec. '.$siswa[$i + 1]['AKAD_SISWA']['NAMA_KEC'].' '. str_replace('Kabupaten', 'Kab.', $siswa[$i + 1]['AKAD_SISWA']['NAMA_KAB']).' Prov. '.$siswa[$i + 1]['AKAD_SISWA']['NAMA_PROV'] : NULL);
    $pdf->Ln();
    
    $pdf->Cell(3, 3, '', 'L', 0, 'L');
    $pdf->Cell(14, 3, 'I', 1, 0, 'C');
    $pdf->Cell(14, 3, 'II', 1, 0, 'C');
    $pdf->Cell(14, 3, 'III', 1, 0, 'C');
    $pdf->Cell(14, 3, 'IV', 1, 0, 'C');
    $pdf->Cell(14, 3, 'V', 1, 0, 'C');
    $pdf->Cell(14, 3, 'VI', 1, 0, 'C');
    $pdf->Cell(3, 3, '', 'R', 0, 'C');
    $pdf->Cell(5);
    if($isset) {
    $pdf->Cell(3, 3, '', 'L', 0, 'L');
    $pdf->Cell(14, 3, 'I', 1, 0, 'C');
    $pdf->Cell(14, 3, 'II', 1, 0, 'C');
    $pdf->Cell(14, 3, 'III', 1, 0, 'C');
    $pdf->Cell(14, 3, 'IV', 1, 0, 'C');
    $pdf->Cell(14, 3, 'V', 1, 0, 'C');
    $pdf->Cell(14, 3, 'VI', 1, 0, 'C');
    $pdf->Cell(3, 3, '', 'R', 0, 'C');
    }
    $pdf->Ln();
    
//    $pdf->Cell(3, 4, '', 'L', 0, 'L');
//    $pdf->Cell(14, 4, $this->date_format->to_print_short($siswa[$i]['DENAH'][0]['TANGGAL']), 1, 0, 'C');
//    $pdf->Cell(14, 4, $this->date_format->to_print_short($siswa[$i]['DENAH'][1]['TANGGAL']), 1, 0, 'C');
//    $pdf->Cell(14, 4, $this->date_format->to_print_short($siswa[$i]['DENAH'][2]['TANGGAL']), 1, 0, 'C');
//    $pdf->Cell(14, 4, $this->date_format->to_print_short($siswa[$i]['DENAH'][3]['TANGGAL']), 1, 0, 'C');
//    $pdf->Cell(14, 4, $this->date_format->to_print_short($siswa[$i]['DENAH'][4]['TANGGAL']), 1, 0, 'C');
//    $pdf->Cell(14, 4, $this->date_format->to_print_short($siswa[$i]['DENAH'][5]['TANGGAL']), 1, 0, 'C');
//    $pdf->Cell(3, 4, '', 'R', 0, 'C');
//    $pdf->Cell(5);
//    if($isset) {
//    $pdf->Cell(3, 4, '', 'L', 0, 'L');
//    $pdf->Cell(14, 4, $this->date_format->to_print_short($siswa[$i + 1]['DENAH'][0]['TANGGAL']), 1, 0, 'C');
//    $pdf->Cell(14, 4, $this->date_format->to_print_short($siswa[$i + 1]['DENAH'][1]['TANGGAL']), 1, 0, 'C');
//    $pdf->Cell(14, 4, $this->date_format->to_print_short($siswa[$i + 1]['DENAH'][2]['TANGGAL']), 1, 0, 'C');
//    $pdf->Cell(14, 4, $this->date_format->to_print_short($siswa[$i + 1]['DENAH'][3]['TANGGAL']), 1, 0, 'C');
//    $pdf->Cell(14, 4, $this->date_format->to_print_short($siswa[$i + 1]['DENAH'][4]['TANGGAL']), 1, 0, 'C');
//    $pdf->Cell(14, 4, $this->date_format->to_print_short($siswa[$i + 1]['DENAH'][5]['TANGGAL']), 1, 0, 'C');
//    $pdf->Cell(3, 4, '', 'R', 0, 'C');
//    }
//    
//    $pdf->Ln();    
    
    $pdf->Cell(3, 4, '', 'L', 0, 'L');
    $pdf->Cell(14, 4, $siswa[$i]['DENAH'][0]['RUANG']['NOMOR'].' '.$siswa[$i]['DENAH'][0]['RUANG']['ID'], 1, 0, 'C');
    $pdf->Cell(14, 4, $siswa[$i]['DENAH'][1]['RUANG']['NOMOR'].' '.$siswa[$i]['DENAH'][1]['RUANG']['ID'], 1, 0, 'C');
    $pdf->Cell(14, 4, $siswa[$i]['DENAH'][2]['RUANG']['NOMOR'].' '.$siswa[$i]['DENAH'][2]['RUANG']['ID'], 1, 0, 'C');
    $pdf->Cell(14, 4, $siswa[$i]['DENAH'][3]['RUANG']['NOMOR'].' '.$siswa[$i]['DENAH'][3]['RUANG']['ID'], 1, 0, 'C');
    $pdf->Cell(14, 4, $siswa[$i]['DENAH'][4]['RUANG']['NOMOR'].' '.$siswa[$i]['DENAH'][4]['RUANG']['ID'], 1, 0, 'C');
    $pdf->Cell(14, 4, $siswa[$i]['DENAH'][5]['RUANG']['NOMOR'].' '.$siswa[$i]['DENAH'][5]['RUANG']['ID'], 1, 0, 'C');
    $pdf->Cell(3, 4, '', 'R', 0, 'C');
    $pdf->Cell(5);
    if($isset) {
    $pdf->Cell(3, 4, '', 'L', 0, 'L');
    $pdf->Cell(14, 4, $siswa[$i + 1]['DENAH'][0]['RUANG']['NOMOR'].' '.$siswa[$i + 1]['DENAH'][0]['RUANG']['ID'], 1, 0, 'C');
    $pdf->Cell(14, 4, $siswa[$i + 1]['DENAH'][1]['RUANG']['NOMOR'].' '.$siswa[$i + 1]['DENAH'][1]['RUANG']['ID'], 1, 0, 'C');
    $pdf->Cell(14, 4, $siswa[$i + 1]['DENAH'][2]['RUANG']['NOMOR'].' '.$siswa[$i + 1]['DENAH'][2]['RUANG']['ID'], 1, 0, 'C');
    $pdf->Cell(14, 4, $siswa[$i + 1]['DENAH'][3]['RUANG']['NOMOR'].' '.$siswa[$i + 1]['DENAH'][3]['RUANG']['ID'], 1, 0, 'C');
    $pdf->Cell(14, 4, $siswa[$i + 1]['DENAH'][4]['RUANG']['NOMOR'].' '.$siswa[$i + 1]['DENAH'][4]['RUANG']['ID'], 1, 0, 'C');
    $pdf->Cell(14, 4, $siswa[$i + 1]['DENAH'][5]['RUANG']['NOMOR'].' '.$siswa[$i + 1]['DENAH'][5]['RUANG']['ID'], 1, 0, 'C');
    $pdf->Cell(3, 4, '', 'R', 0, 'C');
    }
    $pdf->Ln();
    
    $pdf->SetFont('Arial', 'I', 8);
    $pdf = create_title($pdf, $isset, 'Kartu ini harus dibawa setiap mengikuti ujian');
    $pdf->Ln();
    
    $pdf->Cell(90, 1, '', 'RBL', 0, 'C');
    $pdf->Cell(5);
    if($isset) {
    $pdf->Cell(90, 1, '', 'RBL', 0, 'C');
    }
    $pdf->Ln(5);
    
    $count_foto++;
    
    if((($i + 2) % 10) == 0) {
        $pdf = create_page ($pdf);
        
        $count_foto = 0;
        $posisi_y = 0;
    }
    
    $i++;
}
            
$pdf->Output();