<?php

ini_set('max_execution_time', -1);

// MODE 0 => XLS (TIDAK MUNCUL GRAFIK)
// MODE 1 => XLSX (MUNCUL GRAFIK NAMUN ADA ISU BUG)
$MODE = 0;

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
foreach ($data as $detail_item) {
    $title = $detail_item['title'];
    $kelas = $detail_item['kelas'];
    $pelanggar = $detail_item['pelanggar'];
    $tindakan = $detail_item['tindakan'];

    $objPHPExcel->createSheet();
    $NUMBER_SHEET++;
    $objPHPExcel->setActiveSheetIndex($NUMBER_SHEET);

    $DATA_SHEET[$NUMBER_SHEET] = array(
        'title' => 'data_'.$title,
        'text' => 'Data '.$title
    );

// TITLE
    $objPHPExcel->getActiveSheet()->setCellValue('A1', $DATA_SHEET[0]['text']);
    $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(16);
    $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('A1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    $objPHPExcel->getActiveSheet()->getStyle('A1')->getFill()->getStartColor()->setARGB('FF62CB31');
    $objPHPExcel->getActiveSheet()->mergeCells('A1:B1');
    $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $objPHPExcel->getActiveSheet()->getCell('A1')->getHyperlink()->setUrl("sheet://'" . $DATA_SHEET[0]['title'] . "'!A1");

    $START_ROW_DATA = 6;
    $END_ROW_DATA = 0;

    $COLUMN_END_HEADER = 'E';
    $COLUMN_JUMLAH_SISWA = 'D';
    $COLUMN_PORSENTASE_SISWA = 'E';
    $COLUMN_START_PELANGGARAN = $this->cetak->next_char($COLUMN_END_HEADER, 1);

// HEADER
    $objPHPExcel->getActiveSheet()->setCellValue($COLUMN_START_PELANGGARAN . '1', 'DATA JUMLAH SISWA DAN POIN PELANGGARAN SERTA RANGKING KELAS YANG BUTUH PENANGANAN INTENSIF');
    $objPHPExcel->getActiveSheet()->getStyle($COLUMN_START_PELANGGARAN . '1')->getFont()->setSize(14);
    $objPHPExcel->getActiveSheet()->getStyle($COLUMN_START_PELANGGARAN . '1')->getFont()->setBold(true);

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

    $objPHPExcel->getActiveSheet()->setCellValue($COLUMN_START_PELANGGARAN . '3', 'KODE PELANGGARAN');
    $objPHPExcel->getActiveSheet()->mergeCells($COLUMN_START_PELANGGARAN . '3:' . $this->cetak->next_char($COLUMN_START_PELANGGARAN, ($jumlah_kode_pelanggaran * 3) - 1) . '3');

    for ($i = 0; $i < 2; $i++) {
        if ($i == 0) {
            $column_start = $this->cetak->next_char($COLUMN_START_PELANGGARAN, $jumlah_kode_pelanggaran * 3);
        } elseif ($i == 1) {
            $column_start = $this->cetak->next_char($column_start, $end_column + 1);
        }

        $objPHPExcel->getActiveSheet()->setCellValue($column_start . '3', $i == 0 ? 'UMUM' : 'KHUSUS');
        $objPHPExcel->getActiveSheet()->mergeCells($column_start . '3:' . $this->cetak->next_char($column_start, 7) . '3');

        $objPHPExcel->getActiveSheet()->setCellValue($column_start . '4', 'JUMLAH');
        $objPHPExcel->getActiveSheet()->mergeCells($column_start . '4:' . $this->cetak->next_char($column_start, 2) . '4');
        $objPHPExcel->getActiveSheet()->setCellValue($this->cetak->next_char($column_start, 3) . '4', 'PORSENTASE KELAS');
        $objPHPExcel->getActiveSheet()->mergeCells($this->cetak->next_char($column_start, 3) . '4:' . $this->cetak->next_char($column_start, 5) . '4');
        $objPHPExcel->getActiveSheet()->setCellValue($this->cetak->next_char($column_start, 6) . '4', 'RANGKING');
        $objPHPExcel->getActiveSheet()->mergeCells($this->cetak->next_char($column_start, 6) . '4:' . $this->cetak->next_char($column_start, 6) . '5');
        $objPHPExcel->getActiveSheet()->setCellValue($this->cetak->next_char($column_start, 7) . '4', 'PORSENTASE PELANGGARAN');
        $objPHPExcel->getActiveSheet()->mergeCells($this->cetak->next_char($column_start, 7) . '4:' . $this->cetak->next_char($column_start, 7) . '5');

        if ($i == 0)
            $COLUMN_START_UMUM = $column_start;
        elseif ($i == 1)
            $COLUMN_START_KHUSUS = $column_start;

        $end_column = 7;
    }

// HEADER JENIS TINDAKAN
    $COLUMN_START_JENIS_PELANGGARAN = $this->cetak->next_char($column_start, $end_column + 1);
    $DATA_TINDAKAN = array();
    $i = 0;
    foreach ($jenis_tindakan as $detail) {
        $column = $this->cetak->next_char($COLUMN_START_JENIS_PELANGGARAN, $i);
        $objPHPExcel->getActiveSheet()->setCellValue($column . '4', $detail->NAMA_KJT);
        $objPHPExcel->getActiveSheet()->mergeCells($column . '4:' . $column . '5');

        $DATA_TINDAKAN[$detail->ID_KJT] = $column;

        $i++;
    }
    $COLUMN_END_JENIS_PELANGGARAN = $this->cetak->next_char($COLUMN_START_JENIS_PELANGGARAN, $i - 1);
    $objPHPExcel->getActiveSheet()->setCellValue($COLUMN_START_JENIS_PELANGGARAN . '3', 'JUMLAH TINDAKAN');
    $objPHPExcel->getActiveSheet()->mergeCells($COLUMN_START_JENIS_PELANGGARAN . '3:' . $COLUMN_END_JENIS_PELANGGARAN . '3');

// HEADER KODE PELANGGARAN
    $DATA_KODE = array();
    $DATA_KODE_WARNING = array();
    $temp_column = $COLUMN_END_HEADER;
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

// HEADER DETAIL
    $temp_column = $COLUMN_END_HEADER;
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

// HEADER DETAIL UMUM DAN KHUSUS
    for ($i = 0; $i < 2; $i++) {
        if ($i == 0)
            $temp_column = $COLUMN_START_UMUM;
        elseif ($i == 1)
            $temp_column = $COLUMN_START_KHUSUS;

        for ($j = 0; $j < 2; $j++) {
            if ($j == 1)
                $temp_column = $this->cetak->next_char($temp_column, 1);

            $objPHPExcel->getActiveSheet()->setCellValue($temp_column . '5', '*');
            $objPHPExcel->getActiveSheet()->getColumnDimension($temp_column)->setWidth(10);
            $temp_column = $this->cetak->next_char($temp_column, 1);
            $objPHPExcel->getActiveSheet()->setCellValue($temp_column . '5', '**');
            $objPHPExcel->getActiveSheet()->getColumnDimension($temp_column)->setWidth(10);
            $temp_column = $this->cetak->next_char($temp_column, 1);
            $objPHPExcel->getActiveSheet()->setCellValue($temp_column . '5', '***');
            $objPHPExcel->getActiveSheet()->getColumnDimension($temp_column)->setWidth(10);
        }
    }

// DATA TABEL PELANGGARAN
    $temp_jenjang = NULL;
    $temp_kelas = NULL;
    $start_row_jenjang = 0;
    $DATA_ROW_KELAS = array();
    $END_ROW_DATA = $START_ROW_DATA - 1;
    foreach ($kelas as $detail) {
        if ($temp_kelas != $detail->NAMA_KELAS) {
            $END_ROW_DATA++;

            $DATA_ROW_KELAS[$detail->ID_KELAS] = $END_ROW_DATA;
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $END_ROW_DATA, $END_ROW_DATA - 5);
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $END_ROW_DATA, $detail->NAMA_KELAS);
            $objPHPExcel->getActiveSheet()->setCellValue('D' . $END_ROW_DATA, $detail->JUMLAH_SISWA_KELAS);
            $temp_kelas = $detail->NAMA_KELAS;
        }
        if ($temp_jenjang != $detail->NAMA_DEPT) {
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $END_ROW_DATA, $detail->NAMA_DEPT);
            $temp_jenjang = $detail->NAMA_DEPT;

            if (($start_row_jenjang > 0) && ($start_row_jenjang != $END_ROW_DATA))
                $objPHPExcel->getActiveSheet()->mergeCells('B' . $start_row_jenjang . ':B' . ($END_ROW_DATA - 1));

            $start_row_jenjang = $END_ROW_DATA;
        }
        $objPHPExcel->getActiveSheet()->setCellValue($DATA_KODE[$detail->KODE_KJP] . $END_ROW_DATA, $detail->JUMLAH_PELANGGAR);
        $objPHPExcel->getActiveSheet()->setCellValue($this->cetak->next_char($DATA_KODE[$detail->KODE_KJP], 1) . $END_ROW_DATA, $detail->JUMLAH_PELANGGARAN);
        $objPHPExcel->getActiveSheet()->setCellValue($this->cetak->next_char($DATA_KODE[$detail->KODE_KJP], 2) . $END_ROW_DATA, $detail->JUMLAH_POIN);
    }
    $objPHPExcel->getActiveSheet()->mergeCells('B' . $start_row_jenjang . ':B' . $END_ROW_DATA);
    $END_ROW_DATA = $END_ROW_DATA;

