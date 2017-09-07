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
class Laporan_tunggakan extends CI_Controller {
    
    var $edit_id = FALSE;
    var $primary_key = "ID_DT";

    public function __construct() {
        parent::__construct();
        $this->load->model(array(
            'laporan_tunggakan_model' => 'laporan_tunggakan',
            ));
        $this->auth->validation(4);
    }

    public function index() {
        $this->generate->backend_view('keuangan/laporan_tunggakan/index');
    }

    public function ajax_list() {
        $this->generate->set_header_JSON();

        $id_datatables = 'datatable1';
        $list = $this->laporan_tunggakan->get_datatables();
        $nominal = $this->laporan_tunggakan->nominal_all();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $item) {
            $no++;
            $row = array();
            $row[] = $item->NAMA_TA;
            $row[] = $item->NAMA_TAG;
            $row[] = $item->NAMA_DT;
            $row[] = $item->DEPT_DT;
            $row[] = $item->NIS_SISWA;
            $row[] = $item->NAMA_SISWA;

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->laporan_tunggakan->count_all(),
            "recordsFiltered" => $this->laporan_tunggakan->count_filtered(),
            "data" => $data,
            "nominal" => $nominal
            );

        $this->generate->output_JSON($output);
    }
}
