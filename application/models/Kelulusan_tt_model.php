<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Kelulusan_tt_model extends CI_Model {

    var $table = 'akad_siswa';
    var $column = array('NAMA_TA','NIS_SISWA', 'NAMA_SISWA','JK_SISWA','DEPT_TINGK','NAMA_TINGK','NAMA_KELAS', 'NAMA_PEG', 'LULUS_AS', 'ID_AS', 'ID_AS', 'ID_AS', 'ID_AS', 'ID_AS', 'ID_AS');
    var $primary_key = "ID_AS";
    var $order = array("ID_AS" => 'ASC');

    public function __construct() {
        parent::__construct();
    }

    private function _get_table() {
        $this->db->from($this->table);
        $this->db->join('md_siswa ms',$this->table.'.SISWA_AS=ms.ID_SISWA');
        $this->db->join('md_tahun_ajaran mta',$this->table.'.TA_AS=mta.ID_TA');
        $this->db->join('md_tingkat mt',$this->table.'.TINGKAT_AS=mt.ID_TINGK');
        $this->db->join('md_jenjang_departemen mjd','mt.DEPT_TINGK=mjd.DEPT_MJD');
        $this->db->join('md_jenjang_sekolah mjs','mjd.JENJANG_MJD=mjs.ID_JS');
        $this->db->join('akad_kelas ak',$this->table.'.KELAS_AS=ak.ID_KELAS');
        $this->db->join('md_pegawai mp','ak.WALI_KELAS=mp.ID_PEG');
        $this->db->where(array(
            'AKTIF_AS' => 1,
            'KONVERSI_AS' => 0,
            'NAIK_AS' => NULL,
        ));
        
        $this->db->group_start();
        $this->db->where('LULUS_AS', 'TK');
        $this->db->or_where('LULUS_AS', 'TQ');
        $this->db->or_where('LULUS_AS', 'TTK');
        $this->db->or_where('LULUS_AS', 'TTQ');
        $this->db->or_where('LULUS_AS', 'TQTK');
        $this->db->or_where('LULUS_AS', 'TTQTK');
        $this->db->or_where('LULUS_AS', 'TQTTK');
        $this->db->or_where('LULUS_AS', 'TTQTTK');
        $this->db->group_end();
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
                if((($search_columns[$i]['search']['value'] != "") || ($i < 6)) && ($i != 1)) 
                    $this->db->like($item, $search_columns[$i]['search']['value']);
                if (count($search_columns) - 1 == $i) {
                    $this->db->group_end();
                    break;
                }
            }
            $column[$i] = $item;
            $i++;
        }

        if (isset($_POST['order']) && isset($column)) {
            $this->db->order_by($this->column[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
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
        $this->_get_table();

        return $this->db->count_all_results();
    }
}
