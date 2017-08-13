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
class Siswa_editor extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model(array(
            'siswa_editor_model' => 'siswa_editor',
        ));
        $this->auth->validation(array(2));
    }

    public function index() {
        $this->generate->backend_view('akademik/siswa_editor/index');
    }

    public function ajax_list() {
        $this->generate->set_header_JSON();

        $id_datatables = 'datatable1';
        $list = $this->siswa_editor->get_datatables();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $item) {
            $no++;
            $row = array();
            $row[] = $item->NIS_SISWA;
            $row[] = $item->NAMA_SISWA;
            $row[] = $item->JK_SISWA;
            $row[] = $item->NAMA_KELAS_NOW;

            $row[] = '<button class="btn btn-xs btn-primary" onclick="open_editor('.$item->ID_SISWA.')"><i class="fa fa-eye"></i></button>&nbsp;&nbsp;<button class="btn btn-xs btn-info" onclick="kartu_pelajar('.$item->ID_SISWA.')"><i class="fa fa-camera"></i></button>';

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->siswa_editor->count_all(),
            "recordsFiltered" => $this->siswa_editor->count_filtered(),
            "data" => $data,
        );

        $this->generate->output_JSON($output);
    }

    public function view_data() {
        $this->generate->set_header_JSON();

        $data = $this->siswa_editor->get_by_id($this->input->post('ID_SISWA'));

        $this->generate->output_JSON($data);
    }

    public function get_wilayah() {
        $this->generate->set_header_JSON();

        $data = $this->siswa_editor->get_wilayah();

        $this->generate->output_JSON($data);
    }
    
    public function get_jk() {
        $this->generate->set_header_JSON();

        $data = $this->siswa_editor->get_jk();

        $this->generate->output_JSON($data);
    }
}
