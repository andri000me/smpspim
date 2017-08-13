<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Siswa_editor_model extends CI_Model {

    var $table = 'md_siswa';
    var $column = array('NIS_SISWA','NAMA_SISWA', 'JK_SISWA', 'IF(NAMA_KELAS IS NULL, "-", NAMA_KELAS)','ID_SISWA');
    var $primary_key = "ID_SISWA";
    var $order = array("ID_SISWA" => 'ASC');

    public function __construct() {
        parent::__construct();
    }
    
    private function _get_table_simple($ALL = FALSE) {
        $this->db->select('*, IF(NAMA_KELAS IS NULL, "-", NAMA_KELAS) AS NAMA_KELAS_NOW');
        $this->db->from($this->table);
        $this->db->join('md_jenis_kelamin mjk', $this->table.'.JK_SISWA=mjk.ID_JK', 'LEFT');
        $this->db->join('md_kecamatan kec', $this->table.'.KECAMATAN_SISWA=kec.ID_KEC', 'LEFT');
        $this->db->join('md_kabupaten kab', 'kec.KABUPATEN_KEC=kab.ID_KAB', 'LEFT');
        $this->db->join('md_provinsi prov', 'kab.PROVINSI_KAB=prov.ID_PROV', 'LEFT');
        $this->db->join('akad_siswa asw', $this->table.'.ID_SISWA=asw.SISWA_AS AND asw.TA_AS="'.$this->session->userdata("ID_TA_ACTIVE").'" AND asw.KONVERSI_AS=0 AND asw.AKTIF_AS=1 ', 'LEFT');
        $this->db->join('akad_kelas ak', 'asw.KELAS_AS=ak.ID_KELAS', 'LEFT');
        if ($ALL)
            $this->db->where(array(
                'STATUS_MUTASI_SISWA' => NULL
            ));
        else 
            $this->db->where(array(
                'AKTIF_SISWA' => 1
            ));
    }

    private function _get_datatables_query() {
        $this->_get_table_simple();
        
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

    // MODEL UNTUK SISWA
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

    public function count_all($check = NULL) {
        $this->db->from($this->table);

        if ($check !== NULL) $this->db->where($check['name'], $check['value']);
        
        return $this->db->count_all_results();
    }

    public function get_by_id($id) {
        $this->_get_table_simple(TRUE);
        
        $this->db->where($this->primary_key, $id);

        return $this->db->get()->row();
    }
    
    public function get_wilayah() {
        $this->db->select('ID_KEC AS id, CONCAT(NAMA_KEC, " - ", NAMA_KAB, " - ", NAMA_PROV) AS text');
        $this->db->from('md_kecamatan kec');
        $this->db->join('md_kabupaten kab', 'kec.KABUPATEN_KEC=kab.ID_KAB');
        $this->db->join('md_provinsi prov', 'kab.PROVINSI_KAB=prov.ID_PROV');
        $this->db->join('md_negara n', 'prov.NEGARA_PROV=n.ID_NEGARA');
        $this->db->order_by('NAMA_PROV', 'ASC');
        $this->db->order_by('NAMA_KAB', 'ASC');
        $this->db->order_by('NAMA_KEC', 'ASC');
        $query = $this->db->get();

        return $query->result();
    }
    
    public function get_jk() {
        $this->db->from('md_jenis_kelamin');
        $query = $this->db->get();

        return $query->result();
    }
}
