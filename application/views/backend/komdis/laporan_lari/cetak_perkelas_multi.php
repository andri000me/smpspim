<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if (!isset($data) || count($data) == 0) {
    echo '<h1>TIDAK ADA DATA YANG AKAN DICETAK.</h1>';

    exit();
}

$pdf = $this->fpdf;

$temp_kelas = null;
$temp_siswa = array();
foreach ($data as $key => $detail) {
    if ($temp_kelas != $detail->NAMA_KELAS) {
        
        foreach ($temp_siswa as $key_temp) {
            $siswa = $DETAIL_PELANGGARAN[$key_temp]['siswa'];
            $pelanggaran = $DETAIL_PELANGGARAN[$key_temp]['pelanggaran'];

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
            $pdf->Cell(100, 5, ': ' . $this->cetak->nama_wali_siswa($siswa));
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
            foreach ($pelanggaran as $detail_pelanggaran) {
                $data_header = array(
                    array('align' => 'C', 'width' => 10, 'height' => 5, 'text' => $i++),
                    array('align' => 'L', 'width' => 35, 'text' => $this->date_format->get_day($detail_pelanggaran->TANGGAL_INPUT) . ', ' . $this->date_format->to_print_short($detail_pelanggaran->TANGGAL_INPUT)),
                    array('align' => 'L', 'width' => 90, 'text' => $detail_pelanggaran->NAMA_KJP),
                    array('align' => 'L', 'width' => 40, 'text' => $this->date_format->get_day($detail_pelanggaran->TANGGAL_KS) . ', ' . $this->date_format->to_print_short($detail_pelanggaran->TANGGAL_KS)),
                    array('align' => 'C', 'width' => 15, 'text' => $detail_pelanggaran->POIN_KJP)
                );

                $pdf = $this->pdf_handler->wrap_row_table($pdf, $data_header);
            }
            $pdf->SetFont('Arial', 'I', 8);
            $pdf->Cell(0, 5, 'Dicetak tanggal: ' . $this->date_format->to_print_short(date('Y-m-d')), 0, 0, 'R');
        }

        if ($temp_kelas != null) {
            $pdf->AddPage("P", "A4");
            
            for ($i = 0; $i < 300; $i++) {
                $pdf->Line(0, $i, 250, $i);
            }

            $pdf->SetY(100);
            $pdf->SetFont('Arial', 'B', 40);
            $pdf->Cell(0, 5, 'DIVIDER', 0, 0, 'C');
            $pdf->Ln();
        }

        $temp_kelas = $detail->NAMA_KELAS;

        $temp_siswa = array();

        $pdf->AddPage("P", "A4");
        //	$pdf->SetMargins(6, 0);
        $pdf->SetAutoPageBreak(true, 0);

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 5, 'DATA LARI SISWA LEBIH DARI 2', 0, 0, 'C');
        $pdf->Ln(8);

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(100, 5, 'Kelas: ' . $detail->NAMA_KELAS);
        $pdf->Cell(0, 5, 'Waki Kelas: ' . $this->cetak->nama_peg_print_title($detail->GELAR_AWAL_PEG, $detail->NAMA_PEG, $detail->GELAR_AKHIR_PEG));
        $pdf->Ln();

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(20, 5, 'No Absen', 1, 0, 'C');
        $pdf->Cell(25, 5, 'NIS', 1, 0, 'C');
        $pdf->Cell(55, 5, 'Nama', 1, 0, 'C');
        $pdf->Cell(40, 5, 'Nama Ayah', 1, 0, 'C');
        $pdf->Cell(25, 5, 'Jumlah Poin', 1, 0, 'C');
        $pdf->Cell(25, 5, 'Jumlah Lari', 1, 0, 'C');
        $pdf->Ln();

        $pdf->SetFont('Arial', '', 10);
    }

    $pdf->Cell(20, 5, $detail->NO_ABSEN_AS, 1, 0, 'C');
    $pdf->Cell(25, 5, $detail->NIS_SISWA, 1, 0, 'C');
    $pdf->Cell(55, 5, $detail->NAMA_SISWA, 1, 0, 'L');
    $pdf->Cell(40, 5, $detail->AYAH_NAMA_SISWA, 1, 0, 'L');
    $pdf->Cell(25, 5, $detail->JUMLAH_POIN_KSH, 1, 0, 'C');
    $pdf->Cell(25, 5, $detail->JUMLAH_LARI_KSH, 1, 0, 'C');
    $pdf->Ln();

    $temp_siswa[] = $key;
}

$pdf->Output();
