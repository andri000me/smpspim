<?php

ini_set('max_execution_time', 10000);

function next_char($char, $count) {
    for ($i = 0; $i < $count; $i++) {
        ++$char;
    }

    return $char;
}

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Protection;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$spreadsheet = new Spreadsheet();

$styleArrayBorder = array(
    'borders' => array(
        'allBorders' => array(
            'borderStyle' => Border::BORDER_THIN,
            'color' => array('argb' => 'FF000000'),
        ),
    ),
);

$spreadsheet->getProperties()->setCreator('Rohmad Eko Wahyudi')
        ->setLastModifiedBy('PIM Kajen Pati')
        ->setTitle('Legger');

$spreadsheet->setActiveSheetIndex(0);
$spreadsheet->getActiveSheet()->setTitle('DAFTAR NILAI');

$sheet = $spreadsheet->getActiveSheet();
$sheet->getProtection()->setSheet(true);

$row = 1;
$sheet->setCellValue("A$row", "PESANTREN ISLAM MATHALIUL FALAH - KAJEN");
$sheet->getStyle("A$row")->getFont()->setSize(14);
$sheet->getStyle("A$row")->getFont()->setBold(true);

$row = 2;
$sheet->setCellValue("A$row", "LEGGER NILAI");
$sheet->getStyle("A$row")->getFont()->setSize(16);
$sheet->getStyle("A$row")->getFont()->setBold(true);

$row = 3;
$sheet->setCellValue("A$row", "$CAWU TA $TA");
$sheet->getStyle("A$row")->getFont()->setSize(12);
$sheet->getStyle("A$row")->getFont()->setBold(true);

$row = 4;
$sheet->setCellValue("A$row", "(Tidak diperbolehkan merubah format. Hanya diperbolehkan mengisi nilai di cell berwarna kuning saja.)");

$row = 6;
$sheet->setCellValue("A$row", "KELAS");
$sheet->getStyle("A$row")->getFont()->setBold(true);
$sheet->setCellValue("C$row", $KELAS->NAMA_KELAS);
$sheet->getStyle("C$row")->getFont()->setBold(true);

$row = 7;
$sheet->setCellValue("A$row", "WALI KELAS");
$sheet->getStyle("A$row")->getFont()->setBold(true);
$sheet->setCellValue("C$row", $this->cetak->nama_peg_print((array) $KELAS));
$sheet->getStyle("C$row")->getFont()->setBold(true);

$row = 8;
$startRowTable = $row;
$sheet->setCellValue("A$row", "No");
$sheet->getColumnDimension('A')->setWidth(4);
$sheet->mergeCells("A$row:A" . ($row + 1));
$sheet->setCellValue("B$row", "NIS");
$sheet->getColumnDimension('B')->setWidth(15);
$sheet->mergeCells("B$row:B" . ($row + 1));
$sheet->setCellValue("C$row", "NAMA");
$sheet->getColumnDimension('C')->setWidth(40);
$sheet->mergeCells("C$row:C" . ($row + 1));
$sheet->setCellValue("D$row", "NILAI");

$row = 9;
$listMapel = array();
$lastColumnMapel = 'C';
foreach ($MAPEL as $key => $detail) {
    $lastColumnMapel = next_char($lastColumnMapel, 1);
    $listMapel[$detail->ID_MAPEL] = array(
        'column' => $lastColumnMapel,
        'key' => $key
    );

    $sheet->setCellValue("$lastColumnMapel$row", $detail->NAMA_MAPEL);
    $sheet->getColumnDimension($lastColumnMapel)->setWidth(4);
    $sheet->getStyle("$lastColumnMapel$row")->getAlignment()->setTextRotation(90);
}

$lastColumn = next_char($lastColumnMapel, 1);
$columnJumlah = $lastColumn;
$sheet->setCellValue("$lastColumn$row", "JUMLAH");
$sheet->getColumnDimension($lastColumn)->setWidth(10);
$sheet->getStyle("$lastColumn$row")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

$lastColumn = next_char($lastColumn, 1);
$columnRataRata = $lastColumn;
$sheet->setCellValue("$lastColumn$row", "RATA-RATA");
$sheet->getColumnDimension($lastColumn)->setWidth(10);
$sheet->getStyle("$lastColumn$row")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
$endColumnNilai = $lastColumn;

$lastColumn = next_char($lastColumn, 1);
$startColumnKeterangan = $lastColumn;
$columnSakit = $lastColumn;
$sheet->setCellValue("$lastColumn" . ($row - 1), "KETERANGAN");
$sheet->setCellValue("$lastColumn$row", "SAKIT");
$sheet->getColumnDimension($lastColumn)->setWidth(4);
$sheet->getStyle("$lastColumn$row")->getAlignment()->setTextRotation(90);

