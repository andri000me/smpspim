<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Nilai_siswa_model extends CI_Model {

    var $table = 'akad_guru_mapel';
    var $primary_key = "ID_AGM";
    var $order = array("ID_AGM" => 'ASC');

    public function __construct() {
        parent::__construct();
    }

    private function _get_table() {
        $this->db->select('*, (IF(NILAI_AN IS NULL, 0, NILAI_AN)) AS NILAI_SISWA');
        $this->db->from($this->table);
        $this->db->join('akad_siswa as', $this->table.'.KELAS_AGM=as.KELAS_AS AND as.TA_AS='.$this->session->userdata('ID_TA_ACTIVE').' AND as.KONVERSI_AS=0 AND as.AKTIF_AS=1');
        $this->db->join('akad_nilai an', $this->table.'.ID_AGM=an.GURU_MAPEL_AN AND an.SISWA_AN=as.ID_AS AND an.CAWU_AN='.$this->session->userdata('ID_CAWU_ACTIVE').' AND an.TA_AN='.$this->session->userdata('ID_TA_ACTIVE').' ', 'LEFT');
        $this->db->join('md_mapel mm',$this->table.'.MAPEL_AGM=mm.ID_MAPEL');
        $this->db->where(array(
            'RAPOR_MAPEL' => 1
        ));
        $this->db->order_by('TIPE_MAPEL', 'ASC');
        $this->db->order_by('KODE_MAPEL', 'ASC');
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

    public function get_all($for_html = true) {
        if ($for_html) $this->db->select("ID_AGM as value, NAMA_AGAMA as label");
        $this->_get_table();

        return $this->db->get()->result();
    }

    public function get_all_ac($where) {
        $this->db->select("ID_AGM as id, NAMA_AGAMA as text");
        $this->_get_table();
        $this->db->like('NAMA_AGAMA', $where);

        return $this->db->get()->result();
    }

    public function save($data) {
        $this->db->insert($this->table_crud, $data);

        return $this->db->insert_id();
    }

    public function update($where, $data) {
        $this->db->update($this->table_crud, $data, $where);
        
        return $this->db->affected_rows();
    }

}
