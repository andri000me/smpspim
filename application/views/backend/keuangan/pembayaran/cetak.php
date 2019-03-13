<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 * 
  $this->CoreFonts = array('Arial', 'helvetica', 'times', 'symbol', 'zapfdingbats');
 * 
 */

class PDF_Rotate extends FPDF {

    var $angle = 0;

    function Rotate($angle, $x = -1, $y = -1) {
        if ($x == -1)
            $x = $this->x;
        if ($y == -1)
            $y = $this->y;
        if ($this->angle != 0)
            $this->_out('Q');
        $this->angle = $angle;
        if ($angle != 0) {
            $angle *= M_PI / 180;
            $c = cos($angle);
            $s = sin($angle);
            $cx = $x * $this->k;
            $cy = ($this->h - $y) * $this->k;
            $this->_out(sprintf('q %.5F %.5F %.5F %.5F %.2F %.2F cm 1 0 0 1 %.2F %.2F cm', $c, $s, -$s, $c, $cx, $cy, -$cx, -$cy));
        }
    }

    function _endpage() {
        if ($this->angle != 0) {
            $this->angle = 0;
            $this->_out('Q');
        }
        parent::_endpage();
    }

}

class AlphaPDF extends PDF_Rotate {

    var $extgstates = array();

    // alpha: real value from 0 (transparent) to 1 (opaque)
    // bm:    blend mode, one of the following:
    //          Normal, Multiply, Screen, Overlay, Darken, Lighten, ColorDodge, ColorBurn,
    //          HardLight, SoftLight, Difference, Exclusion, Hue, Saturation, Color, Luminosity
    function SetAlpha($alpha, $bm = 'Normal') {
        // set alpha for stroking (CA) and non-stroking (ca) operations
        $gs = $this->AddExtGState(array('ca' => $alpha, 'CA' => $alpha, 'BM' => '/' . $bm));
        $this->SetExtGState($gs);
    }

    function AddExtGState($parms) {
        $n = count($this->extgstates) + 1;
        $this->extgstates[$n]['parms'] = $parms;
        return $n;
    }

    function SetExtGState($gs) {
        $this->_out(sprintf('/GS%d gs', $gs));
    }

    function _enddoc() {
        if (!empty($this->extgstates) && $this->PDFVersion < '1.4')
            $this->PDFVersion = '1.4';
        parent::_enddoc();
    }

    function _putextgstates() {
        for ($i = 1; $i <= count($this->extgstates); $i++) {
            $this->_newobj();
            $this->extgstates[$i]['n'] = $this->n;
            $this->_out('<</Type /ExtGState');
            $parms = $this->extgstates[$i]['parms'];
            $this->_out(sprintf('/ca %.3F', $parms['ca']));
            $this->_out(sprintf('/CA %.3F', $parms['CA']));
            $this->_out('/BM ' . $parms['BM']);
            $this->_out('>>');
            $this->_out('endobj');
        }
    }

    function _putresourcedict() {
        parent::_putresourcedict();
        $this->_out('/ExtGState <<');
        foreach ($this->extgstates as $k => $extgstate)
            $this->_out('/GS' . $k . ' ' . $extgstate['n'] . ' 0 R');
        $this->_out('>>');
    }

    function _putresources() {
        $this->_putextgstates();
        parent::_putresources();
    }

}

class PDF extends AlphaPDF {

    var $req_watermark;

    function Header() {
        //Put the watermark
        if ($this->req_watermark) {
            $this->SetAlpha(0.5);
            $this->SetFont('Arial', 'B', 80);
            $this->SetTextColor(80, 80, 255);

            if ($this->req_watermark == 1)
                $this->RotatedText(60, 100, 'C O P Y', 25);
            elseif ($this->req_watermark == 2)
                $this->RotatedText(40, 120, 'KOPERASI', 25);
            
            $this->SetAlpha(1);
        }
    }

    function SetWatermark($req_watermark) {
        $this->req_watermark = $req_watermark;
    }

    function RotatedText($x, $y, $txt, $angle) {
        //Text rotated around its origin
        $this->Rotate($angle, $x, $y);
        $this->Text($x, $y, $txt);
        $this->Rotate(0);
    }

    function RotatedImage($file, $x, $y, $w, $h, $angle) {
        //Image rotated around its upper-left corner
        $this->Rotate($angle, $x, $y);
        $this->Image($file, $x, $y, $w, $h);
        $this->Rotate(0);
    }

}

$pdf = new PDF();

