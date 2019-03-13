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
class Laporan_perpondok extends CI_Controller {

    var $edit_id = FALSE;
    var $primary_key = "ID_PNH";

    public function __construct() {
        parent::__construct();
        $this->load->model(array(
            'laporan_hafalan_pondok_model' => 'laporan',
        ));

        $this->auth->validation(5);
    }

    public function index() {
        $this->generate->backend_view('ph/laporan_perpondok/index');
    }

    public function ajax_list($ID_KELAS) {
        $this->generate->set_header_JSON();

        $list = $this->nilai_hafalan->get_datatables($ID_KELAS);
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $item) {
            $no++;
            $row = array();
            $row[] = $item->NO_ABSEN_AS;
            $row[] = $item->NIS_SISWA;
            $row[] = $item->NAMA_SISWA;

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->nilai_hafalan->count_all($ID_KELAS),
            "recordsFiltered" => $this->nilai_hafalan->count_filtered($ID_KELAS),
            "data" => $data,
        );

        $this->generate->output_JSON($output);
    }

}
