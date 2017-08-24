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
class Nilai_dauroh extends CI_Controller {
    
    var $jenis = 'DAUROH';

    public function __construct() {
        parent::__construct();
        $this->load->model(array(
            'nilai_dauroh_model' => 'nilai_dauroh',
        ));
        $this->auth->validation(8);
    }

    public function index() {
        $this->generate->backend_view('lpba/nilai_dauroh/index');
    }

    public function ajax_list() {
        $this->generate->set_header_JSON();

        $id_datatables = 'datatable1';
        $list = $this->nilai_dauroh->get_datatables();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $item) {
            $no++;
            $row = array();
            $row[] = $item->NIS_SISWA;
            $row[] = $item->NO_ABSEN_AS;
            $row[] = $item->NAMA_SISWA;
            $row[] = $item->JK_SISWA;
//            $row[] = $item->KETERANGAN_TINGK;
            $row[] = $item->NAMA_KELAS;
            $row[] = $item->NAMA_PEG;
            $row[] = '<p id="KEHADIRAN_LN_'.$item->ID_SISWA.'">'.$item->KEHADIRAN_LN.'</p>';
            $row[] = '<input type="text" class="form-control input-sm" value="'.($item->SYAFAWI_LN == NULL ? "" : $item->SYAFAWI_LN).'" style="width: 60px;" data-siswa="'.$item->ID_SISWA.'" data-field="SYAFAWI_LN" onchange="simpan_nilai(this);" '.($item->NAIK_AS == NULL ? '' : 'disabled').'/>';
            $row[] = '<input type="text" class="form-control input-sm" value="'.($item->TAHRIRI_LN == NULL ? "" : $item->TAHRIRI_LN).'" style="width: 60px;" data-siswa="'.$item->ID_SISWA.'" data-field="TAHRIRI_LN" onchange="simpan_nilai(this);" '.($item->NAIK_AS == NULL ? '' : 'disabled').'/>';
            $row[] = '<p id="TOTAL_LN_'.$item->ID_SISWA.'">'.$item->TOTAL_LN.'</p>';
            $row[] = '<p><h4 id="TAQDIR_LN_'.$item->ID_SISWA.'">'.$item->TAQDIR_LN.'</h4></p>';

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->nilai_dauroh->count_all(),
            "recordsFiltered" => $this->nilai_dauroh->count_filtered(),
            "data" => $data,
        );

        $this->generate->output_JSON($output);
    }
    
    public function simpan_nilai() {
        $this->generate->set_header_JSON();
        
        $SISWA_LN = $this->input->post('SISWA_LN');
        $FIELD = $this->input->post('FIELD');
        $NILAI_LN = $this->input->post('NILAI_LN');
        
        $data = array(
            $FIELD => $NILAI_LN,
            'USER_LN' => $this->session->userdata('ID_USER'),
        );
        $where = array(
            'TA_LN' => $this->session->userdata('ID_TA_ACTIVE'),
            'CAWU_LN' => 3,
            'SISWA_LN' => $SISWA_LN,
            'JENIS_LN' => $this->jenis,
        );
        $result = $this->nilai_dauroh->simpan_nilai($data, $where);
        
        $this->generate->output_JSON($result);
    }

}
