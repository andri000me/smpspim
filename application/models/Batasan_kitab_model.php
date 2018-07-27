<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Batasan_kitab_model extends CI_Model {

    var $table = 'ph_batasan';
    var $column = array('NAMA_TA','NAMA_KITAB','KETERANGAN_TINGK','NAMA_JK','AWAL_BATASAN','AKHIR_BATASAN','NILAI_MAKS_BATASAN', 'ID_BATASAN');
    var $primary_key = "ID_BATASAN";
    var $order = array("ID_BATASAN" => 'ASC');

    public function __construct() {
        parent::__construct();
    }

    private function _get_table() {
        $this->db->from($this->table);
        $this->db->join('md_tahun_ajaran mta',$this->table.'.TA_BATASAN=mta.ID_TA');
        $this->db->join('ph_kitab pk',$this->table.'.KITAB_BATASAN=pk.ID_KITAB');
        $this->db->join('md_tingkat mt',$this->table.'.TINGKAT_BATASAN=mt.ID_TINGK');
        $this->db->join('md_jenis_kelamin mjk',$this->table.'.JK_BATASAN=mjk.ID_JK');
        $this->db->where('ID_TA', $this->session->userdata('ID_TA_ACTIVE'));
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

    public function get_rows_kelas($where) {
        $this->_get_table();
        $this->db->join('akad_kelas kls',$this->table.'.TINGKAT_BATASAN=kls.TINGKAT_KELAS AND JK_KELAS=JK_BATASAN');
        $this->db->where($where);
        $this->db->order_by('ID_KITAB', 'ASC');
//        $this->db->order_by('ID_KITAB');

        return $this->db->get()->result();
    }

    public function get_all($for_html = true) {
        if ($for_html) $this->db->select("ID_BATASAN as value, CONCAT('KITAB: ',NAMA_KITAB, '  BATASAN: ',AWAL_BATASAN,' - ',AKHIR_BATASAN) as label");
        $this->_get_table();

        return $this->db->get()->result();
    }

    public function get_all_ac($where) {
        $this->db->select("ID_BATASAN as id, CONCAT('KITAB: ',NAMA_KITAB, '  BATASAN: ',AWAL_BATASAN,' - ',AKHIR_BATASAN) as text");
        $this->_get_table();
        $this->db->like("CONCAT('KITAB: ',NAMA_KITAB, '  BATASAN: ',AWAL_BATASAN,' - ',AKHIR_BATASAN)", $where);

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
