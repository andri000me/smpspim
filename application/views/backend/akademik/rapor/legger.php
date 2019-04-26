<?php

ini_set('max_execution_time', 10000);

function next_char($char, $count) {
    for ($i = 0; $i < $count; $i++) {
        ++$char;
    }

    return $char;
}

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\WriterXlsx;

$spreadsheet = new Spreadsheet();

$sheet = $spreadsheet->getActiveSheet();
$sheet->setCellValue('A1', 'TEST');
$sheet->setCellValue('A2', 'Gipsy Avenger');
$sheet->setCellValue('A3', 'Striker Eureka');

$writer = new Xlsx($spreadsheet);

$filename = 'legger_nilai_' . date('Y_m_d_H_i_s');

header('Content-Type: application/vnd.ms-excel'); // generate excel file
header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
header('Cache-Control: max-age=0');

$writer->save('php://output'); // download file 