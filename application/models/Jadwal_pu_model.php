<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Jadwal_pu_model extends CI_Model {

    var $table = 'pu_jadwal';
    var $column = array(
        'UM' => array('ID_PUJ', 'NAMA_TA','TANGGAL_PUJ','JAM_MULAI_PUJ','JAM_SELESAI_PUJ','ID_PUJ'),
        'US' => array('ID_PUJ', 'NAMA_TA','NAMA_CAWU','JK_PUJ','TANGGAL_PUJ','JAM_MULAI_PUJ','JAM_SELESAI_PUJ','ID_PUJ')
    );
    var $primary_key = "ID_PUJ";
    var $order = array("ID_PUJ" => 'ASC');

    public function __construct() {
        parent::__construct();
    }

    private function _get_table($tipe) {
        $this->db->from($this->table);
        $this->db->join('md_tahun_ajaran ta',$this->table.'.TA_PUJ=ta.ID_TA');
        $this->db->join('md_catur_wulan cw',$this->table.'.CAWU_PUJ=cw.ID_CAWU', 'LEFT');
        $this->db->where('TIPE_PUJ',$tipe);
    }

    private function _get_datatables_query($tipe) {
        $this->_get_table($tipe);
        $i = 0;
        $search_value = $_POST['search']['value'];
        $search_columns = $_POST['columns'];
        foreach ($this->column[$tipe] as $item) {
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
        foreach ($this->column[$tipe] as $item) {
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

    function get_datatables($tipe) {
        $this->_get_datatables_query($tipe);
        if ($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();

        return $query->result();
    }

    function count_filtered($tipe) {
        $this->_get_datatables_query($tipe);
        $query = $this->db->get();

        return $query->num_rows();
    }

    public function get_by_id($tipe, $id) {
        $this->_get_table($tipe);
        $this->db->where($this->primary_key, $id);

        return $this->db->get()->row();
    }

    public function get_by_tanggal($tipe, $tanggal) {
        $this->_get_table($tipe);
        $this->db->where('TANGGAL_PUJ', $this->date_format->to_store_db($tanggal));

        return $this->db->get()->result_array();
    }

    public function get_all_group_tanggal($tipe) {
        $this->_get_table($tipe);
        $this->db->where('TA_PUJ', $this->session->userdata('ID_PSB_ACTIVE'));
        $this->db->group_by('TANGGAL_PUJ');

        return $this->db->get()->result_array();
    }
    
    public function get_jadwal_um() {
        $this->db->from($this->table);
        $this->db->where('TA_PUJ', $this->session->userdata('ID_PSB_ACTIVE'));

        return $this->db->get()->result_array();
    }

    public function count_all($tipe) {
        $this->db->from($this->table);
        $this->db->where('TIPE_PUJ',$tipe);

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
    
    public function get_mapel_by_tingkat($tipe, $tingkat) {
        $this->db->from($this->table);
        $this->db->join('pu_mapel pm', $this->table.'.ID_PUJ=pm.JADWAL_PUM');
        $this->db->join('md_mapel mm', 'mm.ID_MAPEL=pm.MAPEL_PUM');
        $this->db->where(array(
            'TINGKAT_PUM' => $tingkat,
            'TIPE_PUJ' => ($tipe == 'UM') ? "UM" : "US",
            'CAWU_PUJ' => ($tipe == 'UM') ? NULL : NULL,
            'TA_PUJ' => ($tipe == 'UM') ? $this->session->userdata("ID_PSB_ACTIVE") : $this->session->userdata("ID_TA_ACTIVE"),
        ));
        
        return $this->db->get()->result_array();
    }
    
    public function relasi_jenjang_departemen($jadwal, $jenjang, $tingkat) {
        $this->db->from('md_jenjang_departemen mjd');
        $this->db->join('md_jenjang_sekolah mjs', 'mjs.ID_JS=mjd.JENJANG_MJD');
        $this->db->join('md_tingkat mt', 'mt.DEPT_TINGK=mjd.DEPT_MJD');
        $this->db->join('pu_mapel pm', 'pm.TINGKAT_PUM=mt.ID_TINGK');
        $this->db->join('md_mapel mm', 'mm.ID_MAPEL=pm.MAPEL_PUM');
        $this->db->where(array(
            'NAMA_TINGK' => $tingkat,
            'ID_JS' => $jenjang,
            'JADWAL_PUM' => $jadwal,
        ));
        
        return $this->db->get()->row();
    }
    
    public function get_tanggal_aktif($tipe) {
        $this->_get_table($tipe);
        $this->db->where(array(
            'TIPE_PUJ' => ($tipe == 'UM') ? "UM" : "US",
            'CAWU_PUJ' => ($tipe == 'UM') ? NULL : NULL,
            'TA_PUJ' => ($tipe == 'UM') ? $this->session->userdata("ID_PSB_ACTIVE") : $this->session->userdata("ID_TA_ACTIVE"),
        ));
        $this->db->group_by('TANGGAL_PUJ');
        $this->db->limit(1);
        
        $result = $this->db->get()->row();
        
        return $result->TANGGAL_PUJ;
    }

}