// DATA TABEL PELANGGAR
    foreach ($pelanggar as $mode => $value) {
        foreach ($value as $detail) {
            $objPHPExcel->getActiveSheet()->setCellValue(($mode == 'umum' ? $COLUMN_START_UMUM : $COLUMN_START_KHUSUS) . $DATA_ROW_KELAS[$detail->KELAS_AS], $detail->JUMLAH_PELANGGAR);
        }
    }

// DATA TABEL TINDAKAN
    foreach ($tindakan as $detail) {
        foreach ($DATA_TINDAKAN as $id => $column) {
            if ($detail['TINDAKAN_' . $id] > 0)
                $objPHPExcel->getActiveSheet()->setCellValue($column . $DATA_ROW_KELAS[$detail['KELAS_AS']], $detail['TINDAKAN_' . $id]);
        }
    }

// MENGHITUNG JUMLAH UMUM DAN KHUSUS
    for ($i = $START_ROW_DATA; $i <= $END_ROW_DATA; $i++) {
        $objPHPExcel->getActiveSheet()->setCellValue($COLUMN_PORSENTASE_SISWA . $i, '=(' . $COLUMN_JUMLAH_SISWA . $i . '/' . $COLUMN_JUMLAH_SISWA . ($END_ROW_DATA + 1) . ')');
        $objPHPExcel->getActiveSheet()->getStyle($COLUMN_PORSENTASE_SISWA . $i)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);

        for ($k = 0; $k < 2; $k++) {
            if ($k == 0) {
                $DATA = $DATA_KODE;
                $column_start = $COLUMN_START_UMUM;
            } elseif ($k == 1) {
                $DATA = $DATA_KODE_WARNING;
                $column_start = $COLUMN_START_KHUSUS;
            }

            $formula_pelanggar = '';
            $formula_pelanggaran = '';
            $formula_poin = '';

            $j = 1;
            foreach ($DATA as $key => $column) {
                $formula_pelanggar .= $column . $i . ($j == count($DATA) ? '' : '+');
                $formula_pelanggaran .= $this->cetak->next_char($column, 1) . $i . ($j == count($DATA) ? '' : '+');
                $formula_poin .= $this->cetak->next_char($column, 2) . $i . ($j == count($DATA) ? '' : '+');
                $j++;
            }
//        $objPHPExcel->getActiveSheet()->setCellValue($column_start . $i, '=' . $formula_pelanggar);
            $objPHPExcel->getActiveSheet()->setCellValue($this->cetak->next_char($column_start, 1) . $i, '=' . $formula_pelanggaran);
            $objPHPExcel->getActiveSheet()->setCellValue($this->cetak->next_char($column_start, 2) . $i, '=' . $formula_poin);

            $column_porsentase = $column_start;
//        $objPHPExcel->getActiveSheet()->setCellValue($this->cetak->next_char($column_start, 3) . $i, '=' . $column_porsentase . $i . '*' . $COLUMN_PORSENTASE_SISWA . $i);
            $objPHPExcel->getActiveSheet()->setCellValue($this->cetak->next_char($column_start, 3) . $i, '=' . $column_porsentase . $i . '/' . $COLUMN_JUMLAH_SISWA . $i);
            $objPHPExcel->getActiveSheet()->getStyle($this->cetak->next_char($column_start, 3) . $i)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
            $column_porsentase = $this->cetak->next_char($column_start, 1);
            $objPHPExcel->getActiveSheet()->setCellValue($this->cetak->next_char($column_start, 4) . $i, '=' . $column_porsentase . $i . '*' . $COLUMN_PORSENTASE_SISWA . $i);
            $objPHPExcel->getActiveSheet()->getStyle($this->cetak->next_char($column_start, 4) . $i)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
            $column_porsentase = $this->cetak->next_char($column_start, 2);
            $objPHPExcel->getActiveSheet()->setCellValue($this->cetak->next_char($column_start, 5) . $i, '=' . $column_porsentase . $i . '*' . $COLUMN_PORSENTASE_SISWA . $i);
            $objPHPExcel->getActiveSheet()->getStyle($this->cetak->next_char($column_start, 5) . $i)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);

            $column_rank = $this->cetak->next_char($column_start, 4);
            $objPHPExcel->getActiveSheet()->setCellValue($this->cetak->next_char($column_start, 6) . $i, '=RANK(' . $column_rank . $i . ',' . $column_rank . $START_ROW_DATA . ':' . $column_rank . $END_ROW_DATA . ')');

            $column_pors = $this->cetak->next_char($column_start, 1);
            $objPHPExcel->getActiveSheet()->setCellValue($this->cetak->next_char($column_start, 7) . $i, '=(' . $column_pors . $i . '/' . $column_pors . ($END_ROW_DATA + 1) . ')');
            $objPHPExcel->getActiveSheet()->getStyle($this->cetak->next_char($column_start, 7) . $i)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
        }
    }

