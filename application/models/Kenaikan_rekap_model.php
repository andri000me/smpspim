<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Kenaikan_rekap_model extends CI_Model {

    var $table = 'akad_siswa';
    var $column = array('NO_ABSEN_AS','NIS_SISWA', 'NAMA_SISWA','JK_SISWA','DEPT_TINGK','NAMA_TINGK', 'NAIK_AS','ID_AS');
    var $primary_key = "ID_AS";
    var $order = array("NO_ABSEN_AS" => 'ASC');

    public function __construct() {
        parent::__construct();
    }

    private function _get_table($kelas) {
        $this->db->from($this->table);
        $this->db->join('md_siswa ms',$this->table.'.SISWA_AS=ms.ID_SISWA');
        $this->db->join('md_tingkat mt',$this->table.'.TINGKAT_AS=mt.ID_TINGK');
        $this->db->where(array(
            'KONVERSI_AS' => 0,
            'NAIK_AS <> ' => NULL,
            'KELAS_AS' => $kelas,
            'TA_AS' => $this->session->userdata('ID_TA_ACTIVE')
        ));
    }

    private function _get_datatables_query($kelas) {
        $this->_get_table($kelas);
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

    function get_datatables($kelas) {
        $this->_get_datatables_query($kelas);
        if ($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
//        var_dump($this->db->last_query());

        return $query->result();
    }

    function count_filtered($kelas) {
        $this->_get_datatables_query($kelas);
        $query = $this->db->get();

        return $query->num_rows();
    }

    public function count_all($kelas) {
        $this->_get_table($kelas);

        return $this->db->count_all_results();
    }

}
