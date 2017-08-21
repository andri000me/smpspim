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
class Alumni extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model(array(
            'alumni_model' => 'alumni',
        ));
        $this->auth->validation(array(11, 3));
    }

    public function index() {
        $this->generate->backend_view('master_data/alumni/index');
    }

    public function ajax_list() {
        $this->generate->set_header_JSON();

        $id_datatables = 'datatable1';
        $list = $this->alumni->get_datatables();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $item) {
            $no++;
            $row = array();
//            $row[] = $item->NIS_SISWA;
            $row[] = $item->NAMA_SISWA;
            $row[] = $item->ANGKATAN_SISWA;
            $row[] = $item->JK_SISWA;
            $row[] = $item->AYAH_NAMA_SISWA;
//            $row[] = $item->TEMPAT_LAHIR_SISWA;
//            $row[] = $item->TANGGAL_LAHIR_SISWA;
            $row[] = $item->ALAMAT_SISWA;
            $row[] = $item->NAMA_KEC;
            $row[] = $item->NAMA_KAB;
            $row[] = $item->NAMA_PROV;
            $row[] = $item->NAMA_MUTASI;

            $row[] = '
                <div class="btn-group">
                    <button data-toggle="dropdown" class="btn btn-primary btn-xs dropdown-toggle" aria-expanded="false">AKSI&nbsp;&nbsp;<span class="caret"></span></button>
                    <ul class="dropdown-menu">
                        <li><a href="javascript:void()" title="Lihat Data" onclick="view_data(\'' . $item->ID_SISWA . '\')"><i class="fa fa-eye"></i>&nbsp;&nbsp;Lihat Data</a></li>
                        <li><a href="javascript:void()" title="Foto Siswa" onclick="view_photo(\'' . $item->ID_SISWA . '\')"><i class="fa fa-file-photo-o "></i>&nbsp;&nbsp;Foto Siswa</a></li>
                    </ul>
                </div>';

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->alumni->count_all(),
            "recordsFiltered" => $this->alumni->count_filtered(),
            "data" => $data,
        );

        $this->generate->output_JSON($output);
    }
    
    public function view_data() {
        $this->generate->set_header_JSON();
        
        $data = $this->alumni->get_by_id($this->input->post('ID_SISWA'));
        
        $this->generate->output_JSON($data);
    }
    
    public function view_photo() {
        $this->generate->set_header_JSON();
        
        $data = $this->alumni->get_by_id($this->input->post('ID_SISWA'));
        
        if($data->FOTO_SISWA == NULL) $status = FALSE;
        else $status = TRUE;
        
        $this->generate->output_JSON(array(
            'status' => $status,
            'data' => array(
                'FOTO_SISWA' => $data->FOTO_SISWA,
                'NAMA_SISWA' => $data->NAMA_SISWA,
            )
        ));
    }

    public function auto_complete() {
        $this->generate->set_header_JSON();
        
        $data = $this->alumni->get_all_ac($this->input->post('q'));
        
        $this->generate->output_JSON($data);
    }
}
