<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Cetak {
    
    var $margin_title = 25;
    var $length_kop = 90;

    public function __construct() {
        $this->CI = & get_instance();
        $this->setup = $this->CI->pengaturan;
    }
    
    public function logo_yayasan($pdf, $margin = 0) {
        $pdf->Image(base_url($this->setup->getLogo()), 13, 10 - $margin, 23, 24, '', '');
        
        return $pdf;
    }

    public function header_yayasan($pdf, $margin = 0) {
        if($margin > 0) $margin = 10 - $margin;
        
        $pdf = $this->logo_yayasan($pdf, $margin);
        
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell($this->margin_title);
        $pdf->Cell($this->length_kop, 5, strtoupper($this->setup->getNamaLembaga()), 0, 0, 'C');
        $pdf->Image(base_url('files/aplikasi/kop_1.png'), 130, 10 - $margin, 55, 7, '', '');
        
        $pdf->Ln(5);
        
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell($this->margin_title);
        $pdf->Cell($this->length_kop, 5, strtoupper($this->setup->getNamaYayasan()), 0, 0, 'C');
        $pdf->Image(base_url('files/aplikasi/kop_2.png'), 142, 17 - $margin, 30, 7, '', '');
        
        $pdf->Ln(5);
        
        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell($this->margin_title);
        $pdf->Cell($this->length_kop, 5, strtoupper($this->setup->getDesa().' - '.$this->setup->getKecamatan().' - '.$this->setup->getKabupaten().' '.$this->setup->getKodepos()), 0, 0, 'C');
        $pdf->Image(base_url('files/aplikasi/kop_3.png'), 139, 23 - $margin, 35, 6, '', '');
        
        $pdf->Ln(4);
        
        $pdf->Cell($this->margin_title);
        $pdf->Cell($this->length_kop, 5, strtoupper($this->setup->getProvinsi().' - '.$this->setup->getNegara()), 0, 0, 'C');
        
        $pdf->Ln(4);
        
        $pdf->Cell($this->margin_title);
        $pdf->Cell($this->length_kop, 5, 'TELP. '.$this->setup->getTelp().' FAX. '.$this->setup->getFax(), 0, 0, 'C');
        $pdf->Image(base_url('files/aplikasi/kop_4.png'), 130, 28 - $margin, 60, 6, '', '');
        
        $pdf->Ln(8);
        
        $pdf->SetLineWidth(0.50);
        $pdf->Line(11, 35  - $margin, 200, 35 - $margin);
        $pdf->SetLineWidth(0.30);
        $pdf->Line(11, 36 - $margin, 200, 36 - $margin);
        
        return $pdf;
    }
    
    public function replace_string($string) {
        if((strtolower($string) == 'muhammad') || (strtolower($string) == 'muhamad') || (strtolower($string) == 'mohammad') || (strtolower($string) == 'mohammad') || (strtolower($string) == 'mukhammad') || (strtolower($string) == 'mukhamad') || (strtolower($string) == 'mokhammad') || (strtolower($string) == 'mokhammad') || (strtolower($string) == 'muchammad') || (strtolower($string) == 'muchamad') || (strtolower($string) == 'mochammad') || (strtolower($string) == 'mochammad') || (strtolower($string) == 'muh') || (strtolower($string) == 'muh.') || (strtolower($string) == 'moh') || (strtolower($string) == 'moh.'))
            return "M";
        elseif((strtolower($string) == 'ahmad') || (strtolower($string) == 'achmad') || (strtolower($string) == 'ahmed'))
            return 'A';
        else
            return $string;
    }
    
    public function nama_pendek_3($nama) {
        $nama_exp = explode(" ", trim($nama));
        return ((count($nama_exp) > 1) ? $this->replace_string($nama_exp[0]) : $nama_exp[0]) .' '.(isset($nama_exp[1]) ? $this->replace_string($nama_exp[1]) : "").' '.(isset($nama_exp[2]) ? substr($nama_exp[0], 0, 1).'.' : "");
    }

    public function header_panitia_a5($pdf, $nama_modul) {
        $pdf = $this->logo_yayasan($pdf);
        
        $margin_title = 30;
        
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell($margin_title);
        $pdf->Cell($this->length_kop, 5, strtoupper($this->setup->getNamaLembaga()), 0, 0, 'C');
        $pdf->Ln(5);
        
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell($margin_title);
        $pdf->Cell($this->length_kop, 5, strtoupper(($nama_modul == NULL) ? $this->setup->getNamaYayasan() : $nama_modul), 0, 0, 'C');
        
        $pdf->Ln(5);
        
        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell($margin_title);
        $pdf->Cell($this->length_kop, 5, strtoupper($this->setup->getDesa().' - '.$this->setup->getKecamatan().' - '.$this->setup->getKabupaten().' '.$this->setup->getKodepos()), 0, 0, 'C');
        
        $pdf->Ln(4);
        
        $pdf->Cell($margin_title);
        $pdf->Cell($this->length_kop, 5, strtoupper($this->setup->getProvinsi().' - '.$this->setup->getNegara()), 0, 0, 'C');
        
        $pdf->Ln(4);
        
        $pdf->Cell($margin_title);
        $pdf->Cell($this->length_kop, 5, 'TELP. '.$this->setup->getTelp().' FAX. '.$this->setup->getFax(), 0, 0, 'C');
        
        $pdf->Ln(8);
        
        $pdf->SetLineWidth(0.50);
        $pdf->Line(11, 35, 138, 35);
        $pdf->SetLineWidth(0.30);
        $pdf->Line(11, 36, 138, 36);
        
        return $pdf;
    }

    public function header_panitia_a4($pdf, $nama_modul) {
        $pdf = $this->logo_yayasan($pdf);
        
        $margin_title = 33;
        
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell($margin_title);
        $pdf->Cell(0, 5, strtoupper($this->setup->getNamaLembaga()), 0, 0, 'L');
        $pdf->Ln(6);
        
        $pdf->SetFont('Arial', 'B', 18);
        $pdf->Cell($margin_title);
        $pdf->Cell(0, 5, strtoupper($this->setup->getNamaYayasan()), 0, 0, 'L');
        $pdf->Ln(6);
        
        $pdf->SetFont('Arial', 'B', 20);
        $pdf->Cell($margin_title);
        $pdf->Cell(0, 5, strtoupper($nama_modul), 0, 0, 'L');
        $pdf->Ln(6);
        
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell($margin_title);
        $pdf->Cell(0, 5, strtoupper($this->setup->getDesa().' - '.$this->setup->getKecamatan().' - '.$this->setup->getKabupaten().' '.$this->setup->getKodepos()).' TELP. '.$this->setup->getTelp().' FAX. '.$this->setup->getFax(), 0, 0, 'L');
        
        $pdf->Ln(8);
        
        $pdf->SetLineWidth(0.50);
        $pdf->Line(11, 35, 200, 35);
        $pdf->SetLineWidth(0.30);
        $pdf->Line(11, 36, 200, 36);
        
        return $pdf;
    }

    public function header_yayasan_dotmartrix($pdf) {
        $pdf->Image(base_url($this->setup->getLogo()), 25, 9, 23, 24, '', '');
        
        $this->length_kop = 150;
        
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell($this->margin_title);
        $pdf->Cell($this->length_kop, 5, strtoupper($this->setup->getNamaLembaga()), 0, 0, 'C');
        $pdf->Image(base_url('files/aplikasi/kop_1.png'), 177, 10, 55, 7, '', '');
        
        $pdf->Ln(5);
        
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell($this->margin_title);
        $pdf->Cell($this->length_kop, 5, strtoupper($this->setup->getNamaYayasan()), 0, 0, 'C');
        $pdf->Image(base_url('files/aplikasi/kop_2.png'), 189, 17, 30, 7, '', '');
        
        $pdf->Ln(5);
        
        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell($this->margin_title);
        $pdf->Cell($this->length_kop, 5, strtoupper($this->setup->getDesa().' - '.$this->setup->getKecamatan().' - '.$this->setup->getKabupaten().' '.$this->setup->getKodepos()), 0, 0, 'C');
        $pdf->Image(base_url('files/aplikasi/kop_3.png'), 188, 23, 35, 6, '', '');
        
        $pdf->Ln(4);
        
        $pdf->Cell($this->margin_title);
        $pdf->Cell($this->length_kop, 5, strtoupper($this->setup->getProvinsi().' - '.$this->setup->getNegara()), 0, 0, 'C');
        
        $pdf->Ln(4);
        
        $pdf->Cell($this->margin_title);
        $pdf->Cell($this->length_kop, 5, 'TELP. '.$this->setup->getTelp().' FAX. '.$this->setup->getFax(), 0, 0, 'C');
        $pdf->Image(base_url('files/aplikasi/kop_4.png'), 177, 28, 60, 6, '', '');
        
        $pdf->Ln(8);
        
        $pdf->SetLineWidth(0.50);
        $pdf->Line(11, 35, 267, 35);
        $pdf->SetLineWidth(0.30);
        $pdf->Line(11, 36, 267, 36);
        
        return $pdf;
    }

    public function nama_peg_print($detail_guru) {
        if (is_array($detail_guru)) {
            return 'Ust. '.($detail_guru['GELAR_AWAL_PEG'] == NULL ? '' : $detail_guru['GELAR_AWAL_PEG'].'. ').$detail_guru['NAMA_PEG'].($detail_guru['GELAR_AKHIR_PEG'] == NULL ? '' : ', '.$detail_guru['GELAR_AKHIR_PEG']);
        } elseif (is_object($detail_guru)) {
            return 'Ust. '.($detail_guru->GELAR_AWAL_PEG == NULL ? '' : $detail_guru->GELAR_AWAL_PEG.'. ').$detail_guru->NAMA_PEG.($detail_guru->GELAR_AKHIR_PEG == NULL ? '' : ', '.$detail_guru->GELAR_AKHIR_PEG);
        } else {
            return "";
        }
    }

}
