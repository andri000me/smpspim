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
$objPHPExcel->getActiveSheet()->setCellValue('A1', 'DATA JUMLAH SISWA DAN POIN PELANGGARAN SERTA RANGKING KELAS YANG BUTUH PENANGANAN INTENSIF');
$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(14);
$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);

$last_column_kode_pelanggaran = $this->cetak->next_char('C', $jumlah_kode_pelanggaran * 3);
$objPHPExcel->getActiveSheet()->setCellValue('A3', 'NO');
$objPHPExcel->getActiveSheet()->setCellValue('B3', 'KELAS');
$objPHPExcel->getActiveSheet()->setCellValue('C3', 'KODE PELANGGARAN');
$objPHPExcel->getActiveSheet()->setCellValue($last_column_kode_pelanggaran . '3', 'Total');
$objPHPExcel->getActiveSheet()->setCellValue($this->cetak->next_char($last_column_kode_pelanggaran, 3) . '3', 'RANGKING JUMLAH PELANGGARAN');
$objPHPExcel->getActiveSheet()->mergeCells('C3:' . $this->cetak->next_char('C', ($jumlah_kode_pelanggaran * 3) - 1) . '3');
$objPHPExcel->getActiveSheet()->mergeCells('A3:A5');
$objPHPExcel->getActiveSheet()->mergeCells('B3:B5');
$objPHPExcel->getActiveSheet()->mergeCells($this->cetak->next_char($last_column_kode_pelanggaran, 3) . '3:' . $this->cetak->next_char($last_column_kode_pelanggaran, 3) . '5');
$objPHPExcel->getActiveSheet()->mergeCells($last_column_kode_pelanggaran . '3:' . $this->cetak->next_char($last_column_kode_pelanggaran, 2) . '4');

$DATA_KODE = array();
$temp_column = 'B';
foreach ($kode as $detail) {
    $temp_column = $this->cetak->next_char($temp_column, 1);
    $objPHPExcel->getActiveSheet()->setCellValue($temp_column . '4', $detail->KODE_KJP);
    $DATA_KODE[$detail->KODE_KJP] = $temp_column;
    $objPHPExcel->getActiveSheet()->mergeCells($temp_column . '4:' . ($this->cetak->next_char($temp_column, 2)) . '4');
    $temp_column = $this->cetak->next_char($temp_column, 2);
}

$temp_column = 'B';
for ($colomn = 0; $colomn < ($jumlah_kode_pelanggaran + 1); $colomn++) {
    $temp_column = $this->cetak->next_char($temp_column, 1);
    $objPHPExcel->getActiveSheet()->setCellValue($temp_column . '5', 'JUMLAH PELANGGAR');
    $objPHPExcel->getActiveSheet()->getColumnDimension($temp_column)->setWidth(15);
    $temp_column = $this->cetak->next_char($temp_column, 1);
    $objPHPExcel->getActiveSheet()->setCellValue($temp_column . '5', 'JUMLAH PELANGGARAN');
    $objPHPExcel->getActiveSheet()->getColumnDimension($temp_column)->setWidth(15);
    $temp_column = $this->cetak->next_char($temp_column, 1);
    $objPHPExcel->getActiveSheet()->setCellValue($temp_column . '5', 'JUMLAH POIN');
    $objPHPExcel->getActiveSheet()->getColumnDimension($temp_column)->setWidth(15);
}

$temp_kelas = NULL;
$row = 5;
foreach ($kelas as $detail) {
    if ($temp_kelas != $detail->NAMA_KELAS) {
        $row++;
        $objPHPExcel->getActiveSheet()->setCellValue('A' . $row, $row - 5);
        $objPHPExcel->getActiveSheet()->setCellValue('B' . $row, $detail->NAMA_KELAS);
        $temp_kelas = $detail->NAMA_KELAS;
    }
    $objPHPExcel->getActiveSheet()->setCellValue($DATA_KODE[$detail->KODE_KJP] . $row, $detail->JUMLAH_PELANGGAR);
    $objPHPExcel->getActiveSheet()->setCellValue($this->cetak->next_char($DATA_KODE[$detail->KODE_KJP], 1) . $row, $detail->JUMLAH_PELANGGARAN);
    $objPHPExcel->getActiveSheet()->setCellValue($this->cetak->next_char($DATA_KODE[$detail->KODE_KJP], 2) . $row, $detail->JUMLAH_POIN);
}