// MENGHITUNG JUMLAH SETIAP DATA TABEL
    $objPHPExcel->getActiveSheet()->setCellValue('A' . ($END_ROW_DATA + 1), 'TOTAL');
    $objPHPExcel->getActiveSheet()->mergeCells('A' . ($END_ROW_DATA + 1) . ':C' . ($END_ROW_DATA + 1));

    $objPHPExcel->getActiveSheet()->setCellValue('A' . ($END_ROW_DATA + 2), 'PORSENTASE');
    $objPHPExcel->getActiveSheet()->mergeCells('A' . ($END_ROW_DATA + 2) . ':C' . ($END_ROW_DATA + 2));

    $objPHPExcel->getActiveSheet()->setCellValue('D' . ($END_ROW_DATA + 1), '=SUM(D' . $START_ROW_DATA . ':D' . $END_ROW_DATA . ')');

    foreach ($DATA_KODE as $key => $column) {
        // MENGHITUNG JUMLAH DATA SETIAP KOLOM
        $objPHPExcel->getActiveSheet()->setCellValue($column . ($END_ROW_DATA + 1), '=SUM(' . $column . $START_ROW_DATA . ':' . $column . $END_ROW_DATA . ')');
        $objPHPExcel->getActiveSheet()->setCellValue($this->cetak->next_char($column, 1) . ($END_ROW_DATA + 1), '=SUM(' . $this->cetak->next_char($column, 1) . $START_ROW_DATA . ':' . $this->cetak->next_char($column, 1) . $END_ROW_DATA . ')');
        $objPHPExcel->getActiveSheet()->setCellValue($this->cetak->next_char($column, 2) . ($END_ROW_DATA + 1), '=SUM(' . $this->cetak->next_char($column, 2) . $START_ROW_DATA . ':' . $this->cetak->next_char($column, 2) . $END_ROW_DATA . ')');

        // MENGHITUNG PORSENTASE DATA SETIAP KOLOM
//    $objPHPExcel->getActiveSheet()->setCellValue($column . ($END_ROW_DATA + 2), '=(' . $column . ($END_ROW_DATA + 1) . '/' . $COLUMN_START_UMUM . ($END_ROW_DATA + 1) . ')');
        $objPHPExcel->getActiveSheet()->setCellValue($column . ($END_ROW_DATA + 2), '=(' . $column . ($END_ROW_DATA + 1) . '/' . $COLUMN_JUMLAH_SISWA . ($END_ROW_DATA + 1) . ')');
        $objPHPExcel->getActiveSheet()->getStyle($column . ($END_ROW_DATA + 2))->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
        $objPHPExcel->getActiveSheet()->getStyle($column . ($END_ROW_DATA + 2))->getFont()->setSize(8);
        $objPHPExcel->getActiveSheet()->setCellValue($this->cetak->next_char($column, 1) . ($END_ROW_DATA + 2), '=(' . $this->cetak->next_char($column, 1) . ($END_ROW_DATA + 1) . '/' . $this->cetak->next_char($COLUMN_START_UMUM, 1) . ($END_ROW_DATA + 1) . ')');
        $objPHPExcel->getActiveSheet()->getStyle($this->cetak->next_char($column, 1) . ($END_ROW_DATA + 2))->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
        $objPHPExcel->getActiveSheet()->getStyle($this->cetak->next_char($column, 1) . ($END_ROW_DATA + 2))->getFont()->setSize(8);
        $objPHPExcel->getActiveSheet()->setCellValue($this->cetak->next_char($column, 2) . ($END_ROW_DATA + 2), '=(' . $this->cetak->next_char($column, 2) . ($END_ROW_DATA + 1) . '/' . $this->cetak->next_char($COLUMN_START_UMUM, 2) . ($END_ROW_DATA + 1) . ')');
        $objPHPExcel->getActiveSheet()->getStyle($this->cetak->next_char($column, 2) . ($END_ROW_DATA + 2))->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
        $objPHPExcel->getActiveSheet()->getStyle($this->cetak->next_char($column, 2) . ($END_ROW_DATA + 2))->getFont()->setSize(8);
    }

