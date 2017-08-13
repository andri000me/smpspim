<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Nis_model extends CI_Model {

    var $table = 'md_nis';
    var $primary_key = "ID_NIS";

    public function __construct() {
        parent::__construct();
    }

    public function get_by_id($id) {
        $this->db->from($this->table);
        $this->db->where($this->primary_key, $id);

        return $this->db->get()->row();
    }

    public function get_all() {
        $this->db->from($this->table);

        return $this->db->get()->result();
    }

    public function save($data) {
        $this->db->insert($this->table, $data);

        return $this->db->insert_id();
    }

    public function get_nis_null() {
        $this->db->from('md_siswa ms');
        $this->db->join('akad_siswa as', 'ms.ID_SISWA=as.SISWA_AS AND as.AKTIF_AS=1 AND as.TA_AS="'.$this->session->userdata('ID_TA_ACTIVE').'" ');
        $this->db->join('md_tingkat mdt', 'mdt.ID_TINGK=as.TINGKAT_AS');
        $this->db->join('akad_kelas ak', 'as.KELAS_AS=ak.ID_KELAS');
        $this->db->join('md_nis mn', 'mn.SISWA_NIS=ms.ID_SISWA', 'LEFT');
        $this->db->where(array(
            'KELAS_AS <> ' => NULL,
            'STATUS_PSB_SISWA' => 1,
            'AKTIF_KELAS' => 1,
            'NIS_SISWA' => NULL,
            'KONVERSI_AS' => 0
        ));
        $this->db->order_by('JK_KELAS', 'ASC');
        $this->db->order_by('TINGKAT_KELAS', 'ASC');
        $this->db->order_by('NAMA_KELAS', 'ASC');
        $this->db->order_by('NAMA_SISWA', 'ASC');
        
        $query = $this->db->get();
        
        return $query->result_array();
    }

    public function get_siswa($ID_SISWA) {
        $this->db->from('md_siswa ms');
        $this->db->join('akad_siswa as', 'ms.ID_SISWA=as.SISWA_AS AND as.AKTIF_AS=1 AND as.TA_AS="'.$this->session->userdata('ID_TA_ACTIVE').'" ');
        $this->db->join('md_tingkat mdt', 'mdt.ID_TINGK=as.TINGKAT_AS');
        $this->db->where(array(
            'ID_SISWA' => $ID_SISWA,
            'KONVERSI_AS' => 0
        ));
        
        $query = $this->db->get();
        
        return $query->row();
    }
    
    public function nisNULL($ID_SISWA) {
        $this->db->from('md_siswa');
        $this->db->where(array(
            'ID_SISWA' => $ID_SISWA,
            'NIS_SISWA' => NULL,
        ));
        
        if($this->db->count_all_results() > 0) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

}