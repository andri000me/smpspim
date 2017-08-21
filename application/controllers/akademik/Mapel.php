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
class Mapel extends CI_Controller {
    
    var $edit_id = FALSE;
    var $primary_key = "KODE_MAPEL";

    public function __construct() {
        parent::__construct();
        $this->load->model(array(
            'mapel_akad_model' => 'mapel_akad',
            'tipe_mapel_model' => 'tipe_mapel',
            'departemen_model' => 'departemen',
        ));
        $this->auth->validation(array(2));
    }

    public function index() {
        $this->generate->backend_view('akademik/mapel/index');
    }

    public function ajax_list() {
        $this->generate->set_header_JSON();

        $id_datatables = 'datatable1';
        $list = $this->mapel_akad->get_datatables();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $item) {
            $no++;
            $row = array();
            $row[] = $item->KODE_MAPEL;
            $row[] = $item->NAMA_DEPT;
            $row[] = $item->NAMA_MTM;
            $row[] = $item->NAMA_MAPEL;
            $row[] = ($item->UJIAN_MAPEL == 1) ? 'YA' : 'TIDAK';

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->mapel_akad->count_all(),
            "recordsFiltered" => $this->mapel_akad->count_filtered(),
            "data" => $data,
        );

        $this->generate->output_JSON($output);
    }
    
    public function auto_complete() {
        $this->generate->set_header_JSON();
        
        $data = $this->mapel_akad->get_all_ac($this->input->post('q'));
        
        $this->generate->output_JSON($data);
    }

}
