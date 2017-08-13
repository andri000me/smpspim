<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Tingkat_model extends CI_Model {

    var $table = 'md_tingkat';
    var $column = array('ID_TINGK', 'NAMA_TINGK', 'NAMA_DEPT', 'KETERANGAN_TINGK', 'ID_TINGK');
    var $primary_key = "ID_TINGK";
    var $order = array("ID_TINGK" => 'ASC');

    public function __construct() {
        parent::__construct();
    }

    private function _get_table() {
        $this->db->from($this->table);
        $this->db->join('md_departemen d',$this->table.'.DEPT_TINGK=d.ID_DEPT');
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

    public function get_id($dept, $tingkat) {
        $this->_get_table();
        $this->db->where(array(
            'DEPT_TINGK' => $dept,
            'NAMA_TINGK' => $tingkat,
        ));

        return $this->db->get()->row()->ID_TINGK;
    }

    public function get_tingkat_dept($dept = NULL) {
        $this->_get_table();
        
        if($dept == NULL) {
            $this->db->group_by('DEPT_TINGK');
            
            return $this->db->get()->result();
        } else {
            $this->db->where(array(
                'DEPT_TINGK' => $dept,
            ));

            return $this->db->get()->row();
        }
    }

    public function get_id_jenjang($dept, $tingkat) {
        $this->db->from($this->table);
        $this->db->join('md_jenjang_departemen mjd',$this->table.'.DEPT_TINGK=mjd.DEPT_MJD');
        $this->db->where(array(
            'JENJANG_MJD' => $dept,
            'NAMA_TINGK' => $tingkat,
        ));

        return $this->db->get()->row()->ID_TINGK;
    }

    public function get_all($for_html = true) {
        if ($for_html) $this->db->select("ID_TINGK as id, NAMA_TINGK as text");
        $this->_get_table();
        $this->db->order_by('ID_TINGK', 'ASC');

        return $this->db->get()->result();
    }
    
    public function get_all_urut() {
        $this->_get_table();
        $this->db->order_by('URUT_DEPT', 'ASC');
        $this->db->order_by('NAMA_TINGK', 'ASC');

        return $this->db->get()->result();
    }

    public function get_all_ac($where) {
        $this->db->select("ID_TINGK as id, KETERANGAN_TINGK as text");
        $this->_get_table();
        $this->db->like('KETERANGAN_TINGK', $where);

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

    public function get_all_except_id($id) {
        $this->_get_table();
        $this->db->where($this->primary_key.' <> ', $id);
        $this->db->order_by('URUT_DEPT', 'ASC');
        $this->db->order_by('NAMA_TINGK', 'ASC');

        return $this->db->get()->result();
    }

}
