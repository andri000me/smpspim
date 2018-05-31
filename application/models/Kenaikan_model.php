<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Kenaikan_model extends CI_Model {

    var $table = 'akad_siswa';
    var $table_crud = 'akad_nilai';
    var $column = array('NO_ABSEN_AS','NIS_SISWA','NAMA_SISWA', 'ID_AS', 'ID_AS', 'ID_AS', 'ID_AS', 'ID_AS', 'ID_AS', 'ID_AS', 'ID_AS', 'ID_AS', 'ID_AS', 'ID_AS', 'ID_AS', 'ID_AS', 'ID_AS', 'ID_AS', 'ID_AS', 'ID_AS', 'ID_AS', 'ID_AS', 'ID_AS', 'ID_AS', 'ID_AS', 'ID_AS', 'TINGKAT_AS');
    var $primary_key = "NAMA_SISWA";
    var $order = array("NAMA_SISWA" => 'ASC');

    public function __construct() {
        parent::__construct();
    }

    private function _get_table($kelas = NULL) {
        $this->db->from($this->table);
        $this->db->join('md_siswa ms',$this->table.'.SISWA_AS=ms.ID_SISWA');
        $this->db->join('akad_kelas ak',$this->table.'.KELAS_AS=ak.ID_KELAS');
        $this->db->join('md_pegawai mp','ak.WALI_KELAS=mp.ID_PEG');
        if($kelas != NULL)
            $this->db->where(array(
                'KELAS_AS' => $kelas,
            ));
        $this->db->where(array(
            'TA_AS' => $this->session->userdata('ID_TA_ACTIVE'),
            'KONVERSI_AS' => 0,
            'NAIK_AS' => NULL
        ));
    }

    private function _get_datatables_query($kelas) {
        $this->_get_table($kelas);
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

    function get_datatables($kelas) {
        $this->_get_datatables_query($kelas);
        if ($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();

//        var_dump($this->db->last_query());
        return $query->result();
    }

    function count_filtered($kelas) {
        $this->_get_datatables_query($kelas);
        $query = $this->db->get();

        return $query->num_rows();
    }

    public function count_all($kelas) {
        $this->_get_table($kelas);

        return $this->db->count_all_results();
    }

    public function get_by_id($id) {
        $this->_get_table();
        $this->db->where($this->primary_key, $id);

        return $this->db->get()->row();
    }

    public function get_all($for_html = true, $kelas = NULL) {
        if ($for_html) $this->db->select("ID_AGM as value, NAMA_AGAMA as label");
        $this->_get_table($kelas);

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

    public function get_row($where) {
        $this->_get_table();
        $this->db->where($where);

        return $this->db->get()->row();
    }

    public function get_rows($where) {
        $this->_get_table();
        $this->db->where($where);
        $this->db->order_by('ID_AS', 'ASC');

        return $this->db->get()->result();
    }
    
    public function get_nilai($siswa, $cawu, $tipe) {
        $this->db->select('SUM(NILAI_AN) AS TOTAL_NILAI,  COUNT(MAPEL_AGM) AS JUMLAH_MAPEL');
        $this->db->from('akad_siswa as');
        $this->db->join('akad_guru_mapel agm','as.KELAS_AS=agm.KELAS_AGM AND as.TA_AS='.$this->session->userdata('ID_TA_ACTIVE'));
        $this->db->join('md_mapel mm','mm.ID_MAPEL=agm.MAPEL_AGM AND mm.TIPE_MAPEL='.$tipe);
        $this->db->join('akad_nilai an', 'agm.ID_AGM=an.GURU_MAPEL_AN AND an.SISWA_AN=as.ID_AS AND an.CAWU_AN='.$cawu.' AND an.TA_AN='.$this->session->userdata('ID_TA_ACTIVE'), 'LEFT');
        $this->db->where(array(
            'TA_AS' => $this->session->userdata('ID_TA_ACTIVE'),
            'SISWA_AS' => $siswa,
            'KONVERSI_AS' => 0
        ));
        
        return $this->db->get()->row();
    }
    
    public function get_absensi($siswa, $status) {
        $this->db->select('COUNT(ID_AKH) AS JUMLAH');
        $this->db->from('akad_kehadiran');
        $this->db->where(array(
            'TA_AKH' => $this->session->userdata('ID_TA_ACTIVE'),
            'SISWA_AKH' => $siswa,
            'ALASAN_AKH' => $status,
            'JENIS_AKH' => 1 // KBM
        ));
        
        return $this->db->get()->row()->JUMLAH;
    }
    
    public function get_dauroh($siswa) {
        $this->db->from('lpba_nilai');
        $this->db->where(array(
            'TA_LN' => $this->session->userdata('ID_TA_ACTIVE'),
            'CAWU_LN' => 3,
            'SISWA_LN' => $siswa,
            'JENIS_LN' => 'DAUROH',
        ));
        
        $result = $this->db->get()->row();
        
        if ($result == NULL)
            return '-';
        else
            return $result->NILAI_LN;
    }
    
    public function get_poin($siswa) {
        $this->db->select('SUM(POIN_KSH) AS JUMLAH');
        $this->db->from('komdis_siswa_header');
        $this->db->where(array(
            'TA_KSH' => $this->session->userdata('ID_TA_ACTIVE'),
            'SISWA_KSH' => $siswa,
        ));
        
        $result = $this->db->get()->row()->JUMLAH;
        
        return ($result == '') ? 0 : $result;
    }

}
