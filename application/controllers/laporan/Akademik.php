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
class Akademik extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model(array(
            'laporan_akademik_model' => 'akademik',
            'tahun_ajaran_model' => 'ta',
            'tingkat_model' => 'tingkat',
            'kelas_model' => 'kelas',
        ));
        $this->load->library('chart_handler');
        $this->auth->validation(12);
    }

    public function index() {
        $data = array(
            'TA' => $this->ta->get_all(FALSE),
            'TINGKAT' => $this->tingkat->get_all(FALSE),
        );
        
        $this->generate->backend_view('laporan/akademik/index', $data);
    }

    public function get_data() {
        $this->generate->set_header_JSON();
        
        $pie_donut = $this->input->post('pie_donut');
        $ta = $this->input->post('ta');
        $tingkat = $this->input->post('tingkat');
        $kelas = $this->input->post('kelas');

        $kelompok = $this->input->post('kelompok');
        $exp_kelompok = explode("#", $kelompok);
        $data_peg = $this->akademik->get_data($exp_kelompok[0], $ta, $tingkat, $kelas);

        $data = $this->chart_handler->format_output_single($pie_donut, $data_peg, $exp_kelompok[1], $exp_kelompok[2], $exp_kelompok[3]);

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
        header("Content-Disposition: attachment; filename=data_akademik_" . date('Y-m-d_H-i-s') . ".csv");
        
        $ta = $this->input->get('ta');
        $tingkat = $this->input->get('tingkat');
        $kelas = $this->input->get('kelas');
        $jk = $this->input->get('jk');

        echo $this->akademik->export_data($ta, $tingkat, $kelas, $jk);
    }

}
