<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Laporan_lari_model extends CI_Model {

    var $table = 'komdis_siswa_header';
    var $column = array('NIS_SISWA', 'NO_ABSEN_AS', 'NAMA_SISWA', 'NAMA_KELAS', 'NAMA_PEG', 'JUMLAH_POIN_KSH', 'JUMLAH_LARI_KSH', 'ID_KSH', 'ID_KSH', 'ID_KSH');
    var $primary_key = "ID_KSH";
    var $order = array("POIN_KSH" => 'DESC');

    public function __construct() {
        parent::__construct();
    }

    private function _get_table() {
        $this->db->from('(SELECT *, SUM(POIN_KSH) AS JUMLAH_POIN_KSH, SUM(LARI_KSH) AS JUMLAH_LARI_KSH FROM komdis_siswa_header WHERE TA_KSH=' . $this->session->userdata('ID_TA_ACTIVE') . ' GROUP BY SISWA_KSH) ' . $this->table);
        $this->db->join('md_tahun_ajaran mta', $this->table . '.TA_KSH=mta.ID_TA');
        $this->db->join('md_siswa ms', $this->table . '.SISWA_KSH=ms.ID_SISWA AND ms.AKTIF_SISWA=1');
        $this->db->join('akad_siswa as', 'as.SISWA_AS=ms.ID_SISWA AND as.TA_AS=' . $this->session->userdata('ID_TA_ACTIVE'));
        $this->db->join('akad_kelas ak', 'as.KELAS_AS=ak.ID_KELAS');
        $this->db->join('md_pegawai mp', 'ak.WALI_KELAS=mp.ID_PEG');
        $this->db->join('md_kecamatan kec', 'ms.KECAMATAN_SISWA=kec.ID_KEC');
        $this->db->join('md_kabupaten kab', 'kec.KABUPATEN_KEC=kab.ID_KAB');
        $this->db->join('komdis_jenis_tindakan kjt', $this->table.'.JUMLAH_POIN_KSH>=kjt.POIN_KJT AND '.$this->table.'.JUMLAH_POIN_KSH<=kjt.POIN_MAKS_KJT', 'LEFT');
        $this->db->where('KONVERSI_AS', 0);
        $this->db->where('JUMLAH_LARI_KSH > ', 2);
        $this->db->where('CETAK_LARI_KSH', 0);
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

    public function get_data_cetak($where) {
        $this->_get_table();
        $this->db->where($where);
        $this->db->where('CETAK_LARI_KSH', 0);
        $this->db->order_by('NAMA_KELAS', 'ASC');
        $this->db->order_by('NO_ABSEN_AS', 'ASC');

        return $this->db->get()->result();
    }

    public function get_poin_siswa($TA_KSH, $CAWU_KSH, $SISWA_KSH) {
        $this->db->from($this->table);
        $this->db->where(array(
            'TA_KSH' => $TA_KSH,
            'CAWU_KSH' => $CAWU_KSH,
            'SISWA_KSH' => $SISWA_KSH,
        ));

        $result = $this->db->get()->row();

        if ($result == NULL)
            return 0;
        else
            return $result->POIN_KSH;
    }

    public function get_all($for_html = true) {
        if ($for_html)
            $this->db->select("ID_KSH as value, NAMA_AGAMA as label");
        $this->_get_table();

        return $this->db->get()->result();
    }

    public function get_all_ac($where) {
        $this->db->select("ID_KSH as id, NAMA_AGAMA as text");
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

}
