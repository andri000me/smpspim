<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Pelanggaran_perpelanggaran_model extends CI_Model {

    var $table = 'akad_siswa';
    var $column = array('NO_ABSEN_AS', 'NIS_SISWA', 'NAMA_SISWA', 'AYAH_NAMA_SISWA', 'NAMA_KELAS', 'NAMA_PEG', 'ID_AS');
    var $primary_key = "ID_AS";
    var $order = array("ID_AS" => 'DESC');

    public function __construct() {
        parent::__construct();
    }

    private function _get_table($TANGGAL_PELANGGARAN, $PELANGGARAN_KS) {
        $this->db->from($this->table);
        $this->db->join('md_siswa ms', $this->table . '.SISWA_AS=ms.ID_SISWA');
        $this->db->join('(SELECT * FROM komdis_siswa WHERE PELANGGARAN_KS=' . $PELANGGARAN_KS . ' AND TANGGAL_KS="' . $TANGGAL_PELANGGARAN . '") ks', 'ks.SISWA_KS=ms.ID_SISWA AND ks.TA_KS=TA_AS', 'LEFT');
        $this->db->join('md_tingkat mt', $this->table . '.TINGKAT_AS=mt.ID_TINGK');
        $this->db->join('akad_kelas ak', $this->table . '.KELAS_AS=ak.ID_KELAS');
        $this->db->join('md_pegawai mp', 'ak.WALI_KELAS=mp.ID_PEG');
        $this->db->where('TA_AS', $this->session->userdata('ID_TA_ACTIVE'));
        $this->db->where('KONVERSI_AS', 0);
        $this->db->where('AKTIF_AS', 1);
    }

    private function _get_datatables_query($TANGGAL_PELANGGARAN, $PELANGGARAN_KS) {
        $this->_get_table($TANGGAL_PELANGGARAN, $PELANGGARAN_KS);
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
            $order[7] = 'DOMISILI_SISWA';
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    function get_datatables($TANGGAL_PELANGGARAN, $PELANGGARAN_KS) {
        $this->_get_datatables_query($TANGGAL_PELANGGARAN, $PELANGGARAN_KS);
        if ($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();

        return $query->result();
    }

    function count_filtered($TANGGAL_PELANGGARAN, $PELANGGARAN_KS) {
        $this->_get_datatables_query($TANGGAL_PELANGGARAN, $PELANGGARAN_KS);
        $query = $this->db->get();

        return $query->num_rows();
    }

    public function count_all($TANGGAL_PELANGGARAN, $PELANGGARAN_KS) {
        $this->db->from($this->table);

        return $this->db->count_all_results();
    }

    public function save($data) {
        $this->db->insert($this->table, $data);

        return $this->db->insert_id();
    }

    public function delete_by_id($id) {
        $where = array($this->primary_key => $id);
        $this->db->delete($this->table, $where);

        return $this->db->affected_rows();
    }

}
