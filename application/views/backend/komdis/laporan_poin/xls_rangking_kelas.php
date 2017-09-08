<?php

    function next_char($char, $count) {
        for ($i = 0; $i < $count; $i++) {
            ++$char;
        }

        return $char;
    }


$jumlah_kode_pelanggaran = count($kode);

//exit();

$this->load->library('PHPExcel/PHPExcel');

$objPHPExcel = new PHPExcel();

$objPHPExcel->getProperties()->setCreator("Rohmad Eko Wahyudi")
        ->setTitle("SIMAPES - KOMDIS");


// ====================================================================================================================================

$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->setCellValue('A1', 'DATA JUMLAH SISWA DAN POIN PELANGGARAN');
$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(14);
$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);

$last_column_kode_pelanggaran = $this->cetak->next_char('C', $jumlah_kode_pelanggaran * 2);
$objPHPExcel->getActiveSheet()->setCellValue('A3', 'No');
$objPHPExcel->getActiveSheet()->setCellValue('B3', 'Kelas');
$objPHPExcel->getActiveSheet()->setCellValue('C3', 'Kode Pelanggaran');
$objPHPExcel->getActiveSheet()->setCellValue($last_column_kode_pelanggaran . '3', 'Total');
$objPHPExcel->getActiveSheet()->mergeCells('C3:' . $this->cetak->next_char('C', ($jumlah_kode_pelanggaran * 2) - 1) . '3');
$objPHPExcel->getActiveSheet()->mergeCells('A3:A5');
$objPHPExcel->getActiveSheet()->mergeCells('B3:B5');
$objPHPExcel->getActiveSheet()->mergeCells($last_column_kode_pelanggaran . '3:' . $this->cetak->next_char('C', ($jumlah_kode_pelanggaran * 2) + 1) . '5');
$objPHPExcel->getActiveSheet()->getStyle('A3:' . $objPHPExcel->getActiveSheet()->getHighestColumn() . '5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('A3:' . $objPHPExcel->getActiveSheet()->getHighestColumn() . '5')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('A3:' . $objPHPExcel->getActiveSheet()->getHighestColumn() . '5')->getFont()->setBold(true);

$objPHPExcel->getActiveSheet()->mergeCells($last_column_kode_pelanggaran . '3:' . $last_column_kode_pelanggaran . '4');

$temp_column = 'B';
foreach ($kode as $detail) {
    $objPHPExcel->getActiveSheet()->setCellValue(($this->cetak->next_char($temp_column, 1)) . '4', $detail->KODE_KJP);
    $objPHPExcel->getActiveSheet()->mergeCells(($this->cetak->next_char($temp_column, 1)) . '4:' . ($this->cetak->next_char($temp_column, 2)) . '4');
    $temp_column = ($this->cetak->next_char($temp_column, 2));
}

$objPHPExcel->getActiveSheet()->getDefaultColumnDimension()->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(15);

$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);

$objPHPExcel->getActiveSheet()->setTitle('Data pelanggaran');

// ====================================================================================================================================

$objPHPExcel->createSheet();

$objPHPExcel->setActiveSheetIndex(1);

$objPHPExcel->getActiveSheet()->setCellValue('A1', 'DATA JENIS PELANGGARAN');
$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(14);
$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);

$row = 3;
foreach ($kode as $detail) {
    $objPHPExcel->getActiveSheet()->setCellValue('A' . $row, $detail->KODE_KJP);
    $objPHPExcel->getActiveSheet()->getStyle('A' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
    $objPHPExcel->getActiveSheet()->getStyle('A' . $row)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $objPHPExcel->getActiveSheet()->setCellValue('B' . $row, $detail->NAMA_KJP);
    $row++;
}

$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);

$objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(15);
$objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(20);

$objPHPExcel->getActiveSheet()->setTitle('Jenis Pelanggaran');

// ====================================================================================================================================

$objPHPExcel->setActiveSheetIndex(0);

// ====================================================================================================================================

header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="statistik_1.xls"');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
