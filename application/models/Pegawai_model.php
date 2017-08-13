<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Pegawai_model extends CI_Model {

    var $table = 'md_pegawai';
    var $column = array('NIP_PEG', 'NAMA_PEG', 'JK_PEG','ALAMAT_PEG','NAMA_KEC','NAMA_KAB','AKTIF_PEG', 'ID_PEG');
    var $primary_key = "ID_PEG";
    var $order = array("ID_PEG" => 'ASC');

    public function __construct() {
        parent::__construct();
    }

    private function _get_table() {
        $this->db->from($this->table);
        $this->db->join('md_jenis_kelamin mjk', $this->table.'.JK_PEG=mjk.ID_JK', 'LEFT');
        $this->db->join('md_suku msk', $this->table.'.SUKU_PEG=msk.ID_SUKU', 'LEFT');
        $this->db->join('md_agama mag', $this->table.'.AGAMA_PEG=mag.ID_AGAMA', 'LEFT');
        $this->db->join('md_kecamatan kec', $this->table.'.KECAMATAN_PEG=kec.ID_KEC');
        $this->db->join('md_kabupaten kab', 'kec.KABUPATEN_KEC=kab.ID_KAB');
        $this->db->join('md_provinsi prov', 'kab.PROVINSI_KAB=prov.ID_PROV');
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
    
    public function get_name($id) {
        $this->_get_table();
        $this->db->where($this->primary_key, $id);
        $result = $this->db->get()->row();

        if($result == NULL) 
            return NULL;
        else
            return $result->NAMA_PEG;
    }

    public function get_all($for_html = true) {
        if ($for_html) $this->db->select("ID_PEG as value, NAMA_PEG as label");
        $this->_get_table();

        return $this->db->get()->result();
    }

    public function get_all_ac($where = NULL) {
        $this->db->select("ID_PEG as id, NAMA_PEG as text");
        $this->_get_table();
        if($where != NULL) $this->db->like('NAMA_PEG', $where);
        $this->db->where('AKTIF_PEG', 1);
        $this->db->order_by('NAMA_PEG', 'ASC');

        return $this->db->get()->result();
    }

    public function get_all_ac_guru($where) {
        $this->db->select("ID_PEG as id, NAMA_PEG as text");
        $this->_get_table();
        $this->db->like('NAMA_PEG', $where);
        $this->db->where('AKTIF_PEG', 1);
        $this->db->where('GURU_PEG', 1);

        return $this->db->get()->result();
    }

    public function count_all($check = NULL) {
        $this->db->from($this->table);

        if ($check !== NULL) $this->db->where($check['name'], $check['value']);
        
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
