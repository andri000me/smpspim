<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Kelas_model extends CI_Model {

    var $table = 'akad_kelas';
    var $column = array('KETERANGAN_TINGK','NAMA_RUANG', 'NAMA_PEG','NAMA_KELAS','KAPASITAS_RUANG','JUMLAH_SISWA_KELAS', 'AKTIF_KELAS','ID_KELAS');
    var $primary_key = "ID_KELAS";
    var $order = array("ID_KELAS" => 'ASC');

    public function __construct() {
        parent::__construct();
    }

    private function _get_table() {
        $this->db->from($this->table);
        $this->db->join('md_tahun_ajaran mta',$this->table.'.TA_KELAS=mta.ID_TA');
        $this->db->join('md_tingkat mt',$this->table.'.TINGKAT_KELAS=mt.ID_TINGK');
        $this->db->join('md_departemen md','mt.DEPT_TINGK=md.ID_DEPT');
        $this->db->join('md_ruang mr',$this->table.'.RUANG_KELAS=mr.KODE_RUANG');
//        $this->db->join('md_jurusan mj',$this->table.'.JURUSAN_KELAS=mj.ID_JURUSAN');
        $this->db->join('md_pegawai mp',$this->table.'.WALI_KELAS=mp.ID_PEG');
        $this->db->join('md_jenis_kelamin mjp',$this->table.'.JK_KELAS=mjp.ID_JK');
        $this->db->where('ID_TA', $this->session->userdata('ID_TA_ACTIVE'));
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

    public function get_by_id_simple($id) {
        $this->db->from($this->table);
        $this->db->where($this->primary_key, $id);

        return $this->db->get()->row();
    }

    public function get_jumlah_siswa($id) {
        $this->db->from($this->table);
        $this->db->where($this->primary_key, $id);

        return $this->db->get()->row()->JUMLAH_SISWA_KELAS;
    }

    public function get_rows($where) {
        $this->_get_table();
        $this->db->where($where);

        return $this->db->get()->result();
    }

    public function get_rows_array($where) {
        $this->_get_table();
        $this->db->where($where);

        return $this->db->get()->result_array();
    }

    public function get_kelas($ID_PEG) {
        $where = array(
            'TA_KELAS' => $this->session->userdata('ID_TA_ACTIVE'),
            'WALI_KELAS' => $ID_PEG
        );
        $this->_get_table();
        $this->db->where($where);

        return $this->db->get()->result();
    }

    public function get_kelas_kenaikan($ID_PEG) {
        $where = array(
            'TA_KELAS' => $this->session->userdata('ID_TA_ACTIVE'),
            'WALI_KELAS' => $ID_PEG,
        );
        $this->_get_table();
        $this->db->join('md_jenjang_departemen mjd', 'mt.DEPT_TINGK=mjd.DEPT_MJD');
        $this->db->join('md_jenjang_sekolah mjs', 'mjd.JENJANG_MJD=mjs.ID_JS AND mt.NAMA_TINGK<>mjs.JUMLAH_KELAS_JS');
        
        $this->db->where($where);

        return $this->db->get()->result();
    }

    public function get_kelas_kelulusan($ID_PEG) {
        $where = array(
            'TA_KELAS' => $this->session->userdata('ID_TA_ACTIVE'),
            'WALI_KELAS' => $ID_PEG,
        );
        $this->_get_table();
        $this->db->join('md_jenjang_departemen mjd', 'mt.DEPT_TINGK=mjd.DEPT_MJD');
        $this->db->join('md_jenjang_sekolah mjs', 'mjd.JENJANG_MJD=mjs.ID_JS AND mt.NAMA_TINGK=mjs.JUMLAH_KELAS_JS');
        
        $this->db->where($where);

        return $this->db->get()->result();
    }

    public function get_wali_kelas($q = '') {
        $where = array(
            'TA_KELAS' => $this->session->userdata('ID_TA_ACTIVE'),
        );
        $this->db->select("WALI_KELAS as id, NAMA_PEG as text");
        $this->_get_table();
        $this->db->where($where);
        if($q != '') $this->db->like('NAMA_PEG', $q);
        $this->db->group_by('WALI_KELAS');

        return $this->db->get()->result();
    }

    public function get_all($for_html = true) {
        if ($for_html) $this->db->select("ID_KELAS as value, NAMA_KELAS as label");
        $this->_get_table();
        $this->db->order_by('ID_KELAS', 'ASC');

        return $this->db->get()->result();
    }

    public function get_all_ac($where) {
        $this->db->select("ID_KELAS as id, CONCAT('KELAS: ', NAMA_KELAS, ' | WALI KELAS: ', NAMA_PEG) as text");
        $this->_get_table();
        $this->db->like('NAMA_KELAS', $where);
        $this->db->order_by('NAMA_KELAS', 'ASC');

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

    public function get_kapasitas_kelas() {
        $this->db->select('IF(SUM(KAPASITAS_RUANG) IS NULL, 0, SUM(KAPASITAS_RUANG)) AS JUMLAH_KAPASITAS, mt.*');
        $this->db->from('md_tingkat mt');
        $this->db->join('akad_kelas ak', 'ak.TINGKAT_KELAS=mt.ID_TINGK AND ak.TA_KELAS='.$this->session->userdata('ID_TA_ACTIVE'), 'LEFT');
        $this->db->join('md_ruang mr', 'ak.RUANG_KELAS=mr.KODE_RUANG', 'LEFT');
        $this->db->group_by('ID_TINGK');

        return $this->db->get()->result_array();
    }

}
