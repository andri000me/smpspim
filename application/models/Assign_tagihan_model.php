<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Assign_tagihan_model extends CI_Model {

    var $table = 'keu_setup';
    var $column = array('NIS_SISWA', 'NAMA_SISWA', 'ANGKATAN_SISWA','JK_SISWA','AYAH_NAMA_SISWA','NAMA_KELAS', 'NAMA_PEG', 'ID_SISWA');
    var $primary_key = "ID_SETUP";
    var $order = array("ID_SETUP" => 'ASC');

    public function __construct() {
        parent::__construct();
    }

    private function _get_table() {
        $this->db->from($this->table);
        $this->db->join('keu_detail dt',$this->table.'.DETAIL_SETUP=dt.ID_DT');
        $this->db->join('keu_tagihan t','dt.TAGIHAN_DT=t.ID_TAG');
        $this->db->join('md_tahun_ajaran ta','t.TA_TAG=ta.ID_TA');
        $this->db->join('md_siswa s',$this->table.'.SISWA_SETUP=s.ID_SISWA');
        $this->db->join('md_kecamatan kec', 's.KECAMATAN_SISWA=kec.ID_KEC');
        $this->db->join('md_kabupaten kab', 'kec.KABUPATEN_KEC=kab.ID_KAB');
        $this->db->join('md_provinsi prov', 'kab.PROVINSI_KAB=prov.ID_PROV');
        $this->db->join('md_negara neg', 'prov.NEGARA_PROV=neg.ID_NEGARA');
    }

    private function _get_table_table() {
        $table = 'akad_siswa';
        $this->db->from($table);
        $this->db->join('md_siswa ms',$table.'.SISWA_AS=ms.ID_SISWA');
        $this->db->join('md_tingkat mt',$table.'.TINGKAT_AS=mt.ID_TINGK');
        $this->db->join('md_jenjang_departemen mjd','mt.DEPT_TINGK=mjd.DEPT_MJD');
        $this->db->join('md_jenjang_sekolah mjs','mjd.JENJANG_MJD=mjs.ID_JS');
        $this->db->join('akad_kelas ak',$table.'.KELAS_AS=ak.ID_KELAS');
        $this->db->join('md_pegawai mp','ak.WALI_KELAS=mp.ID_PEG');
        $this->db->where(array(
            'KONVERSI_AS' => 0,
            'AKTIF_AS' => 1,
            'TA_AS' => $this->session->userdata('ID_TA_ACTIVE')
        ));
        
        if($this->session->userdata('TAGIHAN') !== NULL) {
            $keu = json_decode($this->session->userdata('TAGIHAN'));
            $i = 0;
            foreach ($keu as $detail) {
                if($i == 0) {
                    $this->db->group_start();
                    $this->db->where('(DEPT_TINGK="'.$detail->DEPT_DT.'" AND JK_SISWA="'.$detail->JK_MUK.'")');
                } else {
                    $this->db->or_where('(DEPT_TINGK="'.$detail->DEPT_DT.'" AND JK_SISWA="'.$detail->JK_MUK.'")');
                }
                
                if($i == (count($keu) - 1)) {
                    $this->db->group_end();
                }
                
                $i++;
            }
        }
    }

    private function _get_datatables_query() {
        $this->_get_table_table();
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

    public function get_data_kartu($ID_KELAS, $ID_SISWA) {
        $table = 'akad_siswa';
        $this->db->from($table);
        $this->db->join('md_siswa ms',$table.'.SISWA_AS=ms.ID_SISWA');
        $this->db->join('md_kecamatan kec', 'ms.KECAMATAN_SISWA=kec.ID_KEC');
        $this->db->join('md_kabupaten kab', 'kec.KABUPATEN_KEC=kab.ID_KAB');
        $this->db->join('akad_kelas ak',$table.'.KELAS_AS=ak.ID_KELAS');
        $this->db->join('md_tingkat mt',$table.'.TINGKAT_AS=mt.ID_TINGK');
        $this->db->where(array(
            'KONVERSI_AS' => 0,
            'AKTIF_AS' => 1,
            'TA_AS' => $this->session->userdata('ID_TA_ACTIVE')
        ));
        
        if($ID_SISWA != NULL) $this->db->where('ID_SISWA', $ID_SISWA);
        elseif($ID_KELAS != NULL) $this->db->where('ID_KELAS', $ID_KELAS);
        
        $query = $this->db->get();

        return $query->result();
    }

    function count_filtered() {
        $this->_get_datatables_query();
        $query = $this->db->get();

        return $query->num_rows();
    }

    public function count_all() {
        $this->_get_table_table();

        return $this->db->count_all_results();
    }

    public function get_by_id($id) {
        $this->_get_table();
        $this->db->where($this->primary_key, $id);

        return $this->db->get()->row();
    }

    public function get_all($for_html = true) {
        if ($for_html) $this->db->select("ID_SETUP as value,  CONCAT(NAMA_TA, ' - ', NAMA_TAG) as label");
        $this->_get_table();

        return $this->db->get()->result();
    }

    public function get_all_ac($where) {
        $this->db->select("ID_SETUP as id,  CONCAT(NAMA_TA, ' - ', NAMA_TAG) as text");
        $this->_get_table();
        $this->db->like('NAMA_TAG', $where);

        return $this->db->get()->result();
    }

    public function save($data) {
        $this->db->insert($this->table, $data);

        return $this->db->insert_id();
    }

    public function update($where, $data) {
        $this->db->update($this->table, $data, $where);
        
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

        return $this->db->get()->result();
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

    public function get_tagihan_siswa_simple($ID_SISWA) {
        $this->db->from($this->table);
        $this->db->join('keu_detail dt',$this->table.'.DETAIL_SETUP=dt.ID_DT');
        $this->db->join('keu_tagihan t','dt.TAGIHAN_DT=t.ID_TAG');
        $this->db->where(array(
            'SISWA_SETUP' => $ID_SISWA,
            'KADALUARSA_SETUP' => 0,
            'PSB_TAG' => 0,
            'TA_TAG' => $this->session->userdata('ID_TA_ACTIVE')
        ));
        $result = $this->db->get();
        
        return $result->result();
    }

    public function get_tagihan_siswa_kartu($ID_SISWA) {
        $this->db->from($this->table);
        $this->db->join('keu_detail dt',$this->table.'.DETAIL_SETUP=dt.ID_DT');
        $this->db->join('keu_tagihan t','dt.TAGIHAN_DT=t.ID_TAG AND t.PSB_TAG=0');
        $this->db->where(array(
            'SISWA_SETUP' => $ID_SISWA,
            'KADALUARSA_SETUP' => 0,
            'TA_TAG' => $this->session->userdata('ID_TA_ACTIVE')
        ));
        $this->db->order_by('ID_DT', 'ASC');
        $result = $this->db->get();
        
        return $result->result();
    }
    
    public function get_tagihan_siswa($ID_SISWA) {
        $this->_get_table();
        $this->db->where(array(
            'SISWA_SETUP' => $ID_SISWA,
            'STATUS_SETUP' => 0,
            'KADALUARSA_SETUP' => 0,
        ));
        
        if($this->session->userdata('TAGIHAN') !== NULL) {
            $keu = json_decode($this->session->userdata('TAGIHAN'));
            $i = 0;
            foreach ($keu as $detail) {
                if($i == 0) {
                    $this->db->group_start();
                    $this->db->where('ID_TAG='.$detail->ID_TAG.' AND DEPT_DT="'.$detail->DEPT_DT.'"');
                } else {
                    $this->db->or_where('ID_TAG='.$detail->ID_TAG.' AND DEPT_DT="'.$detail->DEPT_DT.'"');
                }
                
                if($i == (count($keu) - 1)) {
                    $this->db->group_end();
                }
                
                $i++;
            }
        }
        
        $this->db->order_by('NAMA_TA', 'ASC');
        $this->db->order_by('PSB_TAG', 'DESC');
        $this->db->order_by('ID_DT', 'ASC');
        $result = $this->db->get();
        
        return $result->result();
    }
    
    public function get_tagihan_siswa_mutasi($ID_SISWA) {
        $this->db->from($this->table);
        $this->db->join('keu_detail dt',$this->table.'.DETAIL_SETUP=dt.ID_DT');
        $this->db->join('keu_tagihan t','dt.TAGIHAN_DT=t.ID_TAG');
        $this->db->join('md_tahun_ajaran ta','t.TA_TAG=ta.ID_TA');
        $this->db->where(array(
            'SISWA_SETUP' => $ID_SISWA,
            'STATUS_SETUP' => 0,
            'KADALUARSA_SETUP' => 0,
        ));
        
        $this->db->order_by('NAMA_TA', 'ASC');
        $this->db->order_by('PSB_TAG', 'DESC');
        $this->db->order_by('ID_DT', 'ASC');
        $result = $this->db->get();
        
        return $result->result();
    }
    
    public function is_psb_lunas($ID_SISWA) {
        $this->_get_table();
        $this->db->where(array(
            'SISWA_SETUP' => $ID_SISWA,
            'STATUS_SETUP' => 0,
            'KADALUARSA_SETUP' => 0,
            'PSB_TAG' => 1
        ));
        
        if($this->db->count_all_results() > 0) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

    public function is_assigned($DETAIL_SETUP) {
        $this->db->from($this->table);
        $this->db->where('DETAIL_SETUP', $DETAIL_SETUP);

        if($this->db->count_all_results() > 0)
            return TRUE;
        else 
            return FALSE;
    }

    public function get_nominal_detail($id) {
        $this->_get_table();
        $this->db->where($this->primary_key, $id);

        return $this->db->get()->row()->NOMINAL_DT;
    }
    
    public function pembayaran_pengembalian($ID_SISWA) {
        $this->_get_table();
        $this->db->where(array(
            'ID_SISWA' => $ID_SISWA,
            'STATUS_SETUP' => 1,
            'NOMINAL_DT > ' => 0,
        ));
        $result = $this->db->get();
        
        return $result->result();
    }
}
