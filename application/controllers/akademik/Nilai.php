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
class Nilai extends CI_Controller {
    
    var $edit_id = FALSE;
    var $primary_key = "ID_AGM";

    public function __construct() {
        parent::__construct();
        $this->load->model(array(
            'jadwal_model' => 'jadwal',
            'guru_mapel_model' => 'guru_mapel',
            'nilai_guru_model' => 'nilai',
        ));
        $this->auth->validation(array(2, 9));
    }

    public function index() {
        $this->generate->backend_view('akademik/nilai/index');
    }

    public function ajax_list($mapel, $guru, $kelas) {
        $this->generate->set_header_JSON();

        if ($this->session->userdata('ID_HAKAKSES') != 2) 
            $guru = $this->session->userdata('ID_USER');
        
        $id_datatables = 'datatable1';
        $list = $this->nilai->get_datatables($mapel, $guru, $kelas);
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $item) {
            $no++;
            $row = array();
            $row[] = $item->NIS_SISWA;
            $row[] = $item->NAMA_SISWA;
            $row[] = '<input type="number" class="form-control input-sm" onchange="simpan_nilai(this)" data-gurumapel="'.$item->ID_AGM.'" data-siswa="'.$item->ID_AS.'" data-nilai="'.$item->ID_NILAI.'" value="'.$item->NILAI_SISWA.'" style="width: 70px;" '.($item->NAIK_AS == NULL ? '' : 'disabled').' '.($item->LULUS_AS == NULL ? '' : 'disabled').' />';

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->nilai->count_all($mapel, $guru, $kelas),
            "recordsFiltered" => $this->nilai->count_filtered($mapel, $guru, $kelas),
            "data" => $data,
        );

        $this->generate->output_JSON($output);
    }

    public function list_mapel_guru() {
        $this->generate->set_header_JSON();
        
        $where = array(
            'TA_AGM' => $this->session->userdata('ID_TA_ACTIVE'),
            'GURU_AGM' => $this->session->userdata('ID_HAKAKSES') == 2 ? $this->input->post('ID_PEG') : $this->session->userdata('ID_USER'),
        );
        
        $data = $this->guru_mapel->get_rows($where);
        
        $this->generate->output_JSON($data);
    }

    public function list_kelas_mapel() {
        $this->generate->set_header_JSON();
        
        $where = array(
            'TA_AGM' => $this->session->userdata('ID_TA_ACTIVE'),
            'GURU_AGM' => $this->session->userdata('ID_HAKAKSES') == 2 ? $this->input->post('ID_PEG') : $this->session->userdata('ID_USER'),
            'MAPEL_AGM' => $this->input->post('ID_MAPEL'),
        );
        
        $data = $this->guru_mapel->get_rows($where);
        
        $this->generate->output_JSON($data);
    }

    public function simpan_nilai() {
        $this->generate->set_header_JSON();
        
        $ID_AN = $this->input->post('ID_NILAI');
        
        $data_insert = array(
            'TA_AN' => $this->session->userdata('ID_TA_ACTIVE'),
            'CAWU_AN' => $this->session->userdata('ID_CAWU_ACTIVE'),
            'GURU_MAPEL_AN' => $this->input->post('ID_AGM'),
            'SISWA_AN' => $this->input->post('ID_SISWA'),
            'NILAI_AN' => $this->input->post('NILAI_AN'),
            'USER_AN' => $this->session->userdata('ID_USER'),
        );
        $data_update = array(
            'NILAI_AN' => $this->input->post('NILAI_AN'),
            'USER_AN' => $this->session->userdata('ID_USER'),
        );
        $where_update = array(
            'ID_AN' => $ID_AN
        );
        
        if($ID_AN == 'NONE') $status = $this->nilai->save($data_insert);
        else $status = $this->nilai->update($where_update, $data_update);
        
        $this->generate->output_JSON(array('status' => $status));
    }

}
