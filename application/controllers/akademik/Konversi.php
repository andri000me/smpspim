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
class Konversi extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model(array(
            'konversi_model' => 'konversi',
            'siswa_model' => 'siswa',
            'kelas_model' => 'kelas',
            'tingkat_model' => 'tingkat',
        ));
        $this->load->library('konversi_handler');
        $this->auth->validation(2);
    }

    public function index() {
        $this->generate->backend_view('akademik/konversi/form');
    }

    public function proses() {
        $this->generate->set_header_JSON();
        $this->generate->cek_validation_simple('add');
        
        $ID_AS = $this->input->post('ID_AS');
        $ID_SISWA = $this->input->post('ID_SISWA');
        $ID_TINGKAT = $this->input->post('ID_TINGKAT');
        $ID_KELAS = $this->input->post('ID_KELAS');
        
        $data_siswa = $this->siswa->get_by_id($ID_SISWA);
        
        $status = $this->konversi_handler->proses($ID_AS, $ID_SISWA, $data_siswa->ANGKATAN_SISWA, $ID_TINGKAT, $ID_KELAS);

        $this->generate->output_JSON(array("status" => $status, 'msg' => 'Berhasil meng-konversi siswa.'));
    }
    
    public function ac_siswa() {
        $this->generate->set_header_JSON();
        
        $data = $this->konversi->get_ac_siswa($this->input->post('q'));
        
        $this->generate->output_JSON($data);
    }
    
    public function get_data_siswa() {
        $this->generate->set_header_JSON();
        
        $data_siswa = $this->siswa->get_by_id($this->input->post('ID_SISWA'));

        if (file_exists('files/siswa/' . $data_siswa->NIS_SISWA . '.jpg')) {
            $data_siswa->FOTO_SISWA = $data_siswa->NIS_SISWA . '.jpg';
        } elseif (file_exists('files/siswa/' . $data_siswa->ID_SISWA . '.png') || $data_siswa->FOTO_SISWA != NULL) {
            $data_siswa->FOTO_SISWA = $data_siswa->ID_SISWA . '.png';
        }
        
        $data = array(
            'siswa' => $data_siswa,
            'tingkat' => $this->tingkat->get_all_except_id($data_siswa->TINGKAT_AS)
        );
        
        $this->generate->output_JSON($data);
    }
    
    public function change_tingkat() {
        $this->generate->set_header_JSON();
        
        $ID_TINGK = $this->input->post('ID_TINGK');
        $JK_SISWA = $this->input->post('JK_SISWA');
        
        $where_kelas = array(
            'TA_KELAS' => $this->session->userdata('ID_TA_ACTIVE'),
            'TINGKAT_KELAS' => $ID_TINGK,
            'JK_KELAS' => $JK_SISWA,
            'AKTIF_KELAS' => 1,
        );

        $data_kelas = $this->kelas->get_rows($where_kelas);
        
        $this->generate->output_JSON(array('kelas' => $data_kelas));
    }

}
