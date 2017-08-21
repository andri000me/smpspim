<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Cetak_kelulusan_model extends CI_Model {

    var $table = 'md_siswa';
    var $column = array('NAMA_SISWA', 'ANGKATAN_SISWA','JK_SISWA','AYAH_NAMA_SISWA', 'ALAMAT_SISWA', 'kec.NAMA_KEC', 'kab.NAMA_KAB', 'prov.NAMA_PROV','NAMA_MUTASI','ID_SISWA');
    var $primary_key = "ID_AS";
    var $order = array("ID_AS" => 'ASC');

    public function __construct() {
        parent::__construct();
    }

    private function _get_table() {
        $this->db->from($this->table);
        $this->db->join('md_jenis_kelamin mjk', $this->table.'.JK_SISWA=mjk.ID_JK');
        $this->db->join('md_kecamatan kec', $this->table.'.KECAMATAN_SISWA=kec.ID_KEC');
        $this->db->join('md_kabupaten kab', 'kec.KABUPATEN_KEC=kab.ID_KAB');
        $this->db->join('md_provinsi prov', 'kab.PROVINSI_KAB=prov.ID_PROV');
        $this->db->join('md_status_mutasi msmt', $this->table.'.STATUS_MUTASI_SISWA=msmt.ID_MUTASI');
        
        $this->db->where(array(
            'STATUS_MUTASI_SISWA' => 99,
            'AKTIF_SISWA' => 0
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
//        var_dump($this->db->last_query());

        return $query->result();
    }

    function count_filtered() {
        $this->_get_datatables_query();
        $query = $this->db->get();

        return $query->num_rows();
    }

    public function count_all() {
        $this->_get_table();

        return $this->db->count_all_results();
    }

    public function get_data_akademik($ID_SISWA, $ARABIC) {
        $table = 'akad_siswa';
        
        $this->db->select('*, (SELECT MIN(TANGGAL_PUJ) AS TANGGAL_MULAI_UJIAN FROM `pu_jadwal` WHERE TA_PUJ=ID_TA AND CAWU_PUJ=3 GROUP BY TA_PUJ, CAWU_PUJ) AS TANGGAL_MULAI_UJIAN, (SELECT MAX(TANGGAL_PUJ) AS TANGGAL_MULAI_UJIAN FROM `pu_jadwal` WHERE TA_PUJ=ID_TA AND CAWU_PUJ=3 GROUP BY TA_PUJ, CAWU_PUJ) AS TANGGAL_SELESAI_UJIAN');
        $this->db->from($table);
        $this->db->join('md_tahun_ajaran mta',$table.'.TA_AS=mta.ID_TA');
        $this->db->join('md_siswa ms',$table.'.SISWA_AS=ms.ID_SISWA');
        $this->db->join('md_nis mn',$table.'.SISWA_AS=mn.SISWA_NIS AND mn.TA_NIS='.$table.'.TA_AS');
        $this->db->join('md_tingkat mt',$table.'.TINGKAT_AS=mt.ID_TINGK');
        $this->db->join('md_departemen md','mt.DEPT_TINGK=md.ID_DEPT');
        $this->db->where('LULUS_AS', 'L');
        
        if($ID_SISWA == NULL) $this->db->where('ID_TA', $this->session->userdata('ID_TA_ACTIVE'));
        else $this->db->where('ID_SISWA', $ID_SISWA);
        
        if($ARABIC != NULL) {
            if($ARABIC) {
                $this->db->group_start();
                $this->db->where('ID_DEPT', 'DU');
                $this->db->or_where('ID_DEPT', 'DW');
                $this->db->group_end();
            } else {
                $this->db->group_start();
                $this->db->where('ID_DEPT', 'MI');
                $this->db->or_where('ID_DEPT', 'TS');
                $this->db->or_where('ID_DEPT', 'AL');
                $this->db->group_end();
            }
        }
        $this->db->order_by('CREATED_NIS', 'ASC');

        return $this->db->get()->result();
    }
    
    public function get_nilai($ID_AS, $TRANSKRIP = TRUE) {
        $table = 'akad_guru_mapel';
        
        $this->db->select('*, (IF(NILAI_AN IS NULL, 0, NILAI_AN)) AS NILAI_SISWA');
        $this->db->from($table);
        $this->db->join('akad_siswa as', $table.'.KELAS_AGM=as.KELAS_AS');
        $this->db->join('akad_nilai an', $table.'.ID_AGM=an.GURU_MAPEL_AN AND an.SISWA_AN=as.ID_AS AND an.CAWU_AN=3 AND an.TA_AN=as.TA_AS', 'LEFT');
        $this->db->join('md_mapel mm',$table.'.MAPEL_AGM=mm.ID_MAPEL');
        $this->db->where('ID_AS', $ID_AS);
        
        if($TRANSKRIP) $this->db->where('TRANSKRIP_MAPEL', 1);
        else $this->db->where('SYAHADAH_MAPEL', 1);
        
        $this->db->order_by('TIPE_MAPEL', 'ASC');
        $this->db->order_by('KODE_MAPEL', 'ASC');
        
        $result = $this->db->get();

        return $result->result();
    }

}
