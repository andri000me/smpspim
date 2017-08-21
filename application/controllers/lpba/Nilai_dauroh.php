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
            $row[] = $item->KETERANGAN_TINGK;
            $row[] = $item->NAMA_KELAS;
            $row[] = $item->NAMA_PEG;
            $row[] = '<select class="form-control input-sm" style="width: 60px;" data-siswa="'.$item->ID_SISWA.'" onchange="simpan_nilai(this);" '.($item->NAIK_AS == NULL ? '' : 'disabled').'>'
                    . '<option value="" '.($item->NILAI_LN == NULL ? "selected" : '').'>-</option>'
                    . '<option value="م" '.($item->NILAI_LN == 'م' ? "selected" : '').'>م</option>'
                    . '<option value="ج ج" '.($item->NILAI_LN == 'ج ج' ? "selected" : '').'>ج ج</option>'
                    . '<option value="ج" '.($item->NILAI_LN == 'ج' ? "selected" : '').'>ج</option>'
                    . '<option value="ر" '.($item->NILAI_LN == 'ر' ? "selected" : '').'>ر</option>'
                    . '</select>';
//            $row[] = '<input type="text" class="form-control input-sm" value="'.($item->NILAI_LN == NULL ? "" : $item->NILAI_LN).'" style="width: 60px;" data-siswa="'.$item->ID_SISWA.'" onchange="simpan_nilai(this);" '.($item->NAIK_AS == NULL ? '' : 'disabled').'/>';

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
        $NILAI_LN = $this->input->post('NILAI_LN');
        
        $data = array(
            'TA_LN' => $this->session->userdata('ID_TA_ACTIVE'),
            'CAWU_LN' => 3,
            'SISWA_LN' => $SISWA_LN,
            'JENIS_LN' => $this->jenis,
            'NILAI_LN' => $NILAI_LN,
            'USER_LN' => $this->session->userdata('ID_USER'),
        );
        $where = array(
            'TA_LN' => $this->session->userdata('ID_TA_ACTIVE'),
            'CAWU_LN' => 3,
            'SISWA_LN' => $SISWA_LN,
            'JENIS_LN' => $this->jenis,
        );
        $this->nilai_dauroh->hapus_nilai($where);
        $result = $this->nilai_dauroh->simpan_nilai($data);
        
        $this->generate->output_JSON(array('status' => $result));
    }

}
