<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 * 
  $this->CoreFonts = array('courier', 'helvetica', 'times', 'symbol', 'zapfdingbats');
 * 
 */

$pdf = $this->fpdf;

$pdf->AddPage("P", $this->pengaturan->getUkuranF4());
$pdf->SetAutoPageBreak(true, 0);


$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 4, 'NILAI KELAS ', 0, 0, 'C');
$pdf->Ln(10);

$pdf->Output();
