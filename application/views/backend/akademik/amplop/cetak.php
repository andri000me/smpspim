<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 * 
  $CI->CoreFonts = array('Arial', 'helvetica', 'times', 'symbol', 'zapfdingbats');
 * 
 */

$pdf = $this->fpdf;

foreach ($SISWA as $detail) {
    $pdf = cetak($pdf, $detail, $DATA);
    
    break;
}

function cetak($pdf, $siswa, $data) {
    $CI =& get_instance();

    $width = 210;
    $height = 297 / 4;
    $margin = 4;

    $pdf->SetMargins($margin + 6, $margin);
    $pdf->AddPage("L", array($width, $height));
    $pdf->SetAutoPageBreak(true, 0);
    
    $pdf->SetTextColor(2, 116, 54);
    $pdf->SetDrawColor(2, 116, 54);
    $pdf = $CI->cetak->header_yayasan($pdf, $margin);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetDrawColor(0, 0, 0);

    $pdf->SetFont('Arial', '', 9);
    
    $pdf->Cell(13, 5, 'Nomor');
    $pdf->Cell(0, 5, ': KM/         /A-II/PIM/'.(date('Y') - $CI->pengaturan->getTahunBerdiri()).'/'.$CI->date_format->toRomawi(date('n')).'/'. date('Y'));
    $pdf->Ln();
    
    $pdf->Cell(13, 5, 'Hal');
    $pdf->SetFont('Arial', 'I', 9);
    $pdf->MultiCell(80, 5, ': '.$data['HAL']);
    $pdf->Ln();
    
    $pdf->SetY($height * 0.6);
    $pdf->SetFont('Arial', '', 9);
    
    $pdf->Cell(120);
    $pdf->Cell(0, 5, 'Kepada Yang Terhormat,');
    $pdf->Ln(8);
    
    $pdf->Cell(120);
    $pdf->Cell(0, 5, 'Bapak '.$siswa->AYAH_NAMA_SISWA);
    $pdf->Ln();
    
    $pdf->Cell(120);
    $pdf->Cell(0, 5, 'Wali Murid Santri '.$siswa->NAMA_SISWA);
    $pdf->Ln();
    
    return $pdf;
}

$pdf->Output();