// MENGHITUNG PORSENTASE JUMLAH SISWA
    for ($i = 0; $i < 2; $i++) {
        if ($i == 0)
            $column = $COLUMN_START_UMUM;
        elseif ($i == 1)
            $column = $COLUMN_START_KHUSUS;

        $objPHPExcel->getActiveSheet()->setCellValue($this->cetak->next_char($column, 3) . ($END_ROW_DATA + 1), '=' . $column . ($END_ROW_DATA + 1) . '/' . $COLUMN_JUMLAH_SISWA . ($END_ROW_DATA + 1));
        $objPHPExcel->getActiveSheet()->getStyle($this->cetak->next_char($column, 3) . ($END_ROW_DATA + 1))->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
    }

// MENGHITUNG PORSENTASE JUMLAH TINDAKAN
    foreach ($DATA_TINDAKAN as $id => $column) {
        $objPHPExcel->getActiveSheet()->setCellValue($column . ($END_ROW_DATA + 2), '=' . $column . ($END_ROW_DATA + 1) . '/' . $COLUMN_JUMLAH_SISWA . ($END_ROW_DATA + 1));
        $objPHPExcel->getActiveSheet()->getStyle($column . ($END_ROW_DATA + 2))->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
    }

// MENGHITUNG JUMLAH TINDAKAN SETIAP KOLOM
    foreach ($DATA_TINDAKAN as $column) {
        $objPHPExcel->getActiveSheet()->setCellValue($column . ($END_ROW_DATA + 1), '=SUM(' . $column . $START_ROW_DATA . ':' . $column . $END_ROW_DATA . ')');
    }

