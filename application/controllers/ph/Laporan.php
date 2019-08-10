<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Laporan extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model(array(
            'laporan_hafalan_model' => 'hafalan',
            'tahun_ajaran_model' => 'ta',
            'catur_wulan_model' => 'cawu',
            'tingkat_model' => 'tingkat',
            'departemen_model' => 'dept',
            'kelas_model' => 'kelas',
            'jk_model' => 'jk',
        ));
        $this->auth->validation(5);
    }

    public function index() {
        $data = array(
            'data1' => array(
                array(
                    'url' => site_url('ph/laporan/hafalan_perkelas'),
                    'title' => 'Cetak Laporan Hafalan Siswa perkelas',
                ),
                array(
                    'url' => site_url('ph/laporan/tabungan_hafalan'),
                    'title' => 'Cetak Laporan Tabungan Hafalan',
                ),
            ),
            'data2' => array(
                array(
                    'url' => site_url('ph/laporan/hafalan_perjenjang'),
                    'title' => 'Cetak Laporan Hafalan Siswa perjenjang',
                ),
                array(
                    'url' => site_url('ph/laporan/hafalan_perpondok'),
                    'title' => 'Cetak Laporan Hafalan Siswa perpondok',
                ),
            )
        );

        $this->generate->backend_view('ph/laporan/index', $data);
    }

    public function hafalan_perkelas() {
        $ta = $this->session->userdata('ID_TA_ACTIVE');
        $tingkat = null;
        $jenjang = null;
        $jk = null;

        $data = array();
        $data['jumlah_siswa'] = $this->hafalan->get_data('Jumlah Siswa', $ta, $tingkat, $jenjang, $jk, ['select' => 'ID_KELAS', 'mode' => 'hafalan_perkelas']);
        $data['jumlah_siswa_setoran'] = $this->hafalan->get_data('Jumlah Siswa Setoran', $ta, $tingkat, $jenjang, $jk, ['select' => 'ID_KELAS', 'mode' => 'hafalan_perkelas']);
        $data['jumlah_siswa_belum_setoran'] = $this->hafalan->get_data('Jumlah Siswa Belum Setoran', $ta, $tingkat, $jenjang, $jk, ['select' => 'ID_KELAS', 'mode' => 'hafalan_perkelas']);
        $data['jumlah_siswa_hafal'] = $this->hafalan->get_data('Jumlah Siswa Hafal', $ta, $tingkat, $jenjang, $jk, ['select' => 'ID_KELAS', 'mode' => 'hafalan_perkelas']);
        $data['jumlah_siswa_tidak_hafal'] = $this->hafalan->get_data('Jumlah Siswa Tidak Hafal', $ta, $tingkat, $jenjang, $jk, ['select' => 'ID_KELAS', 'mode' => 'hafalan_perkelas']);
        $data['jumlah_siswa_gugur'] = $this->hafalan->get_data('Jumlah Siswa Gugur', $ta, $tingkat, $jenjang, $jk, ['select' => 'ID_KELAS', 'mode' => 'hafalan_perkelas']);
        $data['jumlah_siswa_keluar'] = $this->hafalan->get_data('Jumlah Siswa Keluar', $ta, $tingkat, $jenjang, $jk, ['select' => 'ID_KELAS', 'mode' => 'hafalan_perkelas']);
        $data['nama_kelas'] = $this->hafalan->get_data('Nama Kelas', $ta, $tingkat, $jenjang, $jk, ['select' => 'ID_KELAS', 'mode' => 'hafalan_perkelas']);

        $this->load->view('backend/ph/laporan/hafalan_perkelas', $data);
    }

    public function hafalan_perjenjang() {
        $ta = $this->session->userdata('ID_TA_ACTIVE');
        $tingkat = null;
        $jenjang = null;
        $jk = null;

        $data = array();
        $data['jumlah_siswa'] = $this->hafalan->get_data('Jumlah Siswa', $ta, $tingkat, $jenjang, $jk, ['select' => 'ID_KELAS', 'mode' => 'hafalan_perjenjang']);
        $data['jumlah_siswa_setoran'] = $this->hafalan->get_data('Jumlah Siswa Setoran', $ta, $tingkat, $jenjang, $jk, ['select' => 'ID_KELAS', 'mode' => 'hafalan_perjenjang']);
        $data['jumlah_siswa_belum_setoran'] = $this->hafalan->get_data('Jumlah Siswa Belum Setoran', $ta, $tingkat, $jenjang, $jk, ['select' => 'ID_KELAS', 'mode' => 'hafalan_perjenjang']);
        $data['jumlah_siswa_hafal'] = $this->hafalan->get_data('Jumlah Siswa Hafal', $ta, $tingkat, $jenjang, $jk, ['select' => 'ID_KELAS', 'mode' => 'hafalan_perjenjang']);
        $data['jumlah_siswa_tidak_hafal'] = $this->hafalan->get_data('Jumlah Siswa Tidak Hafal', $ta, $tingkat, $jenjang, $jk, ['select' => 'ID_KELAS', 'mode' => 'hafalan_perjenjang']);
        $data['jumlah_siswa_gugur'] = $this->hafalan->get_data('Jumlah Siswa Gugur', $ta, $tingkat, $jenjang, $jk, ['select' => 'ID_KELAS', 'mode' => 'hafalan_perjenjang']);
        $data['jumlah_siswa_keluar'] = $this->hafalan->get_data('Jumlah Siswa Keluar', $ta, $tingkat, $jenjang, $jk, ['select' => 'ID_KELAS', 'mode' => 'hafalan_perjenjang']);
        $data['nama_kelas'] = $this->hafalan->get_data('Nama Kelas', $ta, $tingkat, $jenjang, $jk, ['select' => 'ID_KELAS', 'mode' => 'hafalan_perjenjang']);

        $this->load->view('backend/ph/laporan/hafalan_perjenjang', $data);
    }

    public function hafalan_perpondok() {
        $ta = $this->session->userdata('ID_TA_ACTIVE');
        $tingkat = null;
        $jenjang = null;
        $jk = null;

        $data = array();
        $data['jumlah_siswa'] = $this->hafalan->get_data('Jumlah Siswa', $ta, $tingkat, $jenjang, $jk, ['select' => 'ID_KELAS', 'mode' => 'hafalan_perpondok']);
        $data['jumlah_siswa_setoran'] = $this->hafalan->get_data('Jumlah Siswa Setoran', $ta, $tingkat, $jenjang, $jk, ['select' => 'ID_KELAS', 'mode' => 'hafalan_perpondok']);
        $data['jumlah_siswa_belum_setoran'] = $this->hafalan->get_data('Jumlah Siswa Belum Setoran', $ta, $tingkat, $jenjang, $jk, ['select' => 'ID_KELAS', 'mode' => 'hafalan_perpondok']);
        $data['jumlah_siswa_hafal'] = $this->hafalan->get_data('Jumlah Siswa Hafal', $ta, $tingkat, $jenjang, $jk, ['select' => 'ID_KELAS', 'mode' => 'hafalan_perpondok']);
        $data['jumlah_siswa_tidak_hafal'] = $this->hafalan->get_data('Jumlah Siswa Tidak Hafal', $ta, $tingkat, $jenjang, $jk, ['select' => 'ID_KELAS', 'mode' => 'hafalan_perpondok']);
        $data['jumlah_siswa_gugur'] = $this->hafalan->get_data('Jumlah Siswa Gugur', $ta, $tingkat, $jenjang, $jk, ['select' => 'ID_KELAS', 'mode' => 'hafalan_perpondok']);
        $data['jumlah_siswa_keluar'] = $this->hafalan->get_data('Jumlah Siswa Keluar', $ta, $tingkat, $jenjang, $jk, ['select' => 'ID_KELAS', 'mode' => 'hafalan_perpondok']);
        $data['nama_kelas'] = $this->hafalan->get_data('Nama Kelas', $ta, $tingkat, $jenjang, $jk, ['select' => 'ID_KELAS', 'mode' => 'hafalan_perpondok']);

        $this->load->view('backend/ph/laporan/hafalan_perpondok', $data);
    }

}
