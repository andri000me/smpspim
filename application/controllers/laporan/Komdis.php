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
class Komdis extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model(array(
            'laporan_komdis_model' => 'komdis',
            'tahun_ajaran_model' => 'ta',
            'catur_wulan_model' => 'cawu',
            'tingkat_model' => 'tingkat',
            'kelas_model' => 'kelas',
        ));
        $this->load->library('chart_handler');
        $this->auth->validation(12);
    }

    public function index() {
        $data = array(
            'TA' => $this->ta->get_all(FALSE),
            'CAWU' => $this->cawu->get_all(FALSE),
            'TINGKAT' => $this->tingkat->get_all(FALSE),
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

        $this->generate->backend_view('laporan/komdis/index', $data);
    }

    public function get_data() {
        $this->generate->set_header_JSON();

        $pie_donut = $this->input->post('pie_donut');
        $ta = $this->input->post('ta');
        $cawu = $this->input->post('cawu');
        $tingkat = $this->input->post('tingkat');
        $kelas = $this->input->post('kelas');
        $bulan = $this->input->post('bulan');
        $tahun = $this->input->post('tahun');

        $kelompok = $this->input->post('kelompok');
        $exp_kelompok = explode("#", $kelompok);
        
        if($exp_kelompok[0] == 'PELANGGARAN_KS' || $exp_kelompok[0] == 'SUMBER_KS') 
            $mode = 'detail';
        else
            $mode = 'header';
        
        $data_lap = $this->komdis->get_data($mode, $exp_kelompok[0], $ta, $tingkat, $kelas, $cawu, $bulan, $tahun);

        $data = $this->chart_handler->format_output_single($pie_donut, $data_lap, $exp_kelompok[1], $exp_kelompok[2], $exp_kelompok[3]);

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
        $detail = $this->input->get('detail');
        $ta = $this->input->get('ta');
        $cawu = $this->input->get('cawu');
        $tingkat = $this->input->get('tingkat');
        $kelas = $this->input->get('kelas');
        $bulan = $this->input->get('bulan');
        $tahun = $this->input->get('tahun');
        
        header("Content-type: application/vnd-ms-excel");
        header("Content-Disposition: attachment; filename=data_komdis_".$detail."_" . date('Y-m-d_H-i-s') . ".csv");

        echo $this->komdis->export_data($detail, $ta, $tingkat, $kelas, $cawu, $bulan, $tahun);
    }

}