// MENGHITUNG TINDAKAN
    for ($i = 0; $i < 2; $i++) {
        if ($i == 0)
            $column = $COLUMN_START_UMUM;
        elseif ($i == 1)
            $column = $COLUMN_START_KHUSUS;

        for ($j = 0; $j < 3; $j++) {
            $objPHPExcel->getActiveSheet()->setCellValue($this->cetak->next_char($column, $j) . ($END_ROW_DATA + 1), '=SUM(' . $this->cetak->next_char($column, $j) . $START_ROW_DATA . ':' . $this->cetak->next_char($column, $j) . $END_ROW_DATA . ')');
        }
    }

    $end_row_table = $END_ROW_DATA + 2;

// MEWARNAI DATA PELANGGARAN
    foreach ($DATA_KODE as $key => $column) {
        $cell = $column . ($START_ROW_DATA - 1) . ':' . $column . $end_row_table;
        $objPHPExcel->getActiveSheet()->getStyle($cell)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
        $objPHPExcel->getActiveSheet()->getStyle($cell)->getFill()->getStartColor()->setARGB('FFFFFF99');

        $cell = $this->cetak->next_char($column, 1) . ($START_ROW_DATA - 1) . ':' . $this->cetak->next_char($column, 1) . $end_row_table;
        $objPHPExcel->getActiveSheet()->getStyle($cell)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
        $objPHPExcel->getActiveSheet()->getStyle($cell)->getFill()->getStartColor()->setARGB('FFCCCCCC');

        $cell = $this->cetak->next_char($column, 2) . ($START_ROW_DATA - 1) . ':' . $this->cetak->next_char($column, 2) . $end_row_table;
        $objPHPExcel->getActiveSheet()->getStyle($cell)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
        $objPHPExcel->getActiveSheet()->getStyle($cell)->getFill()->getStartColor()->setARGB('FF66FFFF');
    }

    for ($i = 0; $i < 2; $i++) {
        $start = 0;
        if ($i == 0)
            $column = $COLUMN_START_UMUM;
        elseif ($i == 1)
            $column = $COLUMN_START_KHUSUS;

        for ($j = 0; $j < 2; $j++) {
            $cell = $this->cetak->next_char($column, $start) . ($START_ROW_DATA - 1) . ':' . $this->cetak->next_char($column, $start) . $end_row_table;
            $objPHPExcel->getActiveSheet()->getStyle($cell)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
            $objPHPExcel->getActiveSheet()->getStyle($cell)->getFill()->getStartColor()->setARGB('FFFFFF99');
            $start++;

            $cell = $this->cetak->next_char($column, $start) . ($START_ROW_DATA - 1) . ':' . $this->cetak->next_char($column, $start) . $end_row_table;
            $objPHPExcel->getActiveSheet()->getStyle($cell)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
            $objPHPExcel->getActiveSheet()->getStyle($cell)->getFill()->getStartColor()->setARGB('FFCCCCCC');
            $start++;

            $cell = $this->cetak->next_char($column, $start) . ($START_ROW_DATA - 1) . ':' . $this->cetak->next_char($column, $start) . $end_row_table;
            $objPHPExcel->getActiveSheet()->getStyle($cell)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
            $objPHPExcel->getActiveSheet()->getStyle($cell)->getFill()->getStartColor()->setARGB('FF66FFFF');
            $start++;
        }

        for ($k = 0; $k < 2; $k++) {
            $cell = $this->cetak->next_char($column, $start) . ($START_ROW_DATA - 1) . ':' . $this->cetak->next_char($column, $start) . $end_row_table;
            $objPHPExcel->getActiveSheet()->getStyle($cell)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
            $objPHPExcel->getActiveSheet()->getStyle($cell)->getFill()->getStartColor()->setARGB('FFCCCCCC');
            $start++;
        }
    }

