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
$objPHPExcel->getActiveSheet()->setCellValue('A1', 'DATA JUMLAH SISWA DAN POIN PELANGGARAN SERTA RANGKING KELAS YANG BUTUH PENANGANAN INTENSIF (TAHAP PENGEMBANGAN - BELUM DAPAT DIGUNAKAN)');
$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(14);
$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);

$start_row_data = 6;
$end_row_data = 0;

$column_end_header = 'C';
$column_start_pelanggaran = $this->cetak->next_char($column_end_header, 1);
$last_column_kode_pelanggaran = $this->cetak->next_char($column_start_pelanggaran, $jumlah_kode_pelanggaran * 3);
$objPHPExcel->getActiveSheet()->setCellValue('A3', 'NO');
$objPHPExcel->getActiveSheet()->setCellValue('B3', 'KELAS');
$objPHPExcel->getActiveSheet()->setCellValue('C3', 'JUMLAH SISWA');
$objPHPExcel->getActiveSheet()->setCellValue($column_start_pelanggaran . '3', 'KODE PELANGGARAN');
$objPHPExcel->getActiveSheet()->setCellValue($last_column_kode_pelanggaran . '3', 'Total');
$objPHPExcel->getActiveSheet()->setCellValue($this->cetak->next_char($last_column_kode_pelanggaran, 3) . '3', 'RANGKING JUMLAH PELANGGARAN');
$objPHPExcel->getActiveSheet()->mergeCells($column_start_pelanggaran . '3:' . $this->cetak->next_char($column_start_pelanggaran, ($jumlah_kode_pelanggaran * 3) - 1) . '3');
$objPHPExcel->getActiveSheet()->mergeCells('A3:A5');
$objPHPExcel->getActiveSheet()->mergeCells('B3:B5');
$objPHPExcel->getActiveSheet()->mergeCells('C3:C5');
$objPHPExcel->getActiveSheet()->mergeCells($this->cetak->next_char($last_column_kode_pelanggaran, 3) . '3:' . $this->cetak->next_char($last_column_kode_pelanggaran, 3) . '5');
$objPHPExcel->getActiveSheet()->mergeCells($last_column_kode_pelanggaran . '3:' . $this->cetak->next_char($last_column_kode_pelanggaran, 2) . '4');

$DATA_KODE = array();
$temp_column = $column_end_header;
foreach ($kode as $detail) {
    $temp_column = $this->cetak->next_char($temp_column, 1);
    $objPHPExcel->getActiveSheet()->setCellValue($temp_column . '4', $detail->KODE_KJP);
    if ($detail->PELANGGARAN_ALPHA_MJK != NULL) {
        $objPHPExcel->getActiveSheet()->getStyle($temp_column . '4')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
        $objPHPExcel->getActiveSheet()->getStyle($temp_column . '4')->getFill()->getStartColor()->setARGB('FF62CB31');
    }
    $DATA_KODE[$detail->KODE_KJP] = $temp_column;
    $objPHPExcel->getActiveSheet()->mergeCells($temp_column . '4:' . ($this->cetak->next_char($temp_column, 2)) . '4');
    $temp_column = $this->cetak->next_char($temp_column, 2);
}

$temp_column = $column_end_header;
for ($colomn = 0; $colomn < ($jumlah_kode_pelanggaran + 1); $colomn++) {
    $temp_column = $this->cetak->next_char($temp_column, 1);
    $objPHPExcel->getActiveSheet()->setCellValue($temp_column . '5', '*');
    $objPHPExcel->getActiveSheet()->getColumnDimension($temp_column)->setWidth(8);
    $temp_column = $this->cetak->next_char($temp_column, 1);
    $objPHPExcel->getActiveSheet()->setCellValue($temp_column . '5', '**');
    $objPHPExcel->getActiveSheet()->getColumnDimension($temp_column)->setWidth(8);
    $temp_column = $this->cetak->next_char($temp_column, 1);
    $objPHPExcel->getActiveSheet()->setCellValue($temp_column . '5', '***');
    $objPHPExcel->getActiveSheet()->getColumnDimension($temp_column)->setWidth(8);
}

