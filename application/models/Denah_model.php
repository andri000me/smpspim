<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Denah_model extends CI_Model {

    var $table = 'pu_denah';
    var $primary_key = "ID_DENAH";

    public function __construct() {
        parent::__construct();
    }

    private function _get_table() {
        $this->db->from($this->table);
        $this->db->join('pu_aturan_denah ad', $this->table . '.ATURAN_DENAH=ad.ID_PUD');
    }

    public function get_by_id($id) {
        $this->_get_table();
        $this->db->where($this->primary_key, $id);

        return $this->db->get()->row();
    }

    public function get_row($where) {
        $this->_get_table();
        $this->db->where($where);

        return $this->db->get()->row();
    }

    public function get_rows($where) {
        $this->_get_table();
        $this->db->where($where);

        return $this->db->get()->result();
    }

    public function get_rows_array($where) {
        $this->_get_table();
        $this->db->where($where);
        $this->db->order_by('JADWAL_DENAH', 'ASC');

        return $this->db->get()->result_array();
    }

    public function get_denah_by_tanggal($tanggal) {
        $this->db->from($this->table);
        $this->db->where('JADWAL_DENAH', $tanggal);

        return $this->db->get()->row()->SISWA_DENAH;
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
    
    public function is_denah_exist($tanggal) {
        $this->_get_table();
        $this->db->where('JADWAL_DENAH', $tanggal);

        if($this->db->count_all_results() > 0)
            return TRUE;
        else 
            return FALSE;
    }

}
