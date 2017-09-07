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
class Aplikasi extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model(array(
            'pengaturan_model' => 'aplikasi',
            'pegawai_model' => 'pegawai'
        ));
        $this->auth->validation(11);
    }

    public function index() {
        $data['data'] = $this->aplikasi->get_editable();
        $data['pegawai'] = $this->pegawai;
        
        $this->generate->backend_view('master_data/aplikasi/form', $data);
    }

    public function simpan() {
        $this->generate->set_header_JSON();
        $this->generate->cek_validation_simple('edit');
        
        $data = $this->aplikasi->get_editable();
        
        foreach ($data as $detail) {
            $affected_row = $this->aplikasi->update($detail->ID_PENGATURAN, $this->input->post($detail->ID_PENGATURAN));
        }

        $this->generate->output_JSON(array("status" => $affected_row));
    }
}
