<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Pdf_handler
 *
 * @author rohmad
 * 
 * 
 * 

  $data_header = array(
  array('align' => 'C', 'width' => 10, 'height' => 5,'text' => 'No'),
  array('align' => 'C', 'width' => 35, 'text' => 'Nama'),
  array('align' => 'C', 'width' => 25, 'text' => 'Kelas'),
  array('align' => 'C', 'width' => 40, 'text' => 'Wali Kelas'),
  array('align' => 'C', 'width' => 35, 'text' => 'Orang Tua'),
  array('align' => 'C', 'width' => 42, 'text' => 'Alamat'),
  array('align' => 'C', 'width' => 42, 'text' => 'Domisili'),
  array('align' => 'C', 'width' => 15, 'text' => 'Poin Tahun Lalu'),
  array('align' => 'C', 'width' => 15, 'text' => 'Poin Skrg'),
  array('align' => 'C', 'width' => 15, 'text' => 'Lari'),
  );

  $pdf->SetFont('Arial', 'B', 10);
  $pdf = $pdf->pdf_handler->wrap_row_table($pdf, $data_header);
 * 
 */
class Pdf_handler {

    //put your code here

    public function wrap_row_table($pdf, $data) {
        $position = array();
        $index = 0;
        $OFFSET_Y = 0;
        $WIDTH = 0;

        foreach ($data as $detail) {
            if ($index > 0)
                $pdf->SetXY($position[$index - 1]['TOP']['X'] + $width, $position[$index - 1]['TOP']['Y']);

            $position[$index]['TOP'] = array(
                'X' => $pdf->GetX(),
                'Y' => $pdf->GetY(),
            );

            $pdf->MultiCell($detail['width'], isset($detail['height']) ? $detail['height'] : 5, $detail['text'], 0, $detail['align']);

            $position[$index]['BOTTOM'] = array(
                'X' => $pdf->GetX(),
                'Y' => $pdf->GetY(),
            );

            if ($position[$index]['BOTTOM']['Y'] > $OFFSET_Y)
                $OFFSET_Y = $position[$index]['BOTTOM']['Y'];

            $index++;
            $width = $detail['width'];
        }

        $index = 0;
        $pdf->SetLineWidth(0.25);
        foreach ($position as $detail) {
            if ($index == 0) {
                $START_X = $detail['TOP']['X'];
                $START_Y = $detail['TOP']['Y'];
            }

            $X = $detail['TOP']['X'];
            $Y = $detail['TOP']['Y'];
            $pdf->Line($X, $Y, $X, $OFFSET_Y);

            $index++;
        }
        $X += $width;
        $pdf->Line($X, $Y, $X, $OFFSET_Y);
        $pdf->Line($START_X, $START_Y, $X, $START_Y);
        $pdf->Line($START_X, $OFFSET_Y, $X, $OFFSET_Y);

        $pdf->SetX($START_X);
        $pdf->SetY($OFFSET_Y);

        return $pdf;
    }

    public function cut_text($pdf, $text, $width) {
        while ($pdf->GetStringWidth($text) > $width) {
            $text = substr($text, 0, -1);
        }

        return $text;
    }

    function next_char($char, $count) {
        for ($i = 0; $i < $count; $i++) {
            ++$char;
        }

        return $char;
    }

}
