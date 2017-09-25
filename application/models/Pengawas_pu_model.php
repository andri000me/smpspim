<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Pengawas_pu_model extends CI_Model {

    var $table = 'pu_pengawas';

    public function __construct() {
        parent::__construct();
    }

    private function _get_table() {
        $this->db->from($this->table);
        $this->db->join('pu_jadwal pj',$this->table.'.JADWAL_PENG=pj.ID_PUJ');
        $this->db->join('md_ruang mr',$this->table.'.RUANGAN_PENG=mr.KODE_RUANG');
        $this->db->join('md_pegawai mp',$this->table.'.PEGAWAI_PENG=mp.ID_PEG');
    }

    public function get_by_jadwal_lk($id) {
        $this->_get_table();
        $this->db->where('JADWAL_PENG', $id);
        $this->db->where('JK_PENG', 'L');
        $this->db->order_by('RUANGAN_PENG', 'ASC');

        return $this->db->get()->result_array();
    }

    public function get_by_jadwal_pr($id) {
        $this->_get_table();
        $this->db->where('JADWAL_PENG', $id);
        $this->db->where('JK_PENG', 'P');
        $this->db->order_by('CREATED_PENG', 'ASC');

        return $this->db->get()->result_array();
    }

    public function get_by_jadwal_ruang($id, $jk, $ruangan) {
        $this->_get_table();
        $this->db->where(array(
            'JADWAL_PENG' => $id,
            'JK_PENG' => $jk,
            'RUANGAN_PENG' => $ruangan,
        ));
        $this->db->order_by('CREATED_PENG', 'ASC');

        return $this->db->get()->row();
    }

    public function get_by_jadwal($id) {
        $this->db->select($this->table.'.*, mp.NAMA_PEG');
        $this->_get_table();
        $this->db->where('JADWAL_PENG', $id);
        $this->db->order_by('CREATED_PENG', 'ASC');

        return $this->db->get()->result_array();
    }

    public function save($data) {
        $this->db->insert($this->table, $data);

        return $this->db->insert_id();
    }

    public function update($where, $data) {
        $this->db->update($this->table, $data, $where);
        
        return $this->db->affected_rows();
    }

    public function delete_by_jadwal($id) {
        $where = array('JADWAL_PENG' => $id);
        $this->db->delete($this->table, $where);
        
        return $this->db->affected_rows();
    }

}
