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
    
    var $edit_id = FALSE;
    var $primary_key = "ID_AGM";

    public function __construct() {
        parent::__construct();
        $this->load->model(array(
            'rapor_model' => 'rapor',
            'kelas_model' => 'kelas',
        ));
        $this->auth->validation(array(10));
    }

    public function index() {
        $this->generate->backend_view('wali_kelas/siswa');
    }

    public function ajax_list($kelas) {
        $this->generate->set_header_JSON();
        $jumlah_mapel = 2;
        $id_datatables = 'datatable1';
        $list = $this->rapor->get_datatables($jumlah_mapel, $kelas);
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $item) {
            $no++;
            $nilai_total = 0;
            $row = array();
            $row[] = $item->NO_ABSEN_AS;
            $row[] = $item->NIS_SISWA;
            $row[] = $item->NAMA_SISWA;
            $row[] = '<a href="'. site_url('pencarian/detail/'.$item->ID_SISWA).'" target="_blank" class="btn btn-primary btn-sm"><i class="fa fa-eye"></i> Detail</a>';

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->rapor->count_all($kelas),
            "recordsFiltered" => $this->rapor->count_filtered($jumlah_mapel, $kelas),
            "data" => $data,
        );

        $this->generate->output_JSON($output);
    }

    public function list_kelas() {
        $this->generate->set_header_JSON();
        
        $data = $this->kelas->get_kelas($this->input->post('ID_PEG'));
        
        $this->generate->output_JSON($data);
    }

    public function list_mapel() {
        $this->generate->set_header_JSON();
        
        $data = $this->rapor->get_mapel($this->input->post('ID_KELAS'));
        
        $this->generate->output_JSON($data);
    }
}
