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
class Peserta_testing_tt extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model(array(
            'peserta_testing_tt_model' => 'peserta_testing_tt',
        ));
        $this->auth->validation(6);
    }

    public function index() {
        $this->generate->backend_view('pu/peserta_testing_tt/index');
    }

    public function ajax_list_quran() {
        $this->generate->set_header_JSON();

        $id_datatables = 'datatable1';
        $list = $this->peserta_testing_tt->get_datatables();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $item) {
            $no++;
            $row = array();
            $row[] = $item->NAMA_TA;
            $row[] = $item->NIS_SISWA;
            $row[] = $item->NAMA_SISWA;
            $row[] = $item->JK_SISWA;
            $row[] = $item->DEPT_TINGK;
            $row[] = $item->NAMA_TINGK;
            $row[] = $item->NAMA_KELAS;
            $row[] = $item->NAMA_PEG;
            $row[] = $item->LULUS_AS;
            $row[] = '<input type="number" class="form-control input-sm" onchange="simpan_nilai(this)" data-id="'.$item->ID_TN.'" value="'.$item->NILAI_TN.'" style="width: 70px;"/>';
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->peserta_testing_tt->count_all(),
            "recordsFiltered" => $this->peserta_testing_tt->count_filtered(),
            "data" => $data,
        );

        $this->generate->output_JSON($output);
    }

    public function ajax_list_kitab() {
        $this->generate->set_header_JSON();

        $id_datatables = 'datatable2';
        $list = $this->peserta_testing_tt->get_datatables(FALSE);
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $item) {
            $no++;
            $row = array();
            $row[] = $item->NAMA_TA;
            $row[] = $item->NIS_SISWA;
            $row[] = $item->NAMA_SISWA;
            $row[] = $item->JK_SISWA;
            $row[] = $item->DEPT_TINGK;
            $row[] = $item->NAMA_TINGK;
            $row[] = $item->NAMA_KELAS;
            $row[] = $item->NAMA_PEG;
            $row[] = $item->LULUS_AS;
            $row[] = '<input type="number" class="form-control input-sm" onchange="simpan_nilai(this)" data-id="'.$item->ID_TN.'" value="'.$item->NILAI_TN.'" style="width: 70px;"/>';
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->peserta_testing_tt->count_all(FALSE),
            "recordsFiltered" => $this->peserta_testing_tt->count_filtered(FALSE),
            "data" => $data,
        );

        $this->generate->output_JSON($output);
    }
    
    public function update_nilai() {
        $this->generate->set_header_JSON();
        
        $data = array(
            'NILAI_TN' => $this->input->post('NILAI_TN')
        );
        $where = array(
            'ID_TN' => $this->input->post('ID_TN')
        );
        $status = $this->peserta_testing_tt->update_nilai($where, $data);
        
        $this->generate->output_JSON(array('status' => $status));
    }

}
