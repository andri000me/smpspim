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
class Siswa extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model(array(
            'laporan_siswa_model' => 'siswa',
        ));
        $this->load->library('chart_handler');
        $this->auth->validation(12);
    }

    public function index() {
        $this->generate->backend_view('laporan/siswa/index');
    }
    
    public function get_data() {
        $this->generate->set_header_JSON();
        
        $pie_donut = $this->input->post('pie_donut');
        $keaktifan = $this->input->post('keaktifan');
        
        $kelompok = $this->input->post('kelompok');
        $exp_kelompok = explode("#", $kelompok);
        $data_peg = $this->siswa->get_data($exp_kelompok[0], $keaktifan);
        
        $data = $this->chart_handler->format_output_single($pie_donut, $data_peg, $exp_kelompok[1], $exp_kelompok[2], $exp_kelompok[3], TRUE);
        
        $this->generate->output_JSON($data);
    }

    public function export($keaktifan = "") {
        if($keaktifan == "") $status = 'semua';
        elseif($keaktifan == 0) $status = 'tidak_aktif';
        elseif($keaktifan == 1) $status = 'aktif';
        
        header("Content-type: application/vnd-ms-excel");
        header("Content-Disposition: attachment; filename=data_siswa_".$status."_" . date('Y-m-d_H-i-s') . ".csv");

        echo $this->siswa->export_data($keaktifan);
    }
}