$temp_kelas = NULL;
$end_row_data = $start_row_data - 1;
foreach ($kelas as $detail) {
    if ($temp_kelas != $detail->NAMA_KELAS) {
        $end_row_data++;
        $objPHPExcel->getActiveSheet()->setCellValue('A' . $end_row_data, $end_row_data - 5);
        $objPHPExcel->getActiveSheet()->setCellValue('B' . $end_row_data, $detail->NAMA_KELAS);
        $objPHPExcel->getActiveSheet()->setCellValue('C' . $end_row_data, $detail->JUMLAH_SISWA_KELAS);
        $temp_kelas = $detail->NAMA_KELAS;
    }
    $objPHPExcel->getActiveSheet()->setCellValue($DATA_KODE[$detail->KODE_KJP] . $end_row_data, $detail->JUMLAH_PELANGGAR);
    $objPHPExcel->getActiveSheet()->setCellValue($this->cetak->next_char($DATA_KODE[$detail->KODE_KJP], 1) . $end_row_data, $detail->JUMLAH_PELANGGARAN);
    $objPHPExcel->getActiveSheet()->setCellValue($this->cetak->next_char($DATA_KODE[$detail->KODE_KJP], 2) . $end_row_data, $detail->JUMLAH_POIN);
}
$end_row_data = $end_row_data;

for ($i = $start_row_data; $i <= $end_row_data; $i++) {
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
    $objPHPExcel->getActiveSheet()->setCellValue($this->cetak->next_char($last_column_kode_pelanggaran, 3) . $i, '=RANK(' . $this->cetak->next_char($last_column_kode_pelanggaran, 1) . $i . ',' . $this->cetak->next_char($last_column_kode_pelanggaran, 1) . $start_row_data . ':' . $this->cetak->next_char($last_column_kode_pelanggaran, 1) . $end_row_data . ')');
}

$objPHPExcel->getActiveSheet()->setCellValue('A' . ($end_row_data + 1), 'TOTAL');
$objPHPExcel->getActiveSheet()->mergeCells('A' . ($end_row_data + 1) . ':B' . ($end_row_data + 1));

$objPHPExcel->getActiveSheet()->setCellValue('C' . ($end_row_data + 1), '=SUM(C' . $start_row_data . ':C' . $end_row_data . ')');
foreach ($DATA_KODE as $key => $column) {
    $objPHPExcel->getActiveSheet()->setCellValue($column . ($end_row_data + 1), '=SUM(' . $column . $start_row_data . ':' . $column . $end_row_data . ')');
    $objPHPExcel->getActiveSheet()->setCellValue($this->cetak->next_char($column, 1) . ($end_row_data + 1), '=SUM(' . $this->cetak->next_char($column, 1) . $start_row_data . ':' . $this->cetak->next_char($column, 1) . $end_row_data . ')');
    $objPHPExcel->getActiveSheet()->setCellValue($this->cetak->next_char($column, 2) . ($end_row_data + 1), '=SUM(' . $this->cetak->next_char($column, 2) . $start_row_data . ':' . $this->cetak->next_char($column, 2) . $end_row_data . ')');
}
$objPHPExcel->getActiveSheet()->setCellValue($this->cetak->next_char($column, 3) . ($end_row_data + 1), '=SUM(' . $this->cetak->next_char($column, 3) . $start_row_data . ':' . $this->cetak->next_char($column, 3) . $end_row_data . ')');
$objPHPExcel->getActiveSheet()->setCellValue($this->cetak->next_char($column, 4) . ($end_row_data + 1), '=SUM(' . $this->cetak->next_char($column, 4) . $start_row_data . ':' . $this->cetak->next_char($column, 4) . $end_row_data . ')');
$objPHPExcel->getActiveSheet()->setCellValue($this->cetak->next_char($column, 5) . ($end_row_data + 1), '=SUM(' . $this->cetak->next_char($column, 5) . $start_row_data . ':' . $this->cetak->next_char($column, 5) . $end_row_data . ')');


$start_row_keterangan = $end_row_data + 3;
$objPHPExcel->getActiveSheet()->setCellValue('A' . $start_row_keterangan, 'KETERANGAN');
$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->setCellValue('B' . ($start_row_keterangan + 1), '1. Kolom tanda * adalah jumlah pelanggar (siswa yang melanggar)');
$objPHPExcel->getActiveSheet()->setCellValue('B' . ($start_row_keterangan + 2), '2. Kolom tanda ** adalah jumlah pelanggaran');
$objPHPExcel->getActiveSheet()->setCellValue('B' . ($start_row_keterangan + 3), '3. Kolom tanda *** adalah jumlah poin');

