<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Pencarian_model extends CI_Model {

    var $table = 'md_siswa';

    public function __construct() {
        parent::__construct();
    }

    private function _get_table() {
        $this->db->from($this->table);
        $this->db->join('md_jenis_kelamin mjk', $this->table.'.JK_SISWA=mjk.ID_JK', 'LEFT');
        $this->db->join('md_kecamatan kec', $this->table.'.KECAMATAN_SISWA=kec.ID_KEC', 'LEFT');
        $this->db->join('md_kabupaten kab', 'kec.KABUPATEN_KEC=kab.ID_KAB', 'LEFT');
        $this->db->join('md_provinsi prov', 'kab.PROVINSI_KAB=prov.ID_PROV', 'LEFT');
        $this->db->join('md_pondok_siswa mps', $this->table.'.PONDOK_SISWA=mps.ID_MPS', 'LEFT');
        $this->db->join('akad_siswa asw', $this->table.'.ID_SISWA=asw.SISWA_AS AND TA_AS='.$this->session->userdata('ID_TA_ACTIVE'), 'LEFT');
        $this->db->join('akad_kelas ak','asw.KELAS_AS=ak.ID_KELAS', 'LEFT');
        $this->db->order_by('NAMA_SISWA', 'ASC');
    }
    
    private function _get_table_detail() {
        $this->db->select('*, moha.NAMA_SO AS NAMA_SO_AYAH, mohi.NAMA_SO AS NAMA_SO_IBU, mjpa.NAMA_JP AS NAMA_JP_AYAH, mjpi.NAMA_JP AS NAMA_JP_IBU, , mjpw.NAMA_JP AS NAMA_JP_WALI, mpka.NAMA_JENPEK AS NAMA_JENPEK_AYAH, mpki.NAMA_JENPEK AS NAMA_JENPEK_IBU, mpkw.NAMA_JENPEK AS NAMA_JENPEK_WALI, kec.NAMA_KEC AS NAMA_KEC_SISWA, kab.NAMA_KAB AS NAMA_KAB_SISWA, prov.NAMA_PROV AS NAMA_PROV_SISWA, keco.NAMA_KEC AS NAMA_KEC_ORTU, kabo.NAMA_KAB AS NAMA_KAB_ORTU, provo.NAMA_PROV AS NAMA_PROV_ORTU, mjsas.NAMA_JS AS NAMA_JS_AS, mjs.NAMA_JS AS NAMA_JS_SISWA, kecas.NAMA_KEC AS NAMA_KEC_AS, kabas.NAMA_KAB AS NAMA_KAB_AS, provas.NAMA_PROV AS NAMA_PROV_AS, mpmut.NAMA_PEG AS NAMA_PEG_MUTASI');
        $this->db->join('md_asal_sekolah as', $this->table.'.ASAL_SEKOLAH_SISWA=as.ID_AS', 'LEFT');
        $this->db->join('md_jenjang_sekolah mjsas', 'as.JENJANG_AS=mjsas.ID_JS', 'LEFT');
        $this->db->join('md_kecamatan kecas', 'as.KECAMATAN_AS=kecas.ID_KEC', 'LEFT');
        $this->db->join('md_kabupaten kabas', 'kecas.KABUPATEN_KEC=kabas.ID_KAB', 'LEFT');
        $this->db->join('md_provinsi provas', 'kabas.PROVINSI_KAB=provas.ID_PROV', 'LEFT');
        $this->db->join('md_suku msk', $this->table.'.SUKU_SISWA=msk.ID_SUKU', 'LEFT');
        $this->db->join('md_agama mag', $this->table.'.AGAMA_SISWA=mag.ID_AGAMA', 'LEFT');
        $this->db->join('md_kondisi mkd', $this->table.'.KONDISI_SISWA=mkd.ID_KONDISI', 'LEFT');
        $this->db->join('md_jenjang_sekolah mjs', $this->table.'.MASUK_JENJANG_SISWA=mjs.ID_JS', 'LEFT');
        $this->db->join('md_jenjang_departemen mjd', 'mjs.ID_JS=mjd.JENJANG_MJD', 'LEFT');
        $this->db->join('md_tingkat mdt', 'mdt.NAMA_TINGK='.$this->table.'.MASUK_TINGKAT_SISWA AND mdt.DEPT_TINGK=mjd.DEPT_MJD', 'LEFT');
        $this->db->join('md_kewarganegaraan mkw', $this->table.'.WARGA_SISWA=mkw.ID_WARGA', 'LEFT');
        $this->db->join('md_golongan_darah mgd', $this->table.'.GOL_DARAH_SISWA=mgd.ID_DARAH', 'LEFT');
        $this->db->join('md_hobi mhi', $this->table.'.HOBI_SISWA=mhi.ID_HOBI', 'LEFT');
        $this->db->join('md_tempat_tinggal mtt', $this->table.'.TEMPAT_TINGGAL_SISWA=mtt.ID_TEMTING', 'LEFT');
        $this->db->join('md_ortu_hidup moha', $this->table.'.AYAH_HIDUP_SISWA=moha.ID_SO', 'LEFT');
        $this->db->join('md_jenjang_pendidikan mjpa', $this->table.'.AYAH_PENDIDIKAN_SISWA=mjpa.ID_JP', 'LEFT');
        $this->db->join('md_pekerjaan mpka', $this->table.'.AYAH_PEKERJAAN_SISWA=mpka.ID_JENPEK', 'LEFT');
        $this->db->join('md_ortu_hidup mohi', $this->table.'.IBU_HIDUP_SISWA=mohi.ID_SO', 'LEFT');
        $this->db->join('md_jenjang_pendidikan mjpi', $this->table.'.IBU_PENDIDIKAN_SISWA=mjpi.ID_JP', 'LEFT');
        $this->db->join('md_pekerjaan mpki', $this->table.'.IBU_PEKERJAAN_SISWA=mpki.ID_JENPEK', 'LEFT');
        $this->db->join('md_hubungan mhb', $this->table.'.WALI_HUBUNGAN_SISWA=mhb.ID_HUB', 'LEFT');
        $this->db->join('md_asal_santri mas', $this->table.'.STATUS_ASAL_SISWA=mas.MD_ASSAN', 'LEFT');
        $this->db->join('md_jenjang_pendidikan mjpw', $this->table.'.WALI_PENDIDIKAN_SISWA=mjpw.ID_JP', 'LEFT');
        $this->db->join('md_pekerjaan mpkw', $this->table.'.WALI_PEKERJAAN_SISWA=mpkw.ID_JENPEK', 'LEFT');
        $this->db->join('md_penghasilan mpg', $this->table.'.ORTU_PENGHASILAN_SISWA=mpg.ID_HASIL', 'LEFT');
        $this->db->join('md_kecamatan keco', $this->table.'.ORTU_KECAMATAN_SISWA=keco.ID_KEC', 'LEFT');
        $this->db->join('md_kabupaten kabo', 'keco.KABUPATEN_KEC=kabo.ID_KAB', 'LEFT');
        $this->db->join('md_provinsi provo', 'kabo.PROVINSI_KAB=provo.ID_PROV', 'LEFT');
        $this->db->join('md_pegawai mpmut', $this->table.'.USER_MUTASI_SISWA=mpmut.ID_PEG', 'LEFT');
        $this->db->join('md_status_mutasi msm', $this->table.'.STATUS_MUTASI_SISWA=msm.ID_MUTASI', 'LEFT');
    }

    private function set_param($kata_kunci, $filter) {
        $i = 0;
        foreach ($filter as $detail) {
            if($i == 0) $this->db->like($detail, $kata_kunci);
            else $this->db->or_like($detail, $kata_kunci);
            
            if($detail == 'NIS_SISWA') {
                $this->db->or_like('NIS_NIS', $kata_kunci);
                $this->db->join('md_nis mn', $this->table.'.ID_SISWA=mn.SISWA_NIS', 'LEFT');
            }
            
            $i++;
        }
    }
    
    public function get_rows($kata_kunci, $filter) {
        $this->_get_table();
        $this->set_param($kata_kunci, $filter);
        
        return $this->db->get()->result();
    }
    
    public function get_by_id($id) {
        $this->_get_table();
        $this->_get_table_detail();
        $this->db->where('ID_SISWA', $id);
        
        return $this->db->get()->row();
    }

    function count_filtered($kata_kunci, $filter) {
        $this->_get_table();
        $this->set_param($kata_kunci, $filter);
        $query = $this->db->get();

        return $query->num_rows();
    }

    public function count_all() {
        $this->_get_table();

        return $this->db->count_all_results();
    }
    
    public function get_nilai_um($ID_SISWA) {
        $this->db->from('pu_nilai_um pum');
        $this->db->join('md_mapel pm', 'pm.ID_MAPEL=pum.MAPEL_PNU');
        $this->db->where('SISWA_PNU', $ID_SISWA);
        
        return $this->db->get()->result();
    }
    
    public function get_akad_siswa($ID_SISWA, $TA) {
        $this->db->from('akad_siswa as');
        $this->db->join('md_tingkat mt','as.TINGKAT_AS=mt.ID_TINGK');
        $this->db->join('akad_kelas ak','as.KELAS_AS=ak.ID_KELAS', 'LEFT');
        $this->db->join('md_ruang mr','ak.RUANG_KELAS=mr.KODE_RUANG', 'LEFT');
        $this->db->join('md_pegawai mp','ak.WALI_KELAS=mp.ID_PEG', 'LEFT');
        $this->db->where('KONVERSI_AS', 0);
        $this->db->where(array(
            'TA_AS' => $TA,
            'SISWA_AS' => $ID_SISWA
        ));
        
        return $this->db->get()->row();
    }
    
    public function get_nilai($ID_AS, $KELAS, $CAWU, $TA) {
        $this->db->select('*, (IF(NILAI_AN IS NULL, 0, NILAI_AN)) AS NILAI_SISWA');
        $this->db->from('akad_guru_mapel agm');
        $this->db->join('md_mapel mm','mm.ID_MAPEL=agm.MAPEL_AGM');
        $this->db->join('md_pegawai mp','mp.ID_PEG=agm.GURU_AGM');
        $this->db->join('akad_nilai an','an.GURU_MAPEL_AN=agm.ID_AGM AND an.TA_AN='.$TA.' AND an.CAWU_AN='.$CAWU.' AND an.SISWA_AN='.$ID_AS, 'LEFT');
        $this->db->where(array(
            'TA_AGM' => $TA,
            'KELAS_AGM' => $KELAS
        ));
        
        return $this->db->get()->result();
    }
    
    public function get_absensi($ID_SISWA, $STATUS, $CAWU, $TA, $JENIS) {
//        $this->db->select('COUNT(ID_AKH) AS JUMLAH');
        $this->db->from('akad_kehadiran');
        $this->db->where(array(
            'TA_AKH' => $TA,
            'CAWU_AKH' => $CAWU,
            'SISWA_AKH' => $ID_SISWA,
            'ALASAN_AKH' => $STATUS,
            'JENIS_AKH' => $JENIS
        ));
        
        return $this->db->get()->result();
    }
    
    public function get_jenis_kehadiran() {
        $this->db->from('md_jenis_kehadiran');
        
        return $this->db->get()->result();
    }
    
    public function get_poin($ID_SISWA, $CAWU, $TA) {
        $this->db->from('komdis_siswa_header');
        $this->db->where(array(
            'TA_KSH' => $TA,
            'CAWU_KSH' => $CAWU,
            'SISWA_KSH' => $ID_SISWA,
        ));
        
        $result = $this->db->get()->row();
        
        if($result == NULL) return 0;
        else return $result->POIN_KSH;
    }
    
    public function get_nis($ID_SISWA, $TA) {
        $this->db->from('md_nis');
        $this->db->where(array(
            'TA_NIS' => $TA,
            'SISWA_NIS' => $ID_SISWA,
        ));
        
        $result = $this->db->get()->row();
        
        if ($result == NULL) return NULL;
        else return $result->NIS_NIS;
    }
    
    public function get_keuangan($ID_SISWA, $TA) {
        $this->db->from('keu_setup ks');
        $this->db->join('keu_detail kd','kd.ID_DT=ks.DETAIL_SETUP');
        $this->db->join('keu_tagihan kt','kt.ID_TAG=kd.TAGIHAN_DT AND kt.TA_TAG='.$TA);
        $this->db->where(array(
            'SISWA_SETUP' => $ID_SISWA,
            'KADALUARSA_SETUP' => 0
        ));
        
        return $this->db->get()->result();
    }
}
