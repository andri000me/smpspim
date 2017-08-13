<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Aturan_denah_model extends CI_Model {

    var $table = 'pu_aturan_denah';
    var $primary_key = "ID_PUD";

    public function __construct() {
        parent::__construct();
    }

    private function _get_table() {
        $this->db->from($this->table);
        $this->db->join('md_tahun_ajaran ta', $this->table . '.TA_PUD=ta.ID_TA');
        $this->db->join('md_catur_wulan cw', $this->table . '.CAWU_PUD=cw.ID_CAWU');
    }

    public function get_by_id($id) {
        $this->_get_table();
        $this->db->where($this->primary_key, $id);

        return $this->db->get()->row();
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
    
    public function get_aturan_um() {
        $this->db->from($this->table);
        $this->db->where(array(
            'TA_PUD' => $this->session->userdata('ID_PSB_ACTIVE'),
            'CAWU_PUD' => NULL,
        ));
        
        return $this->db->get()->row()->ATURAN_RUANG_PUD;
    }
    
    public function get_aturan_us() {
        $this->db->from($this->table);
        $this->db->where(array(
            'TA_PUD' => $this->session->userdata('ID_TA_ACTIVE'),
            'CAWU_PUD' => $this->session->userdata('ID_CAWU_ACTIVE'),
        ));
        
        return $this->db->get()->row()->ATURAN_RUANG_PUD;
    }
    
    public function get_id_um() {
        $this->db->from($this->table);
        $this->db->where(array(
            'TA_PUD' => $this->session->userdata('ID_PSB_ACTIVE'),
            'CAWU_PUD' => NULL,
        ));
        
        return $this->db->get()->row()->ID_PUD;
    }
    
    public function get_id_us() {
        $this->db->from($this->table);
        $this->db->where(array(
            'TA_PUD' => $this->session->userdata('ID_TA_ACTIVE'),
            'CAWU_PUD' => $this->session->userdata('ID_CAWU_ACTIVE'),
        ));
        
        return $this->db->get()->row()->ID_PUD;
    }

    public function is_um_dibuat() {
        $this->db->from($this->table);
        $this->db->where(array(
            'TA_PUD' => $this->session->userdata('ID_PSB_ACTIVE'),
            'CAWU_PUD' => NULL,
        ));

        if ($this->db->count_all_results() > 0)
            return TRUE;
        else
            return FALSE;
    }

    public function is_us_dibuat() {
        $this->db->from($this->table);
        $this->db->where(array(
            'TA_PUD' => $this->session->userdata('ID_TA_ACTIVE'),
            'CAWU_PUD' => $this->session->userdata('ID_CAWU_ACTIVE'),
        ));

        if ($this->db->count_all_results() > 0)
            return TRUE;
        else
            return FALSE;
    }

    public function is_um_validasi() {
        $this->db->from($this->table);
        $this->db->where(array(
            'TA_PUD' => $this->session->userdata('ID_PSB_ACTIVE'),
            'CAWU_PUD' => NULL,
            'VALIDASI_DENAH' => 1
        ));

        if ($this->db->count_all_results() > 0)
            return TRUE;
        else
            return FALSE;
    }

    public function is_us_validasi() {
        $this->db->from($this->table);
        $this->db->where(array(
            'TA_PUD' => $this->session->userdata('ID_TA_ACTIVE'),
            'CAWU_PUD' => $this->session->userdata('ID_CAWU_ACTIVE'),
            'VALIDASI_DENAH' => 1
        ));

        if ($this->db->count_all_results() > 0)
            return TRUE;
        else
            return FALSE;
    }
    
    public function get_denah_psb() {
        $this->db->from($this->table);
        $this->db->where(array(
            'TA_PUD' => $this->session->userdata('ID_PSB_ACTIVE'),
            'CAWU_PUD' => NULL,
        ));
        
        return $this->db->get()->row()->DATA_DENAH;
    }
    
    public function get_denah_cawu() {
        $this->db->from($this->table);
        $this->db->where(array(
            'TA_PUD' => $this->session->userdata('ID_TA_ACTIVE'),
            'CAWU_PUD' => $this->session->userdata('ID_CAWU_ACTIVE'),
        ));
        
        return $this->db->get()->row()->DATA_DENAH;
    }
    
    public function validasi_denah_psb() {
        $data = array(
            'VALIDASI_DENAH' => 1
        );
        $where = array(
            'TA_PUD' => $this->session->userdata('ID_PSB_ACTIVE'),
            'CAWU_PUD' => NULL,
            'READY_DENAH' => 1
        );
        
        $this->db->update($this->table, $data, $where);
        
        return $this->db->affected_rows();
    }
    
    public function validasi_denah_us() {
        $data = array(
            'VALIDASI_DENAH' => 1
        );
        $where = array(
            'TA_PUD' => $this->session->userdata('ID_TA_ACTIVE'),
            'CAWU_PUD' => $this->session->userdata('ID_CAWU_ACTIVE'),
            'READY_DENAH' => 1
        );
        
        $this->db->update($this->table, $data, $where);
        
        return $this->db->affected_rows();
    }
    
    public function ready_denah_psb() {
        $data = array(
            'READY_DENAH' => 1
        );
        $where = array(
            'TA_PUD' => $this->session->userdata('ID_PSB_ACTIVE'),
            'CAWU_PUD' => NULL,
        );
        
        $this->db->update($this->table, $data, $where);
        
        return $this->db->affected_rows();
    }
    
    public function ready_denah_us() {
        $data = array(
            'READY_DENAH' => 1
        );
        $where = array(
            'TA_PUD' => $this->session->userdata('ID_TA_ACTIVE'),
            'CAWU_PUD' => $this->session->userdata('ID_CAWU_ACTIVE'),
        );
        
        $this->db->update($this->table, $data, $where);
        
        return $this->db->affected_rows();
    }

}
