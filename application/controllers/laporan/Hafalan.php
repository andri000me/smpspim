<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Sekolah
 *
 * @author rohmad
 */
class Hafalan extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model(array(
            'laporan_hafalan_model' => 'hafalan',
            'tahun_ajaran_model' => 'ta',
            'catur_wulan_model' => 'cawu',
            'tingkat_model' => 'tingkat',
            'departemen_model' => 'dept',
            'kelas_model' => 'kelas',
        ));
        $this->load->library('chart_handler');
        $this->auth->validation(array(12, 5));
    }

    public function index() {
        $data = array(
            'TA' => $this->ta->get_all(FALSE),
            'CAWU' => $this->cawu->get_all(FALSE),
            'DEPT' => $this->dept->get_all(FALSE),
            'BULAN' => array(
                '-- Pilih Bulan --',
                'Januari',
                'Februari',
                'Maret',
                'April',
                'Mei',
                'Juni',
                'Juli',
                'Agustus',
                'September',
                'Oktober',
                'Nopember',
                'Desember',
            )
        );

        $this->generate->backend_view('laporan/hafalan/index', $data);
    }

    public function get_data() {
        $this->generate->set_header_JSON();

        $pie_donut = $this->input->post('pie_donut');
        $ta = $this->input->post('ta');
        $tingkat = $this->input->post('tingkat');
        $jenjang = $this->input->post('jenjang');

        $jumlah_siswa = $this->hafalan->get_data('Jumlah Siswa', $ta, $tingkat, $jenjang);
        $jumlah_siswa_setoran = $this->hafalan->get_data('Jumlah Siswa Setoran', $ta, $tingkat, $jenjang);
        $jumlah_siswa_hafal = $this->hafalan->get_data('Jumlah Siswa Hafal', $ta, $tingkat, $jenjang);
        $jumlah_siswa_tidak_hafal = $this->hafalan->get_data('Jumlah Siswa Tidak Hafal', $ta, $tingkat, $jenjang);
        $jumlah_siswa_gugur = $this->hafalan->get_data('Jumlah Siswa Gugur', $ta, $tingkat, $jenjang);
        $jumlah_siswa_keluar = $this->hafalan->get_data('Jumlah Siswa Keluar', $ta, $tingkat, $jenjang);
        $nama_kelas = $this->hafalan->get_data('Nama Kelas', $ta, $tingkat, $jenjang);
        
        $data_source = array(
            $jumlah_siswa,
            $jumlah_siswa_setoran,
            $jumlah_siswa_hafal,
            $jumlah_siswa_tidak_hafal,
            $jumlah_siswa_gugur,
            $jumlah_siswa_keluar,
        );
        
        $names = array(
            'data0' => 'Jumlah Siswa',
            'data1' => 'Jumlah Siswa Setoran',
            'data2' => 'Jumlah Siswa Hafal',
            'data3' => 'Jumlah Siswa Tidak Hafal',
            'data4' => 'Jumlah Siswa Gugur',
            'data5' => 'Jumlah Siswa Keluar',
        );
        $data = $this->chart_handler->format_output_multiple($data_source, $nama_kelas, 'Kelas', 'Jumlah Siswa', $names);

        $this->generate->output_JSON($data);
    }

    public function get_tingkat() {
        $this->generate->set_header_JSON();

        $where = array(
            'DEPT_TINGK' => $this->input->post('jenjang')
        );
        $data = $this->tingkat->get_rows($where);

        $this->generate->output_JSON($data);
    }

    public function export() {
        $detail = $this->input->get('detail');
        $ta = $this->input->post('ta');
        $tingkat = $this->input->post('tingkat');
        $jenjang = $this->input->post('jenjang');
        $tingkat = $this->input->post('tingkat');
        
        header("Content-type: application/vnd-ms-excel");
        header("Content-Disposition: attachment; filename=data_hafalan_".$detail."_" . date('Y-m-d_H-i-s') . ".csv");

        echo $this->hafalan->export_data($detail, $ta, $tingkat, $cawu, $bulan, $tahun);
    }

}
