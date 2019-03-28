<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Guru_mapel_model extends CI_Model {

    var $table = 'akad_guru_mapel';
    var $primary_key = "ID_AGM";

    public function __construct() {
        parent::__construct();
    }

    private function _get_table() {
        $this->db->from($this->table);
        $this->db->join('md_tahun_ajaran mta', $this->table . '.TA_AGM=mta.ID_TA');
        $this->db->join('akad_kelas ak', $this->table . '.KELAS_AGM=ak.ID_KELAS');
        $this->db->join('md_mapel mm', $this->table . '.MAPEL_AGM=mm.ID_MAPEL');
        $this->db->join('md_pegawai mp', $this->table . '.GURU_AGM=mp.ID_PEG');
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

    public function list_mapel_guru($where) {
        $this->_get_table();
        $this->db->where($where);
        $this->db->group_by('MAPEL_AGM');

        return $this->db->get()->result();
    }

    public function get_rows_array($where) {
        $this->_get_table();
        $this->db->where($where);

        return $this->db->get()->result_array();
    }

    public function get_all($for_html = true) {
        if ($for_html)
            $this->db->select("ID_AGM as value, NAMA_AGAMA as label");
        $this->_get_table();

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

    public function delete_by_where($where) {
        $this->db->delete($this->table, $where);

        return $this->db->affected_rows();
    }

    public function get_list_jadwal_group() {
        $this->_get_table();
        $this->db->group_by('TA_AGM');

        return $this->db->get()->result();
    }

    public function delete_before_import($ta, $jenjang) {
        $this->db->query("DELETE akad_guru_mapel FROM akad_guru_mapel
            INNER JOIN akad_kelas ON KELAS_AGM=ID_KELAS
            INNER JOIN md_tingkat ON TINGKAT_KELAS=ID_TINGK
            WHERE DEPT_TINGK='$jenjang' AND TA_AGM='$ta'");
    }

}
