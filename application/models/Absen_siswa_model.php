<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Absen_siswa_model extends CI_Model {

    var $table = 'akad_siswa';
    var $column = array('NO_ABSEN_AS', 'IF(NIS_SISWA IS NULL, "KELUAR", NIS_SISWA)', 'NAMA_SISWA', 'ID_AS', 'ID_AS', 'ID_AS', 'ID_AS', 'ID_AS', 'ID_AS', 'ID_AS', 'ID_AS', 'ID_AS', 'ID_AS', 'ID_AS', 'ID_AS', 'ID_AS', 'ID_AS', 'ID_AS', 'ID_AS', 'ID_AS', 'ID_AS', 'ID_AS', 'ID_AS', 'ID_AS', 'ID_AS', 'ID_AS', 'ID_AS', 'ID_AS', 'ID_AS', 'ID_AS', 'ID_AS', 'ID_AS', 'ID_AS', 'ID_AS', 'ID_AS', 'ID_AS', 'ID_AS', 'ID_AS', 'ID_AS', 'ID_AS', 'ID_AS', 'ID_AS', 'ID_AS', 'ID_AS', 'ID_AS', 'ID_AS');
    var $primary_key = "ID_AS";
    var $order = array("ID_AS" => 'ASC');

    public function __construct() {
        parent::__construct();
    }

    private function _get_table($ID_KELAS, $JENIS_AKH = NULL, $TANGGAL_AKH = NULL) {
        $this->db->select('*, IF(NIS_SISWA IS NULL, "KELUAR", NIS_SISWA) AS NIS_SISWA_SHOW');
        $this->db->from($this->table);
        $this->db->join('md_siswa ms', $this->table . '.SISWA_AS=ms.ID_SISWA');
        $this->db->join('md_tingkat mt', $this->table . '.TINGKAT_AS=mt.ID_TINGK');
        if (($JENIS_AKH != NULL) && ($TANGGAL_AKH != NULL))
            $this->db->join('akad_kehadiran akh', $this->table . '.SISWA_AS=akh.SISWA_AKH AND akh.TA_AKH=' . $this->session->userdata('ID_TA_ACTIVE') . ' (akh.JENIS_AKH=' . $JENIS_AKH . ' OR akh.JENIS_AKH=1) AND akh.TANGGAL_AKH="' . $this->date_format->to_store_db($TANGGAL_AKH) . '"', 'LEFT'); //  AND akh.CAWU_AKH=' . $this->session->userdata('ID_CAWU_ACTIVE') . ' AND
        $this->db->where(array(
            'TA_AS' => $this->session->userdata('ID_TA_ACTIVE'),
            'KONVERSI_AS' => 0,
            'KELAS_AS' => $ID_KELAS,
        ));
    }

    private function _get_datatables_query($ID_KELAS, $JENIS_AKH = NULL, $TANGGAL_AKH = NULL) {
        $this->_get_table($ID_KELAS, $JENIS_AKH, $TANGGAL_AKH);
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
                if ((($search_columns[$i]['search']['value'] != "") || ($i < 6)) && ($i != 1))
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
            $order[1] = 'NIS_SISWA_SHOW';
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    function get_datatables($ID_KELAS, $JENIS_AKH = NULL, $TANGGAL_AKH = NULL) {
        $this->_get_datatables_query($ID_KELAS, $JENIS_AKH, $TANGGAL_AKH);
        if ($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
//        var_dump($this->db->last_query());
        return $query->result();
    }

    function count_filtered($ID_KELAS, $JENIS_AKH = NULL, $TANGGAL_AKH = NULL) {
        $this->_get_datatables_query($ID_KELAS, $JENIS_AKH, $TANGGAL_AKH);
        $query = $this->db->get();

        return $query->num_rows();
    }

    public function count_all($ID_KELAS, $JENIS_AKH = NULL, $TANGGAL_AKH = NULL) {
        $this->_get_datatables_query($ID_KELAS, $JENIS_AKH, $TANGGAL_AKH);

        return $this->db->count_all_results();
    }
    
    public function get_kehadiran($SISWA_AKH, $JENIS_AKH, $BULAN, $TAHUN) {
        $this->db->from('akad_kehadiran');
        $this->db->where(array(
            'TA_AKH' => $this->session->userdata('ID_TA_ACTIVE'),
            'SISWA_AKH' => $SISWA_AKH,
            'LEFT(TANGGAL_AKH, 7)=' => $TAHUN.'-'.$BULAN,
            'JENIS_AKH' => $JENIS_AKH
        ));
        $this->db->order_by('TANGGAL_AKH', 'ASC');
        $query = $this->db->get();

        return $query->result();
    }

}
