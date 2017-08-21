<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Nilai_um_model extends CI_Model {

    var $table = 'md_siswa';
    var $column = array('NO_UM_SISWA', 'NAMA_SISWA', 'NAMA_SISWA', 'NAMA_SISWA', 'NAMA_SISWA', 'NAMA_SISWA', 'NAMA_SISWA', 'NAMA_SISWA', 'NAMA_SISWA', 'NAMA_SISWA', 'NAMA_SISWA', 'NAMA_SISWA', 'NAMA_SISWA', 'NAMA_SISWA', 'NAMA_SISWA', 'NAMA_SISWA', 'NAMA_SISWA', 'NAMA_SISWA', 'ID_SISWA');
    var $primary_key = "ID_SISWA";
    var $order = array("NO_UM_SISWA" => 'ASC');

    public function __construct() {
        parent::__construct();
    }

    private function _get_table($ID_TINGK, $JK_SISWA) {
        $this->db->from($this->table);
        $this->db->join('md_jenjang_sekolah jss', $this->table . '.MASUK_JENJANG_SISWA=jss.ID_JS');
        $this->db->join('md_jenjang_departemen jdd', 'jdd.JENJANG_MJD=jss.ID_JS');
        $this->db->join('md_tingkat mdt', 'mdt.NAMA_TINGK='.$this->table.'.MASUK_TINGKAT_SISWA AND mdt.DEPT_TINGK=jdd.DEPT_MJD');
        $this->db->where(array(
            'STATUS_ASAL_SISWA' => 5,
            'STATUS_PSB_SISWA' => 1,
            'AKTIF_SISWA' => 0,
            'NO_UM_SISWA<>' => NULL,
            'ANGKATAN_SISWA' => $this->pengaturan->getTahunPSBAwal(),
        ));

        if($ID_TINGK != NULL) $this->db->where('ID_TINGK', $ID_TINGK);
        if($JK_SISWA != NULL) $this->db->where('JK_SISWA', $JK_SISWA);

        $this->db->order_by('ID_TINGK', 'ASC');
        $this->db->order_by('JK_SISWA', 'ASC');
        $this->db->order_by('NO_UM_SISWA', 'ASC');
    }

    private function _get_datatables_query($ID_TINGK, $JK_SISWA) {
        $this->_get_table($ID_TINGK, $JK_SISWA);
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

    function get_datatables($ID_TINGK, $JK_SISWA) {
        $this->_get_datatables_query($ID_TINGK, $JK_SISWA);
        if ($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();

        return $query->result();
    }

    function get_all($ID_TINGK, $JK_SISWA) {
        $this->_get_table($ID_TINGK, $JK_SISWA);
        $query = $this->db->get();

        return $query->result();
    }

    function count_filtered($ID_TINGK, $JK_SISWA) {
        $this->_get_datatables_query($ID_TINGK, $JK_SISWA);
        $query = $this->db->get();

        return $query->num_rows();
    }

    public function count_all($ID_TINGK, $JK_SISWA) {
        $this->_get_table($ID_TINGK, $JK_SISWA);

        return $this->db->count_all_results();
    }

    public function get_all_denah($jk) {
        $result_data = array();
        $result_count = array();
        $data = $this->pengaturan->getDataUjianPSB();

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
        $psb_ex = explode('/', $this->session->userdata('NAMA_PSB_ACTIVE'));
        $tahun_req = $psb_ex[0];

        $this->db->select('ID_SISWA');
        $this->db->from($this->table);
        $this->db->where(array(
            'JK_SISWA' => $jk,
            'MASUK_JENJANG_SISWA' => $jenjang,
            'MASUK_TINGKAT_SISWA' => $tingkat,
            'ANGKATAN_SISWA' => $tahun_req
        ));

        return $this->db->get()->result();
    }

    function get_nilai_siswa($ID_SISWA) {
        $this->_get_table(NULL, NULL);
        $this->db->where('ID_SISWA', $ID_SISWA);
        $query = $this->db->get();

        return $query->row();
    }

    public function save($data) {
        $this->db->insert('pu_nilai_um', $data);

        return $this->db->insert_id();
    }

    public function update($where, $data) {
        $this->db->update('pu_nilai_um', $data, $where);
        
        return $this->db->affected_rows();
    }

    public function is_stored($data) {
        $this->db->from('pu_nilai_um');
        $this->db->where($data);

        if($this->db->count_all_results() > 0) 
            return TRUE;
        else 
            return FALSE;
    }

    public function get_data($JADWAL_PNU, $SISWA_PNU, $MAPEL_PNU) {
        $this->db->from('pu_nilai_um');
        $this->db->where(array(
            'JADWAL_PNU' => $JADWAL_PNU,
            'SISWA_PNU' => $SISWA_PNU,
            'MAPEL_PNU' => $MAPEL_PNU,
        ));
        
        return $this->db->get()->row();
    }

    function get_data_all() {
        $this->_get_table(NULL, NULL);

        return $this->db->get()->result();
    }
}
