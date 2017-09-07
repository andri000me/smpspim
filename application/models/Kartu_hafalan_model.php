<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Kartu_hafalan_model extends CI_Model {

    var $table = 'akad_siswa';
    var $primary_key = "ID_AS";

    public function __construct() {
        parent::__construct();
    }

    private function _get_table() {
        $this->db->from($this->table);
        $this->db->join('md_siswa ms',$this->table.'.SISWA_AS=ms.ID_SISWA');
        $this->db->join('md_tingkat mt',$this->table.'.TINGKAT_AS=mt.ID_TINGK');
        $this->db->join('akad_kelas ak',$this->table.'.KELAS_AS=ak.ID_KELAS');
        $this->db->join('md_pegawai mp','ak.WALI_KELAS=mp.ID_PEG', 'LEFT');
        $this->db->where(array(
            'TA_AS' => $this->session->userdata('ID_TA_ACTIVE'),
            'KONVERSI_AS' => 0,
        ));
        $this->db->order_by('KELAS_AS', 'ASC');
        $this->db->order_by('NAMA_SISWA', 'ASC');
    }

    public function get_by_id($id) {
        $this->_get_table();
        $this->db->where($this->primary_key, $id);

        return $this->db->get()->row();
    }

    public function get_all() {
        $this->_get_table();

        return $this->db->get()->result();
    }

    public function get_by_siswa($id) {
        $this->_get_table();
        $this->db->where('ID_SISWA', $id);

        return $this->db->get()->result();
    }

    public function get_batasan($TA, $TINGKAT, $JK) {
        $this->db->from('ph_batasan pb');
        $this->db->join('ph_kitab pk','pb.KITAB_BATASAN=pk.ID_KITAB');
        $this->db->where(array(
            'TA_BATASAN' => $TA,
            'TINGKAT_BATASAN' => $TINGKAT,
            'JK_BATASAN' => $JK,
        ));

        return $this->db->get()->result();
    }

    public function get_siswa($where) {
        $this->db->select("ID_SISWA as id, CONCAT(NIS_SISWA, ' - ',NAMA_SISWA, ' | KELAS: ', NAMA_KELAS) as text");
        $this->_get_table();
        $this->db->where('NIS_SISWA <> ', NULL);
        $this->db->where('NAMA_KELAS <> ', NULL);
        $this->db->like('NAMA_SISWA', $where);

        return $this->db->get()->result();
    }

    public function save($data) {
        $this->db->insert($this->table, $data);

        return $this->db->insert_id();
    }

    public function update($where, $data) {
        $this->db->update($this->table, $data, $where);
        
        return $this->db->affected_rows();
    }

    public function delete_by_id($id) {
        $where = array($this->primary_key => $id);
        $this->db->delete($this->table, $where);
        
        return $this->db->affected_rows();
    }

    public function get_rows($where) {
        $this->_get_table();
        $this->db->where($where);
        $this->db->order_by('NO_ABSEN_AS', 'ASC');

        return $this->db->get()->result();
    }

    public function get_rows_array($where) {
        $this->_get_table();
        $this->db->where($where);

        return $this->db->get()->result_array();
    }
}
