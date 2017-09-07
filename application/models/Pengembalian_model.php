<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Pengembalian_model extends CI_Model {

    var $table = 'keu_pembayaran';
    var $column = array('ID_BAYAR', 'KODE_BAYAR','ID_BAYAR');
    var $primary_key = "ID_BAYAR";
    var $order = array("ID_BAYAR" => 'ASC');

    public function __construct() {
        parent::__construct();
    }

    private function _get_table($select = TRUE) {
        if ($select) $this->db->select('*, IF(NIS_SISWA IS NULL, "-", NIS_SISWA) AS NIS_SISWA');
        $this->db->from($this->table);
        $this->db->join('keu_setup ds',$this->table.'.SETUP_BAYAR=ds.ID_SETUP');
        $this->db->join('keu_detail dt','ds.DETAIL_SETUP=dt.ID_DT');
        $this->db->join('keu_tagihan t','dt.TAGIHAN_DT=t.ID_TAG');
        $this->db->join('md_tahun_ajaran ta','t.TA_TAG=ta.ID_TA');
        $this->db->join('md_siswa ms','ds.SISWA_SETUP=ms.ID_SISWA');
        $this->db->join('md_user mu',$this->table.'.USER_BAYAR=mu.ID_USER');
        $this->db->join('md_pegawai mp','mu.PEGAWAI_USER=mp.ID_PEG');
        $this->db->where(array(
            'ID_TA' => $this->session->userdata('ID_TA_ACTIVE'),
            'USER_BAYAR' => $this->session->userdata('ID_USER')
        ));
    }
    
    public function get_siswa($where) {
        $this->db->select("ID_SISWA as id, CONCAT((IF(NIS_SISWA IS NULL, 'BELUM PUNYA NIS', NIS_SISWA)), ' - ', NAMA_SISWA) as text");
        $this->_get_table(FALSE);
        $this->db->like('CONCAT((IF(NIS_SISWA IS NULL, "BELUM PUNYA NIS", NIS_SISWA))," ",NAMA_SISWA)', $where);
        $this->db->where(array(
            'STATUS_SETUP' => 1,
            'JENIS_BAYAR' => 'PEMBAYARAN',  
        ));
        $this->db->group_by('ID_SISWA');
        
        return $this->db->get()->result();
    }
    
    public function get_tagihan_siswa($ID_SISWA) {
        $this->_get_table(FALSE);
        $this->db->where(array(
            'STATUS_SETUP' => 1,
            'JENIS_BAYAR' => 'PEMBAYARAN',
            'ID_SISWA' => $ID_SISWA
        ));
        
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

    public function get_by_id($id) {
        $this->_get_table();
        $this->db->where(array(
            $this->primary_key => $id
        ));

        return $this->db->get()->row();
    }

    public function check_ketersediaan($setup) {
        if($this->_check_ketersediaan('PENGEMBALIAN', $setup) < $this->_check_ketersediaan('PEMBAYARAN', $setup)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    private function _check_ketersediaan($jenis, $setup) {
        $this->db->from('keu_pembayaran');
        $this->db->where(array(
            'JENIS_BAYAR' => $jenis,
            'SETUP_BAYAR' => $setup
        ));

        $query = $this->db->get();

        return $query->num_rows();
    }

}
