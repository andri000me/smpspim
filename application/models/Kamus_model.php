<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Kamus_model extends CI_Model {

    var $table = 'gen_kamus';
    var $column = array('LATIN_GK','ARAB_GK', 'ID_GK');
    var $primary_key = "ID_GK";
    var $order = array("LATIN_GK" => 'ASC');
    
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

    public function count_all() {
        $this->db->from($this->table);

        return $this->db->count_all_results();
    }

    public function get_by_id($id) {
        $this->_get_table();
        $this->db->where($this->primary_key, $id);

        return $this->db->get()->row();
    }

    public function save($data) {
        $this->db->insert($this->table, $data);

        return $this->db->insert_id();
    }

    public function update($where, $data) {
        $this->db->update($this->table, $data, $where);
        
        return $this->db->affected_rows();
    }

    public function get_text($latin) {
        $this->db->from($this->table);
        $this->db->where('UPPER(LATIN_GK)', strtoupper($latin));

        $result = $this->db->get()->row();
        
        if($result == NULL)
            return NULL;
        else
            return $result->ARAB_GK;
//            return $result->ARAB_GK.($result->SAMBUNG_GK ? '' : ' ');
    }

    public function cek_kata($kata) {
        $this->db->from($this->table);
        $this->db->where('UPPER(LATIN_GK)', strtoupper($kata));

        $result = $this->db->get()->result();
        
        if(count($result) > 0)
            return TRUE;
        else 
            return FALSE;
    }

}
