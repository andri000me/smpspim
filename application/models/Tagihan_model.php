<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Tagihan_model extends CI_Model {

    var $table = 'keu_tagihan';
    var $column = array('ID_TAG', 'NAMA_TA','NAMA_TAG', 'PSB_TAG','ID_TAG');
    var $primary_key = "ID_TAG";
    var $order = array("ID_TAG" => 'ASC');

    public function __construct() {
        parent::__construct();
    }

    private function _get_table() {
        $this->db->from($this->table);
        $this->db->join('md_tahun_ajaran ta',$this->table.'.TA_TAG=ta.ID_TA');
    }

    private function _get_datatables_query() {
        $this->_get_table();
        $i = 0;
        $search_value = $_POST['search']['value'];
        $search_columns = $_POST['columns'];
        foreach ($this->column as $item) {
            if ($search_value || $search_columns) {
                if ($i === 0) {
                    $this->db->group_start();
                    $this->db->like($item, $search_value);
                } else {
                    $this->db->or_like($item, $search_value);
                }
                if (count($search_columns) - 1 == $i) {
                    $this->db->group_end();
                    break;
                }
            }
            $column[$i] = $item;
            $i++;
        }
        $i = 0;
        foreach ($this->column as $item) {
            if ($search_columns) {
                if ($i === 0)
                    $this->db->group_start();
                $this->db->like($item, $search_columns[$i]['search']['value']);
                if (count($search_columns) - 1 == $i) {
                    $this->db->group_end();
                    break;
                }
            }
            $column[$i] = $item;
            $i++;
        }

        if (isset($_POST['order'])) {
            $this->db->order_by($column[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    function get_datatables() {
        $this->_get_datatables_query();
        if ($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();

        return $query->result();
    }

    function count_filtered() {
        $this->_get_datatables_query();
        $query = $this->db->get();

        return $query->num_rows();
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
        if ($for_html) $this->db->select("ID_TAG as value,  CONCAT(NAMA_TA, ' - ', NAMA_TAG) as label");
        $this->_get_table();
        $this->db->order_by('ID_TAG', 'DESC');

        return $this->db->get()->result();
    }

    public function get_all_ta_active($for_html = true) {
        if ($for_html) $this->db->select("ID_TAG as value,  NAMA_TAG as label");
        $this->_get_table();
        $this->db->where('ID_TA', $this->session->userdata('ID_TA_ACTIVE'));
        $this->db->order_by('ID_TAG', 'DESC');

        return $this->db->get()->result();
    }

    public function get_all_ta_active_dropdown($for_html = true) {
        if ($for_html) $this->db->select("ID_TAG as id,  NAMA_TAG as text");
        $this->_get_table();
        $this->db->where('ID_TA', $this->session->userdata('ID_TA_ACTIVE'));
        $this->db->order_by('ID_TAG', 'DESC');

        return $this->db->get()->result();
    }

    public function get_all_ac($where) {
        $this->db->select("ID_TAG as id,  CONCAT(NAMA_TA, ' - ', NAMA_TAG) as text");
        $this->_get_table();
        $this->db->like('NAMA_TAG', $where);

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

    public function delete_user($id) {
        $where = array('USER_MUK' => $id);
        $this->db->delete('md_user_keuangan', $where);
        
        return $this->db->affected_rows();
    }

    public function add_user($data) {
        $this->db->insert('md_user_keuangan', $data);
        
        return $this->db->insert_id();
    }
    
    public function get_all_psb_active() {
        $this->_get_table();
        $this->db->where('ID_TA', $this->session->userdata('ID_PSB_ACTIVE'));

        return $this->db->get()->result();
    }

    public function delete_by_id($id) {
        $where = array($this->primary_key => $id);
        $this->db->delete($this->table, $where);
        
        return $this->db->affected_rows();
    }

}
