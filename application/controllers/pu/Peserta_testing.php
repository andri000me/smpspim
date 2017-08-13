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
class Peserta_testing extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model(array(
            'peserta_testing_model' => 'peserta_testing',
        ));
        $this->auth->validation(6);
    }

    public function index() {
        $this->generate->backend_view('pu/peserta_testing/index');
    }

    public function ajax_list_quran() {
        $this->generate->set_header_JSON();

        $id_datatables = 'datatable1';
        $list = $this->peserta_testing->get_datatables();
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
            "recordsTotal" => $this->peserta_testing->count_all(),
            "recordsFiltered" => $this->peserta_testing->count_filtered(),
            "data" => $data,
        );

        $this->generate->output_JSON($output);
    }

    public function ajax_list_kitab() {
        $this->generate->set_header_JSON();

        $id_datatables = 'datatable2';
        $list = $this->peserta_testing->get_datatables(FALSE);
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
            "recordsTotal" => $this->peserta_testing->count_all(FALSE),
            "recordsFiltered" => $this->peserta_testing->count_filtered(FALSE),
            "data" => $data,
        );

        $this->generate->output_JSON($output);
    }

}
