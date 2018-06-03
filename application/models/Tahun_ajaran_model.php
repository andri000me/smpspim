<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Tahun_ajaran_model extends CI_Model {

    var $table = 'md_tahun_ajaran';
    var $column = array('ID_TA', 'NAMA_TA', 'TANGGAL_MULAI_TA', 'TANGGAL_AKHIR_TA', 'AKTIF_TA', 'KETERANGAN_TA', 'PSB_TA', 'ID_TA');
    var $primary_key = "ID_TA";
    var $order = array("ID_TA" => 'ASC');

    public function __construct() {
        parent::__construct();
    }

    private function _get_table() {
        $this->db->from($this->table);
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

    public function get_nama($id) {
        $this->_get_table();
        $this->db->where($this->primary_key, $id);

        return $this->db->get()->row()->NAMA_TA;
    }

    public function get_all($for_html = true) {
        if ($for_html)
            $this->db->select("ID_TA as id, NAMA_TA as text");
        $this->_get_table();
        $this->db->order_by('ID_TA', 'ASC');

        return $this->db->get()->result();
    }

    public function get_all_ac($where) {
        $this->db->select("ID_TA as id, NAMA_TA as text");
        $this->_get_table();
        $this->db->like('NAMA_TA', $where);

        return $this->db->get()->result();
    }

    public function get_all_ac_no_active($where) {
        $this->db->select("ID_TA as id, NAMA_TA as text");
        $this->_get_table();
        $this->db->where('ID_TA <> ', $this->session->userdata('ID_TA_ACTIVE'));
        $this->db->like('NAMA_TA', $where);

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

    public function set_ta_deactive() {
        $data['AKTIF_TA'] = 0;
        $this->db->update($this->table, $data);

        return $this->db->affected_rows();
    }

    public function set_ta_active($where) {
        $data['AKTIF_TA'] = 1;
        $this->db->update($this->table, $data, $where);

        return $this->db->affected_rows();
    }

    public function set_psb_deactive() {
        $data['PSB_TA'] = 0;
        $this->db->update($this->table, $data);

        return $this->db->affected_rows();
    }

    public function set_psb_active($where) {
        $data['PSB_TA'] = 1;
        $this->db->update($this->table, $data, $where);

        return $this->db->affected_rows();
    }

    public function get_ta_active() {
        $this->_get_table();
        $this->db->where('AKTIF_TA', 1);

        return $this->db->get()->row();
    }

    public function get_psb_active() {
        $this->_get_table();
        $this->db->where('PSB_TA', 1);

        return $this->db->get()->row();
    }

}
