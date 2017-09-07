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
class Peserta_um extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model(array(
            'peserta_um_model' => 'peserta_um',
        ));
        $this->auth->validation(array(3, 6));
    }

    public function index() {
        $this->generate->backend_view('psb/peserta_um/index');
    }

    public function ajax_list() {
        $this->generate->set_header_JSON();

        $id_datatables = 'datatable1';
        $list = $this->peserta_um->get_datatables();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $item) {
            $no++;
            $row = array();
            $row[] = $item->NO_UM_SISWA;
            $row[] = $item->NAMA_SISWA;
            $row[] = $item->ANGKATAN_SISWA;
            $row[] = $item->JK_SISWA;
            $row[] = $item->NAMA_AS;
            $row[] = $item->NAMA_KEC;
            $row[] = $item->NAMA_KAB;
            $row[] = $item->NAMA_PROV;
            $row[] = $item->NAMA_JS;
            $row[] = $item->MASUK_TINGKAT_SISWA;

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->peserta_um->count_all(),
            "recordsFiltered" => $this->peserta_um->count_filtered(),
            "data" => $data,
        );

        $this->generate->output_JSON($output);
    }

}
