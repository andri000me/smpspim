<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 * 
  $this->CoreFonts = array('courier', 'helvetica', 'times', 'symbol', 'zapfdingbats');
 * 
 */
if (!isset($data) || count($data) == 0) {
    echo '<h1>TIDAK ADA DATA YANG AKAN DICETAK.</h1>';

    exit();
}

$pdf = $this->fpdf;

foreach ($data as $kelas) {

    $pdf->AddPage("P", "A4");

    $pdf->SetLineWidth(80);
    $pdf->Line(0, 0, 200, 0);

    $pdf->SetLineWidth(130);
    $pdf->Line(0, 240, 200, 240);

    $pdf->SetY(100);
    $pdf->SetFont('Arial', 'B', 40);
    $pdf->Cell(0, 5, 'DIVIDER', 0, 0, 'C');
    $pdf->Ln();

    foreach ($kelas as $detail) {
        $siswa = $detail['siswa'];
        $pelanggaran = $detail['pelanggaran'];

        $pdf->AddPage("P", "A4");
//	$pdf->SetMargins(6, 0);
        $pdf->SetAutoPageBreak(true, 0);

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 5, 'DETAIL PELANGGARAN SISWA', 0, 0, 'C');
        $pdf->Ln();

        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(0, 5, 'KOMISI DISIPLIN SISWA', 0, 0, 'C');
        $pdf->Ln(8);

        $pdf->SetFont('Arial', '', 10);

//$pdf->Cell(20, 5, 'T A');
//$pdf->Cell(100, 5, ': ' . $siswa->NAMA_TA);
//$pdf->Cell(20, 5, 'Catur Wulan');
//$pdf->Cell(0, 5, ': ' . $siswa->NAMA_CAWU);
//$pdf->Ln();

        $pdf->Cell(20, 5, 'Nama');
        $pdf->Cell(100, 5, ': ' . $siswa->NAMA_SISWA);
        $pdf->Cell(20, 5, 'N I S');
        $pdf->Cell(0, 5, ': ' . $siswa->NIS_SISWA);
        $pdf->Ln();

        $pdf->Cell(20, 5, 'Domisili');
        $pdf->Cell(100, 5, ': ' . $this->pdf_handler->cut_text($pdf, ($siswa->PONDOK_SISWA == NULL || $siswa->PONDOK_SISWA == 1) ? 'Belum Mondok' : $siswa->NAMA_PONDOK_MPS . ' ' . $siswa['ALAMAT_MPS'], 100));
        $pdf->Cell(20, 5, 'Kelas');
        $pdf->Cell(0, 5, ': ' . $siswa->NAMA_KELAS);
        $pdf->Ln();

        $pdf->Cell(20, 5, 'Alamat');
        $pdf->Cell(100, 5, ': ' . $this->pdf_handler->cut_text($pdf, $siswa->ALAMAT_SISWA . ', Kec. ' . $siswa->NAMA_KEC . ', ' . str_replace('kabupaten', 'Kab.', strtolower($siswa->NAMA_KAB)), 100));
        $pdf->Cell(20, 5, 'Surat');
        $pdf->Cell(0, 5, ': ' . ($siswa->NAMA_KJT == NULL ? '-' : $siswa->NAMA_KJT));
        $pdf->Ln();

        $pdf->Cell(20, 5, 'Wali Santri');
        $pdf->Cell(100, 5, ': ' . $siswa->AYAH_NAMA_SISWA);
        $pdf->Cell(20, 5, 'Jumlah Poin');
        $pdf->Cell(0, 5, ': ' . $siswa->JUMLAH_POIN_KSH);
        $pdf->Ln();

        $pdf->Cell(20, 5, 'Wali Kelas');
        $pdf->Cell(100, 5, ': ' . $this->cetak->nama_peg_print($siswa));
        $pdf->Cell(20, 5, 'Jumlah Lari');
        $pdf->Cell(0, 5, ': ' . $siswa->JUMLAH_LARI_KSH);
        $pdf->Ln();

        $data_header = array(
            array('align' => 'C', 'width' => 10, 'height' => 5, 'text' => 'No'),
            array('align' => 'C', 'width' => 35, 'text' => 'Tanggal Memasukan Data'),
            array('align' => 'C', 'width' => 90, 'text' => 'Pelanggaran'),
            array('align' => 'C', 'width' => 40, 'text' => 'Tanggal Pelanggaran'),
            array('align' => 'C', 'width' => 15, 'text' => 'Poin')
        );

        $pdf->SetFont('Arial', 'B', 10);
        $pdf = $this->pdf_handler->wrap_row_table($pdf, $data_header);

        $pdf->SetFont('Arial', '', 10);
        $i = 1;
        foreach ($pelanggaran as $detail) {
            $data_header = array(
                array('align' => 'C', 'width' => 10, 'height' => 5, 'text' => $i++),
                array('align' => 'L', 'width' => 35, 'text' => $this->date_format->get_day($detail->TANGGAL_INPUT) . ', ' . $this->date_format->to_print_short($detail->TANGGAL_INPUT)),
                array('align' => 'L', 'width' => 90, 'text' => $detail->NAMA_KJP),
                array('align' => 'L', 'width' => 40, 'text' => $this->date_format->get_day($detail->TANGGAL_KS) . ', ' . $this->date_format->to_print_short($detail->TANGGAL_KS)),
                array('align' => 'C', 'width' => 15, 'text' => $detail->POIN_KJP)
            );

            $pdf = $this->pdf_handler->wrap_row_table($pdf, $data_header);
        }
        $pdf->SetFont('Arial', 'I', 8);
        $pdf->Cell(0, 5, 'Dicetak tanggal: ' . $this->date_format->to_print_short(date('Y-m-d')), 0, 0, 'R');
    }
}

$pdf->Output();
