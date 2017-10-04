<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Peserta_us_model extends CI_Model {

    var $table = 'akad_siswa';
    var $column = array('NIS_SISWA', 'NAMA_SISWA','JK_SISWA','DEPT_TINGK','NAMA_TINGK','NAMA_KELAS','NAMA_PEG', 'ID_AS');
    var $primary_key = "ID_AS";
    var $order = array("ID_AS" => 'ASC');

    public function __construct() {
        parent::__construct();
    }

    private function _get_table() {
        $this->db->from($this->table);
        $this->db->join('md_siswa ms',$this->table.'.SISWA_AS=ms.ID_SISWA');
        $this->db->join('md_tingkat mt',$this->table.'.TINGKAT_AS=mt.ID_TINGK');
        $this->db->join('md_jenjang_departemen mjd','mt.DEPT_TINGK=mjd.DEPT_MJD');
        $this->db->join('md_jenjang_sekolah mjs','mjd.JENJANG_MJD=mjs.ID_JS');
        $this->db->join('akad_kelas ak',$this->table.'.KELAS_AS=ak.ID_KELAS');
        $this->db->join('md_pegawai mp','ak.WALI_KELAS=mp.ID_PEG', 'LEFT');
        $this->db->where(array(
            'KONVERSI_AS' => 0,
            'AKTIF_SISWA' => 1,
            'AKTIF_AS' => 1,
            'STATUS_MUTASI_SISWA' => NULL,
            'TA_AS' => $this->session->userdata('ID_TA_ACTIVE')
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
                if((($search_columns[$i]['search']['value'] != "") || ($i < 6)) && ($i != 1)) 
                    $this->db->like($item, $search_columns[$i]['search']['value']);
                if (count($search_columns) - 1 == $i) {
                    $this->db->group_end();
                    break;
                }
            }
            $column[$i] = $item;
            $i++;
        }

        if (isset($_POST['order']) && isset($column)) {
            $this->db->order_by($this->column[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
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
        if ($for_html) $this->db->select("ID_AS as value, NAMA_AGAMA as label");
        $this->_get_table();

        return $this->db->get()->result();
    }

    public function get_data_blanko_nilai() {
        $this->db->from('akad_siswa as');
        $this->db->join('md_siswa ms','as.SISWA_AS=ms.ID_SISWA');
        $this->db->join('akad_kelas ak','as.KELAS_AS=ak.ID_KELAS');
        $this->db->join('md_pegawai mp','ak.WALI_KELAS=mp.ID_PEG');
        $this->db->where('KONVERSI_AS', 0);
        $this->db->where('TA_AS', $this->session->userdata('ID_TA_ACTIVE'));
        $this->db->order_by('ID_KELAS', 'ASC');
        $this->db->order_by('NO_ABSEN_AS', 'ASC');

        return $this->db->get()->result();
    }

    public function get_siswa_kartu() {
        $this->db->from($this->table);
        $this->db->join('md_siswa ms',$this->table.'.SISWA_AS=ms.ID_SISWA');
        $this->db->join('md_kecamatan kec', 'ms.KECAMATAN_SISWA=kec.ID_KEC', 'LEFT');
        $this->db->join('md_kabupaten kab', 'kec.KABUPATEN_KEC=kab.ID_KAB', 'LEFT');
        $this->db->join('md_provinsi prov', 'kab.PROVINSI_KAB=prov.ID_PROV', 'LEFT');
        $this->db->join('md_tingkat mt',$this->table.'.TINGKAT_AS=mt.ID_TINGK');
        $this->db->join('akad_kelas ak',$this->table.'.KELAS_AS=ak.ID_KELAS');
        $this->db->join('md_pegawai mp','ak.WALI_KELAS=mp.ID_PEG', 'LEFT');
        $this->db->where(array(
            'KONVERSI_AS' => 0,
            'AKTIF_SISWA' => 1,
            'AKTIF_AS' => 1,
            'STATUS_MUTASI_SISWA' => NULL,
            'TA_AS' => $this->session->userdata('ID_TA_ACTIVE')
        ));
        $this->db->order_by('TINGKAT_AS', 'ASC');
        $this->db->order_by('KELAS_AS', 'ASC');
        $this->db->order_by('NO_ABSEN_AS', 'ASC');

        return $this->db->get()->result_array();
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

    public function get_rows($where) {
        $this->_get_table();
        $this->db->where($where);
        $this->db->order_by('NAMA_SISWA', 'ASC');

        return $this->db->get()->result();
    }

    public function get_rows_array($where) {
        $this->_get_table();
        $this->db->where($where);

        return $this->db->get()->result_array();
    }

    public function get_all_denah($jk) {
        $result_data = array();
        $result_count = array();
        $data = $this->pengaturan->getDataUjianCawu();

        foreach ($data as $jenjang => $detail) {
            $data_tingkat = array();
            $data_count = array();
            foreach ($detail as $tingkat) {
                $data_tingkat[$tingkat] = $this->get_detail_denah($jenjang, $tingkat, $jk);
                $data_count[$tingkat] = count($data_tingkat[$tingkat]);
            }
            $result_data[$jenjang] = $data_tingkat;
            $result_count[$jenjang] = $data_count;
            unset($data_tingkat);
            unset($data_count);
        }
        
        $result = array(
            'DATA' => $result_data,
            'COUNT' => $result_count,
        );

        return $result;
    }

    public function get_detail_denah($jenjang, $tingkat, $jk) {
        $this->db->select('ID_SISWA, RUANG_KELAS');
        $this->_get_table();
        $this->db->where(array(
            'JK_SISWA' => $jk,
            'ID_JS' => $jenjang,
            'NAMA_TINGK' => $tingkat,
        ));

        return $this->db->get()->result();
    }

}
