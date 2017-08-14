<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


class Absensi extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->auth->validation(2);
    }

    public function index() {
        $data = array(
            'data1' => array(
                array(
                    'url' => site_url('akademik/kehadiran/cetak_absen')."/' + ID_KELAS + '/0",
                    'title' => 'Cetak Kehadiran KBM',
                ),
                array(
                    'url' => site_url('akademik/kehadiran/cetak_absen')."/' + ID_KELAS + '/1",
                    'title' => 'Cetak Kehadiran Dauroh Bhs Arab',
                ),
                array(
                    'url' => site_url('akademik/kehadiran/cetak_absen')."/' + ID_KELAS + '/4",
                    'title' => 'Cetak Kehadiran Dauroh Bhs Inggris',
                ),
                array(
                    'url' => site_url('akademik/kehadiran/cetak_absen')."/' + ID_KELAS + '/3",
                    'title' => 'Cetak Kehadiran Sorogan',
                ),
                array(
                    'url' => site_url('akademik/kehadiran/cetak_absen')."/' + ID_KELAS + '/5",
                    'title' => 'Cetak Kehadiran Kursus Ilmu Alat',
                ),
                array(
                    'url' => site_url('akademik/kehadiran/cetak_absen')."/' + ID_KELAS + '/6",
                    'title' => 'Cetak Kehadiran Pramuka',
                ),
            ),
            'data2' => array(
                array(
                    'url' => site_url('akademik/siswa/cetak_kartu_kelas')."/' + ID_KELAS + '",
                    'title' => 'Cetak Kartu Siswa',
                ),
                array(
                    'url' => site_url('keuangan/assign_tagihan/cetak_kartu')."/' + ID_KELAS + '",
                    'title' => 'Cetak Kartu Khoirot',
                ),
                array(
                    'url' => site_url('ph/cetak_kartu/cetak_all')."?blanko=0&kelas=' + ID_KELAS + '",
                    'title' => 'Cetak Bukti Semaan Hafalan',
                ),
                array(
                    'url' => site_url('ph/cetak_kartu/cetak_all')."?blanko=1&kelas=' + ID_KELAS + '",
                    'title' => 'Cetak Blanko Semaan Hafalan',
                ),
                array(
                    'url' => site_url('akademik/kehadiran/cetak_absen')."/' + ID_KELAS + '/2",
                    'title' => 'Cetak Buku Daftar Nilai',
                ),
                array(
                    'url' => site_url('akademik/kehadiran/cetak_absen')."/' + ID_KELAS + '/7",
                    'title' => 'Excel Buku Daftar Nilai',
                ),
                
            )
        );
        
        $this->generate->backend_view('akademik/absensi/index', $data);
    }
}
