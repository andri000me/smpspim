<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Laporan_absensi_model extends CI_Model {

    var $table = 'akad_siswa';

    public function __construct() {
        parent::__construct();
    }

    private function _get_table($label = NULL) {
        $this->db->from($this->table);
        $this->db->join('md_siswa ms', $this->table . '.SISWA_AS=ms.ID_SISWA');
        $this->db->join('akad_kehadiran akh',$this->table.'.SISWA_AS=akh.SISWA_AKH');
        $this->db->join('md_tahun_ajaran mta', $this->table . '.TA_AS=mta.ID_TA');
        $this->db->join('md_tingkat mt', $this->table . '.TINGKAT_AS=mt.ID_TINGK');
        $this->db->join('akad_kelas ak', $this->table . '.KELAS_AS=ak.ID_KELAS');
    }

    public function get_data($ta, $tingkat, $kelas, $cawu, $jenis_kegiatan, $bulan, $tahun) {
        $label = 'ALASAN_AKH';
        
        $this->db->select('COUNT(ID_SISWA) AS data, IF(' . $label . ' IS NULL, CONCAT("TIDAK" , " ", "ADA", " ", "DATA"), ' . $label . ') AS x_label');
        $this->_get_table($label);

        if ($ta != "")
            $this->db->where('TA_AKH', $ta);
        if ($tingkat != "")
            $this->db->where('TINGKAT_AS', $tingkat);
        if ($kelas != "")
            $this->db->where('KELAS_AS', $kelas);
        if ($cawu != "")
            $this->db->where('CAWU_AKH', $cawu);
        if ($jenis_kegiatan != "")
            $this->db->where('JENIS_AKH', $jenis_kegiatan);
        if ($bulan != 0)
            $this->db->where('MONTH(TANGGAL_AKH)', $bulan);
        if ($tahun != "")
            $this->db->where('YEAR(TANGGAL_AKH)', $tahun);
        
        $this->db->where('KONVERSI_AS', 0);
        $this->db->group_by($label);
        $this->db->order_by('x_label', 'ASC');

        return $this->db->get()->result();
    }

    public function export_data($ta, $tingkat, $kelas, $cawu, $jenis_kegiatan, $bulan, $tahun) {
        $this->load->dbutil();
        
        $this->db->select(''
                . 'ID_TA AS KODE_TAHUN_AJARAN'
                . ',NAMA_TA AS NAMA_TAHUN_AJARAN'
                . ',ID_CAWU AS KODE_CAWU'
                . ',NAMA_CAWU AS NAMA_CAWU'
                . ',NIS_SISWA AS NIS'
                . ',NAMA_SISWA AS NAMA SISWA'
                . ',KETERANGAN_TINGK AS TINGKAT'
                . ',ID_KELAS AS KODE_KELAS'
                . ',NAMA_KELAS AS NAMA_KELAS'
                . ',NO_ABSEN_AS AS NOMOR_ABSEN'
                . ',AKTIF_AS AS STATUS_KEAKTIFAN'
                . ',KONVERSI_AS AS STATUS_KONVERSI'
                . ',ID_MJK AS KODE_JENIS_KEGIATAN'
                . ',NAMA_MJK AS NAMA_JENIS_KEGIATAN'
                . ',TANGGAL_AKH AS TANGGAL_TIDAK_HADIR'
                . ',ALASAN_AKH AS ALASAN_TIDAK_HADIR'
                . ',KETERANGAN_AKH AS KETERANGAN_TIDAK_HADIR'
                . '');
        $this->_get_table();
        $this->db->join('md_pegawai mp','ak.WALI_KELAS=mp.ID_PEG', 'LEFT');
        $this->db->join('md_jenis_kehadiran mjk','akh.JENIS_AKH=mjk.ID_MJK', 'LEFT');
        $this->db->join('md_catur_wulan mcw','akh.CAWU_AKH=mcw.ID_CAWU', 'LEFT');
        if ($ta != "")
            $this->db->where('TA_AKH', $ta);
        if ($tingkat != "")
            $this->db->where('TINGKAT_AS', $tingkat);
        if ($kelas != "")
            $this->db->where('KELAS_AS', $kelas);
        if ($cawu != "")
            $this->db->where('CAWU_AKH', $cawu);
        if ($jenis_kegiatan != "")
            $this->db->where('JENIS_AKH', $jenis_kegiatan);
        if ($bulan != 0)
            $this->db->where('MONTH(TANGGAL_AKH)', $bulan);
        if ($tahun != "")
            $this->db->where('YEAR(TANGGAL_AKH)', $tahun);

        $sql = $this->db->get();

        return $this->dbutil->csv_from_result($sql);
    }

}
