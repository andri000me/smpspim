<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Pelanggaran_catatan_model extends CI_Model {

    var $table = 'komdis_siswa_catatan';
    var $column = array('NAMA_CAWU','TANGGAL_KS','NO_ABSEN_AS','NIS_SISWA','NAMA_SISWA','NAMA_KELAS','mpw.NAMA_PEG','IF(NAMA_PONDOK_MPS IS NULL, CONCAT(ALAMAT_SISWA, ", ", NAMA_KEC, ", ", NAMA_KAB), CONCAT(NAMA_PONDOK_MPS, ", ", ALAMAT_MPS))','NAMA_KJP','POIN_KJP', 'ID_KS');
//    var $column = array('NAMA_CAWU','TANGGAL_KS','NO_ABSEN_AS','NIS_SISWA','NAMA_SISWA','NAMA_KELAS','mpw.NAMA_PEG','mp.NAMA_PEG','CONCAT(INDUK_KJP, IF(ANAK_KJP IS NULL, "", "."),IF(ANAK_KJP IS NULL, "", ANAK_KJP))','POIN_KJP','KETERANGAN_KS', 'ID_KS');
    var $primary_key = "ID_KS";
    var $order = array("ID_KS" => 'DESC');

    public function __construct() {
        parent::__construct();
    }

    private function _get_table($select = false) {
        if(!$select) $this->db->select('*, CONCAT(INDUK_KJP, IF(ANAK_KJP IS NULL, "", "."),IF(ANAK_KJP IS NULL, "", ANAK_KJP)) AS NO_KJP, mpw.NAMA_PEG AS WALI_KELAS, mp.NAMA_PEG AS SUMBER_INFO, IF(NAMA_PONDOK_MPS IS NULL, CONCAT(ALAMAT_SISWA, ", ", NAMA_KEC, ", ", NAMA_KAB), CONCAT(NAMA_PONDOK_MPS, ", ", ALAMAT_MPS)) AS DOMISILI_SISWA');
        $this->db->from($this->table);
        $this->db->join('md_tahun_ajaran mta', $this->table.'.TA_KS=mta.ID_TA');
        $this->db->join('md_catur_wulan mcw', $this->table.'.CAWU_KS=mcw.ID_CAWU');
        $this->db->join('md_siswa ms', $this->table.'.SISWA_KS=ms.ID_SISWA');
        $this->db->join('md_kecamatan kec', 'ms.KECAMATAN_SISWA=kec.ID_KEC');
        $this->db->join('md_kabupaten kab', 'kec.KABUPATEN_KEC=kab.ID_KAB');
        $this->db->join('md_pondok_siswa mps', 'ms.PONDOK_SISWA=mps.ID_MPS', 'LEFT');
        $this->db->join('akad_siswa as',$this->table.'.SISWA_KS=as.SISWA_AS AND '.$this->table.'.TA_KS=as.TA_AS');
        $this->db->join('akad_kelas ak','as.KELAS_AS=ak.ID_KELAS');
        $this->db->join('md_pegawai mpw','ak.WALI_KELAS=mpw.ID_PEG');
        $this->db->join('md_pegawai mp', $this->table.'.SUMBER_KS=mp.ID_PEG');
        $this->db->join('komdis_jenis_pelanggaran kjp', $this->table.'.PELANGGARAN_KS=kjp.ID_KJP');
        $this->db->where('TA_KS', $this->session->userdata('ID_TA_ACTIVE'));
        $this->db->where('JK_KELAS', $this->session->userdata('JK_PEG'));
        $this->db->order_by('CAWU_KS', 'DESC');
        $this->db->order_by('NAMA_KELAS', 'ASC');
        $this->db->order_by('NO_ABSEN_AS', 'ASC');
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
            $order[7] = 'DOMISILI_SISWA';
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

    public function get_row($where) {
        $this->_get_table();
        $this->db->where($where);

        return $this->db->get()->row();
    }

    public function get_rows($where) {
        $this->_get_table();
        $this->db->where($where);

        return $this->db->get()->result();
    }

    public function get_cetak_pelanggaran($where) {
        $this->db->select('*, LEFT(CREATED_KS, 10) AS TANGGAL_INPUT');
        $this->_get_table(TRUE);
        $this->db->where($where);
        $this->db->order_by('TANGGAL_INPUT', 'ASC');
        $this->db->order_by('TANGGAL_KS', 'ASC');

        return $this->db->get()->result();
    }

    public function get_all($for_html = true) {
        if ($for_html) $this->db->select("ID_KS as value, NAMA_AGAMA as label");
        $this->_get_table();

        return $this->db->get()->result();
    }

    public function get_all_ac($where) {
        $this->db->select("ID_KS as id, NAMA_AGAMA as text");
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
    
    public function get_data_scanner($ID_SISWA) {
        $this->db->from('akad_siswa asw');
        $this->db->join('md_siswa ms','asw.SISWA_AS=ms.ID_SISWA');
        $this->db->join('md_tingkat mt','asw.TINGKAT_AS=mt.ID_TINGK');
        $this->db->join('akad_kelas ak','asw.KELAS_AS=ak.ID_KELAS');
        $this->db->join('md_ruang mr','ak.RUANG_KELAS=mr.KODE_RUANG');
        $this->db->join('md_pegawai mp','ak.WALI_KELAS=mp.ID_PEG');
        $this->db->where('KONVERSI_AS', 0);
        $this->db->where('TA_AS', $this->session->userdata('ID_TA_ACTIVE'));
        $this->db->where('NIS_SISWA', $ID_SISWA);
        $result = $this->db->get();
        
        return $result->row();
    }

}
