<?php

ini_set('max_execution_time', 10000);

function next_char($char, $count) {
    for ($i = 0; $i < $count; $i++) {
        ++$char;
    }

    return $char;
}

$this->load->library('PhpOffice/PhpSpreadsheet/Spreadsheet');
$this->load->library('PhpOffice/PhpSpreadsheet/Writer/Xlsx');

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setCellValue('A1', 'Hello World !');

$writer = new Xlsx($spreadsheet);
$writer->save('hello world.xlsx');