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

    public function cetak_siswa_perpondok() {
        $id_pondok = $this->input->get('id');

        $data = array(
            'pondok' => $this->db_handler->get_row('md_pondok_siswa', [
                'where' => [
                    'ID_MPS' => $id_pondok
                ]
            ]),
            'siswa' => $this->db_handler->get_rows('akad_siswa', [
                'where' => [
                    'TA_AS' => $this->session->userdata('ID_TA_ACTIVE'),
                    'KONVERSI_AS' => 0,
                    'PONDOK_SISWA' => $id_pondok
                ],
                'order_by' => [
                    'NAMA_SISWA' => 'ASC'
                ]
                    ], '*', [
                ['md_siswa', 'SISWA_AS=ID_SISWA'],
                ['akad_kelas', 'KELAS_AS=ID_KELAS'],
                ['ph_nilai', 'SISWA_AS=SISWA_PHN AND TA_PHN=TA_AS'],
                ['ph_batasan', 'BATASAN_PHN=ID_BATASAN'],
                ['ph_kitab', 'KITAB_BATASAN=ID_KITAB'],
                    ], FALSE)
        );

        $this->load->view('backend/ph/laporan/cetak_siswa_perpondok', $data);
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

    public function tabungan_hafalan() {
        $ta = $this->session->userdata('ID_TA_ACTIVE');

        $data_kelas = $this->db_handler->get_rows('ph_tabungan', [
            'where' => [
                'TA_TABUNGAN' => $ta
            ],
            'group_by' => ['ID_KELAS'],
            'order_by' => ['NAMA_KELAS'],
                ], 'COUNT(SISWA_TABUNGAN) AS JUMLAH, ID_KELAS', [
            ['md_siswa', 'SISWA_TABUNGAN=ID_SISWA'],
            ['akad_siswa', 'TA_AS=TA_TABUNGAN AND SISWA_AS=SISWA_TABUNGAN'],
            ['akad_kelas', 'ID_KELAS=KELAS_AS']
        ]);

        $kelas = array();
        foreach ($data_kelas as $detail) {
            $kelas[$detail->ID_KELAS] = $detail->JUMLAH;
        }

        $data = array(
            'kelas' => $this->db_handler->get_rows('akad_kelas', ['where' => ['TA_KELAS' => $ta], 'order_by' => ['NAMA_KELAS' => 'ASC']]),
            'tabungan' => $kelas
        );

        $this->load->view('backend/ph/laporan/tabungan_hafalan', $data);
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