// MEMBERIKAN KETERANGAN
    $start_row_keterangan = $end_row_table + 2;
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

    $styleArrayBorder = array(
        'borders' => array(
            'allborders' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array('argb' => 'FF000000'),
            )
        )
    );
    $objPHPExcel->getActiveSheet()->getStyle('A3:' . $objPHPExcel->getActiveSheet()->getHighestColumn() . $end_row_table)->applyFromArray($styleArrayBorder);

    $objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(15);

    $objPHPExcel->getActiveSheet()->getStyle('A3:' . $objPHPExcel->getActiveSheet()->getHighestColumn() . '5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->getStyle('A3:' . $objPHPExcel->getActiveSheet()->getHighestColumn() . '5')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $objPHPExcel->getActiveSheet()->getStyle('B' . $START_ROW_DATA . ':B' . $END_ROW_DATA)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $objPHPExcel->getActiveSheet()->getStyle('A3:' . $objPHPExcel->getActiveSheet()->getHighestColumn() . '5')->getAlignment()->setWrapText(true);
    $objPHPExcel->getActiveSheet()->getStyle('A3:' . $objPHPExcel->getActiveSheet()->getHighestColumn() . '5')->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('A' . $end_row_table . ':' . $objPHPExcel->getActiveSheet()->getHighestColumn() . $end_row_table)->getFont()->setBold(true);

    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension($objPHPExcel->getActiveSheet()->getHighestColumn())->setWidth(10);
    $objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(25);

    $objPHPExcel->getActiveSheet()->freezePane($COLUMN_START_PELANGGARAN . $START_ROW_DATA);

// ===========================================================================================================================================================================
    $objPHPExcel->createSheet();
    $NUMBER_SHEET++;
    $objPHPExcel->setActiveSheetIndex($NUMBER_SHEET);

    $DATA_SHEET[$NUMBER_SHEET] = array(
        'title' => 'grafik_tahunan',
        'text' => 'Grafik Tahunan'
    );

    $DATA_NUMBER_SHEET = $NUMBER_SHEET - 1;

    $start = -1;

    for ($i = 0; $i < 2; $i++) {
        if ($i == 0) {
            $colomn = $COLUMN_START_UMUM;
            $title_grafik = 'GRAFIK JUMLAH PELANGGARAN UMUM';
        } elseif ($i == 1) {
            $colomn = $COLUMN_START_KHUSUS;
            $title_grafik = 'GRAFIK JUMLAH PELANGGARAN KHUSUS';
        }

        $dataSeriesLabels = array(
            new PHPExcel_Chart_DataSeriesValues('String', $DATA_SHEET[$DATA_NUMBER_SHEET]['title'] . '!$' . $this->cetak->next_char($colomn, $start + 1) . '$' . ($START_ROW_DATA - 1), NULL, 1),
            new PHPExcel_Chart_DataSeriesValues('String', $DATA_SHEET[$DATA_NUMBER_SHEET]['title'] . '!$' . $this->cetak->next_char($colomn, $start + 2) . '$' . ($START_ROW_DATA - 1), NULL, 1),
            new PHPExcel_Chart_DataSeriesValues('String', $DATA_SHEET[$DATA_NUMBER_SHEET]['title'] . '!$' . $this->cetak->next_char($colomn, $start + 3) . '$' . ($START_ROW_DATA - 1), NULL, 1),
        );

        $xAxisTickValues = array(
            new PHPExcel_Chart_DataSeriesValues('String', $DATA_SHEET[$DATA_NUMBER_SHEET]['title'] . '!$C$' . $START_ROW_DATA . ':$C$' . $END_ROW_DATA, NULL, $END_ROW_DATA - $START_ROW_DATA)
        );

        $dataSeriesValues = array(
            new PHPExcel_Chart_DataSeriesValues('Number', $DATA_SHEET[$DATA_NUMBER_SHEET]['title'] . '!$' . $this->cetak->next_char($colomn, $start + 1) . '$' . $START_ROW_DATA . ':$' . $this->cetak->next_char($colomn, $start + 1) . '$' . $END_ROW_DATA, NULL, $END_ROW_DATA - $START_ROW_DATA),
            new PHPExcel_Chart_DataSeriesValues('Number', $DATA_SHEET[$DATA_NUMBER_SHEET]['title'] . '!$' . $this->cetak->next_char($colomn, $start + 2) . '$' . $START_ROW_DATA . ':$' . $this->cetak->next_char($colomn, $start + 2) . '$' . $END_ROW_DATA, NULL, $END_ROW_DATA - $START_ROW_DATA),
            new PHPExcel_Chart_DataSeriesValues('Number', $DATA_SHEET[$DATA_NUMBER_SHEET]['title'] . '!$' . $this->cetak->next_char($colomn, $start + 3) . '$' . $START_ROW_DATA . ':$' . $this->cetak->next_char($colomn, $start + 3) . '$' . $END_ROW_DATA, NULL, $END_ROW_DATA - $START_ROW_DATA),
        );

        $series = new PHPExcel_Chart_DataSeries(
                PHPExcel_Chart_DataSeries::TYPE_BARCHART, PHPExcel_Chart_DataSeries::GROUPING_STANDARD, range(0, count($dataSeriesValues) - 1), $dataSeriesLabels, $xAxisTickValues, $dataSeriesValues
        );

        $series->setPlotDirection(PHPExcel_Chart_DataSeries::DIRECTION_COL);

        $plotArea = new PHPExcel_Chart_PlotArea(NULL, array($series));
        $legend = new PHPExcel_Chart_Legend(PHPExcel_Chart_Legend::POSITION_BOTTOM, NULL, false);

        $title = new PHPExcel_Chart_Title($title_grafik);
        $xAxisLabel = new PHPExcel_Chart_Title('Kelas');
        $yAxisLabel = new PHPExcel_Chart_Title('Jumlah');

        $chart = new PHPExcel_Chart(
                'chart1', $title, $legend, $plotArea, true, 0, $xAxisLabel, $yAxisLabel
        );

        $COLUMN_START_CHART = 'B';

        if ($i == 0) {
            $ROW_START_CHART = 4;
        } elseif ($i == 1) {
            $ROW_START_CHART += 40;
        }
        $chart->setTopLeftPosition($COLUMN_START_CHART . $ROW_START_CHART);
        $chart->setBottomRightPosition($this->cetak->next_char($COLUMN_START_CHART, 60) . ($ROW_START_CHART + 35));

        $objPHPExcel->getActiveSheet()->addChart($chart);

        $objPHPExcel->getActiveSheet()->setCellValue('A' . ($ROW_START_CHART - 2), $title_grafik);
        $objPHPExcel->getActiveSheet()->getStyle('A' . ($ROW_START_CHART - 2))->getFont()->setSize(14);
        $objPHPExcel->getActiveSheet()->getStyle('A' . ($ROW_START_CHART - 2))->getFont()->setBold(true);
    }

    $ROW_START_CHART += 45;

    $title_grafik = 'GRAFIK JUMLAH TINDAKAN';

    $objPHPExcel->getActiveSheet()->setCellValue('A' . ($ROW_START_CHART - 2), $title_grafik);
    $objPHPExcel->getActiveSheet()->getStyle('A' . ($ROW_START_CHART - 2))->getFont()->setSize(14);
    $objPHPExcel->getActiveSheet()->getStyle('A' . ($ROW_START_CHART - 2))->getFont()->setBold(true);

    $dataSeriesLabels1 = array(
        new PHPExcel_Chart_DataSeriesValues('String', $DATA_SHEET[$DATA_NUMBER_SHEET]['title'] . '!$A$' . ($END_ROW_DATA + 2), NULL, 1)
    );
    $xAxisTickValues1 = array(
        new PHPExcel_Chart_DataSeriesValues('String', $DATA_SHEET[$DATA_NUMBER_SHEET]['title'] . '!$' . $COLUMN_START_JENIS_PELANGGARAN . '$4:$' . $COLUMN_END_JENIS_PELANGGARAN . '$4', NULL, count($jenis_tindakan)),
    );
    $dataSeriesValues1 = array(
        new PHPExcel_Chart_DataSeriesValues('Number', $DATA_SHEET[$DATA_NUMBER_SHEET]['title'] . '!$' . $COLUMN_START_JENIS_PELANGGARAN . '$' . ($END_ROW_DATA + 1) . ':$' . $COLUMN_END_JENIS_PELANGGARAN . '$' . ($END_ROW_DATA + 1), NULL, count($jenis_tindakan)),
    );

    $series1 = new PHPExcel_Chart_DataSeries(
            PHPExcel_Chart_DataSeries::TYPE_PIECHART, NULL, range(0, count($dataSeriesValues1) - 1), $dataSeriesLabels1, $xAxisTickValues1, $dataSeriesValues1
    );
    $layout1 = new PHPExcel_Chart_Layout();
    $layout1->setShowVal(TRUE);
    $layout1->setShowPercent(TRUE);
    $plotArea1 = new PHPExcel_Chart_PlotArea($layout1, array($series1));
    $legend1 = new PHPExcel_Chart_Legend(PHPExcel_Chart_Legend::POSITION_RIGHT, NULL, false);

    $title1 = new PHPExcel_Chart_Title('GRAFIK JUMLAH TINDAKAN');
    $chart1 = new PHPExcel_Chart(
            'chart1', $title1, $legend1, $plotArea1, true, 0, NULL, NULL
    );

    $chart1->setTopLeftPosition($COLUMN_START_CHART . $ROW_START_CHART);
    $chart1->setBottomRightPosition($this->cetak->next_char($COLUMN_START_CHART, 10) . ($ROW_START_CHART + 25));

    $objPHPExcel->getActiveSheet()->addChart($chart1);
}

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

