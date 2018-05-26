<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Lanjut_jenjang_model extends CI_Model {

    var $table = 'akad_siswa';
    var $column = array('NIS_NIS', 'NO_ABSEN_AS', 'NAMA_SISWA', 'JK_SISWA', 'ID_AS', 'ID_AS', 'ID_AS');
    var $primary_key = "ID_AS";
    var $order = array("ID_AS" => 'ASC');

    public function __construct() {
        parent::__construct();
    }

    private function _get_table($ID_KELAS = NULL) {
        $this->db->from($this->table);
        $this->db->join('md_tahun_ajaran mta', $this->table . '.TA_AS=mta.ID_TA');
        $this->db->join('md_siswa ms', $this->table . '.SISWA_AS=ms.ID_SISWA AND ms.AKTIF_SISWA=0');
//        $this->db->join('md_tingkat mt', $this->table . '.TINGKAT_AS=mt.ID_TINGK');
//        $this->db->join('akad_kelas ak', $this->table . '.KELAS_AS=ak.ID_KELAS');
//        $this->db->join('md_pegawai mp', 'ak.WALI_KELAS=mp.ID_PEG');
        $this->db->join('md_nis mn', 'SISWA_NIS=SISWA_AS AND TA_NIS=TA_AS');
        $this->db->where('KONVERSI_AS', 0);
        $this->db->where('AKTIF_AS', 1);
        $this->db->where('LULUS_AS', 'L');
//        $this->db->where('NAIK_AS', NULL);
        $this->db->where('TA_AS', $this->session->userdata('ID_TA_ACTIVE'));

        if ($ID_KELAS != NULL)
            $this->db->where('KELAS_AS', $ID_KELAS);

        $this->db->group_start();
        $this->db->where('TINGKAT_AS', 6);
        $this->db->or_where('TINGKAT_AS', 8);
        $this->db->or_where('TINGKAT_AS', 10);
        $this->db->or_where('TINGKAT_AS', 13);
        $this->db->group_end();
    }

    private function _get_datatables_query($ID_KELAS) {
        $this->_get_table($ID_KELAS);
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
                $this->db->like("IFNULL(" . $item . ", '')", $search_columns[$i]['search']['value']);
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

    function get_datatables($ID_KELAS) {
        $this->_get_datatables_query($ID_KELAS);
        if ($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
//        echo $this->db->last_query();

        return $query->result();
    }

    function count_filtered($ID_KELAS) {
        $this->_get_datatables_query($ID_KELAS);
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

    public function count_all($ID_KELAS) {
        $this->_get_table($ID_KELAS);

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

    public function get_rows($where) {
        $this->_get_table();
        $this->db->where($where);
        $this->db->order_by('NAMA_SISWA', 'ASC');

        return $this->db->get()->result();
    }

    public function get_row($where) {
        $this->_get_table();
        $this->db->where($where);

        return $this->db->get()->row();
    }

    public function get_rows_array($where) {
        $this->_get_table();
        $this->db->where($where);

        return $this->db->get()->result_array();
    }

    public function simpan_nilai($data) {
        $this->db->insert('lpba_nilai', $data);

        return $this->db->insert_id();
    }

    public function hapus_nilai($where) {
        $this->db->delete('lpba_nilai', $where);

        return $this->db->affected_rows();
    }

    public function get_data_kelas($ID_KELAS) {
        $this->db->from('akad_kelas');
        $this->db->join('md_ruang', 'RUANG_KELAS=KODE_RUANG');
        $this->db->where('ID_KELAS', $ID_KELAS);
        
        return $this->db->get()->row();
    }

}