for ($i = 6; $i <= $row; $i++) {
    $formula_pelanggar = '';
    $formula_pelanggaran = '';
    $formula_poin = '';

    $j = 1;
    foreach ($DATA_KODE as $key => $column) {
        $formula_pelanggar .= $column . $i . ($j == count($DATA_KODE) ? '' : '+');
        $formula_pelanggaran .= $this->cetak->next_char($column, 1) . $i . ($j == count($DATA_KODE) ? '' : '+');
        $formula_poin .= $this->cetak->next_char($column, 2) . $i . ($j == count($DATA_KODE) ? '' : '+');
        $j++;
    }

    $objPHPExcel->getActiveSheet()->setCellValue($last_column_kode_pelanggaran . $i, '=' . $formula_pelanggar);
    $objPHPExcel->getActiveSheet()->setCellValue($this->cetak->next_char($last_column_kode_pelanggaran, 1) . $i, '=' . $formula_pelanggaran);
    $objPHPExcel->getActiveSheet()->setCellValue($this->cetak->next_char($last_column_kode_pelanggaran, 2) . $i, '=' . $formula_poin);
    $objPHPExcel->getActiveSheet()->setCellValue($this->cetak->next_char($last_column_kode_pelanggaran, 3) . $i, '=RANK(' . $this->cetak->next_char($last_column_kode_pelanggaran, 1) . $i . ',' . $this->cetak->next_char($last_column_kode_pelanggaran, 1) . '6:' . $this->cetak->next_char($last_column_kode_pelanggaran, 1) . $row . ')');
}

$objPHPExcel->getActiveSheet()->setCellValue('A' . ($row + 1), 'TOTAL');
$objPHPExcel->getActiveSheet()->mergeCells('A' . ($row + 1) . ':B' . ($row + 1));

foreach ($DATA_KODE as $key => $column) {
    $objPHPExcel->getActiveSheet()->setCellValue($column . ($row + 1), '=SUM(' . $column . '6:' . $column . $row . ')');
    $objPHPExcel->getActiveSheet()->setCellValue($this->cetak->next_char($column, 1) . ($row + 1), '=SUM(' . $this->cetak->next_char($column, 1) . '6:' . $this->cetak->next_char($column, 1) . $row . ')');
    $objPHPExcel->getActiveSheet()->setCellValue($this->cetak->next_char($column, 2) . ($row + 1), '=SUM(' . $this->cetak->next_char($column, 2) . '6:' . $this->cetak->next_char($column, 2) . $row . ')');
}
$objPHPExcel->getActiveSheet()->setCellValue($this->cetak->next_char($column, 3) . ($row + 1), '=SUM(' . $this->cetak->next_char($column, 3) . '6:' . $this->cetak->next_char($column, 3) . $row . ')');
$objPHPExcel->getActiveSheet()->setCellValue($this->cetak->next_char($column, 4) . ($row + 1), '=SUM(' . $this->cetak->next_char($column, 4) . '6:' . $this->cetak->next_char($column, 4) . $row . ')');
$objPHPExcel->getActiveSheet()->setCellValue($this->cetak->next_char($column, 5) . ($row + 1), '=SUM(' . $this->cetak->next_char($column, 5) . '6:' . $this->cetak->next_char($column, 5) . $row . ')');


$objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(15);

$objPHPExcel->getActiveSheet()->getStyle('A3:' . $objPHPExcel->getActiveSheet()->getHighestColumn() . '5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('A3:' . $objPHPExcel->getActiveSheet()->getHighestColumn() . '5')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('C5:' . $objPHPExcel->getActiveSheet()->getHighestColumn() . '5')->getAlignment()->setWrapText(true);
$objPHPExcel->getActiveSheet()->getStyle($objPHPExcel->getActiveSheet()->getHighestColumn() . '3')->getAlignment()->setWrapText(true);
$objPHPExcel->getActiveSheet()->getStyle('A3:' . $objPHPExcel->getActiveSheet()->getHighestColumn() . '5')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('A' . ($row + 1) . ':' . $objPHPExcel->getActiveSheet()->getHighestColumn() . ($row + 1))->getFont()->setBold(true);

$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
$objPHPExcel->getActiveSheet()->getColumnDimension($objPHPExcel->getActiveSheet()->getHighestColumn())->setWidth(15);
$objPHPExcel->getActiveSheet()->getRowDimension('5')->setRowHeight(30);

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
