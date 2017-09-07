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
class Data_us extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model(array(
            'peserta_us_model' => 'peserta_us',
        ));
        $this->auth->validation(6);
    }

    public function index() {
        $this->generate->backend_view('pu/peserta_us/index');
    }

    public function ajax_list() {
        $this->generate->set_header_JSON();

        $id_datatables = 'datatable1';
        $list = $this->peserta_us->get_datatables();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $item) {
            $no++;
            $row = array();
            $row[] = $item->NIS_SISWA;
            $row[] = $item->NAMA_SISWA;
            $row[] = $item->JK_SISWA;
            $row[] = $item->DEPT_TINGK;
            $row[] = $item->NAMA_TINGK;
            $row[] = $item->NAMA_KELAS;
            $row[] = $item->NAMA_PEG;
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->peserta_us->count_all(),
            "recordsFiltered" => $this->peserta_us->count_filtered(),
            "data" => $data,
        );

        $this->generate->output_JSON($output);
    }

}
