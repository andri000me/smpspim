<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Nilai_dauroh_model extends CI_Model {

    var $table = 'akad_siswa';
    var $column = array('NIS_SISWA', 'NO_ABSEN_AS', 'NAMA_SISWA', 'JK_SISWA', 'NAMA_KELAS', 'NAMA_PEG', 'ID_AS', 'ID_AS', 'ID_AS', 'ID_AS', 'ID_AS', 'ID_AS', 'ID_AS'); //KETERANGAN_TINGK
    var $primary_key = "ID_AS";
    var $order = array("ID_AS" => 'ASC');

    public function __construct() {
        parent::__construct();
    }

    private function _get_table($TA = NULL) {
        $this->db->from($this->table);
        $this->db->join('md_tahun_ajaran mta', $this->table . '.TA_AS=mta.ID_TA');
        $this->db->join('md_siswa ms', $this->table . '.SISWA_AS=ms.ID_SISWA');
        $this->db->join('md_tingkat mt', $this->table . '.TINGKAT_AS=mt.ID_TINGK');
        $this->db->join('akad_kelas ak', $this->table . '.KELAS_AS=ak.ID_KELAS');
        $this->db->join('md_pegawai mp', 'ak.WALI_KELAS=mp.ID_PEG');
        $this->db->join('lpba_nilai ln', 'ln.SISWA_LN=ms.ID_SISWA AND ln.CAWU_LN=3 AND ln.TA_LN=' . ($TA == NULL ? $this->session->userdata('ID_TA_ACTIVE') : $TA) . ' AND ln.JENIS_LN="DAUROH"', 'LEFT');
        $this->db->where('KONVERSI_AS', 0);
        $this->db->where('AKTIF_AS', 1);
//        $this->db->where('NAIK_AS', NULL);
        $this->db->where('TA_AS', ($TA == NULL ? $this->session->userdata('ID_TA_ACTIVE') : $TA));
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
//        var_dump($this->db->last_query());

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

    public function get_all($for_html = true) {
        if ($for_html)
            $this->db->select("ID_AS as value, NAMA_AGAMA as label");
        $this->_get_table();

        return $this->db->get()->result();
    }

    public function get_all_ac($where) {
        $this->db->select("ID_AS as id, NAMA_AGAMA as text");
        $this->_get_table();
        $this->db->like('NAMA_AGAMA', $where);

        return $this->db->get()->result();
    }

    public function count_all() {
        $this->db->from($this->table);

        return $this->db->count_all_results();
    }

    public function save($data) {
        $this->db->insert($this->table, $data);

        return $this->db->insert_id();
    }

    public function update($where, $data) {
        $this->db->update($this->table, $data, $where);

        return $this->db->affected_rows();
    }

    public function delete_by_id($id) {
        $where = array($this->primary_key => $id);
        $this->db->delete($this->table, $where);

        return $this->db->affected_rows();
    }

    public function get_rows($where, $TA = NULL) {
        $this->_get_table($TA);
        $this->db->where($where);
        $this->db->order_by('NAMA_SISWA', 'ASC');

        return $this->db->get()->result();
    }

    public function get_row($where) {
        $this->_get_table();
        $this->db->where($where);

        return $this->db->get()->row();
    }

    public function get_rows_array($where, $TA = NULL) {
        $this->_get_table($TA);
        $this->db->where($where);

        return $this->db->get()->result_array();
    }

    private function nilai_siswa_ada($where) {
        $this->db->from('lpba_nilai');
        $this->db->where($where);
        $query = $this->db->get();

        if ($query->num_rows() > 0)
            return TRUE;
        else
            return FALSE;
    }

    public function simpan_nilai($data, $where) {
        if ($this->nilai_siswa_ada($where)) {
            $status = $this->db->update('lpba_nilai', $data, $where);
            $this->kalkulasi_nilai($where);
        } else {
            $data_insert = array_merge($data, $where);
            $status = $this->db->insert('lpba_nilai', $data_insert);
            $this->kalkulasi_nilai($where);
        }

        $this->db->from('lpba_nilai');
        $this->db->where($where);
        $result = $this->db->get()->row();

        return array('status' => $status, 'data' => $result);
    }

    private function kalkulasi_nilai($where) {
        $where_text = '';
        $start = TRUE;
        foreach ($where as $field => $value) {
            $where_text .= ($start ? '' : ' AND ') . $field . '="' . $value . '" ';
            $start = FALSE;
        }

        $this->db->query('UPDATE `lpba_nilai` SET TOTAL_LN=(SYAFAWI_LN + TAHRIRI_LN) WHERE ' . $where_text . ';');
        $this->db->query('UPDATE `lpba_nilai` SET TAQDIR_LN=(SELECT NAMA_TAQDIR FROM lpba_taqdir WHERE TOTAL_LN>=NILAI_MIN_TAQDIR AND TOTAL_LN<=NILAI_MAKS_TAQDIR) WHERE ' . $where_text . ';');
    }

}
