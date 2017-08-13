<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Testing_kitab_model extends CI_Model {

    var $table = 'testing_waktu';
    var $jenis = 'KITAB';
    var $column = array('NAMA_TA','TANGGAL_TW','MULAI_TW','AKHIR_TW', 'ID_TW');
    var $primary_key = "ID_TW";
    var $order = array("CREATED_TW" => 'DESC');

    public function __construct() {
        parent::__construct();
    }

    private function _get_table() {
        $this->db->from($this->table);
        $this->db->join('md_tahun_ajaran mta',$this->table.'.TA_TW=mta.ID_TA');
        $this->db->where('JENIS_TW', $this->jenis);
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
    
    private function get_join($table) {
        if ($table == 'testing_waktu') {
            $this->db->join('md_tahun_ajaran ta', $table.'.TA_TW=ta.ID_TA');
            $this->db->join('md_jenis_kelamin jk', $table.'.JK_TW=jk.ID_JK');
        } elseif ($table == 'testing_ruang') {
            $this->db->join('md_tahun_ajaran ta', $table.'.TA_TR=ta.ID_TA');
            $this->db->join('md_ruang mr', $table.'.RUANG_TR=mr.KODE_RUANG', 'LEFT');
            $this->db->join('md_jenis_kelamin jk', $table.'.JK_TR=jk.ID_JK');
            $this->db->where('JENIS_TR', $this->jenis);
        } elseif ($table == 'testing_mapel') {
            $this->db->join('md_tahun_ajaran ta', $table.'.TA_TM=ta.ID_TA');
            $this->db->join('md_mapel mp', $table.'.MAPEL_TM=mp.ID_MAPEL', 'LEFT');
            $this->db->where('JENIS_TM', $this->jenis);
        } elseif ($table == 'testing_jadwal') {
            $this->db->join('md_tahun_ajaran ta', $table.'.TA_TP=ta.ID_TA');
            $this->db->join('md_pegawai peg', $table.'.PEGAWAI_TP=peg.ID_PEG', 'LEFT');
            $this->db->join('testing_mapel tm', $table.'.MAPEL_TP=tm.ID_TM AND tm.TA_TM='.$this->session->userdata('ID_TA_ACTIVE').' AND tm.JENIS_TM="'.$this->jenis.'"');
            $this->db->join('testing_ruang tr', $table.'.RUANG_TP=tr.ID_TR AND tr.TA_TR='.$this->session->userdata('ID_TA_ACTIVE').' AND tr.JENIS_TR="'.$this->jenis.'"');
            $this->db->join('testing_waktu tw', $table.'.WAKTU_TP=tw.ID_Tw AND tw.TA_TW='.$this->session->userdata('ID_TA_ACTIVE').' AND tw.JENIS_TW="'.$this->jenis.'"');
            $this->db->join('md_ruang mr', 'tr.RUANG_TR=mr.KODE_RUANG');
            $this->db->join('md_mapel mp', 'tm.MAPEL_TM=mp.ID_MAPEL');
            $this->db->join('md_jenis_kelamin jkw', 'tw.JK_TW=jkw.ID_JK');
            $this->db->join('md_jenis_kelamin jkr', 'tr.JK_TR=jkr.ID_JK');
            $this->db->where('JENIS_TP', $this->jenis);
        }
    }

    public function get_by_id($table, $id) {
        $this->db->from($table);
        $this->db->where($this->primary_key, $id);
        $this->get_join($table);

        return $this->db->get()->row();
    }

    public function get_row($table, $where) {
        $this->db->from($table);
        $this->db->where($where);
        $this->get_join($table);

        return $this->db->get()->row();
    }

    public function get_rows($table, $where) {
        $this->db->from($table);
        $this->db->where($where);
        $this->get_join($table);

        return $this->db->get()->result();
    }

    public function save($table, $data) {
        $this->db->insert($table, $data);

        return $this->db->insert_id();
    }

    public function update($table, $where, $data) {
        $this->db->update($table, $data, $where);
        
        return $this->db->affected_rows();
    }
    
    public function reset_jadwal_ta() {
        $this->delete_by_where('testing_jadwal', array('TA_TP' => $this->session->userdata('ID_TA_ACTIVE'), 'JENIS_TP' => $this->jenis));
        $this->delete_by_where('testing_mapel', array('TA_TM' => $this->session->userdata('ID_TA_ACTIVE'), 'JENIS_TM' => $this->jenis));
        $this->delete_by_where('testing_ruang', array('TA_TR' => $this->session->userdata('ID_TA_ACTIVE'), 'JENIS_TR' => $this->jenis));
        $this->delete_by_where('testing_waktu', array('TA_TW' => $this->session->userdata('ID_TA_ACTIVE'), 'JENIS_TW' => $this->jenis));
    }

    public function delete_by_where($table, $where) {
        $this->db->delete($table, $where);
        
        return $this->db->affected_rows();
    }

}
