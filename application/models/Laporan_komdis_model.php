<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Laporan_komdis_model extends CI_Model {

    var $table_detail = 'komdis_siswa';
    var $table_header = 'komdis_siswa_header';

    public function __construct() {
        parent::__construct();
    }

    private function _get_table_detail($label = NULL) {
        $this->db->from($this->table_detail);
        $this->db->join('md_tahun_ajaran mta', $this->table_detail.'.TA_KS=mta.ID_TA');
        $this->db->join('md_catur_wulan mcw', $this->table_detail.'.CAWU_KS=mcw.ID_CAWU');
        $this->db->join('md_siswa ms', $this->table_detail.'.SISWA_KS=ms.ID_SISWA');
        $this->db->join('md_pegawai mps', $this->table_detail.'.SUMBER_KS=mps.ID_PEG');
        $this->db->join('komdis_jenis_pelanggaran kjp', $this->table_detail.'.PELANGGARAN_KS=kjp.ID_KJP');
    }
    
    private function _get_table_header($label = NULL) {
        $this->db->from($this->table_header);
        $this->db->join('md_tahun_ajaran mta', $this->table_header.'.TA_KSH=mta.ID_TA');
        $this->db->join('md_catur_wulan mcw', $this->table_header.'.CAWU_KSH=mcw.ID_CAWU');
        $this->db->join('md_siswa ms', $this->table_header.'.SISWA_KSH=ms.ID_SISWA');
    }
    
    private function _get_table_akademik($ta = NULL) {
        $this->db->join('akad_siswa asw', 'ms.ID_SISWA=asw.SISWA_AS AND asw.TA_AS="'.$ta.'" AND asw.KONVERSI_AS=0');
        $this->db->join('akad_kelas ak', 'asw.KELAS_AS=ak.ID_KELAS');
        $this->db->join('md_pegawai mp','ak.WALI_KELAS=mp.ID_PEG');
        $this->db->join('md_tingkat mtnow', 'asw.TINGKAT_AS=mtnow.ID_TINGK');
        $this->db->join('md_departemen mdp', 'mtnow.DEPT_TINGK=mdp.ID_DEPT');
    }

    public function get_data($mode, $label, $ta, $tingkat, $kelas, $cawu, $bulan, $tahun) {
        if($label == 'PELANGGARAN_KS')
            $this->db->select('COUNT(ID_SISWA) AS data, IF(' . $label . ' IS NULL, CONCAT("TIDAK" , " ", "ADA", " ", "DATA"), CONCAT(INDUK_KJP, IF(ANAK_KJP IS NULL, "", CONCAT(".", ANAK_KJP)))) AS x_label');
        elseif($label == 'SUMBER_KS')
            $this->db->select('COUNT(ID_SISWA) AS data, IF(' . $label . ' IS NULL, CONCAT("TIDAK" , " ", "ADA", " ", "DATA"), NIP_PEG) AS x_label');
        else
            $this->db->select('COUNT(ID_SISWA) AS data, IF(' . $label . ' IS NULL, CONCAT("TIDAK" , " ", "ADA", " ", "DATA"), ' . $label . ') AS x_label');
        
        if($mode == 'detail') $this->_get_table_detail($label);
        elseif($mode == 'header') $this->_get_table_header($label);

        if ($ta != "")
            $this->db->where('ID_TA', $ta);
        if ($tingkat != "")
            $this->db->where('TINGKAT_AS', $tingkat);
        if ($kelas != "")
            $this->db->where('KELAS_AS', $kelas);
        if ($cawu != "")
            $this->db->where('ID_CAWU', $cawu);
        if ($bulan != 0)
            $this->db->where('MONTH(TANGGAL_KS)', $bulan);
        if ($tahun != "")
            $this->db->where('YEAR(TANGGAL_KS)', $tahun);

        if ($label == 'TANGGAL_KS')
            $this->db->group_by('YEARS(' . $label . ')');
        else
            $this->db->group_by($label);

        $this->db->order_by('x_label', 'ASC');

        return $this->db->get()->result();
    }

    public function export_data($mode, $ta, $tingkat, $kelas, $cawu, $bulan, $tahun) {
        $this->load->dbutil();

        $select = 'ID_TA AS KODE_TA'
            . ',NAMA_TA AS NAMA_TA'
            . ',ID_CAWU AS KODE_CAWU'
            . ',NAMA_CAWU AS NAMA_CAWU'
            . ',NIS_SISWA AS NIS'
            . ',NAMA_SISWA AS NAMA_SISWA'
            . ',mp.NIP_PEG AS NIP_WALI_KELAS'
            . ',mp.NAMA_PEG AS NAMA_WALI_KELAS';
        
        if($mode == 'detail') 
            $select .= ',CONCAT(INDUK_KJP, IF(ANAK_KJP IS NULL, "", CONCAT(".", ANAK_KJP))) AS KODE_PELANGGARAN'
                . ',NAMA_KJP AS NAMA_PELANGGARAN'
                . ',POIN_KJP AS POIN_PELANGGARAN'
                . ',TANGGAL_KS AS TANGGAL_MELANGGAR'
                . ',mps.ID_PEG AS NIP_SUMBER_INFORMASI'
                . ',mps.NAMA_PEG AS NAMA_SUMBER_INFORMASI'
                . ',KETERANGAN_KS AS KETERANGAN';
        else
            $select .= ',POIN_KSH AS POIN_SISWA'
                . ',LARI_KSH AS LARI';
        
        $this->db->select($select);
        
        if($mode == 'detail') $this->_get_table_detail();
        else $this->_get_table_header();
        
        $this->_get_table_akademik($ta == "" ? $this->session->userdata("ID_TA_ACTIVE") : $ta);

        if ($ta != "")
            $this->db->where('ID_TA', $ta);
        if ($tingkat != "")
            $this->db->where('TINGKAT_AS', $tingkat);
        if ($kelas != "")
            $this->db->where('KELAS_AS', $kelas);
        if ($cawu != "")
            $this->db->where('ID_CAWU', $cawu);
        if ($bulan != 0)
            $this->db->where('MONTH(TANGGAL_KS)', $bulan);
        if ($tahun != "")
            $this->db->where('YEAR(TANGGAL_KS)', $tahun);
        
        $sql = $this->db->get();

        return $this->dbutil->csv_from_result($sql);
    }

}
