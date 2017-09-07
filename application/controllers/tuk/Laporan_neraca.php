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
class Laporan_neraca extends CI_Controller {
    
    var $edit_id = FALSE;
    var $primary_key = "ID_TJK";

    public function __construct() {
        parent::__construct();
        $this->load->model('neraca_model', 'neraca');
        $this->load->library('neraca_handler');
        $this->auth->validation(13);
    }

    public function index() {
        $this->generate->backend_view('tuk/neraca/index');
    }
    
    public function proses_keuangan() {
        $this->generate->set_header_JSON();
        
        $this->neraca_handler->proses();
        
        $this->generate->output_JSON(array('status' => 1));
    }
    
    public function get_neraca() {
        $this->generate->set_header_JSON();
        
        $data = $this->neraca->get_neraca();
        
        $this->generate->output_JSON($data);
    }
    
    
}
