<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Neraca_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_neraca() {
        $this->db->from('tuk_jenis_kelompok');
        $this->db->where('TA_TJK', $this->session->userdata('ID_TA_ACTIVE'));

        return $this->db->get()->result();
    }

    public function get_pembayaran() {
        return $this->get_bayar('PEMBAYARAN');
    }

    public function get_pengembalian() {
        return $this->get_bayar('PENGEMBALIAN');
    }
    
    private function get_bayar($status) {
        $this->db->select('SUM(NOMINAL_BAYAR) AS TOTAL, ID_BAYAR');
        $this->db->from('keu_pembayaran');
        $this->db->where('DIHITUNG_BAYAR', 0);
        $this->db->where('JENIS_BAYAR', $status);
        $this->db->order_by('ID_BAYAR', 'DESC');

        return $this->db->get()->row();
    }
    
    public function get_jurnal() {
        $this->db->select('SUM(NOMINAL_TJ) AS TOTAL, KELOMPOK_TJ, ID_TJ');
        $this->db->from('tuk_jurnal');
        $this->db->where('DIHITUNG_TJ', 0);
        $this->db->group_by('KELOMPOK_TJ');
        $this->db->order_by('ID_TJ', 'DESC');

        return $this->db->get()->result();
    }
    
    public function get_nominal_pembayaran() {
        $this->db->from('tuk_jenis_kelompok');
        $where = array(
            'TA_TJK' => $this->session->userdata('ID_TA_ACTIVE'),
            'JENIS_TJK' => 'PEMASUKAN',
            'NAMA_TJK' => 'TAGIHAN'
        );
        $this->db->where($where);

        return $this->db->get()->row()->NOMINAL_TJK;
    }
    
    public function get_nominal_neraca($ID_TJK) {
        $this->db->from('tuk_jenis_kelompok');
        $where = array(
            'ID_TJK' => $ID_TJK
        );
        $this->db->where($where);

        return $this->db->get()->row()->NOMINAL_TJK;
    }
    
    public function ubah_status_pembayaran($ID_BAYAR) {
        $data = array(
            'DIHITUNG_BAYAR' => 1
        );
        $where = array(
//            'ID_BAYAR <=' => $ID_BAYAR
            'DIHITUNG_BAYAR' => 0
        );
        $this->db->update('keu_pembayaran', $data, $where);
        
        return $this->db->affected_rows();
    }
    
    public function ubah_status_tagihan($NOMINAL_TJK) {
        $data = array(
            'NOMINAL_TJK' => $NOMINAL_TJK
        );
        $where = array(
            'TA_TJK' => $this->session->userdata('ID_TA_ACTIVE'),
            'JENIS_TJK' => 'PEMASUKAN',
            'NAMA_TJK' => 'TAGIHAN'
        );
        $this->db->update('tuk_jenis_kelompok', $data, $where);
        
        return $this->db->affected_rows();
    }
    
    public function ubah_status_neraca($ID_TJK, $NOMINAL_TJK) {
        $data = array(
            'NOMINAL_TJK' => $NOMINAL_TJK
        );
        $where = array(
            'ID_TJK' => $ID_TJK
        );
        $this->db->update('tuk_jenis_kelompok', $data, $where);
        
        return $this->db->affected_rows();
    }
    
    public function ubah_status_jurnal($ID_TJ) {
        $data = array(
            'DIHITUNG_TJ' => 1
        );
        $where = array(
//            'ID_TJ <=' => $ID_TJ
            'DIHITUNG_TJ' => 0
        );
        $this->db->update('tuk_jurnal', $data, $where);
        
        return $this->db->affected_rows();
    }

}
