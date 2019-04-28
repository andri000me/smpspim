<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Rapor_model extends CI_Model {

    var $table = 'akad_siswa';
    var $table_crud = 'akad_nilai';
    var $column = array('NIS_SISWA', 'NAMA_SISWA', 'NAMA_SISWA', 'AKTIF_SISWA');
    var $primary_key = "NAMA_SISWA";
    var $order = array("NAMA_SISWA" => 'ASC');

    public function __construct() {
        parent::__construct();
    }

    private function _get_table($kelas = NULL) {
        $this->db->from($this->table);
        $this->db->join('md_siswa ms', $this->table . '.SISWA_AS=ms.ID_SISWA');
        $this->db->join('akad_kelas ak', $this->table . '.KELAS_AS=ak.ID_KELAS');
        $this->db->join('md_pegawai mp', 'ak.WALI_KELAS=mp.ID_PEG');
        if ($kelas != NULL)
            $this->db->where(array(
                'KELAS_AS' => $kelas,
            ));
        $this->db->where(array(
            'TA_AS' => $this->session->userdata('ID_TA_ACTIVE'),
            'KONVERSI_AS' => 0
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
                    $this->db->like('IFNULL(' . $item . ', "")', $search_value);
                } else {
                    $this->db->or_like('IFNULL(' . $item . ', "")', $search_value);
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
                $this->db->like('IFNULL(' . $item . ', "")', $search_columns[$i]['search']['value']);
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

    function get_datatables($jumlah_mapel, $kelas) {
        $this->set_columns($jumlah_mapel);
        $this->_get_datatables_query($kelas);
        if ($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();

        return $query->result();
    }

    function count_filtered($jumlah_mapel, $kelas) {
        $this->set_columns($jumlah_mapel);
        $this->_get_datatables_query($kelas);
        $query = $this->db->get();

        return $query->num_rows();
    }

    private function set_columns($jumlah_mapel) {
        $column_start = array('NIS_SISWA', 'NAMA_SISWA');
        $column = array_fill(2, $jumlah_mapel + 5, 'AKTIF_SISWA');
        $this->column = array_merge($column_start, $column);
    }

    public function count_all($kelas) {
        $this->_get_table($kelas);

        return $this->db->count_all_results();
    }

    public function get_by_id($id) {
        $this->_get_table();
        $this->db->where($this->primary_key, $id);

        return $this->db->get()->row();
    }

    public function get_all($for_html = true) {
        if ($for_html)
            $this->db->select("ID_AGM as value, NAMA_AGAMA as label");
        $this->_get_table();

        return $this->db->get()->result();
    }

    public function get_all_ac($where) {
        $this->db->select("ID_AGM as id, NAMA_AGAMA as text");
        $this->_get_table();
        $this->db->like('NAMA_AGAMA', $where);

        return $this->db->get()->result();
    }

    public function save($data) {
        $this->db->insert($this->table_crud, $data);

        return $this->db->insert_id();
    }

    public function update($where, $data) {
        $this->db->update($this->table_crud, $data, $where);

        return $this->db->affected_rows();
    }

    public function get_row($where) {
        $this->_get_table();
        $this->db->where($where);

        return $this->db->get()->row();
    }

    public function get_rows($where) {
        $this->_get_table();
        $this->db->where($where);
        $this->db->order_by('NO_ABSEN_AS', 'ASC');

        return $this->db->get()->result();
    }

    public function get_mapel($ID_KELAS) {
        $this->db->from('akad_kelas ak');
        $this->db->join('akad_guru_mapel agm', 'ak.ID_KELAS=agm.KELAS_AGM AND agm.TA_AGM=' . $this->session->userdata('ID_TA_ACTIVE'));
        $this->db->join('md_mapel mm', 'agm.MAPEL_AGM=mm.ID_MAPEL');
        $this->db->where('ID_KELAS', $ID_KELAS);

        return $this->db->get()->result();
    }

    public function get_mapel_guru($ID_KELAS) {
        $this->db->from('akad_guru_mapel agm');
        $this->db->join('md_mapel mm', 'agm.MAPEL_AGM=mm.ID_MAPEL');
        $this->db->join('md_pegawai mp', 'agm.GURU_AGM=mp.ID_PEG');
        $this->db->where('KELAS_AGM', $ID_KELAS);
        $this->db->where('TA_AGM', $this->session->userdata('ID_TA_ACTIVE'));
        $this->db->order_by('TIPE_MAPEL', 'DESC');
        $this->db->order_by('NAMA_MAPEL', 'ASC');

        return $this->db->get()->result();
    }

    public function get_nilai($guru_mapel, $siswa) {
        $this->db->from('akad_nilai');
        $this->db->where(array(
            'TA_AN' => $this->session->userdata('ID_TA_ACTIVE'),
            'CAWU_AN' => $this->session->userdata('ID_CAWU_ACTIVE'),
            'GURU_MAPEL_AN' => $guru_mapel,
            'SISWA_AN' => $siswa,
        ));

        $result = $this->db->get()->row();

        if ($result == NULL)
            return 0;
        else
            return $result->NILAI_AN;
    }

    public function get_absensi($siswa, $status) {
        $this->db->select('COUNT(ID_AKH) AS JUMLAH');
        $this->db->from('akad_kehadiran');
        $this->db->where(array(
            'TA_AKH' => $this->session->userdata('ID_TA_ACTIVE'),
            'CAWU_AKH' => $this->session->userdata('ID_CAWU_ACTIVE'),
            'SISWA_AKH' => $siswa,
            'ALASAN_AKH' => $status,
            'JENIS_AKH' => 1,
        ));

        return $this->db->get()->row()->JUMLAH;
    }

}
