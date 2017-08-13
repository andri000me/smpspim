<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Jurnal_model extends CI_Model {

    var $table = 'tuk_jurnal';
    var $column = array('NAMA_TJK','NOMINAL_TJ','KETERANGAN_TJ','NAMA_PEG','CREATED_TJ', 'ID_TJ');
    var $primary_key = "ID_TJ";
    var $order = array("ID_TJ" => 'ASC');

    public function __construct() {
        parent::__construct();
    }

    private function _get_table($JENIS_TJK = NULL) {
        $this->db->from($this->table);
        $this->db->join('tuk_jenis_kelompok tjk',$this->table.'.KELOMPOK_TJ=tjk.ID_TJK');
        $this->db->join('md_user mu',$this->table.'.USER_TJ=mu.ID_USER');
        $this->db->join('md_pegawai mp','mp.ID_PEG=mu.PEGAWAI_USER');
        
        if($JENIS_TJK != NULL) $this->db->where('JENIS_TJK', $JENIS_TJK);
    }

    private function _get_datatables_query($JENIS_TJK) {
        $this->_get_table($JENIS_TJK);
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

    function get_datatables($JENIS_TJK) {
        $this->_get_datatables_query($JENIS_TJK);
        if ($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();

        return $query->result();
    }

    function count_filtered($JENIS_TJK) {
        $this->_get_datatables_query($JENIS_TJK);
        $query = $this->db->get();

        return $query->num_rows();
    }

    public function get_by_id($id) {
        $this->_get_table();
        $this->db->where($this->primary_key, $id);

        return $this->db->get()->row();
    }

    public function get_all($for_html = true) {
        if ($for_html) $this->db->select("ID_TJ as value, NAMA_AGAMA as label");
        $this->_get_table();

        return $this->db->get()->result();
    }

    public function get_all_ac($where) {
        $this->db->select("ID_TJ as id, NAMA_AGAMA as text");
        $this->_get_table();
        $this->db->like('NAMA_AGAMA', $where);

        return $this->db->get()->result();
    }

    public function count_all($JENIS_TJK) {
        $this->_get_table($JENIS_TJK);

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