$END_ROW_DATA = 3;
$objPHPExcel->getActiveSheet()->setCellValue('A' . $END_ROW_DATA, 'KODE');
$objPHPExcel->getActiveSheet()->setCellValue('B' . $END_ROW_DATA, 'JENIS PELANGGARAN');
$objPHPExcel->getActiveSheet()->setCellValue('C' . $END_ROW_DATA, 'POIN');
$objPHPExcel->getActiveSheet()->getStyle('A' . $END_ROW_DATA . ':C' . $END_ROW_DATA)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('A' . $END_ROW_DATA . ':C' . $END_ROW_DATA)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('A' . $END_ROW_DATA . ':C' . $END_ROW_DATA)->getFont()->setBold(true);

$END_ROW_DATA++;
foreach ($kode as $detail) {
    $objPHPExcel->getActiveSheet()->setCellValue('A' . $END_ROW_DATA, $detail->KODE_KJP);
    $objPHPExcel->getActiveSheet()->getStyle('A' . $END_ROW_DATA)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
    $objPHPExcel->getActiveSheet()->getStyle('A' . $END_ROW_DATA)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $objPHPExcel->getActiveSheet()->setCellValue('B' . $END_ROW_DATA, $detail->NAMA_KJP);
    $objPHPExcel->getActiveSheet()->setCellValue('C' . $END_ROW_DATA, $detail->POIN_KJP);
    $END_ROW_DATA++;
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

if ($MODE == 0) {
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="statistik_komdis_' . date('Y_m_d_H_i_s') . '.xls"');
    header('Cache-Control: max-age=0');
    header('Cache-Control: max-age=1');

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
} elseif ($MODE == 1) {
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="statistik_komdis_' . date('Y_m_d_H_i_s') . '.xlsx"');
    header('Cache-Control: max-age=0');
    header('Cache-Control: max-age=1');

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $objWriter->setIncludeCharts(TRUE);
}

$objWriter->save('php://output');
