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
class Cetak extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('nilai_dauroh_model', 'nilai_dauroh');
        $this->load->library('translasi_handler');
        $this->auth->validation(8);
    }

    public function index() {
        $this->generate->backend_view('lpba/cetak/index');
    }

    public function cetak() {
        $post = $this->input->get();
        
        $where = array(
            'KELAS_AS' => $post['KELAS'],
        );
        $data['data'] = $this->nilai_dauroh->get_rows($where, $post['ID_TA_FILTER']);
        $data['tanggal_hijriyah'] = $post['TANGGAL'];
        $data['nama_ta'] = $post['NAMA_TA_FILTER'];
        $data['ketua_panitia'] = $post['KETUA_PANITIA'];
        
        $this->load->view('backend/lpba/cetak/cetak', $data);
    }

}
