<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Laporan_pegawai_model extends CI_Model {

    var $table = 'md_pegawai';
    
    public function __construct() {
        parent::__construct();
    }

    private function _get_table() {
        $this->db->from($this->table);
        $this->db->join('md_jenis_kelamin mjk', $this->table.'.JK_PEG=mjk.ID_JK', 'LEFT');
        $this->db->join('md_suku msk', $this->table.'.SUKU_PEG=msk.ID_SUKU', 'LEFT');
        $this->db->join('md_agama mag', $this->table.'.AGAMA_PEG=mag.ID_AGAMA', 'LEFT');
        $this->db->join('md_kecamatan kec', $this->table.'.KECAMATAN_PEG=kec.ID_KEC');
        $this->db->join('md_kabupaten kab', 'kec.KABUPATEN_KEC=kab.ID_KAB');
        $this->db->join('md_provinsi prov', 'kab.PROVINSI_KAB=prov.ID_PROV');
    }
    
    public function get_data($label, $keaktifan) {
        if($label == 'AKTIF_PEG') 
           $this->db->select('COUNT(ID_PEG) AS data, IF('.$label.' IS NULL, CONCAT("TIDAK" , " ", "ADA", " ", "DATA"), IF('.$label.' = 1, "AKTIF", CONCAT("TIDAK", " ", "AKTIF")) ) AS x_label');
        elseif($label == 'GURU_PEG') 
           $this->db->select('COUNT(ID_PEG) AS data, IF('.$label.' IS NULL, CONCAT("TIDAK" , " ", "ADA", " ", "DATA"), IF('.$label.' = 1, "GURU", CONCAT("BUKAN", " ", "GURU")) ) AS x_label');
        elseif($label == 'TANGGAL_LAHIR_PEG')
           $this->db->select('COUNT(ID_PEG) AS data, IF('.$label.' IS NULL, CONCAT("TIDAK" , " ", "ADA", " ", "DATA"), (YEAR(CURDATE()) - LEFT('.$label.', 4))) AS x_label');
        else
            $this->db->select('COUNT(ID_PEG) AS data, IF('.$label.' IS NULL, CONCAT("TIDAK" , " ", "ADA", " ", "DATA"), '.$label.') AS x_label');
        $this->_get_table();
        if($keaktifan != "") $this->db->where('AKTIF_PEG',$keaktifan);
        
        if($label == 'TANGGAL_LAHIR_PEG')
            $this->db->group_by('LEFT('.$label.', 4)');
        else
            $this->db->group_by($label);
        
        $this->db->order_by('x_label', 'ASC');
        
        return $this->db->get()->result();
    }
    
    public function export_data($keaktifan) {
        $this->load->dbutil();
        
        $this->db->select(''
                . 'NIP_PEG AS NIP'
                . ',NIK_PEG AS NIK'
                . ',NAMA_PEG AS NAMA'
                . ',GELAR_AWAL_PEG AS GELAR_AWAL'
                . ',GELAR_AKHIR_PEG AS GELAR_AKHIR'
                . ',PANGGILAN_PEG AS PANGGILAN'
                . ',GURU_PEG AS STATUS_GURU'
                . ',JK_PEG AS KODE_JENIS_KELAMIN'
                . ',NAMA_JK AS NAMA_JENIS_KELAMIN'
                . ',TEMPAT_LAHIR_PEG AS TEMPAT_LAHIR'
                . ',TANGGAL_LAHIR_PEG AS TANGGAL_LAHIR'
                . ',AGAMA_PEG AS KODE_AGAMA'
                . ',NAMA_AGAMA AS NAMA_AGAMA'
                . ',SUKU_PEG AS KODE_SUKU'
                . ',NAMA_SUKU AS NAMA_SUKU'
                . ',MENIKAH_PEG AS STATUS_PERNIKAHAN'
                . ',ALAMAT_PEG AS ALAMAT'
                . ',KECAMATAN_PEG AS KODE_KECAMATAN'
                . ',NAMA_KEC AS NAMA_KECAMATAN'
                . ',ID_KAB AS KODE_KABUPATEN'
                . ',NAMA_KAB AS NAMA_KABUPATEN'
                . ',ID_PROV AS KODE_PROVINSI'
                . ',NAMA_PROV AS NAMA_PROVINSI'
                . ',NOHP_PEG AS NO_HP'
                . ',EMAIL_PEG AS EMAIL'
                . ',KETERANGAN_PEG AS KETERANGAN'
                . ',AKTIF_PEG AS STATUS_KEAKTIFAN'
                . '');
        $this->_get_table();
        if($keaktifan != "") $this->db->where('AKTIF_PEG',$keaktifan);
        
        $sql = $this->db->get();
        
        return $this->dbutil->csv_from_result($sql);
    }
}
