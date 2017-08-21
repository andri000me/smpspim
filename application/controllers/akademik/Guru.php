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
class Guru extends CI_Controller {
    
    var $edit_id = TRUE;
    var $primary_key = "ID_PEG";

    public function __construct() {
        parent::__construct();
        $this->load->model(array(
            'guru_model' => 'guru',
            'user_model' => 'user'
        ));
        $this->auth->validation(array(2));
    }

    public function index() {
        $this->generate->backend_view('akademik/guru/index');
    }

    public function ajax_list() {
        $this->generate->set_header_JSON();

        $id_datatables = 'datatable1';
        $list = $this->guru->get_datatables();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $item) {
            $no++;
            $row = array();
            $row[] = $item->NIP_PEG;
            
            $row[] = $item->NAMA_PEG;
            
            $row[] = $item->JK_PEG;
            $row[] = $item->ALAMAT_PEG;
            $row[] = $item->NAMA_KEC;
            $row[] = $item->NAMA_KAB;
            $row[] = $item->NOHP_PEG;
            $row[] = $item->EMAIL_PEG;

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->guru->count_all(),
            "recordsFiltered" => $this->guru->count_filtered(),
            "data" => $data,
        );

        $this->generate->output_JSON($output);
    }
    
    public function auto_complete() {
        $this->generate->set_header_JSON();
        
        $data = $this->guru->get_all_ac($this->input->post('q'));
        
        $this->generate->output_JSON($data);
    }
}
