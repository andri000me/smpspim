<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Nilai_hafalan_model extends CI_Model {

    var $table = 'ph_nilai_header';
    var $column = array('NIS_SISWA', 'NAMA_SISWA', 'NAMA_KELAS', 'NAMA_PEG', 'NILAI_PNH', 'ID_PNH');
    var $primary_key = "ID_PNH";
    var $order = array("ID_PNH" => 'ASC');

    public function __construct() {
        parent::__construct();
    }

    private function _get_table() {
        $this->db->from($this->table);
        $this->db->join('md_tahun_ajaran mta', $this->table . '.TA_PNH=mta.ID_TA');
        $this->db->join('md_siswa ms', $this->table . '.SISWA_PNH=ms.ID_SISWA');
        $this->db->join('akad_siswa as', 'as.SISWA_AS=ms.ID_SISWA AND as.KONVERSI_AS=0 AND as.AKTIF_AS=1 AND as.TA_AS=' . $this->session->userdata('ID_TA_ACTIVE'));
        $this->db->join('md_tingkat mt', 'as.TINGKAT_AS=mt.ID_TINGK');
        $this->db->join('akad_kelas ak', 'as.KELAS_AS=ak.ID_KELAS');
        $this->db->join('md_pegawai mp', 'ak.WALI_KELAS=mp.ID_PEG');
        $this->db->where(array(
            'TA_PNH' => $this->session->userdata('ID_TA_ACTIVE')
        ));
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

    public function get_all($for_html = true) {
        if ($for_html)
            $this->db->select("ID_PNH as value, NILAI_PNH as label");
        $this->_get_table();

        return $this->db->get()->result();
    }

    public function get_all_ac($where) {
        $this->db->select("ID_PNH as id, NILAI_PNH as text");
        $this->_get_table();
        $this->db->like('NILAI_PNH', $where);

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

    public function get_nilai($siswa) {
        $where = array(
            'TA_PNH' => $this->session->userdata('ID_TA_ACTIVE'),
            'SISWA_PNH' => $siswa,
        );
        $this->_get_table();
        $this->db->where($where);
        $result = $this->db->get()->row();

        if ($result == NULL)
            return NULL;
        else
            return $result->NILAI_PNH;
    }

    public function get_detail_nilai($ID_SISWA) {
        $this->db->from('ph_nilai pn');
        $this->db->join('md_siswa ms', 'pn.SISWA_PHN=ms.ID_SISWA');
        $this->db->where(array(
            'TA_PHN' => $this->session->userdata('ID_TA_ACTIVE'),
            'SISWA_PHN' => $ID_SISWA
        ));
        $this->db->group_by('SISWA_PHN');
        $this->db->limit(1);

        return $this->db->get()->row();
    }

    public function get_batasan($ID_SISWA) {
        $this->db->from('akad_siswa as');
        $this->db->join('md_siswa ms', 'as.SISWA_AS=ms.ID_SISWA');
        $this->db->join('ph_batasan pb', 'as.TINGKAT_AS=pb.TINGKAT_BATASAN AND pb.TA_BATASAN=' . $this->session->userdata('ID_TA_ACTIVE') . ' AND ms.JK_SISWA=pb.JK_BATASAN');
        $this->db->join('ph_kitab pk', 'pk.ID_KITAB=pb.KITAB_BATASAN');
        $this->db->join('ph_nilai pn', 'as.SISWA_AS=pn.SISWA_PHN AND pb.ID_BATASAN=pn.BATASAN_PHN AND pn.TA_PHN=' . $this->session->userdata('ID_TA_ACTIVE'), 'LEFT');
        $this->db->where(array(
            'TA_AS' => $this->session->userdata('ID_TA_ACTIVE'),
            'SISWA_AS' => $ID_SISWA,
            'KONVERSI_AS' => 0
        ));

        return $this->db->get()->result();
    }

    public function simpan_nilai($data) {
        $where = array(
            'TA_PHN' => $data['TA_PHN'],
            'SISWA_PHN' => $data['SISWA_PHN'],
            'BATASAN_PHN' => $data['BATASAN_PHN'],
        );
        $this->db->delete('ph_nilai', $where);
        $this->db->insert('ph_nilai', $data);

        return $this->db->insert_id();
    }

    public function ubah_nilai($where, $data) {
        $this->db->update('ph_nilai', $data, $where);

        return $this->db->affected_rows();
    }

    public function reset_nilai_header($ID_SISWA, $NILAI, $STATUS_PNH) {
        $data = array(
            'TA_PNH' => $this->session->userdata('ID_TA_ACTIVE'),
            'SISWA_PNH' => $ID_SISWA
        );
        $this->db->delete('ph_nilai_header', $data);

        $data['NILAI_PNH'] = $NILAI;
        $data['STATUS_PNH'] = $STATUS_PNH;
        $this->db->insert('ph_nilai_header', $data);

        return $this->db->insert_id();
    }

    public function update_status($ID_SISWA, $STATUS_PNH) {
        $data = array(
            'TA_PNH' => $this->session->userdata('ID_TA_ACTIVE'),
            'SISWA_PNH' => $ID_SISWA,
            'STATUS_PNH' => $STATUS_PNH
        );
        $where = array(
            'TA_PNH' => $this->session->userdata('ID_TA_ACTIVE'),
            'SISWA_PNH' => $ID_SISWA,
        );
        $this->db->from('ph_nilai_header');
        $this->db->where($where);
        $result = $this->db->get();

        if ($result->num_rows() > 0) {
            $where = array(
                'TA_PNH' => $this->session->userdata('ID_TA_ACTIVE'),
                'SISWA_PNH' => $ID_SISWA
            );
            $this->db->update('ph_nilai_header', $data, $where);
        } else {
            $this->db->insert('ph_nilai_header', $data);
        }

        return $this->db->affected_rows();
    }

    public function get_penyemak() {
        $this->db->from('md_pegawai');
        $this->db->order_by('NAMA_PEG', 'ASC');

        return $this->db->get()->result();
    }

}
