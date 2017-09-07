<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Hakakses_model
 *
 * @author Rohmad Eko Wahyudi
 */
class Hakakses_model extends CI_Model{
    //put your code here
    
    var $table = 'md_menu';
    var $column = array('NAME_MENU'); 
    var $order = array('NAME_MENU' => 'ASC');
 
    public function __construct()
    {
        parent::__construct();
    }

    private function _get_table() 
    {
        $this->db->from($this->table);
//        $this->db->join('levelmenu', $this->table.'.ID_MENU = levelmenu.MENU_LEVELMENU');
//        $this->db->join('hakakses', 'levelmenu.HAKAKSES_LEVELMENU = hakakses.ID_HAKAKSES');
    }
 
    private function _get_datatables_query() {
         
        $this->_get_table();
        $i = 0;
        $search_value = $_POST['search']['value'];
        $search_columns = $_POST['columns'];
        foreach ($this->column as $item) {
            if($search_value || $search_columns) {
                if($i === 0) {
                    $this->db->group_start();
                    $this->db->like($item, $search_value);
                } else {
                    $this->db->or_like($item, $search_value);
                }
                if(count($this->column) - 1 == $i) 
                    $this->db->group_end(); 
            }
            $column[$i] = $item;
            $i++;
        }
        $i = 0;
        foreach ($this->column as $item) {
            if($search_columns) {
                if($i === 0) 
                    $this->db->group_start();
                $this->db->like($item, $search_columns[$i]['search']['value']);
                if(count($this->column) - 1 == $i) 
                    $this->db->group_end(); 
            }
            $column[$i] = $item;
            $i++;
        }
         
        if(isset($_POST['order'])) {
            $this->db->order_by($column[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if(isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }
 
    function get_datatables()
    {
        $this->_get_datatables_query();
        if($_POST['length'] != -1)
        $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }
 
    function count_filtered()
    {
        $this->_get_datatables_query();
        $query = $this->db->get();
        return $query->num_rows();
    }
 
    public function count_all()
    {
        $this->db->from($this->table);
        return $this->db->count_all_results();
    }
    
    public function get_hakakses() {
        $this->db->from('md_hakakses');
        return $this->db->get()->result();
    }
    
    public function get_all() {
        $this->db->select('ID_HAKAKSES as value, NAME_HAKAKSES as label');
        $this->db->from('md_hakakses');
        $this->db->where('ID_HAKAKSES <>', 1);
        
        return $this->db->get()->result_array();
    }
    
    public function get_levelmenu() {
        $this->db->from('levelmenu');
        return $this->db->get()->result();
    }
    
    public function change_role($status, $data) {
        if($status)
            $this->db->insert('levelmenu', $data);
        else
            $this->db->delete('levelmenu', $data);
    }
}
