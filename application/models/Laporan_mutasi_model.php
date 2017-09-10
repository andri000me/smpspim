<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Laporan_mutasi_model extends CI_Model {

    var $table = 'md_siswa';
    var $column = array('NIS_NIS', 'NAMA_SISWA','JK_SISWA','ALAMAT_SISWA','NAMA_KEC','NAMA_KAB', 'NAMA_KELAS','NAMA_MUTASI','NO_SURAT_MUTASI_SISWA','TANGGAL_MUTASI_SISWA', 'JUMLAH_POIN_KSH', 'JUMLAH_LARI_KSH', 'ID_SISWA', 'ID_SISWA');
    var $primary_key = "ID_SISWA";
    var $order = array("ID_SISWA" => 'DESC');

    public function __construct() {
        parent::__construct();
    }

    private function _get_table() {
        $this->db->from('md_siswa ms');
        $this->db->join('md_nis mn', 'mn.SISWA_NIS=ms.ID_SISWA');
        $this->db->join('akad_siswa as', 'as.SISWA_AS=mn.SISWA_NIS AND as.TA_AS=mn.TA_NIS', 'LEFT');
        $this->db->join('akad_kelas ak', 'as.KELAS_AS=ak.ID_KELAS', 'LEFT');
        $this->db->join('md_pegawai mp','ak.WALI_KELAS=mp.ID_PEG', 'LEFT');
        $this->db->join('md_status_mutasi msmt', 'ms.STATUS_MUTASI_SISWA=msmt.ID_MUTASI', 'LEFT');
        $this->db->join('md_kecamatan kec', 'ms.KECAMATAN_SISWA=kec.ID_KEC', 'LEFT');
        $this->db->join('md_kabupaten kab', 'kec.KABUPATEN_KEC=kab.ID_KAB', 'LEFT');
        $this->db->join('md_provinsi prov', 'kab.PROVINSI_KAB=prov.ID_PROV', 'LEFT');
        $this->db->join('(SELECT * FROM (SELECT *, SUM(POIN_KSH) AS JUMLAH_POIN_KSH, SUM(LARI_KSH) AS JUMLAH_LARI_KSH FROM komdis_siswa_header WHERE TA_KSH=' . $this->session->userdata('ID_TA_ACTIVE') . ' GROUP BY SISWA_KSH) ksh LEFT JOIN komdis_jenis_tindakan kjt ON ksh.JUMLAH_POIN_KSH>=kjt.POIN_KJT AND ksh.JUMLAH_POIN_KSH<=kjt.POIN_MAKS_KJT) ksh', 'ksh.SISWA_KSH=ms.ID_SISWA', 'LEFT');
        $this->db->where('NAMA_KELAS <> ', NULL);
        $this->db->where('TA_AS', $this->session->userdata('ID_TA_ACTIVE'));
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
                if (count($search_columns) == $i) {
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

    public function get_all() {
        $this->_get_table();

        return $this->db->get()->result();
    }

    public function count_all() {
        $this->db->from($this->table);

        return $this->db->count_all_results();
    }

    public function get_full_by_id($where, $order_by = NULL) {
        $this->_get_table();
        $this->db->join('md_pondok_siswa mps', 'ms.PONDOK_SISWA=mps.ID_MPS', 'LEFT');
        if($order_by != NULL) $this->db->order_by($order_by, 'ASC');

        $this->db->where($where);

        return $this->db->get()->result();
    }
}
