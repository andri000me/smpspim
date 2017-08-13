<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Calon_siswa_model extends CI_Model {

    var $table = 'md_siswa';
    var $column = array('NAMA_SISWA', 'ANGKATAN_SISWA','JK_SISWA', 'TEMPAT_LAHIR_SISWA', 'TANGGAL_LAHIR_SISWA', 'ALAMAT_SISWA', 'kec.NAMA_KEC', 'kab.NAMA_KAB', 'prov.NAMA_PROV','ID_SISWA');
    var $primary_key = "ID_SISWA";
    var $order = array("ID_SISWA" => 'ASC');

    public function __construct() {
        parent::__construct();
    }

    private function _get_table() {
        $this->db->select('*, moha.NAMA_SO AS NAMA_SO_AYAH, mohi.NAMA_SO AS NAMA_SO_IBU, mjpa.NAMA_JP AS NAMA_JP_AYAH, mjpi.NAMA_JP AS NAMA_JP_IBU, , mjpw.NAMA_JP AS NAMA_JP_WALI, mpka.NAMA_JENPEK AS NAMA_JENPEK_AYAH, mpki.NAMA_JENPEK AS NAMA_JENPEK_IBU, mpkw.NAMA_JENPEK AS NAMA_JENPEK_WALI, kec.NAMA_KEC AS NAMA_KEC_SISWA, kab.NAMA_KAB AS NAMA_KAB_SISWA, prov.NAMA_PROV AS NAMA_PROV_SISWA, keco.NAMA_KEC AS NAMA_KEC_ORTU, kabo.NAMA_KAB AS NAMA_KAB_ORTU, provo.NAMA_PROV AS NAMA_PROV_ORTU');
        $this->db->from($this->table);
        $this->db->join('md_asal_sekolah as', $this->table.'.ASAL_SEKOLAH_SISWA=as.ID_AS', 'LEFT');
        $this->db->join('md_suku msk', $this->table.'.SUKU_SISWA=msk.ID_SUKU', 'LEFT');
        $this->db->join('md_agama mag', $this->table.'.AGAMA_SISWA=mag.ID_AGAMA', 'LEFT');
        $this->db->join('md_kondisi mkd', $this->table.'.KONDISI_SISWA=mkd.ID_KONDISI', 'LEFT');
        $this->db->join('md_jenjang_sekolah mjs', $this->table.'.MASUK_JENJANG_SISWA=mjs.ID_JS', 'LEFT');
        $this->db->join('md_jenjang_departemen mjd', 'mjs.ID_JS=mjd.JENJANG_MJD', 'LEFT');
        $this->db->join('md_tingkat mdt', 'mdt.NAMA_TINGK='.$this->table.'.MASUK_TINGKAT_SISWA AND mdt.DEPT_TINGK=mjd.DEPT_MJD', 'LEFT');
        $this->db->join('md_jenis_kelamin mjk', $this->table.'.JK_SISWA=mjk.ID_JK', 'LEFT');
        $this->db->join('md_kewarganegaraan mkw', $this->table.'.WARGA_SISWA=mkw.ID_WARGA', 'LEFT');
        $this->db->join('md_golongan_darah mgd', $this->table.'.GOL_DARAH_SISWA=mgd.ID_DARAH', 'LEFT');
        $this->db->join('md_tempat_tinggal mtt', $this->table.'.TEMPAT_TINGGAL_SISWA=mtt.ID_TEMTING', 'LEFT');
        $this->db->join('md_pondok_siswa mps', $this->table.'.PONDOK_SISWA=mps.ID_MPS', 'LEFT');
        $this->db->join('md_ortu_hidup moha', $this->table.'.AYAH_HIDUP_SISWA=moha.ID_SO', 'LEFT');
        $this->db->join('md_jenjang_pendidikan mjpa', $this->table.'.AYAH_PENDIDIKAN_SISWA=mjpa.ID_JP', 'LEFT');
        $this->db->join('md_pekerjaan mpka', $this->table.'.AYAH_PEKERJAAN_SISWA=mpka.ID_JENPEK', 'LEFT');
        $this->db->join('md_ortu_hidup mohi', $this->table.'.IBU_HIDUP_SISWA=mohi.ID_SO', 'LEFT');
        $this->db->join('md_jenjang_pendidikan mjpi', $this->table.'.IBU_PENDIDIKAN_SISWA=mjpi.ID_JP', 'LEFT');
        $this->db->join('md_pekerjaan mpki', $this->table.'.IBU_PEKERJAAN_SISWA=mpki.ID_JENPEK', 'LEFT');
        $this->db->join('md_hubungan mhb', $this->table.'.WALI_HUBUNGAN_SISWA=mhb.ID_HUB', 'LEFT');
        $this->db->join('md_jenjang_pendidikan mjpw', $this->table.'.WALI_PENDIDIKAN_SISWA=mjpw.ID_JP', 'LEFT');
        $this->db->join('md_pekerjaan mpkw', $this->table.'.WALI_PEKERJAAN_SISWA=mpkw.ID_JENPEK', 'LEFT');
        $this->db->join('md_penghasilan mpg', $this->table.'.ORTU_PENGHASILAN_SISWA=mpg.ID_HASIL', 'LEFT');
        $this->db->join('md_kecamatan kec', $this->table.'.KECAMATAN_SISWA=kec.ID_KEC', 'LEFT');
        $this->db->join('md_kabupaten kab', 'kec.KABUPATEN_KEC=kab.ID_KAB', 'LEFT');
        $this->db->join('md_provinsi prov', 'kab.PROVINSI_KAB=prov.ID_PROV', 'LEFT');
        $this->db->join('md_kecamatan keco', $this->table.'.ORTU_KECAMATAN_SISWA=keco.ID_KEC', 'LEFT');
        $this->db->join('md_kabupaten kabo', 'keco.KABUPATEN_KEC=kabo.ID_KAB', 'LEFT');
        $this->db->join('md_provinsi provo', 'kabo.PROVINSI_KAB=provo.ID_PROV', 'LEFT');
        $this->db->where(array(
            'STATUS_ASAL_SISWA' => 5,
            'STATUS_PSB_SISWA' => 1,
            'AKTIF_SISWA' => 0,
            'STATUS_MUTASI_SISWA' => NULL,
            'ANGKATAN_SISWA' => $this->pengaturan->getTahunPSBAwal(),
        ));
    }
    
    private function _get_table_simple() {
        $this->db->from($this->table);
        $this->db->join('md_jenis_kelamin mjk', $this->table.'.JK_SISWA=mjk.ID_JK', 'LEFT');
        $this->db->join('md_kecamatan kec', $this->table.'.KECAMATAN_SISWA=kec.ID_KEC', 'LEFT');
        $this->db->join('md_kabupaten kab', 'kec.KABUPATEN_KEC=kab.ID_KAB', 'LEFT');
        $this->db->join('md_provinsi prov', 'kab.PROVINSI_KAB=prov.ID_PROV', 'LEFT');
        $this->db->where(array(
            'STATUS_ASAL_SISWA' => 5,
            'STATUS_PSB_SISWA' => 1,
            'AKTIF_SISWA' => 0,
            'STATUS_MUTASI_SISWA' => NULL,
            'ANGKATAN_SISWA' => $this->pengaturan->getTahunPSBAwal(),
        ));
    }

    private function _get_datatables_query() {
        $this->_get_table_simple();
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

    public function count_all($check = NULL) {
        $this->db->from($this->table);

        if ($check !== NULL) $this->db->where($check['name'], $check['value']);
        
        return $this->db->count_all_results();
    }

    public function get_by_id($id) {
        $this->_get_table();
        $this->db->where($this->primary_key, $id);

        return $this->db->get()->row();
    }

    public function get_by_id_simple($id) {
        $this->db->from($this->table);
        $this->db->join('md_jenjang_sekolah mjs', $this->table.'.MASUK_JENJANG_SISWA=mjs.ID_JS');
        $this->db->join('md_jenjang_departemen mjd', 'mjs.ID_JS=mjd.JENJANG_MJD');
        $this->db->join('md_tingkat mdt', 'mdt.NAMA_TINGK='.$this->table.'.MASUK_TINGKAT_SISWA AND mdt.DEPT_TINGK=mjd.DEPT_MJD');
        $this->db->where($this->primary_key, $id);

        return $this->db->get()->row();
    }
    
    public function get_last_number($jenjang, $tingkat) {
        $this->db->select('MAX(NO_UM_SISWA) AS NOMOR_UJIAN');
        $this->db->from($this->table);
        $this->db->where(array(
            'STATUS_ASAL_SISWA' => 5,
            'AKTIF_SISWA' => 0,
            'MASUK_JENJANG_SISWA' => $jenjang,
            'MASUK_TINGKAT_SISWA' => $tingkat,
            'ANGKATAN_SISWA' => $this->pengaturan->getTahunPSBAwal(),
        ));
        
        $result = $this->db->get()->row();
        
        if($result == NULL) 
            return 0;
        else 
            return $result->NOMOR_UJIAN;
    }

}