$lastColumn = next_char($lastColumn, 1);
$columnIzin = $lastColumn;
$sheet->setCellValue("$lastColumn$row", "IZIN");
$sheet->getColumnDimension($lastColumn)->setWidth(4);
$sheet->getStyle("$lastColumn$row")->getAlignment()->setTextRotation(90);

$lastColumn = next_char($lastColumn, 1);
$columnLari = $lastColumn;
$sheet->setCellValue("$lastColumn$row", "LARI");
$sheet->getColumnDimension($lastColumn)->setWidth(4);
$sheet->getStyle("$lastColumn$row")->getAlignment()->setTextRotation(90);
$sheet->mergeCells("$startColumnKeterangan" . ($row - 1) . ":$lastColumn" . ($row - 1));

$sheet->getRowDimension($row)->setRowHeight(100);
$sheet->getStyle("A$startRowTable:$lastColumn$row")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet->getStyle("A$startRowTable:C$row")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
$sheet->getStyle("A$startRowTable:$lastColumn$row")->getFont()->setBold(true);

$sheet->mergeCells("A1:" . $lastColumn . "1");
$sheet->getStyle("A1:" . $lastColumn . "1")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet->mergeCells("A2:" . $lastColumn . "2");
$sheet->getStyle("A2:" . $lastColumn . "2")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet->mergeCells("A3:" . $lastColumn . "3");
$sheet->getStyle("A3:" . $lastColumn . "3")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet->mergeCells("A4:" . $lastColumn . "4");
$sheet->getStyle("A4:" . $lastColumn . "4")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet->mergeCells("A6:B6");
$sheet->mergeCells("A7:B7");
$sheet->mergeCells("D$startRowTable:$endColumnNilai$startRowTable");

$row = 10;
foreach ($DATA as $key => $detail) {
    $sheet->setCellValue("A$row", $detail['SISWA']->NO_ABSEN_AS);
    $sheet->setCellValue("B$row", $detail['SISWA']->AKTIF_SISWA == 0 ? 'KELUAR' : $detail['SISWA']->NIS_SISWA);
    $sheet->setCellValue("C$row", $detail['SISWA']->NAMA_SISWA);

    foreach ($listMapel as $ID_MAPEL => $mapel) {
        $sheet->getStyle($mapel['column'] . "$row")->getFill()->setFillType(Fill::FILL_SOLID);
        $sheet->getStyle($mapel['column'] . "$row")->getFill()->getStartColor()->setARGB(Color::COLOR_YELLOW);
        if ($detail['SISWA']->AKTIF_SISWA == 1)
            $sheet->getStyle($mapel['column'] . "$row")->getProtection()->setLocked(Protection::PROTECTION_UNPROTECTED);
        foreach ($detail['NILAI'] as $detailNilai) {
            if ($detailNilai->MAPEL_AGM == $ID_MAPEL && $detailNilai->NILAI_AN !== NULL)
                $sheet->setCellValue($mapel['column'] . "$row", $detailNilai->NILAI_AN);
        }
    }

    $sheet->setCellValue("$columnJumlah$row", "=SUM(D$row:$lastColumnMapel$row)");
    $sheet->setCellValue("$columnRataRata$row", "=AVERAGE($columnJumlah$row/" . count($listMapel) . ")");
    $sheet->setCellValue("$columnIzin$row", $detail['ABSEN']['IZIN']);
    $sheet->setCellValue("$columnSakit$row", $detail['ABSEN']['SAKIT']);
    $sheet->setCellValue("$columnLari$row", $detail['ABSEN']['ALPHA']);

    if ($detail['SISWA']->AKTIF_SISWA == 0) {
        $column = 'A';
        for ($i = 0; $i < 100; $i++) {
            if ($column == next_char($lastColumn, 1))
                break;

            $sheet->getStyle("$column$row")->getFill()->setFillType(Fill::FILL_SOLID);
            $sheet->getStyle("$column$row")->getFill()->getStartColor()->setARGB(Color::COLOR_RED);
            $sheet->getStyle("$column$row")->getFont()->getColor()->setARGB(Color::COLOR_WHITE);

            ++$column;
        }
    }

    $row++;
}

$sheet->getStyle("A$startRowTable:$lastColumn" . ($row - 1))->applyFromArray($styleArrayBorder);

$spreadsheet->createSheet(1);
$spreadsheet->setActiveSheetIndex(1);
$spreadsheet->getActiveSheet()->setTitle('DAFTAR GURU MAPEL');

$sheet = $spreadsheet->getActiveSheet();
$sheet->getProtection()->setSheet(true);



$spreadsheet->setActiveSheetIndex(0);

$writer = new Xlsx($spreadsheet);

$filename = 'legger_nilai_' . date('Y_m_d_H_i_s');

header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
header('Cache-Control: max-age=0');

$writer->save('php://output');
