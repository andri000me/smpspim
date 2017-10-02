<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Matapelajaran_model extends CI_Model {

    var $table = 'md_mapel';
    var $column = array('URUTAN_MAPEL','ID_MAPEL','KODE_MAPEL', 'NAMA_MTM','NAMA_MAPEL','PMB_MAPEL', 'AKTIF_MAPEL','NAMA_ARAB_MAPEL','ID_MAPEL');
    var $primary_key = "ID_MAPEL";
    var $order = array("URUTAN_MAPEL" => 'ASC');

    public function __construct() {
        parent::__construct();
    }

    private function _get_table() {
        $this->db->from($this->table);
        $this->db->join('md_tipe_mapel mtm',$this->table.'.TIPE_MAPEL=mtm.ID_MTM');
        $this->db->join('md_departemen md',$this->table.'.DEPT_MAPEL=md.ID_DEPT');
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
                if ($i < 10)
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

    public function get_urutan($urutan, $naik) {
        $this->_get_table();
        $this->db->where('URUTAN_MAPEL '.($naik ? '>' : '<'), $urutan);
        $this->db->limit(1);
        $this->db->order_by('URUTAN_MAPEL', $naik ? 'ASC' : 'DESC');

        return $this->db->get()->row();
    }

    public function get_rows($where) {
        $this->_get_table();
        $this->db->where($where);

        return $this->db->get()->result();
    }

    public function get_all($for_html = true) {
        if ($for_html) $this->db->select("ID_MAPEL as value, NAMA_MAPEL as label");
        $this->_get_table();

        return $this->db->get()->result();
    }

    public function get_all_ac($where) {
        $this->db->select("ID_MAPEL as id, CONCAT(NAMA_DEPT,' - ',NAMA_MAPEL) as text");
        $this->_get_table();
        $this->db->like('NAMA_MAPEL', $where);
        $this->db->order_by('URUT_DEPT', 'ASC');
        $this->db->order_by('NAMA_MAPEL', 'ASC');

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
