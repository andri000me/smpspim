<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Nilai_guru_model extends CI_Model {

    var $table = 'akad_guru_mapel';
    var $table_crud = 'akad_nilai';
    var $column = array('NO_ABSEN_AS','NIS_SISWA','NAMA_SISWA','(IF(NILAI_AN IS NULL, 0, NILAI_AN))', 'ID_AGM');
    var $primary_key = "ID_AGM";
    var $order = array("ID_AGM" => 'ASC');

    public function __construct() {
        parent::__construct();
    }

    private function _get_table($mapel = NULL, $guru = NULL, $kelas = NULL) {
        $this->db->select('*, (IF(NILAI_AN IS NULL, 0, NILAI_AN)) AS NILAI_SISWA, (IF(ID_AN IS NULL, "NONE", ID_AN)) AS ID_NILAI');
        $this->db->from($this->table);
        $this->db->join('akad_siswa as', $this->table.'.KELAS_AGM=as.KELAS_AS AND as.TA_AS='.$this->session->userdata('ID_TA_ACTIVE').' AND as.KONVERSI_AS=0 AND as.AKTIF_AS=1');
        $this->db->join('akad_nilai an', $this->table.'.ID_AGM=an.GURU_MAPEL_AN AND an.SISWA_AN=as.ID_AS AND an.CAWU_AN='.$this->session->userdata('ID_CAWU_ACTIVE').' AND an.TA_AN='.$this->session->userdata('ID_TA_ACTIVE').' ', 'LEFT');
        $this->db->join('akad_kelas ak', 'ak.ID_KELAS=as.KELAS_AS AND ak.TA_KELAS='.$this->session->userdata('ID_TA_ACTIVE').' ');
        $this->db->join('md_siswa ms','as.SISWA_AS=ms.ID_SISWA');
        if(!($mapel == NULL || $guru == NULL || $kelas == NULL))
            $this->db->where(array(
                'MAPEL_AGM' => $mapel,
                'GURU_AGM' => $guru,
                'KELAS_AGM' => $kelas,
            ));
        $this->db->where(array(
            'TA_AGM' => $this->session->userdata('ID_TA_ACTIVE'),
//            'NAIK_AS' => NULL
        ));
    }

    private function _get_datatables_query($mapel, $guru, $kelas) {
        $this->_get_table($mapel, $guru, $kelas);
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

    function get_datatables($mapel, $guru, $kelas) {
        $this->_get_datatables_query($mapel, $guru, $kelas);
        if ($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();

//        var_dump($this->db->last_query());
        return $query->result();
    }

    function count_filtered($mapel, $guru, $kelas) {
        $this->_get_datatables_query($mapel, $guru, $kelas);
        $query = $this->db->get();

        return $query->num_rows();
    }

    public function count_all($mapel, $guru, $kelas) {
        $this->_get_table($mapel, $guru, $kelas);

        return $this->db->count_all_results();
    }

    public function get_by_id($id) {
        $this->_get_table();
        $this->db->where($this->primary_key, $id);

        return $this->db->get()->row();
    }

    public function get_all($for_html = true) {
        if ($for_html) $this->db->select("ID_AGM as value, NAMA_AGAMA as label");
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

}
