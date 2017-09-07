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
class Pemasukan extends CI_Controller {
    
    var $edit_id = FALSE;
    var $primary_key = "ID_TJK";

    public function __construct() {
        parent::__construct();
        $this->load->model('jurnal_model', 'jurnal');
        $this->auth->validation(13);
    }

    public function index() {
        $data['JENIS'] = 'pemasukan';
        $this->generate->backend_view('tuk/transaksi/index', $data);
    }
    
    public function ajax_add() {
        $this->generate->set_header_JSON();
        
        $post = $this->input->post();
        unset($post['TEMP_NOMINAL_TJ']);
        unset($post['validasi']);
        $post['USER_TJ'] = $this->session->userdata('ID_USER');
        
        $insert = $this->jurnal->save($post);
        
        $this->generate->output_JSON(array('status' => $insert));
    }
}
