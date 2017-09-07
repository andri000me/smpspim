<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Jadwal_model extends CI_Model {

    var $table = 'akad_jadwal';
    var $column = array('NAMA_KELAS','NAMA_MAPEL','NIP_PEG', 'NAMA_PEG','NAMA_HARI','CONCAT(MULAI_MJP,"-",AKHIR_MJP, " WIB")', 'ID_AJ');
    var $primary_key = "ID_AJ";
    var $order = array("ID_AJ" => 'DESC');

    public function __construct() {
        parent::__construct();
    }

    private function _get_table() {
        $this->db->select('*, CONCAT(MULAI_MJP,"-",AKHIR_MJP, " WIB") AS JAM_PELAJARAN');
        $this->db->from($this->table);
        $this->db->join('akad_guru_mapel agm',$this->table.'.GURU_MAPEL_AJ=agm.ID_AGM');
        $this->db->join('md_tahun_ajaran mta','agm.TA_AGM=mta.ID_TA');
        $this->db->join('akad_kelas ak','agm.KELAS_AGM=ak.ID_KELAS');
        $this->db->join('md_mapel mm','agm.MAPEL_AGM=mm.ID_MAPEL');
        $this->db->join('md_pegawai mp','agm.GURU_AGM=mp.ID_PEG');
        $this->db->join('md_hari mh',$this->table.'.HARI_AJ=mh.ID_HARI');
        $this->db->join('md_jam_pelajaran mjp',$this->table.'.JAM_AJ=mjp.ID_MJP');
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

    public function get_jadwal_kelas($kelas, $hari, $jam) {
        $this->db->from($this->table);
        $this->db->join('akad_guru_mapel agm',$this->table.'.GURU_MAPEL_AJ=agm.ID_AGM');
        $this->db->join('md_pegawai mp','agm.GURU_AGM=mp.ID_PEG');
        $this->db->join('md_mapel mm','agm.MAPEL_AGM=mm.ID_MAPEL');
        $this->db->where('TA_AGM', $this->session->userdata('ID_TA_ACTIVE'));
        $this->db->where(array(
            'KELAS_AGM' => $kelas,
            'HARI_AJ' => $hari,
            'JAM_AJ' => $jam,
        ));

        return $this->db->get()->result();
    }

    public function _jadwal_guru($guru) {
        $this->db->from($this->table);
        $this->db->join('akad_guru_mapel agm',$this->table.'.GURU_MAPEL_AJ=agm.ID_AGM');
        $this->db->where('TA_AGM', $this->session->userdata('ID_TA_ACTIVE'));
        $this->db->where(array(
            'GURU_AGM' => $guru,
        ));
    }

    public function get_jadwal_guru($guru, $where = NULL) {
        $this->_jadwal_guru($guru);
        $this->db->join('akad_kelas ak','agm.KELAS_AGM=ak.ID_KELAS');
        $this->db->join('md_mapel mm','agm.MAPEL_AGM=mm.ID_MAPEL');
        $this->db->join('md_hari mh',$this->table.'.HARI_AJ=mh.ID_HARI');
        $this->db->join('md_jam_pelajaran mjp',$this->table.'.JAM_AJ=mjp.ID_MJP');
        $this->db->order_by('ID_HARI', 'ASC');
        $this->db->order_by('ID_MJP', 'ASC');
        $this->db->order_by('ID_KELAS', 'ASC');
        $this->db->order_by('ID_MAPEL', 'ASC');
        
        if (is_array($where)) $this->db->where($where);

        return $this->db->get()->result();
    }

    public function get_kehadiran_guru($where) {
        $this->db->from($this->table);
        $this->db->join('akad_guru_mapel agm',$this->table.'.GURU_MAPEL_AJ=agm.ID_AGM');
        $this->db->join('akad_kelas ak','agm.KELAS_AGM=ak.ID_KELAS');
        $this->db->where('TA_AGM', $this->session->userdata('ID_TA_ACTIVE'));
        $this->db->where($where);

        return $this->db->get()->row();
    }

    public function get_jumlah_kelas_guru($guru) {
        $this->_jadwal_guru($guru);
        $this->db->group_by('KELAS_AGM');

        return $this->db->get()->num_rows();
    }

    public function get_jumlah_mapel_guru($guru) {
        $this->_jadwal_guru($guru);
        $this->db->group_by('ID_AJ');

        return $this->db->get()->num_rows();
    }

    public function get_rows_array($where) {
        $this->_get_table();
        $this->db->where($where);

        return $this->db->get()->result_array();
    }

    public function get_all($for_html = true) {
        if ($for_html) $this->db->select("ID_AJ as value, NAMA_AGAMA as label");
        $this->_get_table();

        return $this->db->get()->result();
    }

    public function get_all_ac($where) {
        $this->db->select("ID_AJ as id, NAMA_AGAMA as text");
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

    public function delete_by_where($where) {
        $this->db->delete($this->table, $where);
        
        return $this->db->affected_rows();
    }

}
