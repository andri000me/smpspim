<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Kehadiran_model extends CI_Model {

    var $table = 'akad_kehadiran';
    var $column = array('NAMA_CAWU', 'NIS_SISWA', 'NAMA_SISWA', 'NAMA_KELAS', 'NAMA_PEG', 'TANGGAL_AKH', 'NAMA_MJK', 'ALASAN_AKH', 'KETERANGAN_AKH', 'ID_AKH');
    var $primary_key = "ID_AKH";
    var $order = array("ID_AKH" => 'ASC');

    public function __construct() {
        parent::__construct();
    }

    private function _get_table() {
        $this->db->from($this->table);
        $this->db->join('md_catur_wulan mcw', $this->table . '.CAWU_AKH=mcw.ID_CAWU');
        $this->db->join('md_jenis_kehadiran mjk', $this->table . '.JENIS_AKH=mjk.ID_MJK');
        $this->db->join('md_siswa ms', $this->table . '.SISWA_AKH=ms.ID_SISWA');
        $this->db->join('akad_siswa as', $this->table . '.SISWA_AKH=as.SISWA_AS AND ' . $this->table . '.TA_AKH=as.TA_AS AND as.KONVERSI_AS=0 AND as.AKTIF_AS=1');
        $this->db->join('akad_kelas ak', 'as.KELAS_AS=ak.ID_KELAS');
        $this->db->join('md_pegawai mp', 'ak.WALI_KELAS=mp.ID_PEG');
        $this->db->where('TA_AKH', $this->session->userdata('ID_TA_ACTIVE'));
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

    public function get_by_id($id) {
        $this->_get_table();
        $this->db->where($this->primary_key, $id);

        return $this->db->get()->row();
    }

    public function get_all($for_html = true) {
        if ($for_html)
            $this->db->select("ID_AKH as value, NAMA_AGAMA as label");
        $this->_get_table();

        return $this->db->get()->result();
    }

    public function get_all_ac($where) {
        $this->db->select("ID_AKH as id, NAMA_AGAMA as text");
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

    public function get_siswa($where) {
        $this->db->select("ID_SISWA as id, CONCAT('SISWA: ',NIS_SISWA, ' - ', NAMA_SISWA, ' | KELAS: ', NAMA_KELAS) as text");
        $this->db->from('md_siswa ms');
        $this->db->join('akad_siswa as', 'ms.ID_SISWA=as.SISWA_AS AND as.TA_AS=' . $this->session->userdata('ID_TA_ACTIVE') . ' AND as.KONVERSI_AS=0 AND as.AKTIF_AS=1 AND as.KELAS_AS IS NOT NULL');
        $this->db->join('akad_kelas ak', 'as.KELAS_AS=ak.ID_KELAS');
        $this->db->like('CONCAT(NIS_SISWA," ",NAMA_SISWA)', $where);
        $this->db->where(array(
            'STATUS_MUTASI_SISWA' => NULL,
            'NIS_SISWA <> ' => NULL,
        ));
        $this->db->order_by('NAMA_SISWA', 'ASC');

        return $this->db->get()->result();
    }

    public function absen_divalidasi($TANGGAL_AVA, $KELAS_AVA) {
        $this->db->from('akad_validasi_absen');
        $this->db->where(array(
            'TANGGAL_AVA' => $TANGGAL_AVA,
            'KELAS_AVA' => $KELAS_AVA,
            'STATUS_AVA' => 1
        ));
        $result = $this->db->get();

        if ($result->num_rows() > 0)
            return TRUE;
        else
            return FALSE;
    }

    private function get_validasi($TANGGAL_AVA) {
        $this->db->from('akad_validasi_absen');
        $this->db->where(array(
            'TANGGAL_AVA' => $TANGGAL_AVA,
            'STATUS_AVA' => 1
        ));
        $result = $this->db->get();

        return $result->result();
    }

    public function tambah_validasi_kelas($TANGGAL_AVA, $KELAS_AVA) {
        if (is_array($KELAS_AVA)) {
            $KELAS_VALIDASI = $this->get_validasi($TANGGAL_AVA);
            $ID_KELAS_VALIDASI = array();

            foreach ($KELAS_VALIDASI as $DETAIL) {
                $ID_KELAS_VALIDASI[] = $DETAIL->KELAS_AVA;
            }
            
            foreach ($KELAS_AVA as $DETAIL) {
                if (!in_array($DETAIL->ID_KELAS, $ID_KELAS_VALIDASI)) {
                    $data = array(
                        'TANGGAL_AVA' => $TANGGAL_AVA,
                        'KELAS_AVA' => $DETAIL->ID_KELAS,
                        'STATUS_AVA' => 1,
                        'USER_AVA' => $this->session->userdata('ID_USER')
                    );

                    $this->db->insert('akad_validasi_absen', $data);
                }
            }
        } else {
            $data = array(
                'TANGGAL_AVA' => $TANGGAL_AVA,
                'KELAS_AVA' => $KELAS_AVA,
                'STATUS_AVA' => 1,
                'USER_AVA' => $this->session->userdata('ID_USER')
            );

            $this->db->insert('akad_validasi_absen', $data);
        }
    }

    public function hapus_validasi_kelas($TANGGAL_AVA, $KELAS_AVA) {
        $where = array(
            'TANGGAL_AVA' => $TANGGAL_AVA,
            'KELAS_AVA' => $KELAS_AVA,
        );

        $this->db->delete('akad_validasi_absen', $where);
    }
    
    public function rekapitulasi_absen($where) {
        $this->db->select('*, (CASE WHEN ALASAN_AKH = "SAKIT" THEN COUNT(ALASAN_AKH) END) AS TOTAL_SAKIT, (CASE WHEN ALASAN_AKH = "IZIN" THEN COUNT(ALASAN_AKH) END) AS TOTAL_IZIN, (CASE WHEN ALASAN_AKH = "ALPHA" THEN COUNT(ALASAN_AKH) END) AS TOTAL_ALPHA');
        $this->db->from($this->table);
        $this->db->where('TA_AKH', $this->session->userdata('ID_TA_ACTIVE'));
        $this->db->where('CAWU_AKH', $this->session->userdata('ID_CAWU_ACTIVE'));
        $this->db->where('JENIS_AKH', 1);
        $this->db->group_by('SISWA_AKH, ALASAN_AKH');
        $sql = $this->db->get_compiled_select();
        
        $this->db->select('*, MAX(TOTAL_SAKIT) AS JUMLAH_SAKIT, MAX(TOTAL_IZIN) AS JUMLAH_IZIN, MAX(TOTAL_ALPHA) AS JUMLAH_ALPHA');
        $this->db->from('akad_siswa as');
        $this->db->join('('.$sql.') x', 'x.SISWA_AKH=as.SISWA_AS AND x.TA_AKH=as.TA_AS', 'LEFT');
        $this->db->join('md_siswa ms', 'as.SISWA_AS=ms.ID_SISWA', 'LEFT');
        $this->db->where($where);
        $this->db->where('KONVERSI_AS', 0);
        $this->db->order_by('NO_ABSEN_AS', 'ASC');
        $this->db->group_by('SISWA_AS');
        $result = $this->db->get();
        
        return $result->result();
    }

}
