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
class Absensi extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model(array(
            'laporan_absensi_model' => 'absensi',
            'tahun_ajaran_model' => 'ta',
            'catur_wulan_model' => 'cawu',
            'tingkat_model' => 'tingkat',
            'kelas_model' => 'kelas',
            'jenis_absensi_model' => 'jenis_absensi',
        ));
        $this->load->library('chart_handler');
        $this->auth->validation(12);
    }

    public function index() {
        $data = array(
            'TA' => $this->ta->get_all(FALSE),
            'CAWU' => $this->cawu->get_all(FALSE),
            'TINGKAT' => $this->tingkat->get_all(FALSE),
            'JENIS_ABSENSI' => $this->jenis_absensi->get_all(FALSE),
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

        $this->generate->backend_view('laporan/absensi/index', $data);
    }

    public function get_data() {
        $this->generate->set_header_JSON();

        $pie_donut = $this->input->post('pie_donut');
        $ta = $this->input->post('ta');
        $cawu = $this->input->post('cawu');
        $tingkat = $this->input->post('tingkat');
        $kelas = $this->input->post('kelas');
        $jenis_kegiatan = $this->input->post('jenis_kegiatan');
        $bulan = $this->input->post('bulan');
        $tahun = $this->input->post('tahun');

        $data_peg = $this->absensi->get_data($ta, $tingkat, $kelas, $cawu, $jenis_kegiatan, $bulan, $tahun);

        $data = $this->chart_handler->format_output_single($pie_donut, $data_peg, 'Tipe Absensi', 'Jumlah (siswa)', 'Absensi');

        $this->generate->output_JSON($data);
    }

    public function get_kelas() {
        $this->generate->set_header_JSON();

        $where = array(
            'TA_KELAS' => $this->input->post('ta')
        );
        $data = $this->kelas->get_rows($where);

        $this->generate->output_JSON($data);
    }

    public function export() {
        header("Content-type: application/vnd-ms-excel");
        header("Content-Disposition: attachment; filename=data_absensi_" . date('Y-m-d_H-i-s') . ".csv");
        
        $ta = $this->input->get('ta');
        $cawu = $this->input->get('cawu');
        $tingkat = $this->input->get('tingkat');
        $kelas = $this->input->get('kelas');
        $jenis_kegiatan = $this->input->get('jenis_kegiatan');
        $bulan = $this->input->get('bulan');
        $tahun = $this->input->get('tahun');

        echo $this->absensi->export_data($ta, $tingkat, $kelas, $cawu, $jenis_kegiatan, $bulan, $tahun);
    }

}
