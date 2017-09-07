<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Absen_jamaah_siswa_model extends CI_Model {

    var $table = 'komdis_absen';
    var $column = array('NAMA_TA', 'NAMA_CAWU', 'TANGGAL_KAH', 'NIS_SISWA', 'NO_ABSEN_AS','NAMA_SISWA', 'AYAH_NAMA_SISWA','NAMA_KELAS', 'NAMA_PEG', 'IF(ALASAN_KA IS NULL, "", ALASAN_KA)','IF(KETERANGAN_KA IS NULL, "", KETERANGAN_KA)', 'ID_KAH');
    var $primary_key = "ID_KAH";
    var $order = array("ID_KAH" => 'ASC');

    public function __construct() {
        parent::__construct();
    }

    private function _get_table($ID_KAH) {
        $this->db->select('*, IF(ALASAN_KA IS NULL, "", ALASAN_KA) AS ALASAN_KA_SHOW, IF(KETERANGAN_KA IS NULL, "", KETERANGAN_KA) AS KETERANGAN_KA_SHOW');
        $this->db->from($this->table);
        $this->db->join('komdis_absen_header kah', $this->table.'.KAH_KA=kah.ID_KAH');
        $this->db->join('md_tahun_ajaran mta', 'kah.TA_KAH=mta.ID_TA');
        $this->db->join('md_catur_wulan mcw', 'kah.CAWU_KAH=mcw.ID_CAWU');
        $this->db->join('akad_siswa as', $this->table.'.SISWA_KA=as.ID_AS');
        $this->db->join('md_siswa ms','as.SISWA_AS=ms.ID_SISWA');
        // $this->db->join('md_kecamatan kec', 'ms.KECAMATAN_SISWA=kec.ID_KEC', 'LEFT');
        // $this->db->join('md_kabupaten kab', 'kec.KABUPATEN_KEC=kab.ID_KAB', 'LEFT');
        // $this->db->join('md_provinsi prov', 'kab.PROVINSI_KAB=prov.ID_PROV', 'LEFT');
        $this->db->join('akad_kelas ak','as.KELAS_AS=ak.ID_KELAS');
        $this->db->join('md_pegawai mp','ak.WALI_KELAS=mp.ID_PEG');
        $this->db->where('ID_KAH', $ID_KAH);
    }

    private function _get_datatables_query($ID_KAH) {
        $this->_get_table($ID_KAH);
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

    function get_datatables($ID_KAH) {
        $this->_get_datatables_query($ID_KAH);
        if ($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();

        return $query->result();
    }

    function count_filtered($ID_KAH) {
        $this->_get_datatables_query($ID_KAH);
        $query = $this->db->get();

        return $query->num_rows();
    }

    public function get_by_id($id, $ID_KAH) {
        $this->_get_table($ID_KAH);
        $this->db->where($this->primary_key, $id);

        return $this->db->get()->row();
    }

    public function get_rows($ID_KAH, $where) {
        $this->_get_table($ID_KAH);
        $this->db->where($where);

        return $this->db->get()->result();
    }

    public function count_all($ID_KAH) {
        $this->_get_table($ID_KAH);

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

    public function delete_absen($ID_KAH, $ALASAN_KA) {
        $where = array(
            'ALASAN_KA' => $ALASAN_KA,
            'KAH_KA' => $ID_KAH
        );
        $this->db->delete($this->table, $where);
        
        return $this->db->affected_rows();
    }

    public function get_data_scanner($ID_KAH, $NIS_SISWA) {
        $this->_get_table($ID_KAH);
        $this->db->where('NIS_SISWA', $NIS_SISWA);
        $result = $this->db->get();

        if($result == NULL) return NULL;
        else return $result->row();
    }

    public function get_absen_kosong($ID_KAH) {
        $this->_get_table($ID_KAH);
        $this->db->where('ALASAN_KA', '-');

        if($this->db->count_all_results() == 0) return TRUE;
        else return FALSE;
    }

    public function pindah_data_kekehadiran($ID_KAH) {
        $this->db->query('INSERT INTO akad_kehadiran (TA_AKH, CAWU_AKH, SISWA_AKH, TANGGAL_AKH, JENIS_AKH, ALASAN_AKH, KETERANGAN_AKH, USER_AKH) SELECT '.$this->session->userdata('ID_TA_ACTIVE').', '.$this->session->userdata('ID_CAWU_ACTIVE').', SISWA_AS, TANGGAL_KAH, 5, ALASAN_KA, KETERANGAN_KA, '.$this->session->userdata('ID_USER').' FROM komdis_absen ka INNER JOIN komdis_absen_header kah ON ka.KAH_KA=kah.ID_KAH INNER JOIN akad_siswa asw ON ka.SISWA_KA=asw.ID_AS'); // 5 => JAMAAH
        
        return $this->db->affected_rows();
    }

    public function get_data_alpha($ID_KAH) {
        $this->db->from($this->table);
        $this->db->join('komdis_absen_header kah', $this->table.'.KAH_KA=kah.ID_KAH');
        $this->db->join('akad_kehadiran akh', 'akh.TA_AKH=kah.TA_KAH AND akh.CAWU_AKH=kah.CAWU_KAH AND akh.TANGGAL_AKH=kah.TANGGAL_KAH AND akh.JENIS_AKH=5 AND akh.ALASAN_AKH="ALPHA"');
        $this->db->join('akad_siswa asw', $this->table.'.SISWA_KA=asw.ID_AS AND asw.SISWA_AS=akh.SISWA_AKH');
        $this->db->where('ID_KAH', $ID_KAH);
        $result = $this->db->get();

        return $result->result();
    }

}
