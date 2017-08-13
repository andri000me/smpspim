<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Kalender_akademik_model extends CI_Model {

    var $table = 'akad_kalender';
    var $primary_key = "ID_AK";
    var $order = array("ID_AK" => 'ASC');

    public function __construct() {
        parent::__construct();
    }

    private function _get_table() {
        $this->db->select('ID_AK as id, TGL_MULAI_AK as start, TGL_SELESAI_AK as end, CONCAT(NAMA_TA, " - ", NAMA_AK) as title, BACKGROUND_AK as backgroundColor, BORDER_AK as borderColor');
        $this->db->from($this->table);
        $this->db->join('md_user mu',$this->table.'.USER_AK=mu.ID_USER');
        $this->db->join('md_tahun_ajaran mta',$this->table.'.TA_AK=mta.ID_TA');
    }

    public function get_by_id($id) {
        $this->_get_table();
        $this->db->where($this->primary_key, $id);

        return $this->db->get()->row();
    }

    public function get_rows($where) {
        $this->_get_table();
        $this->db->where($where);

        return $this->db->get()->result();
    }

    public function get_all($for_html = true) {
        if ($for_html) $this->db->select("ID_AK as value, NAMA_AK as label");
        $this->_get_table();

        return $this->db->get()->result();
    }

    public function get_all_ac($where) {
        $this->db->select("ID_AK as id, NAMA_AK as text");
        $this->_get_table();
        $this->db->like('NAMA_AK', $where);

        return $this->db->get()->result();
    }

    public function count_all() {
        $this->db->from($this->table);

        return $this->db->count_all_results();
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

}
