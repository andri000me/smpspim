<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Laporan_keuangan_model extends CI_Model {

    var $table = 'keu_pembayaran';
    var $column = array('NAMA_TA', 'NAMA_TAG','NAMA_DT', 'DEPT_DT','IF(NIS_SISWA IS NULL, "-", NIS_SISWA)','NAMA_SISWA','NOMINAL_BAYAR','KETERANGAN_BAYAR', 'NAMA_PEG','CREATED_BAYAR','ID_BAYAR');
    var $primary_key = "ID_BAYAR";
    var $order = array("ID_BAYAR" => 'ASC');

    public function __construct() {
        parent::__construct();
    }

    private function _get_table($select = TRUE) {
        if ($select) $this->db->select('*, IF(NIS_SISWA IS NULL, "-", NIS_SISWA) AS NIS_SISWA');
        $this->db->from($this->table);
        $this->db->join('keu_setup ds',$this->table.'.SETUP_BAYAR=ds.ID_SETUP');
        $this->db->join('keu_detail dt','ds.DETAIL_SETUP=dt.ID_DT');
        $this->db->join('keu_tagihan t','dt.TAGIHAN_DT=t.ID_TAG');
        $this->db->join('md_tahun_ajaran ta','t.TA_TAG=ta.ID_TA');
        $this->db->join('md_siswa ms','ds.SISWA_SETUP=ms.ID_SISWA');
        $this->db->join('md_user mu',$this->table.'.USER_BAYAR=mu.ID_USER');
        $this->db->join('md_pegawai mp','mu.PEGAWAI_USER=mp.ID_PEG');
        $this->db->where(array('JENIS_BAYAR' => 'PEMBAYARAN'));
        
        if($this->session->userdata('ID_HAKAKSES') > 1) {
            $this->db->where('ID_USER', $this->session->userdata('ID_USER'));
        }
    }

    private function _get_datatables_query($select = TRUE) {
        $this->_get_table($select);
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

    function nominal_all() {
        $this->db->select("SUM(NOMINAL_DT) AS TOTAL");
        $this->_get_datatables_query(FALSE);
        $query = $this->db->get()->row();

        return $query->TOTAL;
    }

    public function get_by_id($id) {
        $this->_get_table();
        $this->db->where($this->primary_key, $id);

        return $this->db->get()->row();
    }

    public function get_all($for_html = true) {
        if ($for_html) $this->db->select("ID_BAYAR as value, NAMA_DT as label");
        $this->_get_table();

        return $this->db->get()->result();
    }

    public function get_all_ac($where) {
        $this->db->select("ID_BAYAR as id, NAMA_DT as text");
        $this->_get_table();
        $this->db->like('NAMA_DT', $where);

        return $this->db->get()->result();
    }

    public function count_all() {
        $this->db->from($this->table);

        return $this->db->count_all_results();
    }

}
