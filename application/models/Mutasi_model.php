<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Mutasi_model extends CI_Model {

    var $table = 'akad_siswa';
    var $column = array('NIS_NIS', 'NAMA_SISWA','JK_SISWA','ALAMAT_SISWA','NAMA_KEC','NAMA_KAB', 'IF(DEPT_TINGK IS NULL,"",DEPT_TINGK)','IF(NAMA_TINGK IS NULL, "", NAMA_TINGK)','NAMA_MUTASI','NO_SURAT_MUTASI_SISWA','TANGGAL_MUTASI_SISWA', 'ID_SISWA', 'ID_SISWA');
    var $primary_key = "ID_AS";
    var $order = array("TANGGAL_MUTASI_SISWA" => 'DESC');

    public function __construct() {
        parent::__construct();
    }

    private function _get_table() {
        $this->db->from($this->table);
        $this->db->join('md_tahun_ajaran mta',$this->table.'.TA_AS=mta.ID_TA');
        $this->db->join('md_siswa ms',$this->table.'.SISWA_AS=ms.ID_SISWA');
        $this->db->join('md_tingkat mt',$this->table.'.TINGKAT_AS=mt.ID_TINGK');
        $this->db->join('akad_kelas ak',$this->table.'.KELAS_AS=ak.ID_KELAS');
        $this->db->join('md_ruang mr','ak.RUANG_KELAS=mr.KODE_RUANG');
        $this->db->join('md_pegawai mp','ak.WALI_KELAS=mp.ID_PEG');
        $this->db->where(array(
            'TA_AS' => $this->session->userdata('ID_TA_ACTIVE'),
            'KELAS_AS <> ' => NULL,
            'KONVERSI_AS' => 0,
            'AKTIF_SISWA' => 1
        ));
    }

    private function _get_table_datatables($select = true) {
        if($select) $this->db->select('*, IF(NAMA_TINGK IS NULL, "", NAMA_TINGK) AS TINGK, IF(DEPT_TINGK IS NULL,"",DEPT_TINGK) AS DEPT');
        $this->db->from('md_siswa ms');
        $this->db->join('md_nis mn', 'mn.SISWA_NIS=ms.ID_SISWA');
        $this->db->join('akad_siswa as', 'as.SISWA_AS=mn.SISWA_NIS AND as.TA_AS=mn.TA_NIS', 'LEFT');
        $this->db->join('akad_kelas ak', 'as.KELAS_AS=ak.ID_KELAS', 'LEFT');
        $this->db->join('md_tingkat mt','as.TINGKAT_AS=mt.ID_TINGK', 'LEFT');
        $this->db->join('md_status_mutasi msmt', 'ms.STATUS_MUTASI_SISWA=msmt.ID_MUTASI', 'LEFT');
        $this->db->join('md_kecamatan kec', 'ms.KECAMATAN_SISWA=kec.ID_KEC', 'LEFT');
        $this->db->join('md_kabupaten kab', 'kec.KABUPATEN_KEC=kab.ID_KAB', 'LEFT');
        $this->db->join('md_provinsi prov', 'kab.PROVINSI_KAB=prov.ID_PROV', 'LEFT');
    }

    private function _get_datatables_query() {
        $this->_get_table_datatables();
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
            $order[6] = 'TINGK';
            $order[7] = 'DEPT';
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    function get_datatables() {
        $this->_get_datatables_query();
        if ($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
//        echo $this->db->last_query();
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

    public function get_detail_siswa($id) {
        $this->_get_table_datatables();
        $this->db->where('ID_SISWA', $id);

        return $this->db->get()->row();
    }

    public function get_all() {
        $this->_get_table();

        return $this->db->get()->result();
    }

    public function get_ac_siswa($where) {
        $this->db->select("ID_SISWA as id, CONCAT(NIS_SISWA,' - ',NAMA_SISWA) as text");
        $this->db->from('md_siswa');
        $this->db->where('AKTIF_SISWA', 1);
        $this->db->like('CONCAT(NIS_SISWA," ",NAMA_SISWA)', $where);
        $this->db->order_by('NAMA_SISWA', 'ASC');

        return $this->db->get()->result();
    }

    public function count_all() {
        $this->_get_table_datatables();

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
