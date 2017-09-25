<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Mapel_pu_model extends CI_Model {

    var $table = 'pu_mapel';

    public function __construct() {
        parent::__construct();
    }

    private function _get_table() {
        $this->db->from($this->table);
        $this->db->join('pu_jadwal pj',$this->table.'.JADWAL_PUM=pj.ID_PUJ');
        $this->db->join('md_tingkat mt',$this->table.'.TINGKAT_PUM=mt.ID_TINGK');
        $this->db->join('md_departemen md','mt.DEPT_TINGK=md.ID_DEPT');
        $this->db->join('md_jenjang_departemen jp','mt.DEPT_TINGK=jp.DEPT_MJD');
        $this->db->join('md_jenjang_sekolah js','js.ID_JS=jp.JENJANG_MJD');
        $this->db->join('md_mapel mm',$this->table.'.MAPEL_PUM=mm.ID_MAPEL');
    }

    public function get_by_jadwal($id) {
        $this->_get_table();
        $this->db->where('JADWAL_PUM', $id);
        $this->db->order_by('ID_JS', 'ASC');

        return $this->db->get()->result_array();
    }

    public function get_all_by_jadwal($tanggal) {
        $this->_get_table();
        $this->db->where('TA_PUJ', $this->session->userdata('ID_PSB_ACTIVE'));
        $this->db->order_by('TINGKAT_PUM', 'ASC');

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
        $where = array('JADWAL_PUM' => $id);
        $this->db->delete($this->table, $where);
        
        return $this->db->affected_rows();
    }

}
