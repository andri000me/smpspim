<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Psb_validasi_model extends CI_Model {

    var $table = 'psb_validasi';
    var $primary_key = "ID_PSB_V";

    public function __construct() {
        parent::__construct();
    }

    private function _get_table() {
        $this->db->from($this->table);
        $this->db->join('md_tahun_ajaran ta',$this->table.'.TA_PSB_V=ta.ID_TA');
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
    
    public function update_status($status) {
        $data['STATUS_PSB_V'] = $status;
        $where['TA_PSB_V'] = $this->session->userdata('ID_PSB_ACTIVE');
        
        $this->db->update($this->table, $data, $where);
        
        return $this->db->affected_rows();
    }
    
    public function simpan_status($status) {
        $data = array(
            'STATUS_PSB_V' => $status,
            'TA_PSB_V' => $this->session->userdata('ID_PSB_ACTIVE')
        );
        
        $this->db->insert($this->table, $data);
        
        return $this->db->insert_id();
    }
    
    public function is_status_ada() {
        $this->_get_table();
        $this->db->where(array(
            'TA_PSB_V' => $this->session->userdata('ID_PSB_ACTIVE')
        ));

        if ($this->db->count_all_results() > 0) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    public function is_psb_tutup() {
        $this->_get_table();
        $this->db->where(array(
            'TA_PSB_V' => $this->session->userdata('ID_PSB_ACTIVE'),
            'STATUS_PSB_V' => 1
        ));

        if ($this->db->count_all_results() > 0) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

}
