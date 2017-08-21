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
class Peserta_non_um extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model(array(
            'peserta_non_um_model' => 'peserta_non_um',
            'akad_siswa_model' => 'akad_siswa',
            'siswa_model' => 'siswa',
        ));
        $this->auth->validation(array(3));
    }

    public function index() {
        $this->generate->backend_view('psb/peserta_non_um/index');
    }

    public function ajax_list() {
        $this->generate->set_header_JSON();

        $id_datatables = 'datatable1';
        $list = $this->peserta_non_um->get_datatables();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $item) {
            $no++;
            $row = array();
            $row[] = $item->NAMA_SISWA;
            $row[] = $item->ANGKATAN_SISWA;
            $row[] = $item->JK_SISWA;
            $row[] = $item->NAMA_JS;
            $row[] = $item->MASUK_TINGKAT_SISWA;

            $row[] = '<i style="cursor: pointer;" onclick="luluskan(\'' . $item->ID_SISWA . '\');" class="fa fa-thumbs-up"></i>';

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->peserta_non_um->count_all(),
            "recordsFiltered" => $this->peserta_non_um->count_filtered(),
            "data" => $data,
        );

        $this->generate->output_JSON($output);
    }

    public function luluskan_semua() {
        $this->generate->set_header_JSON();

        $list = $this->peserta_non_um->get_all();
        foreach ($list as $item) {
            $this->luluskan($item->ID_SISWA);
        }

        $this->generate->output_JSON(array('status' => TRUE));
    }

    public function luluskan($ID_SISWA = NULL) {
        if ($ID_SISWA == NULL) {
            $this->generate->set_header_JSON();
            $ajax_req = TRUE;
            $ID_SISWA = $this->input->post('ID_SISWA');
        } else {
            $ajax_req = FALSE;
        }

        $data_siswa = $this->peserta_non_um->get_by_id($ID_SISWA);

        $data = array('AKTIF_SISWA' => 1);
        $where = array('ID_SISWA' => $ID_SISWA);

        $status = $this->siswa->update($where, $data);

        // MEMASUKAN SISWA KE AKADEMIK
        $data_akad = array(
            'TA_AS' => $this->session->userdata('ID_PSB_ACTIVE'),
            'SISWA_AS' => $ID_SISWA,
            'TINGKAT_AS' => $data_siswa->ID_TINGK,
            'USER_AS' => $this->session->userdata("ID_USER")
        );

        if ($status)
            $this->akad_siswa->save($data_akad);

        if ($ajax_req)
            $this->generate->output_JSON(array('nomor_induk' => $status));
    }

    public function mengundurkan_diri() {
        $this->generate->set_header_JSON();

        $ID_SISWA = $this->input->post('ID_SISWA');
        $data = array(
            'STATUS_PSB_SISWA' => 0
        );
        $where = array(
            'ID_SISWA' => $ID_SISWA
        );
        $status = $this->siswa->update($where, $data);
        
        $this->generate->output_JSON(array('status' => $status));
    }

}