for ($i = 0; $i < 3; $i++) {
    $pdf->SetWatermark($i);

    $pdf->SetMargins(7, 10);
//    $pdf->AddPage("L", "A5");
    $pdf->AddPage("L", array(215, 165));
    $pdf->SetAutoPageBreak(true, 0);

    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 5, strtoupper($this->pengaturan->getNamaLembaga()), 0, 0, 'C');
    $pdf->Ln();

    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 5, $STATUS_PSB ? 'PANITIA PENDAFTARAN SISWA BARU' : 'BIDANG KEUANGAN', 0, 0, 'C');
    $pdf->Ln();

    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(0, 5, $this->pengaturan->getDesa() . ' - ' . $this->pengaturan->getKecamatan() . ' - ' . $this->pengaturan->getKabupaten() . ' ' . $this->pengaturan->getKodepos() . ' Telp. ' . $this->pengaturan->getTelp() . ' Fax. ' . $this->pengaturan->getFax(), 0, 0, 'C');
    $pdf->Ln(8);

    $pdf->SetLineWidth(0.40);
    $pdf->Line(7, 25, 208, 25);

    $pdf->SetLineWidth(0.20);
    $pdf->Line(7, 26, 208, 26);

    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 5, 'KWITANSI PEMBAYARAN', 0, 0, 'C');
    $pdf->Ln(8);

    $pdf->SetFont('Arial', '', 10);

    $pdf->Cell(22, 5, $SISWA->NIS_SISWA == NULL ? 'NO. UM' : 'NIS', 0, 0, 'L');
    $pdf->Cell(90, 5, ': ' . ($SISWA->NIS_SISWA == NULL ? ($SISWA->NO_UM_SISWA == NULL ? '-' : $this->pengaturan->getKodeUM($SISWA)) : $SISWA->NIS_SISWA), 0, 0, 'L');
    $pdf->Cell(22, 5, $SISWA->NAMA_TA == NULL ? 'ALAMAT' : 'TA', 0, 0, 'L');
    $pdf->Cell(0, 5, ': ' . ($SISWA->NAMA_TA == NULL ? $SISWA->ALAMAT_SISWA : $SISWA->NAMA_TA), 0, 0, 'L');
    $pdf->Ln();
    $pdf->Cell(22, 5, 'NAMA', 0, 0, 'L');
    $pdf->Cell(90, 5, ': ' . $SISWA->NAMA_SISWA, 0, 0, 'L');
    $pdf->Cell(22, 5, $SISWA->KETERANGAN_TINGK_NOW == NULL ? '' : 'TINGKAT', 0, 0, 'L');
    $pdf->Cell(0, 5, ($SISWA->KETERANGAN_TINGK_NOW == NULL ? 'Kec. ' . $SISWA->NAMA_KEC_SISWA . ', ' . (str_replace("Kabupaten", "Kab.", $SISWA->NAMA_KAB_SISWA)) : ': ' . $SISWA->KETERANGAN_TINGK_NOW), 0, 0, 'L');
    $pdf->Ln();
    $pdf->Cell(22, 5, $SISWA->NAMA_PEG == NULL ? 'TTL' : 'WALI KELAS', 0, 0, 'L');
    $pdf->Cell(90, 5, ': ' . ($SISWA->NAMA_PEG == NULL ? $SISWA->TEMPAT_LAHIR_SISWA . ', ' . $this->date_format->to_print_text($SISWA->TANGGAL_LAHIR_SISWA) : $this->cetak->nama_peg_print($SISWA)), 0, 0, 'L');
    $pdf->Cell(22, 5, $SISWA->NAMA_KELAS == NULL ? 'NAMA AYAH' : 'KELAS', 0, 0, 'L');
    $pdf->Cell(0, 5, ': ' . ($SISWA->NAMA_KELAS == NULL ? $SISWA->AYAH_NAMA_SISWA : $SISWA->NAMA_KELAS), 0, 0, 'L');
    $pdf->Ln(6);

    $pdf->SetLineWidth(0.20);
    $pdf->Line(7, 52, 208, 52);

    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(5, 5, '#', 0, 0, 'L');
    $pdf->Cell(22, 5, 'TA', 0, 0, 'L');
    $pdf->Cell(50, 5, 'TAGIHAN', 0, 0, 'L');
    $pdf->Cell(83, 5, 'DETAIL', 0, 0, 'L');
    $pdf->Cell(37, 5, 'NOMINAL', 0, 0, 'L');
    $pdf->Ln();

    $pdf->Line(7, 57, 208, 57);

    $pdf->SetFont('Arial', '', 10);
    $no = 1;
    $total = 0;
    foreach ($PEMBAYARAN as $detail) {
        $total += $detail->NOMINAL_BAYAR;
        $pdf->Cell(5, 5, $no++, 0, 0, 'L');
        $pdf->Cell(22, 5, $detail->NAMA_TA, 0, 0, 'L');
        $pdf->Cell(50, 5, $detail->NAMA_TAG, 0, 0, 'L');
        $pdf->Cell(83, 5, $detail->NAMA_DT, 0, 0, 'L');
        $pdf->Cell(37, 5, $this->money->format($detail->NOMINAL_BAYAR), 0, 0, 'R');
        $pdf->Ln();
    }

    $pdf->SetY(115);
    $pdf->Line(7, 115, 208, 115);

    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(160, 5, 'TOTAL', 0, 0, 'R');
    $pdf->Cell(37, 5, $this->money->format($total), 0, 0, 'R');
    $pdf->Ln(8);

    $pdf->Line(7, 120, 208, 120);

    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(140, 5, 'KETERANGAN:', 0, 0, 'L');
    $pdf->Cell(0, 5, 'PETUGAS', 0, 0, 'L');
    $pdf->Ln();

    $pdf->MultiCell(130, 5, $KETERANGAN);
    $pdf->Ln(2);

    if ($STATUS_PSB) {
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(0, 5, $STATUS_PSB ? ($SISWA->NO_UM_SISWA == NULL ? 'DAFTAR DI: ' . $SISWA->DEPT_MJD . '-' . $SISWA->MASUK_TINGKAT_SISWA . ' (TANPA UM)' : 'NO. UM: ' . $this->pengaturan->getKodeUM($SISWA)) : '', 0, 0, 'L');
        $pdf->Ln();
    }

    $pdf->SetY(140);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(140, 5, $STATUS_PSB ? 'PEMBAYARAN PSB: ' . ($STATUS_LUNAS ? 'LUNAS' : 'BELUM LUNAS') : '', 0, 0, 'L');
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(0, 5, $this->session->userdata('FULLNAME_USER'), 0, 0, 'L');
    $pdf->Ln();

    $pdf->SetFont('Arial', 'I', 8);
    $pdf->Cell(0, 5, 'Kode: ' . $NOTA->KODE_NOTA . ' Dibuat: ' . $NOTA->CREATED_NOTA . ' Dicetak: ' . date('Y-m-d H:i:s'), 0, 0, 'L');

    $pdf->Ln();
}

$pdf->Output();