foreach ($DATA_KODE as $key => $column) {
    $cell = $column . ($start_row_data - 1) . ':' . $column . ($end_row_data + 1);
    $objPHPExcel->getActiveSheet()->getStyle($cell)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    $objPHPExcel->getActiveSheet()->getStyle($cell)->getFill()->getStartColor()->setARGB('FFFFFF99');

    $cell = $this->cetak->next_char($column, 1) . ($start_row_data - 1) . ':' . $this->cetak->next_char($column, 1) . ($end_row_data + 1);
    $objPHPExcel->getActiveSheet()->getStyle($cell)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    $objPHPExcel->getActiveSheet()->getStyle($cell)->getFill()->getStartColor()->setARGB('FFCCCCCC');

    $cell = $this->cetak->next_char($column, 2) . ($start_row_data - 1) . ':' . $this->cetak->next_char($column, 2) . ($end_row_data + 1);
    $objPHPExcel->getActiveSheet()->getStyle($cell)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    $objPHPExcel->getActiveSheet()->getStyle($cell)->getFill()->getStartColor()->setARGB('FF66FFFF');
}
$cell = $this->cetak->next_char($column, 3) . ($start_row_data - 1) . ':' . $this->cetak->next_char($column, 3) . ($end_row_data + 1);
$objPHPExcel->getActiveSheet()->getStyle($cell)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$objPHPExcel->getActiveSheet()->getStyle($cell)->getFill()->getStartColor()->setARGB('FFFFFF99');

$cell = $this->cetak->next_char($column, 4) . ($start_row_data - 1) . ':' . $this->cetak->next_char($column, 4) . ($end_row_data + 1);
$objPHPExcel->getActiveSheet()->getStyle($cell)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$objPHPExcel->getActiveSheet()->getStyle($cell)->getFill()->getStartColor()->setARGB('FFCCCCCC');

$cell = $this->cetak->next_char($column, 5) . ($start_row_data - 1) . ':' . $this->cetak->next_char($column, 5) . ($end_row_data + 1);
$objPHPExcel->getActiveSheet()->getStyle($cell)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$objPHPExcel->getActiveSheet()->getStyle($cell)->getFill()->getStartColor()->setARGB('FF66FFFF');

$objPHPExcel->getActiveSheet()->getStyle('A3:'.$objPHPExcel->getActiveSheet()->getHighestColumn().($end_row_data + 1))->getBorders()->getOutline()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle($cell)->getBorders()->getOutline()->getColor()->setARGB('FF000000');

$objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(15);

$objPHPExcel->getActiveSheet()->getStyle('A3:' . $objPHPExcel->getActiveSheet()->getHighestColumn() . '5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('A3:' . $objPHPExcel->getActiveSheet()->getHighestColumn() . '5')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('A3:' . $objPHPExcel->getActiveSheet()->getHighestColumn() . '5')->getAlignment()->setWrapText(true);
$objPHPExcel->getActiveSheet()->getStyle('A3:' . $objPHPExcel->getActiveSheet()->getHighestColumn() . '5')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('A' . ($end_row_data + 1) . ':' . $objPHPExcel->getActiveSheet()->getHighestColumn() . ($end_row_data + 1))->getFont()->setBold(true);

$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
$objPHPExcel->getActiveSheet()->getColumnDimension($objPHPExcel->getActiveSheet()->getHighestColumn())->setWidth(15);
//$objPHPExcel->getActiveSheet()->getRowDimension('5')->setRowHeight(30);

$objPHPExcel->getActiveSheet()->setTitle('Data pelanggaran');

// ====================================================================================================================================

$objPHPExcel->createSheet();

$objPHPExcel->setActiveSheetIndex(1);

$objPHPExcel->getActiveSheet()->setCellValue('A1', 'DATA JENIS PELANGGARAN');
$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(14);
$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);

$end_row_data = 3;
foreach ($kode as $detail) {
    $objPHPExcel->getActiveSheet()->setCellValue('A' . $end_row_data, $detail->KODE_KJP);
    $objPHPExcel->getActiveSheet()->getStyle('A' . $end_row_data)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
    $objPHPExcel->getActiveSheet()->getStyle('A' . $end_row_data)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $objPHPExcel->getActiveSheet()->setCellValue('B' . $end_row_data, $detail->NAMA_KJP);
    $end_row_data++;
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
