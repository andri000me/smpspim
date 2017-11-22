<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Laporan_hafalan_model extends CI_Model {

    var $table = 'akad_siswa';

    public function __construct() {
        parent::__construct();
    }

    private function _get_table() {
        $this->db->from($this->table);
        $this->db->join('md_siswa ms', $this->table . '.SISWA_AS=ms.ID_SISWA', 'LEFT');
        $this->db->join('md_tingkat mt', $this->table . '.TINGKAT_AS=mt.ID_TINGK', 'LEFT');
        $this->db->join('akad_kelas ak', $this->table . '.KELAS_AS=ak.ID_KELAS', 'LEFT');
        $this->db->join('ph_nilai_header pnh', $this->table . '.TA_AS=pnh.TA_PNH AND ID_SISWA=SISWA_PNH', 'LEFT');
    }

    public function get_data($group, $ta, $tingkat, $jenjang, $jk) {
        if ($group == 'Jumlah Siswa') {
            $this->db->select('COUNT(ID_AS) AS data, NAMA_KELAS AS x_label');
        } elseif ($group == 'Jumlah Siswa Setoran') {
            $this->db->select('COUNT(ID_PNH) AS data, NAMA_KELAS AS x_label');
            $this->db->where('ID_PNH IS NOT NULL');
        } elseif ($group == 'Jumlah Siswa Belum Setoran') {
            $this->db->select('(COUNT(ID_AS) - COUNT(ID_PNH)) AS data, NAMA_KELAS AS x_label');
        } elseif ($group == 'Jumlah Siswa Hafal') {
            $this->db->select('COUNT(ID_PNH) AS data, NAMA_KELAS AS x_label');
            $this->db->where('ID_PNH IS NOT NULL');
            $this->db->where('STATUS_PNH', 'HAFAL');
        } elseif ($group == 'Jumlah Siswa Tidak Hafal') {
            $this->db->select('COUNT(ID_PNH) AS data, NAMA_KELAS AS x_label');
            $this->db->where('ID_PNH IS NOT NULL');
            $this->db->where('STATUS_PNH', 'TIDAK HAFAL');
        } elseif ($group == 'Jumlah Siswa Gugur') {
            $this->db->select('COUNT(ID_PNH) AS data, NAMA_KELAS AS x_label');
            $this->db->where('ID_PNH IS NOT NULL');
            $this->db->where('STATUS_PNH', 'GUGUR');
        } elseif ($group == 'Jumlah Siswa Keluar') {
            $this->db->select('COUNT(ID_PNH) AS data, NAMA_KELAS AS x_label');
            $this->db->where('ID_PNH IS NOT NULL');
            $this->db->where('STATUS_PNH', 'KELUAR');
        } elseif ($group == 'Nama Kelas') {
            $this->db->select('NAMA_KELAS AS x_label');
        }

        $this->_get_table();

        if ($ta != "")
            $this->db->where('TA_AS', $ta);
        if ($tingkat != "")
            $this->db->where('TINGKAT_AS', $tingkat);
        if ($jenjang != "")
            $this->db->where('DEPT_TINGK', $jenjang);
        if ($jk != "")
            $this->db->where('JK_SISWA', $jk);

        $this->db->where('NAMA_KELAS IS NOT NULL');
        $this->db->group_by('ID_KELAS');
        $this->db->order_by('x_label', 'ASC');
        $result = $this->db->get();

        return $result->result();
    }

}
