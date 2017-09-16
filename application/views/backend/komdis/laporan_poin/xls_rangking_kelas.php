<?php

$DATA_SHEET = array();
$NUMBER_SHEET = 0;

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


// ===========================================================================================================================================================================

$objPHPExcel->setActiveSheetIndex($NUMBER_SHEET);

$DATA_SHEET[$NUMBER_SHEET] = array(
    'title' => 'menu',
    'text' => 'Menu'
);

// ===========================================================================================================================================================================
$objPHPExcel->createSheet();
$NUMBER_SHEET++;
$objPHPExcel->setActiveSheetIndex($NUMBER_SHEET);

$DATA_SHEET[$NUMBER_SHEET] = array(
    'title' => 'tahunan',
    'text' => 'Tahunan'
);

$objPHPExcel->getActiveSheet()->setCellValue('A1', $DATA_SHEET[0]['text']);
$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(16);
$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('A1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$objPHPExcel->getActiveSheet()->getStyle('A1')->getFill()->getStartColor()->setARGB('FF62CB31');
$objPHPExcel->getActiveSheet()->mergeCells('A1:B1');
$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$objPHPExcel->getActiveSheet()->getCell('A1')->getHyperlink()->setUrl("sheet://'" . $DATA_SHEET[0]['title'] . "'!A1");

$start_row_data = 6;
$end_row_data = 0;

$column_end_header = 'E';
$column_jumlah_siswa = 'D';
$column_porsentase_siswa = 'E';
$column_start_pelanggaran = $this->cetak->next_char($column_end_header, 1);
$last_column_kode_pelanggaran = $this->cetak->next_char($column_start_pelanggaran, $jumlah_kode_pelanggaran * 3);

$objPHPExcel->getActiveSheet()->setCellValue($column_start_pelanggaran . '1', 'DATA JUMLAH SISWA DAN POIN PELANGGARAN SERTA RANGKING KELAS YANG BUTUH PENANGANAN INTENSIF');
$objPHPExcel->getActiveSheet()->getStyle($column_start_pelanggaran . '1')->getFont()->setSize(14);
$objPHPExcel->getActiveSheet()->getStyle($column_start_pelanggaran . '1')->getFont()->setBold(true);

$objPHPExcel->getActiveSheet()->setCellValue('A3', 'NO');
$objPHPExcel->getActiveSheet()->mergeCells('A3:A5');
$objPHPExcel->getActiveSheet()->setCellValue('B3', 'JENJANG');
$objPHPExcel->getActiveSheet()->mergeCells('B3:B5');
$objPHPExcel->getActiveSheet()->setCellValue('C3', 'KELAS');
$objPHPExcel->getActiveSheet()->mergeCells('C3:C5');
$objPHPExcel->getActiveSheet()->setCellValue('D3', 'JUMLAH SISWA');
$objPHPExcel->getActiveSheet()->mergeCells('D3:D5');
$objPHPExcel->getActiveSheet()->setCellValue('E3', '% KELAS');
$objPHPExcel->getActiveSheet()->mergeCells('E3:E5');

$objPHPExcel->getActiveSheet()->setCellValue($column_start_pelanggaran . '3', 'KODE PELANGGARAN');
$objPHPExcel->getActiveSheet()->mergeCells($column_start_pelanggaran . '3:' . $this->cetak->next_char($column_start_pelanggaran, ($jumlah_kode_pelanggaran * 3) - 1) . '3');

$objPHPExcel->getActiveSheet()->setCellValue($last_column_kode_pelanggaran . '3', 'UMUM');
$objPHPExcel->getActiveSheet()->mergeCells($last_column_kode_pelanggaran . '3:' . $this->cetak->next_char($last_column_kode_pelanggaran, 6) . '3');
$objPHPExcel->getActiveSheet()->setCellValue($last_column_kode_pelanggaran . '4', 'JUMLAH');
$objPHPExcel->getActiveSheet()->mergeCells($last_column_kode_pelanggaran . '4:' . $this->cetak->next_char($last_column_kode_pelanggaran, 2) . '4');
$objPHPExcel->getActiveSheet()->setCellValue($this->cetak->next_char($last_column_kode_pelanggaran, 3) . '4', 'PORSENTASE');
$objPHPExcel->getActiveSheet()->mergeCells($this->cetak->next_char($last_column_kode_pelanggaran, 3) . '4:' . $this->cetak->next_char($last_column_kode_pelanggaran, 5) . '4');
$objPHPExcel->getActiveSheet()->setCellValue($this->cetak->next_char($last_column_kode_pelanggaran, 6) . '4', 'RANGKING');
$objPHPExcel->getActiveSheet()->mergeCells($this->cetak->next_char($last_column_kode_pelanggaran, 6) . '4:' . $this->cetak->next_char($last_column_kode_pelanggaran, 6) . '5');

$objPHPExcel->getActiveSheet()->setCellValue($this->cetak->next_char($last_column_kode_pelanggaran, 7) . '3', 'KHUSUS');
$objPHPExcel->getActiveSheet()->mergeCells($this->cetak->next_char($last_column_kode_pelanggaran, 7) . '3:' . $this->cetak->next_char($last_column_kode_pelanggaran, 13) . '3');
$objPHPExcel->getActiveSheet()->setCellValue($this->cetak->next_char($last_column_kode_pelanggaran, 7) . '4', 'JUMLAH');
$objPHPExcel->getActiveSheet()->mergeCells($this->cetak->next_char($last_column_kode_pelanggaran, 7) . '4:' . $this->cetak->next_char($last_column_kode_pelanggaran, 9) . '4');
$objPHPExcel->getActiveSheet()->setCellValue($this->cetak->next_char($last_column_kode_pelanggaran, 10) . '4', 'PORSENTASE');
$objPHPExcel->getActiveSheet()->mergeCells($this->cetak->next_char($last_column_kode_pelanggaran, 10) . '4:' . $this->cetak->next_char($last_column_kode_pelanggaran, 12) . '4');
$objPHPExcel->getActiveSheet()->setCellValue($this->cetak->next_char($last_column_kode_pelanggaran, 13) . '4', 'RANGKING');
$objPHPExcel->getActiveSheet()->mergeCells($this->cetak->next_char($last_column_kode_pelanggaran, 13) . '4:' . $this->cetak->next_char($last_column_kode_pelanggaran, 13) . '5');

$DATA_KODE = array();
$DATA_KODE_WARNING = array();
$temp_column = $column_end_header;
foreach ($kode as $detail) {
    $temp_column = $this->cetak->next_char($temp_column, 1);
    $objPHPExcel->getActiveSheet()->setCellValue($temp_column . '4', $detail->KODE_KJP);
    if ($detail->PELANGGARAN_ALPHA_MJK != NULL) {
        $objPHPExcel->getActiveSheet()->getStyle($temp_column . '4')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
        $objPHPExcel->getActiveSheet()->getStyle($temp_column . '4')->getFill()->getStartColor()->setARGB('FF62CB31');
        $DATA_KODE_WARNING[$detail->KODE_KJP] = $temp_column;
    }
    $DATA_KODE[$detail->KODE_KJP] = $temp_column;
    $objPHPExcel->getActiveSheet()->mergeCells($temp_column . '4:' . ($this->cetak->next_char($temp_column, 2)) . '4');
    $temp_column = $this->cetak->next_char($temp_column, 2);
}

$temp_column = $column_end_header;
for ($colomn = 0; $colomn < $jumlah_kode_pelanggaran; $colomn++) {
    $temp_column = $this->cetak->next_char($temp_column, 1);
    $objPHPExcel->getActiveSheet()->setCellValue($temp_column . '5', '*');
    $objPHPExcel->getActiveSheet()->getColumnDimension($temp_column)->setWidth(5);
    $temp_column = $this->cetak->next_char($temp_column, 1);
    $objPHPExcel->getActiveSheet()->setCellValue($temp_column . '5', '**');
    $objPHPExcel->getActiveSheet()->getColumnDimension($temp_column)->setWidth(5);
    $temp_column = $this->cetak->next_char($temp_column, 1);
    $objPHPExcel->getActiveSheet()->setCellValue($temp_column . '5', '***');
    $objPHPExcel->getActiveSheet()->getColumnDimension($temp_column)->setWidth(5);
}

for ($i = 0; $i < 2; $i++) {
    for ($j = 0; $j < 2; $j++) {
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
    $temp_column = $this->cetak->next_char($temp_column, 1);
    $objPHPExcel->getActiveSheet()->getColumnDimension($temp_column)->setWidth(10);
}

$temp_jenjang = NULL;
$temp_kelas = NULL;
$start_row_jenjang = 0;
$end_row_data = $start_row_data - 1;
foreach ($kelas as $detail) {
    if ($temp_kelas != $detail->NAMA_KELAS) {
        $end_row_data++;
        $objPHPExcel->getActiveSheet()->setCellValue('A' . $end_row_data, $end_row_data - 5);
        $objPHPExcel->getActiveSheet()->setCellValue('C' . $end_row_data, $detail->NAMA_KELAS);
        $objPHPExcel->getActiveSheet()->setCellValue('D' . $end_row_data, $detail->JUMLAH_SISWA_KELAS);
        $temp_kelas = $detail->NAMA_KELAS;
    }
    if ($temp_jenjang != $detail->NAMA_DEPT) {
        $objPHPExcel->getActiveSheet()->setCellValue('B' . $end_row_data, $detail->NAMA_DEPT);
        $temp_jenjang = $detail->NAMA_DEPT;

        if (($start_row_jenjang > 0) && ($start_row_jenjang != $end_row_data))
            $objPHPExcel->getActiveSheet()->mergeCells('B' . $start_row_jenjang . ':B' . ($end_row_data - 1));

        $start_row_jenjang = $end_row_data;
    }
    $objPHPExcel->getActiveSheet()->setCellValue($DATA_KODE[$detail->KODE_KJP] . $end_row_data, $detail->JUMLAH_PELANGGAR);
    $objPHPExcel->getActiveSheet()->setCellValue($this->cetak->next_char($DATA_KODE[$detail->KODE_KJP], 1) . $end_row_data, $detail->JUMLAH_PELANGGARAN);
    $objPHPExcel->getActiveSheet()->setCellValue($this->cetak->next_char($DATA_KODE[$detail->KODE_KJP], 2) . $end_row_data, $detail->JUMLAH_POIN);
}
$objPHPExcel->getActiveSheet()->mergeCells('B' . $start_row_jenjang . ':B' . $end_row_data);
$end_row_data = $end_row_data;

for ($i = $start_row_data; $i <= $end_row_data; $i++) {
    $objPHPExcel->getActiveSheet()->setCellValue($column_porsentase_siswa . $i, '=(' . $column_jumlah_siswa . $i . '/' . $column_jumlah_siswa . ($end_row_data + 1) . ')*100');

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

    $column_porsentase = $last_column_kode_pelanggaran;
    $objPHPExcel->getActiveSheet()->setCellValue($this->cetak->next_char($last_column_kode_pelanggaran, 3) . $i, '=' . $column_porsentase . $i . '*' . $column_porsentase_siswa . $i);
    $column_porsentase = $this->cetak->next_char($last_column_kode_pelanggaran, 1);
    $objPHPExcel->getActiveSheet()->setCellValue($this->cetak->next_char($last_column_kode_pelanggaran, 4) . $i, '=' . $column_porsentase . $i . '*' . $column_porsentase_siswa . $i);
    $column_porsentase = $this->cetak->next_char($last_column_kode_pelanggaran, 2);
    $objPHPExcel->getActiveSheet()->setCellValue($this->cetak->next_char($last_column_kode_pelanggaran, 5) . $i, '=' . $column_porsentase . $i . '*' . $column_porsentase_siswa . $i);

    $column_rank = $this->cetak->next_char($last_column_kode_pelanggaran, 4);
    $objPHPExcel->getActiveSheet()->setCellValue($this->cetak->next_char($last_column_kode_pelanggaran, 6) . $i, '=RANK(' . $column_rank . $i . ',' . $column_rank . $start_row_data . ':' . $column_rank . $end_row_data . ')');

    $formula_pelanggar = '';
    $formula_pelanggaran = '';
    $formula_poin = '';

    $j = 1;
    foreach ($DATA_KODE_WARNING as $key => $column) {
        $formula_pelanggar .= $column . $i . ($j == count($DATA_KODE_WARNING) ? '' : '+');
        $formula_pelanggaran .= $this->cetak->next_char($column, 1) . $i . ($j == count($DATA_KODE_WARNING) ? '' : '+');
        $formula_poin .= $this->cetak->next_char($column, 2) . $i . ($j == count($DATA_KODE_WARNING) ? '' : '+');
        $j++;
    }

    $objPHPExcel->getActiveSheet()->setCellValue($this->cetak->next_char($last_column_kode_pelanggaran, 7) . $i, '=' . $formula_pelanggar);
    $objPHPExcel->getActiveSheet()->setCellValue($this->cetak->next_char($last_column_kode_pelanggaran, 8) . $i, '=' . $formula_pelanggaran);
    $objPHPExcel->getActiveSheet()->setCellValue($this->cetak->next_char($last_column_kode_pelanggaran, 9) . $i, '=' . $formula_poin);

    $column_porsentase = $last_column_kode_pelanggaran;
    $objPHPExcel->getActiveSheet()->setCellValue($this->cetak->next_char($last_column_kode_pelanggaran, 10) . $i, '=' . $column_porsentase . $i . '*' . $column_porsentase_siswa . $i);
    $column_porsentase = $this->cetak->next_char($last_column_kode_pelanggaran, 1);
    $objPHPExcel->getActiveSheet()->setCellValue($this->cetak->next_char($last_column_kode_pelanggaran, 11) . $i, '=' . $column_porsentase . $i . '*' . $column_porsentase_siswa . $i);
    $column_porsentase = $this->cetak->next_char($last_column_kode_pelanggaran, 2);
    $objPHPExcel->getActiveSheet()->setCellValue($this->cetak->next_char($last_column_kode_pelanggaran, 12) . $i, '=' . $column_porsentase . $i . '*' . $column_porsentase_siswa . $i);

    $column_rank = $this->cetak->next_char($last_column_kode_pelanggaran, 11);
    $objPHPExcel->getActiveSheet()->setCellValue($this->cetak->next_char($last_column_kode_pelanggaran, 13) . $i, '=RANK(' . $column_rank . $i . ',' . $column_rank . $start_row_data . ':' . $column_rank . $end_row_data . ')');
}

$objPHPExcel->getActiveSheet()->setCellValue('A' . ($end_row_data + 1), 'TOTAL');
$objPHPExcel->getActiveSheet()->mergeCells('A' . ($end_row_data + 1) . ':C' . ($end_row_data + 1));

$objPHPExcel->getActiveSheet()->setCellValue('D' . ($end_row_data + 1), '=SUM(D' . $start_row_data . ':D' . $end_row_data . ')');
$objPHPExcel->getActiveSheet()->setCellValue('E' . ($end_row_data + 1), '=SUM(E' . $start_row_data . ':E' . $end_row_data . ')');
foreach ($DATA_KODE as $key => $column) {
    $objPHPExcel->getActiveSheet()->setCellValue($column . ($end_row_data + 1), '=SUM(' . $column . $start_row_data . ':' . $column . $end_row_data . ')');
    $objPHPExcel->getActiveSheet()->setCellValue($this->cetak->next_char($column, 1) . ($end_row_data + 1), '=SUM(' . $this->cetak->next_char($column, 1) . $start_row_data . ':' . $this->cetak->next_char($column, 1) . $end_row_data . ')');
    $objPHPExcel->getActiveSheet()->setCellValue($this->cetak->next_char($column, 2) . ($end_row_data + 1), '=SUM(' . $this->cetak->next_char($column, 2) . $start_row_data . ':' . $this->cetak->next_char($column, 2) . $end_row_data . ')');
}

$start = 3;
for ($i = 0; $i < 2; $i++) {
    for ($j = $start; $j < ($start + 3); $j++) {
        $objPHPExcel->getActiveSheet()->setCellValue($this->cetak->next_char($column, $j) . ($end_row_data + 1), '=SUM(' . $this->cetak->next_char($column, $j) . $start_row_data . ':' . $this->cetak->next_char($column, $j) . $end_row_data . ')');
    }
    $start = 10;
}

$start_row_keterangan = $end_row_data + 3;
$objPHPExcel->getActiveSheet()->setCellValue('A' . $start_row_keterangan, 'KETERANGAN');
$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->setCellValue('B' . ($start_row_keterangan + 1), '1. Kolom tanda * adalah jumlah pelanggar (siswa yang melanggar)');
$objPHPExcel->getActiveSheet()->mergeCells('B' . ($start_row_keterangan + 1) . ':' . $objPHPExcel->getActiveSheet()->getHighestColumn() . ($start_row_keterangan + 1));
$objPHPExcel->getActiveSheet()->setCellValue('B' . ($start_row_keterangan + 2), '2. Kolom tanda ** adalah jumlah pelanggaran');
$objPHPExcel->getActiveSheet()->mergeCells('B' . ($start_row_keterangan + 2) . ':' . $objPHPExcel->getActiveSheet()->getHighestColumn() . ($start_row_keterangan + 2));
$objPHPExcel->getActiveSheet()->setCellValue('B' . ($start_row_keterangan + 3), '3. Kolom tanda *** adalah jumlah poin');
$objPHPExcel->getActiveSheet()->mergeCells('B' . ($start_row_keterangan + 3) . ':' . $objPHPExcel->getActiveSheet()->getHighestColumn() . ($start_row_keterangan + 3));
$objPHPExcel->getActiveSheet()->setCellValue('B' . ($start_row_keterangan + 4), '4. Kolom UMUM adalah kalkulasi untuk keseluruhan pelanggaran');
$objPHPExcel->getActiveSheet()->mergeCells('B' . ($start_row_keterangan + 4) . ':' . $objPHPExcel->getActiveSheet()->getHighestColumn() . ($start_row_keterangan + 4));
$objPHPExcel->getActiveSheet()->setCellValue('B' . ($start_row_keterangan + 5), '5. Kolom KHUSUS adalah kalkulasi untuk pelanggaran pada jenis absensi. Kolom ini harus mendapatkan perhatian penanganan khusus.');
$objPHPExcel->getActiveSheet()->mergeCells('B' . ($start_row_keterangan + 5) . ':' . $objPHPExcel->getActiveSheet()->getHighestColumn() . ($start_row_keterangan + 5));

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

$start = 2;
for ($i = 0; $i < 2; $i++) {
    for ($j = 0; $j < 2; $j++) {
        $start++;
        $cell = $this->cetak->next_char($column, $start) . ($start_row_data - 1) . ':' . $this->cetak->next_char($column, $start) . ($end_row_data + 1);
        $objPHPExcel->getActiveSheet()->getStyle($cell)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
        $objPHPExcel->getActiveSheet()->getStyle($cell)->getFill()->getStartColor()->setARGB('FFFFFF99');

        $start++;
        $cell = $this->cetak->next_char($column, $start) . ($start_row_data - 1) . ':' . $this->cetak->next_char($column, $start) . ($end_row_data + 1);
        $objPHPExcel->getActiveSheet()->getStyle($cell)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
        $objPHPExcel->getActiveSheet()->getStyle($cell)->getFill()->getStartColor()->setARGB('FFCCCCCC');

        $start++;
        $cell = $this->cetak->next_char($column, $start) . ($start_row_data - 1) . ':' . $this->cetak->next_char($column, $start) . ($end_row_data + 1);
        $objPHPExcel->getActiveSheet()->getStyle($cell)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
        $objPHPExcel->getActiveSheet()->getStyle($cell)->getFill()->getStartColor()->setARGB('FF66FFFF');
    }
    $start++;
    $cell = $this->cetak->next_char($column, $start) . ($start_row_data - 1) . ':' . $this->cetak->next_char($column, $start) . ($end_row_data + 1);
    $objPHPExcel->getActiveSheet()->getStyle($cell)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    $objPHPExcel->getActiveSheet()->getStyle($cell)->getFill()->getStartColor()->setARGB('FFCCCCCC');
}

$styleArrayBorder = array(
    'borders' => array(
        'allborders' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN,
            'color' => array('argb' => 'FF000000'),
        )
    )
);
$objPHPExcel->getActiveSheet()->getStyle('A3:' . $objPHPExcel->getActiveSheet()->getHighestColumn() . ($end_row_data + 1))->applyFromArray($styleArrayBorder);

$objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(15);

$objPHPExcel->getActiveSheet()->getStyle('A3:' . $objPHPExcel->getActiveSheet()->getHighestColumn() . '5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('A3:' . $objPHPExcel->getActiveSheet()->getHighestColumn() . '5')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('B' . $start_row_data . ':B' . $end_row_data)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('A3:' . $objPHPExcel->getActiveSheet()->getHighestColumn() . '5')->getAlignment()->setWrapText(true);
$objPHPExcel->getActiveSheet()->getStyle('A3:' . $objPHPExcel->getActiveSheet()->getHighestColumn() . '5')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('A' . ($end_row_data + 1) . ':' . $objPHPExcel->getActiveSheet()->getHighestColumn() . ($end_row_data + 1))->getFont()->setBold(true);

$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension($objPHPExcel->getActiveSheet()->getHighestColumn())->setWidth(10);
$objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(25);

$objPHPExcel->getActiveSheet()->freezePane($column_start_pelanggaran . $start_row_data);

// ##########################################################################################################################################################################

$start = -1;

$dataSeriesLabels = array(
    new PHPExcel_Chart_DataSeriesValues('String', $DATA_SHEET[$NUMBER_SHEET]['title'] . '!$' . $this->cetak->next_char($last_column_kode_pelanggaran, $start + 1) . '$' . ($start_row_data - 1), NULL, 1),
    new PHPExcel_Chart_DataSeriesValues('String', $DATA_SHEET[$NUMBER_SHEET]['title'] . '!$' . $this->cetak->next_char($last_column_kode_pelanggaran, $start + 2) . '$' . ($start_row_data - 1), NULL, 1),
    new PHPExcel_Chart_DataSeriesValues('String', $DATA_SHEET[$NUMBER_SHEET]['title'] . '!$' . $this->cetak->next_char($last_column_kode_pelanggaran, $start + 3) . '$' . ($start_row_data - 1), NULL, 1),
);

$xAxisTickValues = array(
    new PHPExcel_Chart_DataSeriesValues('String', $DATA_SHEET[$NUMBER_SHEET]['title'] . '!$C$' . $start_row_data . ':$C$' . $end_row_data, NULL, $end_row_data - $start_row_data)
);

$dataSeriesValues = array(
    new PHPExcel_Chart_DataSeriesValues('Number', $DATA_SHEET[$NUMBER_SHEET]['title'] . '!$' . $this->cetak->next_char($last_column_kode_pelanggaran, $start + 1) . '$' . $start_row_data . ':$' . $this->cetak->next_char($last_column_kode_pelanggaran, $start + 1) . '$' . $end_row_data, NULL, $end_row_data - $start_row_data),
    new PHPExcel_Chart_DataSeriesValues('Number', $DATA_SHEET[$NUMBER_SHEET]['title'] . '!$' . $this->cetak->next_char($last_column_kode_pelanggaran, $start + 2) . '$' . $start_row_data . ':$' . $this->cetak->next_char($last_column_kode_pelanggaran, $start + 2) . '$' . $end_row_data, NULL, $end_row_data - $start_row_data),
    new PHPExcel_Chart_DataSeriesValues('Number', $DATA_SHEET[$NUMBER_SHEET]['title'] . '!$' . $this->cetak->next_char($last_column_kode_pelanggaran, $start + 3) . '$' . $start_row_data . ':$' . $this->cetak->next_char($last_column_kode_pelanggaran, $start + 3) . '$' . $end_row_data, NULL, $end_row_data - $start_row_data),
);

$series = new PHPExcel_Chart_DataSeries(
        PHPExcel_Chart_DataSeries::TYPE_BARCHART, PHPExcel_Chart_DataSeries::GROUPING_STANDARD, range(0, count($dataSeriesValues) - 1), $dataSeriesLabels, $xAxisTickValues, $dataSeriesValues
);

$series->setPlotDirection(PHPExcel_Chart_DataSeries::DIRECTION_COL);

$plotArea = new PHPExcel_Chart_PlotArea(NULL, array($series));
$legend = new PHPExcel_Chart_Legend(PHPExcel_Chart_Legend::POSITION_BOTTOM, NULL, false);

$title = new PHPExcel_Chart_Title('Grafik Jumlah Pelanggaran Umum');
$xAxisLabel = new PHPExcel_Chart_Title('Kelas');
$yAxisLabel = new PHPExcel_Chart_Title('Jumlah');

$chart = new PHPExcel_Chart(
        'chart1', $title, $legend, $plotArea, true, 0, $xAxisLabel, $yAxisLabel
);

$chart->setTopLeftPosition($this->cetak->next_char($column_end_header, 2) . ($end_row_data + 15));
$chart->setBottomRightPosition($this->cetak->next_char($column_end_header, 60) . ($end_row_data + 65));

$objPHPExcel->getActiveSheet()->addChart($chart);

// ===========================================================================================================================================================================

$objPHPExcel->createSheet();
$NUMBER_SHEET++;
$objPHPExcel->setActiveSheetIndex($NUMBER_SHEET);

$DATA_SHEET[$NUMBER_SHEET] = array(
    'title' => 'jenis_pelanggaran',
    'text' => 'Jenis Pelanggaran'
);

$objPHPExcel->getActiveSheet()->setCellValue('A1', 'DATA JENIS PELANGGARAN');
$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(14);
$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);

$end_row_data = 3;
$objPHPExcel->getActiveSheet()->setCellValue('A' . $end_row_data, 'KODE');
$objPHPExcel->getActiveSheet()->setCellValue('B' . $end_row_data, 'JENIS PELANGGARAN');
$objPHPExcel->getActiveSheet()->setCellValue('C' . $end_row_data, 'POIN');
$objPHPExcel->getActiveSheet()->getStyle('A' . $end_row_data . ':C' . $end_row_data)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('A' . $end_row_data . ':C' . $end_row_data)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('A' . $end_row_data . ':C' . $end_row_data)->getFont()->setBold(true);

$end_row_data++;
foreach ($kode as $detail) {
    $objPHPExcel->getActiveSheet()->setCellValue('A' . $end_row_data, $detail->KODE_KJP);
    $objPHPExcel->getActiveSheet()->getStyle('A' . $end_row_data)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
    $objPHPExcel->getActiveSheet()->getStyle('A' . $end_row_data)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $objPHPExcel->getActiveSheet()->setCellValue('B' . $end_row_data, $detail->NAMA_KJP);
    $objPHPExcel->getActiveSheet()->setCellValue('C' . $end_row_data, $detail->POIN_KJP);
    $end_row_data++;
}

$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(8);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);

$objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(15);
$objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(20);

// ===========================================================================================================================================================================

foreach ($DATA_SHEET as $index => $item) {
    $objPHPExcel->setActiveSheetIndex($index);

    $objPHPExcel->getActiveSheet()->setTitle($item['title']);
}

// ===========================================================================================================================================================================

$objPHPExcel->setActiveSheetIndex(0);

$objPHPExcel->getActiveSheet()->setCellValue('A1', 'MENU STATISTIK KOMIDIS KEDISIPLINAN SISWA (TAHAP PENGEMBANGAN - BELUM DAPAT DIGUNAKAN)');
$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(14);
$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);

$objPHPExcel->getActiveSheet()->setCellValue('A2', 'Untuk membuka menu silahkan tekan keyboard ctrl dan di klik.');

$row_start = 4;
$number = 1;

foreach ($DATA_SHEET as $index => $item) {
    $objPHPExcel->getActiveSheet()->setCellValue('A' . $row_start, $index + 1);
    $objPHPExcel->getActiveSheet()->setCellValue('B' . $row_start, $item['text']);
    $objPHPExcel->getActiveSheet()->getCell('B' . $row_start)->getHyperlink()->setUrl("sheet://'" . $item['title'] . "'!A3");
    $row_start++;
}

$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
$objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(15);
$objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(20);
$objPHPExcel->getActiveSheet()->getRowDimension(2)->setRowHeight(20);


// ===========================================================================================================================================================================

$objPHPExcel->setActiveSheetIndex(1);

// ===========================================================================================================================================================================

//header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
//header('Content-Disposition: attachment;filename="statistik_komdis_' . date('Y_m_d_H_i_s') . '.xlsx"');
//header('Cache-Control: max-age=0');
//header('Cache-Control: max-age=1');
//
//$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
//$objWriter->setIncludeCharts(TRUE);
//$objWriter->save('php://output');

header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="statistik_komdis_' . date('Y_m_d_H_i_s') . '.xls"');
header('Cache-Control: max-age=0');
header('Cache-Control: max-age=1');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
