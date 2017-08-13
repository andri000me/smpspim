<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Peserta_testing_model extends CI_Model {

    var $table = 'akad_siswa';
    var $column = array('NIS_SISWA', 'NAMA_SISWA','JK_SISWA','DEPT_TINGK','NAMA_TINGK','NAMA_KELAS','NAMA_PEG', 'ID_AS', 'ID_AS');
    var $primary_key = "ID_AS";
    var $order = array("ID_AS" => 'ASC');

    public function __construct() {
        parent::__construct();
    }

    private function _get_table($quran = true) {
        $this->db->from($this->table);
        $this->db->join('md_siswa ms',$this->table.'.SISWA_AS=ms.ID_SISWA');
        $this->db->join('md_tingkat mt',$this->table.'.TINGKAT_AS=mt.ID_TINGK');
        $this->db->join('md_jenjang_departemen mjd','mt.DEPT_TINGK=mjd.DEPT_MJD');
        $this->db->join('md_jenjang_sekolah mjs','mjd.JENJANG_MJD=mjs.ID_JS');
        $this->db->join('akad_kelas ak',$this->table.'.KELAS_AS=ak.ID_KELAS');
        $this->db->join('md_pegawai mp','ak.WALI_KELAS=mp.ID_PEG');
        $this->db->join('testing_nilai tn','tn.TA_TN='.$this->session->userdata('ID_TA_ACTIVE').' AND tn.SISWA_TN=ms.ID_SISWA AND tn.JENIS_TN="'.($quran ? 'QURAN' : 'KITAB').'"', 'LEFT');
        $this->db->where(array(
            'AKTIF_AS' => 1,
            'KONVERSI_AS' => 0,
            'NAIK_AS' => NULL,
            'TA_AS' => $this->session->userdata('ID_TA_ACTIVE')
        ));
        
        if ($quran) 
            $this->db->where(array(
                'TINGKAT_AS' => 16,
            ));
        else {
            $this->db->group_start();
            $this->db->where('TINGKAT_AS', 16);
            $this->db->or_where('TINGKAT_AS', 10);
            $this->db->or_where('TINGKAT_AS', 13);
            $this->db->group_end();
        }
    }

    private function _get_datatables_query($quran = true) {
        $this->_get_table($quran);
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

    function get_datatables($quran = true) {
        $this->_get_datatables_query($quran);
        if ($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();

        return $query->result();
    }

    function count_filtered($quran = true) {
        $this->_get_datatables_query($quran);
        $query = $this->db->get();

        return $query->num_rows();
    }

    public function count_all($quran = true) {
        $this->_get_table($quran);

        return $this->db->count_all_results();
    }

    public function count_jk($quran = true, $jk) {
        $this->_get_table($quran);
        $this->db->where('JK_SISWA', $jk);

        return $this->db->count_all_results();
    }
}
