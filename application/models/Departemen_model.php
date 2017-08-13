<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Departemen_model extends CI_Model {

    var $table = 'md_departemen';
    var $column = array('ID_DEPT', 'NAMA_DEPT', 'NAMA_PEG', 'TELP_DEPT','AKTIF_DEPT', 'ID_DEPT');
    var $primary_key = "ID_DEPT";
    var $order = array("ID_DEPT" => 'ASC');

    public function __construct() {
        parent::__construct();
    }

    private function _get_table() {
        $this->db->from($this->table);
        $this->db->join('md_pegawai p', $this->table.'.DIREKTUR_DEPT=p.ID_PEG');
        $this->db->join('md_kecamatan kec', $this->table.'.KECAMATAN_DEPT=kec.ID_KEC');
        $this->db->join('md_kabupaten kab', 'kec.KABUPATEN_KEC=kab.ID_KAB');
        $this->db->join('md_provinsi prov', 'kab.PROVINSI_KAB=prov.ID_PROV');
        $this->db->join('md_negara n', 'prov.NEGARA_PROV=n.ID_NEGARA');
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

    public function get_nama_by_jenjang($dept) {
        $this->db->from($this->table);
        $this->db->join('md_jenjang_departemen mjd',$this->table.'.ID_DEPT=mjd.DEPT_MJD');
        $this->db->where('JENJANG_MJD', $dept);
        
        return $this->db->get()->row()->NAMA_DEPT;
    }

    public function get_id_by_jenjang($dept) {
        $this->db->from($this->table);
        $this->db->join('md_jenjang_departemen mjd',$this->table.'.ID_DEPT=mjd.DEPT_MJD');
        $this->db->where('JENJANG_MJD', $dept);
        
        return $this->db->get()->row()->ID_DEPT;
    }

    public function get_all($for_html = true) {
        if ($for_html) $this->db->select("ID_DEPT as id, NAMA_DEPT as text");
        $this->_get_table();
        $this->db->order_by('URUT_DEPT', 'ASC');

        return $this->db->get()->result();
    }

    public function get_nominal($id) {
        $this->_get_table();
        $this->db->where($this->primary_key, $id);

        return $this->db->get()->row();
    }

    public function get_all_ac($where) {
        $this->db->select("ID_DEPT as id, NAMA_DEPT as text");
        $this->_get_table();
        $this->db->like('NAMA_DEPT', $where);

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
