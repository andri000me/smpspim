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
class Amplop extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model(array(
            'akad_siswa_model' => 'siswa',
        ));
        $this->auth->validation(2);
    }

    public function index() {
        $this->generate->backend_view('akademik/amplop/index');
    }
    
    public function cetak() {
        $post = $this->input->post(); 
        
        $where_siswa = array(
            'KELAS_AS' => $post['KELAS']
        );
        $data = array(
            'DATA' => $post,
            'SISWA' => $this->siswa->get_rows($where_siswa, TRUE)
        );
        
        $this->load->view('backend/akademik/amplop/cetak', $data);
    }
}
