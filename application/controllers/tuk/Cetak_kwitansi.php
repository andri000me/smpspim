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
class Cetak_kwitansi extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model(array(
            'akad_siswa_model' => 'siswa',
            'jurnal_model' => 'jurnal',
            'pegawai_model' => 'pegawai'
        ));
        $this->auth->validation(13);
    }

    public function index() {
        $this->generate->backend_view('tuk/kwitansi/index');
    }
    
    public function cetak() {
        $post = $this->input->post();
        
        $where_siswa = array(
            'KELAS_AS' => $post['KELAS']
        );
        $data = array(
            'DATA' => $post,
            'SISWA' => $this->siswa->get_rows($where_siswa, TRUE),
            'PEGAWAI' => $this->pegawai->get_by_id($post['PENERIMA'])
        );
        
        $this->load->view('backend/tuk/kwitansi/cetak', $data);
    }
    
    public function cetak_individu($id) {
        $data = $this->jurnal->get_by_id($id);
        
        $this->load->view('backend/tuk/kwitansi/cetak_individu', $data);
    }
}
