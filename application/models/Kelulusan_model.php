<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Kelulusan_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }
    
    public function get_testing($siswa, $jenis, $ta = NULL) {
        $this->db->from('testing_nilai');
        $this->db->where(array(
            'TA_TN' => ($ta == NULL) ? $this->session->userdata('ID_TA_ACTIVE') : $ta,
            'SISWA_TN' => $siswa,
            'JENIS_TN' => $jenis,
        ));
        
        $result = $this->db->get()->row();
        
        if($result == NULL)
            return 0;
        else
            return $result->NILAI_TN;
    }
    
    public function get_kta($siswa) {
        $this->db->from('lpba_nilai');
        $this->db->where(array(
            'TA_LN' => $this->session->userdata('ID_TA_ACTIVE'),
            'CAWU_LN' => 3,
            'SISWA_LN' => $siswa,
            'JENIS_LN' => 'KTA',
        ));
        
        $result = $this->db->get()->row();
        
        if ($result == NULL)
            return '-';
        else
            return $result->NILAI_LN;
    }
    
    public function simpan_nilai_kta($siswa, $nilai) {
        $data = array(
            'TA_LN' => $this->session->userdata('ID_TA_ACTIVE'),
            'CAWU_LN' => 3,
            'SISWA_LN' => $siswa,
            'JENIS_LN' => 'KTA',
            'NILAI_LN' => $nilai,
            'USER_LN' => $this->session->userdata('ID_USER'),
        );
        $where = array(
            'TA_LN' => $this->session->userdata('ID_TA_ACTIVE'),
            'CAWU_LN' => 3,
            'SISWA_LN' => $siswa,
            'JENIS_LN' => 'KTA',
        );
        $this->db->delete('lpba_nilai', $where);
        $this->db->insert('lpba_nilai', $data);

        return $this->db->insert_id();
    }

}
